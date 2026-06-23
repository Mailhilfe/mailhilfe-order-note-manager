=== Mailhilfe Order Note Manager for WooCommerce ===
Contributors: schaum
Tags: woocommerce, order notes, templates, hpos, admin
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 2.0.19
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


Built-in help is available in English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech directly inside the WordPress admin area.

Mailhilfe Order Note Manager for WooCommerce helps shop administrators and shop managers add consistent internal or customer-facing WooCommerce order notes from reusable templates.

Features:

* Create, edit and delete note templates.
* Select templates directly in WooCommerce orders.
* Choose internal note or customer note.
* Automatically add an internal timestamp log when a customer note is created from a template.
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
* Fully translatable with an included POT file and bundled translations for German, formal German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech.
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

Yes. The plugin uses the `mailhilfe-order-note-manager` text domain and includes a POT file plus bundled PO/MO files for standard German, formal German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech. Other languages should be provided through reviewed WordPress.org language packs.

= Why do I still see English texts? =

First check Settings > General > Site Language and your user profile language. If a WordPress.org language pack is installed, WordPress may prefer that language pack. For direct ZIP installations, the plugin includes bundled fallback translations for standard German, formal German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech.

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

= How are customer-note creation and email processing recorded? =

When a template is added as a customer note, the plugin also adds an internal log note with date, time, current user and template name. This helps the shop team see when a customer-visible message was created.

= How do template languages work? =

Each template can be assigned to all languages or to a specific supported locale. In multilingual shops the plugin tries to prefer templates matching the order language, user language or site language.

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

= 2.0.19 =
* Added a complete Czech (cs_CZ) translation for the interface, built-in help, FAQ and demo templates.
* Added Czech locale fallback handling, template-language selection and a Czech repository guide.
* Updated all release and translation metadata for version 2.0.19.
* Marked the Czech catalog for additional native-speaker review before submission to translate.wordpress.org.

= 2.0.18 =
* Added a complete Vietnamese (vi) translation for the interface, built-in help, FAQ and demo templates.
* Added Vietnamese locale fallback handling and a Vietnamese repository guide.
* Updated all release and translation metadata for version 2.0.18.
* Marked the Vietnamese catalog for additional native-speaker review before submission to translate.wordpress.org.

= 2.0.17 =
* Added a complete Persian (fa_IR) translation for the interface, built-in help, FAQ and demo templates.
* Added Persian locale fallback handling, template-language selection and a Persian repository guide.
* Improved RTL presentation for help lists and technical placeholder examples.
* Updated all release and translation metadata for version 2.0.17.
* Marked the Persian catalog for additional native-speaker review before submission to translate.wordpress.org.

= 2.0.16 =
* Added a complete Turkish (tr_TR) translation for the interface, built-in help, FAQ and demo templates.
* Added Turkish locale fallback handling and a Turkish repository guide.
* Updated all release and translation metadata for version 2.0.16.
* Marked the Turkish catalog for additional native-speaker review before submission to translate.wordpress.org.

= 2.0.15 =
* Added a complete Polish (pl_PL) translation for the interface, built-in help, FAQ and demo templates.
* Added Polish locale fallback handling and a Polish repository guide.
* Updated all release and translation metadata for version 2.0.15.

= 2.0.14 =
* Added a complete Dutch (nl_NL) translation for the interface, built-in help, FAQ and demo templates.
* Added Dutch locale fallback handling and a Dutch template-language option.
* Updated all release and translation metadata for version 2.0.14.

= 2.0.13 =
* Added a complete Japanese (ja) translation for the interface, built-in help, FAQ and demo templates.
* Added Japanese locale fallback handling.
* Updated all release and translation metadata for version 2.0.13.

= 2.0.12 =
* Added a complete Simplified Chinese (zh_CN) translation for the interface, built-in help, FAQ and demo templates.
* Added Simplified Chinese locale fallback handling while keeping Traditional Chinese locales separate.
* Updated all release and translation metadata for version 2.0.12.

= 2.0.11 =
* Added a complete Hindi (hi_IN) translation for the interface, built-in help, FAQ and demo templates.
* Added Hindi locale fallback handling.
* Updated all release and translation metadata for version 2.0.11.

= 2.0.10 =
* Added a complete Italian translation for the interface, built-in help, FAQ and demo templates.
* Added Italian locale fallbacks for variants such as it_CH.
* Updated all release and translation metadata for version 2.0.10.

= 2.0.9 =
* Added a complete Brazilian Portuguese translation for the interface, built-in help, FAQ and demo templates.
* Added Portuguese locale fallbacks for variants such as pt_PT.
* Updated translation documentation and release metadata.

= 2.0.8 =
* Added a complete Russian translation for the interface, built-in help, FAQ and demo templates.
* Added Russian locale fallback handling.
* Updated translation documentation and release metadata.

= 2.0.7 =
* Added a complete French translation for the interface, built-in help, FAQ and demo templates.
* Added French locale fallback handling.
* Updated release metadata and WordPress.org asset deployment configuration.

= 2.0.6 =
* Added complete Spanish and reviewed German translations.
* Added built-in localized help, FAQ and demo templates.
* Added deployment, diagnostics, history, conditions, favorites and developer hooks.

