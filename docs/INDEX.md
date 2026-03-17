# Documentation Index

Welcome to the DnDaedrAI documentation. This guide helps you navigate our documentation system and find what you need.

## Quick Navigation

**New to the project?** Start with:
1. [Project README](./README.md) - Project overview and quick start
2. [UI_REQUIREMENTS.md](./UI_REQUIREMENTS.md) - Understand the 3-mode vision
3. [FEATURES.md](./FEATURES.md) - See what's implemented

**Want to contribute?** Check:
1. [FEATURES.md](./FEATURES.md) - Find what needs to be done
2. [NIGHTSHIFT.md](./NIGHTSHIFT.md) - Learn the automated workflow
3. [ARCHITECTURE.md](./ARCHITECTURE.md) - Understand the codebase

**Need technical reference?**
- [ARCHITECTURE.md](./ARCHITECTURE.md) - System design
- [DATA_MODELS.md](./DATA_MODELS.md) - Database schema
- [API_ROUTES.md](./API_ROUTES.md) - API endpoints
- [FRONTEND.md](./FRONTEND.md) - Component patterns

---

## Documentation Hierarchy

### Strategic Vision (Start Here)

#### [UI_REQUIREMENTS.md](./UI_REQUIREMENTS.md)
**Purpose:** Design philosophy and long-term UX vision

**Contains:**
- The 3-mode system (Plan → Prep → Play)
- User stories and workflows
- Component specifications
- Future feature designs

**When to use:**
- Understanding the app's purpose
- Designing new features
- Making UX decisions

**Status tracking:** ❌ None (vision only - see FEATURES.md for status)

---

#### [FEATURES.md](./FEATURES.md)
**Purpose:** Single source of truth for feature implementation status

**Contains:**
- All features with status markers (✅/🚧/📋/❌)
- Implementation details and file locations
- Planned features and priorities
- Technical debt tracking

**When to use:**
- Finding what to work on
- Checking if a feature is complete
- Planning next sprint
- Updating status after finishing work

**Integration:** Syncs with [GitHub Project #4](https://github.com/users/MicahDitto/projects/4/views/8)

---

### Workflow & Automation

#### [NIGHTSHIFT.md](./NIGHTSHIFT.md)
**Purpose:** Automated overnight development workflow

**Contains:**
- How nightshift works
- Task discovery from docs and GitHub
- Morning review workflow
- GitHub integration details
- Troubleshooting guide

**Key Tool:** `bin/sync-docs-github` - Check consistency between GitHub and docs

---

### Technical Reference

| Doc | Purpose |
|-----|---------|
| [ARCHITECTURE.md](./ARCHITECTURE.md) | System design and structure |
| [DATA_MODELS.md](./DATA_MODELS.md) | Database schema and relationships |
| [API_ROUTES.md](./API_ROUTES.md) | Complete API documentation |
| [FRONTEND.md](./FRONTEND.md) | Vue component patterns |
| [DESIGN_SYSTEM.md](./DESIGN_SYSTEM.md) | Visual language and styling |

---

## Documentation Relationships

```
Strategic Layer:
  UI_REQUIREMENTS.md ──► FEATURES.md
  (Vision)                (Status)
          ↓
Workflow Layer:
  NIGHTSHIFT.md ◄──► GitHub Project #4
  (Automation)        (Tasks)
          ↓
Technical Layer:
  ARCHITECTURE → DATA_MODELS → API_ROUTES
  FRONTEND → DESIGN_SYSTEM
```

---

## Update Workflow

### After Implementing a Feature

1. ✅ Commit your code changes
2. ✅ Update [FEATURES.md](./FEATURES.md) status marker
3. ✅ Close related GitHub issue

### After Planning a New Feature

1. ✅ Add vision to [UI_REQUIREMENTS.md](./UI_REQUIREMENTS.md)
2. ✅ Add tracking entry to [FEATURES.md](./FEATURES.md) with 📋 status
3. ✅ (Optional) Create GitHub issue

---

## Common Scenarios

**"What should I work on?"**
→ Check [FEATURES.md](./FEATURES.md) for 📋 Planned items or [GitHub Project #4](https://github.com/users/MicahDitto/projects/4/views/8)

**"How does this feature work?"**
→ [FEATURES.md](./FEATURES.md) for overview → [UI_REQUIREMENTS.md](./UI_REQUIREMENTS.md) for design → Code

**"I want to add a new feature"**
1. Read [UI_REQUIREMENTS.md](./UI_REQUIREMENTS.md)
2. Add entry to [FEATURES.md](./FEATURES.md) with 📋 status
3. Implement
4. Update status to ✅

**"Docs are out of sync"**
→ Run `bin/sync-docs-github` to identify mismatches

---

## Status Markers (from FEATURES.md)

- ✅ Complete - Fully implemented and working
- 🚧 Partial - Basic functionality exists, needs enhancement
- 📋 Planned - In the roadmap, not yet started
- ❌ Placeholder - UI exists but no functionality

**Key Principle:** FEATURES.md is the single source for status. Other docs reference it.

---

## Tools & Scripts

### bin/nightshift
Automated overnight development workflow

```bash
bin/nightshift --hours 8         # Run for 8 hours
bin/nightshift --iterations 10   # Run 10 tasks
```

See: [NIGHTSHIFT.md](./NIGHTSHIFT.md)

### bin/sync-docs-github
Check consistency between FEATURES.md and GitHub Project #4

```bash
bin/sync-docs-github
```

---

## Quick Reference

| I want to... | Doc to Read |
|--------------|-------------|
| Understand the vision | UI_REQUIREMENTS.md |
| Check feature status | FEATURES.md |
| Find work to do | FEATURES.md + GitHub |
| Learn architecture | ARCHITECTURE.md |
| Look up database | DATA_MODELS.md |
| Check API endpoints | API_ROUTES.md |
| Learn components | FRONTEND.md |
| Match design | DESIGN_SYSTEM.md |
| Run automation | NIGHTSHIFT.md |

---

**Last Updated:** 2026-03-17
