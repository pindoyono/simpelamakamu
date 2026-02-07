<?php

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;

class CustomAccountWidget extends AccountWidget
{
    protected static ?int $sort = -3;

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
