<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Http;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function change_password_force(Request $request){
    //   dd($request->type);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        // dd($credentials);
        //update password
        $change_res = Http::asForm()
        ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/change_password', [
            'email' => $credentials['email'],
            'new_password' => $request['password'],
            'change_type' => 'change',
            'type' => $request->type
        ]);
        if($change_res->successful()){
            $data =$change_res->json();
            // dd($data);
            if($data['message']=="changed password"){
            $user = User::updateOrCreate(
                ['email' => $credentials['email']],
                [  
                 'password' => $data['password'],
                ]
             );
           }
            Auth::login($user);
            return redirect('/dashboard');
        }
    }
    //
    public function forgot_password(Request $request){
        // dd($request->email);
        $checkUser = User::where('email',$request->email)->first();
        if($checkUser){
            $type = explode('-',$checkUser->seller_id);
            $change_res = Http::asForm()
            ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
            ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/forgot_password', [
                'email' => $request->email,
                'change_type' => 'forgot',
                'type' => $type[1]
            ]);
            // dd($change_res->json());
            if($change_res->successful()){
                $data =$change_res->json();
                // dd($data);
                if($data['success']==0){
                
                // $user = User::updateOrCreate(
                //     ['email' => $request->email],
                //     [  
                //      'password' => $data['password'],
                //     ]
                //  );
               }
               $mailBody = [
                        "subject"=>"Temporary Password",
                        "name"=>$checkUser->name,
                        "password"=>$data['password']
               ];
               $recipient = $request->recipient?$request->recipient:$request->email;
            //    dd($recipient);
               Mail::to($recipient)->send(new SendMail("forgotPassworTemplate",$mailBody));
               return back()->with('status', 'Temporary Password has been send to your email.');
            }
        }
        // return ["message"=>"invalid email"];
        return back()->withErrors(['email' => 'This email is not registered in our system.']);
        
    }
    // dd($response->json());
    //
    public function update(Request $request): RedirectResponse
    {
        // $validated = $request->validate([
        //     'current_password' => ['required', 'current_password'],
        //     'password' => ['required', Password::defaults(), 'confirmed'],
        // ]);

        // $request->user()->update([
        //     'password' => Hash::make($validated['password']),
        // ]);

        // return back();
        
        //update in CS

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'current_password' => ['required'],
        ]);
        
        $response = Http::asForm()
        ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/seller_login_info', [
            'email' => $credentials['email'],
            'password' => $credentials['current_password'],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if($data['message'] === "Login successful"){ //papalitan sa CS
                $change_res = Http::asForm()
                ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
                ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/change_password', [
                    'email' => $credentials['email'],
                    'new_password' => $request->password,
                    'change_type' => 'change',
                    'type' => $request->type
                ]);
                if($change_res->successful()){
                    $data =$change_res->json();
                    $user = User::updateOrCreate(
                        ['email' => $credentials['email']],
                        [  
                            'password' => $data['password'],
                        ]
                    );
                    return redirect('/profile');
                }
            // Session::put('external_token', $data['data']['password']);
            // Session::put('external_user', $data['data']['email']);
    
            // Create or update a temporary user locally
           
            }
            else
            {
                return redirect()->back()->withErrors([
                    'message' => 'wrong_password',
                    'error' => 'The provided credentials are incorrect.',
                ]); 
    
                return back()->withErrors([
                    'email' => 'The provided credentials are incorrect.',
                ]);  
            }
        }
        return back()->withErrors([
            'email' => 'The provided credentials are incorrect.',
        ]);
    }
    
}
