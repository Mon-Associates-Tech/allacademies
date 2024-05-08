<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Display a listing of the resource(user).
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('administrate');

        $users = User::query()->when($request->missing('all'))->whereNotNull('email_verified_at')->latest('id')->paginate()->withQueryString();

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

        return view('users.show', [
            'user' => $user,
        ]);
    }

}

