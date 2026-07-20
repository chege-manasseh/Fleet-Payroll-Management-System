<?php

namespace App\Controllers;

use App\Models\Models;
use App\Controllers\Controller;
use App\Core\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return $this->json([
            'message' => 'Fleet Payroll Management System API',
        ], 200);
    }
    public function testConnection(Request $request)
    {
        $models = new Models();
        try {
            if ($models->getPDO()) {
                return $this->json([
                    'message' => 'Connected to the database',
                ], 200);
            }
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Failed to connect to the database: ' . $e->getMessage(),
            ], 500);
        }
    }
}
