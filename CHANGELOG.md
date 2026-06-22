# Changelog

All notable user-facing changes are documented here. The complete historical changelog is available in `readme.txt`.

## 2.0.6

- Fixed WordPress revision restoration and synchronized restored content with template metadata.
- Hardened placeholder, preview, note-content and diagnostics extension filters against invalid return values.
- Corrected the combined order date/time placeholder and custom placeholder value validation.
- Prevented duplicate email-failure history entries, verified history-table creation and optimized history lookups.
- Added rollback protection to drag-and-drop sorting when a database update fails midway.
- Fixed Visual/Text editor handling for placeholder insertion and test-order previews.
- Corrected zero-value maximum-total conditions and shipping-method instance IDs such as `flat_rate:1`.
- Hardened JSON import/export for malformed values, invalid UTF-8 and failed export encoding.
- Improved duplicate-template redirects and template content limits.
- Corrected multisite user-data cleanup, language-filter cleanup and cache cleanup during uninstall.
- Synchronized all POT, PO and MO catalogs with the current source and rechecked multilingual help and FAQ content.

## 2.0.5

- Prepared a GitHub-ready repository with screenshots, branding assets and project documentation.
- Added issue and pull-request templates.
- Added automated PHP/JavaScript and release-package checks.
- Added reproducible Bash and PowerShell release-build scripts.
- Added security, contribution, translation and developer-hook documentation.

## 2.0.4

- Removed the extra “Sent to customer” label from customer notes inserted into the WooCommerce order-notes list.

## 2.0.3

- Added HPOS-compatible links from the central history page to WooCommerce orders.

## 2.0.2

- Removed inline customer-note warning notices from the order screen.
- Redesigned the order-note meta box and fixed admin-footer overlap.

## 2.0.1

- Expanded built-in help and FAQ in all bundled languages.

## 2.0.0

- Added template conditions.
- Added central note, usage and email-processing history.
- Added test-order previews, personal favorites and recently used templates.
- Added diagnostics and developer extension hooks.
