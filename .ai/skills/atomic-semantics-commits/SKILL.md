---
name: atomic-semantics-commits
description: Strict guidelines for generating clean, atomic, and standardized Git commit messages without scopes.
tags:
  - git
  - commits
  - conventional-commits
  - version-control
agrument-hint: "[git, commit, semantic commits, atomic commits]"
when_to_use: Use this skill when you need to generate Git commit messages that are clear, concise, and follow a strict format for better project history management.
---

# atomic-semantics-commits

## Goal

Generate clear, concise, and atomic Git commit messages following a strict, modified implementation of the Conventional Commits specification. Each commit must represent a single logical change to maintain a clean and traceable project history.

## Context
You are an expert version control assistant managing Git history. Your objective is to generate clear, concise, and atomic commit messages following a strict, modified implementation of the Conventional Commits specification. Every logical change must be isolated into its own commit to maintain a clean, readable, and highly traceable project history.

## Rules

### 1. Allowed Types
You must strictly use one of the following prefixes for the commit title. Do not use alternative words (e.g., use `feat`, never `feature`).
- `feat` - New features
- `fix` - Bug fixes
- `improvements` - General enhancements not tied to a specific feature or bug
- `perf` - Performance improvements
- `build` - Build system changes
- `ci` - Continuous Integration modifications
- `refactor` - Code refactoring (no new features or fixes)
- `docs` - Documentation changes
- `test` - Adding or updating tests
- `style` - Code style changes (formatting, missing semi-colons, etc.)
- `chore` - Maintenance tasks, dependency bumps
- `other` - Changes that do not fit above (ignored in changelog)

### 2. Formatting & Syntax constraints
- **Strictly No Scopes:** Never use sub-typing or scopes. Use `feat: `, NOT `feat(ui): ` or `feat(auth): `.
- **Third-Person Tense:** Always use the third-person singular present tense for the action verb (e.g., "adds", "fixes", "updates", "refactors"). Do NOT use "add", "added", or "adding".
- **Title Length Limit:** The subject line (title) must be 72 characters or fewer.
- **Context belongs in the Body:** Never include "for [reason]" or "for [component]" in the title. If you need to explain *why* or *for what*, put it in the commit body.
- **Empty Line Separation:** Always separate the subject line from the body with a single blank line.
- **Optional Body:** Only generate a commit body if additional context, reasoning, or explanation is truly necessary.
- **No Emojis:** Never use emojis in the title or the body. They are not universally supported and violate this project's style.
- **Breaking Changes:** If a commit introduces a breaking change, the body MUST contain the exact phrase `BREAKING CHANGE:` followed by a space and the explanation.

## Examples

### Adding a new feature (context moved to body)
```text
feat: adds sub-navigation
Adds secondary navigation structure to the CustomerResource to enhance 
user navigation experience.
```

### Fixing a bug (context moved to body)
```text
fix: fixes URL generation
Corrects the base URL resolution for customer balance management.
```

### Refactoring code (no new features or fixes)
```text
refactor: refactors hooks folder structure
Moves Hooks folder outside Components folder for better separation of concerns.
```

### Performance improvement (context moved to body)
```text
perf: optimizes query execution time
Improves customer search performance by optimizing database queries and indexing.
```

### Documentation change (no context needed)
```text
docs: adds installation guide
Adds a step-by-step installation guide to the README for new contributors.
```

### Breaking change
```text
feat: adds new authentication method
BREAKING CHANGE: This change replaces the existing authentication system, requiring all clients to update their authentication flow to use the new method.
```

### Simple chore (no body needed)
```text
chore: bumps Laravel to v11.7.0
```

## Antipatterns to Avoid
- Bundling unrelated changes: Committing a bug fix and a new feature together (e.g., `feat: adds login and fixes navbar bug`). Commits must be atomic (one logical change per commit).
- Vague messaging: Using titles like `fix: fixes stuff`, `chore: updates code`, or `other: WIP`. The title must explicitly state what changed.
- Using scopes/sub-types: Generating `feat(auth): adds token validation`. (Must be feat: adds token validation).
- Including "for" in the title: Generating `fix: fixes URL generation for customer balance management`. (The "for..." part belongs in the body).
- Using imperative mood or past tense: Generating `feat: add sub-navigation` or `feat: added sub-navigation`. (Must use "adds").
- Using Emojis: Generating `✨ feat: adds sub-navigation.` (Strictly text only).

## References

[Conventional Commits Specification](https://www.conventionalcommits.org) (Note: Applied with custom overrides for tense and scoping as defined in this skill).