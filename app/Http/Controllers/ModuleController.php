<?php

namespace App\Http\Controllers;


use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuleController extends Controller
{
    //
    public function index()
    {
        // dd(Module::all());
        return Inertia::render('Module/Index', [
            'modules' => Module::all()
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->getContent());
        $data = $request->validate([
            'name' => 'required|string|unique:modules,name',
            'slug' => 'required|string',
            'description' => 'nullable|string',
            'add' => 'nullable|boolean',
            'edit' => 'nullable|boolean',
            'delete' => 'nullable|boolean',
            'view' => 'nullable|boolean',
            'import' => 'nullable|boolean',
            'export' => 'nullable|boolean'
        ]);

        Module::create($data);

        return redirect()->back()->with('success', 'Module created!');
    }

    public function update(Request $request, Module $module)
    {
        // dd($request->slug);
        $data = $request->validate([
            'name' => 'required|string|unique:modules,name,' . $module->id,
            'slug' => 'required|string',
            'description' => 'nullable|string',
            'add' => 'nullable|boolean',
            'edit' => 'nullable|boolean',
            'delete' => 'nullable|boolean',
            'view' => 'nullable|boolean',
            'import' => 'nullable|boolean',
            'export' => 'nullable|boolean'
        ]);
        // dd($data);
        $module->update($data);
        
        return redirect()->back()->with('success', 'Module updated!');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->back()->with('success', 'Module deleted!');
    }
}
