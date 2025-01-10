<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\References\Facades\References;
use Homeful\References\Models\Reference;
use App\Actions\GetSellerReferenceCode;
use Homeful\Properties\Models\Project;
use Homeful\Contacts\Models\Contact;
use Homeful\References\Models\Input;
use App\Models\User;

uses(RefreshDatabase::class, WithFaker::class);

test('use has cascading seller commission codes', function() {
    $user = User::factory()->create(['seller_commission_code' => $seller_commission_code1 = fake()->word()]);
    expect(Project::all())->toHaveCount(0);
    expect(GetSellerReferenceCode::run($user))->toBe($seller_commission_code1);
    expect(GetSellerReferenceCode::run($user, fake()->word))->toBe($seller_commission_code1);
    [$project1, $project2] = Project::factory(2)->create();
    $user->projects()->attach($project1,[
        'seller_commission_code' => $seller_commission_code2 = fake()->word(),
    ]);
    $user->projects()->attach($project2,[
        'seller_commission_code' => $seller_commission_code3 = fake()->word(),
    ]);
    expect(GetSellerReferenceCode::run($user, $project1->code))->toBe($seller_commission_code2);
    expect(GetSellerReferenceCode::run($user, $project2->code))->toBe($seller_commission_code3);
});

test('reference an input and contact', function () {
    $user = User::factory()->create();
    $seller_commission_code = fake()->word();
    //TODO: get contact from homeful id
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
    $user_contact_pivot = $user->contacts()->whereKey($contact)->first()?->pivot;
    expect($user_contact_pivot->seller_commission_code)->toBe($seller_commission_code);
    expect($user_contact_pivot->invited_at)->toBe($reference->create_at);
});
