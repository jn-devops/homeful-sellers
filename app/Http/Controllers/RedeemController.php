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
        RedeemVoucherCode::run($voucher);
        $voucher->refresh();
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
