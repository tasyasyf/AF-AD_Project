# AF/AD Management & Claim Processing System

A web-based management system for Academic Facilitators (AF) and Academic Developers (AD) built with Laravel 13. The system handles profile registration, appointment tracking, claim submission, executive verification workflows, and a full audit trail.

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [User Roles](#user-roles)
- [Features](#features)
- [System Flow](#system-flow)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation & Setup](#installation--setup)
- [Demo Accounts](#demo-accounts)
- [Web Routes](#web-routes)
- [REST API](#rest-api)
  - [Authentication](#authentication)
  - [AF/AD Endpoints](#afad-endpoints)
  - [Executive Endpoints](#executive-endpoints)
  - [API Error Responses](#api-error-responses)
- [Claim Status Lifecycle](#claim-status-lifecycle)
- [Document Checklist](#document-checklist)

---

## Overview

The AF/AD Management & Claim Processing System digitises the end-to-end workflow of:

1. **AF/AD profile registration** — personal info, qualifications, and bank details
2. **Certificate verification** — uploaded credentials reviewed by the School Executive
3. **Appointment management** — course assignments created by the School Executive
4. **Claim submission** — AFs/ADs submit payment claims with supporting documents
5. **Approval workflow** — School Executive approves, returns, or rejects each claim
6. **Audit trail** — every action on a claim is logged with user, timestamp, and remarks

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.3) |
| Database | SQLite (local) |
| Authentication (Web) | Laravel Session Auth |
| Authentication (API) | Laravel Sanctum (Bearer Token) |
| Frontend | Blade Templates + Bootstrap 5 (CDN) |
| Icons | Bootstrap Icons (CDN) |

---

## User Roles

| Role | Description |
|---|---|
| `afad` | Academic Facilitator / Academic Developer — registers profile, submits claims, views own history |
| `executive` | School Executive — verifies profiles, creates appointments, approves/returns/rejects claims |
| `admin` | Administrator — read-only overview of all profiles, appointments, and claims |

> An AD may concurrently hold an AF role. Both are managed under the same `afad` role type.

---

## Features

### AF/AD
- Register and edit personal profile (name, IC, contact, qualification, bank account)
- Upload qualification certificates (PDF/image)
- View assigned appointments (course, role, semester, dates)
- Create claim submissions linked to an appointment
- Upload supporting documents per checklist
- Submit, edit (if draft/returned), or delete draft claims
- View claim status and full activity log

### School Executive
- View and filter all AF/AD profiles by status
- Verify or reject profiles (with mandatory rejection reason)
- Create and manage course appointments for verified AF/ADs
- Review submitted claims with supporting documents
- Approve, return for revision, or reject claims with remarks

### Admin
- Dashboard with system-wide statistics (totals, approved amounts)
- Read-only view of all profiles, appointments, and claims
- Full audit trail per claim

---

## System Flow

```
AF/AD registers profile
        │
        ▼
Executive verifies profile ──► Rejected (AF/AD can edit & resubmit)
        │
        ▼
Executive creates appointment for AF/AD
        │
        ▼
AF/AD creates claim (draft) → uploads required documents
        │
        ▼
AF/AD submits claim
        │
        ▼
Executive reviews claim
        ├──► Approved ✓
        ├──► Returned (AF/AD edits & resubmits)
        └──► Rejected ✗
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── AfAd/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── CertificateController.php
│   │   │   ├── AppointmentController.php
│   │   │   ├── ClaimController.php
│   │   │   └── ClaimDocumentController.php
│   │   ├── Executive/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileVerificationController.php
│   │   │   ├── AppointmentController.php
│   │   │   └── ClaimReviewController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── AppointmentController.php
│   │   │   └── ClaimController.php
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── ProfileController.php
│   │       ├── AppointmentController.php
│   │       ├── ClaimController.php
│   │       └── ExecutiveController.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── User.php
│   ├── Profile.php
│   ├── Certificate.php
│   ├── Appointment.php
│   ├── Claim.php
│   ├── ClaimDocument.php
│   └── ClaimAudit.php
database/
├── migrations/
│   ├── ..._create_users_table.php
│   ├── ..._create_profiles_table.php
│   ├── ..._create_certificates_table.php
│   ├── ..._create_appointments_table.php
│   ├── ..._create_claims_table.php
│   ├── ..._create_claim_documents_table.php
│   └── ..._create_claim_audits_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── UserSeeder.php
    ├── ProfileSeeder.php
    ├── AppointmentSeeder.php
    └── ClaimSeeder.php
resources/views/
├── components/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── guest.blade.php
│   ├── alert.blade.php
│   ├── status-badge.blade.php
│   ├── confirm-modal.blade.php
│   └── document-row.blade.php
├── auth/login.blade.php
├── afad/
├── executive/
└── admin/
routes/
├── web.php
└── api.php
```

---

## Database Schema

```
users
├── id, name, email, password
├── role          ENUM(afad, executive, admin)
└── is_active     BOOLEAN

profiles
├── id, user_id → users
├── full_name, ic_number, phone, address, contact_email
├── qualification, qualification_level, specialisation
├── bank_name, bank_account_number, bank_account_holder
├── status        ENUM(pending, verified, rejected)
└── verified_by → users, verified_at, rejection_reason

certificates
├── id, profile_id → profiles
├── title, issuing_institution, year_obtained
├── file_path, file_original_name, file_mime, file_size
└── is_verified, verified_by → users, verified_at

appointments
├── id, profile_id → profiles
├── course_code, course_name
├── role_type     ENUM(af, ad)
├── semester, academic_session
├── start_date, end_date, venue, student_count
└── appointed_by → users, is_active

claims
├── id, appointment_id → appointments, profile_id → profiles
├── claim_reference   (auto-generated: CLAIM-YYYY-NNNNN)
├── claim_type        ENUM(teaching, marking, module_development, consultation)
├── period_from, period_to
├── total_hours, rate_per_hour, total_amount
├── status            ENUM(draft, submitted, under_review, approved, returned, rejected)
├── submitted_at
└── reviewed_by → users, reviewed_at, executive_remarks

claim_documents
├── id, claim_id → claims
├── document_type     ENUM(attendance_sheet, lesson_plan, marking_scheme, ...)
├── label, is_required, is_uploaded
└── file_path, file_original_name, uploaded_at

claim_audits  (append-only)
├── id, claim_id → claims
├── performed_by → users
├── action        ENUM(created, submitted, approved, rejected, returned_for_revision, ...)
├── from_status, to_status, remarks
└── created_at    (no updated_at — immutable)
```

---

## Installation & Setup

### Requirements
- PHP 8.3+
- Composer

### Steps

```bash
# 1. Clone the repository
git clone <repo-url>
cd AD-Project

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and generate key
cp .env.example .env
php artisan key:generate

# 4. Run migrations and seed demo data
php artisan migrate:fresh --seed

# 5. Start the development server
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`.

---

## Demo Accounts

All demo accounts use the password: **`password`**

| Name | Email | Role |
|---|---|---|
| System Administrator | `admin@system.my` | Admin |
| Dr. Sarah Ahmad | `executive@system.my` | School Executive |
| Ahmad bin Ali | `afad1@system.my` | AF/AD (verified, has appointments & claims) |
| Siti binti Hassan | `afad2@system.my` | AF/AD (pending verification) |

---

## Web Routes

### Public
| Method | URI | Description |
|---|---|---|
| GET | `/login` | Login page |
| POST | `/login` | Authenticate user |
| POST | `/logout` | Logout |

### AF/AD (`/afad/*`) — requires `afad` role
| Method | URI | Description |
|---|---|---|
| GET | `/afad/dashboard` | Dashboard |
| GET | `/afad/profile` | View own profile |
| GET | `/afad/profile/create` | Profile registration form |
| POST | `/afad/profile` | Submit profile |
| GET | `/afad/profile/edit` | Edit profile |
| PUT | `/afad/profile` | Update profile |
| GET | `/afad/certificates` | List certificates |
| POST | `/afad/certificates` | Upload certificate |
| DELETE | `/afad/certificates/{id}` | Remove certificate |
| GET | `/afad/appointments` | List appointments |
| GET | `/afad/appointments/{id}` | View appointment |
| GET | `/afad/claims` | List claims |
| POST | `/afad/claims` | Create draft claim |
| GET | `/afad/claims/{id}` | View claim + document checklist |
| PUT | `/afad/claims/{id}` | Update draft/returned claim |
| POST | `/afad/claims/{id}/submit` | Submit claim for review |
| DELETE | `/afad/claims/{id}` | Delete draft claim |
| POST | `/afad/claims/{id}/documents/{docId}/upload` | Upload document |
| DELETE | `/afad/claims/{id}/documents/{docId}` | Remove document |

### School Executive (`/executive/*`) — requires `executive` role
| Method | URI | Description |
|---|---|---|
| GET | `/executive/dashboard` | Dashboard |
| GET | `/executive/profiles` | List all AF/AD profiles |
| GET | `/executive/profiles/{id}` | View profile detail |
| POST | `/executive/profiles/{id}/verify` | Verify profile |
| POST | `/executive/profiles/{id}/reject` | Reject profile (reason required) |
| GET | `/executive/appointments` | List appointments |
| GET | `/executive/appointments/create` | New appointment form |
| POST | `/executive/appointments` | Create appointment |
| GET | `/executive/appointments/{id}` | View appointment |
| PUT | `/executive/appointments/{id}` | Update appointment |
| GET | `/executive/claims` | List claims for review |
| GET | `/executive/claims/{id}` | View claim detail |
| POST | `/executive/claims/{id}/approve` | Approve claim |
| POST | `/executive/claims/{id}/return` | Return for revision (reason required) |
| POST | `/executive/claims/{id}/reject` | Reject claim (reason required) |

### Admin (`/admin/*`) — requires `admin` role
| Method | URI | Description |
|---|---|---|
| GET | `/admin/dashboard` | System statistics |
| GET | `/admin/profiles` | View all profiles |
| GET | `/admin/profiles/{id}` | View profile detail |
| GET | `/admin/appointments` | View all appointments |
| GET | `/admin/appointments/{id}` | View appointment detail |
| GET | `/admin/claims` | View all claims |
| GET | `/admin/claims/{id}` | View claim + full audit trail |

---

## REST API

The API uses **Laravel Sanctum** (Bearer token). All endpoints are prefixed with `/api`. No CSRF token is required.

### Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "afad1@system.my",
  "password": "password",
  "device_name": "MyApp"
}
```
**Response `200`:**
```json
{
  "token": "1|AbCdEfGh...",
  "token_type": "Bearer",
  "user": {
    "id": 3,
    "name": "Ahmad bin Ali",
    "email": "afad1@system.my",
    "role": "afad"
  }
}
```

Use the token in all subsequent requests:
```http
Authorization: Bearer 1|AbCdEfGh...
```

#### Logout
```http
POST /api/logout
Authorization: Bearer <token>
```

#### Get current user
```http
GET /api/me
Authorization: Bearer <token>
```

---

### AF/AD Endpoints

All require: `Authorization: Bearer <afad-token>`

#### Get own profile
```http
GET /api/afad/profile
```
**Response `200`:**
```json
{
  "id": 1,
  "full_name": "Ahmad bin Ali",
  "ic_number": "850101-01-1234",
  "phone": "012-3456789",
  "contact_email": "ahmad.ali@mail.com",
  "qualification": "Master of Education (Instructional Technology)",
  "qualification_level": "masters",
  "bank_name": "Maybank",
  "bank_account_number": "1234567890",
  "status": "verified",
  "certificates": [
    {
      "id": 1,
      "title": "Master of Education (Instructional Technology)",
      "issuing_institution": "Universiti Teknologi Malaysia",
      "year_obtained": 2012,
      "is_verified": true
    }
  ]
}
```

#### Register profile
```http
POST /api/afad/profile
Content-Type: application/json

{
  "full_name": "Ahmad bin Ali",
  "ic_number": "850101-01-1234",
  "phone": "012-3456789",
  "address": "No. 12, Jalan Merdeka, Subang Jaya",
  "contact_email": "ahmad@mail.com",
  "qualification": "Master of Education",
  "qualification_level": "masters",
  "bank_name": "Maybank",
  "bank_account_number": "1234567890",
  "bank_account_holder": "Ahmad bin Ali"
}
```
**Response `201`:**
```json
{
  "message": "Profile registered successfully. Awaiting verification.",
  "profile": { "id": 1, "status": "pending" }
}
```

#### List appointments
```http
GET /api/afad/appointments
```
**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "course_code": "BBB1013",
      "course_name": "Bahasa Melayu Komunikasi",
      "role_type": "af",
      "semester": "2024/2025-1",
      "start_date": "2024-09-01",
      "end_date": "2025-01-31",
      "is_active": true
    }
  ]
}
```

#### List claims
```http
GET /api/afad/claims
GET /api/afad/claims?status=submitted
```
Supported `status` filter values: `draft` `submitted` `under_review` `approved` `returned` `rejected`

#### Create a claim (saves as draft)
```http
POST /api/afad/claims
Content-Type: application/json

{
  "appointment_id": 1,
  "claim_type": "teaching",
  "period_from": "2025-01-01",
  "period_to": "2025-03-31",
  "total_hours": 20,
  "rate_per_hour": 50.00
}
```
**Response `201`:**
```json
{
  "message": "Claim created as draft.",
  "claim": {
    "id": 4,
    "claim_reference": "CLAIM-2025-00004",
    "claim_type": "teaching",
    "total_hours": "20.00",
    "rate_per_hour": "50.00",
    "total_amount": "1000.00",
    "status": "draft"
  }
}
```

Supported `claim_type` values: `teaching` `marking` `module_development` `consultation`

#### View claim detail
```http
GET /api/afad/claims/{id}
```
Returns claim details including `documents` array and `audit_trail` array.

#### Submit a claim
```http
POST /api/afad/claims/{id}/submit
```
> Returns `422` if any required document has not been uploaded.

---

### Executive Endpoints

All require: `Authorization: Bearer <executive-token>`

#### List all profiles
```http
GET /api/executive/profiles
GET /api/executive/profiles?status=pending
```
Supported `status` filter values: `pending` `verified` `rejected`

#### Verify a profile
```http
POST /api/executive/profiles/{id}/verify
```
**Response `200`:**
```json
{ "message": "Profile for Ahmad bin Ali verified successfully." }
```

#### Reject a profile
```http
POST /api/executive/profiles/{id}/reject
Content-Type: application/json

{
  "rejection_reason": "Missing academic certificate. Please upload your degree certificate."
}
```

#### List claims for review
```http
GET /api/executive/claims
GET /api/executive/claims?status=submitted
```

#### Approve a claim
```http
POST /api/executive/claims/{id}/approve
Content-Type: application/json

{
  "remarks": "All documents verified and in order."
}
```

#### Return a claim for revision
```http
POST /api/executive/claims/{id}/return
Content-Type: application/json

{
  "remarks": "Attendance sheet is incomplete. Please upload the correct version."
}
```

#### Reject a claim
```http
POST /api/executive/claims/{id}/reject
Content-Type: application/json

{
  "remarks": "Duplicate claim submitted for the same period."
}
```

---

### API Error Responses

| HTTP Code | Meaning |
|---|---|
| `401` | Unauthenticated — missing or invalid token |
| `403` | Forbidden — wrong role for this endpoint |
| `404` | Resource not found |
| `409` | Conflict — e.g. profile already exists |
| `422` | Validation failed |

**Validation error example (`422`):**
```json
{
  "message": "The total hours field is required.",
  "errors": {
    "total_hours": ["The total hours field is required."]
  }
}
```

---

## Claim Status Lifecycle

```
draft ──► submitted ──► approved ✓
               │
               ├──► returned ──► (AF/AD edits) ──► submitted
               │
               └──► rejected ✗
```

| Status | Set By | Description |
|---|---|---|
| `draft` | AF/AD | Created but not yet submitted |
| `submitted` | AF/AD | Submitted for executive review |
| `under_review` | Executive | Being actively reviewed |
| `approved` | Executive | Approved for payment |
| `returned` | Executive | Returned to AF/AD for corrections |
| `rejected` | Executive | Permanently rejected |

---

## Document Checklist

Documents are auto-created per claim type when a claim is first saved.

| Claim Type | Required Documents | Optional Documents |
|---|---|---|
| `teaching` | Attendance Sheet, Lesson Plan | Student List |
| `marking` | Marking Scheme, Assignment Sample | Attendance Sheet |
| `module_development` | Module Draft / Outline, Approval Letter | — |
| `consultation` | Consultation Record | Supporting Document |

Accepted formats: **PDF, JPG, JPEG, PNG** — max **10 MB** per file.
