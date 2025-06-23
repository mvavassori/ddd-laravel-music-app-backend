<?php

namespace App\Application\UserListening\Services;

use Illuminate\Support\Facades\DB;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\MusicCatalog\ValueObjects\GenreId;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\Services\MixGenerator;
use App\Domain\UserListening\ValueObjects\Playlist;
use App\Domain\UserListening\Repositories\PlayRepositoryInterface;
use App\Domain\UserListening\Repositories\PlaylistRepositoryInteface;
use App\Domain\UserListening\Repositories\PlaylistRepositoryInterface;
use App\Domain\MusicCatalog\Repositories\SongRepositoryInterface;

class PlaylistApplicationService {
    private PlayRepositoryInterface $playRepository;
    private SongRepositoryInterface $songRepository;
    private PlaylistRepositoryInterface $playlistRepository;
    private MixGenerator $mixGenerator;

    public function __construct(PlayRepositoryInterface $playRepository, SongRepositoryInterface $songRepository, PlaylistRepositoryInterface $playlistRepository, MixGenerator $mixGenerator) {
        $this->playRepository = $playRepository;
        $this->songRepository = $songRepository;
        $this->playlistRepository = $playlistRepository;
        $this->mixGenerator = $mixGenerator;
    }

    public function getDailyMix(string $userId) {
        $userIdObj = new UserId($userId);
        
        // Check if daily mix already exists for today
        $existingMix = $this->playlistRepository->findByUserAndType($userIdObj, 'daily_mix', today());
        
        if ($existingMix) {
            return $this->toArray($existingMix);
        }
        
        // Generate new daily mix
        return $this->generateDailyMix($userId);
    }

    public function generateDailyMix(string $userId) {
        $userIdObj = new UserId($userId);

        DB::transaction(function() use ($userIdObj) {
            $topGenreId = $this->playRepository->getTopGenreByUser($userIdObj);

            if (!$topGenreId) {
                throw new \DomainException("User has no listening history");
            }

            $mostPlayedSongIds = $this->playRepository->getMostPlayedSongIdsByUser($userIdObj, 10);

            $genreSongIdsAtRandom = $this->songRepository->getSongIdsByGenreAtRandom($topGenreId, 10);

            $playlist = $this->mixGenerator->generateDailyMix(
                $userIdObj,
                $mostPlayedSongIds,
                $genreSongIdsAtRandom,
            );

            $this->playlistRepository->create($playlist);
            return $this->toArray($playlist);
        });
    }

    private function getTodaysDailyMix(UserId $userId) {
        return $this->playlistRepository->findByUserAndType($userId, 'daily_mix', today());
    }

    private function toArray(Playlist $playlist) {
        return [
            'id' => $playlist->getId()->getValue(),
            'user_id' => $playlist->getUserId()->getValue(),
            'name' => $playlist->getName(),
            'type' => $playlist->getType()->getValue(),
            'song_ids' => array_map(
                fn(SongId $songId) => $songId->getValue(),
                $playlist->getSongIds()
            ),
        ];
    }
}