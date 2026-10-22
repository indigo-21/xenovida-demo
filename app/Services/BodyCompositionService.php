<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Centralised client for the Xenovida Body Composition API.
 *
 * All outbound communication with the external platform flows through this
 * class. Credentials and the base URL are pulled from config/services.php
 * (which reads the XV_API_* environment variables) so the API key is never
 * hardcoded and never leaves the server.
 */
class BodyCompositionService
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
        return $this->baseUrl . '/api/v1/body-composition';
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
     * Submit a manual-entry (application/json) body composition record.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>  Normalised result envelope.
     */
    public function submitManual(array $payload): array
    {
        $payload['entry_method'] = 'manual_entry';

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
     * Submit a file-upload (multipart/form-data) body composition record.
     *
     * Metric fields are ignored by the API in this mode; only the reference,
     * optional device model, optional new-patient object and the Tanita file
     * are sent.
     *
     * @param  array<string, mixed>  $fields  Non-file form fields (incl. patient[...]).
     * @return array<string, mixed>  Normalised result envelope.
     */
    public function submitFile(array $fields, UploadedFile $file): array
    {
        $fields['entry_method'] = 'file_upload';

        try {
            $response = $this->client()
                ->attach(
                    'tanita_file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post($this->endpoint(), $this->flatten($fields));
        } catch (ConnectionException $e) {
            return $this->connectionError($e);
        }

        return $this->normalise($response->status(), $response->json(), $response->body());
    }

    /**
     * Flatten a nested array into bracket-notation keys for multipart bodies,
     * e.g. ['patient' => ['firstname' => 'Jane']] => ['patient[firstname]' => 'Jane'].
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function flatten(array $data, string $prefix = ''): array
    {
        $flat = [];

        foreach ($data as $key => $value) {
            $name = $prefix === '' ? $key : "{$prefix}[{$key}]";

            if (is_array($value)) {
                $flat += $this->flatten($value, $name);
            } elseif ($value !== null && $value !== '') {
                $flat[$name] = $value;
            }
        }

        return $flat;
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
                'message' => $json['message'] ?? 'Record created successfully.',
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
            Log::warning('Xenovida API server error', ['status' => $status, 'body' => $rawBody]);
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
        Log::error('Xenovida API connection failed', ['error' => $e->getMessage()]);

        return [
            'ok' => false,
            'status' => 0,
            'data' => null,
            'message' => 'Could not reach the external API. Please verify the service is running and try again.',
            'errors' => [],
        ];
    }
}
