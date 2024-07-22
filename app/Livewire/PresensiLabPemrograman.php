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

class PresensiLabPemrograman extends Component
{
    public $rfid = '';
    public $name = '';
    public $rfidExists = null;
    public $hasAttended = false;
    public $roomFull = false;
    public $ongoingPeriod = null;

    protected $listeners = ['refreshRfid'];

    public function mount()
    {
        $this->updateRfid();
    }

    #[Computed()]
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
    public function assistants()
    {
        return AssistantMeet::with('assistant', 'meet', 'meet.period')
            ->whereHas('meet', function ($query) {
                $query->where('date', now()->format('Y-m-d'));
            })
            ->whereHas('meet.period', function ($query) {
                $query->where('start', '<=', now()->format('H:i:s'))
                    ->where('end', '>=', now()->format('H:i:s'));
            })
            ->get();
    }

    public function updateRfid()
    {
        $this->resetValues();

        $latestRfid = Rfid::latest()->first();
        $currentDay = now()->format('l');
        $currentTime = now()->format('H:i:s');
        $room = 'Pemrograman';

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

        if (!$ongoingPeriod) {
            $this->ongoingPeriod = null;
            $this->rfidExists = true;
            return;
        }

        if (!$latestRfid) {
            Rfid::truncate();
            return;
        }

        $this->rfid = $latestRfid->rfid;

        $this->ongoingPeriod = $ongoingPeriod;

        $totalMeetCount = Meet::where('group_id', $ongoingPeriod->group_id)
            ->count('meet_count');

        $newMeetCount = $totalMeetCount + 1;

        if (!$totalMeetCount) {
            $newMeetCount = 1;
        }

        $meet = Meet::firstOrCreate([
            'group_id' => $ongoingPeriod->group_id,
            'period_id' => $ongoingPeriod->period_id,
            'date' => now()->format('Y-m-d'),
        ], [
            'meet_count' => $newMeetCount
        ]);

        $assistant = Assistant::where('rfid', $this->rfid)->first();

        if (!$assistant) {
            $this->rfidExists = false;
            $this->name = null;
            Rfid::truncate();
            return;
        }

        $this->rfidExists = true;
        $this->name = $assistant->name;

        $alreadyAttended = AssistantMeet::where('meet_id', $meet->id)
            ->where('assistant_id', $assistant->id)
            ->first();

        if ($alreadyAttended) {
            $this->hasAttended = true;
            Rfid::truncate();
            return;
        }

        $roomSlot = AssistantMeet::where('meet_id', $meet->id)->sum('slot_used');
        if ($roomSlot >= $ongoingPeriod->room->slots) {
            $this->roomFull = true;
            $this->rfidExists = true;
            $this->name = null;
            Rfid::truncate();
            return;
        }

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

        Rfid::truncate();
    }

    public function resetValues()
    {
        $this->rfid = '';
        $this->name = '';
        $this->rfidExists = null;
        $this->hasAttended = false;
        $this->roomFull = false;
        $this->ongoingPeriod = null;
    }

    public function render()
    {
        return view('livewire.presensi-lab-pemrograman')
            ->layout(AppLayout::class, ['title' => 'Presensi Lab Pemrograman']);
    }
}
