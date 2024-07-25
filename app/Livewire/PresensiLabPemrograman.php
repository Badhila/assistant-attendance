<?php

namespace App\Livewire;

use App\Models\Assistant;
use App\Models\AssistantMeet;
use App\Models\Meet;
use App\Models\Rfid;
use App\Models\Schedule;
use App\View\Components\AppLayout;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Class PresensiLabPemrograman
 * @package App\Livewire
 *
 * This class represents the Livewire component for managing the attendance in the Pemrograman lab.
 */
class PresensiLabPemrograman extends Component
{
    public $rfid = '';
    public $name = '';
    public $rfidExists = null;
    public $hasAttended = false;
    public $alreadyAttendOnOtherRoom = false;
    public $roomFull = false;
    public $ongoingPeriod = null;

    protected $listeners = ['refreshRfid'];

    /**
     * Mount the component.
     *
     * This method is called when the component is being mounted.
     * It updates the RFID value and resets the component's values.
     */
    public function mount()
    {
        $this->updateRfid();
    }

    #[Computed()]
    /**
     * Get the current schedule.
     *
     * This method retrieves the current schedule for the Pemrograman lab.
     *
     * @return mixed The current schedule.
     */
    public function schedule()
    {
        return Schedule::with('group', 'period', 'period.day', 'room')
            ->whereHas('room', function ($query) {
                $query->where('name', 'Pemrograman');
            })
            ->whereHas('period.day', function ($query) {
                $query->where('name', now()->format('l'));
            })
            ->whereHas('period', function ($query) {
                $query->where('start', '<=', now()->format('H:i:s'))
                    ->where('end', '>=', now()->format('H:i:s'));
            })
            ->first();
    }

    #[Computed()]
    /**
     * Get the assistants for the current meet.
     *
     * This method retrieves the assistants for the current meet in the Pemrograman lab.
     *
     * @return mixed The assistants for the current meet.
     */
    public function assistants()
    {
        return AssistantMeet::with('assistant','meet', 'meet.period', 'meet.room')
            ->whereHas('meet', function ($query) {
                $query->where('date', now()->format('Y-m-d'));
            })
            ->whereHas('meet.period', function ($query) {
                $query->where('start', '<=', now()->format('H:i:s'))
                    ->where('end', '>=', now()->format('H:i:s'));
            })
            ->whereHas('meet.room', function ($query) {
                $query->where('name', 'Pemrograman');
            })
            ->get();
    }

    /**
     * Update the RFID value.
     *
     * This method updates the RFID value and performs various checks and operations based on the RFID value.
     */
    /**
     * Update the RFID information and perform attendance tracking.
     *
     * @return void
     */
    public function updateRfid()
    {
        $this->resetValues();

        // Get the latest RFID entry
        $latestRfid = Rfid::latest()->first();

        // Get the current day and time
        $currentDay = now()->format('l');
        $currentTime = now()->format('H:i:s');

        // Set the room name
        $room = 'Pemrograman';

        // Find the ongoing period for the given room, day, and time
        $ongoingPeriod = Schedule::with('group', 'period', 'period.day', 'room')
            ->whereHas('room', function ($query) use ($room) {
                $query->where('name', $room);
            })
            ->whereHas('period.day', function ($query) use ($currentDay) {
                $query->where('name', $currentDay);
            })
            ->whereHas('period', function ($query) use ($currentTime) {
                $query->where('start', '<=', $currentTime)
                    ->where('end', '>=', $currentTime);
            })
            ->first();

        // If there is no ongoing period, set the necessary variables and return
        if (!$ongoingPeriod) {
            $this->ongoingPeriod = null;
            $this->rfidExists = true;
            return;
        }

        // If there is no latest RFID entry, truncate the Rfid table and return
        if (!$latestRfid) {
            Rfid::truncate();
            return;
        }

        // Set the RFID value
        $this->rfid = $latestRfid->rfid;

        // Set the ongoing period
        $this->ongoingPeriod = $ongoingPeriod;

        // Count the total number of meets for the ongoing period
        $totalMeetCount = Meet::where('group_id', $ongoingPeriod->group_id)
            ->where('period_id', $ongoingPeriod->period_id)
            ->where('room_id', $ongoingPeriod->room_id)
            ->count('meet_count');

        // Calculate the new meet count
        $newMeetCount = $totalMeetCount + 1;

        // If there are no previous meets, set the new meet count to 1
        if (!$totalMeetCount) {
            $newMeetCount = 1;
        }

        // Create or update the meet entry
        $meet = Meet::firstOrCreate([
            'group_id' => $ongoingPeriod->group_id,
            'period_id' => $ongoingPeriod->period_id,
            'room_id' => $ongoingPeriod->room_id,
            'date' => now()->format('Y-m-d'),
        ], [
            'meet_count' => $newMeetCount
        ]);

        // Find the assistant based on the RFID value
        $assistant = Assistant::where('rfid', $this->rfid)->first();

        // If the assistant is not found, set the necessary variables, truncate the Rfid table, and return
        if (!$assistant) {
            $this->rfidExists = false;
            $this->name = null;
            Rfid::truncate();
            return;
        }

        // Set the necessary variables for the assistant
        $this->rfidExists = true;
        $this->name = $assistant->name;

        // Check if the assistant has already attended the current meet
        $alreadyAttended = AssistantMeet::where('meet_id', $meet->id)
            ->where('assistant_id', $assistant->id)
            ->first();

        // If the assistant has already attended, set the necessary variables, truncate the Rfid table, and return
        if ($alreadyAttended) {
            $this->hasAttended = true;
            Rfid::truncate();
            return;
        }

        // Check if the assistant has already attended a meet in another room on the same day
        $alreadyAttendOnOtherRoom = AssistantMeet::where('assistant_id', $assistant->id)
            ->whereHas('meet', function ($query) use ($ongoingPeriod) {
                $query->where('date', now()->format('Y-m-d'))
                    ->where('room_id', '!=', $ongoingPeriod->room_id);
            })
            ->first();

        // If the assistant has already attended in another room, set the necessary variables, truncate the Rfid table, and return
        if ($alreadyAttendOnOtherRoom) {
            $this->rfidExists = true;
            $this->alreadyAttendOnOtherRoom = true;
            Rfid::truncate();
            return;
        }

        // Check if the room is already full
        $roomSlot = AssistantMeet::where('meet_id', $meet->id)->sum('slot_used');
        if ($roomSlot >= $ongoingPeriod->room->slots) {
            $this->roomFull = true;
            $this->rfidExists = true;
            $this->name = null;
            Rfid::truncate();
            return;
        }

        // If the assistant has not already attended, create a new AssistantMeet entry
        if (!$alreadyAttended) {
            $this->hasAttended = false;

            AssistantMeet::create([
                'meet_id' => $meet->id,
                'assistant_id' => $assistant->id,
                'slot_used' => 1
            ]);
        } else {
            $this->hasAttended = true;
        }

        // Truncate the Rfid table
        Rfid::truncate();
    }

    /**
     * Reset the component's values.
     *
     * This method resets the component's values to their initial state.
     */
    public function resetValues()
    {
        $this->rfid = '';
        $this->name = '';
        $this->rfidExists = null;
        $this->hasAttended = false;
        $this->roomFull = false;
        $this->ongoingPeriod = null;
    }

    /**
     * Render the component.
     *
     * This method renders the component's view.
     *
     * @return \Illuminate\Contracts\View\View The rendered view.
     */
    public function render()
    {
        return view('livewire.presensi-lab-pemrograman')
            ->layout(AppLayout::class, ['title' => 'Presensi Lab Pemrograman']);
    }
}
