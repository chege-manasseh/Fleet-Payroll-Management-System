<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\Auth\AuthService;
use App\Services\Auth\RefreshToken;
use App\Storage\Database;

class AuthController extends Controller
{
    private AuthService $authService;
    private RefreshToken $refreshTokenService;
    private Response $response;

    public function __construct(AuthService $authService, RefreshToken $refreshTokenService, Response $response)
    {
        $this->authService = $authService;
        $this->refreshTokenService = $refreshTokenService;
        $this->response = $response;
    }

    public function register(Request $request)
    {
        return $this->authService->register($request);
    }
    public function login(Request $request)
    {
        return $this->authService->login($request);
    }
    public function verifyEmail(Request $request) {}
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
    public function changePassword(Request $request)
    {
        $currentPassword = trim($request->input('current_password'));
        $newPassword = trim($request->input('new_password'));
        $confirmPassword = trim($request->input('confirm_passsword'));
        $userId = $request->input('id');

        $user = $this->authService->changePassword($userId, $currentPassword, $newPassword, $confirmPassword);
        if ($user) {
            return $this->response->json("Password updated successfully", ["id" => $userId]);
        }else{
            return $this->response->json("Password update failed",null,401);
        }
    }

    public function resetPassword(Request $request)
    {
        return $this->authService->resetPassword($request);
    }
    // public function verifyResetPassword(Request $request){
    //     return $this->authService->verifyResetPassword($request,$this->response);
    // }
    // public function verifyChangePassword(Request $request){
    //     return $this->authService->verifyChangePassword($request,$this->response);
    // }

    public function refresh(Request $request)
    {
        return $this->refreshTokenService->refreshToken($request);
    }
}
