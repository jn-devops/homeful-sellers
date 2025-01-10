<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Properties\Models\Project;
use App\Models\{ProjectUser, User};

class GetSellerReferenceCode
{
    use AsAction;

    public function handle(User $user, string $project_code = ''): string
    {
        $project = Project::where('code', $project_code)->first();
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
