<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

class JWTAuth extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    final public function handle($request, Closure $next)
    {
        try {
            // Get the token from request. Throws exception if token is null
            if (!$this->auth->parser()->setRequest($request)->hasToken()) {
                return simpleMessageResponse('Token not found', UNAUTHORIZED);
            }

            $email = $this->auth->parseToken()->getClaim('sub');
            Auth::login($user = User::whereEmail($email)->firstOrFail());

        } catch (TokenExpiredException $exception) {
            return simpleMessageResponse($exception->getMessage(), UNAUTHORIZED);
        } catch (TokenInvalidException $exception) {
            return simpleMessageResponse($exception->getMessage(), UNAUTHORIZED);
        } catch (JWTException $exception) {
            return simpleMessageResponse($exception->getMessage(), INTERNAL_SERVER);
        } catch (Throwable $exception) {
            return simpleMessageResponse($exception->getMessage(), INTERNAL_SERVER);
        }

        return $next($request);
    }
}
