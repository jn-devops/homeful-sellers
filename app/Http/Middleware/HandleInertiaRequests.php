<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $currentModuleSlug = $request->segment(1);
        $config = 'config';
        // return [
        //     ...parent::share($request),
        //     'auth' => [
        //         'user' => $request->user(),
        //     ],
        //     'flash' => [
        //         'message' => fn () => $request->session()->get('message'),
        //         'warning' => fn () => $request->session()->get('warning'),
        //         'data' => fn () => $request->session()->get('data'),
        //         'event' => fn () => $request->session()->get('event'),
        //     ],
        //     'data' => [
        //         'appURL' => env('APP_URL', 'http://homeful-contacts.test/'),
        //     ],
        // ];


        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn() => $request->user()
                    ? $request->user()->load('role') //add to load role
                    : null,
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'warning' => fn() => $request->session()->get('warning'),
                'data' => fn() => $request->session()->get('data'),
                'event' => fn() => $request->session()->get('event'),
            ],
            'data' => [
                'appURL' => env('APP_URL', 'http://homeful-contacts.test/'),
            ],
            //added for permissions


            'permissions' => fn() => $request->user()
                ? (function () use ($request, $currentModuleSlug) {
                    $role = $request->user()->role;
                    if (!$role) return [];
                    $modules = $role->module();
                    // dd($modules);
                    foreach ($modules as $module) {
                        if (Str::lower($module->slug ?? $module->name) === Str::lower($currentModuleSlug)) {
                            // dd($module);
                            return [
                                'add' => (bool) $module->add ?? false,
                                'edit' => (bool) $module->edit ?? false,
                                'delete' => (bool) $module->delete ?? false,
                                'view' => (bool) $module->view ?? false,
                                'import' => (bool) $module->import ?? false,
                                'export' => (bool) $module->export ?? false,
                            ];
                        }
                    }
                    return [];
                })()
                : [],
                'config' => fn() => $request->user()
                ? (function () use ($request, $config) {
                    $role = $request->user()->role;
                    if (!$role) return [];

                    $modules = $role->module();
                    // dd($modules);
                    // $config_view[]='';
                    foreach ($modules as $module) {
                        // if (Str::lower($module->slug ?? $module->name) === Str::lower($config)) {
                        //     // dd($module);
                        //     return [
                        //         'add' => (bool) $module->add ?? false,
                        //         'edit' => (bool) $module->edit ?? false,
                        //         'delete' => (bool) $module->delete ?? false,
                        //         'view' => (bool) $module->view ?? false,
                        //         'import' => (bool) $module->import ?? false,
                        //         'export' => (bool) $module->export ?? false,
                        //     ];
                        // }
                        $config_view[$module->slug]=[
                            'view' => (bool) $module->view ?? false,];
                    }
                    return $config_view;
                })()
                : [],

        ];
    }
}
//
