<?php

namespace App\Actions;

use Homeful\Properties\Data\ProjectData;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Properties\Models\Project;
use Spatie\LaravelData\DataCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use App\Models\User;

class SyncProjects
{
    use AsAction;

    public function handle(User $user)
    {
        $route = config('homeful-sellers.end-points.projects');
        $response = Http::acceptJson()->get($route);
        $collection = $response->json('projects');
        foreach ($collection as $attributes) {
            $keys = Arr::only($attributes, 'code');
            $project = app(Project::class)->updateOrCreate($keys, $attributes);

            if ($user->projects()->whereKey($project)->doesntExist()) {
                $user->projects()->attach($project, [
                    'seller_commission_code' => $user->seller_commission_code,
                ]);
            }

//            $alreadyExists = $user->projects()->where('project_id', $project->id)->exists();
//            if (!$alreadyExists) {
//                $user->projects()->attach($project, [
//                    'seller_commission_code' => $user->seller_commission_code,
//                ]);
//            };
        }

        return Project::all();
    }
}
