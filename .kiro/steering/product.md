# Product

Xenovida Demo is a Laravel web application that demonstrates and integrates with the external **Xenovida** platform APIs (Body Composition, Essential Sheet, Progress Sheet).

## What it does

- Provides a public, unauthenticated **demo page** (`/demo`) with separate forms for each integration:
  - **Body Composition** — manual JSON entry of measurement/metric fields, or file upload of a Tanita device export (`.csv` / `.xls` / `.xlsx`).
  - **Essential Sheet** — manual JSON entry, posted to the Essential Sheet endpoint.
  - **Progress Sheet** — manual JSON entry, posted to the Progress Sheet endpoint.
- The browser never calls the external API directly. Submissions post to the app, which validates input and proxies the request server-side so the API key never leaves the server. Upstream results (BMI, FFMI, BMR, segmental metrics, etc.) are normalised into a stable envelope and returned to the page.
- Includes authenticated admin areas for **user** and **user-level** management, plus standard auth (login, registration, profile) from Laravel Breeze.

## Key concepts

- **Patient reference** keys each record; patients are auto-created upstream when the reference is new.
- **Entry method** (`manual_entry` vs `file_upload`) drives validation and request shape for Body Composition.
- Each integration returns a normalised envelope: `{ ok, status, data, message, errors }`.
- The external integration is gated on configuration (`XV_API_URL`, `XV_API_KEY`); the app degrades gracefully with a 503 when unconfigured.
