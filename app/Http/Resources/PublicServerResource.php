<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\MarketServerConnection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MarketServerConnection
 */
class PublicServerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'server_id' => $this->server_id,
            'locale' => $this->locale,
            'world_name' => $this->resolveWorldName(),
            'sync_status' => $this->sync_status,
        ];
    }

    /**
     * The game world name, derived from the stored display name.
     */
    private function resolveWorldName(): string
    {
        $suffix = preg_quote((string) config('market.display_name_suffix', 'Settlers Market'), '/');

        $name = preg_replace('/\s+'.$suffix.'$/i', '', (string) $this->display_name);
        $name = preg_replace('/\s+Market(\s*\([^)]*\))?$/i', '', (string) $name);
        $name = trim((string) $name);

        return $name !== '' ? $name : strtoupper((string) $this->server_id);
    }
}
