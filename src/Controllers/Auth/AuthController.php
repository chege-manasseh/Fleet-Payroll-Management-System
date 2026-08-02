<?php
namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\Auth\AuthService;
use App\Storage\Database;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request){
        return $this->authService->register($request);  
    }
    public function login(Request $request){
        return $this->authService->login($request);
    }
    public function verifyEmail(Request $request){}
    public function logout(Request $request){
        return $this->authService->logout($request);
    }
    public function changePassword(Request $request){
        return $this->authService->changePassword($request);
    }
    // public function forgotPassword(Request $request){
    //     return $this->authService->forgotPassword($request,$this->response);
    // }
    // public function resetPassword(Request $request){
    //     return $this->authService->resetPassword($request,$this->response);
    // }
    // public function verifyResetPassword(Request $request){
    //     return $this->authService->verifyResetPassword($request,$this->response);
    // }
    // public function verifyChangePassword(Request $request){
    //     return $this->authService->verifyChangePassword($request,$this->response);
    // }
    // public function verifyForgotPassword(Request $request){
    //     return $this->authService->verifyForgotPassword($request,$this->response);
    // }
}