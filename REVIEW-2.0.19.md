# Release review 2.0.19

## Scope

- Added Czech (`cs_CZ`) interface translation.
- Added Czech built-in help, FAQ and demo templates.
- Added Czech locale fallback and template-language selection.
- Updated plugin version, Stable tag, catalog metadata and repository documentation.

## Automated checks

Run `bash scripts/check-project.sh` before tagging the release. Verify PHP syntax, JavaScript syntax, version consistency, release exclusions and ZIP integrity.

## Translation checks

- 188 interface source messages translated.
- Czech three-form plural rule configured.
- Placeholder tokens and HTML tags preserved.
- No fuzzy or empty translations.
- Native-speaker review is recommended before importing the catalog into translate.wordpress.org.
