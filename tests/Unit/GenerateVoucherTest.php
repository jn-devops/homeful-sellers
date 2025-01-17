<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\References\Facades\References;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Project;
use App\Actions\GenerateVoucherCode;
use App\Actions\RedeemVoucherCode;
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
        [fn() => env('TEST_CONTACT_REFERENCE_CODE', 'H-CB6YWH')]
    ];
});

test('generate voucher code works', function (User $user, string $contact_reference_code) {
    expect($user->seller_commission_code)->toBe(getSellerCode());
    $action = app(GenerateVoucherCode::class);
    $project_code = null;
    $voucher_code = $action->run($user, $contact_reference_code, $project_code);
    $reference = app(Reference::class)->whereCode($voucher_code)->first();
    expect($reference)->toBeInstanceOf(Reference::class);
    expect($reference->owner->is($user))->toBeTrue();
    expect($reference->getInput()->seller_commission_code)->toBe(getSellerCode());
    $contact = $reference->getContact();
    expect($action->getReference()->is($reference))->toBeTrue();
    expect($action->getContact()->is($contact))->toBeTrue();
    expect($contact->reference_code)->toBeNull();
    $success = References::redeem($voucher_code, $user);
    expect($success)->toBeTrue();
    $contact->refresh();
    expect($contact->reference_code)->toBe(getSellerCode());
})->with('user', 'contact_reference_code');

test('redeem via action', function (User $user, string $contact_reference_code) {
    $project = Project::factory()->create(['code' => 'AA-0317']);
    $action = app(GenerateVoucherCode::class);
    $action->run($user, $contact_reference_code, $project);
    $reference = $action->getReference();
    $contact = $action->getContact();
    expect(RedeemVoucherCode::run($reference, ['project_code' => 'AA-0317']))->toBeTrue();
    $contact->refresh();
    expect($contact->reference_code)->toBe(getSellerCode());
})->with('user', 'contact_reference_code');

test('redeem via end point', function (User $user, string $contact_reference_code) {
    $project = Project::factory()->create(['code' => 'AA-0317']);
    $action = app(GenerateVoucherCode::class);
    $voucher_code = $action->run($user, $contact_reference_code, $project);
    $route = route('redeem', ['voucher' => $voucher_code]);
    $response = $this->postJson($route);
    expect($response->status())->toBe(200);
    expect($response->json('seller_commission_code'))->toBe(getSellerCode());
})->with('user', 'contact_reference_code');
