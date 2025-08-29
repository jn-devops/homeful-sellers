<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class APIController extends Controller
{
    public function Auth(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user,
        ]);
    }
    public static function authenticate_token($token)
    {
        return $token===config('homeful-sellers.api_token')?true:false;
    }

    public static function getBrokerList()
    {
        // $response = Http::asForm()
        // ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        // ->post('https://everyhome.enclavewrx.io/developers/api_storefront_seller/broker_list', [
        //     'email' => $credentials['email'],
        //     'password' => $credentials['password'],
        // ]);

        $response = Http::asForm()
        ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        ->get('https://everyhome.enclavewrx.io/developers/api_storefront_seller/seller_list_NOAH', [

        ]);
        return $response;
    }
}
