<?php

namespace App\Http\Controllers;

use App\Actions\GetSellerCommissionCode;
use App\Models\User;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Project;
use App\Actions\RedeemVoucherCode;

class RedeemController extends Controller
{
    public function __invoke(Reference $voucher, Project|string $project = null): array|false
    {

        // dd($project);
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
