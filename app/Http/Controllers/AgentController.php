<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use App\Http\Controllers\DefaultSetupController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class AgentController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

        // dd($user);
        return Inertia::render('Agent/Index', [
            'users' => User::where('meta->seller_commission_code',$user->meta->seller_commission_code)->get(),
            'roles' => UserRole::all(),
            // 'sellers' => DefaultSetupController::getSellerCode()
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        $data = $request->validate([
            'name' => 'required|string|unique:users,name',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'contact' => 'required|string|unique:users',
            'user_role_id' => 'integer|nullable',
            'seller_commission_code' => 'required|string',
        ]);
        // dd($request->getContent());
        // dd($data);
        User::create($data);
        return redirect()->back()->with('success', 'User created!');
    }

    public function update(Request $request, User $user)
    {
        
        $data = $request->validate([
            'name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'contact' => 'required|string|unique:users,contact,' . $user->id,
            'user_role_id' => 'integer|nullable',
            'seller_commission_code' => 'required|string',
        ]);
    
        $user->update($data);
    
        // dd($data);
        // $user->update($data);
        // dd($user);
        return redirect()->back()->with('success', 'User updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted!');
    }
}
