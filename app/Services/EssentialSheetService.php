<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Centralised client for the Xenovida Essential Sheet API.
 *
 * Mirrors BodyCompositionService: all outbound communication with the external
 * platform flows through this class, credentials and the base URL come from
 * config/services.php (the XV_API_* environment variables), and the API key
 * never leaves the server. The browser posts to the local controller, which
 * proxies the request here.
 */
class EssentialSheetService
{
    private string $baseUrl;

    private string $apiKey;

    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.xenovida.url'), '/');
        $this->apiKey = (string) config('services.xenovida.key');
        $this->timeout = (int) config('services.xenovida.timeout', 30);
    }

    /**
     * The documented endpoint path, appended to the configured base URL.
     */
    private function endpoint(): string
    {
        return $this->baseUrl.'/api/v1/essential-sheet';
    }

    /**
     * Whether the integration has the minimum configuration to run.
     */
    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->apiKey !== '';
    }

    /**
     * A pre-authenticated HTTP client with the Bearer key and shared options.
     */
    private function client(): PendingRequest
    {
        return Http::withToken($this->apiKey)
            ->accept('application/json')
            ->timeout($this->timeout);
    }

    /**
     * Submit an Essential Sheet record (application/json).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed> Normalised result envelope.
     */
    public function submit(array $payload): array
    {
        try {
            $response = $this->client()
                ->asJson()
                ->post($this->endpoint(), $payload);
        } catch (ConnectionException $e) {
            return $this->connectionError($e);
        }

        return $this->normalise($response->status(), $response->json(), $response->body());
    }

    /**
     * Convert a raw HTTP response into a stable, view-friendly envelope.
     *
     * @param  array<string, mixed>|null  $json
     * @return array{ok: bool, status: int, data: array|null, message: string, errors: array}
     */
    private function normalise(int $status, ?array $json, string $rawBody): array
    {
        $json ??= [];

        if ($status === 201 || ($status >= 200 && $status < 300 && ($json['success'] ?? false))) {
            return [
                'ok' => true,
                'status' => $status,
                'data' => $json['data'] ?? [],
                'message' => $json['message'] ?? 'Essential sheet created successfully.',
                'errors' => [],
            ];
        }

        $message = match ($status) {
            401 => $json['message'] ?? 'Authentication failed. Check the API key configuration.',
            422 => $json['message'] ?? 'The given data was invalid.',
            429 => $json['message'] ?? 'Too many attempts. Please slow down and retry shortly.',
            default => $json['message'] ?? 'The external API returned an unexpected response.',
        };

        if ($status >= 500) {
            Log::warning('Xenovida Essential API server error', ['status' => $status, 'body' => $rawBody]);
        }

        return [
            'ok' => false,
            'status' => $status,
            'data' => null,
            'message' => $message,
            'errors' => $json['errors'] ?? [],
        ];
    }

    /**
     * @return array{ok: bool, status: int, data: null, message: string, errors: array}
     */
    private function connectionError(ConnectionException $e): array
    {
        Log::error('Xenovida Essential API connection failed', ['error' => $e->getMessage()]);

        return [
            'ok' => false,
            'status' => 0,
            'data' => null,
            'message' => 'Could not reach the external API. Please verify the service is running and try again.',
            'errors' => [],
        ];
    }
}
