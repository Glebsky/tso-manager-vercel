<?php

declare(strict_types=1);

namespace App\Services\Market;

use App\Models\MarketServerConnection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read-only replacement for the full MarketServerService.
 *
 * The public portal never creates, verifies or synchronises a server
 * connection - it only lists the game worlds that already have market data
 * in the database.
 */
final class PublicServerService
{
    /**
     * Servers visible to the public portal.
     *
     * @return Collection<int, MarketServerConnection>
     */
    public function publicServers(): Collection
    {
        return MarketServerConnection::query()
            ->select('id', 'server_id', 'locale', 'display_name', 'sync_status')
            ->whereNotNull('account_id')
            ->orderBy('id')
            ->get();
    }
}
