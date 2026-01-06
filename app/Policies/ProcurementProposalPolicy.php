<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProcurementProposal;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcurementProposalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProcurementProposal');
    }

    public function view(AuthUser $authUser, ProcurementProposal $procurementProposal): bool
    {
        return $authUser->can('View:ProcurementProposal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProcurementProposal');
    }

    public function update(AuthUser $authUser, ProcurementProposal $procurementProposal): bool
    {
        return $authUser->can('Update:ProcurementProposal');
    }

    public function delete(AuthUser $authUser, ProcurementProposal $procurementProposal): bool
    {
        return $authUser->can('Delete:ProcurementProposal');
    }

    public function restore(AuthUser $authUser, ProcurementProposal $procurementProposal): bool
    {
        return $authUser->can('Restore:ProcurementProposal');
    }

    public function forceDelete(AuthUser $authUser, ProcurementProposal $procurementProposal): bool
    {
        return $authUser->can('ForceDelete:ProcurementProposal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProcurementProposal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProcurementProposal');
    }

    public function replicate(AuthUser $authUser, ProcurementProposal $procurementProposal): bool
    {
        return $authUser->can('Replicate:ProcurementProposal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProcurementProposal');
    }

}