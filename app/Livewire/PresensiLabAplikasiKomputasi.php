<?php

namespace App\Livewire;

use App\Models\Assistant;
use App\Models\AssistantMeet;
use App\Models\Meet;
use App\Models\Rfid;
use App\Models\Schedule;
use App\View\Components\AppLayout;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PresensiLabAplikasiKomputasi extends Component
{
    public $rfid = '';
    public $name = '';
    public $rfidExists = null;
    public $hasAttended = false;
    public $alreadyAttendOnOtherRoom = false;
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
        Carbon::setLocale('id');
        return Schedule::with('group', 'period', 'period.day', 'room')
            ->whereHas('room', function ($query) {
                $query->where('name', 'Aplikasi Komputasi');
            })
            ->whereHas('period.day', function ($query) {
                $query->where('name', now()->translatedFormat('l'));
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
        return AssistantMeet::with('assistant', 'meet', 'meet.period', 'meet.room')
            ->whereHas('meet', function ($query) {
                $query->where('date', now()->format('Y-m-d'));
            })
            ->whereHas('meet.period', function ($query) {
                $query->where('start', '<=', now()->format('H:i:s'))
                    ->where('end', '>=', now()->format('H:i:s'));
            })
            ->whereHas('meet.room', function ($query) {
                $query->where('name', 'Aplikasi Komputasi');
            })
            ->get();
    }

    public function updateRfid()
    {
        $this->resetValues();

        $latestRfid = Rfid::latest()->first();
        Carbon::setLocale('id');
        $currentDay = now()->translatedFormat('l');
        $currentTime = now()->format('H:i:s');
        $room = 'Aplikasi Komputasi';

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
            ->where('period_id', $ongoingPeriod->period_id)
            ->where('room_id', $ongoingPeriod->room_id)
            ->count('meet_count');

        $newMeetCount = $totalMeetCount + 1;

        if (!$totalMeetCount) {
            $newMeetCount = 1;
        }

        $meet = Meet::firstOrCreate([
            'group_id' => $ongoingPeriod->group_id,
            'period_id' => $ongoingPeriod->period_id,
            'room_id' => $ongoingPeriod->room_id,
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

        $alreadyAttendOnOtherRoom = AssistantMeet::where('assistant_id', $assistant->id)
            ->whereHas('meet', function ($query) use ($ongoingPeriod) {
                $query->where('date', now()->format('Y-m-d'))
                    ->where('room_id', '!=', $ongoingPeriod->room_id);
            })
            ->first();

        if ($alreadyAttendOnOtherRoom) {
            $this->rfidExists = true;
            $this->alreadyAttendOnOtherRoom = true;
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
        return view('livewire.presensi-lab-aplikasi-komputasi')
        ->layout(AppLayout::class, ['title' => 'Presensi Lab Aplikasi Komputasi']);
    }
}
