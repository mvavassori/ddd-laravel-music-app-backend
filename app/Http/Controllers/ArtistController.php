<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArtistUpdateRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ArtistStoreRequest;
use App\Application\MusicCatalog\Services\ArtistApplicationService;

class ArtistController extends Controller {
    private ArtistApplicationService $artistApplicationService;
    public function __construct(ArtistApplicationService $artistApplicationService) {
        $this->artistApplicationService = $artistApplicationService;
    }
    public function store(ArtistStoreRequest $request) {
        $validatedData = $request->validated();
        try {
            $artist = $this->artistApplicationService->createArtist(name: $validatedData['name'], bio: $validatedData['bio'], imageUrlString: $validatedData['image_url']);
            return response()->json($artist, 201);
        } catch (\DomainException $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]);
        } catch (\Throwable $th) {
            Log::error("Failed to create artist.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update($id, ArtistUpdateRequest $request) {
        $validatedData = $request->validated();
        try {
            $updated = $this->artistApplicationService->updateArtist(
                $id,
                $validatedData['name'] ?? null,
                $validatedData['bio'] ?? null,
                $validatedData['image_url'] ?? null
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
        $artist = $this->artistApplicationService->findArtist($id);
        if (empty($artist)) {
            return response()->json(null, 404);
        }
        return response()->json($artist, 200);
    }

    public function showWithContributions($id) {
        $artist = $this->artistApplicationService->findArtistWithContributions($id);
        if (empty($artist)) {
            return response()->json(null, 404);
        }
        return response()->json($artist, 200);
    }

    public function showWithSongs($id) {
        $artist = $this->artistApplicationService->findArtistWithSongs($id);
        if (empty($artist)) {
            return response()->json(null, 404);
        }
        return response()->json($artist, 200);
    }

    public function destroy($id) {
        $this->artistApplicationService->deleteArtist($id);
        return response()->noContent(204);
    }
}