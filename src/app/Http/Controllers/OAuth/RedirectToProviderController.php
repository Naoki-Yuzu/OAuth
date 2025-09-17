<?php declare(strict_types=1);

namespace App\Http\Controllers\OAuth;

use App\Enums\SocialProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class RedirectToProviderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SocialProvider $provider): RedirectResponse
    {
        return Socialite::driver($provider->value)->redirect();
    }
}
