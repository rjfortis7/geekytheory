<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Validator;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if (!$id) {
            $user = User::with('userMeta')->findOrFail(Auth::id());
        } else {
            $user = User::with('userMeta')->findOrFail($id);
        }

        // The $user variable cant be sent as 'user' because there is already a 'user' 
        // variable shared to all the views.
        return view('home.profile.profile', ['userProfile' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = array(
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|unique:users,username,' . $user->id,
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('home/profile/' . $user->id)->withErrors($validator->messages());
        } else {
            $user->update($request->except('_token'));
        }

        return Redirect::to('home/profile/' . $user->id)->withSuccess(trans('auth.user_update_success'));
    }
}
