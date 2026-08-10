<?php

namespace App\Controllers;

use App\Models\Models;
use App\Controllers\Controller;
use App\Core\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return $this->json('fleet management', null, 200);
    }
}
