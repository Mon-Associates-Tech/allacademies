# AHPC Load Balancing and Central-First Implementation Plan

Draft v1 · 11 November 2026 · Plan only: nothing below has been run on real servers yet

## 1. Goal

Two stages built from the same parts:

- **Stage A, local-first with Central overflow (now).** Each center serves its own students (about 400 concurrent). When the center is saturated or down, new candidates are served by Central instead.
- **Stage B, central-first with center fallback (target).** Central is the main server for all candidates (about 2000 concurrent across every center). Each center server becomes the edge proxy, and the fallback when Central is busy or unreachable.

Starting facts: a center has 32 GB RAM, 800 GB storage and about 400 concurrent students. Central has 64 GB RAM, 1 TB storage and serves about 2000 concurrent students across all centers. Not yet known: Central CPU cores and disk type, and the bandwidth and latency between each center and Central.

## 2. Architecture

```
Students (LAN) -> Center HAProxy :80/:443  (TLS, routing, sticky cookie)
                      |
         +------------+-------------+
         v                          v
  Local app                    Central (WAN, HTTPS)
  Nginx 127.0.0.1:8081         Nginx + PHP-FPM
  PHP-FPM, MariaDB, Redis      MariaDB, Redis
         |                          ^
         +-- sync, every 15 min ----+
```

- **Center:** HAProxy takes over ports 80/443 and ends TLS. The app's Nginx moves to 127.0.0.1:8081 (HTTP only, with fastcgi\_param HTTPS on so Laravel still sees HTTPS).
- **Central:** keeps its public Nginx. It gains two small endpoints, /up (is the app alive) and /lb-health (returns 503 when busy PHP workers pass about 85%). It also trusts each center's IP as a proxy so real client IPs reach Laravel.
- **Students** only ever talk to their center's address. They need no changes and never see Central.

## 3. Routing rules

| Situation | Stage A (local-first) | Stage B (central-first) |
| --- | --- | --- |
| Candidate already has the ahpc\_route cookie | Stays on the server named in the cookie | Same |
| New candidate, preferred server healthy and below its limit | Local | Central |
| Preferred server full or down | Central | Local |
| Static files (/build/, /storage/) | Local | Always local (same release), so only dynamic requests cross the WAN |
| Both servers down | Branded 503 page | Branded 503 page |

Principles:

- A candidate is never moved mid-exam. The server that starts an attempt owns it. The only exception is when that server is down, and it should be documented as a known edge case.
- The cookie lasts at least as long as the longest exam plus a margin (starting value 4 hours) and is only set over HTTPS.
- Starting thresholds, to be tuned in Phase 3: a center counts itself full at about 150 concurrent connections (its PHP workers max out at about 189); in Stage B a center sends at most about 350 connections to Central. Because each center only sees its own connections, Central's /lb-health endpoint is the global signal: when it returns 503, every center treats Central as down and falls back to local.

## 4. Questions to settle first (Phase 0)

1. **Sync semantics.** Which server owns an attempt, what gets pushed where, and can Central run an exam for a center's students and make the results visible to the center? Today sync pulls and pushes every 15 minutes. This gates everything else.
2. **Central hardware and network.** CPU cores, disk type, and the bandwidth and latency from each center to Central. Does every center have reliable internet?
3. **The app behind proxies.** Laravel TrustProxies, real client IP, HTTPS awareness, and rate limiters that key on IP (otherwise every overflow student looks like one address).
4. **One release everywhere.** Vite asset hashes must match on Central and centers, so deploy by tag, not by branch head.
5. **Real memory per PHP worker and database size.** The script assumes 100 MB per worker, which at 2000 students means about 58 GB of Central's 64 GB in the worst case.
6. **Exam-start behaviour.** About 2000 logins and exam loads in seconds is the real risk. Decide on staggered starts or a waiting room, and cache questions in Redis.

## 5. Phases

Effort: S is a few days, M about a week, L several weeks. These are rough guesses, not commitments.

### Phase 0: Measure and decide (S)

- Record the numbers from section 4 items 2 and 5. Use iperf3 and mtr between each center and Central.
- Read the sync code and write down how an attempt moves between servers.
- Choose the exam-start policy and the starting thresholds.

Exit: numbers recorded, sync behaviour written down, go/no-go on continuing.

### Phase 1: Prepare Central and the app (M)

- Take a backup of Central and test a restore before changing anything.
- Re-tune Central: ahpc-setup --only mariadb,redis,php,nginx --students 2000. Expect about 400 PHP workers, a 16 GB InnoDB pool, 2 GB Redis and 550 DB connections. Adjust --fpm-children from the Phase 0 memory data.
- App changes: TrustProxies for the center IPs, /up and /lb-health, rate limiters on the real client IP, SESSION\_DOMAIN null, secure cookies.
- Pin releases: deploy the same tag to every server.
- Install Central's CA on each center with --central-ca so HAProxy can verify Central.

Exit: Central passes a 400-student load test on its own; health endpoints work; releases pinned.

### Phase 2: Build the balancer on one pilot center, Stage A (M)

- Add the lb section to the script (section 6).
- On the pilot: move the app Nginx to 127.0.0.1:8081, install HAProxy, build haproxy.pem from the local certificate, load the Stage A config from the appendix, enable the stats socket.
- Verify: the cookie is set on the first visit and respected afterwards, HTTP redirects to HTTPS, Central logs show real client IPs, student devices need no change.

Exit: the pilot works under normal traffic; the kill switch (disable server) is tested.

### Phase 3: Load test and tune (M)

- Write a load test (k6 or Locust): login burst, exam load, steady autosave, submit.
- Run the tests in section 7, then tune the thresholds, PHP workers, DB connections and timeouts.

Exit: every Stage A test in section 7 passes.

### Phase 4: Pilot a real exam in Stage A (S)

- Run a small real session at the pilot center with monitoring. Temporarily lower the threshold so a handful of candidates really are served by Central.
- Check that results appear where reports expect them and no answers were lost.

Exit: no lost answers; results reconcile.

### Gate: go or no-go for central-first

Suggested starting criteria, to be adjusted from the Phase 3 data:

- At 2000 simulated concurrent students: error rate under 0.5%, exam page p95 under 1.5 s, autosave p95 under 500 ms.
- PHP workers under 85% busy and database load under 70% for a sustained period, no out-of-memory events.
- WAN peak under 70% of the link at every center.
- Failure drills in section 7 pass, and sync is confirmed to reconcile attempts.

### Phase 5: Central-first at the pilot center, Stage B (M)

- Load the Stage B config. Serve /build/ locally and use /lb-health as Central's health check.
- Re-run the 2000-student test using several load generators, then the failure drills (Central down, Central slow, WAN cut mid-exam).

Exit: gate criteria met under Stage B; fallback drills pass.

### Phase 6: Roll out (M)

- Move one center per exam session, keeping the Stage A config as a one-command rollback.
- Write the runbook (section 9). Make sure every center runs the same release.

Exit: all centers on Stage B and one full multi-center session completed.

### Phase 7: Operate (ongoing)

- Monitoring and alerts, a weekly certificate check, capacity reviews, a quarterly restore drill.
- Plan a standby or replica for Central, and keep the center servers as the fallback.

## 6. Script work (backlog)

- **lb section:** install HAProxy and render its config from the saved settings. Flags: --lb-mode local-first|central-first|off, --central-url, --lb-local-max (150), --lb-central-max (350), --lb-cookie-hours (4).
- Move the app Nginx to 127.0.0.1:8081 when lb is on; HAProxy handles the HTTPS redirect.
- tls section: build haproxy.pem (certificate plus key) and reload HAProxy on renewal or an IP change.
- --release-tag, so every server deploys the same tag.
- A Central sizing profile with a tunable per-worker memory figure.
- A backup section: database dump and storage to a second disk, retention, restore test.
- The load-test script, and dry-run plus tests for all of the above.

## 7. Test plan

Pass criteria are suggested starting values.

| Test | How | Pass |
| --- | --- | --- |
| Sticky routing | Repeat requests from two clients | Cookie set once, same server every time |
| Overflow (Stage A) | Push the center past its limit | Only new candidates go to Central; existing stay put |
| Central-first default (Stage B) | Normal load | New candidates land on Central; assets served locally |
| Login burst | 2000 logins in 60 s | Errors under 0.5%, p95 under 1.5 s |
| Steady exam | 2000 users autosaving every 10 s | Autosave p95 under 500 ms; workers under 85% busy |
| Central down | Stop Central | New candidates go local within one health-check interval |
| Central overloaded | Force /lb-health to 503 | Centers fall back; recover automatically |
| WAN cut mid-exam | Cut a center's link | Local candidates unaffected; behaviour for Central candidates documented |
| Local down (Stage A) | Stop PHP-FPM at the center | New candidates go to Central |
| Power loss | Pull power mid-exam | No committed answers lost; services restart cleanly |
| IP change | ahpc-setup --fix-ip | New certificate picked up; cookies still work |
| Rollback | Switch mode | Back to the previous mode in under 5 minutes |

## 8. Risks

| Risk | Mitigation |
| --- | --- |
| Sync cannot reconcile attempts | Phase 0 gate; one owner per attempt; do not proceed until confirmed |
| All centers overload Central at once | /lb-health global signal, per-center cap, staggered starts, standby capacity |
| WAN is the bottleneck | Serve static files locally; measure first; keep centers as fallback |
| Central is a single point of failure | Center fallback, backups, a standby plan |
| A candidate is rerouted mid-exam | Cookie rule; documented exception when the server is down |
| All overflow students share one IP | TrustProxies and a test on rate limits |
| Cookie not stored over HTTP | HTTPS only, with a redirect |
| Asset hashes differ between servers | Pinned releases |
| A bad config locks everyone out | One-command rollback, dry run, VM test first, console access on the pilot |
| be\_conn counts idle keep-alive connections | Tune the threshold in the load test |

## 9. Operations and rollback

The ahpc-setup flags below do not exist yet; they arrive with the lb section.

- **State:** echo 'show stat' | socat stdio /run/haproxy/admin.sock
- **Force new candidates local:** echo 'disable server central/central' | socat stdio /run/haproxy/admin.sock
- **Force new candidates to Central:** echo 'disable server local/app' | socat stdio /run/haproxy/admin.sock
- **Stage B back to Stage A:** ahpc-setup --only lb --lb-mode local-first
- **Remove the balancer entirely:** ahpc-setup --only lb,nginx,tls --lb-mode off (puts Nginx back on 80/443)

Every phase needs a rollback written before it starts.

## Appendix: HAProxy reference config (untested)

Common parts, used by both stages:

```
global
    maxconn 4000
    stats socket /run/haproxy/admin.sock mode 660 level admin

defaults
    mode http
    option forwardfor
    timeout connect 3s
    timeout client 60s
    timeout server 120s
    timeout queue 10s

frontend web
    bind :80
    bind :443 ssl crt /etc/ahpc/tls/haproxy.pem
    http-request redirect scheme https unless { ssl_fc }
    acl route_local   req.cook(ahpc_route) -m str local
    acl route_central req.cook(ahpc_route) -m str central
    acl local_down    nbsrv(local) lt 1
    acl central_down  nbsrv(central) lt 1
    acl local_full    be_conn(local) ge 150
    acl central_full  be_conn(central) ge 350
    # routing rules: see Stage A or Stage B below

backend local
    option httpchk GET /up
    http-response add-header Set-Cookie 'ahpc_route=local; Path=/; Max-Age=14400; HttpOnly; Secure; SameSite=Lax' unless { req.cook(ahpc_route) -m found }
    server app 127.0.0.1:8081 check maxconn 180

backend central
    option httpchk GET /lb-health
    http-check expect status 200
    http-response add-header Set-Cookie 'ahpc_route=central; Path=/; Max-Age=14400; HttpOnly; Secure; SameSite=Lax' unless { req.cook(ahpc_route) -m found }
    server central CENTRAL_IP:443 check ssl verify required ca-file /usr/local/share/ca-certificates/ahpc-central-ca.crt verifyhost CENTRAL_IP maxconn 400
```

Stage A routing rules (local-first):

```
    use_backend central if route_central !central_down
    use_backend local   if route_local !local_down
    use_backend central if local_down !central_down
    use_backend central if local_full !central_down
    default_backend local
```

Stage B routing rules (central-first):

```
    use_backend local   if { path_beg /build/ } !local_down
    use_backend local   if route_local !local_down
    use_backend central if route_central !central_down
    use_backend local   if central_down !local_down
    use_backend local   if central_full !local_down
    default_backend central
```
