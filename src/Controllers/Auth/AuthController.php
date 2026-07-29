<?php
namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\Auth\AuthService;
use App\Core\Database;

class AuthController extends Controller
{
    private AuthService $authService;
    private Database $db;
    public function __construct(AuthService $authService, Database $db)
    {
        $this->authService = $authService;
        $this->db = $db;
    }

    public function register(Request $request){
        return $this->authService->register($request,$this->db);  
    }
    public function login(Request $request){
        return $this->authService->login($request,$this->db);
    }
    public function verifyEmail(Request $request){}
    public function logout(Request $request){
        return $this->authService->logout($request);
    }
    public function changePassword(Request $request){
        return $this->authService->changePassword($request);
    }
    public function forgotPassword(Request $request){
        return $this->authService->forgotPassword($request);
    }
    public function resetPassword(Request $request){
        return $this->authService->resetPassword($request);
    }
    public function verifyResetPassword(Request $request){
        return $this->authService->verifyResetPassword($request);
    }
    public function verifyChangePassword(Request $request){
        return $this->authService->verifyChangePassword($request);
    }
    public function verifyForgotPassword(Request $request){
        return $this->authService->verifyForgotPassword($request);
    }
}