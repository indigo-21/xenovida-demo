## Option A — Manual entry (`application/json`)

### Example payload

```json
{
  "patient_reference": "PTR-000123",

  "patient": {
    "firstname": "Jane",
    "lastname": "Doe",
    "email": "jane.doe@example.com",
    "postcode": "SW1A 1AA",
    "telephone": "02079460000",
    "mobile": "07700900000",
    "dob": "1990-05-14",
    "gender": "Female",
    "patient_code": "TH",
    "external_patient_id": "EXT-001"
  },

  "entry_method": "manual_entry",
  "device_model": "Tanita MC-780",

  "measurement_date": "2026-10-21",
  "gender": "Male",
  "height_cm": 178,
  "weight_kg": 72.4,
  "body_type": "Normal",

  "body_fat_percentage": 18.2,
  "fat_mass_kg": 13.2,
  "lean_body_mass_kg": 59.2,
  "muscle_mass_kg": 34.1,
  "visceral_fat_score": 5,
  "bone_mass_kg": 3.1,
  "total_body_water_percentage": 58.3,
  "total_body_water_kg": 42.2,
  "extracellular_water_kg": 17.0,
  "intracellular_water_kg": 25.2,
  "tanita_bmr_kcal": 1680,
  "tanita_bmr_kj": 7029,
  "basal_metabolic_rate": 1680,
  "metabolic_age": 28,
  "phase_angle": 6.2,
  "physique_rating": 6,

  "left_arm_muscle_score": 5,
  "left_arm_muscle_mass_kg": 2.1,
  "left_arm_fat_score": 4,
  "left_arm_fat_mass_kg": 0.4,
  "right_arm_muscle_mass_kg": 2.2,
  "trunk_muscle_mass_kg": 24.0,
  "left_leg_muscle_mass_kg": 8.9,
  "right_leg_muscle_mass_kg": 9.0
}
```

### Required fields

| Field | Type | Constraint |
|---|---|---|
| `patient_reference` | string | max 255 |
| `entry_method` | string | `manual_entry` |
| `measurement_date` | date | on or before today |
| `gender` | string | `Male` or `Female` |
| `height_cm` | numeric | 50–250 |
| `weight_kg` | numeric | 20–350 |
| `body_type` | string | `Normal` or `Athletic` |

### Optional metric fields

| Field | Type | Constraint |
|---|---|---|
| `device_model` | string | max 100 |
| `body_fat_percentage` | numeric | 3–75 |
| `fat_mass_kg` | numeric | 0.5–200 |
| `lean_body_mass_kg` | numeric | 15–150 |
| `muscle_mass_kg` | numeric | 10–100 |
| `visceral_fat_score` | integer | 1–59 |
| `bone_mass_kg` | numeric | 0.5–5 |
| `total_body_water_percentage` | numeric | 35–75 |
| `total_body_water_kg` | numeric | 15–80 |
| `extracellular_water_kg` | numeric | 5–30 |
| `intracellular_water_kg` | numeric | 10–50 |
| `tanita_bmr_kcal` | integer | — |
| `tanita_bmr_kj` | integer | — |
| `basal_metabolic_rate` | integer | — |
| `metabolic_age` | integer | 1–120 |
| `phase_angle` | numeric | 1–15 |
| `physique_rating` | integer | 1–9 |

### Optional segmental fields

For each segment in `left_arm`, `right_arm`, `trunk`, `left_leg`, `right_leg` (all nullable, numeric):

- `{segment}_muscle_score`
- `{segment}_muscle_mass_kg`
- `{segment}_fat_score`
- `{segment}_fat_mass_kg`
- `{segment}_fat_percent` *(Tanita import field)*
- `{segment}_fat_amount` *(Tanita import field)*
- `{segment}_free_fat_score` *(Tanita import field)*

---

## Option B — File upload (`multipart/form-data`)

Metric fields are ignored in this mode; values are extracted from the Tanita file.

```
POST /api/v1/body-composition
Authorization: Bearer xv_your_api_key
Accept: application/json
Content-Type: multipart/form-data

patient_reference = PTR-000123
entry_method      = file_upload
device_model      = Tanita MC-780      (optional)
tanita_file       = @export.csv        (required)
```

New-patient fields use bracket notation: `patient[firstname]`, `patient[gender]`, etc.

### Fields

| Field | Type | Constraint |
|---|---|---|
| `patient_reference` | string | required, max 255 |
| `entry_method` | string | `file_upload` |
| `tanita_file` | file | required, `.csv` / `.xls` / `.xlsx`, ≤ 10 MB |
| `device_model` | string | optional, max 100 |

---

## New-patient object (`patient.*`)

Used only when `patient_reference` does not match an existing patient. All fields optional; defaults applied when omitted.

| Field | Type | Constraint | Default when omitted |
|---|---|---|---|
| `patient.firstname` | string | max 255 | `Unknown` |
| `patient.lastname` | string | max 255 | `Patient` |
| `patient.email` | email | max 255 | generated placeholder (unique, derived from reference) |
| `patient.postcode` | string | max 255 | empty string |
| `patient.telephone` | string | max 255 | `null` |
| `patient.mobile` | string | max 255 | `null` |
| `patient.dob` | date | on or before today | `null` |
| `patient.gender` | string | `Male` / `Female` / `Other` | `Other` |
| `patient.patient_code` | string | max 255 | `null` |
| `patient.external_patient_id` | string | max 255 | `null` |

---

## Examples

### cURL — manual entry

```bash
curl -X POST https://your-host/api/v1/body-composition \
  -H "Authorization: Bearer xv_your_api_key" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_reference": "PTR-000123",
    "entry_method": "manual_entry",
    "measurement_date": "2026-10-21",
    "gender": "Male",
    "height_cm": 178,
    "weight_kg": 72.4,
    "body_type": "Normal",
    "body_fat_percentage": 18.2
  }'
```

### cURL — file upload

```bash
curl -X POST https://your-host/api/v1/body-composition \
  -H "Authorization: Bearer xv_your_api_key" \
  -H "Accept: application/json" \
  -F "patient_reference=PTR-000123" \
  -F "entry_method=file_upload" \
  -F "tanita_file=@/path/to/export.csv"
```

---

## Responses

### `201 Created`

```json
{
  "success": true,
  "message": "Body composition record created successfully.",
  "data": {
    "patient": {
      "id": 7,
      "patient_reference": "PTR-000123",
      "full_name": "Jane Doe",
      "created": false
    },
    "measurement": {
      "id": 42,
      "bmi": 22.9,
      "bmi_classification": "Normal Weight",
      "bmi_colour": "#28A745",
      "ffmi": 19.1,
      "bmr_kcal": 1680,
      "muscle_mass_percentage": 47.1,
      "segmental": null
    }
  }
}
```

The `measurement` object contains the full computed metric set (BMI, FFMI, BMR variants, water ratios, classifications/colours, metabolic age difference, physique label) and the `segmental` block when present. `patient.created` is `true` when the patient was auto-created by this request.

### Error responses

| Status | Condition | Body |
|---|---|---|
| `401` | Missing / invalid / expired API key | `{ "message": "..." }` |
| `422` | Validation failed | `{ "success": false, "message": "The given data was invalid.", "errors": { "field": ["..."] } }` |
| `429` | Rate limit exceeded | `{ "message": "Too Many Attempts." }` |

#### Example `422`

```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "measurement_date": ["The measurement date field is required."],
    "height_cm": ["The height cm field must be between 50 and 250."]
  }
}
```
