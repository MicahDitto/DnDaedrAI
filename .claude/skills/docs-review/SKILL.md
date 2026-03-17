---
name: docs-review
description: Product consultant that syncs documentation with codebase reality and helps align docs with your vision for the app.
user-invocable: true
---

# Docs Review

A consultative skill for keeping documentation aligned with both the codebase and your intentions.

## Purpose

This skill helps:
- Audit the codebase to find what's actually implemented
- Auto-update FEATURES.md status markers to reflect reality
- Engage in a design review conversation about priorities
- Capture your preferences and direction for the app
- Ensure nightshift extracts accurate, prioritized tasks

## Workflow

### Phase 1: Reality Check

1. Scan the codebase for implemented features:
   - Check `app/Http/Controllers/` for controller methods
   - Check `resources/js/Pages/` for Vue components
   - Check `routes/web.php` for registered routes
   - Check `database/migrations/` for schema

2. Compare against `docs/FEATURES.md`:
   - Find features marked ❌ that are actually implemented → suggest ✅
   - Find features marked ✅ that are missing code → flag for review
   - Find implemented features not documented → suggest additions

3. Present a summary of discrepancies before making changes.

### Phase 2: Auto-Update (with approval)

After showing the audit results:
1. Ask for approval to update status markers
2. Update FEATURES.md with accurate statuses
3. Add new sections for undocumented features
4. Remove or archive obsolete entries

### Phase 3: GitHub Integration Check

Check consistency with GitHub Project #4:

1. Run the sync tool: `bin/sync-docs-github`
2. Review the output for:
   - GitHub issues not documented in FEATURES.md
   - Planned features without corresponding GitHub issues
3. Suggest creating GitHub issues for planned features
4. Suggest adding FEATURES.md entries for undocumented issues
5. Verify that feature priorities align with GitHub Project status

**GitHub Workflow:**
- Planned features (📋) should have GitHub issues for tracking
- GitHub issues should reference related FEATURES.md entries
- Completed features should have closed GitHub issues
- Design decisions from nightshift create "design-decision" labeled issues

### Phase 4: Design Consultation

Engage in a conversation about the app direction:

**Questions to ask:**
- "Looking at the Planned Features, which areas excite you most?"
- "Are there any features here you've lost interest in?"
- "What's frustrating you about the current state?"
- "What would make your next session more productive?"
- "Should any priorities shift based on recent work?"

**Capture feedback:**
- Update priority levels (High/Medium/Low) based on responses
- Add notes about user preferences
- Flag features for removal if no longer wanted
- Identify features that should be broken into smaller tasks
- Create GitHub issues for high-priority planned features

### Phase 5: Nightshift Alignment

Ensure task extraction will work correctly:
1. Verify placeholder features (❌) have clear, actionable descriptions
2. Check that planned features (📋) are scoped for single sessions
3. Suggest breaking down large features into implementable chunks
4. Confirm the Technical Debt section has concrete tasks
5. Verify GitHub issues have clear descriptions for nightshift to use

## Feature Detection Patterns

### Controllers
Look for standard CRUD patterns:
```
{entity}Index, {entity}Create, {entity}Store, {entity}Show, {entity}Edit, {entity}Update, {entity}Destroy
```

### Vue Pages
Check for page directories:
```
Pages/{Entity}/Index.vue, Create.vue, Show.vue, Edit.vue
```

### Routes
Verify route registration:
```
Route::resource('{entities}', {Entity}Controller::class)
```

## Status Markers Reference

| Marker | Meaning | Detection Rule |
|--------|---------|----------------|
| ✅ | Complete | Has controller + routes + pages |
| 🚧 | Partial | Has some but not all CRUD |
| 📋 | Planned | In roadmap, no code exists |
| ❌ | Placeholder | UI link exists, no functionality |

## Consultation Style

- Be direct but curious
- Ask one question at a time
- Summarize what you heard before moving on
- Don't assume - verify preferences
- Respect "I don't know yet" as a valid answer
- Offer recommendations but don't push

## Integration with Nightshift

After a docs-review session:
1. FEATURES.md should reflect true implementation state
2. All ❌ and 📋 items should be extractable as tasks
3. Priorities should match user's current interests
4. No completed features should appear as pending work
5. GitHub issues should be in sync with FEATURES.md
6. High-priority planned features should have GitHub issues created
7. Run `bin/sync-docs-github` to verify consistency

**See:** [docs/NIGHTSHIFT.md](../../../docs/NIGHTSHIFT.md) for the automated workflow

## Output

At the end of a session, provide:
1. Summary of changes made to docs
2. List of design decisions captured
3. Recommended next features to implement
4. Any questions that need more thought
