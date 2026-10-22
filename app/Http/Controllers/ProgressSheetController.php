<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgressSheetRequest;
use App\Services\ProgressSheetService;
use Illuminate\Http\JsonResponse;

/**
 * Public demo for the Xenovida Essential Progress Sheet API integration.
 *
 * The browser never talks to the external API directly; it posts to this
 * controller, which validates input and proxies the request through the
 * ProgressSheetService so the API key stays on the server. The demo page
 * itself is rendered by BodyCompositionController@show (shared /demo view);
 * this controller only handles the Progress submission.
 */
class ProgressSheetController extends Controller
{
    public function __construct(private readonly ProgressSheetService $service) {}

    /**
     * Validate and forward an Essential Progress Sheet submission.
     */
    public function store(ProgressSheetRequest $request): JsonResponse
    {
        if (! $this->service->isConfigured()) {
            return response()->json([
                'ok' => false,
                'status' => 0,
                'message' => 'The external API is not configured. Set XV_API_URL and XV_API_KEY.',
                'errors' => [],
                'data' => null,
            ], 503);
        }

        $payload = collect($request->validated())
            ->reject(fn ($v) => $v === null || $v === '')
            ->all();

        $result = $this->service->submit($payload);

        // Surface the upstream status (201 success, 422 validation, etc.),
        // defaulting to 200 so the frontend can read the normalised envelope.
        $httpStatus = $result['ok'] ? 200 : ($result['status'] >= 400 ? $result['status'] : 422);

        return response()->json($result, $httpStatus);
    }
}
