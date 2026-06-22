# Release checklist

## Code and metadata

- [ ] Plugin header version updated.
- [ ] `MHONT_VERSION` updated.
- [ ] `Stable tag` in `readme.txt` updated.
- [ ] `CHANGELOG.md` and `readme.txt` changelog updated.
- [ ] PHP requirements and tested versions reviewed honestly.

## Validation

- [ ] Run `bash scripts/check-project.sh`.
- [ ] Test on a clean WordPress installation with `WP_DEBUG` enabled.
- [ ] Test classic order storage and HPOS where supported.
- [ ] Test internal notes and customer notes.
- [ ] Verify customer-note email processing history.
- [ ] Test order-status changes with the plugin meta box present.
- [ ] Test JSON import preview, import and export.
- [ ] Test template language and conditions.
- [ ] Test personal favorites and recent templates.
- [ ] Test permissions with a non-administrator role.
- [ ] Test uninstall on a disposable installation.

## Packaging

- [ ] Run `bash scripts/build-release.sh` or the PowerShell equivalent.
- [ ] Inspect the ZIP root folder.
- [ ] Confirm repository-only files are excluded.
- [ ] Confirm no `.wordpress-org`, test, cache or temporary folders are included.
- [ ] Confirm the ZIP installs and activates successfully.

## GitHub

- [ ] Create and push a matching version tag.
- [ ] Create a GitHub release.
- [ ] Attach the installable ZIP from `build/`.
- [ ] Include concise release notes and upgrade notes.
