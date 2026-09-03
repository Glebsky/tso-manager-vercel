<?php

declare(strict_types=1);

namespace App\Http\Controllers\Market;

use App\Http\Controllers\Controller;
use App\Services\Market\MarketSettingsService;
use Illuminate\Http\JsonResponse;

final class PublicMarketSettingsController extends Controller
{
    public function __construct(
        private readonly MarketSettingsService $marketSettingsService,
    ) {}

    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'combat_simulator_url' => $this->marketSettingsService->combatSimulatorUrl(),
        ]);
    }
}
