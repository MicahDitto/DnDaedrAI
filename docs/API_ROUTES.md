# API Routes

All routes are defined in `routes/web.php`. Most routes require authentication via the `auth` middleware.

## Public Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/` | - | Welcome page |

## Authentication Routes

Provided by Laravel Breeze (`routes/auth.php`):

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/login` | login | Show login form |
| POST | `/login` | - | Process login |
| POST | `/logout` | logout | Log out user |
| GET | `/register` | register | Show registration form |
| POST | `/register` | - | Process registration |
| GET | `/forgot-password` | password.request | Show forgot password form |
| POST | `/forgot-password` | password.email | Send reset link |
| GET | `/reset-password/{token}` | password.reset | Show reset form |
| POST | `/reset-password` | password.store | Process password reset |
| GET | `/verify-email` | verification.notice | Email verification notice |
| GET | `/verify-email/{id}/{hash}` | verification.verify | Verify email |
| POST | `/email/verification-notification` | verification.send | Resend verification |
| GET | `/confirm-password` | password.confirm | Show confirm password form |
| POST | `/confirm-password` | - | Process password confirmation |
| PUT | `/password` | password.update | Update password |

---

## Dashboard

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/dashboard` | dashboard | Redirects to campaigns.index |

---

## Profile Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/profile` | profile.edit | Edit profile page |
| PATCH | `/profile` | profile.update | Update profile |
| DELETE | `/profile` | profile.destroy | Delete account |

---

## Settings Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/settings` | settings.index | Settings page |
| PUT | `/settings/provider` | settings.provider | Update AI provider |
| PUT | `/settings/api-key` | settings.api-key | Save API key |
| DELETE | `/settings/api-key` | settings.api-key.remove | Remove API key |
| PUT | `/settings/preferences` | settings.preferences | Update AI preferences |
| POST | `/settings/test-api-key` | settings.test-api-key | Test API key validity |

---

## Campaign Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns` | campaigns.index | List all campaigns |
| GET | `/campaigns/create` | campaigns.create | Create campaign form |
| POST | `/campaigns` | campaigns.store | Store new campaign |
| GET | `/campaigns/{slug}` | campaigns.show | View campaign dashboard |
| GET | `/campaigns/{slug}/edit` | campaigns.edit | Edit campaign form |
| PUT | `/campaigns/{slug}` | campaigns.update | Update campaign |
| DELETE | `/campaigns/{slug}` | campaigns.destroy | Delete campaign |

---

## Search Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/search` | campaigns.search | Global search within campaign |

**Query Parameters:**
- `q` (string) - Search query

**Returns:** JSON with grouped results by type (characters, places, items, factions, plots, sessions)

---

## Edge/Relationship Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/edges/types` | campaigns.edges.types | Get all relationship types |
| GET | `/campaigns/{campaignSlug}/edges/{nodeId}` | campaigns.edges.index | Get node's relationships |
| GET | `/campaigns/{campaignSlug}/edges/{nodeId}/targets` | campaigns.edges.targets | Get available target nodes |
| POST | `/campaigns/{campaignSlug}/edges` | campaigns.edges.store | Create relationship |
| PUT | `/campaigns/{campaignSlug}/edges/{edgeId}` | campaigns.edges.update | Update relationship |
| DELETE | `/campaigns/{campaignSlug}/edges/{edgeId}` | campaigns.edges.destroy | Delete relationship |

**POST /edges Request Body:**
```json
{
    "source_node_id": "uuid",
    "target_node_id": "uuid",
    "type": "friends_with",
    "label": "Best friends",
    "strength": 8,
    "is_secret": false,
    "bidirectional": true
}
```

---

## Character Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/characters` | campaigns.characters.index | List characters |
| GET | `/campaigns/{campaignSlug}/characters/create` | campaigns.characters.create | Create character form |
| POST | `/campaigns/{campaignSlug}/characters` | campaigns.characters.store | Store character |
| GET | `/campaigns/{campaignSlug}/characters/{nodeSlug}` | campaigns.characters.show | View character |
| GET | `/campaigns/{campaignSlug}/characters/{nodeSlug}/edit` | campaigns.characters.edit | Edit character form |
| PUT | `/campaigns/{campaignSlug}/characters/{nodeSlug}` | campaigns.characters.update | Update character |
| DELETE | `/campaigns/{campaignSlug}/characters/{nodeSlug}` | campaigns.characters.destroy | Delete character |

**Character Subtypes:** pc, npc, villain, ally, neutral

**Content Fields:** appearance, personality, motivation, secrets, voice_notes

---

## Place Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/places` | campaigns.places.index | List places |
| GET | `/campaigns/{campaignSlug}/places/create` | campaigns.places.create | Create place form |
| POST | `/campaigns/{campaignSlug}/places` | campaigns.places.store | Store place |
| GET | `/campaigns/{campaignSlug}/places/{nodeSlug}` | campaigns.places.show | View place |
| GET | `/campaigns/{campaignSlug}/places/{nodeSlug}/edit` | campaigns.places.edit | Edit place form |
| PUT | `/campaigns/{campaignSlug}/places/{nodeSlug}` | campaigns.places.update | Update place |
| DELETE | `/campaigns/{campaignSlug}/places/{nodeSlug}` | campaigns.places.destroy | Delete place |

**Place Subtypes:** city, town, village, dungeon, wilderness, building, region

**Content Fields:** description, atmosphere, history, notable_features, secrets

---

## Item Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/items` | campaigns.items.index | List items |
| GET | `/campaigns/{campaignSlug}/items/create` | campaigns.items.create | Create item form |
| POST | `/campaigns/{campaignSlug}/items` | campaigns.items.store | Store item |
| GET | `/campaigns/{campaignSlug}/items/{nodeSlug}` | campaigns.items.show | View item |
| GET | `/campaigns/{campaignSlug}/items/{nodeSlug}/edit` | campaigns.items.edit | Edit item form |
| PUT | `/campaigns/{campaignSlug}/items/{nodeSlug}` | campaigns.items.update | Update item |
| DELETE | `/campaigns/{campaignSlug}/items/{nodeSlug}` | campaigns.items.destroy | Delete item |

**Item Subtypes:** weapon, armor, artifact, consumable, treasure, mundane

**Content Fields:** description, properties, history, secrets

---

## Faction Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/factions` | campaigns.factions.index | List factions |
| GET | `/campaigns/{campaignSlug}/factions/create` | campaigns.factions.create | Create faction form |
| POST | `/campaigns/{campaignSlug}/factions` | campaigns.factions.store | Store faction |
| GET | `/campaigns/{campaignSlug}/factions/{nodeSlug}` | campaigns.factions.show | View faction |
| GET | `/campaigns/{campaignSlug}/factions/{nodeSlug}/edit` | campaigns.factions.edit | Edit faction form |
| PUT | `/campaigns/{campaignSlug}/factions/{nodeSlug}` | campaigns.factions.update | Update faction |
| DELETE | `/campaigns/{campaignSlug}/factions/{nodeSlug}` | campaigns.factions.destroy | Delete faction |

**Faction Subtypes:** guild, government, cult, military, merchant, criminal

**Content Fields:** description, goals, methods, resources, history, secrets

---

## Plot Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/plots` | campaigns.plots.index | List plots |
| GET | `/campaigns/{campaignSlug}/plots/create` | campaigns.plots.create | Create plot form |
| POST | `/campaigns/{campaignSlug}/plots` | campaigns.plots.store | Store plot |
| GET | `/campaigns/{campaignSlug}/plots/{nodeSlug}` | campaigns.plots.show | View plot |
| GET | `/campaigns/{campaignSlug}/plots/{nodeSlug}/edit` | campaigns.plots.edit | Edit plot form |
| PUT | `/campaigns/{campaignSlug}/plots/{nodeSlug}` | campaigns.plots.update | Update plot |
| DELETE | `/campaigns/{campaignSlug}/plots/{nodeSlug}` | campaigns.plots.destroy | Delete plot |

**Plot Subtypes:** main, side, character, faction

**Content Fields:** description, objectives, stakes, progress, secrets

---

## Session 0 Questionnaire Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/setup` | campaigns.setup | Show questionnaire |
| POST | `/campaigns/{campaignSlug}/setup` | campaigns.setup.store | Save questionnaire answers |
| POST | `/campaigns/{campaignSlug}/setup/complete` | campaigns.setup.complete | Mark setup as complete |

---

## Game Session Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/campaigns/{campaignSlug}/sessions` | campaigns.sessions.index | List sessions |
| GET | `/campaigns/{campaignSlug}/sessions/create` | campaigns.sessions.create | Create session form |
| POST | `/campaigns/{campaignSlug}/sessions` | campaigns.sessions.store | Store session |
| GET | `/campaigns/{campaignSlug}/sessions/{number}` | campaigns.sessions.show | View session |
| GET | `/campaigns/{campaignSlug}/sessions/{number}/edit` | campaigns.sessions.edit | Edit session form |
| PUT | `/campaigns/{campaignSlug}/sessions/{number}` | campaigns.sessions.update | Update session |
| DELETE | `/campaigns/{campaignSlug}/sessions/{number}` | campaigns.sessions.destroy | Delete session |

**Note:** Session routes use `{number}` instead of slug, with a constraint for numeric values.

---

## Common Request/Response Patterns

### Inertia Page Response
Most GET routes return Inertia responses:
```php
return Inertia::render('Characters/Show', [
    'campaign' => $campaign,
    'character' => $character,
]);
```

### Redirect with Flash
Successful mutations redirect with a flash message:
```php
return redirect()
    ->route('campaigns.characters.show', [$campaign->slug, $character->slug])
    ->with('success', 'Character created successfully.');
```

### JSON Response (API endpoints)
Edge and search endpoints return JSON:
```php
return response()->json([
    'edge' => $edge,
    'message' => 'Relationship created successfully.',
], 201);
```

### Validation Errors
Validation failures return errors automatically via Inertia:
```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    // ...
]);
```

---

## Route Parameters

| Parameter | Description | Example |
|-----------|-------------|---------|
| `{slug}` | Campaign URL slug | my-awesome-campaign |
| `{campaignSlug}` | Campaign URL slug (nested) | my-awesome-campaign |
| `{nodeSlug}` | Node URL slug | gandalf-the-grey |
| `{nodeId}` | Node UUID | 550e8400-e29b-41d4-a716-446655440000 |
| `{edgeId}` | Edge ID | 42 |
| `{number}` | Session number | 1, 2, 3... |
