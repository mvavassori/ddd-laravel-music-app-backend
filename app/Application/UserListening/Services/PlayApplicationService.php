<?php

namespace App\Application\UserListening\Services;

use App\Domain\UserListening\ValueObjects\Play;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\Repositories\PlayRepositoryInterface;
use App\Domain\UserListening\Repositories\UserRepositoryInterface;
use App\Domain\MusicCatalog\Repositories\SongRepositoryInterface;

class PlayApplicationService {
    private PlayRepositoryInterface $playRepository;
    private UserRepositoryInterface $userRepository;
    private SongRepositoryInterface $songRepository;

    public function __construct(PlayRepositoryInterface $playRepository, UserRepositoryInterface $userRepository, SongRepositoryInterface $songRepository) {
        $this->playRepository = $playRepository;
        $this->userRepository = $userRepository;
        $this->songRepository = $songRepository;
    }

    public function createPlay(string $userId, string $songId) {
        // validate that role and artists exist
        $userIdObj = new UserId($userId);
        $user = $this->userRepository->find($userIdObj);
        if (!$user) {
            throw new \DomainException('User not found');
        }
        $songIdObj = new SongId($songId);
        $song = $this->songRepository->find($songIdObj);
        if (!$song) {
            throw new \DomainException('Song not found');
        }

        $play = new Play($userIdObj, $songIdObj);

        return $this->toArray($play);
    }

    private function toArray(Play $play): array {
        return [
            'user_id' => $play->getUserId()->getValue(),
            'song_id' => $play->getSongId()->getValue(),
        ];
    }
}