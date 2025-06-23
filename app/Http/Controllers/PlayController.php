<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PlayStoreRequest;
use App\Application\UserListening\Services\PlayApplicationService;

class PlayController extends Controller
{
    private PlayApplicationService $playApplicationService;

    public function __construct(PlayApplicationService $playApplicationService) { // constructor dependency injection // Service container will instantiate the objects for me behind the scenes
        $this->playApplicationService = $playApplicationService;
    }
    public function store(PlayStoreRequest $request) {
        try {
            $validatedData = $request->validated();
            $play = $this->playApplicationService->createPlay($validatedData['user_id'], $validatedData['song_id']);
            return response()->json($play, 201);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Throwable $th) {
            Log::error("Failed to create play.", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }
}
