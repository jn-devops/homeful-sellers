<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Inertia\{Inertia, Response};
use App\Actions\SyncProjects;
use Illuminate\Http\Request;

class SyncProjectsController extends Controller
{
    public function create(Request $request)
    {
        return Inertia::render('SyncProjects/Create');
    }

    public function store(Request $request)
    {
        $validated = Validator::validate($request->all(), ['filters' => ['nullable', 'string']]);
        SyncProjects::dispatch($request->user(), $validated);

        return redirect()->route('projects.index');
    }
}
