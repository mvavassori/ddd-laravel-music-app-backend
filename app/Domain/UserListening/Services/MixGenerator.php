<?php

namespace App\Domain\UserListening\Services;

use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\ValueObjects\Playlist;
use App\Domain\UserListening\ValueObjects\PlaylistType;

class MixGenerator {
    

    public function generateDailyMix(
        UserId $userId,
        array $mostPlayedSongIds,
        array $genreSongIdsAtRandom
    ): Playlist {
        
        $playlist = new Playlist(
            $userId,
            "Daily Mix " . date("Y-m-d"),
            new PlaylistType('daily_mix')
        );

        // remove duplicates
        $songIds = array_unique(array_merge($mostPlayedSongIds, $genreSongIdsAtRandom));

        shuffle($songIds); // acts on the orginal array

        $songsAdded = 0;
        foreach ($songIds as $songId) {
            if ($songsAdded >= 20) {
                break;
            }
            $playlist->addSong(new SongId($songId));
            $songsAdded++;
        }

        return $playlist;
    }    
}