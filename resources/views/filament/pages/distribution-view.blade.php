<x-filament-panels::page>
    <div class="mb-3 rounded-lg border bg-white dark:bg-gray-900">
        <table class="w-full">
            <tbody class="divide-y">
                <tr class="">
                    <td class="w-[220px] px-3 py-2">Nama Penerima</td>
                    <td>:</td>
                    <td class="px-3 py-2 dark:text-gray-300">{{ $record->recipient->name }}</td>
                </tr>
                <tr class="">
                    <td class="w-[220px] px-3 py-2">NIK</td>
                    <td>:</td>
                    <td class="px-3 py-2 dark:text-gray-300">{{ $record->recipient->nik }}</td>
                </tr>
                <tr class="">
                    <td class="w-[220px] px-3 py-2">Asal</td>
                    <td>:</td>
                    <td class="px-3 py-2 dark:text-gray-300">{{ $record->recipient->district->name }} /
                        {{ $record->recipient->village->name }}
                    </td>
                </tr>
                <tr class="">
                    <td class="w-[220px] px-3 py-2">Tahun</td>
                    <td>:</td>
                    <td class="px-3 py-2 dark:text-gray-300">{{ $record->year }}</td>
                </tr>
                <tr class="">
                    <td class="w-[220px] px-3 py-2">Stage</td>
                    <td>:</td>
                    <td class="px-3 py-2 dark:text-gray-300">{{ $record->stage }}</td>
                </tr>
                <tr class="">
                    <td class="w-[220px] px-3 py-2">Tgl. Distribusi</td>
                    <td>:</td>
                    <td class="px-3 py-2 dark:text-gray-300">{{ $record->date->translatedFormat('d F Y') }}</td>
                </tr>
                <tr class="">
                    <td class="w-[220px] px-3 py-2">Jumlah Bantuan</td>
                    <td>:</td>
                    <td class="px-3 py-2 dark:text-gray-300">Rp. {{ number_format($record->amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="">
                    <td class="w-[220px] px-3 py-2">Catatan</td>
                    <td>:</td>
                    <td class="px-3 py-2 text-sm dark:text-gray-300">{{ $record->notes ?: '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex w-full flex-wrap gap-5">
        <div class="space-y-4 text-center">
            @if ($record->recipient->ktp_photo)
            <img class="h-36 w-36 rounded-lg object-cover" src="{{ Storage::url($record->recipient->ktp_photo) }}" alt="ktp_photo">
            @else
            <div class="flex h-36 w-36 items-center justify-center rounded-lg border bg-slate-200 dark:bg-gray-700">
                <small>Ktp belum ada</small>
            </div>
            @endif
            <small class="dark:text-gray-300">Foto KTP</small>
        </div>
        <div class="text-center">
            <img class="h-36 w-36 rounded-lg object-cover" src="{{ Storage::url($record->recipient_photo) }}" alt="recipient_photo">
            <small class="dark:text-gray-300">Foto Penerima</small>
        </div>
    </div>

</x-filament-panels::page>