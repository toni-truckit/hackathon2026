# Project Context: TruckIt Connect (Truckit Connector for Claude)

## 1. Product goal

Build a **public Truckit MCP server** in this Laravel application and list it in **Claude’s Connectors Directory** (and submit the same server to **ChatGPT** in the same release). A user should be able to say, for example, “I need my couch moved from Brisbane to Sydney” in Claude and leave the chat with a **real Truckit Book Now price** and a **booking ready to pay** on truckit.net.

**Competitive frame:** Airtasker ships a public MCP (`mcp.airtasker.com`) with removals as a top category; they suggest a budget and wait for offers. Truckit wins with **instant Book Now pricing** in-chat and **SmartMatch** for providers—not invented budgets or long offer waits.

**Hard rule (Claude directory):** Connectors must **not move money**. `create_booking` creates the booking and returns a **secure truckit.net checkout URL**. Payment and job confirmation happen only on Truckit.

---

## 2. What this repository is

| Layer | Role |
|--------|------|
| **TruckIt Connect (this repo)** | Laravel app: public marketing/support site (Livewire), Filament admin (static pages, FAQs, settings), and **host for the public MCP server** (Laravel MCP). |
| **App database (`DB_*`)** | Sessions, cache, queues, CMS, settings—not Truckit marketplace source of truth. |
| **`business_api` / `monolith` connections** | Optional read-only access for engineering; MCP tools must call **platform APIs**, not ad-hoc monolith queries from tool handlers without a defined boundary. |
| **MCP tools** | **To be implemented** here via [Laravel MCP](https://laravel.com/docs/mcp)—not a separate TypeScript service. |

This is **not** a blog or generic starter kit.

---

## 3. MVP scope (customer side, AU only)

**In scope**

- Eight MCP tools (see below), read/write split, no catch-all tool.
- Interactive **MCP App** cards: quote, provider comparison, booking summary, job status.
- Guided `/truckit:move` skill shipped as a Claude plugin (alongside connector).
- Guest instant quote where product allows; OAuth when listing jobs, bookings, or account data.

**Out of scope for MVP**

- Taking payment inside Claude (directory policy).
- Provider-side tools, Truckit for Business accounts, NZ.
- Messaging providers through Claude, photo upload of items.

**Phase 2:** Provider tools (search lane jobs, submit quote, upcoming jobs); T4B repeat booking; NZ.

**Phase 3:** Public docs for Cursor/n8n; voice; proactive notifications.

---

## 4. Eight MVP tools

Names stay under 64 characters. Descriptions state **what the tool does**, never how Claude should behave.

| Tool | Type | Purpose | OAuth |
|------|------|---------|-------|
| `get_instant_quote` | Read | Book Now price (AUD incl. GST) for items between AU suburbs; quote card | Guest where allowed |
| `get_provider_profile` | Read | Business name, rating, jobs, vehicles, insurance, member since; profile card | No |
| `list_quotes` | Read | Provider quotes on a job; comparison card | Yes |
| `get_job_status` | Read | Status pipeline + provider + dates; status card | Yes |
| `list_my_jobs` | Read | Up to 20 jobs, newest first, optional status filter | Yes |
| `post_job` | Write | Publish marketplace listing when Book Now cannot price; listing card | Yes |
| `create_booking` | Write | Booking from quote or provider quote; checkout URL on truckit.net; booking card | Yes |
| `cancel_job` | Write (destructive) | Cancel **unpaid** jobs only; paid → link to Truckit cancellation | Yes |

**Annotations:** Read tools → `readOnlyHint: true`, `destructiveHint: false`. `post_job` and `create_booking` → `destructiveHint: true` (create records, notify providers). `cancel_job` → destructive and irreversible. Every tool needs a human-facing title (e.g. “Get instant Truckit quote”).

**Errors:** Specific user-facing messages only (e.g. “We can’t price pianos instantly. Post the job to get quotes from providers.”)—not generic “Bad Request”. Keep each tool result well under host token limits (~25k).

---

## 5. Conversation rules

- Ask **at most two questions** before showing a price; use sensible defaults and state assumptions.
- **Never invent a price.** If Book Now cannot price, say so and offer marketplace `post_job` flow.
- Always show **GST-inclusive price** and **date window** together.
- **Write tools** require explicit user approval in the host before execution.

**Three journeys to document and test for directory review:**

1. **Instant price and book:** quote → optional OAuth → `create_booking` → approval → checkout link → later `get_job_status`.
2. **Marketplace quotes:** `post_job` → later `list_quotes` → `get_provider_profile` → `create_booking` → checkout.
3. **Manage jobs:** `list_my_jobs` → `cancel_job` (unpaid only) with confirmation.

---

## 6. Architecture

Claude never touches Truckit databases or payments directly.

```text
User → Claude → Laravel MCP (this app, Streamable HTTP) → Auth / Platform API / Pricing → response + MCP App card
create_booking → checkout URL on truckit.net (source tag: claude_connector)
```

| Dependency | Role |
|------------|------|
| **Laravel MCP server** | Thin gateway: validate OAuth, map tools to API calls, minimise response data, attach card metadata. |
| **Auth Service** | OAuth 2.0 + PKCE; scopes e.g. `truckit:read`, `truckit:write`, `truckit:payments`; allowlist Claude callback URLs. |
| **Truckit platform API** | Jobs, quotes, bookings, notifications. |
| **Book Now / Category Matching / SmartMatch** | Instant price and provider ranking. |
| **truckit.net checkout** | Payment; job confirmed after pay. |

**Transport:** Streamable HTTP (required for Claude directory). Production target: `https://mcp.truckit.net/mcp` (local: same app, `APP_URL` + published MCP route).

**Security:** Validate `Origin`, CORS for Claude clients, rate limit per user, no secrets or internal IDs in tool output. Suburbs in quotes; full addresses only on the user’s own bookings where product allows.

---

## 7. Laravel MCP implementation notes

When building tools in this repo:

1. `composer require laravel/mcp`
2. Publish `routes/ai.php` (`php artisan vendor:publish --tag=ai-routes`)
3. One `Server` class registering **exactly eight** tools—**do not** hide tools behind `ToolSearch` / catch-all `execute_tools` for MVP.
4. One `Tool` class per MVP tool; business logic in Actions/Services that call the platform API.
5. MCP App cards: `App\Mcp\Resources\*` + `#[RendersApp]` on tools that return quote, comparison, booking, status cards.
6. Tests: `Server::tool(ToolClass::class, $input)->assertOk()` (PHPUnit); OAuth paths via `actingAs` where applicable.
7. Beta: `php artisan mcp:inspector <server>` and Claude **custom connector** before directory submission.

Docs: [Laravel MCP](https://laravel.com/docs/mcp).

---

## 8. Cards and UX (MCP Apps)

Four card types: **Quote**, **Provider comparison**, **Booking**, **Job status**. Rules:

- Truckit design tokens; work at ~380px width and dark mode.
- One primary button per card; price also spoken in Claude text (accessibility).
- External links only to **truckit.net** domains (declare in directory allowed link URIs).

---

## 9. Directory and compliance checklist

Before submit:

- [ ] Every tool has title + correct `readOnlyHint` / `destructiveHint`
- [ ] No catch-all tool; read/write fully separated
- [ ] OAuth E2E on Claude web, desktop, Claude Code (no extra SMS/email step for reviewer)
- [ ] Test Truckit account with sample jobs, quotes, booking kept active after review
- [ ] Public help article on truckit.net (setup, 3+ example prompts, privacy, support)
- [ ] Privacy policy lists connector data (names, suburbs, items, job status)
- [ ] Dedicated connector support channel
- [ ] GA server, specific errors, responses under token limits
- [ ] Allowed link URIs for checkout and job pages
- [ ] Screenshots of every card

**Policy:** Tool descriptions are factual only—no prompts to Claude, no cross-sell. Server calls only Truckit-owned APIs on Truckit domains.

---

## 10. Success metrics (first 90 days after listing, proposed)

| Metric | Target |
|--------|--------|
| Connected users (OAuth completed from Claude) | 1,000 |
| Quotes served (`get_instant_quote` with price) | 4,000 |
| Quote → paid booking rate | Above 20.3% (marketplace baseline) |
| Paid bookings (`source=claude_connector`) | 150 |
| Book Now pricing coverage | ≥ 70% of quote requests |
| Tool error rate | Under agreed SLO |

Revenue follows existing marketplace take rate on tagged paid bookings.

---

## 11. Coding directives

### PHP and Laravel

- `declare(strict_types=1);`, explicit param and return types.
- Fat models / Actions / Services; skinny MCP `Tool::handle()` methods.
- No N+1 on any public or MCP-adjacent code paths.
- `config()` only outside config files—never `env()` in application code.

### MCP-specific

- Tools delegate to services that talk to **Auth Service** and **platform API**—do not embed payment or write monolith data from tools without an approved integration layer.
- Tag checkout and analytics with `claude_connector` (or configured equivalent).
- PHPUnit coverage for each tool happy path, auth gate, and representative errors.

### Livewire / Filament (CMS)

- Livewire v4 attributes; Filament for content only—marketplace logic stays out of Filament resources.

### Tests

- Run `php artisan test --compact` with file or filter after MCP or route changes.

---

*Derived from internal scope doc “Truckit Connector for Claude: Feature Scope”, Oct 9, 2026.*
