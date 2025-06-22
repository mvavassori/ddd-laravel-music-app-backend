<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Requests\AlbumStoreRequest;
use App\Http\Requests\AlbumUpdateRequest;
use App\Application\MusicCatalog\Services\AlbumApplicationService;

class AlbumController extends Controller {
    private AlbumApplicationService $albumApplicationService;
    public function __construct(AlbumApplicationService $albumApplicationService) {
        $this->albumApplicationService = $albumApplicationService;
    }

    public function create(AlbumStoreRequest $request) {
        $validatedData = $request->validated();
        try {
            $album = $this->albumApplicationService->createAlbum(
                $validatedData['title'],
                $validatedData['description'],
                $validatedData['image_url'],
                $validatedData['contributions'],
            );
            return response()->json($album, 201);
        } catch (\Throwable $th) {
            Log::error("Failed to create album and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update($id, AlbumUpdateRequest $request) {
        $validatedData = $request->validated();
        try {
            $updated = $this->albumApplicationService->updateAlbum(
                $id,
                $validatedData['title'] ?? null,
                $validatedData['description'] ?? null,
                $validatedData['image_url'] ?? null,
                $validatedData['contributions'] ?? null
            );
            return response()->json($updated);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()]);
        } catch (\Throwable $th) {
            Log::error("Failed to create artist.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function show($id) {
        $album = $this->albumApplicationService->findAlbum($id);
        if (empty($album)) {
            return response()->json(null, 404);
        }
        return response()->json($album, 200);
    }

    public function destroy($id) {
        $this->albumApplicationService->deleteAlbum($id);
        return response()->noContent(204);
    }
}