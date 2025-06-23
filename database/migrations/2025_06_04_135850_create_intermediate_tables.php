<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('song_artist', function (Blueprint $table) {
            $table->foreignUuid('song_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('artist_id')->constrained()->onDelete('cascade');
            $table->primary(['song_id', 'artist_id']);
        });

        Schema::create('album_artist', function (Blueprint $table) {
            $table->foreignUuid('album_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('artist_id')->constrained()->onDelete('cascade');
            $table->primary(['album_id', 'artist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_artist');
        Schema::dropIfExists('album_artist');
    }
};
