<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use App\IpAccesses;
use App\Http\Controllers\MyController;
class RestrictIpAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        if ($this->getUserIP() == true){
            return $next($request);
        }else{
            return response()->json('Unauthorized', 401);
        }
    }

    function getUserIP()
    {
        $client = @$_SERVER['HTTP_X_CLIENT_IP'];
        $c = new MyController();
        $ip = $c->show($client);
        if ( (empty($ip)) || ($ip[0]['blocked'] == 'X') ){
            Log::notice('Acesso negado ip '.$client);
            return false;
        }else{
            Log::notice('Acessando pelo ip '.$client);
            return true;
        }
    }
}
