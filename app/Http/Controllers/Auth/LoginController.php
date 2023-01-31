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
    public function username(){
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

     use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

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
        $estado=false;
        // $user=null;
        $mensaje='';
        $token = $request->token;
        $user = Socialite::driver('google')->userFromToken($token);
        
        DB::beginTransaction();
        try {
           

            
            
             
            if (!is_null($user)) {

                $validaEmail=User::where("correo",$user->getEmail())->first();

                $estado = true;
                if(is_null($validaEmail)){
                    $userT = new User();
                    $userT->nombre = $user->getName();
                    $userT->correo = $user->getEmail();
                    $userT->save();                    
                }

                // $buscaSP==Socialprofile::where(    )

                $socialProfile=new Socialprofile();
                $socialProfile->user_id=$userT->id_usuario;
                $socialProfile->social_id=$user->getId();
                $socialProfile->social_avatar=$user->getAvatar();
                $socialProfile->social_name='Google';
                $socialProfile->save();
            }
            $mensaje='Ingreso exitoso';
        } catch (\Exception $ex) {
            $estado=false;
            $user=null;
            $mensaje='Hubo un error '.$ex->getMessage();
            DB::rollback();
        }
        DB::commit();
        $mensaje='';
        // Auth::attempt(['correo'=>$user->getEmail()]);
        $user2= User::where('correo','=',$user->getEmail())->first();
        $user2=Auth::login($user2);

        return ['estado' => $estado, 'user' => $user2,'mensaje'=>$mensaje];

        // 
        // $token=$user->createToken("Auth")->plainTextToken;
        // return response(['user'=>$user,'token'=>$token],200);
        // return ['estado' => $estado, 'user' => $user,'mensaje'=>$mensaje,'res'=>$res];
    }
}
