<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserStoreRequest;
use App\Application\UserListening\Services\UserApplicationService;

class UserController extends Controller
{
    private UserApplicationService $userApplicationService;
    public function __construct(UserApplicationService $userApplicationService) {
        $this->userApplicationService = $userApplicationService;
    }

    // public function index() {
    //     $users = $this->userApplicationService->getAllUsers();
    //     return response()->json($users, 200);
    // }

    public function show($id) {
        $user = $this->userApplicationService->findUser($id);
        return response()->json($user, 200);
    }

    public function store(UserStoreRequest $request) {
        $validatedData = $request->validated();
        $user = $this->userApplicationService->createUser($validatedData['name'], $validatedData['email']);
        return response()->json($user, 201);
    }
}
