<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermissionMiddleware
{ /**
    * Handle an incoming request.
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @param  string  $permissionType (e.g., 'view', 'add')
    * @param  string  $moduleSlug (e.g., 'modules')
    */

   public function handle(Request $request, Closure $next, string $permissionType): Response
   {
       $user = $request->user();
       $slug = $request->segments();
       
       $permissions = $user->getPermissions();
    //    if (!($permissions[$slug[0]][$permissionType] ?? false)) {
    //         abort(403, 'Unauthorized.');
    //     }
       return $next($request);
   }

   
}
