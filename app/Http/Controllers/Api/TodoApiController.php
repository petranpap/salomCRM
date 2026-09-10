<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;

class TodoApiController extends Controller
{
    public function index(): JsonResponse
    {
        $todos = Todo::orderBy('due_date')->paginate(20);
        return response()->json($todos);
    }
}
