<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SekolahSarpras;
use Illuminate\Auth\Access\HandlesAuthorization;

class SekolahSarprasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SekolahSarpras');
    }

    public function view(AuthUser $authUser, SekolahSarpras $sekolahSarpras): bool
    {
        return $authUser->can('View:SekolahSarpras');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SekolahSarpras');
    }

    public function update(AuthUser $authUser, SekolahSarpras $sekolahSarpras): bool
    {
        return $authUser->can('Update:SekolahSarpras');
    }

    public function delete(AuthUser $authUser, SekolahSarpras $sekolahSarpras): bool
    {
        return $authUser->can('Delete:SekolahSarpras');
    }

    public function restore(AuthUser $authUser, SekolahSarpras $sekolahSarpras): bool
    {
        return $authUser->can('Restore:SekolahSarpras');
    }

    public function forceDelete(AuthUser $authUser, SekolahSarpras $sekolahSarpras): bool
    {
        return $authUser->can('ForceDelete:SekolahSarpras');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SekolahSarpras');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SekolahSarpras');
    }

    public function replicate(AuthUser $authUser, SekolahSarpras $sekolahSarpras): bool
    {
        return $authUser->can('Replicate:SekolahSarpras');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SekolahSarpras');
    }

}