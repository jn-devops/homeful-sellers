<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserRoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Role/Index', [
            'roles' => UserRole::all(),
            'modules' => Module::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'array',
            'module_id.*' => 'exists:modules,id',
        ]);
    
        UserRole::create($data);

        return redirect()->back()->with('success', 'Role created!');
    }
    
    public function update(Request $request, UserRole $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'nullable|integer',
            'module_id' => 'array',
            'module_id.*' => 'exists:modules,id',
        ]);
        
        $role->update($data);

        return redirect()->back()->with('success', 'Role updated!');
    
    }
    
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'name' => 'required|string|unique:user_roles',
    //         'description' => 'nullable|string',
    //     ]);

    //     UserRole::create($data);

    //     return redirect()->back()->with('success', 'Role created!');
    // }

    // public function update(Request $request, UserRole $role)
    // {
    //     $data = $request->validate([
    //         'name' => 'required|string|unique:user_roles,name,' . $role->id,
    //         'description' => 'nullable|string',
    //     ]);

    //     $role->update($data);

    //     return redirect()->back()->with('success', 'Role updated!');
    // }

    public function destroy(UserRole $role)
    {
        $role->delete();
        return redirect()->back()->with('success', 'Role deleted!');
    }
}
