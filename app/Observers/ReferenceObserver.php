<?php

namespace App\Observers;

use App\Notifications\{ConsultedToAvailedSellerNotification, OnboardedToPaidSellerNotification};
use Homeful\References\Models\Reference;
use App\Actions\GetSellerCommissionCode;
use Illuminate\Support\Arr;

class ReferenceObserver
{
    /**
     * This custom event is fired every time
     * the voucher or reference is
     * redeemed.
     *
     * @param Reference $reference
     * @return void
     */
    public function redeemed(Reference $reference): void
    {
        $user = $reference->owner;
        $contact = $reference->getContact();

        /** The following lines forms part of the algorithm */
        /** to get the project code and ultimately  */
        /** the seller commission code. */
        $a = Arr::get($reference->metadata, 'project.code');
        $b = null;
        if ($reference->redeemers) {
            if (isset($reference->redeemers->metadata)) {
                $b = Arr::get($reference->redeemers?->metadata, 'project_code');
            }
        }
        $project_code = is_null($b) ? $a : (($a == $b) ? $b : null);

        /** This is main reason for this listener, to attach the seller commission code to the contact */
        $contact->reference_code = GetSellerCommissionCode::run($user, $project_code);
        $contact->save();

        $user->notify(new ConsultedToAvailedSellerNotification($reference->code, $project_code));
        $user->notify(new OnboardedToPaidSellerNotification($reference->code));
    }
}
