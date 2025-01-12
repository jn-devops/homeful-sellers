<?php

namespace App\Http\Controllers;

use Homeful\References\Models\Reference;
use App\Actions\RedeemVoucherCode;

class RedeemController extends Controller
{
    public function __invoke(Reference $voucher): array|false
    {
        RedeemVoucherCode::run($voucher);
        $voucher->refresh();
        $contact = $voucher->getContact();

        return $voucher->isRedeemed()
            ? ['seller_commission_code' => $contact->reference_code]
            : false
            ;
    }
}
