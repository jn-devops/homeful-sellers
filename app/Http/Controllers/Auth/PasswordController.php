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
use App\Http\Controllers\EncryptController;
use Illuminate\Support\Str;
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
        //start move CS updating 08/25/2025
        //update password
        // $change_res = Http::asForm()
        // ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        // ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/change_password', [
        //     'email' => $credentials['email'],
        //     'new_password' => $request['password'],
        //     'change_type' => 'change',
        //     'type' => $request->type
        // ]);
        // if($change_res->successful()){
        //     $data =$change_res->json();
        //     // dd($data);
        //     if($data['message']=="changed password"){
        //     $user = User::updateOrCreate(
        //         ['email' => $credentials['email']],
        //         [  
        //          'password' => $data['password'],
        //         ]
        //      );
        //    }
        //     Auth::login($user);
        //     return redirect('/dashboard');
        // }
        // end remove CS updating
            // dd($data);
            
            $user = User::updateOrCreate(
                ['email' => $credentials['email']],
                [  
                 'password' => $credentials['password'],
                 'change_password' => 0,
                ]
            );
           
            Auth::login($user);
            return redirect('/dashboard');
        
    }
    //
    public function forgot_password(Request $request){
        // dd($request->email);
        $checkUser = User::where('email',$request->email)->first();
        if($checkUser){

            //start remove CS updating 08/25/2025
            // $type = explode('-',$checkUser->seller_id);
            // $change_res = Http::asForm()
            // ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
            // ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/forgot_password', [
            //     'email' => $request->email,
            //     'change_type' => 'forgot',
            //     'type' => $type[1]
            // ]);
            // if($change_res->successful()){
            //     $data =$change_res->json();
            //     if($data['success']==0){
                
            //    }
            //    $bodyRequest =$request->email."|".$data['password'];

            //    $url = 'https://sellers-staging.homeful.ph/authenticate/login/'.EncryptController::encrypt($bodyRequest);
         
            //    $mailBody = [
            //             "subject"=>"Temporary Password",
            //             "name"=>$checkUser->name,
            //             "password"=>$data['password'],
            //             "quicklink"=>$url
            //    ];
            //    $recipient = $request->recipient?$request->recipient:$request->email;
          
            //    Mail::to($recipient)->send(new SendMail("forgotPassworTemplate",$mailBody));
            //    return back()->with('status', 'Temporary Password has been send to your email.');
            // }
            //end remove CS updating 
               $password = Str::random(8);
               $bodyRequest =$request->email."|".$password;
               $checkUser = User::updateOrCreate(
                ['email' => $request->email],
                [  
                 'password' => $password,
                 'change_password' => 1,
                ]
              );
               $url = config('app.url').'/authenticate/login/'.EncryptController::encrypt($bodyRequest);
         
               $mailBody = [
                        "subject"=>"Temporary Password",
                        "name"=>$checkUser->name,
                        "password"=>$password,
                        "quicklink"=>$url
               ];
               $recipient = $request->recipient?$request->recipient:$request->email;
          
               Mail::to($recipient)->send(new SendMail("forgotPassworTemplate",$mailBody));
               return back()->with('status', 'Temporary Password has been send to your email.');
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
            'password' =>['required'],
            'current_password' => ['required'],
        ]);
        //start remove CS 08/29/2025
        // $response = Http::asForm()
        // ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        // ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/seller_login_info', [
        //     'email' => $credentials['email'],
        //     'password' => $credentials['current_password'],
        // ]);

        // if ($response->successful()) {
        //     $data = $response->json();
        //     if($data['message'] === "Login successful"){ //papalitan sa CS
        //         $change_res = Http::asForm()
        //         ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        //         ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/change_password', [
        //             'email' => $credentials['email'],
        //             'new_password' => $request->password,
        //             'change_type' => 'change',
        //             'type' => $request->type
        //         ]);
        //         if($change_res->successful()){
        //             $data =$change_res->json();
        //             $user = User::updateOrCreate(
        //                 ['email' => $credentials['email']],
        //                 [  
        //                     'password' => $data['password'],
        //                 ]
        //             );
        //             return redirect('/profile');
        //         }
           
        //     }
        //     else
        //     {
        //         return redirect()->back()->withErrors([
        //             'message' => 'wrong_password',
        //             'error' => 'The provided credentials are incorrect.',
        //         ]); 
    
        //         return back()->withErrors([
        //             'email' => 'The provided credentials are incorrect.',
        //         ]);  
        //     }
        // }
        // return back()->withErrors([
        //     'email' => 'The provided credentials are incorrect.',
        // ]);
        //end remove CS
       
        if (Hash::check($credentials['current_password'], Auth::user()->password)) {
            User::updateOrCreate(
                                ['email' => $credentials['email']],
                                [  
                                    'password' => $credentials['password'],
                                ]
                            );
            return redirect('/profile');
        } else {
            return back()->withErrors([
                    'email' => 'The provided credentials are incorrect.',
            ]);
        }
    }
    
    
}
