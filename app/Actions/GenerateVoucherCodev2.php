<?php

namespace App\Actions;

use Homeful\References\Facades\References;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Project;
use Homeful\References\Models\Input;
use Homeful\Contacts\Models\Customer as Contact;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GenerateVoucherCodev2
{
    use AsAction;

    protected static Reference $reference;
    protected static Contact $contact;

    public function handle(string $contact_reference_code, Project|string $project_code = null, array $user ): string|false
    {
        // dd(SyncContact::run($contact_reference_code));
        // return $user['email'];
        // $project_code ="Pasinaya Homes Magalang Pampanga";
        $project = $project_code instanceof Project
            ? $project_code
            : Project::where('code', $project_code)->first();//change to name to code
        // $seller_commission_code = GetSellerCommissionCode::run($user, $project);
        $seller_commission_code = $user['seller_commission_code'];
        // return  $contact_reference_code ;
        $entities = [
            'input' => app(Input::class)->create(compact('seller_commission_code')),
            'contact' => self::$contact = SyncContactv2::run($user,$contact_reference_code)
        ];
        // return $entities;
        // $curr_user = Auth::user();
        // return $user;
        // return User::where('email', $user['email'])->first()?->toArray();
        self::$reference = References::withEntities(...$entities)
            ->withOwner(User::where('email', $user['email'])->first())
            ->withMetadata(['project' => $project])
            ->withStartTime(now())
            ->create();
        $reference_code = self::$reference?->code;
        // return "pass";
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
