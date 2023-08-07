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
        $this->authorize('super_administrate');

        $users = User::query()->latest('id')->paginate();

        return view('users.index', [
            'users' => $users,
        ]);
    }

     /**
     * Show the form for creating a new resource (user).
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('super_administrate');

        return view('users.create');
    }

    /**
     * Store a newly created resource (user) in the db.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->authorize('super_administrate');

        return to_route('users .index');
    }

    /**
     * Display the specified resource(user).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $this->authorize('super_administrate');


        return view('users.show');
    }

    /**
     * Show the form for editing the specified resource (user).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $this->authorize('super_administrate');

        return view('users.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $this->authorize('super_administrate');

    
        return to_route('academic-groups.show');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $this->authorize('super_administrate');

       
        return to_route('users.index');
    }
}

