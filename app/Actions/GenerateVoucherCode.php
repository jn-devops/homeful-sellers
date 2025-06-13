<?php

namespace App\Actions;

use Homeful\References\Facades\References;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Project;
use Homeful\References\Models\Input;
use Homeful\Contacts\Models\Customer as Contact;
use App\Models\User;

class GenerateVoucherCode
{
    use AsAction;

    protected static Reference $reference;
    protected static Contact $contact;

    public function handle(User $user, string $contact_reference_code, Project|string $project_code = null): string|false
    {
        // dd(SyncContact::run($contact_reference_code));
        // return $user;
        // $project_code ="Pasinaya Homes Magalang Pampanga";
        $project = $project_code instanceof Project
            ? $project_code
            : Project::where('name', $project_code)->first();
        $seller_commission_code = GetSellerCommissionCode::run($user, $project);
    
        $entities = [
            'input' => app(Input::class)->create(compact('seller_commission_code')),
            'contact' => self::$contact = SyncContact::run($user,$contact_reference_code)
        ];
        self::$reference = References::withEntities(...$entities)
            ->withOwner($user)
            ->withMetadata(['project' => $project])
            ->withStartTime(now())
            ->create();
        $reference_code = self::$reference?->code;

        return $reference_code ?: false;
    }

    public function getReference(): Reference
    {
        return self::$reference;
    }

    public function getContact(): Contact
    {
        return self::$contact;
    }
}
