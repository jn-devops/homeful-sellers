<?php
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Contacts\Models\Contact;
use App\Models\{ContactUser, User};

uses(RefreshDatabase::class, WithFaker::class);

test('user has seller commission code attribute', function () {
    $user = User::factory()->create();
    expect($user->seller_commission_code)->toBeNull();
    $user->seller_commission_code = $seller_commission_code = fake()->word();
    $user->save();
    expect($user->seller_commission_code)->toBe($seller_commission_code);
});

test('user has many contacts', function() {
    $user = User::factory()->create();
    [$contact1, $contact2] = Contact::factory(2)->create();
    expect(ContactUser::all())->toHaveCount(0);
    expect($user->contacts()->get())->toHaveCount(0);
    $user->contacts()->attach($contact1, ['seller_commission_code' => fake()->word(), 'invited_at' => now(), 'validated_at' => now()]);
    expect(ContactUser::all())->toHaveCount(1);
    expect($user->contacts()->get())->toHaveCount(1);
    $user->contacts()->attach($contact2, ['seller_commission_code' => fake()->word(), 'invited_at' => now(), 'validated_at' => now()]);
    expect(ContactUser::all())->toHaveCount(2);
    expect($user->contacts()->get())->toHaveCount(2);
});

test('can find specific pivot record using contact and user', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();
    $sellerCommissionCode = fake()->word();
    $provisioningUri = fake()->url();
    $user->contacts()->attach($contact, [
        'seller_commission_code' => $sellerCommissionCode,
        'provisioning_uri' => $provisioningUri
    ]);

    $pivotRecord = ContactUser::query()
        ->whereRelation('user', fn($query) => $query->whereKey($user))
        ->whereRelation('contact', fn($query) => $query->whereKey($contact))
        ->first();

    expect($pivotRecord)->not->toBeNull();
    expect($pivotRecord->seller_commission_code)->toBe($sellerCommissionCode);
    expect($pivotRecord->provisioning_uri)->toBe($provisioningUri);

    $pivotRecord = $user->contacts()
        ->whereKey($contact)
        ->first()?->pivot;

    expect($pivotRecord)->not->toBeNull();
    expect($pivotRecord->seller_commission_code)->toBe($sellerCommissionCode);
    expect($pivotRecord->provisioning_uri)->toBe($provisioningUri);
});
