---
paths:
  - 'resources/template/patches/**'
---

# Patches

## Keep feature patches independently composable
Generate shared-file edits against the template base, rather than a tree containing other selected bundles. Anchor insertions to unchanged unique lines so earlier patches cannot shift them. Preserve stable trailing JSON entries when adding array items, and run the full feature matrix after patch changes.
