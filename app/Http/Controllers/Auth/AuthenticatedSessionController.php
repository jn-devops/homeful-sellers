<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\User; // Adjust based on your user model location
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();
    //     $request->session()->regenerate();
    //     return redirect()->intended(route('voucher.create', absolute: false));
    // }

public function store(Request $request)
{
    // Validate user input
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // $response = Http::asForm()->post('https://elanvital.enclavewrx.io/developers/api_storefront_seller/seller_login_info/admin/password', [
    //     'email' => $credentials['email'],
    //     'password' => $credentials['password'],
    // ]);
    $response = Http::asForm()
    ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
    ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/seller_login_info/admin/password', [
        'email' => $credentials['email'],
        'password' => $credentials['password'],
    ]);
    // dd($response->json());
    if ($response->successful()) {
        $data = $response->json();
     
        Session::put('external_token', $data['data']['password']);
        Session::put('external_user', $data['data']['email']);

        // Create or update a temporary user locally
        $user = User::updateOrCreate(
            ['email' => $data['data']['email']],
            [   
                'name' => $data['data']['Seller'], 
                'seller_id' => $data['data']['id'],
                'seller_commission_code' => $data['data']['SalesForceCode'],
                'password' => $data['data']['password'],
            ]
        );
        //create user delete on logout
        Auth::login($user);
        // dd($user);
        return redirect('/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials are incorrect.',
    ]);
}
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
