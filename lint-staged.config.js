/**
 * @type {import('lint-staged').Configuration}
 */
export default {
  '*.{cjs,js,jsx,mjs,ts,tsx}': ['pnpm run fix', 'pnpm run format'],
  '*.{css,json,md}': 'pnpm run format',
  '*.php': 'composer pint',
}
