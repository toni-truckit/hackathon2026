---
title: Truckit MCP sandbox monthly budget
date: 2026-10-09
tags:
  - hackathon
  - aws
  - budget
region: ap-southeast-2
currency: USD
status: estimate
---

# Truckit MCP sandbox monthly budget

Estimate for the sandbox in [[aws-infra-tasks]]: one NAT, one `t3.small`, one MySQL `db.t4g.micro`, one Redis `cache.t4g.micro`, and the ALB plus WAF on `claude.truckitmetro.com`. Prices are public on-demand list prices for Asia Pacific (Sydney), checked against the AWS price lists published in September and October 2026. A month is 730 hours. Figures are USD, before tax.

> [!info] What to budget
> **About $149 a month** while the stack is up and idle. **About $153** in a light demo month. **About $175** if traffic, logs, and photo storage pick up. A week left running with no traffic is about **$34**. Destroying it after a two-week hackathon is about **$70**.

## Standing cost

These charges accrue for as long as the resources exist, even with no users.

| Item | Basis | USD / month |
| --- | --- | --- |
| NAT Gateway | $0.059 / hour × 730 | 43.07 |
| Public IPv4 addresses | 3 in use × $0.005 / hour × 730 | 10.95 |
| Application Load Balancer | $0.0252 / hour × 730 | 18.40 |
| EC2 `t3.small` | $0.0264 / hour × 730 | 19.27 |
| EC2 root volume | 30 GB gp3 × $0.096 | 2.88 |
| RDS MySQL `db.t4g.micro` | $0.025 / hour × 730, single-AZ | 18.25 |
| RDS storage | 20 GB gp3 × $0.138 | 2.76 |
| ElastiCache Redis `cache.t4g.micro` | $0.024 / hour × 730, one node | 17.52 |
| WAF | $5 web ACL + 6 rules × $1 | 11.00 |
| Secrets Manager | 8 secrets × $0.40 | 3.20 |
| S3 | photos, ALB logs, and state, small allowance | 1.00 |
| ECR | image storage, small allowance | 0.50 |
| **Standing total** | | **148.80** |

The three public IPv4 addresses are two on the ALB (one per Availability Zone) and one on the NAT. The EC2 instance has no public address.

The six WAF rules are the three AWS managed groups (`CommonRuleSet`, `KnownBadInputsRuleSet`, `AmazonIpReputationList`) and the three rate rules (`/mcp`, `/oauth/token` plus `/oauth/register`, and the catch-all). Bot Control is not enabled, so there is no extra $10 managed-rule fee.

The eight secrets are `APP_KEY`, the two Passport keys, Truckit API credentials, the ML pricing API, the LLM API key, the database password, and the Redis AUTH token.

## Light demo month

Add this on top of the standing cost for a hackathon month with modest tool calls, a few image pulls, and little photo storage.

| Usage | Assumption | USD |
| --- | --- | --- |
| NAT data processing | 20 GB × $0.059 | 1.18 |
| ALB capacity units | about 0.25 LCU on average × $0.008 × 730 | 1.46 |
| WAF requests | 1 million × $0.60 | 0.60 |
| Cross-AZ data | small allowance if MySQL or Redis lands in the other AZ, $0.01 / GB each way | 1.00 |
| **Usage** | | **4.24** |
| **Light month total** | standing + usage | **153.04** |

Internet egress stays inside the account-wide free 100 GB, so it is $0 in this scenario. NAT processing is still charged on traffic the NAT forwards, in both directions.

## Busy month

Same stack, with heavier tool traffic, logs, and photos. Still one instance and one NAT.

| Usage | Assumption | USD |
| --- | --- | --- |
| NAT data processing | 100 GB × $0.059 | 5.90 |
| ALB capacity units | about 1 LCU on average × $0.008 × 730 | 5.84 |
| WAF requests | 10 million × $0.60 | 6.00 |
| CloudWatch Logs | 5 GB past the 5 GB free ingest, × $0.67 | 3.35 |
| S3 photos above the standing allowance | about 100 GB × $0.025 | 2.50 |
| Cross-AZ data | about 20 GB each way × $0.01 | 0.40 |
| **Usage** | | **23.99** |
| **Busy month total** | standing + usage | **172.79** |

Egress to the internet after the free 100 GB is $0.114 / GB for the next 10 TB. That is not in the $173 figure. 50 GB past the free tier would add about $5.70.

## Shorter than a month

WAF, secrets, storage, and the hourly services are all prorated. Divide the standing cost by 730 hours.

| Left running | Standing cost |
| --- | --- |
| 1 day | about $4.90 |
| 1 week | about $34 |
| 2 weeks | about $69 |
| Full month | $149 |

> [!tip] Turn it off
> The bill is almost all idle capacity. `terraform destroy` on the sandbox when the hackathon is over is the main saving. A weekend of about 48 hours is about $10 of standing cost.

## Included at no extra charge

- ACM certificate for `claude.truckitmetro.com`.
- The Route 53 alias record. The `truckitmetro.com` hosted zone already exists, so its $0.50 monthly fee is not a new cost. Alias queries to the ALB are free.
- Standard SSM parameters.
- RDS backup storage up to the 20 GB provisioned size.
- The first 10 standard-resolution CloudWatch alarms. This design stays inside that.
- The first 5 GB of CloudWatch Logs ingest and storage.
- The first 100 GB of data transfer out to the internet, aggregated across the whole account. If other workloads already use that 100 GB, this stack pays $0.114 / GB.
- Baseline gp3 IOPS and throughput. No extra IOPS are provisioned.
- An attached NAT address is not an extra idle-EIP charge on top of the in-use IPv4 rate above.

## Not in this budget

- The Anthropic API, if the LLM key is used. That invoice is outside AWS.
- Truckit API and the ML pricing API.
- Tax.
- A second NAT, a larger instance, Multi-AZ RDS, or a Redis replica. Those are prod choices and are not in this sandbox.
- Raising the WAF body-inspection size above the default. The default request price is $0.60 per million. Inspecting 32 KB bodies adds $0.30 per million, and 64 KB adds $0.90 per million.
- New-account credits. Do not plan on the free tier covering `t3.small`, the NAT, or the ALB.

## Where the money goes

NAT plus the public IPv4 addresses are about **$54**, a third of the idle bill. The ALB is about **$18** before traffic. The three compute pieces (EC2, MySQL, Redis) plus their disks are about **$61**. WAF and secrets are about **$14**.

> [!warning] Price list dates
> NAT, ALB, EC2, gp3, and IPv4 rates are from the EC2 and VPC price lists (effective dates in September–October 2026). RDS MySQL `db.t4g.micro` and gp3 storage are from the RDS list published 6 Oct 2026. Redis `cache.t4g.micro` is from the ElastiCache list published 14 Sep 2026. WAF, Secrets Manager, S3, ECR, CloudWatch, and data transfer are from the matching regional lists. Re-check the [AWS Pricing Calculator](https://calculator.aws) for Sydney before a long-lived deploy.
