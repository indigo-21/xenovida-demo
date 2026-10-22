<?php

namespace App\Http\Controllers;

use App\Http\Requests\BodyCompositionRequest;
use App\Services\BodyCompositionService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Public demo for the Xenovida Body Composition API integration.
 *
 * The browser never talks to the external API directly; it posts to this
 * controller, which validates input and proxies the request through the
 * BodyCompositionService so the API key stays on the server.
 */
class BodyCompositionController extends Controller
{
    public function __construct(private readonly BodyCompositionService $service) {}

    /**
     * Render the interactive demo page.
     */
    public function show(): View
    {
        return view('demo', [
            'apiConfigured' => $this->service->isConfigured(),
        ]);
    }

    /**
     * Validate and forward a submission (manual entry or file upload).
     */
    public function store(BodyCompositionRequest $request): JsonResponse
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

        $validated = $request->validated();

        if (($validated['entry_method'] ?? null) === 'file_upload') {
            $fields = collect($validated)
                ->except(['tanita_file'])
                ->reject(fn ($v) => $v === null || $v === '')
                ->all();

            $result = $this->service->submitFile($fields, $request->file('tanita_file'));
        } else {
            $payload = collect($validated)
                ->reject(fn ($v) => $v === null || $v === '')
                ->all();

            $result = $this->service->submitManual($payload);
        }

        // Surface the upstream status to the client (201 success, 422 validation, etc.),
        // defaulting to 200 so the frontend can read the normalised envelope.
        $httpStatus = $result['ok'] ? 200 : ($result['status'] >= 400 ? $result['status'] : 422);

        return response()->json($result, $httpStatus);
    }
}
