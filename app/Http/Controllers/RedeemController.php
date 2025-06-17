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
        $voucher = Reference::where('code',$voucher)
        ->whereDate('created_at', '>', $date)
        ->first();
        dd($voucher);
        if($voucher)
        {
            $user = User::where('id',$voucher['owner_id'])->first();
            return  [
                'message' => "Success",
                'seller_commission_code' => $user->meta->seller_commission_code,
                ];
        }
        else{
            return  [
                'message' => "voucher is not existing or expired",
                'seller_commission_code' => null];
        }
    }
    public function redeem($voucher)
    {
        $voucher = Reference::where('code',$voucher)->first();
        if($voucher)
        {
            $user = User::where('id',$voucher['owner_id'])->first();
            // dd($user);
            return  [
                    'message' => "Success",
                    'seller_commission_code' => $user->meta->seller_commission_code,
                    ];
        }
        else{
            return  [
                    'message' => "voucher is not existing or expired",
                    'seller_commission_code' => null];
        }

        $project = Project::where('code',$project)->first();
        RedeemVoucherCode::run($voucher,[
            "project"=>$project->toArray(),
            "project_code"=>$project->code
        ]);
        $voucher->refresh();
        $voucher->save();

        $user = $voucher->owner;

        $seller_commission_code = $user instanceof User
            ? GetSellerCommissionCode::run($user, $project)
            : null;
        return $voucher->isRedeemed()
            ? ['seller_commission_code' => $seller_commission_code]
            : false
            ;
    }
}
