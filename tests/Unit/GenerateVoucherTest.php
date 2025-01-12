<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\References\Facades\References;
use Homeful\References\Models\Reference;
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
        [fn() => 'H-VLY8EV']
    ];
});

test('generate voucher code works', function (User $user, string $contact_reference_code) {
    expect($user->seller_commission_code)->toBe(getSellerCode());
    $action = app(GenerateVoucherCode::class);
    $voucher_code = $action->run($user, $contact_reference_code);
    $reference = app(Reference::class)->whereCode($voucher_code)->first();
    expect($reference)->toBeInstanceOf(Reference::class);
    expect($reference->getInput()->seller_commission_code)->toBe(getSellerCode());
    $contact = $reference->getContact();
    expect($action->getContact()->is($contact))->toBeTrue();
    expect($contact->reference_code)->toBe($contact_reference_code);

    $success = References::redeem($voucher_code, $user);
    expect($success)->toBeTrue();
})->with('user', 'contact_reference_code');
