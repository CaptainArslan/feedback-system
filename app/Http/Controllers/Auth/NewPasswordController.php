<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request,$email='',$token='')
    {
        $email=$email;
        $token=$token;
        return view('auth.reset-password', get_defined_vars());
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
          $check = Validator::make( $request->all(), [
            'token' => 'required',
            'email' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);
        if ($check->fails()) {
            return redirect()->back()->with('error', $check->errors()->first());
        }
       

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $user=User::Where('email',$request->email)->first();
        if($user){
              $user->password = bcrypt($request->new_password);
               $user->save();
               return redirect()->route('login')->with('success', "Password SuccessfullY Changed");
        }else{
            return redirect()->back()->with('error', 'User Not Found');
        }
        // $status = Password::reset(
        //     $request->only('email', 'new_password', 'confirm_password', 'token'),
        //     function ($user) use ($request) {
        //         $user->forceFill([
        //             'password' => Hash::make($request->new_password),
        //             'remember_token' => Str::random(60),
        //         ])->save();

        //         event(new PasswordReset($user));
        //     }
        // );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
