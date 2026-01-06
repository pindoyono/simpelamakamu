<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SarprasCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class SarprasCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SarprasCategory');
    }

    public function view(AuthUser $authUser, SarprasCategory $sarprasCategory): bool
    {
        return $authUser->can('View:SarprasCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SarprasCategory');
    }

    public function update(AuthUser $authUser, SarprasCategory $sarprasCategory): bool
    {
        return $authUser->can('Update:SarprasCategory');
    }

    public function delete(AuthUser $authUser, SarprasCategory $sarprasCategory): bool
    {
        return $authUser->can('Delete:SarprasCategory');
    }

    public function restore(AuthUser $authUser, SarprasCategory $sarprasCategory): bool
    {
        return $authUser->can('Restore:SarprasCategory');
    }

    public function forceDelete(AuthUser $authUser, SarprasCategory $sarprasCategory): bool
    {
        return $authUser->can('ForceDelete:SarprasCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SarprasCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SarprasCategory');
    }

    public function replicate(AuthUser $authUser, SarprasCategory $sarprasCategory): bool
    {
        return $authUser->can('Replicate:SarprasCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SarprasCategory');
    }

}