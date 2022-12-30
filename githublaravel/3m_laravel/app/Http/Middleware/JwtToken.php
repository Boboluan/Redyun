<?php
namespace App\Http\Middleware;
use App\Service\TokenService as Token;
use Closure;
use Illuminate\Http\Request;

class JwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('token');
        if(empty($token)){
            return response()->json(['code'=>40001,'msg'=>'请先登录','data'=>'']);
        }
        $res = (new Token())->validateToken($token);
        if(!is_numeric($res)){
            return response()->json(['code'=>40002,'msg'=>$res,'data'=>'']);
        }
//        $request['uid'] = $res;
        return $next($request);
    }




}
