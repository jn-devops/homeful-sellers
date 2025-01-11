<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\References\Models\Reference;
use Homeful\Contacts\Models\Contact;
use App\Actions\GenerateVoucherCode;
use App\Models\User;

uses(RefreshDatabase::class, WithFaker::class);

function getSellerCode(): string
{
    return 'ABC-123';
}

dataset('user', function () {
    return [
        [fn() => User::factory()->create(['seller_commission_code' => getSellerCode()])]
    ];
});

dataset('contact_reference_code', function () {
    return [
        [fn() => 'H-3VT2MY']
    ];
});

test('generate voucher code works', function (User $user, string $contact_reference_code) {
    expect($user->seller_commission_code)->toBe(getSellerCode());
    $action = app(GenerateVoucherCode::class);
    $voucher_code = $action->run($user, $contact_reference_code);
    $reference = app(Reference::class)->whereCode($voucher_code)->first();
    expect($reference)->toBeInstanceOf(Reference::class);
    expect($reference->getInput()->seller_commission_code)->toBe(getSellerCode());
    expect($contact = $reference->getContact()->is($action->getContact()))->toBeTrue();
})->with('user', 'contact_reference_code');
