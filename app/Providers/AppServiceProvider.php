<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Domain\MusicCatalog\Repositories\ArtistRepositoryInterface::class, \App\Infrastructure\Persistance\Repositories\EloquentArtistRepository::class);
        $this->app->bind(\App\Domain\MusicCatalog\Repositories\AlbumRepositoryInterface::class, \App\Infrastructure\Persistance\Repositories\EloquentAlbumRepository::class);
        $this->app->bind(\App\Domain\MusicCatalog\Repositories\SongRepositoryInterface::class, \App\Infrastructure\Persistance\Repositories\EloquentSongRepository::class);
        $this->app->bind(\App\Domain\MusicCatalog\Repositories\GenreRepositoryInterface::class, \App\Infrastructure\Persistance\Repositories\EloquentGenreRepository::class);
        $this->app->bind(\App\Domain\UserListening\Repositories\PlaylistRepositoryInterface::class, \App\Infrastructure\Persistance\Repositories\EloquentPlaylistRepository::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
