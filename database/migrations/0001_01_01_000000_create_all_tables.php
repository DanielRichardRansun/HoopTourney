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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('role')->default(1); // 1 = admin, 2 = team owner
            $table->unsignedBigInteger('team_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Application Tables
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organizer');
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status');
            $table->unsignedBigInteger('users_id');
            $table->timestamps();
            
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('coach');
            $table->string('manager');
            $table->timestamps();
        });

        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('jersey_number');
            $table->string('position');
            $table->unsignedBigInteger('teams_id');
            $table->timestamps();
            
            $table->foreign('teams_id')->references('id')->on('teams')->onDelete('cascade');
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team1_id');
            $table->unsignedBigInteger('team2_id');
            $table->dateTime('date');
            $table->string('location');
            $table->unsignedBigInteger('tournaments_id');
            $table->string('status'); // scheduled, completed, etc
            $table->string('round');
            $table->timestamps();
            
            $table->foreign('team1_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('team2_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('tournaments_id')->references('id')->on('tournaments')->onDelete('cascade');
        });

        Schema::create('match_results', function (Blueprint $table) {
            $table->id();
            $table->integer('team1_score')->default(0);
            $table->integer('team2_score')->default(0);
            $table->unsignedBigInteger('winning_team_id')->nullable();
            $table->unsignedBigInteger('losing_team_id')->nullable();
            $table->unsignedBigInteger('schedules_id');
            $table->timestamps();
            
            $table->foreign('winning_team_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('losing_team_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('schedules_id')->references('id')->on('schedules')->onDelete('cascade');
        });

        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('players_id');
            $table->unsignedBigInteger('match_results_id');
            $table->integer('quarter_number');
            $table->decimal('per', 8, 2)->default(0);
            $table->integer('point')->default(0);
            $table->integer('fgm')->default(0);
            $table->integer('fga')->default(0);
            $table->integer('fta')->default(0);
            $table->integer('ftm')->default(0);
            $table->integer('orb')->default(0);
            $table->integer('drb')->default(0);
            $table->integer('stl')->default(0);
            $table->integer('ast')->default(0);
            $table->integer('blk')->default(0);
            $table->integer('pf')->default(0);
            $table->integer('to')->default(0);
            $table->timestamps();
            
            $table->foreign('players_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('match_results_id')->references('id')->on('match_results')->onDelete('cascade');
        });

        Schema::create('quarter_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_results_id');
            $table->integer('quarter_number');
            $table->integer('team1_score');
            $table->integer('team2_score');
            $table->timestamps();
            
            $table->foreign('match_results_id')->references('id')->on('match_results')->onDelete('cascade');
        });

        Schema::create('team_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->unsignedBigInteger('teams_id');
            $table->unsignedBigInteger('tournaments_id');
            $table->timestamps();
            
            $table->foreign('teams_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('tournaments_id')->references('id')->on('tournaments')->onDelete('cascade');
        });

        Schema::create('team_tournament', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('tournament_id');
            $table->timestamps();
            
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
        });

        Schema::create('tournament_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('tournament_id');
            $table->string('status'); // pending, approved, rejected
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
        });
        
        // Add foreign key to users table now that teams table exists
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
        
        Schema::dropIfExists('tournament_requests');
        Schema::dropIfExists('team_tournament');
        Schema::dropIfExists('team_stats');
        Schema::dropIfExists('quarter_results');
        Schema::dropIfExists('player_stats');
        Schema::dropIfExists('match_results');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('players');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('tournaments');
        
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
