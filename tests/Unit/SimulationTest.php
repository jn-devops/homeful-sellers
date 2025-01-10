<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\References\Facades\References;
use Homeful\References\Models\Reference;
use Homeful\Contacts\Models\Contact;
use Homeful\References\Models\Input;
use App\Models\User;

uses(RefreshDatabase::class, WithFaker::class);

test('reference an input', function () {
    $user = User::factory()->create();
    $seller_commission_code = fake()->word();
    $entities = [
        'input' => Input::create(compact('seller_commission_code')),
        'contact' => Contact::factory()->create()
    ];
    $reference_code = References::withEntities(...$entities)->withStartTime(now())->create()->code;
    $reference = Reference::where('code', $reference_code)->first();
    $input = $reference->getInput();
    $contact = $reference->getContact();
    $user->contacts()->attach($contact, [
        'seller_commission_code' => $input->seller_commission_code,
        'invited_at' => $reference->create_at
    ]);

    $pivotRecord = $user->contacts()
        ->whereKey($contact)
        ->first()?->pivot;

    expect($pivotRecord->seller_commission_code)->toBe($seller_commission_code);
    expect($pivotRecord->invited_at)->toBe($reference->create_at);
});
