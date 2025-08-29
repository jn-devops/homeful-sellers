<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EncryptController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\User; 
use Illuminate\Support\Facades\Crypt;
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
    public function store(LoginRequest $request): RedirectResponse
    {   
        // dd($request);
        $request->authenticate();
        $request->session()->regenerate();

        // Auth::login($user);
        // dd($request->authenticate());
        $force_pass_change = Auth::user()->change_password;
        if(Auth::user()->change_password){
            return redirect('/change-password')->with('change_password_data', [
                "email"=> Auth::user()->email
            ]);
        }
        return redirect()->intended(route('dashboard', absolute: false));
        // return redirect('/dashboard');
        // return redirect()->intended(route('voucher.create', absolute: false));
    }
public function first_login($credential){

    $decrypt = EncryptController::decrypt($credential);
    $credentials = explode('|', $decrypt);

    // Create a manual request and bind session
    $request = Request::create('/login', 'POST', [
        'email' => $credentials[0],
        'password' => $credentials[1],
    ]);

    // Manually bind the session to the request
    $request->setLaravelSession(app('session')->driver());

    // Convert to LoginRequest (if needed for validation/authenticate logic)
    $loginRequest = LoginRequest::createFrom($request);

    // Bind session again just in case
    $loginRequest->setLaravelSession(app('session')->driver());

    return $this->store($loginRequest);
    
        // dd($credential);
    $request = new Request;
    $decrypt = EncryptController::decrypt($credential);
    // return $encrypt;
    $credential = explode('|',$decrypt);
    $request->email = $credential[0];
    $request->password = $credential[1];
    $request = new LoginRequest([
        'email' => $credential[0],
        'password' => $credential[1],
    ]);
    // dd($request);

    $reponse = $this->store($request);
    return $reponse;
    }
public function storeCS(Request $request)
{   
    // dd($request);
    // Validate user input

    // $credentials = $request->validate([
    //     'email' => ['required', 'email'],
    //     'password' => ['required'],
    // ]);
    $credentials = [
        'email' => $request->email,
        'password' => $request->password
    ];
    // dd($credentials);
    // $response = Http::asForm()->post('https://elanvital.enclavewrx.io/developers/api_storefront_seller/seller_login_info/admin/password', [
    //     'email' => $credentials['email'],
    //     'password' => $credentials['password'],
    // ]);

    //start removed 081525

    $response = Http::asForm()
    ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
    ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/seller_login_info/admin/password', [
        'email' => $credentials['email'],
        'password' => $credentials['password'],
    ]);

    //end removed 081525
    // dd($response->json());
    if ($response->successful()) {
        $data = $response->json();
        // dd($data['data']['contact_number']);
        if($data['message'] === "Login successful"){ //papalitan sa CS
        
        // Session::put('external_token', $data['data']['password']);
        // Session::put('external_user', $data['data']['email']);
        // Create or update a temporary user locally
        $user = User::updateOrCreate(
            ['email' => $data['data']['email']],
            [   
                'name' => $data['data']['first_name']. " " . $data['data']['last_name'], 
                'seller_id' => $data['data']['id'],
                'seller_commission_code' => $data['data']['SalesForceCode'],
                'password' => $credentials['password'],
                'contact' => $data['data']['contact_number']
            ]
        );
        if($data['data']['change_password'] == 1) {
            // Session::put('change_password_data', $data['data']);
            // $data['data']['password']= $credentials['password'];
            $type=explode('-',$data['data']['id']);
            // return redirect('/change-password')->with('change_password_data', $data['data']);
            return redirect('/change-password')->with('change_password_data', [
                "email"=> $credentials['email'],
                "type"=>$type[1]
            ]);
            // return redirect('/change-password');
        }
        //create user delete on logout
        Auth::login($user);
        // dd($user);
        return redirect('/dashboard');
        }
        else
        {

            return back()->withErrors([
                'message' => 'The provided credentials are incorrect.',
                'status' => 'wrong password'
            ]);  
        }
    }
    return back()->withErrors([
        'message' => 'The provided credentials are incorrect.',
        'status' => 'wrong password'
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
