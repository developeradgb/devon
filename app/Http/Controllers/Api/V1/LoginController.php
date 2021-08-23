<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * Login the given user.
     *
     * @param LoginRequest $request
     * @return \Aws\Result|JsonResponse
     */
    final public function login(LoginRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getCredentials($request->email);
            if (!$this->validateCredentials($request->password, $user)) {
                return simpleMessageResponse('User not found', UNAUTHORIZED);
            }

            $user->save();

            $token = JWTAuth::fromUser($user);
            DB::commit();

            //SETTING THE REFRESH TOKEN IN COOKIE FOR WEB CALL'S
            return response(new LoginResource($user, $token));
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return simpleMessageResponse('User not found', NOT_FOUND);
        } catch (Throwable $throwable) {
            DB::rollBack();
            logError('Error while login', 'Api\V1\LoginController@login', $throwable, $request->all());
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }

    /**
     * Get the credentials for the given email.
     *
     * @param string $email
     * @return mixed
     */
    final public function getCredentials(string $email)
    {
        return User::whereEmail($email)->firstOrFail();
    }

    /**
     * Validate the given credentials with the given password.
     *
     * @param string $password
     * @param User $user
     * @return bool
     */
    final public function validateCredentials(string $password, User $user)
    {
        return Hash::check($password, $user->password);
    }

    /**
     * Logout Api.
     *
     * @return JsonResponse
     */
    public function logout()
    {
        try {
            auth()->logout();
            return simpleMessageResponse('User logged out successfully', SUCCESS);
        } catch (\Exception $exception) {
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }
}
