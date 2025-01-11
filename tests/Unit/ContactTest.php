<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Contacts\Models\Contact;
use App\Actions\SyncContact;

uses(RefreshDatabase::class, WithFaker::class);

test('sync contact works', function () {
    expect(Contact::all())->toHaveCount(0);
    $contact1 = SyncContact::run('H-3VT2MY');
    expect($contact1)->toBeInstanceOf(Contact::class);
    expect(Contact::all())->toHaveCount(1);
    $contact2 = SyncContact::run('H-3VT2MY');
    expect($contact2)->toBeInstanceOf(Contact::class);
    expect(Contact::all())->toHaveCount(1);
    expect($contact1->is($contact2))->toBeTrue();
});
