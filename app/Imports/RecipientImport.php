<?php

namespace App\Imports;

use App\Models\Village;
use App\Models\District;
use App\Models\Recipient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RecipientImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {

            foreach ($rows as $row) {
                $nameDistrict   = $row['kecamatan'];
                $nameVillage    = $row['desa'];

                if (!$nameDistrict || !$nameVillage) {
                    continue;
                }

                $district = District::where('name', $nameDistrict)->firstOr(function () use ($nameDistrict) {
                    return District::create([
                        'name' => $nameDistrict,
                        'slug' => str($nameDistrict)->slug(),
                    ]);
                });

                $village = Village::where('name', $nameVillage)->firstOr(function () use ($nameVillage, $district) {
                    return Village::create([
                        'name' => $nameVillage,
                        'slug' => str($nameVillage)->slug(),
                        'district_id' => $district->id
                    ]);
                });

                $checkNik = Recipient::where('nik', $row['nik'])->first();

                if (!$checkNik) {
                    $name = $row['nama'];
                    Recipient::create([
                        'name' => $name,
                        'slug' => str($name)->slug(),
                        'nik' => $row['nik'],
                        'village_id' => $village->id
                    ]);
                }
            }
        });
    }
}
