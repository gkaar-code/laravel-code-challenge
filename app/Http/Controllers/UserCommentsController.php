<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $comments = $user->comments()
        ->when(
            ($authUser = Auth::user()) && $user->is($authUser),
            fn ($query) => $query->visibleForAuthenticated($authUser),
            fn ($query) => $query->visibleForGuests(),
        )
        ->paginate();

        return response($comments, Response::HTTP_OK);
    }
}
