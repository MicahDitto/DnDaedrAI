# Data Models

## Entity Relationship Diagram

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    User     │───┬───│  Campaign   │───┬───│    Node     │
└─────────────┘   │   └─────────────┘   │   └─────────────┘
                  │                      │         │
                  │                      │         │
┌─────────────┐   │   ┌─────────────┐   │   ┌─────────────┐
│ UserSetting │───┘   │ GameSession │───┘   │    Edge     │
└─────────────┘       └─────────────┘       └─────────────┘
                                                   │
                      ┌─────────────┐              │
                      │    Tag      │──────────────┘
                      └─────────────┘

                      ┌─────────────────────┐
                      │QuestionnaireResponse│
                      └─────────────────────┘
```

## Models

### User

The authenticated user account.

**Table:** `users`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Display name |
| email | string | Unique email address |
| email_verified_at | timestamp | Email verification timestamp |
| password | string | Hashed password |
| remember_token | string | Session remember token |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**Relationships:**
- `hasMany` Campaign
- `hasOne` UserSetting

---

### UserSetting

Stores user preferences and encrypted API keys for AI services.

**Table:** `user_settings`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users |
| ai_provider | string | Selected AI provider (openai, anthropic) |
| api_keys | text | Encrypted JSON of API keys |
| ai_preferences | json | AI generation preferences |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**ai_preferences structure:**
```json
{
    "temperature": 0.7,
    "max_tokens": 2000,
    "include_campaign_context": true,
    "include_related_nodes": true
}
```

**Relationships:**
- `belongsTo` User

---

### Campaign

The top-level container for all campaign data.

**Table:** `campaigns`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users |
| name | string | Campaign name |
| slug | string | URL-friendly identifier |
| description | text | Campaign description |
| genre | string | Campaign genre (fantasy, sci-fi, etc.) |
| rule_system | string | Game system (D&D 5e, Pathfinder, etc.) |
| tone_settings | json | Tone and style preferences |
| player_count | integer | Number of players |
| status | string | setup, active, paused, completed |
| settings | json | Miscellaneous settings |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**Relationships:**
- `belongsTo` User
- `hasMany` Node
- `hasMany` Edge
- `hasMany` Tag
- `hasMany` GameSession
- `hasMany` QuestionnaireResponse

**Scoped Relationships:**
- `characters()` - Nodes where type = 'character'
- `places()` - Nodes where type = 'place'
- `items()` - Nodes where type = 'item'
- `factions()` - Nodes where type = 'faction'
- `plots()` - Nodes where type = 'plot'

---

### Node

A polymorphic entity representing any campaign element (character, place, item, faction, plot).

**Table:** `nodes`

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| campaign_id | bigint | Foreign key to campaigns |
| type | string | Entity type (see below) |
| subtype | string | Entity subtype (see below) |
| name | string | Entity name |
| slug | string | URL-friendly identifier |
| summary | text | Brief description |
| content | json | Type-specific content |
| metadata | json | Additional metadata |
| confidence | string | canon, likely, rumor, unknown |
| is_secret | boolean | Hidden from players |
| deleted_at | timestamp | Soft delete timestamp |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**Node Types and Subtypes:**

| Type | Subtypes | Content Fields |
|------|----------|----------------|
| character | pc, npc, villain, ally, neutral | appearance, personality, motivation, secrets, voice_notes |
| place | city, town, village, dungeon, wilderness, building, region | description, atmosphere, history, notable_features, secrets |
| item | weapon, armor, artifact, consumable, treasure, mundane | description, properties, history, secrets |
| faction | guild, government, cult, military, merchant, criminal | description, goals, methods, resources, history, secrets |
| plot | main, side, character, faction | description, objectives, stakes, progress, secrets |

**Relationships:**
- `belongsTo` Campaign
- `belongsToMany` Tag (pivot: node_tag)
- `hasMany` Edge (as source via source_node_id)
- `hasMany` Edge (as target via target_node_id)

**Scopes:**
- `ofType($type)` - Filter by type
- `characters()` - Type = character
- `places()` - Type = place
- `items()` - Type = item
- `factions()` - Type = faction
- `plots()` - Type = plot
- `visible()` - Where is_secret = false

---

### Edge

A directed relationship between two nodes.

**Table:** `edges`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| campaign_id | bigint | Foreign key to campaigns |
| source_node_id | uuid | Foreign key to source node |
| target_node_id | uuid | Foreign key to target node |
| type | string | Relationship type (see below) |
| label | string | Display label |
| strength | integer | Relationship strength (1-10) |
| metadata | json | Additional data |
| is_secret | boolean | Hidden from players |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**Relationship Types:**

| Category | Types |
|----------|-------|
| Social | knows, friends_with, allied_with, enemies_with, rivals_with, related_to, married_to, parent_of, child_of, sibling_of |
| Hierarchy | serves, commands, employs, employed_by, mentors, mentored_by |
| Organization | member_of, leads, founded, headquartered_in |
| Location | located_in, lives_in, rules_over, visited, born_in |
| Possession | owns, owned_by, created, created_by, seeks, guards |
| Plot | involves, involved_in, takes_place_in, caused, affected_by |
| Custom | custom (user-defined label) |

**Relationships:**
- `belongsTo` Campaign
- `belongsTo` Node (as sourceNode)
- `belongsTo` Node (as targetNode)

**Scopes:**
- `ofType($type)` - Filter by type
- `visible()` - Where is_secret = false

---

### Tag

Organizational tags for filtering and grouping nodes.

**Table:** `tags`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| campaign_id | bigint | Foreign key to campaigns |
| name | string | Tag name |
| color | string | Display color |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**Relationships:**
- `belongsTo` Campaign
- `belongsToMany` Node (pivot: node_tag)

---

### GameSession

A record of a game session with planning and recap notes.

**Table:** `game_sessions`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| campaign_id | bigint | Foreign key to campaigns |
| number | integer | Session number |
| title | string | Session title |
| scheduled_at | timestamp | Planned date/time |
| plan | text | DM's session plan |
| notes | text | Notes during session |
| recap | text | Post-session recap |
| outcomes | json | Key outcomes array |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**outcomes structure:**
```json
[
    "Party discovered the hidden temple",
    "Gareth was captured by cultists",
    "Level up to 5"
]
```

**Relationships:**
- `belongsTo` Campaign

---

### QuestionnaireResponse

Stores answers from the Session 0 setup questionnaire.

**Table:** `questionnaire_responses`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| campaign_id | bigint | Foreign key to campaigns |
| question_key | string | Question identifier |
| question_text | string | The question asked |
| response | text | User's answer |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**Common question_key values:**
- genre, world_type, player_count, experience_level
- campaign_length, session_length, play_style
- safety_tools, inspirations, campaign_pitch

**Relationships:**
- `belongsTo` Campaign

---

## Indexes

### nodes
- `campaign_id` - Foreign key lookup
- `type` - Entity type filtering
- `slug` - URL routing

### edges
- `campaign_id` - Foreign key lookup
- `source_node_id` - Relationship queries
- `target_node_id` - Relationship queries

### game_sessions
- `campaign_id` - Foreign key lookup
- `number` - Session ordering

---

## Soft Deletes

Only the `Node` model uses soft deletes (`deleted_at` column). This preserves relationship integrity when entities are deleted.

---

## JSON Field Patterns

The application uses JSON fields extensively for flexibility:

1. **content** (Node) - Type-specific fields that vary by node type
2. **metadata** (Node, Edge) - Extensible data without schema changes
3. **settings** (Campaign) - Campaign-wide preferences
4. **tone_settings** (Campaign) - Style and tone preferences
5. **ai_preferences** (UserSetting) - AI generation settings
6. **outcomes** (GameSession) - Session outcome list

This allows the schema to remain stable while supporting varied content structures.
