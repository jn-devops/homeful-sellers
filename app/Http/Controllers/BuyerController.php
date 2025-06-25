<?php

namespace App\Http\Controllers;


use Homeful\Contacts\Models\Customer as Contact;
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
    public function match(Request $request){
        $body = $request->json();
        $projects = $this->get_projects();
        // dd($projects);
        //get project 
        $proj_name = "";
        foreach($projects as $proj => $name){
            if($name['code']===$request->project_code){
                $proj_name = $name['name'];
                break;
            }
        }
        // dd($proj_name);
        $response = Http::acceptJson()->asForm()->get(config('homeful-sellers.api-urls.match').'/api/match',
        [
            "date_of_birth"=> $request->date_of_birth,
            "monthly_gross_income" =>$request->monthly_gross_income,
            "region" => "NCR",
            "verbose"=>1,
            "limit" => 999999999,//to bypass limit
        ]);
        
        $res_proj = $response->json();
        // dd($res_proj);
        $result=[];
        $suggestions=[];
        foreach($res_proj as $project => $value)
        {   
            // echo $value['name'].'\n';
            if(strtoupper($value['name'])===strtoupper($proj_name))
            {
                $result=[
                    "match" => true,
                    "suggestions" => []
                ];
                break;
            }
        }
        if($result){
           return $result;
        }
        else{
            foreach($res_proj as $project => $value)
            {   
                $exists=false;
                foreach($suggestions as $suggest => $val)
                {
                    if($value['name']===$val['name'])
                    {
                        $exists = true;
                        break;
                    }
                }
                if($exists==false){
                    $suggestions[]=[
                        'name' => $value['name']
                    ];
                }
               
            }
            $result=[
                "match" => false,
                "suggestions" => $suggestions
            ];
        }
        return $result;
    }
    public function edit(Request $request): Response
    {   
        // $projects = Project::all();
        // if (strpos($request->user()->meta->seller_id, '-') !== false) {
        //     $split = explode('-', $request->user()->meta->seller_id);
        //     $id = $split[0];
        //     $table = $split[1];
        // }
        // dd($projects);
    
        // dd($listProject);
        $listProject = $this->get_projects();
        // dd($listProject);
        return Inertia::render('Buyer/Register', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'projects' => $listProject,
            'status' => session('status'),
        ]);
    }

    public function updateBuyer(Request $request)
    {
    $validated = $request->validate([
        'contact_reference_code' => 'required|string',
        'match_link' => 'required|url'
    ]);

    $attributes = [
        'other_mobile' => $validated['match_link']
    ];

    $keys = [
        'reference_code' => $validated['contact_reference_code'],
    ];
    $contact = Contact::updateOrCreate($keys, $attributes);
    return response()->json([
        'success' => true,
        'contact' => $contact,
    ]);
    }
    public function get_projects()
    {
    $prop_url = config('homeful-sellers.api-urls.match');
        $proj_res = Http::asForm()
        // ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e') // Replace with your actual token
        // ->get('https://everyhome.enclavewrx.io/developers/api_storefront_seller/get_seller_projects/'.$id.'/'.$table);
        ->get(config('homeful-sellers.api-urls.property').'/fetch-projects');
        $projects = $proj_res->json();
        $prod_res = Http::asForm()
        ->get(config('homeful-sellers.api-urls.property').'/fetch-products');
        $products = $prod_res->json();
        // dd($products);
        $listProject=[];
        foreach($projects['projects'] as $project => $value)
        {
            foreach($products['products'] as $product => $val)
            {
                // dd($val);
                if($val['project_code']==$value['code'] && $val['phased_out']==false)
                {
                    $exists = false;
                    foreach ($listProject as $proj_list => $i) 
                    {
                        if($i['code']===$value['code'])
                        {
                            $exists = true;
                            break;
                        }
                    }
                    if($exists==false){
                        $listProject[]=[
                            'code' => $value['code'],
                            'name' => $value['name']
                        ];
                        break;
                    }
                }
            }
        }
        return $listProject;
    }
}
