---
name: design-md-tasks
description: Scans Markdown documentation in the repository, extracts actionable implementation tasks, and helps maintain a local nightshift work queue and review notes.
user-invocable: true
---

# Design MD Tasks

Use repository Markdown documentation as the source of work.

## Purpose

This skill helps:
- scan Markdown docs for actionable work
- extract explicit and implied TODOs
- convert them into a prioritized local task queue
- document design questions that need human follow-up
- support a nightshift workflow where one task is worked at a time

## What to look for

Search Markdown files for:
- TODO / FIXME / NOTE / follow-up sections
- unchecked checklists
- implementation plans
- design specs that clearly imply unfinished work
- architecture docs with missing pieces
- migration plans
- onboarding docs that mention planned work
- ADRs that leave implementation steps open
- roadmap docs with concrete next steps

## What to ignore

Ignore:
- generated docs
- dependency docs
- vendor docs
- Markdown files that are purely historical and contain no actionable next step
- vague wishlist items that do not translate into a code task

## Task extraction rules

When creating tasks:
1. Prefer concrete, code-shaped tasks.
2. De-duplicate overlapping tasks.
3. Keep titles short and actionable.
4. Include the source file and a small source-context summary.
5. Add a brief implementation hint when possible.
6. Prioritize high-confidence tasks first.
7. Limit scope so a single coding session can reasonably attempt one task at a time.

## Design question handling

If a task surfaces a design decision that requires human input:
1. Do not block the entire session.
2. Record the question in the designated review document.
3. Include:
   - task id
   - task title
   - related branch
   - the exact decision needed
   - why it matters
   - recommended options
   - your preferred recommendation
4. Mark the task as needing review if the decision materially blocks safe completion.
5. Move on cleanly after documenting the issue.

## Working style

- Be conservative and practical.
- Prefer safe, scoped changes over ambitious rewrites.
- Use the docs as the source of truth.
- If code conflicts with docs, note the mismatch clearly.
- When asked to work a single task, do not pick up additional work.