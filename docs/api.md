# YakTrack REST API Documentation

## Overview

YakTrack provides a versioned REST API for external applications (CLI tools, integrations, etc.) to manage time tracking data programmatically.

- **Base URL:** `/api/v1`
- **Authentication:** Bearer token (Laravel Sanctum)
- **Content Type:** `application/json`
- **Pagination:** All list endpoints return paginated results (15 per page by default, configurable via `?per_page=N`)

## Authentication

The API uses Laravel Sanctum personal access tokens. Include the token in the `Authorization` header:

```
Authorization: Bearer {your-token}
```

### Creating a Token

Tokens can be created via Tinker or a custom command:

```php
$user = User::find(1);
$token = $user->createToken('cli');
echo $token->plainTextToken;
```

### Verifying Authentication

```
GET /api/v1/user
```

Returns the authenticated user. Use this to verify your token is valid.

All endpoints return `401 Unauthorized` if the token is missing or invalid.

---

## Resources

### Clients

#### List Clients

```
GET /api/v1/clients
```

Returns clients ordered by name.

**Response:** `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "name": "Acme Corp",
      "email": "contact@acme.com",
      "is_billable": true,
      "created_at": "2026-01-15T09:00:00+00:00",
      "updated_at": "2026-01-15T09:00:00+00:00"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1 }
}
```

#### Create Client

```
POST /api/v1/clients
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `name` | string | Yes | max 255 characters |
| `email` | string | No | valid email, max 255 characters |

**Response:** `201 Created`

#### Show Client

```
GET /api/v1/clients/{id}
```

**Response:** `200 OK`

#### Update Client

```
PUT /api/v1/clients/{id}
```

Accepts the same fields as create. All fields are optional on update.

**Response:** `200 OK`

#### Delete Client

```
DELETE /api/v1/clients/{id}
```

**Response:** `204 No Content`

---

### Projects

#### List Projects

```
GET /api/v1/projects
```

Returns projects ordered by name. Includes the `client` relationship.

**Response fields:**

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | |
| `name` | string | |
| `description` | string | |
| `task_code_prefix` | string\|null | Prefix for auto-generated task codes |
| `is_billable` | boolean | |
| `archived_at` | string\|null | ISO 8601 timestamp if archived |
| `client_id` | integer\|null | |
| `client` | object\|null | Nested client resource |
| `created_at` | string | ISO 8601 |
| `updated_at` | string | ISO 8601 |

#### Create Project

```
POST /api/v1/projects
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `name` | string | Yes | max 255 characters |
| `description` | string | No | |
| `client_id` | integer | No | must reference an existing client |
| `task_code_prefix` | string | No | max 20 characters |
| `is_billable` | boolean | No | |

**Response:** `201 Created`

#### Show Project

```
GET /api/v1/projects/{id}
```

**Response:** `200 OK`

#### Update Project

```
PUT /api/v1/projects/{id}
```

Accepts the same fields as create. All fields are optional on update.

**Response:** `200 OK`

#### Delete Project

```
DELETE /api/v1/projects/{id}
```

Projects with associated tasks, sprints, acceptance criteria, or test runs cannot be deleted.

**Response:** `204 No Content` on success, `422 Unprocessable Entity` if the project has dependencies.

#### Archive Project

```
PATCH /api/v1/projects/{id}/archive
```

**Response:** `200 OK` with the updated project (includes `archived_at` timestamp).

#### Unarchive Project

```
PATCH /api/v1/projects/{id}/unarchive
```

**Response:** `200 OK` with the updated project (`archived_at` set to null).

---

### Tasks

#### List Tasks

```
GET /api/v1/tasks
```

Returns tasks ordered by ID descending (newest first). Includes `project.client` and `taskStatus` relationships.

**Response fields:**

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | |
| `name` | string | |
| `description` | string | |
| `status` | string | Legacy status field |
| `is_billable` | boolean | |
| `project_id` | integer\|null | |
| `status_id` | integer\|null | References a task status |
| `project` | object\|null | Nested project resource |
| `task_status` | object\|null | `{id, name}` |
| `created_at` | string | ISO 8601 |
| `updated_at` | string | ISO 8601 |

#### Create Task

```
POST /api/v1/tasks
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `name` | string | Yes | max 255, unique per project |
| `description` | string | No | |
| `project_id` | integer | No | must reference an existing project |
| `status_id` | integer | No | must reference an existing task status |
| `is_billable` | boolean | No | |

If `status_id` is not provided and `project_id` is set, the project's default task status is assigned automatically.

**Response:** `201 Created`

#### Show Task

```
GET /api/v1/tasks/{id}
```

**Response:** `200 OK`

#### Update Task

```
PUT /api/v1/tasks/{id}
```

Accepts the same fields as create. All fields are optional on update.

**Response:** `200 OK`

#### Delete Task

```
DELETE /api/v1/tasks/{id}
```

**Response:** `204 No Content`

---

### Sessions

#### List Sessions

```
GET /api/v1/sessions
```

Returns sessions ordered by ID descending (newest first). Includes the `task.project` relationship.

**Response fields:**

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | |
| `started_at` | string | ISO 8601 |
| `ended_at` | string\|null | ISO 8601, null if running |
| `comment` | string\|null | |
| `is_billable` | boolean | |
| `is_running` | boolean | True if `ended_at` is null |
| `duration_in_seconds` | integer | Elapsed seconds |
| `duration_for_humans` | string | e.g. "2h 30m" |
| `task_id` | integer\|null | |
| `invoice_id` | integer\|null | |
| `sprint_id` | integer\|null | |
| `session_category_id` | integer\|null | |
| `task` | object\|null | Nested task resource |
| `created_at` | string | ISO 8601 |
| `updated_at` | string | ISO 8601 |

#### Create Session

```
POST /api/v1/sessions
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `started_at` | string | Yes | valid datetime |
| `ended_at` | string | No | valid datetime, must be after `started_at` |
| `comment` | string | No | |
| `is_billable` | boolean | No | defaults to true |
| `task_id` | integer | No | must reference an existing task |
| `invoice_id` | integer | No | must reference an existing invoice |
| `sprint_id` | integer | No | must reference an existing sprint |
| `session_category_id` | integer | No | must reference an existing session category |

If `ended_at` is omitted (creating a running session), any currently running sessions are stopped automatically.

**Response:** `201 Created`

#### Show Session

```
GET /api/v1/sessions/{id}
```

**Response:** `200 OK`

#### Update Session

```
PUT /api/v1/sessions/{id}
```

Accepts the same fields as create. All fields are optional on update.

**Response:** `200 OK`

#### Delete Session

```
DELETE /api/v1/sessions/{id}
```

**Response:** `204 No Content`

#### Start Session

```
POST /api/v1/sessions/start
```

Stops any running sessions, then creates a new running session with `is_billable: true`. No request body required.

**Response:** `201 Created`

#### Stop Sessions

```
POST /api/v1/sessions/stop
```

Stops all currently running sessions. No request body required.

**Response:** `200 OK`

```json
{
  "message": "2 session(s) stopped.",
  "stopped": [{ ... }, { ... }]
}
```

#### Continue Session

```
POST /api/v1/sessions/{id}/continue
```

Stops any running sessions, then creates a new running session that copies the task, billable status, session category, and sprint from the referenced session. If the original sprint is closed, an open sprint for the same project is used instead.

**Response:** `201 Created`

---

### Sprints

#### List Sprints

```
GET /api/v1/sprints
```

Returns sprints ordered by ID descending (newest first). Includes the `projects` relationship.

**Response fields:**

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | |
| `name` | string | |
| `is_open` | boolean | |
| `projects` | array | Nested project resources |
| `created_at` | string | ISO 8601 |
| `updated_at` | string | ISO 8601 |

#### Create Sprint

```
POST /api/v1/sprints
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `name` | string | Yes | max 255, unique |
| `is_open` | boolean | No | |
| `project_ids` | array | Yes | at least one valid project ID |

**Response:** `201 Created`

#### Show Sprint

```
GET /api/v1/sprints/{id}
```

**Response:** `200 OK`

#### Update Sprint

```
PUT /api/v1/sprints/{id}
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `name` | string | No | max 255, unique (excluding current) |
| `is_open` | boolean | No | |
| `project_ids` | array | No | if provided, replaces all project associations |

**Response:** `200 OK`

#### Delete Sprint

```
DELETE /api/v1/sprints/{id}
```

**Response:** `204 No Content`

---

### Invoices

#### List Invoices

```
GET /api/v1/invoices
```

Returns invoices ordered by ID descending (newest first). Includes the `client` relationship.

**Response fields:**

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | |
| `number` | string\|null | Invoice number |
| `date` | string\|null | Invoice date |
| `due_date` | string\|null | |
| `amount` | integer\|null | Amount in cents |
| `total_hours` | number\|null | |
| `description` | string\|null | |
| `is_paid` | boolean | |
| `is_sent` | boolean | |
| `client_id` | integer\|null | |
| `client` | object\|null | Nested client resource |
| `created_at` | string | ISO 8601 |
| `updated_at` | string | ISO 8601 |

#### Create Invoice

```
POST /api/v1/invoices
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `number` | string | No | max 255, unique |
| `date` | string | No | valid date |
| `due_date` | string | No | valid date |
| `amount` | number | No | in dollars (stored as cents) |
| `total_hours` | number | No | |
| `description` | string | No | |
| `is_paid` | boolean | No | |
| `is_sent` | boolean | No | |
| `client_id` | integer | No | must reference an existing client |

The `amount` field accepts dollars (e.g. `1500.50`) and is stored as cents (`150050`). Responses return the value in cents.

**Response:** `201 Created`

#### Show Invoice

```
GET /api/v1/invoices/{id}
```

**Response:** `200 OK`

#### Update Invoice

```
PUT /api/v1/invoices/{id}
```

Accepts the same fields as create. All fields are optional on update. The `amount` field uses the same dollars-to-cents conversion.

**Response:** `200 OK`

#### Delete Invoice

```
DELETE /api/v1/invoices/{id}
```

**Response:** `204 No Content`

---

## Error Responses

### Validation Error (422)

```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

### Not Found (404)

```json
{
  "message": "No query results for model [App\\Models\\Client] 999."
}
```

### Unauthenticated (401)

```json
{
  "message": "Unauthenticated."
}
```

### Rate Limited (429)

The API allows 60 requests per minute per IP. Exceeding this limit returns a `429 Too Many Requests` response.
