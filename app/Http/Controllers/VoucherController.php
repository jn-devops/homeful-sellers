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
use Homeful\Properties\Models\Project;

class VoucherController extends Controller
{
    public function create(Request $request)
    {   

        $projects = Project::all();
        $voucher_input = [
        "reference_code"=>$request->contact_reference_code,
        "project"=>$request->project_code];
        return Inertia::render('Voucher/Create', ['projects' => $projects,'voucher_input' =>$voucher_input]);
    }

    public function store(GenerateVoucherRequest $request)
    {   
        // dd($request);
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
        // dd($contact->getData());
        return $contact->getData();
        // return app(Contacts::class)->fromContactModelToContactMetadata($contact);
    }
    public function generateVoucher(GenerateVoucherRequest $request)
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
}
