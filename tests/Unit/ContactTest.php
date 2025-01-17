<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Contacts\Models\Contact;
use App\Actions\SyncContact;

uses(RefreshDatabase::class, WithFaker::class);

test('sync contact works', function () {
    expect(Contact::all())->toHaveCount(0);
    $contact1 = SyncContact::run(fake()->word());
    expect($contact1)->toBeFalse();
    $contact_reference_code = env('TEST_CONTACT_REFERENCE_CODE', 'H-CB6YWH');
    $contact1 = SyncContact::run($contact_reference_code);
    expect($contact1)->toBeInstanceOf(Contact::class);
    expect(Contact::all())->toHaveCount(1);
    $contact2 = SyncContact::run($contact_reference_code);
    expect($contact2)->toBeInstanceOf(Contact::class);
    expect(Contact::all())->toHaveCount(1);
    expect($contact1->is($contact2))->toBeTrue();
});
