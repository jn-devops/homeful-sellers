<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\References\Facades\References;
use Homeful\References\Models\Reference;
use App\Actions\GetSellerCommissionCode;
use Homeful\Properties\Models\Project;
use Homeful\References\Models\Input;
use App\Actions\SyncContact;
use App\Models\User;
use App\Actions\GenerateVoucherCode;

uses(RefreshDatabase::class, WithFaker::class);

test('use has cascading seller commission codes', function() {
    $user = User::factory()->create(['seller_commission_code' => $seller_commission_code1 = fake()->word()]);
    expect(Project::all())->toHaveCount(0);
    expect(GetSellerCommissionCode::run($user))->toBe($seller_commission_code1);
    expect(GetSellerCommissionCode::run($user, fake()->word))->toBe($seller_commission_code1);
    [$project1, $project2] = Project::factory(2)->create();
    $user->projects()->attach($project1,[
        'seller_commission_code' => $seller_commission_code2 = fake()->word(),
    ]);
    $user->projects()->attach($project2,[
        'seller_commission_code' => $seller_commission_code3 = fake()->word(),
    ]);
    expect(GetSellerCommissionCode::run($user, $project1->code))->toBe($seller_commission_code2);
    expect(GetSellerCommissionCode::run($user, $project2->code))->toBe($seller_commission_code3);
});

test('reference an input and contact', function () {
    $contact_reference_code = env('TEST_CONTACT_REFERENCE_CODE', 'H-CB6YWH');
    $user = User::factory()->create(['seller_commission_code' => $seller_commission_code1 = fake()->word()]);
    [$project1, $project2] = Project::factory(2)->create();
    $user->projects()->attach($project1,[
        'seller_commission_code' => $seller_commission_code2 = fake()->word(),
    ]);
    $user->projects()->attach($project2,[
        'seller_commission_code' => $seller_commission_code3 = fake()->word(),
    ]);
    $project_code = $project1->code;
    $reference_code = GenerateVoucherCode::run($user, $contact_reference_code, $project_code);
    $reference = app(Reference::class)->whereCode($reference_code)->first();
    $input = $reference->getInput();
    $contact = $reference->getContact();
    $user->contacts()->attach($contact, [
        'seller_commission_code' => $input->seller_commission_code,
        'invited_at' => $reference->create_at
    ]);
    $user_contact_pivot = $user->contacts()->whereKey($contact)->first()?->pivot;
//    expect($user_contact_pivot->seller_commission_code)->toBe($seller_commission_code);
    expect($user_contact_pivot->invited_at)->toBe($reference->create_at);
});
