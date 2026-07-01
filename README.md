# AF/AD Management & Claim Processing System

A web-based management system for **Academic Facilitators (AF)** and **Academic Developers (AD)** built with **Laravel 13**. The system handles profile registration, document & certificate verification, appointment and class tracking, video/document submissions, multi-stage claim approval (Program Coordinator endorsement → Executive approval), in-app notifications, and a full audit trail.

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [User Roles](#user-roles)
- [Features](#features)
- [System Flow](#system-flow)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Middleware & Access Control](#middleware--access-control)
- [Installation & Setup](#installation--setup)
- [Demo Accounts](#demo-accounts)
- [Web Routes](#web-routes)
- [REST API](#rest-api)
  - [Authentication](#authentication)
  - [AF/AD Endpoints](#afad-endpoints)
  - [Executive Endpoints](#executive-endpoints)
  - [API Error Responses](#api-error-responses)
- [Claim Status Lifecycle](#claim-status-lifecycle)
- [Submissions & Document Checklist](#submissions--document-checklist)

---

## Overview

The AF/AD Management & Claim Processing System digitises the end-to-end workflow of:

1. **AF/AD profile registration** — personal info, qualifications, resume, and bank details
2. **Document & certificate verification** — resume and credentials reviewed by the School Executive
3. **Appointment management** — course assignments created by the School Executive / Program Coordinator
4. **Class scheduling** — AFs/ADs record their teaching class sessions
5. **Submissions** — video recording links, attendance sheets, mark-entry forms, question papers & answer sheets
6. **Claim submission** — AFs/ADs submit payment claims with supporting documents and a submission checklist
7. **Two-stage approval** — Program Coordinator endorses, then the School Executive approves / returns / rejects
8. **Notifications** — in-app bell notifications on claim submission, approval, and rejection
9. **Audit trail** — every action on a claim is logged with user, timestamp, IP, and remarks

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.3) |
| Database | SQLite (default, local) |
| Authentication (Web) | Laravel Session Auth |
| Authentication (API) | Laravel Sanctum (Bearer Token) |
| Frontend | Blade Templates + Bootstrap 5 (CDN) |
| Icons | Bootstrap Icons (CDN) |
| Asset Build | Vite 8 + Tailwind CSS 4 (`@tailwindcss/vite`) |
| Notifications | Laravel database notifications |
| Dev Tooling | Laravel Pint, Pail, Collision, PHPUnit 12 |

---

## User Roles

| Role | Key | Description |
|---|---|---|
| AF/AD | `afad` | Academic Facilitator / Academic Developer — registers profile, records classes, uploads submissions, submits claims, views own history |
| School Executive | `executive` | Verifies profiles & documents, creates appointments, reviews submissions, approves/returns/rejects claims |
| Program Coordinator | `pc` | Reviews AF/AD profiles, creates appointments, checks document checklist & question-bank answer sheets, endorses claims, generates reports |
| Administrator | `admin` | Full CRUD management of users, profiles, appointments, classes, certificates, submissions, and claims |

> An AD may concurrently hold an AF role. Both are managed under the same `afad` role type.

---

## Features

### AF/AD (`afad`)
- Register and edit personal profile (name, IC, DOB, gender, contact, qualification, specialisation, resume, bank account)
- Upload a profile photo
- Upload qualification certificates (PDF/image)
- View assigned appointments (course, role, semester, dates) — read-only
- Record, edit, and delete class sessions (course, section, day, time, venue)
- Create claim submissions linked to an appointment, with claim items and a document checklist
- Upload supporting documents per claim checklist
- Submit, edit (if draft/returned), or delete draft claims
- Upload submissions: video recording links, attendance sheets, mark-entry forms, question papers & answer sheets
- View claim status, full activity log, and notifications

> AF/AD access is gated: certificates, appointments, classes, claims, and submissions require a **complete profile**; classes, claims, and submissions additionally require a **verified profile**.

### School Executive (`executive`)
- Dashboard with review queues
- View and filter all AF/AD profiles by status
- View resumes and certificates inline
- Verify profile documents, then verify or reject profiles (with mandatory rejection reason / sections)
- Create and manage course appointments for verified AF/ADs
- Review and download submissions; approve/reject them
- Review submitted claims with supporting documents
- Approve, return for revision, or reject claims with remarks
- Edit own account profile

### Program Coordinator (`pc`)
- Dashboard
- View AF/AD profiles
- Create appointments
- Nomination overview
- Document checklist review — view submissions & claim documents, confirm/reject question-bank answer sheets (QbAs)
- Review claims, endorse or return them to the AF/AD
- Generate and export reports
- Edit own account profile

### Admin (`admin`)
- Dashboard with system-wide statistics
- Full CRUD on **users** (roles), **profiles**, **appointments**, **classes**, **certificates**, **submissions**, and **claims**
- Full audit trail per claim

---

## System Flow

```
AF/AD registers profile
        │
        ▼
Executive verifies documents & profile ──► Rejected (AF/AD edits & resubmits)
        │
        ▼
Executive / PC creates appointment for AF/AD
        │
        ▼
AF/AD records classes & uploads submissions (video, attendance, mark-entry, Q&A)
        │
        ▼
AF/AD creates claim (draft) → uploads required documents & checklist items
        │
        ▼
AF/AD submits claim ──► notification sent
        │
        ▼
Program Coordinator reviews claim
        ├──► Endorsed ──► Executive review
        └──► Returned (AF/AD edits & resubmits)
        │
        ▼
Executive reviews claim
        ├──► Approved ✓ (notification sent)
        ├──► Returned (AF/AD edits & resubmits)
        └──► Rejected ✗ (notification sent)
```

---

## Project Structure

```
AF-AD_Project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── AfAd/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── CertificateController.php
│   │   │   │   ├── AppointmentController.php
│   │   │   │   ├── ClassController.php
│   │   │   │   ├── ClaimController.php
│   │   │   │   ├── ClaimDocumentController.php
│   │   │   │   └── SubmissionController.php
│   │   │   ├── Executive/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProfileVerificationController.php
│   │   │   │   ├── AppointmentController.php
│   │   │   │   ├── ClaimReviewController.php
│   │   │   │   └── SubmissionController.php
│   │   │   ├── ProgramCoordinator/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── AfAdController.php
│   │   │   │   ├── AppointmentController.php
│   │   │   │   ├── NominationController.php
│   │   │   │   ├── DocumentChecklistController.php
│   │   │   │   ├── ClaimReviewController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── AppointmentController.php
│   │   │   │   ├── ClassController.php
│   │   │   │   ├── CertificateController.php
│   │   │   │   ├── SubmissionController.php
│   │   │   │   └── ClaimController.php
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── AppointmentController.php
│   │   │   │   ├── ClaimController.php
│   │   │   │   └── ExecutiveController.php
│   │   │   ├── AccountProfileController.php
│   │   │   ├── NotificationController.php
│   │   │   └── ProfilePhotoController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       ├── EnsureAfAdProfileComplete.php
│   │       └── EnsureAfAdProfileVerified.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Profile.php
│   │   ├── Certificate.php
│   │   ├── Appointment.php
│   │   ├── ClassSession.php
│   │   ├── Claim.php
│   │   ├── ClaimDocument.php
│   │   ├── ClaimAudit.php
│   │   └── Submission.php
│   ├── Notifications/
│   │   ├── ClaimSubmittedNotification.php
│   │   ├── ClaimApprovedNotification.php
│   │   └── ClaimRejectedNotification.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php                 # routing + middleware aliases
│   └── providers.php
├── config/                     # app, auth, database, sanctum, session, etc.
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/             # users, profiles, certificates, appointments,
│   │   │                       # class_sessions, claims, claim_documents,
│   │   │                       # claim_audits, submissions, notifications, + alters
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── ProfileSeeder.php
│       ├── AppointmentSeeder.php
│       └── ClaimSeeder.php
├── public/
│   ├── images/                 # aeu-logo.svg, academic-portal-building-aeu-centered.png
│   ├── index.php
│   ├── favicon.ico
│   └── robots.txt
├── resources/
│   ├── css/app.css
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── account/profile.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── components/
│       │   ├── layouts/
│       │   │   ├── app.blade.php
│       │   │   └── guest.blade.php
│       │   ├── alert.blade.php
│       │   ├── status-badge.blade.php
│       │   ├── confirm-modal.blade.php
│       │   ├── document-row.blade.php
│       │   ├── notification-bell.blade.php
│       │   ├── profile-photo-uploader.blade.php
│       │   ├── submission-documents-notification.blade.php
│       │   └── verification-timeline.blade.php
│       ├── layouts/partials/   # nav-admin, nav-afad, nav-executive, nav-pc
│       ├── notifications/index.blade.php
│       ├── afad/               # dashboard, profile, certificates, appointments,
│       │                       # classes, claims (+ partials), submissions
│       ├── executive/          # dashboard, profiles, appointments, claims, submissions
│       ├── program-coordinator/# dashboard, afad, appointments, nomination,
│       │                       # document-checklist, claims, reports
│       └── admin/              # dashboard, users, profiles, appointments,
│                               # classes, certificates, submissions, claims
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── tests/
│   ├── Feature/ExampleTest.php
│   ├── Unit/ExampleTest.php
│   └── TestCase.php
├── composer.json
├── package.json
└── vite.config.js
```

---

## Database Schema

```
users
├── id, name, email, password
├── role          ENUM(afad, executive, pc, admin)
├── is_active     BOOLEAN
├── email_verified_at
└── profile_photo_path, profile_photo_original_name

profiles
├── id, user_id → users
├── full_name, ic_number, phone, address, contact_email
├── date_of_birth, gender
├── qualification, qualification_level, specialisation, area_of_expertise
├── resume_path, resume_original_name, resume_size
├── bank_name, bank_account_number, bank_account_holder
├── status                 ENUM(pending, verified, rejected)
├── verified_by → users, verified_at
├── documents_verified_by → users, documents_verified_at
└── rejection_reason, rejection_sections (JSON)

certificates
├── id, profile_id → profiles
├── title, issuing_institution, year_obtained
├── file_path, file_original_name, file_mime, file_size
└── is_verified, verified_by → users, verified_at

appointments
├── id, profile_id → profiles
├── course_code, course_name
├── role_type        (af, ad, + expanded role types)
├── semester, academic_session
├── start_date, end_date, venue, student_count
├── delivery_mode
├── appointed_by → users, is_active, notes

class_sessions
├── id, profile_id → profiles
├── course_code, course_name, section
├── day, start_time, end_time, venue
└── semester, academic_session, student_count, notes

claims
├── id, appointment_id → appointments, profile_id → profiles
├── claim_reference        (auto-generated: CLAIM-YYYY-NNNNN)
├── claim_type             ENUM(teaching, marking, module_development, consultation)
├── claim_items (JSON), claim_form_data (JSON)
├── period_from, period_to
├── total_hours, rate_per_hour, total_amount
├── has_mark_entry_forms, has_graded_scripts, has_qa, has_question_bank_answer_sheet (BOOLEAN)
├── status                 ENUM(draft, submitted, under_review, approved, returned, rejected)
├── submitted_at
├── pc_endorsed_by → users, pc_endorsed_at, pc_remarks
├── reviewed_by → users, reviewed_at, executive_remarks
└── payment_reference, paid_at

claim_documents
├── id, claim_id → claims
├── document_type, label, sort_order
├── is_required, is_uploaded, uploaded_at, notes
└── file_path, file_original_name, file_mime, file_size

claim_audits  (append-only, no updated_at)
├── id, claim_id → claims
├── performed_by → users
├── action, from_status, to_status, remarks
├── metadata (JSON), ip_address, user_agent
└── created_at    (immutable)

submissions
├── id, profile_id → profiles, claim_id → claims (nullable)
├── submission_type   (video_recording, attendance_sheet, mark_entry_forms, qa, question_bank_answer_sheet)
├── submission_date, title, description
├── file_path, file_original_name, file_mime, file_size, video_url
├── video_duration_minutes, tutorial_number
├── claim_hours, rate_per_hour, total_amount
├── semester_intake, course, course_name, programme
├── status, reviewed_by → users, reviewed_at, executive_remarks
└── pc_qbas_status, pc_qbas_set_count, pc_qbas_checked_by → users, pc_qbas_checked_at, pc_qbas_remarks

notifications  (Laravel database notifications)
├── id (UUID), type, notifiable_type, notifiable_id
├── data (JSON)
└── read_at
```

---

## Middleware & Access Control

Registered in [bootstrap/app.php](bootstrap/app.php):

| Alias | Class | Purpose |
|---|---|---|
| `role:<role>` | `CheckRole` | Restricts a route group to one or more roles |
| `afad.profile.complete` | `EnsureAfAdProfileComplete` | Blocks AF/AD feature access until profile is fully filled in |
| `afad.profile.verified` | `EnsureAfAdProfileVerified` | Blocks classes/claims/submissions until profile is verified |

---

## Installation & Setup

### Requirements
- PHP 8.3+
- Composer
- Node.js + npm (for asset build)

### Steps

```bash
# 1. Clone the repository
git clone <repo-url>
cd AF-AD_Project

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and generate key
cp .env.example .env
php artisan key:generate

# 4. Run migrations and seed demo data
php artisan migrate:fresh --seed

# 5. Build front-end assets (optional for dev)
npm install
npm run build

# 6. Start the development server
php artisan serve
```

Or run everything (server, queue, logs, Vite) at once:

```bash
composer dev
```

The application will be available at `http://127.0.0.1:8000`.

---

## Demo Accounts

All seeded demo accounts use the password: **`password`**

| Name | Email | Role |
|---|---|---|
| System Administrator | `admin@system.my` | Admin |
| Dr. Sarah Ahmad | `executive@system.my` | School Executive |
| Ahmad bin Ali | `afad1@system.my` | AF/AD (verified, has certificates) |
| Siti binti Hassan | `afad2@system.my` | AF/AD (pending verification) |

> No Program Coordinator (`pc`) account is seeded by default. Create one via the **registration page** (`/register`, role selectable) or through the Admin user management screen.

---

## Web Routes

### Public / Auth
| Method | URI | Description |
|---|---|---|
| GET | `/login` | Login page |
| POST | `/login` | Authenticate user |
| GET | `/register` | Registration page (role selectable) |
| POST | `/register` | Create account |
| POST | `/logout` | Logout |
| GET | `/profile-photo/{user}` | Serve a user's profile photo (auth) |

### Notifications (all authenticated roles)
| Method | URI | Description |
|---|---|---|
| GET | `/notifications` | List notifications |
| GET | `/notifications/{id}/read` | Mark one as read |
| POST | `/notifications/read-all` | Mark all as read |

### AF/AD (`/afad/*`) — requires `afad` role
| Method | URI | Description |
|---|---|---|
| GET | `/afad/dashboard` | Dashboard |
| GET | `/afad/profile` | View own profile |
| GET/POST | `/afad/profile/create`, `/afad/profile` | Register profile |
| GET/PUT | `/afad/profile/edit`, `/afad/profile` | Edit / update profile |
| GET/POST | `/afad/certificates`, `/afad/certificates/create` | List / upload certificates |
| DELETE | `/afad/certificates/{certificate}` | Remove certificate |
| GET | `/afad/appointments`, `/afad/appointments/{appointment}` | List / view appointments |
| GET/POST | `/afad/classes`, `/afad/classes/create` | List / create class sessions |
| GET/PUT/DELETE | `/afad/classes/{class}` | View / edit / update / delete class |
| GET/POST | `/afad/claims`, `/afad/claims/create` | List / create draft claim |
| GET/PUT | `/afad/claims/{claim}`, `/afad/claims/{claim}/edit` | View / edit draft or returned claim |
| POST | `/afad/claims/{claim}/submit` | Submit claim for review |
| DELETE | `/afad/claims/{claim}` | Delete draft claim |
| POST | `/afad/claims/{claim}/documents/{document}/upload` | Upload claim document |
| DELETE | `/afad/claims/{claim}/documents/{document}` | Remove claim document |
| GET/POST | `/afad/submissions`, `/afad/submissions/create` | List / create submission |
| GET | `/afad/submissions/{submission}` | View submission |
| POST | `/afad/submissions/{submission}/duration` | Update video duration |
| GET | `/afad/submissions/{submission}/download` | Download submission file |
| DELETE | `/afad/submissions/{submission}` | Delete submission |

### School Executive (`/executive/*`) — requires `executive` role
| Method | URI | Description |
|---|---|---|
| GET | `/executive/dashboard` | Dashboard |
| GET/PUT | `/executive/profile` | View / update own account |
| GET | `/executive/profiles`, `/executive/profiles/{profile}` | List / view AF/AD profiles |
| GET | `/executive/profiles/{profile}/resume/view` | View resume |
| GET | `/executive/profiles/{profile}/certificates/{certificate}/view` | View certificate |
| POST | `/executive/profiles/{profile}/documents/verify` | Verify documents |
| POST | `/executive/profiles/{profile}/verify` | Verify profile |
| POST | `/executive/profiles/{profile}/reject` | Reject profile (reason required) |
| GET/POST | `/executive/appointments`, `/executive/appointments/create` | List / create appointment |
| GET/PUT | `/executive/appointments/{appointment}`, `/edit` | View / edit / update appointment |
| GET | `/executive/claims`, `/executive/claims/{claim}` | List / view claims |
| POST | `/executive/claims/{claim}/approve` | Approve claim |
| POST | `/executive/claims/{claim}/return` | Return for revision (reason required) |
| POST | `/executive/claims/{claim}/reject` | Reject claim (reason required) |
| GET | `/executive/submissions`, `/executive/submissions/{submission}` | List / view submissions |
| GET | `/executive/submissions/{submission}/download` | Download submission |
| POST | `/executive/submissions/{submission}/review` | Review (approve/reject) submission |

### Program Coordinator (`/pc/*`) — requires `pc` role
| Method | URI | Description |
|---|---|---|
| GET | `/pc/dashboard` | Dashboard |
| GET/PUT | `/pc/profile` | View / update own account |
| GET | `/pc/afad`, `/pc/afad/{profile}` | List / view AF/AD profiles |
| GET/POST | `/pc/appointments`, `/pc/appointments/create` | List / create appointment |
| GET | `/pc/nomination` | Nomination overview |
| GET | `/pc/document-checklist` | Document checklist overview |
| GET | `/pc/document-checklist/submissions/{submission}/view` | View submission |
| GET | `/pc/document-checklist/claim-documents/{document}/view` | View claim document |
| POST | `/pc/document-checklist/qbas/{submission}/confirm` | Confirm question-bank answer sheet |
| POST | `/pc/document-checklist/qbas/{submission}/reject` | Reject question-bank answer sheet |
| GET | `/pc/claims`, `/pc/claims/{claim}` | List / view claims |
| POST | `/pc/claims/{claim}/endorse` | Endorse claim |
| POST | `/pc/claims/{claim}/return` | Return claim to AF/AD |
| GET | `/pc/reports`, `/pc/reports/export` | View / export reports |

### Admin (`/admin/*`) — requires `admin` role
Full resourceful CRUD (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`) for:

| Resource | Base URI |
|---|---|
| Dashboard | `/admin/dashboard` |
| Users / roles | `/admin/users` |
| Profiles | `/admin/profiles` |
| Appointments | `/admin/appointments` |
| Classes | `/admin/classes` |
| Certificates | `/admin/certificates` |
| Submissions | `/admin/submissions` (+ `/download`) |
| Claims | `/admin/claims` |

---

## REST API

The API uses **Laravel Sanctum** (Bearer token). All endpoints are prefixed with `/api`. No CSRF token is required.

> The REST API currently exposes the **AF/AD** and **Executive** workflows. Program Coordinator and Admin actions are web-only.

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

#### List appointments
```http
GET /api/afad/appointments
GET /api/afad/appointments/{id}
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
Supported `claim_type` values: `teaching` `marking` `module_development` `consultation`

#### View claim detail
```http
GET /api/afad/claims/{id}
```
Returns claim details including `documents` and `audit_trail` arrays.

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

#### Reject a profile
```http
POST /api/executive/profiles/{id}/reject
Content-Type: application/json

{ "rejection_reason": "Missing academic certificate. Please upload your degree certificate." }
```

#### List claims for review
```http
GET /api/executive/claims
GET /api/executive/claims?status=submitted
```

#### Approve / Return / Reject a claim
```http
POST /api/executive/claims/{id}/approve
POST /api/executive/claims/{id}/return
POST /api/executive/claims/{id}/reject
Content-Type: application/json

{ "remarks": "All documents verified and in order." }
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
draft ──► submitted ──► (PC endorses) ──► under_review ──► approved ✓
               │                                │
               ├──◄ returned (by PC or Executive) ──► (AF/AD edits) ──► submitted
               │
               └──► rejected ✗
```

| Status | Set By | Description |
|---|---|---|
| `draft` | AF/AD | Created but not yet submitted |
| `submitted` | AF/AD | Submitted for review |
| `under_review` | PC / Executive | Endorsed by PC and being reviewed |
| `approved` | Executive | Approved for payment |
| `returned` | PC / Executive | Returned to AF/AD for corrections |
| `rejected` | Executive | Permanently rejected |

Each transition writes an immutable row to `claim_audits` (actor, from/to status, remarks, IP, user agent). Submitted/approved/rejected events also dispatch in-app notifications.

---

## Submissions & Document Checklist

AF/ADs upload **submissions** that feed into the claim checklist. Supported submission types:

| Type | Key | Form |
|---|---|---|
| Video Recording | `video_recording` | URL link (+ duration, tutorial number) |
| Attendance Sheet | `attendance_sheet` | PDF/image upload |
| Mark-entry Forms | `mark_entry_forms` | PDF/image upload |
| Question Paper & Answer Sheet | `qa` | PDF/image upload |
| Question Bank Answer Sheet | `question_bank_answer_sheet` | PDF/image upload (PC confirms/rejects — "QbAs") |

Claim documents are auto-created per claim type when a claim is first saved:

| Claim Type | Required Documents | Optional Documents |
|---|---|---|
| `teaching` | Attendance Sheet, Lesson Plan | Student List |
| `marking` | Marking Scheme, Assignment Sample | Attendance Sheet |
| `module_development` | Module Draft / Outline, Approval Letter | — |
| `consultation` | Consultation Record | Supporting Document |

Accepted formats: **PDF, JPG, JPEG, PNG** — max **10 MB** per file.
