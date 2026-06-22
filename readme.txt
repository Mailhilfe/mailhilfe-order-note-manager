=== Mailhilfe Order Note Manager for WooCommerce ===
Contributors: schaum
Tags: woocommerce, order notes, templates, hpos, admin
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 2.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Reusable WooCommerce order note templates with placeholders, categories, preview, note type selection, role permissions and HPOS compatibility.

== Description ==


* Conditional templates by order status, payment method, shipping method, country and order total
* Central note, usage and WooCommerce email processing history
* Personal favorites and recently used templates per administrator
* Test-order preview inside the template editor
* Diagnostics page for WooCommerce, HPOS, email and cache status
* Developer hooks and filters for placeholders, content, conditions, history and diagnostics


Multilingual built-in help is available directly inside the WordPress admin area.

Mailhilfe Order Note Manager for WooCommerce helps shop administrators and shop managers add consistent internal or customer-facing WooCommerce order notes from reusable templates.

Features:

* Create, edit and delete note templates.
* Select templates directly in WooCommerce orders.
* Choose internal note or customer note.
* Automatically add an internal timestamp log when a customer notification is created from a template.
* Organize templates with categories.
* Use many placeholders for order data, customer data, billing/shipping details, totals, items and shop information.
* Preview notes with replaced placeholders before adding them.
* Edit the replaced preview text before saving it as an order note.
* Mark frequently used templates as favorites.
* Search/filter templates directly inside the order screen.
* Sort templates with drag-and-drop in the template list.
* Import and export templates as JSON.
* Install practical demo templates.
* Track a usage counter for each template.
* Role capabilities for managing and using templates.
* HPOS compatible.
* Fully translatable with included POT file and translation files for 20 widely used languages.
* WordPress.org-oriented security: nonces, capability checks, escaping and sanitization.

Available placeholders include:

* Order data: `{order_id}`, `{order_number}`, `{order_status}`, `{order_date}`, `{order_time}`, `{date}`, `{paid_date}`, `{completed_date}`
* Customer data: `{customer}`, `{customer_id}`, `{customer_first_name}`, `{customer_last_name}`, `{customer_note}`
* Billing data: `{billing_email}`, `{billing_phone}`, `{billing_company}`, `{billing_address}`, `{billing_city}`, `{billing_postcode}`, `{billing_country}`
* Shipping data: `{shipping_first_name}`, `{shipping_last_name}`, `{shipping_company}`, `{shipping_address}`, `{shipping_city}`, `{shipping_postcode}`, `{shipping_country}`, `{shipping_method}`
* Payment and totals: `{payment_method}`, `{payment_method_id}`, `{order_total}`, `{order_subtotal}`, `{shipping_total}`, `{discount_total}`, `{tax_total}`, `{currency}`, `{currency_symbol}`
* Items and shop: `{item_count}`, `{items}`, `{site_name}`, `{admin_email}`, `{current_date}`, `{current_time}`, `{current_user}`

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install the ZIP file through the WordPress admin area.
2. Activate the plugin in WordPress.
3. Open the new left admin menu item Mailhilfe Order Notes.
4. Create your first template or install the demo templates from Mailhilfe Order Notes > Template Import/Export.
5. Open a WooCommerce order and select the template in the “Mailhilfe Order Note Manager” box.

== Frequently Asked Questions ==

= What does Mailhilfe Order Note Manager for WooCommerce do? =

Mailhilfe Order Note Manager for WooCommerce lets you create reusable note templates for WooCommerce orders. Instead of typing the same internal notes or customer notes again and again, you can select a prepared template directly inside the order screen, preview it with real order data and add it to the order.

= Where do I manage the templates? =

After activation, open the left admin menu item Mailhilfe Order Notes. There you can create, edit, delete, categorize, favorite and sort your templates. Import/export tools and the plugin help are available as submenu pages.

= Where can I use a template in WooCommerce? =

Open a WooCommerce order in the WordPress admin area. The plugin adds an Mailhilfe Order Note Manager box where you can choose a template, preview the replaced placeholders and add the note to the order.

= What is the difference between an internal note and a customer note? =

An internal note is intended for shop staff only. A customer note is customer-visible in WooCommerce and may trigger a customer note email notification depending on your WooCommerce email settings. Review the editable preview and selected note type carefully before adding a customer note.

= Are customer notes automatically emailed to customers? =

WooCommerce can send email notifications for customer notes. Whether an email is actually sent depends on your WooCommerce email configuration and any other email-related plugins in your shop. Always review the preview before adding a customer note.

= Can I format template texts? =

Yes. Template content can be edited with the WordPress editor. You can use common formatting such as paragraphs, bold text, italic text, lists and links. The plugin sanitizes template content with WordPress-safe HTML handling before saving or importing it.

= Which placeholders are supported? =

The plugin supports placeholders for order data, customer data, billing and shipping data, payment methods, totals, items and shop information. Examples include `{order_number}`, `{customer}`, `{billing_email}`, `{shipping_method}`, `{payment_method}`, `{order_total}`, `{items}`, `{site_name}` and `{current_user}`. A placeholder overview is shown in the template editor.

= Can I see the final note before saving it? =

Yes. The order screen includes a preview that replaces placeholders with data from the current WooCommerce order. This helps you check names, totals, shipping method, payment method and customer-visible text before the note is added.

= What happens if a placeholder has no value? =

If order data is not available, the placeholder is replaced with an empty value or a suitable fallback. For example, a paid date can only be shown if the order has actually been paid.

= Can I organize templates into categories? =

Yes. Templates support categories. Categories are useful when you have many templates, for example shipping, payment, returns, customer service or internal processing.

= What are favorites used for? =

Favorites help you keep frequently used templates easy to find. Favorite templates are highlighted in the admin list and can be shown more prominently in the order selection.

= Is there a search function? =

Yes. You can search or filter templates in the WooCommerce order screen so that staff can quickly find the right note template.

= Can templates be sorted manually? =

Yes. The template list supports drag-and-drop sorting. The order is saved by AJAX and protected with nonce and capability checks.

= Can I import and export templates? =

Yes. Open Mailhilfe Order Notes > Template Import/Export to export templates as JSON or import a JSON export file. This is useful for backups, staging sites and moving templates to another shop.

= What is included in the JSON export? =

The JSON export contains published templates with title, content, note type, categories, favorite status, sort order and usage counter data.

= Does the plugin include demo templates? =

Yes. The plugin includes practical demo templates. They are installed automatically when no templates exist yet and can also be installed manually from Mailhilfe Order Notes > Template Import/Export. Demo templates are created in the active admin language where supported.

= Are demo templates available in multiple languages? =

Yes. Demo templates are available for all bundled plugin languages. If the site or user locale is a supported variant, the plugin uses the closest matching bundled language.

= Which roles can use the plugin? =

Administrators and shop managers receive the capabilities `manage_mh_order_note_templates` and `use_mh_order_note_templates` on activation. These capabilities control template management and template usage.

= Can I give access to other roles? =

Yes. The plugin uses WordPress capabilities, so additional roles can be granted access with a role/capability management plugin or custom code. The relevant capabilities are `manage_mh_order_note_templates` and `use_mh_order_note_templates`.

= Is the plugin HPOS compatible? =

Yes. The plugin declares compatibility with WooCommerce High-Performance Order Storage and uses WooCommerce order APIs instead of direct order table queries.

= Does the plugin work without WooCommerce? =

The template admin area can exist in WordPress, but the main purpose of the plugin is WooCommerce order notes. WooCommerce is required to use templates inside orders and to add notes to orders.

= Are translations included? =

Yes. The plugin uses the `mailhilfe-order-note-manager` text domain and includes a POT file as well as bundled PO/MO files for 20 widely used languages.

= Why do I still see English texts? =

First check Settings > General > Site Language and your user profile language. If a WordPress.org language pack is installed, WordPress may prefer that language pack. For direct ZIP installations, the plugin also includes bundled translation fallbacks.

= Can I change the wording of translations? =

Yes. You can translate or adjust strings with common WordPress translation tools such as Poedit or Loco Translate. Use the text domain `mailhilfe-order-note-manager`.

= Does the plugin store personal data? =

The plugin stores note templates and template metadata such as favorite status, sort order and usage count. The notes added to orders are stored by WooCommerce as order notes. If your templates include customer-related placeholders, the resulting note may contain personal order data.

= Is the plugin safe for WordPress.org? =

The plugin is built with WordPress.org-oriented security practices, including capability checks, nonces, sanitization, escaping and WooCommerce APIs for order handling.

= Are imported JSON files checked? =

Yes. Import actions are protected with capability and nonce checks. Imported content is sanitized before it is saved. You should still only import JSON files from trusted sources.

= Can I use HTML in templates? =

Yes, but only safe HTML is kept. The plugin uses WordPress-safe sanitization so unsafe scripts or unsupported markup are not stored as template content.

= Does the plugin count how often templates are used? =

Yes. Each time a template is used to add an order note, the usage counter is increased. The admin list shows the usage count so you can identify frequently used templates.

= Can I reset the usage counter? =

There is currently no separate reset button. The counter is stored as template metadata and can be changed by administrators with suitable database or custom admin tools if needed.

= What should I check before using customer notes? =

Check the preview carefully, make sure no internal information is included, and verify your WooCommerce customer note email settings. Customer notes may be visible to customers.

= Why is my note not formatted exactly like the template editor? =

WooCommerce order notes and email templates may render HTML differently depending on your theme, WooCommerce settings and email plugins. The plugin keeps safe formatting, but final display can vary.

= Can I use the plugin on multisite? =

The plugin follows normal WordPress plugin behavior. On multisite, activate and configure it per site where WooCommerce is used. Templates are stored per site.

= How do I uninstall the plugin? =

Deactivate and delete the plugin from the WordPress plugin screen. The included uninstall file removes plugin-specific templates, categories and stored plugin options when the plugin is deleted.

= Can I edit the preview before adding the note? =

Yes. After selecting a template, the preview contains the replaced order data and can be edited before the note is saved. The edited preview is the final note that will be added to the order.

= When is a customer notification recorded? =

When a template is added as a customer note, the plugin also adds an internal log note with date, time, current user and template name. This helps the shop team see when a customer-visible message was created.

= How do template languages work? =

Each template can be assigned to all languages or to a specific bundled language. In multilingual shops the plugin tries to prefer templates matching the order language, user language or site language.

= Can I use custom order or customer meta fields? =

Yes. Advanced placeholders such as `{order_meta:meta_key}` and `{customer_meta:meta_key}` can read custom fields. Sensitive keys containing words such as password, token, secret, session, auth or hash are blocked.

= Can staff accidentally expose private information? =

The plugin cannot know the business meaning of every placeholder. Always review customer notes before saving them, especially when using meta placeholders or customer-specific data.

= What happens when HTML formatting is disabled? =

If the setting for HTML formatting is disabled, formatted template content is converted to safe plain text before the note is stored. This is useful for shops that want very simple notes only.

= How does the import preview protect existing templates? =

The import preview shows how many templates will be created, updated or skipped before the final import is executed. This helps avoid unwanted overwrites.

= Can I duplicate a template? =

Yes. Use the Duplicate action in the template list. The copy is created as a draft and keeps the content, categories, favorite status and note type, while the usage counter starts at zero.

= Can I restrict JSON imports? =

Yes. The settings page can disable JSON imports. Even when enabled, imports require the proper capability, nonce verification and a valid JSON file.

= How are permissions managed? =

Use the permissions page to grant or remove the plugin capabilities for roles. Administrators keep access so the plugin cannot accidentally lock out the site owner.

= Does the plugin support revisions? =

Yes. Template content is mirrored into the WordPress post content so WordPress revisions can track changes when revisions are available.

= What should I do before using a template for real customer messages? =

Create the template, select a test order, check the preview, verify all placeholders and confirm the selected note type before adding the note. For critical messages, use an internal note first as a test.

= Why does the customer note email status matter? =

WooCommerce can send an email when a customer note is added. Check the WooCommerce customer-note email configuration and use the History and Diagnostics pages to review processing information.

= Can I use the plugin in a staging shop? =

Yes. JSON export and import are useful for moving templates between staging and production. Check customer note settings after moving templates because WooCommerce email settings may differ.

= What data is removed during uninstall? =

The uninstall routine removes the plugin's own templates and plugin options. WooCommerce order notes already added to orders belong to WooCommerce order history and are not removed by uninstalling the plugin.

= How should I report a problem? =

Document the WordPress version, WooCommerce version, whether HPOS is enabled, the active language, the selected template and the exact steps that caused the problem.

= How do template conditions work? =

Conditions can restrict a template by order status, payment method ID, shipping method ID, billing country and minimum or maximum order total. Empty condition fields do not restrict the template. All configured conditions must match, and the plugin validates them again on the server before creating a note.

= What does the email processing history prove? =

For customer notes, the plugin records when WooCommerce reports the customer-note email as processed and records technical `wp_mail()` failures. A processed event means WordPress/WooCommerce handed the message to the mail system; it is not proof of final delivery, inbox placement or that the customer read it.

= Where can I view note and email history? =

Open Mailhilfe Order Notes > History. The page shows recent note creation, template use, email processing and email failures with order, template, user, recipient, event type and time when available. The latest 250 entries are displayed.

= How does the test-order preview work? =

Enter a WooCommerce order ID in the test preview area of the template editor. The plugin renders the current editor content, including unsaved changes, with real order data without creating an order note or sending an email. The current user must be allowed to edit that order.

= What is the difference between global and personal favorites? =

Global favorites are shared by all users. Personal favorites belong only to the current WordPress user. The plugin also stores the ten most recently used templates per user and prefers them in the order selector after a note has been added successfully.

= What information is shown on the Diagnostics page? =

The Diagnostics page shows technical values such as WordPress, PHP and WooCommerce versions, HPOS status, customer-note email status, current locale, published-template count, object-cache status and WP_DEBUG. It does not display customer addresses or order-note content.

= Which developer hooks and filters are available? =

The plugin provides extension points for placeholders, placeholder values, allowed meta keys, template results, conditions, preview content, final note content, actions before and after adding a note, history records and diagnostics. The currently documented hook names are listed in the Developer hooks and filters section of this readme.

== Screenshots ==

1. Template editor with language, conditions, placeholders and test-order preview.
2. WooCommerce order integration with search, personal favorites and an editable preview.
3. Central note and email-processing history with direct order links.
4. Plugin settings and role permissions.
5. JSON import preview, export and localized demo-template tools.
6. Diagnostics page with WordPress, WooCommerce, HPOS, email and cache status.

== Changelog ==

= 2.0.5 =
* Prepared a GitHub-ready development repository with documentation, screenshots, contribution guides, security policy, issue templates, automated lint checks and a reproducible release-build script.

= 2.0.4 =
* Removed the extra “Sent to customer” label from customer notes inserted into the WooCommerce order-notes list.

= 2.0.3 =
* Added direct HPOS-compatible links from order IDs on the central history page to the corresponding WooCommerce order.

= 2.0.2 =
* Removed the inline customer-note warning and WooCommerce email-status notices from the order screen.
* Redesigned the order-note meta box with consistent spacing and a scrollable placeholder area.
* Moved the meta box to the main order column and contained the order-screen layout to prevent the admin footer from overlapping fields.
* Removed the obsolete customer-warning setting.

= 2.0.1 =
* Expanded the built-in help and FAQ for all bundled languages.
* Added documentation for conditions, email processing history, central history, test-order previews, personal favorites, diagnostics and developer hooks.
* Removed untranslated English fallback FAQ entries from non-English built-in FAQ sets.



= 2.0.0 =
* Added template conditions.
* Added central note, usage and email processing history.
* Added test-order previews, personal favorites and recently used templates.
* Added diagnostics page and documented developer extension hooks.


= 1.6.8 =
* Fix: Newly created order notes now appear immediately in the WooCommerce order notes box without reloading the order screen.
* Improvement: Customer-note audit entries are inserted immediately together with the customer note.

= 1.6.7 =
* Fixed drag-and-drop template sorting persistence and default admin list ordering.
* Added explicit AJAX error handling and template-cache invalidation after sorting.

= 1.6.6 =
* Removed a duplicate AJAX preview response key.
* Demo and import lookups now include trashed and other registered template statuses, preventing accidental duplicate templates.
* The published-template transient is removed during uninstall.
* Corrected duplicate historical version headings in the changelog.

= 1.6.5 =
* Fixed HPOS order permission checks by using WooCommerce's `edit_shop_orders` capability with the order ID.
* Removed the overly broad `manage_woocommerce` permission fallback.
* Applied the configured default note type to new templates and the initial order selector.
* Removed a duplicated documentation line in the order integration class.

= 1.6.4 =
* Performance: Load order-screen and placeholder classes only on WooCommerce order and template screens.
* Performance: Load plugin action-link helpers only on the Plugins screen.
* Performance: Persistently cache published template IDs and invalidate the cache on template changes.
* Performance: Do not load the admin JavaScript on settings, permissions, help, FAQ or import/export pages when it is not needed.


= 1.6.3 =
* Performance: Help, FAQ and import/export classes are now loaded only when their pages or actions are requested.
* Performance: Reduced PHP parsing and memory use on unrelated WordPress admin screens.

= 1.6.2 =
* Fixed template metadata cache priming to avoid repeated database queries in the order selector.
* Existing draft, private or trashed templates keep their status when updated through JSON import.
* Completed missing translation entries and rebuilt bundled MO files.

= 1.6.1 =
* Fixed language-aware JSON import so templates with the same title in different languages no longer overwrite each other.
* Existing template fields are preserved when optional JSON properties are omitted.
* Reduced AJAX memory usage by not loading large help, FAQ and import/export classes for admin AJAX callbacks.
* Improved JSON upload compatibility on hosts that report JSON files as text/plain while still requiring a valid .json extension and valid JSON content.
* Removed a duplicate demo-locale mapping entry.

= 1.6.0 =
* Performance: Plugin admin classes are no longer loaded on normal frontend requests.
* Performance: Settings are cached for the duration of each request.
* Performance: Published template IDs are cached and invalidated automatically after template changes.
* Performance: Order language detection is performed once per order request.
* Performance: Template favorite sorting no longer performs repeated metadata lookups inside the sort comparator.
* Performance: jQuery UI Sortable is loaded only on the template list screen.
* Performance: Template queries skip pagination counts and unnecessary term caches.


= 1.5.25 =
* Removed database-meta sorting from the usage column to avoid slow admin queries.
* Added server-side enforcement of template-language availability for preview and note creation.

= 1.5.24 =
* Fixed multilingual JSON exports and template lookups being narrowed by third-party language query filters.
* Prevented stale AJAX preview requests from clearing the active request handle.
* Hid customer-email status notices until Customer note is selected.
* Hardened drag-and-drop sorting against duplicate and oversized ID payloads.

= 1.5.23 =
* Preserved existing usage counters when JSON imports do not explicitly import a valid counter value.
* Preserved paragraph and line breaks when HTML formatting is disabled.
* Fixed custom meta placeholders for valid keys containing dots or colons.
* Loaded all templates before applying the plugin's own order-language filter, improving multilingual compatibility.

= 1.5.22 =
* Hardened plain-text preview and note sanitization so encoded markup cannot be restored after tag removal.
* Plain-text previews are now sent to the server as text instead of serialized HTML.

= 1.5.21 =
* Fixed plain-text preview rendering so encoded markup cannot be interpreted as HTML.
* Uninstall now also removes templates in Trash and other registered post statuses.

= 1.5.20 =
* Re-enable note submission after inserting a placeholder into an already submitted preview.
* Harden order meta-box access with an explicit per-order edit permission check.
* Normalize imported boolean and numeric JSON values safely.

= 1.5.19 =
* Fixed uninstall cleanup so templates, categories, plugin options and role capabilities are actually removed as documented.
* Fixed multilingual filtering so templates assigned to unrelated languages are no longer shown when order-language matching is enabled.

= 1.5.18 =
* Prevented early translation loading that could trigger WordPress debug notices.
* Hardened JSON imports against arrays and objects in scalar fields to avoid PHP 8 type errors.
* Added safe handling for array-valued custom metadata placeholders.


= 1.5.17 =
* Recompiled all bundled MO files from the current PO sources.
* Added the missing translated oversized-note error in all bundled languages.
* Corrected the JSON export filename.

= 1.5.16 =
* Show the WooCommerce customer-note email status only when Customer note is selected.
* Prevent accidental duplicate submission of the same successfully added note until the template, note type, or edited content changes.

= 1.5.15 =
* Prevented failed or stale previews from being submitted as order notes.
* Prevented out-of-order AJAX preview responses from replacing the currently selected template preview.
* Placeholder buttons used in the editable order preview are now resolved before the note is saved.
* Disabled drag-and-drop sorting on paginated or filtered template lists to prevent conflicting menu-order values.


= 1.5.14 =
* Fixed placeholder insertion when the browser selection was outside the editable preview.
* Added a server-side size limit for edited order notes.
* Import previews now detect updates by demo key and legacy title consistently.
* Importing an explicit empty category list now correctly removes existing template categories.

= 1.5.13 =
* Prevented automatic page reload after adding a note, so unsaved WooCommerce order changes are not discarded.
* Added an inline success/error message for AJAX note creation.
* Improved HPOS exception handling and corrected outdated customer-note warning documentation.

= 1.5.12 =
* Added WooCommerce availability guards to prevent fatal errors when WooCommerce is unavailable.
* Improved AJAX error handling so auxiliary customer-notification logging cannot cause duplicate note retries.
* Made plain-text previews and edited notes consistent when HTML formatting is disabled.
* Removed obsolete popup-oriented JavaScript text and clarified the customer-note warning setting.

= 1.5.11 =
* Fixed template-language validation so locale values such as de_DE, en_US and fr_FR are stored correctly.
* Normalized older lowercase locale values when templates are edited, displayed or imported.

= 1.5.10 =
* Fixed HTTP response codes on duplicate-template errors.
* Template language is now copied when duplicating a template.
* Corrected the timezone handling of customer-notification audit timestamps.

= 1.5.9 =
* Corrected HTTP 403 responses for protected admin pages and actions.
* The customer-note warning setting is now respected on order screens.
* Rechecked AJAX, order-screen fields, translations, PHP syntax and package structure.

= 1.5.8 =
* Fixed the note-type label association on the order screen.
* Restored the add-note button label after failed AJAX requests.
* Removed an obsolete customer-note confirmation localization string.
* Rechecked AJAX security, order-screen form isolation, PHP syntax, and package integrity.

= 1.5.7 =
* Removed the browser confirmation popup before adding customer notes. The inline customer-note warning and WooCommerce email status remain visible.

= 1.5.6 =
* Prevented the plugin metabox fields from being submitted with the WooCommerce order form.
* Fixed redirects to edit.php when changing or saving an order status.
* Order-note data is now passed only through the secured AJAX request.

= 1.5.5 =
* Fixed adding notes on WooCommerce order screens by replacing an invalid nested form with a secure AJAX action.
* Prevented redirects to edit.php when adding internal or customer notes.

= 1.5.4 =
* Fixed customer-note logging so the internal order note is created reliably.
* Added a persistent last-customer-notification timestamp to the order screen.
* Added an explicit error when an order note cannot be saved.


= 1.5.3 =
* Changed the WordPress.org contributor username to schaum.
* Removed the .wordpress-org development assets directory from the production plugin package.

= 1.5.2 =
* Expanded the built-in FAQ page in all bundled languages.
* Added additional FAQ topics for editable previews, customer notification logs, template languages, custom meta placeholders, privacy checks, HTML handling, import preview, duplicate templates, permissions, revisions, staging use and uninstall behavior.
* Expanded the WordPress.org readme FAQ with the same additional guidance.

= 1.5.1 =
* Updated the built-in multilingual help page for all bundled languages after the plugin rename.
* Added help sections for settings, template language selection, custom meta placeholders, duplicate templates, revisions, permissions, import preview, customer-note email status and the recommended workflow.
* Updated help page titles to consistently use the new Mailhilfe Order Note Manager for WooCommerce name.

= 1.5.0 =
* Renamed the plugin to Mailhilfe Order Note Manager for WooCommerce.
* Updated the plugin slug and text domain to mailhilfe-order-note-manager.
* Updated plugin headers, readme title, bundled language files and internal labels to use the new distinctive name.

= 1.4.1 =
* Added and compiled missing translations for the new settings, permissions and import preview screens.

= 1.4.0 =
* Added a central settings page for default behavior, customer-note safety, HTML handling, demo installation, usage counters and JSON imports.
* Added a permissions page for managing template capabilities by role.
* Added one-click placeholder insertion in the template editor and editable order preview.
* Added generic custom-field placeholders for order meta and customer meta with sensitive-key blocking.
* Added a duplicate action for templates and enabled revisions by storing template content in post revisions.
* Added JSON import preview with create/update/skip summary before applying changes.
* Added multilingual template language matching with WPML/Polylang-aware order language detection.
* Added WooCommerce customer-note email status notice in the order screen.
* Improved WordPress.org presentation text and feature descriptions.

= 1.3.1 =
* Security hardening: Explicitly disabled public queries, REST exposure, query vars and rewrites for the internal template post type and taxonomy.
* Security hardening: Strengthened JSON import validation with upload error checks, WordPress file extension/type verification, JSON depth limiting and category-count limiting.

= 1.3.0 =
* Added an automatic internal order log note for customer notifications created from templates.
* The log records the date, time, user and template used for the customer notification.

= 1.2.9 =
* Made the replaced order note preview editable before adding the note.
* Added server-side handling for edited preview content with WordPress-safe HTML sanitization.

= 1.2.8 =
* Added a built-in multilingual FAQ page for all bundled languages.
* Added an FAQ link on the WordPress plugins page.

= 1.2.7 =
* Updated the readme.txt `Tested up to` value to WordPress 7.0 after compatibility review.


= 1.2.6 =
* Security review: Added explicit per-order edit permission checks before previewing order data or adding order notes.
* Security review: Restricted template use in order screens to published templates.
* Security review: Added JSON import limits for template count and template content length.
* Security review: Removed unused success query parameter from note creation redirect.

= 1.2.5 =
* Removed the author URI header to avoid an additional external author link on the WordPress plugins page.

= 1.2.4 =
* Removed post-meta query usage from demo template lookup to avoid PluginCheck slow query warnings.
* Demo template IDs are now stored in a small option-based lookup map.

= 1.2.2 =
* Expanded the WordPress.org FAQ section with detailed answers about usage, placeholders, customer notes, translations, roles, HPOS, import/export, demo templates and security.

= 1.2.0 =
* Added a built-in multilingual help page for all bundled languages.
* Added a Help link on the WordPress plugins page.
* Added help sections for templates, placeholders, order usage, customer note warnings, JSON import/export, permissions and HPOS.

= 1.1.9 =
* Added many additional placeholders for order data, customer data, billing/shipping addresses, totals, items and shop/admin information.
* Added a structured placeholder overview in the template editor.

= 1.1.8 =
* Added rich text formatting for template content using the WordPress editor.
* Preview and created order notes now keep safe HTML formatting.

* Demo templates are now generated in the active admin language for all bundled locales.
* Added stable demo keys so existing English demo templates can be updated to the current language.


= 1.1.6 =
* Improved bundled translation loading for all included language files and locale variants.

= 1.1.4 =
* Added bundled translation fallback for direct ZIP installations without using the discouraged plugin textdomain loader.

= 1.1.3 =
* Added a visible left admin menu item for Mailhilfe Order Notes.
* Added a Settings link on the WordPress plugins page.
* Moved import/export tools under the plugin menu.

= 1.1.0 =
* Added favorites.
* Added template search/filtering in orders.
* Added drag-and-drop template sorting.
* Added JSON import/export.
* Added demo templates.
* Added customer note warning and confirmation.
* Added usage counter and usage column.

= 1.0.1 =
* Removed the discouraged manual plugin textdomain loader because WordPress.org loads plugin translations automatically since WordPress 4.6.


= 1.0.0 =
* Initial release.

== Developer hooks ==

The plugin provides extension hooks for integrations:

* `mailhilfe_order_note_placeholders`
* `mailhilfe_order_note_placeholder_values`
* `mailhilfe_order_note_allowed_meta_keys`
* `mailhilfe_order_note_template_results`
* `mailhilfe_order_note_conditions_match`
* `mailhilfe_order_note_preview_content`
* `mailhilfe_order_note_content`
* `mailhilfe_order_note_before_add`
* `mailhilfe_order_note_after_add`
* `mailhilfe_order_note_history_recorded`
* `mailhilfe_order_note_diagnostics`
