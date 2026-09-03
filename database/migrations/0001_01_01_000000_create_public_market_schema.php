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
            $table->string('item_kind', 16)->default('resource');
            $table->string('item_id', 191);
            $table->string('item_name', 255);
            $table->string('item_subject', 191)->nullable();
            $table->integer('amount');
            $table->string('target_item_kind', 16)->nullable();
            $table->string('target_item_id', 191)->nullable();
            $table->string('target_item_name', 255)->nullable();
            $table->string('target_item_subject', 191)->nullable();
            $table->integer('target_amount')->nullable();
            $table->double('price')->nullable();
            $table->integer('volume');
            $table->integer('lots_remaining');
            $table->smallInteger('trade_type')->nullable();
            $table->smallInteger('slot_type')->nullable();
            $table->integer('total_lots')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('collected_at');

            $table->unique(['server_id', 'offer_id']);
            $table->index('server_id');
            $table->index(['server_id', 'item_kind', 'item_id']);
            $table->index(['server_id', 'created_at'], 'idx_market_offers_server_created');
            $table->index(['server_id', 'item_id', 'target_item_id', 'created_at'], 'idx_market_offers_server_pair_created');
        });

        Schema::create('market_history', function (Blueprint $table): void {
            $table->id();
            $table->string('server_id', 50)->default('ru');
            $table->bigInteger('offer_id');
            $table->bigInteger('player_id');
            $table->string('item_kind', 16)->default('resource');
            $table->string('item_id', 191);
            $table->string('item_name', 255);
            $table->string('item_subject', 191)->nullable();
            $table->integer('amount');
            $table->string('target_item_kind', 16)->nullable();
            $table->string('target_item_id', 191)->nullable();
            $table->string('target_item_name', 255)->nullable();
            $table->string('target_item_subject', 191)->nullable();
            $table->integer('target_amount')->nullable();
            $table->double('price')->nullable();
            $table->integer('volume');
            $table->smallInteger('trade_type')->nullable();
            $table->smallInteger('slot_type')->nullable();
            $table->integer('total_lots')->nullable();
            $table->timestamp('collected_at');

            $table->index(['item_id', 'target_item_id']);
            $table->index('collected_at');
            $table->index(['server_id', 'item_kind', 'collected_at']);
            $table->index(['server_id', 'item_id', 'target_item_id']);
            $table->index(['server_id', 'collected_at']);
            $table->index(['server_id', 'item_id', 'target_item_id', 'collected_at'], 'idx_market_history_server_pair_collected');
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
