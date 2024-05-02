<?php

namespace Domain\Auth\Presentation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Domain\Users\Application\UseCases\CreateUser;
use App\Core\Http\Controllers\Controller;
use Domain\Auth\Application\UseCases\Login;
use Domain\Auth\Application\UseCases\Reset;
use Domain\Auth\Application\UseCases\Forgot;
use Domain\Auth\Application\UseCases\Logout;
use Symfony\Component\HttpFoundation\Response;
use Domain\Auth\Presentation\Http\Requests\LoginRequest;
use Domain\Auth\Presentation\Http\Requests\SignupRequest;
use Domain\Auth\Presentation\Http\Resources\UserMeResource;
use Domain\Auth\Presentation\Http\Requests\ResetPasswordRequest;
use Domain\Auth\Presentation\Http\Requests\ForgotPasswordRequest;
use Domain\Users\Infraestructure\Repositories\Contracts\UserRepositoryInterface;


class AuthController extends Controller
{
    public function signup(SignupRequest $request, UserRepositoryInterface $userRepository)
    {
        try {
            $payload = $request->all();
            $action = new CreateUser($userRepository);
            return response($action->execute($payload), Response::HTTP_OK);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $this->catchResponseStatus($e->getCode()));
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $authenticated = (new Login($request))->execute($request->all());
            if (!$authenticated) return response(['error' => trans('auth.failed')], Response::HTTP_BAD_REQUEST);
            return response($authenticated, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $this->catchResponseStatus($e->getCode()));
        }
    }

    public function me($guard)
    {
        try {
            $user = Auth::guard()->user();
            return new UserMeResource($user);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function logout(Request $request)
    {
        try {
            (new Logout($request))->execute();
            return redirect('/');
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function forgot(ForgotPasswordRequest $request)
    {
        try {
            $email = $request->get('email');
            $status = (new Forgot())->execute($email);
            return response(['data' => $status], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function reset(ResetPasswordRequest $request)
    {
        try {
            $payload = $request->all();
            $status = (new Reset())->execute($payload);
            return response(['data' => $status], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode());
        }
    }
}