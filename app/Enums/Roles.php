<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Roles extends Enum
{
    const SUPER_ADMIN = "Super Admin";
    const ADMIN = "Admin";
    const PERANGKAT_DESA = "Perangkat Desa";
}
