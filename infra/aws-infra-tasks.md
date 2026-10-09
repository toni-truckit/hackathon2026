---
title: Truckit MCP Server – AWS Infra Tasks
date: 2026-10-09
tags:
  - hackathon
  - mcp
  - terraform
  - aws
status: todo
---

# Truckit MCP Server – AWS Infra Tasks (Terraform)

Infra for the Laravel MCP server (`laravel/mcp` + Passport OAuth) on EC2, sitting on top of the Truckit APIs. Scope is Claude only (custom connector, later the Claude Connectors Directory). ChatGPT and Copilot are out of scope.

> [!info] Target architecture
> ```
> Claude (custom connector)
>         │  HTTPS (MCP over HTTP from Anthropic servers: POST /mcp, optional streaming)
>         ▼
> Route 53 (mcp.<domain>) ── ACM cert
>         ▼
> WAF ──► ALB (public subnets)
>         ▼
> EC2 ASG (private subnets) ── Docker: nginx + Laravel
>    ├─► RDS MySQL          (Passport clients/tokens, app data)
>    ├─► ElastiCache Redis  (MCP sessions, queue, rate limit, cache)
>    ├─► S3                 (item photos)
>    └─► NAT ─► Truckit APIs / ML pricing API / LLM
> ```

> [!question] Open decisions (resolve before starting)
> - [ ] Sandbox only for the hackathon, or also prod? Sandbox = single AZ, 1 instance, smallest sizes.
> - [ ] Reuse the existing sandbox VPC, or create a new one?
> - [ ] Reuse the existing Truckit RDS, or create a dedicated MCP database?
> - [ ] Domain / hosted zone for `mcp.<domain>`.
> - [ ] Repo host for CI: Bitbucket (follow `loadplanner-laravel`) or Jenkins.
> - [ ] LLM provider for photo-based size estimates: Bedrock or the Anthropic API.

---

## 0. Terraform foundation

- [ ] Create the folder layout:
  ```
  hackathon/infra/
  ├── modules/            # network, edge, compute, data, storage, secrets, observability
  └── envs/
      ├── sandbox/        # main.tf, variables.tf, terraform.tfvars, backend.tf
      └── prod/
  ```
- [ ] Remote state: S3 bucket (versioned, encrypted, public access blocked) + DynamoDB lock table. Bootstrap it separately or create it by hand.
- [ ] Pin the `hashicorp/aws` provider version and set `required_version` for Terraform.
- [ ] `default_tags` on the provider: `Project=truckit-mcp`, `Env`, `Owner`, `ManagedBy=terraform`.
- [ ] Shared variables: `env`, `region` (ap-southeast-2), `domain_name`, `vpc_id` (if reusing), instance sizes.
- [ ] Add `terraform fmt -check`, `validate` and `tflint` / `tfsec` (or `checkov`) to CI.

## 1. Network (`modules/network`)

- [ ] VPC (or a `data` lookup of the existing sandbox VPC).
- [ ] 2× public subnets (ALB, NAT) and 2× private subnets (EC2, RDS, Redis) across two AZs.
- [ ] Internet Gateway and public route table.
- [ ] NAT Gateway (one for sandbox, one per AZ for prod) and private route tables.
- [ ] Security groups:
  - [ ] `alb-sg`: inbound 443 (and 80 for the redirect) from `0.0.0.0/0`.
  - [ ] `app-sg`: inbound 80 from `alb-sg` only (plus SSH from the CI runner / bastion if deploying over SSH).
  - [ ] `rds-sg`: inbound 3306 from `app-sg` only.
  - [ ] `redis-sg`: inbound 6379 from `app-sg` only.
- [ ] (Optional) VPC endpoints for S3, ECR, Secrets Manager and SSM to cut NAT traffic.

## 2. DNS, TLS & edge (`modules/edge`)

- [ ] `data` lookup of the Route 53 hosted zone.
- [ ] ACM certificate for `mcp.<domain>` with DNS validation records.
- [ ] Application Load Balancer in the public subnets.
  - [ ] **Idle timeout ≥ 300s**, so streamed MCP responses aren't cut off.
  - [ ] HTTPS listener (443) with the ACM cert and a modern TLS policy.
  - [ ] HTTP listener (80) that redirects to 443.
  - [ ] Target group (HTTP 80) with a health check on `/up` (Laravel health route).
  - [ ] Access logs to an S3 bucket.
- [ ] Route 53 alias A record `mcp.<domain>` pointing to the ALB.
- [ ] WAF v2 web ACL attached to the ALB:
  - [ ] AWS managed rules: `CommonRuleSet`, `KnownBadInputsRuleSet`, `AmazonIpReputationList`.
  - [ ] Rate-based rule (e.g. 1000 requests / 5 min per IP), with a stricter rule on `/oauth/*`.
  - [ ] Make sure the body-size rules don't block legitimate MCP JSON payloads. Test with real tool calls.

## 3. Compute (`modules/compute`)

- [ ] ECR repository `truckit-mcp` with image scanning on push and a lifecycle policy (keep the last N images).
- [ ] IAM role + instance profile for EC2:
  - [ ] `AmazonSSMManagedInstanceCore` (Session Manager).
  - [ ] ECR pull.
  - [ ] Read the specific Secrets Manager / SSM parameters.
  - [ ] S3 read/write on the photos bucket.
  - [ ] CloudWatch Logs write.
  - [ ] Bedrock invoke (if Bedrock is chosen).
- [ ] Launch template:
  - [ ] Amazon Linux 2023 AMI (`data` lookup via the SSM parameter).
  - [ ] Instance type (sandbox `t3.small`, prod `t3.medium`).
  - [ ] IMDSv2 required, encrypted gp3 root volume.
  - [ ] User data: install Docker + the compose plugin and the CloudWatch agent, log in to ECR, write `.env` from secrets, run `docker compose -f docker-compose.ec2.yml up -d`.
- [ ] Auto Scaling Group in the private subnets, attached to the target group (sandbox min/max 1, prod min 2), ELB health checks.
- [ ] (Prod) Target-tracking scaling policy on CPU or ALB request count.
- [ ] Deploy access, following the chosen CI:
  - [ ] Bitbucket path: SSH key pair or SSM `send-command` for `deploy/deploy.sh`.
  - [ ] Jenkins path: IAM user/role for Jenkins with ECR push + SSM/SSH deploy.

## 4. Data (`modules/data`)

- [ ] RDS MySQL 8 (or a `data` lookup of the existing DB):
  - [ ] DB subnet group in the private subnets.
  - [ ] Parameter group (`utf8mb4`).
  - [ ] Sandbox `db.t4g.micro` single-AZ; prod `db.t4g.small` Multi-AZ.
  - [ ] Encrypted storage, automated backups, deletion protection in prod.
  - [ ] Master password managed by Secrets Manager (`manage_master_user_password = true`).
- [ ] ElastiCache Redis (Valkey/Redis 7):
  - [ ] Subnet group in the private subnets.
  - [ ] Sandbox `cache.t4g.micro` single node; prod replication group with failover.
  - [ ] In-transit encryption and AUTH token stored in Secrets Manager.

## 5. Storage (`modules/storage`)

- [ ] S3 bucket for item photos:
  - [ ] Block all public access, SSE-S3 or KMS encryption.
  - [ ] Lifecycle rule to expire photos after N days.
  - [ ] CORS configuration if photos are uploaded via presigned URLs from the card UI.
- [ ] S3 bucket for ALB access logs (with the bucket policy for the ELB log-delivery principal).
- [ ] (Optional) CloudFront + S3 for the card UI bundle, if it isn't inlined as an MCP resource.

## 6. Secrets & config (`modules/secrets`)

Create the secrets in Terraform with placeholder values. Set the real values outside Terraform so they stay out of state.

- [ ] Laravel `APP_KEY`.
- [ ] Passport private and public keys (OAuth signing).
- [ ] Truckit API credentials (base URL, client ID/secret).
- [ ] ML pricing API URL / key (`truckit-ml-backend-api`).
- [ ] LLM API key (if not using Bedrock).
- [ ] Redis AUTH token.
- [ ] SSM parameters for non-secret config: `APP_URL`, allowed OAuth redirect URIs (Claude callback: `https://claude.ai/api/mcp/auth_callback`), feature flags.

## 7. Observability (`modules/observability`)

- [ ] CloudWatch log groups (`/truckit-mcp/<env>/app`, `/nginx`) with retention (sandbox 14 days, prod 90 days).
- [ ] CloudWatch alarms:
  - [ ] ALB 5xx count.
  - [ ] ALB target p95 response time.
  - [ ] Unhealthy host count > 0.
  - [ ] EC2 CPU high.
  - [ ] RDS CPU, free storage and connections.
  - [ ] Redis memory and evictions.
- [ ] SNS topic for alarm notifications (email / Slack).
- [ ] (Optional) CloudWatch dashboard: requests, tool-call errors, latency.

## 8. Outputs & handoff

- [ ] Terraform outputs: ALB DNS name, MCP URL, ECR repo URL, RDS endpoint, Redis endpoint, photo bucket name, secret ARNs, ASG name.
- [ ] Wire the outputs into the app's `.env` / `deploy/deploy.sh`.
- [ ] Add a `README.md` in `hackathon/infra/` covering the bootstrap steps, `terraform init/plan/apply` per env, and how to set the secret values.

## 9. Validation checklist

- [ ] `https://mcp.<domain>/up` returns 200 through the ALB.
- [ ] `/.well-known/oauth-protected-resource` and `/.well-known/oauth-authorization-server` are reachable publicly.
- [ ] OAuth flow completes from Claude (Settings → Connectors → Add custom connector).
- [ ] A long-running tool call isn't dropped at the ALB (idle-timeout check).
- [ ] WAF doesn't block normal MCP traffic; the rate limit triggers under load.
- [ ] EC2 has no public IP; RDS and Redis are reachable only from `app-sg`.
- [ ] `terraform destroy` on sandbox cleans up fully (no orphaned ENIs, buckets emptied or `force_destroy` set in sandbox).

---

> [!note] App-level items that affect the infra (not Terraform)
> - nginx: `proxy_buffering off` / `fastcgi_buffering off` on `/mcp` if streaming is used.
> - PHP-FPM holds one worker per open stream. Prefer plain JSON responses, or run Laravel Octane.
> - Keep MCP session state in Redis, not in memory, so any instance can serve any request.
> - Don't use Cognito as the OAuth server: it doesn't support the runtime client registration that Claude expects.
