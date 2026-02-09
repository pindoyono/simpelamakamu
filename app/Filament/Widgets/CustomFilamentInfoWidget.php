<?php

namespace App\Filament\Widgets;

use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\Facades\Auth;

class CustomFilamentInfoWidget extends FilamentInfoWidget
{
    protected static ?int $sort = -2;

    public static function canView(): bool
    {
        return false;
    }
}
