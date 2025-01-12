<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateVoucherRequest;
use App\Actions\GenerateVoucherCode;
use Inertia\{Inertia, Response};
use Illuminate\Http\Request;
use Homeful\Contacts\Classes\ContactMetaData;
use Homeful\Contacts\Data\ContactData;
//use Homeful\Contacts\Facades\Contacts;
use Homeful\Contacts\Contacts;

class VoucherController extends Controller
{
    public function create()
    {
        return Inertia::render('Voucher/Create');
    }

    public function store(GenerateVoucherRequest $request)
    {
        $args = array_merge(['user' => $request->user()], $request->validated());
        $action = app(GenerateVoucherCode::class);
        $voucher_code = $action->run(...$args);

        return redirect()->back()->with('event', [
            'name' => 'voucher_generated',
            'data' => [
                'code' => $voucher_code,
                'contact' => $this->getContactData($action)
            ]
        ]);
    }

    protected function getContactData(GenerateVoucherCode $action)
    {
        $contact = $action->getContact();

        return app(Contacts::class)->fromContactModelToContactMetadata($contact);
    }
}
