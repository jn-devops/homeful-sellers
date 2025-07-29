<?php

namespace App\Http\Controllers;

use App\Actions\GetSellerCommissionCode;
use App\Http\Controllers\APIController;
use App\Models\User;
use Homeful\Contacts\Models\Contact;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Project;
use App\Actions\RedeemVoucherCode;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedeemController extends Controller
{   
    public function validated_voucher(Request $request)
    {
        $date = Carbon::now();
        $voucher_code = $request->voucher;

        $auth = APIController::authenticate_token($request->header('Authorization'));
        if($auth===false){
            return [
                'message' => "Invalid API Token",
            ];
        }
    //    dd($request->header('Authorization'));
        // Carbon::now();
        // dd($date);025-06-11 02:22:39
        $voucher = Reference::where('code',$voucher_code)->
        where('redeemed_at',Null)->first();
        // ->whereDate('created_at', '>', $date)
        // ->first();
        // dd($voucher);
        if($voucher)
        {
            $user = User::where('id',$voucher['owner_id'])->first();
            return  [
                'message' => "Success",
                'seller_code' => $user->meta->seller_commission_code,
                'seller_commission_code' => $user->meta->seller_commission_code,
                'seller_name' => $user->name,
                'status' => "Success"
                ];
            $contact = Contact::updateOrCreate($keys, $attributes);
        }
        else{
            return  [
                'message' => "voucher is not existing, redeemed or expired",
                'seller_code' => null,
                'seller_commission_code' => null,
                'seller_name' => null,
                'status' => "Failed"
                ];
        }
    }
    public function redeem(Request $request)
    {   
        $date = Carbon::now();
        $voucher_code = $request->voucher;
        
        // Carbon::now();
        // dd($date);025-06-11 02:22:39
        $auth = APIController::authenticate_token($request->header('Authorization'));
        if($auth===false){
            return [
                'message' => "Invalid API Token",
            ];
        }
        $voucher = Reference::where('code',$voucher_code)
        // ->whereDate('created_at', '>', $date)
        ->first();
        $voucher_entity = DB::table('voucher_entity')
        ->where('voucher_id',$voucher->id)
        ->where('entity_type',"Homeful\Contacts\Models\Customer")
        ->first();
        $buyer_details = Contact::where('id',$voucher_entity->entity_id)->get()->first();
        $buyer_details->current_status = "Paid";

        // dd($buyer_details->current_status);
        
        if($voucher)
        {   
            $voucher = app(Reference::class)->updateOrCreate(['code'=>$voucher_code], ['redeemed_at'=>$date]);
            $user = User::where('id',$voucher['owner_id'])->first();
            //update ako status ng buyer seller \
            // Contact::updateOrCreate($voucher_entity->entity_id, $buyer_details);
            Contact::updateOrCreate(
                ['id' => $voucher_entity->entity_id], // find by ID
                ['current_status' => 'Paid'] // update status
            );
            
            return  [
                'message' => "Success",
                'seller_code' => $user->meta->seller_commission_code,
                'seller_commission_code' => $user->meta->seller_commission_code,
                'seller_name' => $user->name,
                'status' => "Success"
                ];
        }
        else{
            return  [
                'message' => "voucher is not existing or expired",
                'seller_commission_code' => null
                ];
        }
    }
}
