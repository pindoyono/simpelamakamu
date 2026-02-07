<?php

namespace App\Filament\Widgets;

use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\Facades\Auth;

class CustomFilamentInfoWidget extends FilamentInfoWidget
{
    protected static ?int $sort = -2;

    public static function canView(): bool
    {
        $user = Auth::user();
        
        // Hide for users with only 'sekolah' role
        if ($user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            return false;
        }
        
        return true;
    }
}
