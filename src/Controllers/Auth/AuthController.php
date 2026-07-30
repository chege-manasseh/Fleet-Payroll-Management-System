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
    private Response $response;
    public function __construct(AuthService $authService, Database $db, Response $response)
    {
        $this->authService = $authService;
        $this->db = $db;
        $this->response = $response;
    }

    public function register(Request $request){
        return $this->authService->register($request,$this->db,$this->response);  
    }
    public function login(Request $request){
        return $this->authService->login($request,$this->db,$this->response);
    }
    public function verifyEmail(Request $request){}
    public function logout(Request $request){
        return $this->authService->logout($request,$this->response);
    }
    public function changePassword(Request $request){
        return $this->authService->changePassword($request,$this->response);
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