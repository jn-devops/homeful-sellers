<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use Homeful\Properties\Data\ProjectData;
use Homeful\Properties\Models\Project;
use Illuminate\Support\Facades\Auth;
use Inertia\{Inertia, Response};
use Illuminate\Http\Request;
use App\Models\ProjectUser;
use App\Http\Requests\SellerUpdateRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Auth::user()
            ->projects()
            ->orderBy('pivot_project_id')
            ->paginate(8)
            ->withQueryString()
            ->through(fn ($project) => [
                'id' => $project->id,
                'code' => $project->code,
                'name' => $project->name,
                'location' => $project->location,
                'seller_commission_code' => $project->pivot->seller_commission_code ?? null,
            ])
        ;

        return Inertia::render('Project/Index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $pivot = Auth::user()->projects()->whereKey($project)->first()?->pivot;
        if ($pivot instanceof ProjectUser)
            return Inertia::render('Project/Show', [
                'project' => function () use ($pivot) {
                    return [
                        'id' => $pivot->project->id,
                        'name' => $pivot->project->name,
                        'location' => $pivot->project->location,
                        'seller_commission_code' => $pivot->seller_commission_code
                    ];
                }
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $pivot = Auth::user()->projects()->whereKey($project)->first()?->pivot;
        if ($pivot instanceof ProjectUser)
            return Inertia::render('Project/Edit', [
                'project' => function () use ($pivot) {
                    return [
                        'id' => $pivot->project->id,
                        'name' => $pivot->project->name,
                        'location' => $pivot->project->location,
                        'seller_commission_code' => $pivot->seller_commission_code
                    ];
                }
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SellerUpdateRequest $request, Project $project)
    {
        $pivot = Auth::user()->projects()->whereKey($project)->first()?->pivot;
        $pivot->update($request->all());
        $pivot->save();

        return redirect()->route('projects.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        Auth::user()->projects()->detach($project);

        return redirect()->route('projects.index');
    }
}
