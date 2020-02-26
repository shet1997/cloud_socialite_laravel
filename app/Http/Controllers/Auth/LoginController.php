<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\User; 
use Illuminate\Http\Request;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * Redirect the user to the facebook authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }


    /**
     * Obtain the user information from facebook.
     *
     * @return Response
     */


    public function handleProviderCallback(Request $request)
    {

        if (!$request->has('code') || $request->has('denied')) {
            return redirect('/');
        }

        try {

            $socialUser = Socialite::driver('facebook')->stateless()->user();

        } 

        catch (Exception $e) {
            
            return redirect ('/');
        }

        $user = User::where('email', $socialUser->email)->first();

        if($user) {

            auth()->login($user); 
            return redirect ('/home');            

        } else {

            $newUser = new User;
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->password = bcrypt(123456);
            $newUser->save();
            auth()->login($user); 
            return redirect ('/home');

        }

            
    }
}
