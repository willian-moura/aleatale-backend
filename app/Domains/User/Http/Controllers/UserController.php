<?php

namespace App\Domains\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::query()->paginate(10);
    }
}
