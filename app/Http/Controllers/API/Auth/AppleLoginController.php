<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Two\User as OAuthTwoUser;


class AppleLoginController extends Controller
{
    public function appleLogin(Request $request)
    {
        $provider = 'apple';
        $token = $request->token;

        $socialUser = Socialite::driver($provider)->userFromToken($token);
        $user = $this->getLocalUser($socialUser);

        $client = DB::table('oauth_clients')
            ->where('password_client', true)
            ->first();
        if (!$client) {
            return response()->json([
                'message' => trans('validation.passport.client_error'),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = [
            'grant_type' => 'social',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'provider' => 'apple',
            'access_token' => $token
        ];
        $request = Request::create('/oauth/token', 'POST', $data);

        $content = json_decode(app()->handle($request)->getContent());
        if (isset($content->error) && $content->error === 'invalid_request') {
            return response()->json(['error' => true, 'message' => $content->message]);
        }

        return response()->json(
            [
                'error' => false,
                'data' => [
                    'user' => $user,
                    'meta' => [
                        'token' => $content->access_token,
                        'expired_at' => $content->expires_in,
                        'refresh_token' => $content->refresh_token,
                        'type' => 'Bearer'
                    ],
                ]
            ],
            Response::HTTP_OK
        );
    }
    protected function getLocalUser(OAuthTwoUser $socialUser): ?User
    {
        $user = User::where('email', $socialUser->email)->first();

        if (!$user) {
            $user = $this->registerAppleUser($socialUser);
        }

        return $user;
    }
}
