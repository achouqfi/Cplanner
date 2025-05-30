<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Services\GoogleService;

class ProviderController extends Controller
{
    /**
     * Redirect to the provider for authentication.
     */
    public function redirectToProvider(Request $request)
    {
        $providerValue = $request->provider;

        if (!in_array($providerValue, ['google', 'github'])) {
            return redirect()->route('welcome')->with('error', 'Unsupported provider.');
        }

        // Save previous URL to redirect after login
        session()->put('previous_url', url()->previous());

        return Inertia::location(Socialite::driver($providerValue)
            ->scopes([
                'https://www.googleapis.com/auth/webmasters.readonly',
                'https://www.googleapis.com/auth/analytics.readonly',
                'openid',
                'email',
                'profile'
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent'
            ])
            ->redirect());
    }

    /**
     * Handle the provider callback.
     */
    public function handleProviderCallback(Request $request)
    {
        $providerValue = $request->provider;

        if (!in_array($providerValue, ['google', 'github'])) {
            return redirect()->route('welcome')->with('error', 'Unsupported provider.');
        }

        $previousUrl = session()->pull('previous_url', route('welcome'));

        $providerUser = Socialite::driver($providerValue)->user();

        DB::beginTransaction();

        // Check if provider account exists
        $provider = Provider::where('provider', $providerValue)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($provider) {
            $user = $provider->user;
        } else {
            // Check if user exists with same email
            $user = User::where('email', $providerUser->getEmail())->first();

            if (!$user) {
                $deviceInfo = (new User)->getDeviceInfo();

                $user = User::create([
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                    'first_name' => $providerUser->user['given_name'] ?? null,
                    'last_name' => $providerUser->user['family_name'] ?? null,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'avatar' => $providerUser->getAvatar(),
                    'registration_ip' => $deviceInfo['registration_ip'],
                    'browser' => $deviceInfo['browser'],
                    'platform' => $deviceInfo['platform'],
                    'device' => $deviceInfo['device'],
                ]);
            }

            $google = new GoogleService([
                'provider_token' => $providerUser->token,
                'refresh_token' => $providerUser->refreshToken,
            ]);

            $accounts = $google->getAccounts();
            $sites = $google->getWebmasterSites();

            foreach ($sites as $site) {
                $url = $site->getSiteUrl();
                $permission = $site->getPermissionLevel();

                // Store only if not already added
                Website::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'domain'  => $url,
                    ],
                    [
                        'name'       => $permission,
                        'disabled'   => true,
                        'created_at' => now(),
                    ]
                );
            }

            $properties = [];

            if (!empty($accounts)) {
                $accountId = $accounts[0]['name'] ?? null;
                $properties = $accountId ? $google->getProperties($accountId) : [];
            }


            $provider = Provider::updateOrCreate(
                [
                    'provider' => $providerValue,
                    'provider_id' => $providerUser->getId(),
                ],
                [
                    'user_id' => $user->id,
                    'provider_token' => $providerUser->token,
                    'refresh_token' => $providerUser->refreshToken,
                    'avatar' => $providerUser->getAvatar(),
                    'name' => $providerUser->getName(),
                    'nickname' => $providerUser->getNickname(),
                    'properties' => json_encode($properties),
                ]
            );


            if (!$user->avatar) {
                $user->update(['avatar' => $providerUser->getAvatar()]);
            }
        }


        // Optional: update location info
        $user->updateLocationData();

        DB::commit();

        Auth::login($user);

        return redirect($previousUrl)->with('success', 'Logged in successfully.');
    }
}
