<?php

namespace App\Actions;

use Homeful\References\Facades\References;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Input;
use Homeful\Contacts\Models\Contact;
use App\Models\User;

class GenerateVoucherCode
{
    use AsAction;

    protected static Contact $contact;

    public function handle(User $user, string $contact_reference_code, string $project_code = ''): string|false
    {
        $seller_commission_code = GetSellerReferenceCode::run($user, $project_code);
        $entities = [
            'input' => app(Input::class)->create(compact('seller_commission_code')),
            'contact' => self::$contact = SyncContact::run($contact_reference_code)
        ];
        $reference_code = References::withEntities(...$entities)->withStartTime(now())->create()?->code;

        return $reference_code ?: false;
    }

    public function getContact(): Contact
    {
        return self::$contact;
    }
}
