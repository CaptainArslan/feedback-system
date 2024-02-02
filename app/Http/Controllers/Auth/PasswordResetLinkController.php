<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Mail;
use App\Mail\SendCredential;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $testMailData = [
            'email' => $request->only('email'),
            'token' => $request->only('_token')
        ];
        $user=User::where('email',$request->email)->first();
        if(empty($user)){
              return redirect()->back()->with('error', 'Please use the Correct Email');
        }
        try{
        Mail::to($request->email)->send(new SendCredential($testMailData));
         return redirect(route('login'))->with('success', 'Please Check the Email and Create New Password');
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Email not sent due to some technical error');
            
        }
        // $status = Password::sendResetLink(
        //     $request->only('email')
        // );

        // return $status == Password::RESET_LINK_SENT
        //             ? back()->with('status', __($status))
        //             : back()->withInput($request->only('email'))
        //                     ->withErrors(['email' => __($status)]);
    }
}
