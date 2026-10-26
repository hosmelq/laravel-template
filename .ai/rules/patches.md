---
paths:
  - 'resources/template/patches/**'
---

# Patches

## Keep feature patches independently composable
Generate shared-file edits against the template base, rather than a tree containing other selected bundles. Anchor insertions to unchanged unique lines so earlier patches cannot shift them. Preserve stable trailing JSON entries when adding array items, and run the full feature matrix after patch changes.

## Put cross-feature code in combination patches
Code that needs two features (for example Inertia pages for Fortify views, or organization routes that render pages) goes in `resources/template/patches/<feature>+<feature>.patch`, with names in alphabetical order. The installer and feature matrix apply combination patches in a second `git apply` pass, after every single-feature patch, because one pass cannot modify files that another patch in the same pass creates. Generate a combination patch against the tree with only its own features applied.
