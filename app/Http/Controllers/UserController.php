<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // TASK: turn this SQL query into Eloquent
        // select * from users
        //   where email_verified_at is not null
        //   order by created_at desc
        //   limit 3

        $users = User::whereNotNull('email_verified_at')->orderBy('created_at','DESC')->limit(3)->get(); // replace this with Eloquent statement

        return view('users.index', compact('users'));
    }

    public function show($userId)
    {
        $user = NULL; // TASK: find user by $userId or show "404 not found" page
        $user = User::find($userId);
        if (!empty($user)) {
            return view('users.show', compact('user'));
        }
        return abort(404, 'Page not found.');

    }

    public function check_create($name, $email)
    {
        // TASK: find a user by $name and $email
        //   if not found, create a user with $name, $email and random password
        $check = User::where('name',$name)->where('email',$email)->count();
        if ($check == 0) {
            User::create([
                'name'=>$name,
                'email'=>$email,
                'password'=> Hash::make(rand(6,8)),
            ]);
        }
        $user = User::where('name',$name)->where('email',$email)->first();
        return view('users.show', compact('user'));
    }

    public function check_update($name, $email)
    {
        // TASK: find a user by $name and update it with $email
        //   if not found, create a user with $name, $email and random password
        $check = User::where('name',$name)->first();
        if (empty($check)) {
            User::create([
                'name'=>$name,
                'email'=>$email,
                'password'=> Hash::make(rand(6,8)),
            ]);
        }else{
            $check->email = $email;
            $check->save();
        }
        $user = User::where('name',$name)->where('email',$email)->first();
        return view('users.show', compact('user'));
    }

    public function destroy(Request $request)
    {
        // TASK: delete multiple users by their IDs
        // SQL: delete from users where id in ($request->users)
        // $request->users is an array of IDs, ex. [1, 2, 3]

        // Insert Eloquent statement here
        User::whereIn('id',$request->users)->delete();

        return redirect('/')->with('success', 'Users deleted');
    }

    public function only_active()
    {
        // TASK: That "active()" doesn't exist at the moment.
        //   Create this scope to filter "where email_verified_at is not null"
        $users = new User();
        $users = $users->active()->get();

        return view('users.index', compact('users'));
    }

}
