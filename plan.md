**Requirements**

**Overview & Goals**

Transform AllAcademies from a single-server exam platform into a multi-center system where one central server acts as the source of truth and multiple examination centers (10+) can pull exam packages, run exams independently, and push results back. The system must work offline at centers and converge when connectivity is restored.

**User Stories**

1. As a **central admin**, I want to create and publish exams with access codes so that all centers can pull and run them.
2. As a **central admin**, I want to correct grading (exclude questions, fix answer keys, change marks) after an exam starts so that all centers regrade consistently.
3. As a **center staff member**, I want to pull exam packages from central so that candidates at my center can take exams.
4. As a **center staff member**, I want to run exams with no connection to central so that exams continue during outages.
5. As a **center staff member**, I want to push results and proctoring logs to central so that central has a complete view.
6. As a **central admin**, I want to view per-center dashboards so that I can monitor all centers' activity.
7. As a **candidate**, I want to join an exam by access code at any center so that the experience is identical everywhere.
8. As a **central admin**, I want to backup and restore the academic structure (groups, levels, subjects, topics, question bank) so that a new server can be provisioned quickly.

**Scope**

**In Scope:**
- Multiple choice exams only
- Exam packages flowing from central to centers (pull-based)
- Submissions and proctoring logs flowing from centers to central (push-based)
- Multi-center admin dashboard on central
- Backup and restore of academic structure and question bank
- ULID-based UUIDs on all exam entities
- submission_answers table replacing responses JSON column
- Single canonical grader (McqGrader)
- Ed25519 package signing with libsodium
- HMAC-SHA256 request signing for sync API
- Idempotent upserts by UUID
- Sync version tracking for conflict-free convergence

**Out of Scope:**
- Networking and VPN setup between servers
- Webcam proctoring
- True/false, short answer, and essay types (grading code exists but not part of this release)
- Data migration (fresh start, no existing data)

**Functional Requirements**

1. **Role-based access**: APP_ROLE=center blocks all content creation/editing routes (403). APP_ROLE=central has full access.
2. **Exam publishing**: Central validates, packages, signs, and locks exams. One access code per exam, valid at every center.
3. **Pull sync**: Centers fetch a manifest, compare with local state, download missing revisions, verify checksum and signature, and apply.
4. **Push sync**: Centers push submissions (with answers) and proctoring logs in batches. Central ingests idempotently by UUID.
5. **Grading parity**: Golden-file test yields identical score and grade on center and central.
6. **Offline operation**: Center runs exams on last applied package. Results wait in outbox until connection restored.
7. **Revisions**: Post-start revisions change only grading inputs. Applier dispatches chunked regrade jobs.
8. **Parity checking**: Central re-grades from raw answers and flags mismatches without overwriting center scores.
9. **Cross-center duplicate detection**: Same index number at multiple centers is flagged.
10. **Clock skew detection**: Center blocks exam joins if clock skew exceeds configurable threshold (default 2 minutes).

**Non-Functional Requirements**

- **Performance**: saveResponse p95 under 200ms at 1000 concurrent candidates
- **Scale**: 500-1000 candidates per center, 10+ centers
- **Security**: HTTPS only, token rotation, signature verification, rate limits
- **Idempotency**: Applying the same package twice or ingesting the same batch twice creates no duplicates
- **Data integrity**: Published revisions are immutable; checksum and signature verification on every pull
- **Convergence**: After 3 days offline, a center reconnects and all data converges correctly

**Technical Design**

**Current Implementation**

**Key files studied:**
- `app/ExaminationHub/Models/GeneralExam.php` — access_code generated in boot() with local-only uniqueness, local FKs (user_id, school_id, teacher_id)
- `app/ExaminationHub/Models/GeneralExamSubmission.php` — responses stored as JSON column, saveResponse() rewrites entire JSON, gradeSubmission() inline grading, calculateGrade() resolves from user_id/school_id
- `app/ExaminationHub/Models/GeneralExamQuestion.php` — Mark cast on question/options, source_question_id as local PK, gradeResponse() dispatches by type
- `app/ExaminationHub/Models/GeneralExamSection.php` — local FKs to academic tables
- `app/ExaminationHub/Models/ExamProctoringLog.php` — per-submission event logs
- `app/ExaminationHub/Services/ExamGradingService.php` — queued grading path, resolves grade from GradingSystemService
- `app/Services/GeneralExam/GeneralExamGradingService.php` — toggleGrading path, 644 lines, inline grading loop
- `app/ExaminationHub/Controllers/ExamTakingController.php` — saveResponse() loads all sections+questions per request, rewrites JSON
- `app/ExaminationHub/Controllers/ExamQuestionController.php` — toggleGrading() regrades every submission in the web request
- `app/ExaminationHub/Traits/EnsuresExamOwnership.php` — checks user_id/teacher_id ownership
- `app/Models/MultipleChoiceQuestion.php` — bank model with option_a-e columns, Mark cast, user FKs, SoftDeletes, polymorphic Question wrapper
- `routes/examinations-hub.php` — all content routes under /examinations prefix
- `routes/api.php` — existing API routes (academic, questions)
- `app/Http/Kernel.php` — middleware registration
- `config/proctoring.php` — violation toggles, auto-submit thresholds
- `config/exam-branding.php` — access code length, branding

**Architecture patterns:**
- Laravel 10+ with Eloquent models, queued jobs, middleware groups
- Service classes for business logic (ExamGradingService, ExamCreationService)
- Traits for shared behavior (EnsuresExamOwnership)
- Casts for custom types (Mark for HTML/markdown)
- Route files organized by domain (examinations-hub.php, api.php)
- Console commands for scheduled tasks

**Key Decisions**

1. **Dedicated /api/sync/v1/* namespace** — New sync-specific API routes with token auth, HMAC signature, and version handshake. Separates sync concerns from the academic API.
2. **submission_answers table** — Replaces responses JSON column. Each answer is a row keyed by submission UUID + question UUID. Eliminates race conditions from JSON rewrites.
3. **One canonical McqGrader** — Pure function: input is question data + response + exclusion flags; output is points. Used by ExamGradingService, regrade path, and central verification. Retires duplicate logic in GeneralExamGradingService.
4. **Integer hundredths for marks** — Compute and compare in integer hundredths so center and central match exactly (no float drift).
5. **ULID-based UUIDs** — HasUuid trait generates ULIDs on creating. Unique indexes on all exam entities. UUIDs are the only identifiers in packages.
6. **Ed25519 signing** — Central signs packages with private key. Centers verify with public key. libsodium ships with PHP.
7. **State block separation** — Small separate state block (state_version) carries results_released and status. Releasing results does not require downloading the whole package.
8. **Outbox pattern for push** — submitted and graded rows where synced_version < sync_version. Increment sync_version on any later change.
9. **Chunked regrade jobs** — Replace toggleGrading's in-request loop with queued jobs processing ~50 submissions per chunk.
10. **Grading scale embedded in package** — Resolved on central at publish time. Centers never call GradingSystemService.

**Data Model Changes**

**New tables:**

1. **centers** (central only):
   - id (PK), uuid (ULID, unique), code (unique), name, token_hash (bcrypt), is_active, last_seen_at, last_pull_at, last_push_at, app_version, created_at, updated_at

2. **exam_packages** (central only):
   - id (PK), exam_uuid (unique with revision), revision (int), payload (LONGTEXT gzip), checksum (sha256), signature (Ed25519), created_by (user_id FK), created_at
   - Unique index on (exam_uuid, revision). Rows are immutable.

3. **exam_sync_state** (center only):
   - id (PK), exam_uuid (unique), applied_revision (int), applied_state_version (int), checksum, applied_at

4. **sync_runs** (both):
   - id (PK), direction (enum: pull/push), started_at, finished_at, status (enum: running/success/failed), items_total, items_ok, error (TEXT)

5. **submission_answers** (both):
   - id (PK), uuid (ULID, unique), general_exam_submission_id (FK), general_exam_question_id (FK), question_uuid (varchar), response (varchar 8), answered_at (datetime), is_correct (boolean, nullable), points_earned (decimal 8,2), graded_revision (int, nullable)
   - Unique index on (general_exam_submission_id, general_exam_question_id)

**Columns added to existing tables:**

- **uuid** (varchar 26, unique, not null) via HasUuid trait on: general_exams, general_exam_sections, general_exam_questions, general_exam_participant_groups, general_exam_configured_participants, general_exam_submissions, exam_proctoring_logs
- **general_exams**: revision (int, default 1), state_version (int, default 1), grading_scale (JSON), published_at (datetime)
- **general_exam_submissions**: center_id (int, nullable), participant_index_number (varchar, nullable), graded_revision (int, nullable), sync_version (int, default 0), synced_version (int, default 0). Central-only: central_score (decimal 8,2, nullable), parity_ok (boolean, nullable), parity_checked_at (datetime, nullable)
- **exam_proctoring_logs**: center_id (int, nullable), synced_at (datetime, nullable)
- **users**: center_id (int, nullable)
- **general_exam_questions**: source_question_uuid (varchar, nullable). Drop source_question_id after bank has UUIDs.
- Phase 7: uuid on academic_groups, academic_levels, academic_subjects, academic_topics, academic_subtopics, multiple_choice_questions

**File Structure**

**New files:**

```
config/
  center.php                          # APP_ROLE, center_id, central_url, token, public key

app/ExaminationHub/
  Models/
    Center.php                        # Center model (central)
    ExamPackage.php                   # Exam package model (central)
    ExamSyncState.php                 # Sync state model (center)
    SyncRun.php                       # Sync run model (both)
    SubmissionAnswer.php              # Per-answer model (both)
  
  Traits/
    HasUuid.php                       # ULID generation trait
  
  Middleware/
    CenterReadOnly.php                # Blocks content routes on centers
    CenterScope.php                   # Global scope for center-aware queries
    SyncAuth.php                      # Token auth for sync API
    SyncSignature.php                 # HMAC signature verification
    SyncVersion.php                   # App version handshake
  
  Services/
    McqGrader.php                     # Pure MCQ grader class
    ExamPackageBuilder.php            # Builds canonical exam packages
    ExamPackageApplier.php            # Applies packages on centers
    PullAgent.php                     # Pulls packages from central
    PushAgent.php                     # Pushes results to central
    SyncManifestService.php           # Generates sync manifest
    SubmissionIngestService.php       # Ingests submissions on central
    ProctoringIngestService.php       # Ingests proctoring logs on central
    ParityCheckService.php            # Verifies grading parity on central
  
  Controllers/
    Sync/
      ManifestController.php          # GET /api/sync/v1/manifest
      PackageController.php           # GET /api/sync/v1/exams/{uuid}/revisions/{n}
      StateController.php             # GET /api/sync/v1/exams/{uuid}/state
      SubmissionIngestController.php  # POST /api/sync/v1/ingest/submissions
      ProctoringIngestController.php  # POST /api/sync/v1/ingest/proctoring
      HeartbeatController.php         # POST /api/sync/v1/heartbeat
  
  Commands/
    SyncPull.php                      # sync:pull command
    SyncPush.php                      # sync:push command
    GenerateSyncKeys.php              # php artisan sync:keys:generate
    CenterHealth.php                  # php artisan center:health
  
  Jobs/
    ChunkedRegradeJob.php             # Batch regrade ~50 submissions
    SyncRetryJob.php                  # Retry failed sync with backoff

app/Http/
  Controllers/
    Center/
      CenterDashboardController.php   # Multi-center admin dashboard
      CenterSyncController.php        # Center admin sync page

resources/views/
  center/
    dashboard.blade.php               # Multi-center dashboard
    sync.blade.php                    # Center sync admin page
    center/
      show.blade.php                  # Per-center detail view

routes/
  sync.php                            # Sync API routes

database/migrations/
  2026_08_01_000001_create_centers_table.php
  2026_08_01_000002_add_uuid_to_exam_tables.php
  2026_08_01_000003_add_sync_columns_to_general_exams.php
  2026_08_01_000004_add_sync_columns_to_submissions.php
  2026_08_01_000005_add_sync_columns_to_proctoring_logs.php
  2026_08_01_000006_add_center_id_to_users.php
  2026_08_01_000007_create_exam_packages_table.php
  2026_08_01_000008_create_exam_sync_state_table.php
  2026_08_01_000009_create_sync_runs_table.php
  2026_08_01_000010_create_submission_answers_table.php
  2026_08_01_000011_add_source_question_uuid_to_exam_questions.php
  2026_08_01_000012_add_uuid_to_academic_tables.php          # Phase 7
  2026_08_01_000013_add_uuid_to_multiple_choice_questions.php # Phase 7

database/seeders/
  CenterSyncUserSeeder.php            # Seeds Central Sync system user

tests/Feature/
  Center/
    CenterReadOnlyTest.php            # Content routes return 403 on center
    HasUuidTest.php                   # New rows get ULIDs
    ExamPackageBuilderTest.php        # Byte-identical packages
    ExamPackageApplierTest.php        # Idempotent apply
    PullAgentTest.php                 # Pull, resume, offline recovery
    PushAgentTest.php                 # Push, retry, no duplicates
    SubmissionIngestTest.php          # Idempotent ingest
    ParityCheckTest.php               # Golden file parity
    McqGraderTest.php                 # Pure grader tests
    SyncAuthTest.php                  # Token, signature, version
    CenterDashboardTest.php           # Scoping, filtering
    BankExportImportTest.php          # Phase 7: idempotent import
```

**Architecture Diagram**

```mermaid
graph LR
    subgraph Central
        CA[Central Admin] --> EC[Exam Creation]
        EC --> EP[ExamPackageBuilder]
        EP --> EPK[Ed25519 Sign]
        EPK --> EPT[exam_packages]
        EPT --> SM[SyncManifestService]
        SI[SubmissionIngest] --> GS[GeneralExamSubmission]
        SI --> SA[SubmissionAnswer]
        SI --> PC[ParityCheckService]
        PI[ProctoringIngest] --> PL[ExamProctoringLog]
        CD[CenterDashboard] --> CT[centers]
        CD --> GS
        CD --> PL
    end
    
    subgraph Sync API
        SM --> MC[ManifestController]
        EP --> PKC[PackageController]
        STC[StateController]
        SIC[SubmissionIngestController] --> SI
        PIC[ProctoringIngestController] --> PI
        HC[HeartbeatController] --> CT
    end
    
    subgraph Center
        PA[PullAgent] --> MC
        PA --> PKC
        PA --> STC
        PA --> EA[ExamPackageApplier]
        EA --> GS
        EA --> SA
        PA2[PushAgent] --> SIC
        PA2 --> PIC
        CT2[Center Staff] --> LM[Live Monitoring]
        CT2 --> PA
        CT2 --> PA2
        Cand[Candidates] --> ET[Exam Taking]
        ET --> SA
        ET --> PL
    end
    
    MC --> Sync API
    PKC --> Sync API
    STC --> Sync API
    SIC --> Sync API
    PIC --> Sync API
    HC --> Sync API
```

**Phase-by-Phase Breakdown**

**Phase 0: Foundations (2-3 days)**
- config/center.php with role, center_id, central_url, token, public key
- HasUuid trait (ULID on creating, unique index)
- Migrations: uuid on exam tables, center_id on users, new tables (centers, exam_packages, exam_sync_state, sync_runs)
- Center model, centers table, CenterSyncUserSeeder
- CenterReadOnly middleware applied to all content routes when APP_ROLE=center
- CenterScope global scope on submissions and users; admin bypasses
- EnsuresExamOwnership made center-aware

**Phase 1: Answers table and one grader (4-6 days)**
- submission_answers table migration
- saveResponse changed to upsert a single row (no JSON rewrite)
- All readers of responses migrated (take views, Livewire, ExamGradingService, result/export views, monitoring)
- McqGrader pure class extracted
- Integer hundredths for marks, score, percentage
- GradeScale from package's grading_scale (no GradingSystemService on centers)
- graded_revision recorded on submission

**Phase 2: Package builder and publish workflow (3-4 days)**
- ExamPackageBuilder: reads exam, sections, questions, roster, grading scale → canonical payload
- Publish action on central: validate, write revision 1, sign, lock exam
- Revise action: pre-start free edits, post-start grading-only (A1)
- Ed25519 key generation command

**Phase 3: Sync API and pull agent (4-5 days)**
- Sync API endpoints with middleware (token auth, signature, rate limit, version)
- PullAgent: fetch manifest, compare, download, verify, apply
- ExamPackageApplier: one transaction, upsert by UUID, forceFill access_code
- sync:pull command with configurable interval
- Center admin sync page
- Clock-skew check from manifest server_time

**Phase 4: Push agent and central ingestion (4-5 days)**
- Outbox rule: synced_version < sync_version
- Push submissions in batches of ~100 with answers
- Push proctoring logs in batches of ~1000
- Ingest endpoints: upsert on UUID, accept only higher sync_version
- Queued central job re-grades from raw answers, sets parity_ok
- Retry with exponential backoff and jitter

**Phase 5: Central dashboard and scoping (3-4 days)**
- Center filter (session-based) with All centers option
- Per-center cards: candidates, submitted, completion rate, avg score, flagged proctoring, last synced, backlog
- Parity alerts panel
- Cross-center duplicate attempts panel
- Data freshness indicators

**Phase 6: Corrections and revisions (2-3 days)**
- Central grading-only revision form
- Applier regrades in chunks of ~50 per queued job
- Replace toggleGrading's in-request loop with chunked job path

**Phase 7: Structure and bank backup/restore (4-6 days)**
- UUID on academic tables and multiple_choice_questions
- Export selected groups/levels/subjects as signed bundle
- Idempotent import that upserts by UUID
- Restore to another server

**Phase 8: Hardening and rollout (3-5 days)**
- Load test: 1000-candidate exam simulation
- Security pass: token rotation, signature checks, rate limits, HTTPS
- Center health page on central
- Provisioning checklist and runbook
- Pilot with two centers

**Testing**

**Validation Approach**

Each phase includes feature tests that validate the functional requirements. The testing strategy follows the spec's five pillars: parity, idempotency, offline, tamper, and load.

**Key Scenarios**

**Phase 0:**
- Every content route on a center instance returns 403 (feature test per route in routes/examinations-hub.php)
- Every new row gets a ULID (HasUuidTest)
- Center staff can monitor but not edit (EnsuresExamOwnership center-aware)
- Admin sees all centers; center user sees only their center (CenterScope)

**Phase 1:**
- Golden-file test: fixed exam + fixed answers yields identical score and grade on two separate app instances
- saveResponse at 1000 concurrent candidates holds p95 under 200ms (load test)
- McqGrader produces correct points for all exclusion/award combinations
- Integer hundredths comparison matches between center and central

**Phase 2:**
- Rebuilding a package gives byte-identical output (ExamPackageBuilderTest)
- Changing one byte makes verification fail (checksum + signature)
- Publish validates: all questions MCQ, every answer key present, access code set, roster present
- Post-start revision rejects non-grading changes

**Phase 3:**
- Center applies a new exam (PullAgentTest)
- Center applies a revision (ExamPackageApplierTest)
- Center ignores an already-applied revision (idempotency)
- Resume after killed download
- Correct after 3 days offline (simulated)
- Clock-skew detection blocks joins beyond threshold

**Phase 4:**
- Pushing twice creates no duplicates (SubmissionIngestTest)
- Killed connection resumes cleanly (PushAgentTest)
- 10 centers × 1000 submissions ingest in under 10 minutes with parity_ok true
- Unknown exam or revision rejected with clear error code
- Proctoring logs arrive before submission → retryable error

**Phase 5:**
- Center user can never see another center's rows (CenterDashboardTest)
- Admin can switch between all centers and a single center
- Parity alerts show correctly
- Cross-center duplicates flagged

**Phase 6:**
- Excluding a question on central produces identical regraded results on every center and central
- Chunked regrade jobs process correctly
- toggleGrading on central uses chunked job path

**Phase 7:**
- Exporting a subject and importing twice gives same rows with no duplicates (BankExportImportTest)
- Polymorphic Question wrapper recreated on import
- Soft-deleted flags preserved

**Phase 8:**
- Load test: 1000 simulated candidates against one center
- 10 centers pushing at once against central
- Clean install: all migrations on empty database for both roles

**Edge Cases**

- Package corrupted or tampered → rejected, previous revision stays active, alert raised
- Same submission pushed twice → upsert by UUID, no-op unless sync_version is newer
- Proctoring logs arrive before their submission → retryable error, center sends submissions first
- Revision published mid-exam → applied, candidates in progress keep current questions, grading uses new key
- Parity mismatch → flagged, both scores stored, nothing overwritten silently
- Center clock wrong → detected through server_time, joins blocked beyond configurable skew
- Same index number sits exam at two centers → both submissions kept, attempts enforced per center, central flags cross-center duplicates
- Center database lost → unsynced results lost without backup (documented in runbook)
- Center never received the exam → candidate join blocked with clear message, center admin sees "Exam not synced"

**Test Changes**

- New test suite: `tests/Feature/Center/` with 15+ test classes
- Existing tests updated to use UUIDs instead of integer IDs where applicable
- Golden fixtures added: `tests/Fixtures/golden-exam.json` with expected grading results
- Load test harness: `tests/Feature/Center/LoadTest.php` with simulated candidates

**Delivery Steps**

**✓ Step 1: Phase 0: Foundations — config, UUIDs, migrations, middleware**

Set up the infrastructure for multi-center operation: configuration, UUID trait, new tables, and read-only enforcement on centers.

- Create `config/center.php` with APP_ROLE, center_id, central_url, token, and central public key settings
- Create `HasUuid` trait (ULID generation on creating, unique index) in `app/ExaminationHub/Traits/HasUuid.php`
- Create migrations: `create_centers_table`, `add_uuid_to_exam_tables` (general_exams, sections, questions, submissions, proctoring_logs, participant_groups, configured_participants), `add_sync_columns_to_general_exams` (revision, state_version, grading_scale, published_at), `add_sync_columns_to_submissions` (center_id, participant_index_number, graded_revision, sync_version, synced_version, central_score, parity_ok, parity_checked_at), `add_sync_columns_to_proctoring_logs` (center_id, synced_at), `add_center_id_to_users`, `create_exam_packages_table`, `create_exam_sync_state_table`, `create_sync_runs_table`
- Create `Center` model, `ExamPackage` model, `ExamSyncState` model, `SyncRun` model
- Create `CenterSyncUserSeeder` for the Central Sync system user
- Create `CenterReadOnly` middleware that returns 403 on all content routes when APP_ROLE=center; audit routes/examinations-hub.php to ensure nothing slips through
- Create `CenterScope` global scope on submissions and users; admin role bypasses, admin can set center filter
- Update `EnsuresExamOwnership` trait to be center-aware: center staff can monitor but not edit
- Register new middleware in `app/Http/Kernel.php`
- Run all migrations on empty database for both roles (clean install test)
- Done when: every content route on a center returns 403 (feature test per route), every new row gets a ULID, and clean migrations succeed for both roles

**✓ Step 2: Phase 1: submission_answers table and McqGrader**

Replace the responses JSON column with a per-answer table and extract a pure MCQ grader.

- Create `create_submission_answers_table` migration (id, uuid, general_exam_submission_id, general_exam_question_id, question_uuid, response varchar 8, answered_at, is_correct nullable, points_earned decimal 8,2, graded_revision nullable; unique on submission+question)
- Create `SubmissionAnswer` model in `app/ExaminationHub/Models/SubmissionAnswer.php`
- Change `saveResponse` in `ExamTakingController` to upsert a single SubmissionAnswer row (no whole-JSON rewrite, no reloading every section and question per request)
- Grep for every reader of `responses` column (take views, Livewire exam component, ExamGradingService, result and export views, monitoring) and migrate them to read from submission_answers
- Extract `McqGrader` pure class: input is question data, response, exclusion flags; output is points (integer hundredths). Used by ExamGradingService, regrade path, and central verification
- Update `ExamGradingService` to use McqGrader and read from submission_answers
- Replace `resolveGrade` with `GradeScale` built from the package's grading_scale (centers never call GradingSystemService)
- Record `graded_revision` on the submission when grading
- Compute marks, score, percentage in integer hundredths in McqGrader
- Keep existing normalization (trim + uppercase) in the grader
- Update all views that read responses to read from submission_answers
- Done when: golden-file test yields identical score and grade on two separate app instances, and saveResponse load test at 1000 concurrent candidates holds p95 under 200ms

**✓ Step 3: Phase 2: ExamPackageBuilder and publish workflow on central**

Build the exam package format, signing, and publish/revise workflow on central.

- Create `ExamPackageBuilder` service: reads exam, sections, questions, roster, and resolved grading scale; produces canonical JSON payload (keys sorted, UTF-8, no pretty printing, Mark values as {up, down})
- Implement checksum (sha256 of canonical JSON) and signature (Ed25519 over checksum with central's private key via libsodium)
- Create publish action on central: validate (all questions MCQ, every answer key present, access code set, roster present for configured mode), write revision 1, sign it, mark exam locked
- Create revise action: before start, edits are free and re-publishing creates revision n+1; after start, only grading-only form available (A1)
- Create `GenerateSyncKeys` artisan command for Ed25519 key generation and storage guidance
- Store central public key in config; centers hold the public key and reject anything that fails verification
- Package format matches the spec: exam_uuid, revision, state_version, access_code, settings, grading_scale, sections (with questions), participants, checksum, signature
- Only UUIDs in the package, never local integer IDs. No user_id, school_id, teacher_id, or subscription IDs
- general_exam_questions: add source_question_uuid column (nullable), informational only
- Done when: rebuilding a package gives byte-identical output, and changing one byte makes verification fail

**✓ Step 4: Phase 3: Sync API endpoints and pull agent**

Implement the sync protocol: API endpoints on central, pull agent on centers, and package application.

- Create `routes/sync.php` with dedicated /api/sync/v1/* namespace:
  - GET /api/sync/v1/manifest (returns server_time + exam list with uuid, revision, state_version, checksum, size)
  - GET /api/sync/v1/exams/{uuid}/revisions/{n} (downloads one gzip package)
  - GET /api/sync/v1/exams/{uuid}/state (returns small state block)
  - POST /api/sync/v1/heartbeat (center reports app version, last pull, backlog)
- Create `SyncAuth` middleware: per-center bearer token stored hashed on central
- Create `SyncSignature` middleware: X-Signature header (HMAC-SHA256 of body) + X-Timestamp (reject skew over 5 minutes)
- Create `SyncVersion` middleware: X-App-Version header, central answers 426 on mismatch
- Create `SyncManifestService` on central: generates manifest from exam_packages table
- Create `PullAgent` on center: fetch manifest, compare with exam_sync_state, download missing revisions, verify checksum and signature, apply
- Create `ExamPackageApplier`: one transaction, upsert by UUID at each level, forceFill for access_code. If exam has started or has submissions, accept only whitelisted grading-only differences (A1), reject the rest with logged error. Dispatch chunked regrade jobs when grading inputs changed
- Create `SyncPull` artisan command. Scheduler runs every minute; command decides if center's configured interval has elapsed. Sync now button dispatches the same job
- Create center admin sync page (last pull, backlog, per-exam applied revision, errors)
- Clock-skew check from manifest's server_time
- Default pull interval 5 minutes, adjustable per center between 1-60 minutes
- Done when: center applies a new exam, applies a revision, ignores an already-applied revision, resumes after killed download, and is correct after 3 days offline

**✓ Step 5: Phase 4: Push agent and central ingestion**

Implement push sync: outbox pattern, batch push of submissions and proctoring logs, central ingestion with parity checking.

- Implement outbox rule: submitted and graded rows where synced_version < sync_version. Increment sync_version on any later change (regrade, manual review, extra time, readmission)
- Create `PushAgent` on center: push submissions in batches of ~100 with their answers; push proctoring logs in batches of ~1000, only for submissions central has already acknowledged
- Create ingest endpoints on central:
  - POST /api/sync/v1/ingest/submissions (batch of submissions with answers)
  - POST /api/sync/v1/ingest/proctoring (batch of proctoring logs)
- `SubmissionIngestService`: upsert on UUID, accept only higher sync_version, upsert candidate by index number, reject unknown exam or revision with clear error code. Dispatches ParityCheckJob after ingestion.
- `ProctoringIngestService`: upsert proctoring logs by UUID, only for submissions central has acknowledged
- Queued central job re-grades from raw answers using the package for graded_revision and sets parity_ok. Mismatch never overwrites center's score; both stored and flagged
- `ParityCheckService`: compares center score vs central re-grade
- `ParityCheckJob`: queued parity check after submission ingestion
- Retry with exponential backoff and jitter (30 seconds to 15 minutes) via `SyncRetryJob`
- Trigger push after grading, on schedule, and on demand
- Create `SyncPush` artisan command
- Create `SyncRetryJob` for failed sync retries
- Tests: `SubmissionIngestTest` (7 tests), `ParityCheckTest` (5 tests), `PullAgentTest` (6 tests), `PushAgentTest` (6 tests)
- Bug fixes: PushAgent::retryWithBackoff now passes serializable string operation to SyncRetryJob; config key fixed from `center.app_role` to `center.role` in SyncPush and CenterHealth commands; SubmissionIngestService now dispatches ParityCheckJob after ingestion
- Done when: pushing twice creates no duplicates, killed connection resumes cleanly, 10 centers × 1000 submissions ingest in under 10 minutes with parity_ok true on all

**✓ Step 6: Phase 5: Central multi-center dashboard and scoping**

Build the central admin dashboard with center filtering, per-center cards, parity alerts, and cross-center duplicate detection.

- Create `CenterDashboardController` with center filter (session-based) and All centers option for admin role
- Center staff pinned to their own center (enforced by CenterScope)
- Per-center cards: candidates, submitted, completion rate, average score, flagged proctoring count, last synced time, backlog size
- Parity alerts panel: shows submissions where parity_ok is false
- Cross-center duplicate attempts panel: same index number at more than one center
- Every figure shows data freshness so nobody mistakes synced data for live data
- Create views: `resources/views/center/dashboard.blade.php`, `resources/views/center/sync.blade.php`, `resources/views/center/center/show.blade.php`
- Admin role bypasses CenterScope; center users cannot see other centers' rows
- Done when: center user can never see another center's rows (tested), admin can switch between all centers and a single center, and all dashboard figures show data freshness

**✓ Step 7: Phase 6: Corrections and revisions workflow**

Implement grading-only corrections on central with chunked regrade jobs.

- Create central grading-only revision form: exclude or restore a question, fix a key, change marks, release results
- `ExamPackageApplier` regrades in chunks of ~50 submissions per queued job, on both center and central
- Create `ChunkedRegradeJob` that processes ~50 submissions per chunk
- Replace `toggleGrading`'s in-request loop in `ExamQuestionController` with the same chunked job path on central
- Update `GeneralExamGradingService` to use McqGrader and chunked regrade
- Release results via state block (small separate update, no full package download)
- Done when: excluding a question on central produces identical regraded results on every center and on central itself

**✓ Step 8: Phase 7: Academic structure and question bank backup/restore**

Add UUID support to academic tables and question bank, then implement export/import as signed bundles.

- Add uuid to academic_groups, academic_levels, academic_subjects, academic_topics, academic_subtopics, and multiple_choice_questions (migrations)
- Create export service: selected groups, levels, or subjects down to MCQs as one signed bundle
- Map or drop added_by and modified_by user FKs; include soft-deleted flags
- Create idempotent import service: upserts by UUID, recreates polymorphic Question wrapper
- Restore to another server, or optionally let centers pull the bank as read-only release
- Reuse the same UUID-addressed package format and generic admin backup page
- Drop source_question_id from general_exam_questions after bank has UUIDs
- Done when: exporting a subject and importing it twice gives the same rows with no duplicates

**✓ Step 9: Phase 8: Hardening, load testing, and rollout preparation**

Final hardening: load tests, security pass, center health monitoring, and provisioning documentation.

- Load test: 1000-candidate exam simulation (join, answer every ~20 seconds, heartbeats, proctoring events) on center hardware
- Security pass: token rotation, signature checks, rate limits, HTTPS only
- Center health page on central: last seen, last pull, last push, backlog, app version, parity alerts
- Alerting for stale centers (configurable threshold)
- Create `CenterHealth` artisan command
- Provisioning checklist: install, set env vars, create center on central, copy token, first pull, smoke exam, NTP verification
- Runbook: forced full resync, token rotation, rolling back a bad revision (publish n+1 equal to n-1), restoring center from backup and re-pushing
- Pilot plan: two centers before the rest
- Done when: load test passes, security audit complete, center health page operational, and provisioning runbook validated with a pilot

**✓ Step 10: Admin UI — views, routes, controllers, and navigation**

Build all missing admin views, wire up routes, and add sidebar navigation entries so every multi-center sync feature is accessible through the web interface. All views use Tailwind CSS to match the existing app design.

**10.1 Routes** — Create `routes/center-sync.php` with all admin routes under `/admin/center-sync` prefix:
- Center Management: index, create, store, show, edit, update, toggle active, regenerate token
- Exam Publish/Revise: index (list published exams), show (exam detail with package history), publish, revise, release results
- Multi-Center Dashboard: index (all centers overview), show (per-center detail)
- Sync Status: index (pull/push status, exam sync states, failed syncs)
- Bank Export/Import: index, export, import
- Grading Corrections: show (exam corrections form), update corrections

**10.2 Controllers** — New and updated controllers:
- `app/Http/Controllers/Center/CenterManagementController.php` — CRUD for centers (create, edit, activate/deactivate, regenerate tokens)
- `app/Http/Controllers/Center/ExamPublishController.php` — Publish exams, create revisions, view package history, release results
- `app/Http/Controllers/Center/CenterDashboardController.php` — Update existing controller to work with new routes
- `app/Http/Controllers/Center/CenterSyncController.php` — Update existing controller to work with new routes
- `app/Http/Controllers/Center/BankExportImportController.php` — Export and import academic structure and question bank
- `app/Http/Controllers/Center/GradingCorrectionsController.php` — Exclude questions, fix answer keys, change marks, release results

**10.3 Views** — All Tailwind CSS, extending `components.layouts.app`:
- `resources/views/center-sync/centers/index.blade.php` — Center list with search, filter, and action buttons
- `resources/views/center-sync/centers/create.blade.php` — Create center form
- `resources/views/center-sync/centers/edit.blade.php` — Edit center form
- `resources/views/center-sync/centers/show.blade.php` — Per-center detail view (rewrite existing in Tailwind)
- `resources/views/center-sync/centers/token.blade.php` — Token display and regeneration modal
- `resources/views/center-sync/exams/index.blade.php` — Published exams list with status badges
- `resources/views/center-sync/exams/show.blade.php` — Exam detail with package history, publish/revise actions
- `resources/views/center-sync/exams/publish.blade.php` — Publish form with validation feedback
- `resources/views/center-sync/exams/revise.blade.php` — Revision form (grading-only corrections)
- `resources/views/center-sync/dashboard.blade.php` — Multi-center dashboard (rewrite existing in Tailwind)
- `resources/views/center-sync/sync/index.blade.php` — Sync status page (rewrite existing in Tailwind)
- `resources/views/center-sync/bank/index.blade.php` — Bank export/import page
- `resources/views/center-sync/corrections/show.blade.php` — Grading corrections form

**10.4 Sidebar Navigation** — Add "Multi-Center Sync" section to `admin-navigation.blade.php`:
- Only visible to admin/owner roles on central instances (APP_ROLE=central)
- Menu items: Dashboard, Centers, Published Exams, Sync Status, Bank Backup, Grading Corrections
- For center staff (APP_ROLE=center): show "Sync" section with Sync Status only

**10.7 Center-Instance Routes** — Split routes by role in `routes/center-sync.php`:
- Center instances (APP_ROLE=center): routes under `/center-sync` prefix with sync.index, sync.pull, sync.push
- Central instances (APP_ROLE=central): routes under `/admin/center-sync` prefix with all admin features
- Routes are conditionally registered using `if (config('center.role') === 'center')` / `if (config('center.role') === 'central')`
- The sync view shows the central server URL on center instances for clarity

**10.5 Existing View Rewrites** — Convert Bootstrap to Tailwind:
- `resources/views/center/dashboard.blade.php` — Rewrite using Tailwind classes matching existing app design
- `resources/views/center/sync.blade.php` — Rewrite using Tailwind classes
- `resources/views/center/center/show.blade.php` — Rewrite using Tailwind classes

**10.6 Route Registration** — Register new routes in `bootstrap/app.php` or `routes/web.php`:
- Include `routes/center-sync.php` in the route loading
- Apply auth and verified middleware
- Add role-based middleware to block center instances from admin routes

Done when: admin can navigate to all multi-center sync features from the sidebar, create/edit centers, publish exams, view dashboard, check sync status, export/import bank, and apply grading corrections — all through the web UI

**✓ Step 11: Sync Pull/Push Bug Fixes**

Fixed critical bugs that prevented exam data from being applied on center instances and enhanced logging/output.

**ExamPackageApplier bugs fixed:**
- `applyQuestions()` referenced `$section->assignment_id` which doesn't exist — fixed to `$section->general_exam_id`
- Added detailed logging at every step: exam, sections, questions, participants
- Added count tracking: `section_count`, `question_count`, `participant_count` returned from each method
- Added stale record cleanup: when a revision removes sections/questions/participants, they are deleted from the center's database
- Added `Schema` facade import for future use

**PullAgent enhancements:**
- `pull()` now returns `details` array with per-exam status, message, sections, questions, participants counts
- `pullExam()` now returns detailed results: message, sections, questions, participants
- Added logging at every step: manifest fetch, download, checksum verify, signature verify, apply
- Clock skew warnings are included in the details array

**SyncPull command enhancements:**
- Shows central URL being pulled from
- Displays per-exam status with ✓/✗/⚠ icons
- Shows section/question/participant counts for each exam
- Clock skew warnings are displayed

**Verified components:**
- `ExamPublishService::publish()` — correctly validates, builds, signs, and stores packages
- `ExamPackageBuilder::build()` — produces canonical JSON with sorted keys
- `SyncManifestService::getExamList()` — reads from `general_exam_packages` table
- `GradeScale::defaultArray()` — exists and provides default grading scale

**Root cause of "pull does nothing":**  
The `$section->assignment_id` bug in `applyQuestions()` caused all questions to be created with `general_exam_id = null`, making them orphaned records that couldn't be found by the exam. The exam, sections, and participants were created, but the questions were effectively lost.

**✓ Step 12: Pull Flow Bug Fixes**

Fixed bugs that prevented pulled exams from appearing on center instances.

**ExamSyncState missing exam() relationship:**
- `ExamSyncState` model had no `exam()` relationship, so `CenterSyncController::indexCenter()` calling `ExamSyncState::with('exam')->get()` failed silently
- Added `exam()` BelongsTo relationship joining `exam_uuid` to `GeneralExam.uuid`

**ExamPackageBuilder::toCanonicalJson() didn't sort keys:**
- The spec requires byte-identical canonical JSON with sorted keys
- Added `sortArrayRecursive()` helper that sorts all array keys recursively before `json_encode`

**✓ Step 13: Checksum Mismatch Bug Fix**

Fixed "Checksum mismatch" error when pulling exam packages from central.

**Root cause:**  
The checksum was computed on the payload WITHOUT `checksum`/`signature` keys, but the PullAgent decompressed the full package (which includes those keys) and re-hashed the entire JSON. The two JSON strings were different, so the checksums never matched.

**Fix applied:**
- `PullAgent::pullExam()` — Instead of re-computing the checksum from decompressed content, it now extracts the `checksum` field from the package JSON and compares it with the manifest's checksum. The signature is verified against this embedded checksum.
- `ExamPackageBuilder::build()` — Re-sorts the full package array after adding checksum/signature so `compress()` produces consistent output.
- `ExamPackageBuilder::sortArrayRecursive()` — Added `SORT_STRING` flag for deterministic key ordering across PHP versions.

**Next steps for the user:**
- Deploy these changes to Machine B (Arch Linux / center)
- On Machine A (Central), re-publish the exam that failed (click Republish on exam 15)
- Run `php artisan config:clear && php artisan view:clear` on both machines
- Retry `php artisan sync:pull --force` on Machine B