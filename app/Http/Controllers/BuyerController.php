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
use App\Models\User;
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
        $end_point_contracts = config('homeful-sellers.end-points.contract');
        // dd($listProject);
        return Inertia::render('Buyer/Register', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'projects' => $listProject,
            'end_point_contract' => $end_point_contracts,
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
    public function syncBuyer($referenceCode)
    {
        
        $url = config('homeful-sellers.end-points.loan_processing')."contact/get-contact-by-homeful-id/{$referenceCode}";
        try {
            $response = Http::withHeaders([
                'Authorization' => config('homeful-sellers.api_loan_process'),
                'Accept' => 'application/json'
            ])->get($url);
            $data = $response->json();
            // return $data['data'];// convert to array
            $user = Contact::updateOrCreate(
                ['reference_code' => $referenceCode],
                ['current_status' => $data['data']['current_status']] // update status
                
            );
            return $user;
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch data', 'message' => $e->getMessage()]);
        }
    }
    public static function getAttachment($referenceCode)
    {
        $url = config('homeful-sellers.end-points.loan_processing')."api/contact/get-attachment/{$referenceCode}";
        try {
            $response = Http::withHeaders([
                'Authorization' => config('homeful-sellers.api_loan_process'),
                'Accept' => 'application/json'
            ])->get($url);
            return $response->json();
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch data', 'message' => $e->getMessage()]);
        }
    }
    public function sync($response)
    {
        return $response;
    }
    public function index(){
        $seller_commission_code = "%".auth::user()->seller_commission_code."%";
        $buyers = Contact::where('landline', "like" , $seller_commission_code)
        ->orderBy('created_at', 'desc')
        ->get();
        $buyerList = [];
        $list_attachment = [];
        $pending_document =[];
            foreach ($buyers as $buyer) {
                $documents = BuyerController::getAttachment($buyer->reference_code);
                // dd($documents);
                if($documents['success']){
                    // dd($documents['data']);
                    $list_attachment[] = $documents['data']['list_attachment'];
                    $pending_document =[];
                //    dd(($list_attachment));
                    if(count($list_attachment)){
                        foreach($list_attachment[0] as $attachment)
                        {
                            if($attachment['exists']!==true){
                                // dd($attachment);
                                $pending_document[]=['code'=>$attachment['code'],'name'=>$attachment['name']];
                            }
                        //waiting for update sa document list 
                        }

                    }
                
                }
                $buyerList[] = [$buyer,$documents['success']?$documents['data']:[],$pending_document];
            
    }
      return Inertia::render('Buyer/Index', [
            'buyers' => $buyerList,
            'brokers' => User::where('meta->seller_commission_code',auth::user()->seller_commission_code)->get
        ]);
    }
    public function lead(){
        $buyerList = Contact::
        where('created_at', '<=', now()->subDays(7))
        ->whereNull('current_status')
        ->get();
        // dd($buyerList);
        return Inertia::render('Buyer/Lead', [
            'buyers' =>$buyerList,
            'brokers' => User::all()
        ]);
    }
    public function update(Request $request, Contact $user)
    {
        // dd($request->reference_code);
        Contact::updateOrCreate(
            ['reference_code' => $request->reference_code],
            [ 'landline' => $request->seller_email.'/'.$request->seller_commission_code] // update status
        );
        // try {
        //     $validated = $request->validate([
        //         'reference_code' => 'required|string',
        //         'first_name' => 'required|string',
        //         'middle_name' => 'nullable|string',
        //         'last_name' => 'required|string',
        //         'landline' => 'nullable|string',
        //         'email' => 'required|string|email|unique:contacts,email,' . $user->id,
        //         'mobile' => 'required|string|unique:contacts,mobile,' . $user->id,
        //     ]);
        //     dd($validated);
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     dd($e->errors()); // dump validation errors
        // }
        // $user->update($data);
        return redirect()->back()->with('success', 'User updated!');
    }
}
