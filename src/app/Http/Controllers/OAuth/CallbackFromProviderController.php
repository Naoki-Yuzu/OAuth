<?php declare(strict_types=1);

namespace App\Http\Controllers\OAuth;

use App\Enums\SocialProvider;
use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class CallbackFromProviderController extends Controller
{
    public function __construct(
        private AuthManager $auth,
    )
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, SocialProvider $provider): JsonResponse
    {
        $userByOAuth = Socialite::driver($provider->value)->user();

        $socialAccount = SocialAccount::query()
            ->where([
                'provider_name' => $provider->name,
                'provider_id' => $userByOAuth->getId(),
            ])
            ->first();

        if ($socialAccount) {
            $this->auth->guard()->login($socialAccount->user);
            $request->session()->regenerate();

            return new JsonResponse([
                'data' => [
                    'id' => $socialAccount->user->id,
                    'name' => $socialAccount->user->name,
                    'email' => $socialAccount->user->email,
                ],
                'message' => 'Already social linked.',
            ]);
        }

        $user = User::query()
            ->firstOrCreate(
                [
                    'email' => $userByOAuth->getEmail(),
                ],
                [
                    'name' => $userByOAuth->getName(),
                    'password' => Hash::make(Str::random()),
                ]
            );


        $user->socialAccounts()->create([
            'provider_name' => $provider->value,
            'provider_id' => $userByOAuth->getId(),
            'provider_token' => $userByOAuth->token,
            'provider_refresh_token' => $userByOAuth->refreshToken,
        ]);

        $this->auth->guard()->login($user);
        $request->session()->regenerate();

        return new JsonResponse([
            'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'message' => 'Successful social linked.',
        ]);
    }
}
