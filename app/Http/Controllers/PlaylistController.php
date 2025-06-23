<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Application\UserListening\Services\PlaylistApplicationService;

class PlaylistController extends Controller
{
    private PlaylistApplicationService $playlistApplicationService;

    public function __construct(PlaylistApplicationService $playlistApplicationService) {
        $this->playlistApplicationService = $playlistApplicationService;
    }
    public function showDailyMixPlaylist(string $userId) {
        try {
            $playlistDailyMix = $this->playlistApplicationService->getDailyMix($userId);
            return response()->json($playlistDailyMix, 200);
        } catch (\Throwable $th) {
            Log::error("Failed to create daily mix playlist.", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }
}
