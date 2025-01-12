<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Properties\Models\Project;
use App\Models\{ProjectUser, User};

class GetSellerCommissionCode
{
    use AsAction;

    public function handle(User $user, Project|string $project = null): string
    {
        $project = $project instanceof Project
            ? $project
            : app(Project::class)->where('code', $project)->first();
        if ($project instanceof Project) {
            $user_project_pivot = $user->projects()->whereKey($project)->first()?->pivot;
            if ($user_project_pivot instanceof ProjectUser) {
                if ($seller_commission_code = $user_project_pivot->seller_commission_code)
                    return $seller_commission_code;
            }
        }

        return $user->seller_commission_code;
    }
}
