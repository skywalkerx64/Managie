<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Profile;
use App\Models\Secteur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AppConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\GenerateOTPRequest;
use App\Services\Notification\MailObject;
use App\Http\Requests\Auth\CheckNPIRequest;
use App\Http\Requests\Auth\LoginOTPRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\CheckRCCMRequest;
use App\Http\Resources\User\UserShortResource;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\Notification\NotificationService;

class AuthController extends Controller
{
    /**
     * Register.
     *
     * @return \App\User
     */
    protected function register(RegisterRequest $request) : JsonResponse
    {
        $user = User::create([
            'firstname'     => $request->firstname ?? $request->nom_promoteur,
            'lastname'     => $request->lastname ?? $request->prenoms_promoteur,
            'email'    => $request->email ?? $request->email_promoteur,
            'identity' => self::generate_unique_identity(),
            'password' => Hash::make($request->password),
        ]);

        $user_role = Role::where('alias', Role::COLLABORATER_ROLE_ALIAS)->first();   
        
        $user->roles()->attach($user_role->id);

        event(new Registered($user));

        return $this->handleResponse($user, trans('auth.registered'), Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        if (Auth::attempt([
            "email" => $request->email,
            "password" => $request->password,
        ])) 
        {
            if(AppConfiguration::getByCode(User::CAN_USE_OTP_CONF)->value)
            {
                $user = User::where('email', $request->email)->first();

                $otp = Str::random(8);
                $user->update([
                    'otp' => $otp
                ]);
        
                (new NotificationService)->toEmails([$user->email])->sendMail(new MailObject(
                    subject: trans('mails.otp_subject'),
                    template: "emails.otp",
                    data: [
                        "name" => $user->fullname,
                        "otp" => $otp
                    ]
                  ));
                return response()->json(['message' => trans('auth.otp_sent') ,'is_otp_active' =>true]);
            }
            $user = User::find(Auth::user()->id);

            $data["token"] = $user->createToken("LaravelSanctumAuth")->plainTextToken;
            
            $data['user'] = new UserShortResource($user->load('roles', 'permissions'));

            return $this->handleResponse($data, trans('auth.login'));

        } else {

            return $this->handleResponse([], trans('auth.failed'), Response::HTTP_UNAUTHORIZED, false);
        }
    }

    public function logout() : JsonResponse
    {
        Auth::logout();

        return $this->handleResponse(null, trans('auth.logout'));
    }

    public function login_otp(LoginOTPRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if(Hash::check($request->otp, $user->otp))
        {
            $user->update(['otp' => null]);
            Auth::login($user);

            $data["token"] = $user->createToken("LaravelSanctumAuth")->plainTextToken;
            
            $data['user'] = new UserShortResource($user->load('roles', 'permissions', 'profile'));

            return $this->handleResponse($data, trans('auth.login'));
        }
        return $this->handleResponse(trans('auth.failed'), Response::HTTP_UNAUTHORIZED, false);

    }

    public function change_password(ChangePasswordRequest $request)
    {
        $user = User::find(auth()->user()->id);

        if(Hash::check($request->old_password, $user->password))
        {
            if(Hash::check($request->password, $user->password))
            {
                return $this->handleResponse([], trans('passwords.must_not_match'), 422, false);
            }
            $user->update(['password' => $request->password]);

            return $this->handleResponse([], trans('passwords.changed'));
        }
        return $this->handleResponse([],trans('auth.failed'), Response::HTTP_UNPROCESSABLE_ENTITY, false);
    }

    public static function generate_unique_identity()
    {
        $identity = Str::random(16);

        while (User::where('identity', $identity)->exists()) {
            $identity = mt_rand(1000000000, 9999999999);
        }

        return $identity;
    }
}
