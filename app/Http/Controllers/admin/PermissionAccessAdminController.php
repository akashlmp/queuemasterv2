<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

class PermissionAccessAdminController extends Controller
{
    public function index()
    {
        return view('admin.adminindex');
    }
}
