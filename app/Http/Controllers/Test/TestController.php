<?php

namespace App\Http\Controllers\Test;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AutoEvaluation;
use App\Services\SSO\SSOService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function test(Request $request)
    {
        return response()->json([
            User::find($request->user_id)->load(['profile', 'roles', 'permissions']) 
        ]);
    }
}
