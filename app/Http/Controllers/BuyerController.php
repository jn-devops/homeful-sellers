<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Homeful\Properties\Models\Project;
use Inertia\Response;
use Inertia\Inertia;
class BuyerController extends Controller
{
    public function edit(Request $request): Response
    {   
        // $projects = Project::all();
        if (strpos($request->user()->meta->seller_id, '-') !== false) {
            $split = explode('-', $request->user()->meta->seller_id);
            $id = $split[0];
            $table = $split[1];
        }
        // dd($projects);
        $response = Http::asForm()
        // ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        ->get('https://everyhome.enclavewrx.io/developers/api_storefront_seller/get_seller_projects/'.$id.'/'.$table);
        $projects = $response->json();
        // dd($projects);
        return Inertia::render('Buyer/Register', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'projects' => $projects,
            'status' => session('status'),
        ]);
    }
}
