<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\UserRole;
use App\Http\Controllers\DefaultSetupController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //
    public function index()
    {

        return Inertia::render('User/Index', [
            'users' => User::all(),
            'roles' => UserRole::all(),
            'sellers' => DefaultSetupController::getSellerCode()
        ]);
    }

    public function store(Request $request)
    {   
        $data = $request->validate([
            'name' => 'required|string|unique:users,name',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'birthdate' => 'nullable|date',
            'password' => 'required|string', 
            'email' => 'required|string|email|unique:users,email',
            'seller_code' => 'nullable|string',
            'contact' => 'required|string|unique:users,contact',
            'seller_commission_code' => 'required|string',
            'user_role_id' => 'nullable:array',//added ggvivar 
            // 'seller_group' => 'nullable:string',//added ggvivar
            'status' => 'nullable:integer',//added ggvivar
            'change_password' => 'nullable:integer',//added ggvivar
            'active' => 'nullable:integer'
        ]);
        $data['active']  = 1;
        $data['change_password'] = 1;
        // dd($data);
        $data['seller_code'] =  $data['seller_commission_code'].'-'.DefaultSetupController::getBrokerSeqNumbering($data['seller_commission_code']);
        User::create($data);

        if($request->send_email)
        {
        $bodyRequest =$data['email']."|".$data['password'];
        $url = config('app.url').'/authenticate/login/'.EncryptController::encrypt($bodyRequest);
  
        $mailBody = [
            "subject"=>"Seller Login",
            "seller_name"=>$data['first_name'],
            "username"=>$data['email'],
            "password"=>$data['password'],
            "sellerURL"=>config('app.url'),
            "url"=>$url
        ];
        $this->send_mail($mailBody, $data['email']);

        }
        return redirect()->back()->with('success', 'User created!');
    }

    public function update(Request $request, User $user)
    {
        // dd($user->seller_commission_code);
        $data = $request->validate([
            'name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'birthdate' => 'required|date',
            'password' => 'nullable|string',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'contact' => 'required|string|unique:users,contact,' . $user->id,
            'user_role_id' => 'integer|nullable',
            'seller_commission_code' => 'required|string',
            // 'seller_code' => 'nullable|string'
            'status' => 'nullable:integer',//added ggvivar
            'active' => 'nullable:integer'
        ]);
        // dd($data);
        $user->update($data);
        return redirect()->back()->with('success', 'User updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted!');
    }

    public function send_mail($mailBody,$recipient){
        
        Mail::to($recipient)->send(new SendMail("sellerTemplate",$mailBody));
        return back()->with('status', 'Email has been sent.');
    }
}
