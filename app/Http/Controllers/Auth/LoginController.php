<?php

namespace App\Http\Controllers\Auth;
use Socialite;
use Auth;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->stateless()->user();
        //cek apakah user telah terdaftar
        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){
            Auth::login($existingUser, true);
        } else {
            return User::create([
                'name' => $user->name,
                'email'=> $user->email,
                'provider'=> strtoupper($provider),
                'provider-id'=>$user->id,
                'photo'=>$user->getAvatar(),
                'email_verified_at'=>now(),
            ]);
        }
        return redirect($this->redirectTo);
    }
}
