<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $user = $request->user();
      $isOSACProcessor = $user->details->position_id === 36 && //osac
                         $user->details->department_id === 12 && // sezad
                         $user->details->user_function_id === 4 ;//One Stop Action center
      $isLocator =  optional($user->details)->role_id === 3 &&
                    optional($user->details)->permission_id === 2;
      $isCCO =
            optional($user->details)->position_id === 37 &&
            optional($user->details)->department_id === 12 &&
            optional($user->details)->role_id === 2 &&
            optional($user->details)->permission_id === 2;
       if( $isOSACProcessor){ 
        return redirect('/osac');    
       }elseif($isCCO){
        return retirect('/cco');
       }elseif($isLocator){
        return redirect('/locator');
       }
        return $next($request);
    }
}
