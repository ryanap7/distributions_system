<?php

namespace App\Console\Commands;

use App\Enums\Roles;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class GenerateRoles extends Command
{
    protected $signature = 'role:generate';

    protected $description = 'Command for generate all of role in system';

    public function handle()
    {

        $this->call('shield:install');

        collect(Roles::asArray())
            ->filter(function ($it) {
                return $it !== Roles::SUPER_ADMIN;
            })
            ->each(function ($it) {
                Role::create([
                    'name' => $it,
                    'guard_name' => 'web'
                ]);
            });

        $this->info('Roles created');
    }
}
