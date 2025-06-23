<?php

namespace App\Infrastructure\Persistance\Repositories;

use Illuminate\Support\Facades\DB;
use App\Domain\UserListening\ValueObjects\Play;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Infrastructure\Persistance\Mappers\PlayMapper;
use App\Infrastructure\Persistance\Mappers\SongMapper;
use App\Infrastructure\Persistance\Models\EloquentPlayModel;
use App\Infrastructure\Persistance\Models\EloquentSongModel;
use App\Domain\UserListening\Repositories\PlayRepositoryInterface;

class EloquentPlayRepository implements PlayRepositoryInterface {
    private PlayMapper $playMapper;
    private SongMapper $songMapper;
    public function __construct(PlayMapper $playMapper, SongMapper $songMapper) {
        $this->playMapper = $playMapper;
        $this->songMapper = $songMapper;
    }
    public function create(Play $play)  {
        $eloquentPlay = EloquentPlayModel::create($this->playMapper->toPersistence($play));
        return $this->playMapper->toDomain($eloquentPlay);
    }
    public function getMostPlayedSongIdsByUser(UserId $userId, $limit = 10) {
        $userIdString = $userId->getValue();
        $songIdsCollection = EloquentSongModel::whereHas('plays', function ($query) use ($userIdString) { // whereHas finds songs that the user has listened to at least once
            $query->where('user_id', $userIdString);
        })
            ->withCount(['plays' => function ($query) use ($userIdString) { // counts how many times the user has played each song and adds that number to the new column plays_count
                $query->where('user_id', $userIdString);
            }])
            ->orderByDesc('plays_count')
            ->limit($limit)
            ->pluck('id');
        
        $songIdsArray = [];

        foreach($songIdsCollection as $songId) {
            $songIdsArray[] = new SongId($songId); 
        }
        return $songIdsArray;
    }
    public function getTopGenreByUser(UserId $userId) {
        return EloquentPlayModel::where('user_id', $userId) // grab all the plays of the specified user
            ->select('songs.genre_id', DB::raw('COUNT(*) as plays_count')) // SELECT songs.genre, COUNT(*) as plays_count FROM plays... // without DB:raw laravel would have assumed COUNT(*) was a column name.
            ->join('songs', 'plays.song_id', '=', 'songs.id')   // join with songs
            ->groupBy('songs.genre_id')
            ->orderByDesc('plays_count')
            ->limit(1) // top genre
            ->pluck('genre_id'); // don't include the counts i.e. play_count column
            // ->get();
        // dd($topGenre);
    }
}