<div class="py-8 px-4 flex flex-col items-center justify-center dark:bg-gray-900 min-h-screen lg:py-16 lg:px-6">
    <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
        <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">{{ __('Presensi Lab Pemrograman') }}</h2>
    </div>
    <div class="">
        <div wire:poll="updateRfid" class="max-w-full mb-4 mx-auto bg-white shadow-lg rounded-lg border border-gray-100 overflow-hidden dark:bg-gray-800 dark:border-gray-600">
            <div class="p-4">
                @if (is_null($rfidExists))
                    <p class="p-4 text-4xl bg-gray-100 border border-gray-200 rounded-lg">Scan RFID Anda untuk melakukan absensi.</p>
                @elseif (!$rfidExists)
                    <div class="p-4 bg-red-100 border border-red-200 rounded-lg">
                        <span class="text-red-800">RFID Anda belum terdaftar.</span>
                    </div>
                @elseif (is_null($ongoingPeriod))
                    <div class="p-4 bg-red-100 border border-red-200 rounded-lg text-center">
                        <span class="text-red-800 text-2xl">Waktu absensi belum dimulai.</span>
                    </div>
                @elseif ($hasAttended)
                    <div class="p-4 bg-yellow-100 border border-yellow-200 rounded-lg">
                        <span class="text-yellow-800">Anda sudah absen hari ini.</span>
                    </div>
                @elseif ($roomFull)
                    <div class="p-4 bg-red-100 border border-red-200 rounded-lg">
                        <span class="text-red-800">Assisten sudah mencapai batas maksimal ruangan ini.</span>
                    </div>
                @elseif ($name)
                    <div class="p-4 bg-blue-100 border border-blue-200 rounded-lg">
                        <span class="text-blue-800">Selamat Datang, <strong>{{ $name }}</strong>!</span>
                    </div>
                @else
                    <p class="p-4 bg-gray-100 border border-gray-200 rounded-lg">Scan RFID Anda untuk melakukan absensi.</p>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-1 gap-2 lg:grid-cols-2">
            @if($this->schedule)
                <div class="flex flex-col p-6 text-center text-gray-900 rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white">
                    <h1 class="text-2xl font-semibold mb-4">{{ __('Jadwal Saat ini') }}</h1>
                    <div class="space-y-2">
                        <p class="text-lg">{{ $this->schedule->period->day->name }}</p>
                        <p class="text-lg">{{ $this->schedule->period->start }} - {{ $this->schedule->period->end }}</p>
                        <p class="text-lg">{{ $this->schedule->group->name }}</p>
                    </div>
                </div>
            @else
                <div class="flex flex-col p-6 text-center text-gray-900 rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white">
                    <h1 class="text-2xl font-semibold mb-4">{{ __('Jadwal Saat ini') }}</h1>
                    <p class="text-lg">Tidak ada jadwal saat ini.</p>
                </div>
            @endif
            <div class="grid grid-cols-1 rounded-lg border border-gray-100 shadow dark:bg-gray-800 dark:border-gray-600">
                <div class="flex flex-col p-6 justify-center text-center">
                    @forelse ($this->assistants as $assistant)
                        <li>{{ $assistant->assistant->name }}</li>
                    @empty
                        <p class="dark:text-white">Tidak ada asisten yang absen hari ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
