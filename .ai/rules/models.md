---
paths:
  - 'app/Models/**'
---

# Models

## Expose computed Sqids as public identifiers
Use HasSqid for public identifiers computed from integer primary keys; do not persist public_id columns or generate NanoIDs. Honor an explicit RouteKey attribute for models that bind by slug. This supersedes the legacy NanoID examples in the project-patterns skill. Keep identifier configuration stable after an app publishes URLs.
