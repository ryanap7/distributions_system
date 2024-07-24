<?php

namespace App\Imports;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserAccountImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $user = User::where('email', $row['email'])->firstOr(function () use ($row) {
                    return User::create([
                        'name' => $row['nama'],
                        'email' => $row['email'],
                        'phone_number' => $row['no_telepon'],
                        'password' => bcrypt($row['password']),
                    ]);
                });

                $user->assignRole(Roles::PERANGKAT_DESA);
            }
        });
    }
}
