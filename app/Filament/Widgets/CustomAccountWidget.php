<?php

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;

class CustomAccountWidget extends AccountWidget
{
    protected static ?int $sort = -3;

    public static function canView(): bool
    {
        return false;
    }
}
