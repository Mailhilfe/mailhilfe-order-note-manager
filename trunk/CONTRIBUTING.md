# Contributing

Contributions are welcome through focused issues and pull requests.

## Before opening an issue

- Test with the latest stable plugin version.
- Enable `WP_DEBUG` on a staging or local installation.
- Record WordPress, WooCommerce and PHP versions.
- State whether HPOS is enabled.
- Remove personal customer and order data from screenshots and logs.

## Development workflow

1. Fork and clone the repository.
2. Create a feature or fix branch.
3. Keep changes narrowly scoped.
4. Follow WordPress coding and security practices.
5. Run:

```bash
bash scripts/check-project.sh
```

6. Build and inspect the release ZIP:

```bash
bash scripts/build-release.sh
```

7. Submit a pull request with testing notes.

## Coding expectations

- Escape output at render time.
- Sanitize and validate request data.
- Protect state-changing actions with nonces and capability checks.
- Use WooCommerce order APIs instead of direct order-table queries.
- Preserve HPOS compatibility.
- Keep public frontend output disabled unless a feature explicitly requires it.
- Add translatable strings with the `mailhilfe-order-note-manager` text domain.

## Translations

Update the POT source and relevant PO files when adding user-facing strings. See `docs/TRANSLATIONS.md`.

## Changelog

Add a concise entry to `CHANGELOG.md` and `readme.txt` for user-facing changes.
