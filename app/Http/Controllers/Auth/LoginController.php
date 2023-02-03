<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Socialprofile;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function username()
    {
        return 'correo';
    }
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

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo ='/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    // public function FunctionName(Type $var = null)
    // {
    //     # code...
    // }
    // public function login(Request $request)
    // {
    //     // return ['preuba'=>"fdsfd"];

    // }
    public function autenticate(Request $request)
    {
        // return ['algo' => "llegamos"];
        $estado = false;
        // $user=null;
        $mensaje = '';
        $token = $request->token;


        DB::beginTransaction();
        try {
            $user2=null;
            $userReal=null;
            $userSocialite = Socialite::driver('google')->userFromToken($token);
            if (!is_null($userSocialite)) {

                $buscaSP=Socialprofile::where('social_id','=',$userSocialite->getId())->where('social_name','=','Google')->first();
                // return $buscaSP;
                if(is_null($buscaSP)){
                    $userT = User::where("correo", $userSocialite->getEmail())->first();
                    if (is_null($userT)) {
                        $userT = new User();
                        $userT->nombre = $userSocialite->getName();
                        $userT->correo = $userSocialite->getEmail();
                        $userT->save();


                    }

                    $socialProfile = new Socialprofile();
                    $socialProfile->user_id = $userT->id_user;
                    $socialProfile->social_id = $userSocialite->getId();
                    $socialProfile->social_avatar = $userSocialite->getAvatar();
                    $socialProfile->social_name = 'Google';
                    $socialProfile->save();
                    $user2 = Auth::login($userT);
                    $userReal=Auth::user();
                }else{
                    $user2=User::where("id_user",'=',$buscaSP->user_id)->first();
                    $user2=Auth::login($user2);
                    $userReal=Auth::user();
                }
                

                $estado = true;
               
            }else{
                $estado=false;
                $mensaje="Hubo un Problema";
            }

           

            $mensaje = 'Ingreso exitoso';
        } catch (\Exception $ex) {
            $estado = false;
            $user2 = null;
            $userReal=null;
            $mensaje = 'Hubo un error ' . $ex->getMessage();
            DB::rollback();
        }
        DB::commit();
        return ['estado' => $estado, 'user' => $userReal, 'mensaje' => $mensaje];

    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
