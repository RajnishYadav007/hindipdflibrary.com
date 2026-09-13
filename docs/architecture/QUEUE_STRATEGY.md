# Workstream G: Queue & Background Processing Strategy

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Component:** Background Queue & Asynchronous Processing  
**Host Context:** Shared Hosting (No Persistent Daemons / No Supervisor)  
**Auditor:** Lead Solutions Architect & Senior Laravel Engineer  

---

## 1. Executive Summary & Constraints

On standard shared hosting platforms like StackCP, long-running processes (e.g., `php artisan queue:work` running as an infinite daemon loop) are automatically terminated by Linux cgroup resource enforcers or web server watchdog timers. Supervisor is not installed or configurable by shared hosting users.

Therefore, **relying on persistent workers or Redis queues in V1 will lead to silent job execution failures**.

The queue strategy for Hindi PDF Library V1 is **Database Queue with Scheduled Ephemeral Execution (`--stop-when-empty`)**.

---

## 2. Queue Architecture for Shared Hosting (V1)

```
[ HTTP Web Request ]
        │
        ▼ (Asynchronous Dispatch: e.g., dispatch(new SendWelcomeEmail($user)))
[ MySQL `jobs` Table ] (Fast write, < 5ms)
        │
        │ (Scheduled via Cron every 2-5 minutes)
        ▼
[ php artisan queue:work --stop-when-empty --max-time=50 --memory=128 ]
        ├── Job 1 executes (Email sent)
        ├── Job 2 executes (Thumbnail generated)
        ├── Queue empty ──► Process terminates cleanly (0 memory leak)
        └── On failure ──► Written to `failed_jobs` table (Retryable)
```

---

## 3. Implementation Blueprint

### 3.1. Database Driver Configuration
In `.env`:
```dotenv
QUEUE_CONNECTION=database
```

The standard Laravel queue migration will be executed:
- `jobs`: Stores pending, reserved, and available jobs.
- `job_batches`: Stores batched job progress (for bulk PDF indexing/WP imports).
- `failed_jobs`: Records exception traces and payload data for jobs that fail after maximum attempts.

### 3.2. Scheduled Ephemeral Worker Execution
In `app/Console/Kernel.php` (or Laravel 13 `routes/console.php`):
```php
use Illuminate\Support\Facades\Schedule;

// Run queue worker every 2 minutes, stopping when empty to stay within shared memory limits
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=50 --memory=128 --timeout=60')
    ->everyTwoMinutes()
    ->withoutOverlapping(5) // Prevent concurrent workers from locking the same jobs
    ->runInBackground();
```

### 3.3. Worker Parameter Rationale
- `--stop-when-empty`: Worker processes all pending jobs and exits immediately. Does not idle or hold system memory.
- `--max-time=50`: Prevents the PHP process from running past 50 seconds (safely under typical 60s shared hosting timeout limits).
- `--memory=128`: Terminates the worker gracefully if memory reaches 128MB, preventing shared hosting out-of-memory kills.
- `--tries=3`: Retries transient network failures (e.g., SMTP timeout) up to 3 times before moving the job to `failed_jobs`.
- `->withoutOverlapping(5)`: Uses an atomic lock to guarantee only one worker executes at any given moment.

---

## 4. Job Classification & Execution Priorities

| Job Type | Execution Model | Failure Strategy | V1 Shared Hosting Impact |
| :--- | :--- | :--- | :--- |
| **Transactional Emails** (Verification, Reset) | Queued (`database`) | Retry 3 times, exponential backoff | Immediate user feedback; job processed in next cron window (< 2 mins) |
| **Media Conversions** (WebP generation) | Queued (`database`) | Retry 2 times | Heavy image processing runs out of band, preserving web request responsiveness |
| **WordPress Migration Batches** | Queued (`database`) | Log error, continue next batch | Allows importing 1,000s of books in chunked batches without timeout |
| **Sitemap XML Generation** | Scheduled Artisan Task | Alert admin via log | Offloaded to off-peak hours (e.g., 03:00 UTC) |
| **First-Party Analytics Aggregation** | Scheduled Artisan Task | Idempotent recalculation | Aggregates buffer tables every hour |

---

## 5. Handling High Urgency Jobs (Synchronous Fallback)

For actions where users expect immediate response (e.g., password reset token emails during development or testing), if cron latency (1-2 minutes) is undesirable:
- We can selectively specify `dispatchSync()` or configure a dedicated `sync` connection during early testing.
- However, for production, `database` queue + cron provides maximum reliability against third-party SMTP latency.

---

## 6. Future Upgrade Path (VPS / Dedicated Server)

When migrating to a dedicated VPS:
1. Install **Supervisor** on the Linux server.
2. Configure Supervisor configuration file (`/etc/supervisor/conf.d/hindipdf-worker.conf`):
   ```ini
   [program:hindipdf-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/hindipdflibrary.com/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   numprocs=4
   ```
3. Update `.env`:
   ```dotenv
   QUEUE_CONNECTION=redis
   ```
4. **Code impact:** ZERO lines of application code need modification.
