<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Actions\GetSellerCommissionCode;
use Homeful\Properties\Models\Project;
use App\Models\User;

uses(RefreshDatabase::class, WithFaker::class);

test('get seller commission code action works', function () {
    $user = User::factory()->create(['seller_commission_code' => 'ACB-123']);
    expect(GetSellerCommissionCode::run($user))->toBe('ACB-123');
    [$project1, $project2] = Project::factory(2)->create();
    expect(GetSellerCommissionCode::run($user, $project1))->toBe('ACB-123');
    expect(GetSellerCommissionCode::run($user, $project2->code))->toBe('ACB-123');
    $user->projects()->attach($project1, ['seller_commission_code' => 'DEF-456']);
    $user->projects()->attach($project2, ['seller_commission_code' => 'GHI-789']);
    expect(GetSellerCommissionCode::run($user))->toBe('ACB-123');
    expect(GetSellerCommissionCode::run($user, $project1))->toBe('DEF-456');
    expect(GetSellerCommissionCode::run($user, $project2))->toBe('GHI-789');
});

