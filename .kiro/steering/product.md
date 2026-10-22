# Product

Xenovida Demo is a Laravel web application that demonstrates and integrates with the **Xenovida Body Composition API** (an external platform).

## What it does

- Provides a public, unauthenticated **demo page** (`/demo`) where a user can submit body composition data two ways:
  - **Manual entry** — a JSON form of measurement/metric fields.
  - **File upload** — a Tanita device export (`.csv` / `.xls` / `.xlsx`).
- The browser never calls the external API directly. Submissions post to the app, which validates input and proxies the request server-side so the API key never leaves the server. Upstream results (BMI, FFMI, BMR, segmental metrics, etc.) are normalised into a stable envelope and returned to the page.
- Includes authenticated admin areas for **user** and **user-level** management, plus standard auth (login, registration, profile) from Laravel Breeze.

## Key concepts

- **Patient reference** keys each record; patients are auto-created upstream when the reference is new.
- **Entry method** (`manual_entry` vs `file_upload`) drives validation and request shape.
- The external integration is gated on configuration (`XV_API_URL`, `XV_API_KEY`); the app degrades gracefully with a 503 when unconfigured.
