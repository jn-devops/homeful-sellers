<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateVoucherRequest;
use App\Actions\GenerateVoucherCode;
use App\Models\User;
use App\Notifications\OnboardedToPaidSellerNotification;
use FrittenKeeZ\Vouchers\Models\VoucherEntity;
use Homeful\Contacts\Models\Contact;
use Homeful\References\Models\Reference;
use Inertia\{Inertia, Response};
use Illuminate\Http\Request;
use Homeful\Contacts\Classes\ContactMetaData;
use Homeful\Contacts\Data\ContactData;
//use Homeful\Contacts\Facades\Contacts;
use Homeful\Contacts\Contacts;
use Homeful\Properties\Models\Project;
use Illuminate\Support\Facades\Http;

class VoucherController extends Controller
{
    public function create()
    {
        $projects = Project::all();

        return Inertia::render('Voucher/Create', ['projects' => $projects]);
    }

    public function store(GenerateVoucherRequest $request)
    {
        $args = array_merge(['user' => $request->user()], $request->validated());
        $action = app(GenerateVoucherCode::class);
        $voucher_code = $action->run(...$args);
        $request =Http::withToken(config('homeful-sellers.keys.contact_key'))->post(config('homeful-sellers.end-points.contact').'api/get-contact-by-homeful-id', [
            'code' => $voucher_code,
        ]);

        return redirect()->back()->with('event', [
            'name' => 'voucher_generated',
            'data' => [
                'code' => $voucher_code,
                'contact' => $request->json('data')
            ]
        ]);
    }

    protected function getContactData(GenerateVoucherCode $action)
    {
        $contact = $action->getContact();

        return $contact->getData();
//        return app(Contacts::class)->fromContactModelToContactMetadata($contact);
    }

    public function BuyerPaidNotification(Request $request){
        $reference = Reference::where('code',$request->code)->firstOrFail();
        $user = $reference->owner;
        $user->notify(new OnboardedToPaidSellerNotification($reference->code));
    }
}
