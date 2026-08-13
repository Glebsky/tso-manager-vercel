<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The read-only schema the public market portal needs.
 *
 * This is the flattened result of the market migrations of the full
 * application (offers, history, server connections, settings). The portal
 * never writes to these tables - the migration only exists so a fresh
 * (local) database can be created. Against the production database that is
 * filled by the main application you should NOT run migrations.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_offers', function (Blueprint $table): void {
            $table->id();
            $table->string('server_id', 50)->default('ru');
            $table->bigInteger('offer_id');
            $table->bigInteger('player_id');
            $table->string('sender_name');
            $table->string('item_id', 100);
            $table->string('item_name', 255);
            $table->integer('amount');
            $table->string('target_item_id', 100);
            $table->string('target_item_name', 255);
            $table->integer('target_amount');
            $table->double('price');
            $table->integer('volume');
            $table->integer('lots_remaining');
            $table->timestamp('created_at');
            $table->timestamp('collected_at');

            $table->unique(['server_id', 'offer_id']);
            $table->index('server_id');
        });

        Schema::create('market_history', function (Blueprint $table): void {
            $table->id();
            $table->string('server_id', 50)->default('ru');
            $table->bigInteger('offer_id');
            $table->bigInteger('player_id');
            $table->string('item_id', 100);
            $table->string('item_name', 255);
            $table->integer('amount');
            $table->string('target_item_id', 100);
            $table->string('target_item_name', 255);
            $table->integer('target_amount');
            $table->double('price');
            $table->integer('volume');
            $table->timestamp('collected_at');

            $table->index(['item_id', 'target_item_id']);
            $table->index('collected_at');
            $table->index(['server_id', 'item_id', 'target_item_id']);
            $table->index(['server_id', 'collected_at']);
        });

        Schema::create('market_server_connections', function (Blueprint $table): void {
            $table->id();
            $table->string('server_id')->unique();
            $table->string('locale', 10)->default('RU');
            $table->string('display_name');
            // Kept for compatibility with the database of the main app; the
            // public portal has no accounts table and no foreign key.
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('verification_status', 30)->default('unverified');
            $table->string('sync_status', 30)->default('not_configured');
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_error')->nullable();
            $table->unsignedBigInteger('data_version')->default(1);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('market_server_connections');
        Schema::dropIfExists('market_history');
        Schema::dropIfExists('market_offers');
    }
};
