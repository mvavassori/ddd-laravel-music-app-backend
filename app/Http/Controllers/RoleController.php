<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RoleStoreRequest;
use App\Application\MusicCatalog\Services\RoleApplicationService;

class RoleController extends Controller
{
    private RoleApplicationService $roleApplicationService;
    public function __construct(RoleApplicationService $roleApplicationService) {
        $this->roleApplicationService = $roleApplicationService;
    }
    public function show(string $id) {
        try {
            $role = $this->roleApplicationService->findRole($id);
            return response()->json($role, 200);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 404); 
        } catch (\Throwable $th) {
            Log::error("Failed to find artist.", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function index() {
        return $this->roleApplicationService->findAllRoles();
    }

    public function store(RoleStoreRequest $request) {
        $validatedData = $request->validated();
        try {
            return $this->roleApplicationService->createRole($validatedData['name']);
        } catch (\DomainException $e) {
            return response()->json($e->getMessage(), 404);
        } catch (\Throwable $th) {
            Log::error("Failed to create role.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function destroy($id) {
        // try {

        // } catch (\DomainException) {


        // } catch (\Throwable $th) {
        //     //throw $th;
        // }
        // $this->roleApplicationService->deleteRole($id);
        // return response()->noContent(204);
    }
}
