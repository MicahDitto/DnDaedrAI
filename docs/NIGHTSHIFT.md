# Nightshift Workflow Guide

**📊 For Feature Status:** See [FEATURES.md](./FEATURES.md) for current completion status of all features.

**🎨 For UX Vision:** See [UI_REQUIREMENTS.md](./UI_REQUIREMENTS.md) for the 3-mode system design philosophy.

---

## Overview

Nightshift is an automated development workflow that runs overnight to implement features, fix bugs, and make progress on planned work while you sleep. It discovers tasks from documentation and GitHub issues, implements them in isolated branches, and produces a comprehensive review document for morning approval.

**Key Benefits:**
- 🌙 Unattended development during off-hours
- 🔍 Automatic task discovery from docs and GitHub
- 🌿 Safe branch-based workflow for easy review
- 📝 Comprehensive review documentation
- 🔗 Seamless GitHub Project integration

## Quick Start

### Basic Usage (Simple Version)

The recommended approach is using `bin/nightshift-simple`, which commits directly to main and includes GitHub integration:

```bash
# Run nightshift with default priority (GitHub issues first)
bin/nightshift-simple
```

**Task Prioritization:**

Control which tasks nightshift focuses on using the `--priority` flag:

```bash
# Prioritize GitHub Project #4 issues (default)
bin/nightshift-simple --priority=github

# Prioritize FEATURES.md documentation tasks
bin/nightshift-simple --priority=docs
```

- `--priority=github`: Checks GitHub Project #4 for "Todo" issues first, then falls back to FEATURES.md
- `--priority=docs`: Checks FEATURES.md for 📋 Planned features first, then falls back to GitHub issues

**Running in tmux for Long Sessions:**

For overnight runs, use tmux to keep the session active:

```bash
# Start new tmux session
tmux new -s nightshift

# Inside tmux, run nightshift
bin/nightshift-simple

# Detach from tmux: Ctrl+b, then d
# Reattach later: tmux attach -t nightshift
```

### Advanced Usage (Full Version)

The full branching version creates isolated branches for each task:

Run nightshift with default settings (10 iterations):

```bash
bin/nightshift
```

Run nightshift for 8 hours:

```bash
bin/nightshift --hours 8
```

Run nightshift with specific iteration count:

```bash
bin/nightshift --iterations 20
```

### Prerequisites

- Clean working directory (commit or stash changes first)
- Claude CLI installed and authenticated
- Python 3 available (for full version)
- GitHub CLI (`gh`) installed (optional, for GitHub integration)
- tmux installed (recommended for long-running sessions)

## How Nightshift Works

### 1. Bootstrap Phase

Nightshift discovers tasks from two sources:

**Markdown Documentation:**
- Scans `docs/`, `specs/`, `architecture/`, and other .md files
- Finds TODOs, unchecked tasks, planned features
- Extracts actionable engineering work

**GitHub Project #4:**
- Fetches issues with "Todo" status
- Prioritizes GitHub issues over Markdown tasks
- Creates tasks with GitHub issue metadata

Both sources are merged into a unified task queue at `.git/nightshift/{SESSION_ID}/tasks.json`.

### 2. Task Execution

For each task:
1. Creates a new branch: `nightshift/{SESSION_ID}-{task-slug}` or `nightshift/{SESSION_ID}-issue-{NUMBER}-{slug}`
2. Runs Claude to implement the task
3. Commits changes with message: `nightshift({TASK_ID}): {TASK_TITLE}`
4. Updates task status in `tasks.json`
5. Appends details to the review document
6. Pushes branch to remote (if origin exists)

### 3. Post-Task Actions

**For Completed Tasks:**
- If task has a `github_issue_number`, automatically closes the GitHub issue
- Adds comment to issue with link to branch and review doc

**For Tasks Needing Review:**
- Creates a new GitHub issue with label "design-decision"
- Includes the design question and context
- Links to branch and review doc

### 4. Review Generation

Nightshift produces a comprehensive review document at:
```
.git/nightshift/{SESSION_ID}/nightshift-review.md
```

This includes:
- Summary statistics (completed, needs review, skipped)
- Detailed information about each completed task
- Design questions that need human input
- Branches created and their status
- Recommendations for next steps

## Morning Review Workflow

### Step 1: Read the Review Document

```bash
# Find the latest nightshift session
ls -lt .git/nightshift/

# Open the review doc
cat .git/nightshift/20260317-151500/nightshift-review.md
```

The review document contains:
- ✅ Completed tasks with commit links
- 🔍 Tasks needing review (design questions)
- ⏭️ Skipped tasks with reasons
- 📊 Summary statistics

### Step 2: Review Branches

```bash
# List all branches from the session
git branch --list "nightshift/20260317-*"

# Check out a specific branch to review
git checkout nightshift/20260317-151500-combat-tracker

# Review the changes
git diff main

# Run tests if needed
php artisan test
npm run test
```

### Step 3: Merge Approved Branches

For each branch you want to keep:

```bash
git checkout main
git merge --no-ff nightshift/20260317-151500-combat-tracker
git push origin main
git branch -d nightshift/20260317-151500-combat-tracker
```

Or merge multiple branches:

```bash
git checkout main
git merge --no-ff nightshift/20260317-151500-combat-tracker
git merge --no-ff nightshift/20260317-151500-breadcrumbs
git push origin main
git branch -d nightshift/20260317-151500-combat-tracker
git branch -d nightshift/20260317-151500-breadcrumbs
```

### Step 4: Respond to Design Questions

Design questions are tracked as GitHub issues with label "design-decision".

1. Check your GitHub issues: https://github.com/MicahDitto/DnDaedrAI/labels/design-decision
2. Read the context and question
3. Respond with your decision in the issue comments
4. Update the branch if needed, or queue it for the next nightshift run

### Step 5: Update FEATURES.md

For completed features, update the status in `docs/FEATURES.md`:

```markdown
| Inline Editing | 📋 | Edit fields directly on Show pages |
```

Changes to:

```markdown
| Inline Editing | ✅ | Edit fields directly on Show pages |
```

### Step 6: Clean Up Session

```bash
# Delete all nightshift branches from this session (after reviewing)
git branch --list "nightshift/20260317-*" | xargs git branch -D

# Or delete individual branches
git branch -D nightshift/20260317-151500-timeline
```

## GitHub Integration

### GitHub Project #4

Nightshift integrates with [GitHub Project #4](https://github.com/users/MicahDitto/projects/4/views/8).

**Task Discovery:**
- Fetches issues with status "Todo"
- Converts them to nightshift tasks
- Priority controlled by `--priority` flag (default: GitHub first)

**Task Prioritization:**

Use the `--priority` flag to control task selection:

```bash
# Default: Check GitHub first, then FEATURES.md
bin/nightshift-simple --priority=github

# Alternative: Check FEATURES.md first, then GitHub
bin/nightshift-simple --priority=docs
```

**Priority Behavior:**
- `--priority=github`: "I want to focus on bugs and user-reported issues from GitHub"
- `--priority=docs`: "I want to focus on planned feature development from documentation"

Both modes check both sources - the flag just controls which is prioritized.

**Issue Closing:**
- When a task with a GitHub issue reference completes, the issue is automatically closed
- If commit message includes `Fixes #123` or `Closes #123`, that issue is closed
- A comment is added with commit details and session ID

### Branch Naming

**For Markdown Tasks:**
```
nightshift/{SESSION_ID}-{task-slug}
```

**For GitHub Issues:**
```
nightshift/{SESSION_ID}-issue-{NUMBER}-{task-slug}
```

Example: `nightshift/20260317-151500-issue-123-combat-tracker`

### Task Schema

Each task in `tasks.json` has this structure:

```json
{
  "id": "NS-001",
  "title": "Add combat tracker UI",
  "source_file": "github:issue/123",
  "source_context": "GitHub Issue #123 from Project #4",
  "implementation_hint": "See GitHub issue for details",
  "branch_slug": "combat-tracker",
  "status": "completed",
  "design_questions": [],
  "github_issue_number": 123,
  "github_project_item_id": null,
  "github_source": "project:4"
}
```

**Task Statuses:**
- `todo` - Not yet started
- `completed` - Successfully implemented
- `needs_review` - Blocked on design question
- `skipped` - Not actionable from documentation

## Advanced Usage

### Time-Limited Runs

Run nightshift for a specific duration:

```bash
# Run for 8 hours
bin/nightshift --hours 8

# Run for 4 hours
bin/nightshift --hours 4
```

Nightshift will check elapsed time before each iteration and stop when the limit is reached.

### Iteration-Limited Runs

Run nightshift for a specific number of tasks:

```bash
# Process up to 20 tasks
bin/nightshift --iterations 20

# Process 5 tasks
bin/nightshift --iterations 5
```

### Offline Mode

If GitHub CLI is not available or you don't have network access, nightshift automatically falls back to Markdown-only task discovery.

### Simple vs. Full Version

**bin/nightshift-simple** (Recommended):
- ✅ Commits directly to `main` (immediate integration)
- ✅ Produces comprehensive review documents
- ✅ Integrates with GitHub Project #4
- ✅ Supports task prioritization (`--priority` flag)
- ✅ Runs for 8 hours by default
- ❌ No isolated branches (changes go directly to main)

**bin/nightshift** (Full Version):
- ✅ Creates isolated branches for each task
- ✅ Supports manual review before merging
- ✅ Iteration or time-limited runs
- ❌ More complex workflow
- ❌ Requires Python 3 for task extraction

**When to use which:**
- Use `bin/nightshift-simple` for rapid iteration and immediate progress (most common)
- Use `bin/nightshift` when you want to review each change in isolation before merging

## Troubleshooting

### "Working directory is dirty"

Nightshift requires a clean working directory. Commit or stash changes first:

```bash
git add .
git commit -m "WIP: saving progress before nightshift"
bin/nightshift --hours 8
```

### "Claude CLI is not installed"

Install Claude CLI:

```bash
# Follow installation instructions at:
# https://docs.anthropic.com/claude/docs/cli
```

### "No actionable tasks found"

This means nightshift didn't find any TODOs or planned work in the documentation.

**Solutions:**
1. Add tasks to `docs/FEATURES.md` with status 📋 Planned
2. Create GitHub issues in Project #4 with status "Todo"
3. Add TODO comments or unchecked task lists to documentation

### GitHub Integration Not Working

If GitHub features aren't working:

1. Check if `gh` CLI is installed: `gh --version`
2. Authenticate: `gh auth login`
3. Verify access to Project #4: `gh project list --owner "@me"`

Nightshift will continue to work without GitHub, using only Markdown tasks.

### Task Extraction Issues

If tasks aren't being extracted correctly:

1. Check `.git/nightshift/{SESSION_ID}/tasks.json` to see what was discovered
2. Ensure documentation has clear, actionable TODOs
3. Use the format that works best:
   ```markdown
   ## Planned Features

   - [ ] Add combat tracker UI
   - [ ] Implement timeline view
   - [ ] Create NPC generator
   ```

## Session Directory Structure

Each nightshift run creates a session directory:

```
.git/nightshift/{SESSION_ID}/
├── tasks.json              # Task queue with status tracking
├── nightshift-review.md    # Human-readable review document
└── status.log             # Task execution log
```

**Example:**
```
.git/nightshift/20260317-151500/
├── tasks.json
├── nightshift-review.md
└── status.log
```

## Best Practices

### Before Running Nightshift

1. ✅ Commit or stash all changes
2. ✅ Pull latest changes: `git pull origin main`
3. ✅ Review `docs/FEATURES.md` for planned work
4. ✅ Check GitHub Project #4 for open issues
5. ✅ Decide on duration (`--hours 8` for overnight runs)

### During Morning Review

1. ✅ Read the full review document first
2. ✅ Review branches in order of confidence
3. ✅ Test critical changes before merging
4. ✅ Respond to design questions promptly
5. ✅ Update FEATURES.md to reflect completed work

### Maintaining Documentation

1. ✅ Keep FEATURES.md up to date with status markers
2. ✅ Link GitHub issues to FEATURES.md entries
3. ✅ Add implementation hints to help nightshift
4. ✅ Use clear, actionable language in TODOs

### GitHub Workflow

1. ✅ Use GitHub Project #4 for high-priority work
2. ✅ Set status to "Todo" for issues ready to implement
3. ✅ Respond to design-decision issues quickly
4. ✅ Close issues manually if nightshift didn't auto-close

## Examples

### Example 1: Focus on GitHub Issues (Default)

**Scenario:** You have bug reports in GitHub Project #4 that need fixing.

```bash
# Start tmux session
tmux new -s nightshift

# Run nightshift with GitHub priority (default)
bin/nightshift-simple --priority=github

# Detach: Ctrl+b, then d
```

**What happens:**
1. Nightshift checks GitHub Project #4 for "Todo" issues
2. Finds issue #145: "Loading states are inconsistent"
3. Implements the fix and commits to main
4. Automatically closes issue #145 with comment
5. Generates review doc at `.git/nightshift/{SESSION_ID}/nightshift-review.md`

### Example 2: Focus on Feature Development

**Scenario:** You want to work through planned features in FEATURES.md.

```bash
# Start tmux session
tmux new -s nightshift

# Run nightshift with docs priority
bin/nightshift-simple --priority=docs

# Detach: Ctrl+b, then d
```

**What happens:**
1. Nightshift checks FEATURES.md for items marked 📋 Planned
2. Finds "Relationship Graph" visualization feature
3. Implements the feature and commits to main
4. Generates review doc showing the completed work
5. Falls back to GitHub issues if no doc tasks available

### Example 3: Overnight Run with Morning Review

**Evening:**
```bash
tmux new -s nightshift
bin/nightshift-simple
# Ctrl+b, then d to detach
```

**Morning:**
```bash
# Reattach to see if it's still running
tmux attach -t nightshift

# If finished, read the review
cat .git/nightshift/20260318-220000/nightshift-review.md

# Check what was committed
git log --oneline --since="last night"

# Update FEATURES.md status markers
# Close the tmux session
tmux kill-session -t nightshift
```

### Example Task Flow (Full Version)

1. **Create GitHub Issue:**
   - Title: "Add combat tracker to Play mode"
   - Status: "Todo" in Project #4

2. **Nightshift Discovers Task:**
   - Fetches from GitHub Project #4
   - Creates task NS-001 in tasks.json

3. **Nightshift Implements:**
   - Creates branch: `nightshift/20260317-151500-issue-123-combat-tracker`
   - Implements combat tracker UI
   - Commits changes
   - Marks task as completed
   - Closes GitHub issue #123

4. **Morning Review:**
   - Read review doc
   - Checkout branch and test
   - Merge to main: `git merge --no-ff nightshift/20260317-151500-issue-123-combat-tracker`
   - Update FEATURES.md

## Related Tools

### bin/sync-docs-github

Checks consistency between GitHub and FEATURES.md:

```bash
bin/sync-docs-github
```

See which issues don't have FEATURES.md entries and vice versa.

### .claude/skills/docs-review

Reviews documentation for consistency and completeness, including GitHub integration:

```bash
claude -p "Use /docs-review"
```

## Support

For issues or questions:
1. Check this documentation first
2. Review troubleshooting section
3. Check `.git/nightshift/{SESSION_ID}/status.log` for errors
4. Create a GitHub issue with label "nightshift"

## Changelog

| Date | Version | Changes |
|------|---------|---------|
| 2026-03-17 | 2.0 | Added GitHub Project integration, enhanced review docs |
| 2026-03-15 | 1.0 | Initial nightshift implementation |
