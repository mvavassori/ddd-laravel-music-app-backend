<?php

namespace App\Http\Controllers;

use App\Contracts\Services\SongServiceInterface;
use App\Http\Requests\SongStoreRequest;
use App\Http\Requests\SongUpdateRequest;
use App\Infrastructure\Persistance\Repositories\SongApplicationService;
use App\Models\Song;
use Illuminate\Support\Facades\Log;

class SongController extends Controller {

    private SongApplicationService $songApplicationService;

    public function __construct(SongApplicationService $songApplicationService) {
        $this->songApplicationService = $songApplicationService;
    }

    public function show($id) {
        $song = $this->songApplicationService->findSong($id);
        return response()->json($song, 200);
    }

    public function showWithContributions($id) {
        $songWithContributions = $this->songApplicationService->findSongWithContributions($id);
        return response()->json($songWithContributions, 200);
    }

    public function store(SongStoreRequest $request) {
        $vallidatedData = $request->validated();
        try {
            $song = $this->songApplicationService->createSong(
                $vallidatedData['title'],
                $vallidatedData['genre_id'],
                $vallidatedData['album_id'] ?? null, // album_id is optional
                $vallidatedData['contributions']
            );
            return response()->json($song, 201);
        } catch (\Throwable $th) {
            Log::error("Failed to create song and associated relationships.", [
                'input' => $request->all(), // full input for context
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString() // trace for debugging
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update(SongUpdateRequest $request, $id) {
        // $song = Song::findOrFail($id);
        $validatedData = $request->validated();
        try {
            $updatedsong = $this->songApplicationService->updateSong($id,
                $validatedData['title'] ?? null,
                $validatedData['genre_id'] ?? null,
                $validatedData['album_id'] ?? null,
                $validatedData['contributions'] ?? []
            );
            return response()->json($updatedsong, 200);
        } catch (\Throwable $th) {
            Log::error("Failed to update song and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function destroy($id) {
        $this->songApplicationService->deleteSong($id);
        return response()->noContent(204);
    }
}
