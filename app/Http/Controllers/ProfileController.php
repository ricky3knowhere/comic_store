<?php

namespace App\Http\Controllers;

//use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
  /**
  * Show the form for editing the profile.
  *
  * @return \Illuminate\View\View
  */
  public function index() {
    return view('profile.index');
  }
  public function edit() {
    return view('profile.edit');
  }

  /**
  * Update the profile
  *
  * @param  \App\Http\Requests\ProfileRequest  $request
  * @return \Illuminate\Http\RedirectResponse
  */
  public function update(Request $request) {


    if (auth()->user()->id == 1) {
      return back()->withErrors(['not_allow_profile' => __('You are not allowed to change data for a default user.')]);
    }
    
    $user = auth()-> user();
    $old_pict = $user-> picture;

    $picture = $request ->picture;

    $picture_save = '';

    if ($picture) {
      if ($old_pict == null) {
        $picture_name = date('Ymd').rand(100, 99999).'.'.$picture ->getClientOriginalExtension();
        $picture_save = $picture_name;
        
      } else {
        $picture_save = $old_pict;
      }
      $picture ->move(public_path().'/assets/img/users/', $picture_save);
    }




    if (!empty($request -> password)) {

      $user -> password = Hash::make($request ->password);
    }

    $user -> name = $request -> name;
    $user -> email = $request -> email;
    $user -> address = $request -> address;
    $user -> phone = $request -> phone;
    $user -> picture = $picture_save;

    $user ->update();


    return redirect('profile/details')-> with('notif', 'Your profile has been updated...');;

    // auth()->user()->update($request->all());

    // return back()->withStatus(__('Profile successfully updated.'));
  }

  /**
  * Change the password
  *
  * @param  \App\Http\Requests\PasswordRequest  $request
  * @return \Illuminate\Http\RedirectResponse
  */
  public function password(PasswordRequest $request) {
    if (auth()->user()->id == 1) {
      return back()->withErrors(['not_allow_password' => __('You are not allowed to change the password for a default user.')]);
    }

    auth()->user()->update(['password' => Hash::make($request->get('password'))]);

    return back()->withPasswordStatus(__('Password successfully updated.'));
  }
}