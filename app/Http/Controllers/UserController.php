<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    
    /**
     * Display a listing of the resource(user).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('administrate');

        $users = User::query()->latest('id')->paginate();

        return view('users.index', [
            'users' => $users,
        ]);
    }

    

    /**
     * Display the specified resource(user).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('administrate');

        $user->loadCount('subscriptions');
        $user->loadCount('ownedTeams');

        return view('users.show', [
            'user' => $user,
        ]);
    }

}

