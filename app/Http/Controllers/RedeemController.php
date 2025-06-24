<?php

namespace App\Http\Controllers;

use App\Actions\GetSellerCommissionCode;
use App\Models\User;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Project;
use App\Actions\RedeemVoucherCode;
use Illuminate\Support\Carbon;

class RedeemController extends Controller
{
    public function validated_voucher($voucher)
    {
        $date = Carbon::now();
        // Carbon::now();
        // dd($date);025-06-11 02:22:39
        $voucher = Reference::where('code',$voucher)->
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
                'seller_name' => $user->name,
                'status' => "Success"
                ];
        }
        else{
            return  [
                'message' => "voucher is not existing, redeemed or expired",
                'seller_commission_code' => null,
                'seller_name' => null,
                'status' => "Failed"
                ];
        }
    }
    public function redeem($voucher_code)
    {
        $date = Carbon::now();
        // Carbon::now();
        // dd($date);025-06-11 02:22:39
        $voucher = Reference::where('code',$voucher_code)
        // ->whereDate('created_at', '>', $date)
        ->first();
        // dd($voucher);
        if($voucher)
        {   
            $voucher = app(Reference::class)->updateOrCreate(['code'=>$voucher_code], ['redeemed_at'=>$date]);
            $user = User::where('id',$voucher['owner_id'])->first();
            //update ako status ng buyer seller \
            return  [
                'message' => "Success",
                'seller_code' => $user->meta->seller_commission_code,
                'seller_name' => $user->name
                ];
        }
        else{
            return  [
                'message' => "voucher is not existing or expired",
                'seller_commission_code' => null];
        }
    }
}
