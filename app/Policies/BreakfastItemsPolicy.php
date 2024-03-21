<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BreakfastItemsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return Response|bool
     */
    public function viewAny(): Response|bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return Response|bool
     */
    public function view(): Response|bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return Response|bool
     */
    public function create(): Response|bool
    {
       return auth()->user()->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return Response|bool
     */
    public function update(): Response|bool
    {
       return auth()->user()->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return Response|bool
     */
    public function delete(): Response|bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     * @return Response|bool
     */
    public function restore(): Response|bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @return Response|bool
     */
    public function forceDelete(): Response|bool
    {
        return auth()->user()->is_admin;
    }
}
