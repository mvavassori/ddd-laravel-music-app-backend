<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\GenreStoreRequest;
use App\Application\MusicCatalog\Services\GenreApplicationService;

class GenreController extends Controller
{
    private GenreApplicationService $genreApplicationService;
    public function __construct(GenreApplicationService $genreApplicationService) {
        $this->genreApplicationService = $genreApplicationService;
    }
    public function show(string $id) {
        try {
            $genre = $this->genreApplicationService->findGenre($id);
            return response()->json($genre, 200);
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
        return $this->genreApplicationService->findAllGenres();
    }

    public function store(GenreStoreRequest $request) {
        $validatedData = $request->validated();
        try {
            return $this->genreApplicationService->createGenre($validatedData['name']);
        } catch (\DomainException $e) {
            return response()->json($e->getMessage(), 404);
        } catch (\Throwable $th) {
            Log::error("Failed to create genre.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }
}
