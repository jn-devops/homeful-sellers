<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Properties\Models\Project;
use Illuminate\Support\Facades\Http;
use App\Events\ProjectsSynced;
use Illuminate\Support\Arr;
use App\Models\User;

class SyncProjects
{
    use AsAction;

    public function handle(User $user, ?array $attribs = []): void
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
        }
        ProjectsSynced::dispatch($user);
    }

    public function asJob(User $user, ?array $attribs = []): void
    {
        $this->handle($user, $attribs);
    }
}
