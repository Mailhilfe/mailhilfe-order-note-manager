<?php
/**
 * Detailed help page in English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders built-in help in English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech.
 */
final class MHONT_Help {

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu' ) );
	}

	/**
	 * Adds help submenu.
	 *
	 * @return void
	 */
	public static function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE,
			self::text( 'page_title' ),
			self::text( 'menu_title' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-help',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Returns help page URL.
	 *
	 * @return string
	 */
	public static function url() {
		return admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE . '&page=mhont-help' );
	}

	/**
	 * Renders the help page.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_die( esc_html( self::text( 'permission_error' ) ), '', array( 'response' => 403 ) );
		}

		$content = self::get_content();
		?>
		<div class="wrap mhont-help-page">
			<h1><?php echo esc_html( $content['title'] ); ?></h1>
			<p class="description"><?php echo esc_html( $content['intro'] ); ?></p>

			<div class="mhont-help-grid">
				<?php foreach ( $content['sections'] as $section ) : ?>
					<div class="mhont-tool-card mhont-help-card">
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
						<?php foreach ( $section['paragraphs'] as $paragraph ) : ?>
							<p><?php echo wp_kses_post( $paragraph ); ?></p>
						<?php endforeach; ?>
						<?php if ( ! empty( $section['items'] ) ) : ?>
							<ul>
								<?php foreach ( $section['items'] as $item ) : ?>
									<li><?php echo wp_kses_post( $item ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Returns a localized single text.
	 *
	 * @param string $key Text key.
	 * @return string
	 */
	private static function text( $key ) {
		$locale = self::get_help_locale();
		$texts  = self::get_texts();
		$set    = isset( $texts[ $locale ] ) ? $texts[ $locale ] : $texts['en_US'];

		return isset( $set[ $key ] ) ? $set[ $key ] : $texts['en_US'][ $key ];
	}

	/**
	 * Returns help content for the current locale.
	 *
	 * @return array
	 */
	private static function get_content() {
		$locale = self::get_help_locale();
		$sets   = self::get_content_sets();

		return isset( $sets[ $locale ] ) ? $sets[ $locale ] : $sets['en_US'];
	}

	/**
	 * Gets the best matching supported locale.
	 *
	 * @return string
	 */
	private static function get_help_locale() {
		$locales = array( determine_locale(), get_user_locale(), get_locale() );

		foreach ( $locales as $locale ) {
			if ( ! is_string( $locale ) || '' === $locale ) {
				continue;
			}

			$normalized = str_replace( '-', '_', $locale );
			$language   = strtolower( strtok( $normalized, '_' ) );
			if ( 'de' === $language && false !== stripos( $normalized, 'formal' ) ) {
				return 'de_DE_formal';
			}
			if ( 'de' === $language ) {
				return 'de_DE';
			}
			if ( 'es' === $language ) {
				return 'es_ES';
			}
			if ( 'fr' === $language ) {
				return 'fr_FR';
			}
			if ( 'it' === $language ) {
				return 'it_IT';
			}
			if ( 'hi' === $language ) {
				return 'hi_IN';
			}
			if ( 'zh' === $language && in_array( strtolower( $normalized ), array( 'zh', 'zh_cn', 'zh_sg', 'zh_hans' ), true ) ) {
				return 'zh_CN';
			}
			if ( 'ja' === $language ) {
				return 'ja';
			}
			if ( 'nl' === $language ) {
				return 'nl_NL';
			}
			if ( 'pl' === $language ) {
				return 'pl_PL';
			}
			if ( 'tr' === $language ) {
				return 'tr_TR';
			}
			if ( 'fa' === $language ) {
				return 'fa_IR';
			}
			if ( 'vi' === $language ) {
				return 'vi';
			}
			if ( 'cs' === $language ) {
				return 'cs_CZ';
			}
			if ( 'ru' === $language ) {
				return 'ru_RU';
			}
			if ( 'pt' === $language ) {
				return 'pt_BR';
			}
		}

		return 'en_US';
	}

	/**
	 * Returns localized menu texts.
	 *
	 * @return array<string,array<string,string>>
	 */
	private static function get_texts() {
		$json = <<<'JSON'
{
  "en_US": {
    "page_title": "Mailhilfe Order Note Manager Help",
    "menu_title": "Help",
    "permission_error": "You are not allowed to manage note templates."
  },
  "de_DE": {
    "page_title": "Hilfe zu Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Hilfe",
    "permission_error": "Du bist nicht berechtigt, Notizvorlagen zu verwalten."
  },
  "de_DE_formal": {
    "page_title": "Hilfe zu Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Hilfe",
    "permission_error": "Sie sind nicht berechtigt, Notizvorlagen zu verwalten."
  },
  "es_ES": {
    "page_title": "Ayuda de Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Ayuda",
    "permission_error": "No tiene permisos para gestionar plantillas de notas."
  },
  "fr_FR": {
    "page_title": "Aide de Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Aide",
    "permission_error": "Vous n’avez pas l’autorisation de gérer les modèles de notes."
  },
  "ru_RU": {
    "page_title": "Справка по Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Справка",
    "permission_error": "У вас нет прав на управление шаблонами примечаний."
  },
  "pt_BR": {
    "page_title": "Ajuda do Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Ajuda",
    "permission_error": "Você não tem permissão para gerenciar modelos de notas."
  },
  "it_IT": {
    "page_title": "Guida di Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Aiuto",
    "permission_error": "Non disponi dei permessi per gestire i modelli di nota."
  },
  "hi_IN": {
    "page_title": "Mailhilfe Order Note Manager for WooCommerce सहायता",
    "menu_title": "सहायता",
    "permission_error": "आपको नोट टेम्पलेट प्रबंधित करने की अनुमति नहीं है।"
  },
  "zh_CN": {
    "page_title": "Mailhilfe 订单备注管理器帮助",
    "menu_title": "帮助",
    "permission_error": "您无权管理备注模板。"
  },
  "ja": {
    "page_title": "Mailhilfe Order Note Manager for WooCommerce ヘルプ",
    "menu_title": "ヘルプ",
    "permission_error": "メモテンプレートを管理する権限がありません。"
  },
  "nl_NL": {
    "page_title": "Hulp voor Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Hulp",
    "permission_error": "Je hebt geen toestemming om notitiesjablonen te beheren."
  },
  "pl_PL": {
    "page_title": "Pomoc dotycząca Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Pomoc",
    "permission_error": "Nie masz uprawnień do zarządzania szablonami notatek."
  },
  "tr_TR": {
    "page_title": "Mailhilfe Order Note Manager for WooCommerce Yardımı",
    "menu_title": "Yardım",
    "permission_error": "Not şablonlarını yönetme yetkiniz yok."
  },
  "fa_IR": {
    "page_title": "راهنمای Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "راهنما",
    "permission_error": "شما اجازه مدیریت الگوهای یادداشت را ندارید."
  },
  "vi": {
    "page_title": "Trợ giúp Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Trợ giúp",
    "permission_error": "Bạn không được phép quản lý mẫu ghi chú."
  },
  "cs_CZ": {
    "page_title": "Nápověda k Mailhilfe Order Note Manager for WooCommerce",
    "menu_title": "Nápověda",
    "permission_error": "Nemáte oprávnění spravovat šablony poznámek."
  }
}
JSON;
		$texts = json_decode( $json, true );
		return is_array( $texts ) ? $texts : array();
	}

	/**
	 * Returns the bundled English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech help content sets.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private static function get_content_sets() {
		$json = <<<'JSON'
{
  "en_US": {
    "title": "Detailed help for Mailhilfe Order Note Manager for WooCommerce",
    "intro": "This updated help explains the complete workflow under the new name Mailhilfe Order Note Manager for WooCommerce: creating and formatting templates, using template languages, placeholders and meta placeholders, editing previews, safely sending customer notes, settings, permissions, import previews and HPOS compatibility.",
    "sections": [
      {
        "title": "1. What the plugin does",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce lets you store frequently used WooCommerce order notes as reusable templates. This avoids typing the same text repeatedly and keeps communication inside the order history consistent.",
          "A template can be prepared as an internal staff note or as a customer note. You can still change the note type when using the template inside an order."
        ],
        "items": [
          "Typical examples: payment reminders, delivery delays, phone call records, address checks and service responses.",
          "Templates support categories, favorites, sorting, a usage counter and JSON backup."
        ]
      },
      {
        "title": "2. Create a new template",
        "paragraphs": [
          "Open <strong>Mailhilfe Order Notes → Add New</strong>. Enter a clear title, write the note text in the editor and choose whether the default note type should be internal or customer-facing.",
          "Use the title as a short description of the purpose, for example “Payment reminder” or “Customer called about delivery”. This makes the template easier to find in the order screen."
        ],
        "items": [
          "Assign one or more categories when you have many templates.",
          "Mark often used templates as favorites.",
          "Publish the template so it becomes available in orders."
        ]
      },
      {
        "title": "3. Formatting template text",
        "paragraphs": [
          "The template text uses the WordPress editor. You can format text with paragraphs, bold and italic text, lists and links. Formatting is kept when the note is created, but the content is cleaned with WordPress-safe HTML rules.",
          "Use formatting carefully in customer notes. A short paragraph or bullet list is usually easier to read than a long unstructured text."
        ],
        "items": [
          "Good example: a short greeting, one clear explanation and one next step.",
          "Avoid internal abbreviations in customer notes.",
          "Do not insert private staff comments into templates that may be used as customer notes."
        ]
      },
      {
        "title": "4. Placeholders",
        "paragraphs": [
          "Placeholders are words in curly brackets. They are replaced with real order data in the preview and when the note is added to the order.",
          "You can combine normal text and placeholders. Example: <code>Hello {customer}, we have received your order {order_number}.</code>"
        ],
        "items": [
          "Order: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Customer: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Shipping and payment: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Items and shop: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Preview before adding a note",
        "paragraphs": [
          "Open a WooCommerce order and select a template. The preview shows the note with placeholders already replaced by the selected order data.",
          "Always check the preview before creating the note. This is especially important when a placeholder has no value in the order, for example when a shipping company or phone number is missing."
        ],
        "items": [
          "Check names, totals, shipping method and item list.",
          "Check whether the selected note type is correct.",
          "Edit the template first if the same text should be improved for all future orders."
        ]
      },
      {
        "title": "6. Internal notes and customer notes",
        "paragraphs": [
          "Internal notes are meant for shop staff and are normally used for documentation, follow-up tasks or service history. Customer notes may be visible to the customer and may trigger WooCommerce email notifications depending on your WooCommerce settings.",
          "Review the editable preview and selected note type carefully. Use customer notes only for text that the customer is allowed to read."
        ],
        "items": [
          "Internal note: “Customer called, delivery address confirmed.”",
          "Customer note: “Your order is being prepared and will be shipped shortly.”",
          "Never put passwords, private comments or supplier-only information in customer notes."
        ]
      },
      {
        "title": "7. Favorites, search and sorting",
        "paragraphs": [
          "Favorites help place the most important templates at the top of the selection. The search field in the order screen helps you find a template by title, category or content.",
          "In the template list you can change the order by drag and drop. The saved order is used when templates are displayed in the order screen."
        ],
        "items": [
          "Use favorites for daily templates.",
          "Use categories for topic groups such as Payment, Shipping, Returns and Support.",
          "Keep titles short so search results remain readable."
        ]
      },
      {
        "title": "8. Import, export and demo templates",
        "paragraphs": [
          "The JSON export creates a backup of your templates. You can use it before larger changes or to transfer templates to another shop.",
          "The JSON import can create templates and update existing templates with the same title or internal demo key. Demo templates provide a quick starting point and are created in the active language."
        ],
        "items": [
          "Export before bulk changes.",
          "Import only JSON files from a trusted source.",
          "After import, open a few templates and check formatting and placeholders."
        ]
      },
      {
        "title": "9. Permissions and roles",
        "paragraphs": [
          "The plugin uses separate capabilities for managing templates and using templates in orders. Administrators and shop managers receive these permissions automatically during activation.",
          "If you use a role editor plugin, you can grant or remove these permissions for custom roles."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: create, edit, delete and import/export templates.",
          "<code>use_mh_order_note_templates</code>: use templates in WooCommerce orders.",
          "Users without the required permission do not see the related admin functions."
        ]
      },
      {
        "title": "10. Security and HPOS compatibility",
        "paragraphs": [
          "The plugin uses WordPress nonces, capability checks, sanitizing and escaping for admin actions. Template content is cleaned with WordPress-safe HTML before it is saved or used.",
          "Order data is read through WooCommerce order APIs instead of direct database table access. This keeps the plugin compatible with WooCommerce HPOS and classic order storage."
        ],
        "items": [
          "Keep WooCommerce and WordPress updated.",
          "Test customer note workflows after changing WooCommerce email settings.",
          "Use a staging site before importing a large template set."
        ]
      },
      {
        "title": "11. Troubleshooting",
        "paragraphs": [
          "If templates do not appear in an order, check that WooCommerce is active, the template is published and the current user has the permission to use templates.",
          "If translations do not appear, check the site language and user language in WordPress. The plugin includes reviewed bundled fallback files for all supported languages, including Persian, Vietnamese and Czech. Other languages should be supplied through reviewed WordPress.org language packs."
        ],
        "items": [
          "After an update, clear object/cache plugins if the old admin screen is still shown.",
          "If a placeholder stays unchanged, verify that it is written exactly as listed, including curly brackets.",
          "If customer notes are not emailed, check the WooCommerce email settings for customer note notifications."
        ]
      },
      {
        "title": "12. Settings page",
        "paragraphs": [
          "Open <strong>Mailhilfe Order Notes → Settings</strong> to choose the default note type, safe HTML behavior, usage display, favorites, JSON import options and language matching. Use internal notes as the default for safer daily work."
        ],
        "items": []
      },
      {
        "title": "13. Template language and multilingual shops",
        "paragraphs": [
          "Each template can have a template language. Choose <strong>All languages</strong> if the same text may be used for every order, or select a specific language for localized texts.",
          "When available, the plugin can prefer templates that match the order language, user language or common language data from multilingual plugins."
        ],
        "items": [
          "Use one neutral template for internal staff notes.",
          "Create separate customer-facing templates for German, English or other shop languages.",
          "For WPML or Polylang shops, test the order language detection with a real test order."
        ]
      },
      {
        "title": "14. Custom fields and meta placeholders",
        "paragraphs": [
          "Advanced placeholders can read selected order or customer meta fields. Use <code>{order_meta:meta_key}</code> for order data and <code>{customer_meta:meta_key}</code> for customer user data.",
          "For security, sensitive key names such as password, token, secret, session, auth and hash are blocked. Only use meta placeholders when you know what the field contains."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code> for a tracking number stored by a shipping plugin.",
          "Example: <code>{order_meta:_billing_vat_id}</code> for a VAT ID field.",
          "Do not expose internal or sensitive fields in customer notes."
        ]
      },
      {
        "title": "15. Duplicate templates and revisions",
        "paragraphs": [
          "Use the duplicate action when you need a similar template with small changes. The copy is created as a draft so it can be reviewed before publication.",
          "Template revisions allow you to compare earlier versions and restore a previous text when a change was made by mistake."
        ],
        "items": [
          "Duplicate a general shipping template before creating variants for DHL, UPS or pickup.",
          "Check revisions after larger text changes.",
          "Keep titles clear so similar templates are not confused."
        ]
      },
      {
        "title": "16. Permissions page",
        "paragraphs": [
          "Open <strong>Mailhilfe Order Notes → Permissions</strong> to decide which WordPress roles may manage templates and which roles may use templates in orders.",
          "Administrators keep the required permissions. For other roles, grant only the permissions needed for the daily task."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export templates.",
          "Use templates: select a template and add a note in a WooCommerce order.",
          "Give import/export permissions only to trusted users."
        ]
      },
      {
        "title": "17. Import preview",
        "paragraphs": [
          "JSON imports now show a preview before changes are applied. The preview tells you how many templates will be created, updated or skipped.",
          "Only confirm the import after checking the preview. This prevents accidental overwriting of existing templates."
        ],
        "items": [
          "Create an export backup before importing a large set.",
          "Import only JSON files from a trusted source.",
          "After import, test at least one customer note and one internal note."
        ]
      },
      {
        "title": "18. Customer note email behavior",
        "paragraphs": [
          "Customer notes can trigger WooCommerce email notifications when the corresponding email is enabled. The plugin records creation of the customer note separately from email processing. Review the editable preview before adding the note and use the History page to check the mail handler result."
        ],
        "items": []
      },
      {
        "title": "19. Recommended workflow",
        "paragraphs": [
          "A safe daily workflow is: select a template, review the replaced preview, edit the preview if needed, verify the note type and then add the note.",
          "For new templates, first test them in a non-critical order or staging shop before using them with real customers."
        ],
        "items": [
          "Use internal notes for staff-only information.",
          "Use customer notes only for messages that may be sent to the customer.",
          "Review placeholders whenever a template is changed."
        ]
      },
      {
        "title": "20. Template conditions",
        "paragraphs": [
          "Template conditions decide whether a template is available for a particular order. You can restrict templates by order status, payment method, shipping method, billing country and minimum or maximum order total. All configured conditions must match."
        ],
        "items": [
          "Leave a field empty when that condition should not restrict the template.",
          "Use the technical IDs of payment and shipping methods.",
          "Conditions are checked in the interface and again on the server before a note is created."
        ]
      },
      {
        "title": "21. Email processing log",
        "paragraphs": [
          "For customer notes, the plugin records when WooCommerce reports the customer-note email as processed and also records technical wp_mail errors. A processed event confirms that WordPress/WooCommerce handed the message to the mail system; it does not prove final delivery or that the customer read it."
        ],
        "items": [
          "Check the History page for processed and failed email events.",
          "Use an SMTP provider or mail-log service when definitive delivery information is required.",
          "Internal notes do not trigger a customer-note email."
        ]
      },
      {
        "title": "22. Central history",
        "paragraphs": [
          "Open <strong>Mailhilfe Order Notes → History</strong> to review recent note creation, template usage, email processing and email failures. Entries include the order, template, user, recipient, event type and time when available."
        ],
        "items": [
          "Use the history for support, auditing and troubleshooting.",
          "The history is separate from WooCommerce order notes.",
          "The page displays the most recent 250 entries."
        ]
      },
      {
        "title": "23. Test-order preview",
        "paragraphs": [
          "In the template editor, enter a WooCommerce order ID in the test preview area. The current editor content, including unsaved changes, is rendered with data from that order without creating a note or sending an email."
        ],
        "items": [
          "Use a staging order or a non-critical test order.",
          "Check missing values, formatting, conditions and custom meta placeholders.",
          "You must have permission to edit the selected order."
        ]
      },
      {
        "title": "24. Personal favorites and recently used templates",
        "paragraphs": [
          "Each administrator can mark personal favorites in the order screen. The plugin also stores the ten most recently used templates for each user and gives them a higher position in the selection. Global favorites remain shared with all users."
        ],
        "items": [
          "Personal favorites do not change another user’s list.",
          "The recent list is updated only after a note is added successfully.",
          "Personal data is stored as WordPress user metadata."
        ]
      },
      {
        "title": "25. Diagnostics page",
        "paragraphs": [
          "Open <strong>Mailhilfe Order Notes → Diagnostics</strong> to view technical information such as WordPress, PHP and WooCommerce versions, HPOS status, customer-note email status, locale, published-template count, cache status and WP_DEBUG."
        ],
        "items": [
          "Copy the diagnostic values when requesting support.",
          "The page does not display order-note content or customer addresses.",
          "Developers can add rows with the diagnostics filter."
        ]
      },
      {
        "title": "26. Developer hooks and filters",
        "paragraphs": [
          "The plugin provides hooks and filters for placeholders, placeholder values, allowed meta keys, template results, conditions, preview content, final note content, actions before and after adding a note, history records and diagnostics. Hook names and parameters are documented in readme.txt."
        ],
        "items": [
          "Validate, sanitize and escape all custom data.",
          "Use WooCommerce order APIs instead of direct order-table access.",
          "Keep custom extensions compatible with both HPOS and classic order storage."
        ]
      }
    ]
  },
  "de_DE": {
    "title": "Ausführliche Hilfe für Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Diese aktualisierte Hilfe beschreibt den vollständigen Ablauf mit dem neuen Namen Mailhilfe Order Note Manager for WooCommerce: Vorlagen erstellen, formatieren, sprachabhängig verwenden, Platzhalter und Meta-Platzhalter nutzen, Vorschauen bearbeiten, Kundennotizen sicher versenden, Einstellungen, Berechtigungen, Import-Vorschau und HPOS-Kompatibilität verstehen.",
    "sections": [
      {
        "title": "1. Wofür das Plugin gedacht ist",
        "paragraphs": [
          "Mit Mailhilfe Order Note Manager for WooCommerce speicherst du häufig verwendete WooCommerce-Bestellnotizen als wiederverwendbare Vorlagen. Dadurch müssen gleiche Texte nicht immer wieder neu geschrieben werden und die Kommunikation in der Bestellhistorie bleibt einheitlich.",
          "Eine Vorlage kann als interne Notiz für Mitarbeiter oder als Kundennotiz vorbereitet werden. Beim Verwenden in der Bestellung kann der Notiztyp trotzdem noch geändert werden."
        ],
        "items": [
          "Typische Beispiele: Zahlungserinnerungen, Lieferverzögerungen, Telefonnotizen, Adressprüfungen und Serviceantworten.",
          "Vorlagen unterstützen Kategorien, Favoriten, Sortierung, Nutzungszähler und JSON-Sicherung."
        ]
      },
      {
        "title": "2. Neue Vorlage erstellen",
        "paragraphs": [
          "Öffne <strong>Bestellnotiz-Vorlagen → Erstellen</strong>. Trage einen klaren Titel ein, schreibe den Notiztext im Editor und wähle, ob die Vorlage standardmäßig als interne Notiz oder als Kundennotiz verwendet werden soll.",
          "Verwende als Titel eine kurze Beschreibung des Zwecks, zum Beispiel „Zahlungserinnerung“ oder „Kunde wegen Lieferung angerufen“. So findest du die Vorlage später schneller in der Bestellung."
        ],
        "items": [
          "Ordne Kategorien zu, wenn viele Vorlagen vorhanden sind.",
          "Markiere häufig verwendete Vorlagen als Favoriten.",
          "Veröffentliche die Vorlage, damit sie in Bestellungen verfügbar ist."
        ]
      },
      {
        "title": "3. Vorlagentexte formatieren",
        "paragraphs": [
          "Der Vorlagentext wird mit dem WordPress-Editor bearbeitet. Du kannst Absätze, Fettschrift, Kursivschrift, Listen und Links verwenden. Die Formatierung bleibt beim Erstellen der Notiz erhalten, wird aber mit WordPress-sicheren HTML-Regeln bereinigt.",
          "Bei Kundennotizen sollte die Formatierung sparsam verwendet werden. Ein kurzer Absatz oder eine übersichtliche Liste ist meist besser lesbar als ein langer Fließtext."
        ],
        "items": [
          "Gutes Muster: kurze Anrede, klare Erklärung und nächster Schritt.",
          "Interne Abkürzungen sollten in Kundennotizen vermieden werden.",
          "Private Mitarbeiterhinweise gehören nicht in Vorlagen, die als Kundennotiz verwendet werden können."
        ]
      },
      {
        "title": "4. Platzhalter verwenden",
        "paragraphs": [
          "Platzhalter sind Begriffe in geschweiften Klammern. Sie werden in der Vorschau und beim Hinzufügen der Notiz automatisch durch echte Bestelldaten ersetzt.",
          "Du kannst normalen Text und Platzhalter kombinieren. Beispiel: <code>Hallo {customer}, wir haben deine Bestellung {order_number} erhalten.</code>"
        ],
        "items": [
          "Bestellung: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Kunde: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Versand und Zahlung: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Artikel und Shop: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Vorschau vor dem Einfügen prüfen",
        "paragraphs": [
          "Öffne eine WooCommerce-Bestellung und wähle eine Vorlage aus. Die Vorschau zeigt die Notiz bereits mit den ersetzten Platzhaltern der jeweiligen Bestellung.",
          "Prüfe die Vorschau immer vor dem Erstellen der Notiz. Das ist besonders wichtig, wenn ein Platzhalter in der Bestellung keinen Wert hat, zum Beispiel wenn Versandart oder Telefonnummer fehlen."
        ],
        "items": [
          "Prüfe Namen, Beträge, Versandart und Artikelliste.",
          "Kontrolliere, ob der richtige Notiztyp ausgewählt ist.",
          "Verbessere die Vorlage selbst, wenn derselbe Text künftig immer geändert werden soll."
        ]
      },
      {
        "title": "6. Interne Notizen und Kundennotizen",
        "paragraphs": [
          "Interne Notizen sind für Shop-Mitarbeiter gedacht und eignen sich für Dokumentation, Nachverfolgung und Servicehistorie. Kundennotizen können für den Kunden sichtbar sein und je nach WooCommerce-Einstellungen E-Mail-Benachrichtigungen auslösen.",
          "Prüfe die bearbeitbare Vorschau und den ausgewählten Notiztyp sorgfältig. Verwende Kundennotizen nur für Texte, die der Kunde wirklich lesen darf."
        ],
        "items": [
          "Interne Notiz: „Kunde hat angerufen, Lieferadresse bestätigt.“",
          "Kundennotiz: „Deine Bestellung wird vorbereitet und in Kürze versendet.“",
          "Passwörter, private Kommentare oder lieferanteninterne Informationen dürfen nicht in Kundennotizen stehen."
        ]
      },
      {
        "title": "7. Favoriten, Suche und Sortierung",
        "paragraphs": [
          "Favoriten sorgen dafür, dass wichtige Vorlagen in der Auswahl weiter oben erscheinen. Das Suchfeld in der Bestellung hilft, Vorlagen nach Titel, Kategorie oder Inhalt schneller zu finden.",
          "In der Vorlagenliste kann die Reihenfolge per Drag-and-Drop geändert werden. Die gespeicherte Sortierung wird anschließend bei der Anzeige in Bestellungen verwendet."
        ],
        "items": [
          "Nutze Favoriten für tägliche Standardtexte.",
          "Nutze Kategorien für Themen wie Zahlung, Versand, Rückgabe und Support.",
          "Halte Titel kurz, damit Suchergebnisse übersichtlich bleiben."
        ]
      },
      {
        "title": "8. Import, Export und Demo-Vorlagen",
        "paragraphs": [
          "Der JSON-Export erstellt eine Sicherung deiner Vorlagen. Das ist sinnvoll vor größeren Änderungen oder wenn Vorlagen in einen anderen Shop übertragen werden sollen.",
          "Der JSON-Import kann Vorlagen neu anlegen oder vorhandene Vorlagen mit gleichem Titel beziehungsweise internem Demo-Schlüssel aktualisieren. Demo-Vorlagen dienen als schneller Einstieg und werden in der aktiven Sprache erstellt."
        ],
        "items": [
          "Exportiere vor größeren Änderungen eine Sicherung.",
          "Importiere nur JSON-Dateien aus vertrauenswürdigen Quellen.",
          "Öffne nach dem Import einige Vorlagen und prüfe Formatierung und Platzhalter."
        ]
      },
      {
        "title": "9. Rollenrechte",
        "paragraphs": [
          "Das Plugin verwendet getrennte Rechte für das Verwalten von Vorlagen und das Verwenden von Vorlagen in Bestellungen. Administratoren und Shop-Manager erhalten diese Rechte bei der Aktivierung automatisch.",
          "Wenn du ein Rollen-Plugin verwendest, können diese Rechte auch benutzerdefinierten Rollen zugewiesen oder entzogen werden."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: Vorlagen erstellen, bearbeiten, löschen sowie Import/Export nutzen.",
          "<code>use_mh_order_note_templates</code>: Vorlagen in WooCommerce-Bestellungen verwenden.",
          "Benutzer ohne passende Berechtigung sehen die entsprechenden Admin-Funktionen nicht."
        ]
      },
      {
        "title": "10. Sicherheit und HPOS-Kompatibilität",
        "paragraphs": [
          "Das Plugin verwendet WordPress-Nonces, Rechteprüfungen, Sanitizing und Escaping für Admin-Aktionen. Vorlageninhalte werden vor dem Speichern und Verwenden mit WordPress-sicheren HTML-Regeln bereinigt.",
          "Bestelldaten werden über WooCommerce-Bestell-APIs gelesen und nicht direkt aus alten Datenbanktabellen abgefragt. Dadurch bleibt das Plugin mit WooCommerce HPOS und klassischer Bestellspeicherung kompatibel."
        ],
        "items": [
          "Halte WordPress und WooCommerce aktuell.",
          "Teste Kundennotizen nach Änderungen an WooCommerce-E-Mail-Einstellungen.",
          "Nutze vor großen Importen möglichst eine Testumgebung."
        ]
      },
      {
        "title": "11. Fehlerbehebung",
        "paragraphs": [
          "Wenn Vorlagen in einer Bestellung nicht erscheinen, prüfe, ob WooCommerce aktiv ist, die Vorlage veröffentlicht wurde und der aktuelle Benutzer das Recht zur Verwendung von Vorlagen besitzt.",
          "Wenn Übersetzungen nicht erscheinen, prüfe die Website-Sprache und die Benutzersprache in WordPress. Das Plugin enthält geprüfte Fallback-Dateien für alle unterstützten Sprachen, einschließlich Persisch. Weitere Sprachen werden über geprüfte WordPress.org-Sprachpakete bereitgestellt."
        ],
        "items": [
          "Leere nach Updates den Cache, wenn noch alte Adminseiten angezeigt werden.",
          "Wenn ein Platzhalter unverändert bleibt, prüfe die exakte Schreibweise inklusive geschweifter Klammern.",
          "Wenn Kundennotizen nicht per E-Mail versendet werden, prüfe die WooCommerce-E-Mail-Einstellung für Kundennotizen."
        ]
      },
      {
        "title": "12. Einstellungen",
        "paragraphs": [
          "Öffne <strong>Mailhilfe Order Notes → Einstellungen</strong>, um Standard-Notiztyp, sichere HTML-Formatierung, Nutzungsanzeige, Favoriten, JSON-Import und Sprachzuordnung festzulegen. Verwende für die tägliche Arbeit vorzugsweise interne Notizen."
        ],
        "items": []
      },
      {
        "title": "13. Vorlagensprache und mehrsprachige Shops",
        "paragraphs": [
          "Jede Vorlage kann eine Vorlagensprache erhalten. Wähle <strong>Alle Sprachen</strong>, wenn der Text für jede Bestellung verwendet werden darf, oder wähle eine konkrete Sprache für übersetzte Kundentexte.",
          "Wenn möglich, bevorzugt das Plugin Vorlagen, die zur Bestellsprache, Benutzersprache oder zu Sprachinformationen von Mehrsprachen-Plugins passen."
        ],
        "items": [
          "Nutze eine neutrale Vorlage für interne Mitarbeiternotizen.",
          "Erstelle eigene Kundenvorlagen für Deutsch, Englisch oder andere Shop-Sprachen.",
          "Teste bei WPML- oder Polylang-Shops die Spracherkennung mit einer echten Testbestellung."
        ]
      },
      {
        "title": "14. Eigene Felder und Meta-Platzhalter",
        "paragraphs": [
          "Erweiterte Platzhalter können ausgewählte Bestell- oder Kundendaten aus Meta-Feldern auslesen. Verwende <code>{order_meta:meta_key}</code> für Bestelldaten und <code>{customer_meta:meta_key}</code> für Benutzer-Metadaten.",
          "Aus Sicherheitsgründen werden sensible Schlüssel wie password, token, secret, session, auth und hash blockiert. Verwende Meta-Platzhalter nur, wenn du genau weißt, welche Daten das Feld enthält."
        ],
        "items": [
          "Beispiel: <code>{order_meta:_tracking_number}</code> für eine Sendungsnummer aus einem Versandplugin.",
          "Beispiel: <code>{order_meta:_billing_vat_id}</code> für eine Umsatzsteuer-ID.",
          "Gib interne oder sensible Felder niemals in Kundennotizen aus."
        ]
      },
      {
        "title": "15. Vorlagen duplizieren und Versionen",
        "paragraphs": [
          "Nutze die Aktion „Duplizieren“, wenn du eine ähnliche Vorlage mit kleinen Änderungen benötigst. Die Kopie wird als Entwurf erstellt und kann vor der Veröffentlichung geprüft werden.",
          "Über WordPress-Revisionen können frühere Texte verglichen und bei Bedarf wiederhergestellt werden."
        ],
        "items": [
          "Dupliziere z. B. eine allgemeine Versandvorlage für DHL, UPS oder Abholung.",
          "Prüfe die Versionen nach größeren Textänderungen.",
          "Verwende klare Titel, damit ähnliche Vorlagen nicht verwechselt werden."
        ]
      },
      {
        "title": "16. Berechtigungen",
        "paragraphs": [
          "Öffne <strong>Bestellnotiz-Vorlagen → Berechtigungen</strong>, um festzulegen, welche WordPress-Rollen Vorlagen verwalten und welche Rollen Vorlagen in Bestellungen verwenden dürfen.",
          "Administratoren behalten die notwendigen Rechte. Bei anderen Rollen sollten nur die Rechte vergeben werden, die für die tägliche Arbeit wirklich benötigt werden."
        ],
        "items": [
          "Vorlagen verwalten: erstellen, bearbeiten, löschen, importieren und exportieren.",
          "Vorlagen verwenden: Vorlage auswählen und Notiz in einer WooCommerce-Bestellung hinzufügen.",
          "Import/Export sollte nur vertrauenswürdigen Benutzern erlaubt werden."
        ]
      },
      {
        "title": "17. Import-Vorschau",
        "paragraphs": [
          "JSON-Importe zeigen vor der Übernahme eine Vorschau. Dort siehst du, wie viele Vorlagen neu erstellt, aktualisiert oder übersprungen werden.",
          "Bestätige den Import erst nach Prüfung der Vorschau. So wird ein versehentliches Überschreiben vorhandener Vorlagen vermieden."
        ],
        "items": [
          "Erstelle vor größeren Importen immer einen Export als Sicherung.",
          "Importiere nur JSON-Dateien aus vertrauenswürdigen Quellen.",
          "Teste nach dem Import mindestens eine Kundennotiz und eine interne Notiz."
        ]
      },
      {
        "title": "18. E-Mail-Verhalten bei Kundennotizen",
        "paragraphs": [
          "Kundennotizen können WooCommerce-E-Mail-Benachrichtigungen auslösen, wenn die entsprechende E-Mail aktiviert ist. Das Plugin protokolliert die Erstellung der Kundennotiz getrennt von der E-Mail-Verarbeitung. Prüfe vor dem Hinzufügen die bearbeitbare Vorschau und kontrolliere das Ergebnis der Mailverarbeitung auf der Seite „Verlauf“."
        ],
        "items": []
      },
      {
        "title": "19. Empfohlener Arbeitsablauf",
        "paragraphs": [
          "Ein sicherer Ablauf ist: Vorlage auswählen, ersetzte Vorschau prüfen, Vorschau bei Bedarf bearbeiten, Notiztyp kontrollieren und erst dann die Notiz hinzufügen.",
          "Neue Vorlagen sollten zuerst in einer unkritischen Bestellung oder in einer Testumgebung geprüft werden, bevor sie für echte Kunden genutzt werden."
        ],
        "items": [
          "Verwende interne Notizen für rein interne Informationen.",
          "Verwende Kundennotizen nur für Texte, die der Kunde lesen darf.",
          "Prüfe Platzhalter nach jeder Änderung einer Vorlage."
        ]
      },
      {
        "title": "20. Bedingungen für Vorlagen",
        "paragraphs": [
          "Vorlagenbedingungen bestimmen, ob eine Vorlage für eine bestimmte Bestellung verfügbar ist. Vorlagen können nach Bestellstatus, Zahlungsart, Versandart, Rechnungsland sowie Mindest- und Höchstbestellwert eingeschränkt werden. Alle eingetragenen Bedingungen müssen erfüllt sein."
        ],
        "items": [
          "Lass ein Feld leer, wenn diese Bedingung die Vorlage nicht einschränken soll.",
          "Verwende bei Zahlungs- und Versandarten die technischen IDs.",
          "Die Bedingungen werden in der Oberfläche und vor dem Erstellen der Notiz erneut serverseitig geprüft."
        ]
      },
      {
        "title": "21. Protokollierung der E-Mail-Verarbeitung",
        "paragraphs": [
          "Bei Kundennotizen protokolliert das Plugin, wenn WooCommerce die Kundennotiz-E-Mail als verarbeitet meldet. Technische Fehler von wp_mail werden ebenfalls erfasst. „Verarbeitet“ bestätigt nur die Übergabe an das E-Mail-System und nicht die endgültige Zustellung oder das Lesen durch den Kunden."
        ],
        "items": [
          "Prüfe auf der Seite „Verlauf“ verarbeitete und fehlgeschlagene E-Mail-Ereignisse.",
          "Für verbindliche Zustellinformationen ist ein SMTP-Anbieter oder E-Mail-Protokolldienst erforderlich.",
          "Interne Notizen lösen keine Kundennotiz-E-Mail aus."
        ]
      },
      {
        "title": "22. Zentraler Verlauf",
        "paragraphs": [
          "Öffne <strong>Bestellnotiz-Vorlagen → Verlauf</strong>, um das Erstellen von Notizen, die Vorlagennutzung, verarbeitete E-Mails und E-Mail-Fehler zentral einzusehen. Soweit vorhanden werden Bestellung, Vorlage, Benutzer, Empfänger, Ereignistyp und Zeitpunkt angezeigt."
        ],
        "items": [
          "Nutze den Verlauf für Support, Nachvollziehbarkeit und Fehlersuche.",
          "Der Verlauf ist von den WooCommerce-Bestellnotizen getrennt.",
          "Angezeigt werden die neuesten 250 Einträge."
        ]
      },
      {
        "title": "23. Vorschau mit Testbestellung",
        "paragraphs": [
          "Gib im Vorlageneditor im Bereich für die Testvorschau eine WooCommerce-Bestell-ID ein. Der aktuelle Editorinhalt einschließlich noch nicht gespeicherter Änderungen wird mit den Daten dieser Bestellung dargestellt, ohne eine Notiz anzulegen oder eine E-Mail zu senden."
        ],
        "items": [
          "Verwende eine Testbestellung oder eine unkritische Bestellung in einer Staging-Umgebung.",
          "Prüfe fehlende Werte, Formatierung, Bedingungen und eigene Meta-Platzhalter.",
          "Du musst die ausgewählte Bestellung bearbeiten dürfen."
        ]
      },
      {
        "title": "24. Persönliche Favoriten und zuletzt verwendete Vorlagen",
        "paragraphs": [
          "Jeder Benutzer kann in der Bestellansicht persönliche Favoriten markieren. Zusätzlich speichert das Plugin pro Benutzer die zehn zuletzt erfolgreich verwendeten Vorlagen und zeigt sie weiter oben an. Globale Favoriten bleiben weiterhin für alle Benutzer gemeinsam."
        ],
        "items": [
          "Persönliche Favoriten verändern die Auswahl anderer Mitarbeiter nicht.",
          "Die Liste „zuletzt verwendet“ wird erst nach erfolgreichem Hinzufügen einer Notiz aktualisiert.",
          "Die persönlichen Angaben werden als WordPress-Benutzermetadaten gespeichert."
        ]
      },
      {
        "title": "25. Diagnose-Seite",
        "paragraphs": [
          "Unter <strong>Bestellnotiz-Vorlagen → Diagnose</strong> findest du technische Angaben wie WordPress-, PHP- und WooCommerce-Version, HPOS-Status, Status der Kundennotiz-E-Mail, Sprache, Anzahl veröffentlichter Vorlagen, Cache-Status und WP_DEBUG."
        ],
        "items": [
          "Gib diese Werte bei Supportanfragen mit an.",
          "Die Seite zeigt keine Notizinhalte, Kundenadressen oder Bestellpositionen an.",
          "Entwickler können die Diagnose über einen Filter erweitern."
        ]
      },
      {
        "title": "26. Hooks und Filter für Entwickler",
        "paragraphs": [
          "Das Plugin bietet Hooks und Filter für Platzhalter, Platzhalterwerte, erlaubte Meta-Schlüssel, Vorlagenergebnisse, Bedingungen, Vorschauinhalt, endgültigen Notizinhalt, Aktionen vor und nach dem Hinzufügen, Verlaufseinträge und Diagnosewerte. Namen und Parameter sind in der readme.txt dokumentiert."
        ],
        "items": [
          "Validiere, bereinige und maskiere alle eigenen Daten.",
          "Verwende WooCommerce-Bestell-APIs statt direkter Zugriffe auf Bestelltabellen.",
          "Achte bei Erweiterungen auf HPOS und die klassische Bestellspeicherung."
        ]
      }
    ]
  },
  "de_DE_formal": {
    "title": "Ausführliche Hilfe für Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Diese aktualisierte Hilfe beschreibt den vollständigen Ablauf mit dem neuen Namen Mailhilfe Order Note Manager for WooCommerce: Vorlagen erstellen, formatieren, sprachabhängig verwenden, Platzhalter und Meta-Platzhalter nutzen, Vorschauen bearbeiten, Kundennotizen sicher versenden, Einstellungen, Berechtigungen, Import-Vorschau und HPOS-Kompatibilität verstehen.",
    "sections": [
      {
        "title": "1. Wofür das Plugin gedacht ist",
        "paragraphs": [
          "Mit Mailhilfe Order Note Manager for WooCommerce speichern Sie häufig verwendete WooCommerce-Bestellnotizen als wiederverwendbare Vorlagen. Dadurch müssen gleiche Texte nicht immer wieder neu geschrieben werden und die Kommunikation in der Bestellhistorie bleibt einheitlich.",
          "Eine Vorlage kann als interne Notiz für Mitarbeiter oder als Kundennotiz vorbereitet werden. Beim Verwenden in der Bestellung kann der Notiztyp trotzdem noch geändert werden."
        ],
        "items": [
          "Typische Beispiele: Zahlungserinnerungen, Lieferverzögerungen, Telefonnotizen, Adressprüfungen und Serviceantworten.",
          "Vorlagen unterstützen Kategorien, Favoriten, Sortierung, Nutzungszähler und JSON-Sicherung."
        ]
      },
      {
        "title": "2. Neue Vorlage erstellen",
        "paragraphs": [
          "Öffnen Sie <strong>Bestellnotiz-Vorlagen → Erstellen</strong>. Tragen Sie einen klaren Titel ein, schreiben Sie den Notiztext im Editor und wählen Sie, ob die Vorlage standardmäßig als interne Notiz oder als Kundennotiz verwendet werden soll.",
          "Verwenden Sie als Titel eine kurze Beschreibung des Zwecks, zum Beispiel „Zahlungserinnerung“ oder „Kunde wegen Lieferung angerufen“. So finden Sie die Vorlage später schneller in der Bestellung."
        ],
        "items": [
          "Ordnen Sie Kategorien zu, wenn viele Vorlagen vorhanden sind.",
          "Markieren Sie häufig verwendete Vorlagen als Favoriten.",
          "Veröffentlichen Sie die Vorlage, damit sie in Bestellungen verfügbar ist."
        ]
      },
      {
        "title": "3. Vorlagentexte formatieren",
        "paragraphs": [
          "Der Vorlagentext wird mit dem WordPress-Editor bearbeitet. Sie können Absätze, Fettschrift, Kursivschrift, Listen und Links verwenden. Die Formatierung bleibt beim Erstellen der Notiz erhalten, wird aber mit WordPress-sicheren HTML-Regeln bereinigt.",
          "Bei Kundennotizen sollte die Formatierung sparsam verwendet werden. Ein kurzer Absatz oder eine übersichtliche Liste ist meist besser lesbar als ein langer Fließtext."
        ],
        "items": [
          "Gutes Muster: kurze Anrede, klare Erklärung und nächster Schritt.",
          "Interne Abkürzungen sollten in Kundennotizen vermieden werden.",
          "Private Mitarbeiterhinweise gehören nicht in Vorlagen, die als Kundennotiz verwendet werden können."
        ]
      },
      {
        "title": "4. Platzhalter verwenden",
        "paragraphs": [
          "Platzhalter sind Begriffe in geschweiften Klammern. Sie werden in der Vorschau und beim Hinzufügen der Notiz automatisch durch echte Bestelldaten ersetzt.",
          "Sie können normalen Text und Platzhalter kombinieren. Beispiel: <code>Hallo {customer}, wir haben Ihre Bestellung {order_number} erhalten.</code>"
        ],
        "items": [
          "Bestellung: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Kunde: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Versand und Zahlung: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Artikel und Shop: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Vorschau vor dem Einfügen prüfen",
        "paragraphs": [
          "Öffnen Sie eine WooCommerce-Bestellung und wählen Sie eine Vorlage aus. Die Vorschau zeigt die Notiz bereits mit den ersetzten Platzhaltern der jeweiligen Bestellung.",
          "Prüfen Sie die Vorschau immer vor dem Erstellen der Notiz. Das ist besonders wichtig, wenn ein Platzhalter in der Bestellung keinen Wert hat, zum Beispiel wenn Versandart oder Telefonnummer fehlen."
        ],
        "items": [
          "Prüfen Sie Namen, Beträge, Versandart und Artikelliste.",
          "Kontrollieren Sie, ob der richtige Notiztyp ausgewählt ist.",
          "Verbessern Sie die Vorlage selbst, wenn derselbe Text künftig immer geändert werden soll."
        ]
      },
      {
        "title": "6. Interne Notizen und Kundennotizen",
        "paragraphs": [
          "Interne Notizen sind für Shop-Mitarbeiter gedacht und eignen sich für Dokumentation, Nachverfolgung und Servicehistorie. Kundennotizen können für den Kunden sichtbar sein und je nach WooCommerce-Einstellungen E-Mail-Benachrichtigungen auslösen.",
          "Prüfen Sie die bearbeitbare Vorschau und den ausgewählten Notiztyp sorgfältig. Verwenden Sie Kundennotizen nur für Texte, die der Kunde wirklich lesen darf."
        ],
        "items": [
          "Interne Notiz: „Kunde hat angerufen, Lieferadresse bestätigt.“",
          "Kundennotiz: „Ihre Bestellung wird vorbereitet und in Kürze versendet.“",
          "Passwörter, private Kommentare oder lieferanteninterne Informationen dürfen nicht in Kundennotizen stehen."
        ]
      },
      {
        "title": "7. Favoriten, Suche und Sortierung",
        "paragraphs": [
          "Favoriten sorgen dafür, dass wichtige Vorlagen in der Auswahl weiter oben erscheinen. Das Suchfeld in der Bestellung hilft, Vorlagen nach Titel, Kategorie oder Inhalt schneller zu finden.",
          "In der Vorlagenliste kann die Reihenfolge per Drag-and-Drop geändert werden. Die gespeicherte Sortierung wird anschließend bei der Anzeige in Bestellungen verwendet."
        ],
        "items": [
          "Nutzen Sie Favoriten für tägliche Standardtexte.",
          "Nutzen Sie Kategorien für Themen wie Zahlung, Versand, Rückgabe und Support.",
          "Halten Sie Titel kurz, damit Suchergebnisse übersichtlich bleiben."
        ]
      },
      {
        "title": "8. Import, Export und Demo-Vorlagen",
        "paragraphs": [
          "Der JSON-Export erstellt eine Sicherung Ihrer Vorlagen. Das ist sinnvoll vor größeren Änderungen oder wenn Vorlagen in einen anderen Shop übertragen werden sollen.",
          "Der JSON-Import kann Vorlagen neu anlegen oder vorhandene Vorlagen mit gleichem Titel beziehungsweise internem Demo-Schlüssel aktualisieren. Demo-Vorlagen dienen als schneller Einstieg und werden in der aktiven Sprache erstellt."
        ],
        "items": [
          "Exportieren Sie vor größeren Änderungen eine Sicherung.",
          "Importieren Sie nur JSON-Dateien aus vertrauenswürdigen Quellen.",
          "Öffnen Sie nach dem Import einige Vorlagen und prüfen Sie Formatierung und Platzhalter."
        ]
      },
      {
        "title": "9. Rollenrechte",
        "paragraphs": [
          "Das Plugin verwendet getrennte Rechte für das Verwalten von Vorlagen und das Verwenden von Vorlagen in Bestellungen. Administratoren und Shop-Manager erhalten diese Rechte bei der Aktivierung automatisch.",
          "Wenn Sie ein Rollen-Plugin verwenden, können diese Rechte auch benutzerdefinierten Rollen zugewiesen oder entzogen werden."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: Vorlagen erstellen, bearbeiten, löschen sowie Import/Export nutzen.",
          "<code>use_mh_order_note_templates</code>: Vorlagen in WooCommerce-Bestellungen verwenden.",
          "Benutzer ohne passende Berechtigung sehen die entsprechenden Admin-Funktionen nicht."
        ]
      },
      {
        "title": "10. Sicherheit und HPOS-Kompatibilität",
        "paragraphs": [
          "Das Plugin verwendet WordPress-Nonces, Rechteprüfungen, Sanitizing und Escaping für Admin-Aktionen. Vorlageninhalte werden vor dem Speichern und Verwenden mit WordPress-sicheren HTML-Regeln bereinigt.",
          "Bestelldaten werden über WooCommerce-Bestell-APIs gelesen und nicht direkt aus alten Datenbanktabellen abgefragt. Dadurch bleibt das Plugin mit WooCommerce HPOS und klassischer Bestellspeicherung kompatibel."
        ],
        "items": [
          "Halten Sie WordPress und WooCommerce aktuell.",
          "Testen Sie Kundennotizen nach Änderungen an WooCommerce-E-Mail-Einstellungen.",
          "Nutzen Sie vor großen Importen möglichst eine Testumgebung."
        ]
      },
      {
        "title": "11. Fehlerbehebung",
        "paragraphs": [
          "Wenn Vorlagen in einer Bestellung nicht erscheinen, prüfen Sie, ob WooCommerce aktiv ist, die Vorlage veröffentlicht wurde und der aktuelle Benutzer das Recht zur Verwendung von Vorlagen besitzt.",
          "Wenn Übersetzungen nicht erscheinen, prüfen Sie die Website-Sprache und die Benutzersprache in WordPress. Das Plugin enthält geprüfte Fallback-Dateien für alle unterstützten Sprachen, einschließlich Persisch. Weitere Sprachen werden über geprüfte WordPress.org-Sprachpakete bereitgestellt."
        ],
        "items": [
          "Leeren Sie nach Updates den Cache, wenn noch alte Adminseiten angezeigt werden.",
          "Wenn ein Platzhalter unverändert bleibt, prüfen Sie die exakte Schreibweise inklusive geschweifter Klammern.",
          "Wenn Kundennotizen nicht per E-Mail versendet werden, prüfen Sie die WooCommerce-E-Mail-Einstellung für Kundennotizen."
        ]
      },
      {
        "title": "12. Einstellungen",
        "paragraphs": [
          "Öffnen Sie <strong>Mailhilfe Order Notes → Einstellungen</strong>, um Standard-Notiztyp, sichere HTML-Formatierung, Nutzungsanzeige, Favoriten, JSON-Import und Sprachzuordnung festzulegen. Verwenden Sie für die tägliche Arbeit vorzugsweise interne Notizen."
        ],
        "items": []
      },
      {
        "title": "13. Vorlagensprache und mehrsprachige Shops",
        "paragraphs": [
          "Jede Vorlage kann eine Vorlagensprache erhalten. Wählen Sie <strong>Alle Sprachen</strong>, wenn der Text für jede Bestellung verwendet werden darf, oder wählen Sie eine konkrete Sprache für übersetzte Kundentexte.",
          "Wenn möglich, bevorzugt das Plugin Vorlagen, die zur Bestellsprache, Benutzersprache oder zu Sprachinformationen von Mehrsprachen-Plugins passen."
        ],
        "items": [
          "Nutzen Sie eine neutrale Vorlage für interne Mitarbeiternotizen.",
          "Erstellen Sie eigene Kundenvorlagen für Deutsch, Englisch oder andere Shop-Sprachen.",
          "Testen Sie bei WPML- oder Polylang-Shops die Spracherkennung mit einer echten Testbestellung."
        ]
      },
      {
        "title": "14. Eigene Felder und Meta-Platzhalter",
        "paragraphs": [
          "Erweiterte Platzhalter können ausgewählte Bestell- oder Kundendaten aus Meta-Feldern auslesen. Verwenden Sie <code>{order_meta:meta_key}</code> für Bestelldaten und <code>{customer_meta:meta_key}</code> für Benutzer-Metadaten.",
          "Aus Sicherheitsgründen werden sensible Schlüssel wie password, token, secret, session, auth und hash blockiert. Verwenden Sie Meta-Platzhalter nur, wenn Sie genau wissen, welche Daten das Feld enthält."
        ],
        "items": [
          "Beispiel: <code>{order_meta:_tracking_number}</code> für eine Sendungsnummer aus einem Versandplugin.",
          "Beispiel: <code>{order_meta:_billing_vat_id}</code> für eine Umsatzsteuer-ID.",
          "Geben Sie interne oder sensible Felder niemals in Kundennotizen aus."
        ]
      },
      {
        "title": "15. Vorlagen duplizieren und Versionen",
        "paragraphs": [
          "Nutzen Sie die Aktion „Duplizieren“, wenn Sie eine ähnliche Vorlage mit kleinen Änderungen benötigen. Die Kopie wird als Entwurf erstellt und kann vor der Veröffentlichung geprüft werden.",
          "Über WordPress-Revisionen können frühere Texte verglichen und bei Bedarf wiederhergestellt werden."
        ],
        "items": [
          "Duplizieren Sie z. B. eine allgemeine Versandvorlage für DHL, UPS oder Abholung.",
          "Prüfen Sie die Versionen nach größeren Textänderungen.",
          "Verwenden Sie klare Titel, damit ähnliche Vorlagen nicht verwechselt werden."
        ]
      },
      {
        "title": "16. Berechtigungen",
        "paragraphs": [
          "Öffnen Sie <strong>Bestellnotiz-Vorlagen → Berechtigungen</strong>, um festzulegen, welche WordPress-Rollen Vorlagen verwalten und welche Rollen Vorlagen in Bestellungen verwenden dürfen.",
          "Administratoren behalten die notwendigen Rechte. Bei anderen Rollen sollten nur die Rechte vergeben werden, die für die tägliche Arbeit wirklich benötigt werden."
        ],
        "items": [
          "Vorlagen verwalten: erstellen, bearbeiten, löschen, importieren und exportieren.",
          "Vorlagen verwenden: Vorlage auswählen und Notiz in einer WooCommerce-Bestellung hinzufügen.",
          "Import/Export sollte nur vertrauenswürdigen Benutzern erlaubt werden."
        ]
      },
      {
        "title": "17. Import-Vorschau",
        "paragraphs": [
          "JSON-Importe zeigen vor der Übernahme eine Vorschau. Dort sehen Sie, wie viele Vorlagen neu erstellt, aktualisiert oder übersprungen werden.",
          "Bestätigen Sie den Import erst nach Prüfung der Vorschau. So wird ein versehentliches Überschreiben vorhandener Vorlagen vermieden."
        ],
        "items": [
          "Erstellen Sie vor größeren Importen immer einen Export als Sicherung.",
          "Importieren Sie nur JSON-Dateien aus vertrauenswürdigen Quellen.",
          "Testen Sie nach dem Import mindestens eine Kundennotiz und eine interne Notiz."
        ]
      },
      {
        "title": "18. E-Mail-Verhalten bei Kundennotizen",
        "paragraphs": [
          "Kundennotizen können WooCommerce-E-Mail-Benachrichtigungen auslösen, wenn die entsprechende E-Mail aktiviert ist. Das Plugin protokolliert die Erstellung der Kundennotiz getrennt von der E-Mail-Verarbeitung. Prüfen Sie vor dem Hinzufügen die bearbeitbare Vorschau und kontrollieren Sie das Ergebnis der Mailverarbeitung auf der Seite „Verlauf“."
        ],
        "items": []
      },
      {
        "title": "19. Empfohlener Arbeitsablauf",
        "paragraphs": [
          "Ein sicherer Ablauf ist: Vorlage auswählen, ersetzte Vorschau prüfen, Vorschau bei Bedarf bearbeiten, Notiztyp kontrollieren und erst dann die Notiz hinzufügen.",
          "Neue Vorlagen sollten zuerst in einer unkritischen Bestellung oder in einer Testumgebung geprüft werden, bevor sie für echte Kunden genutzt werden."
        ],
        "items": [
          "Verwenden Sie interne Notizen für rein interne Informationen.",
          "Verwenden Sie Kundennotizen nur für Texte, die der Kunde lesen darf.",
          "Prüfen Sie Platzhalter nach jeder Änderung einer Vorlage."
        ]
      },
      {
        "title": "20. Bedingungen für Vorlagen",
        "paragraphs": [
          "Vorlagenbedingungen bestimmen, ob eine Vorlage für eine bestimmte Bestellung verfügbar ist. Vorlagen können nach Bestellstatus, Zahlungsart, Versandart, Rechnungsland sowie Mindest- und Höchstbestellwert eingeschränkt werden. Alle eingetragenen Bedingungen müssen erfüllt sein."
        ],
        "items": [
          "Lassen Sie ein Feld leer, wenn diese Bedingung die Vorlage nicht einschränken soll.",
          "Verwenden Sie bei Zahlungs- und Versandarten die technischen IDs.",
          "Die Bedingungen werden in der Oberfläche und vor dem Erstellen der Notiz erneut serverseitig geprüft."
        ]
      },
      {
        "title": "21. Protokollierung der E-Mail-Verarbeitung",
        "paragraphs": [
          "Bei Kundennotizen protokolliert das Plugin, wenn WooCommerce die Kundennotiz-E-Mail als verarbeitet meldet. Technische Fehler von wp_mail werden ebenfalls erfasst. „Verarbeitet“ bestätigt nur die Übergabe an das E-Mail-System und nicht die endgültige Zustellung oder das Lesen durch den Kunden."
        ],
        "items": [
          "Prüfen Sie auf der Seite „Verlauf“ verarbeitete und fehlgeschlagene E-Mail-Ereignisse.",
          "Für verbindliche Zustellinformationen ist ein SMTP-Anbieter oder E-Mail-Protokolldienst erforderlich.",
          "Interne Notizen lösen keine Kundennotiz-E-Mail aus."
        ]
      },
      {
        "title": "22. Zentraler Verlauf",
        "paragraphs": [
          "Öffnen Sie <strong>Bestellnotiz-Vorlagen → Verlauf</strong>, um das Erstellen von Notizen, die Vorlagennutzung, verarbeitete E-Mails und E-Mail-Fehler zentral einzusehen. Soweit vorhanden werden Bestellung, Vorlage, Benutzer, Empfänger, Ereignistyp und Zeitpunkt angezeigt."
        ],
        "items": [
          "Nutzen Sie den Verlauf für Support, Nachvollziehbarkeit und Fehlersuche.",
          "Der Verlauf ist von den WooCommerce-Bestellnotizen getrennt.",
          "Angezeigt werden die neuesten 250 Einträge."
        ]
      },
      {
        "title": "23. Vorschau mit Testbestellung",
        "paragraphs": [
          "Geben Sie im Vorlageneditor im Bereich für die Testvorschau eine WooCommerce-Bestell-ID ein. Der aktuelle Editorinhalt einschließlich noch nicht gespeicherter Änderungen wird mit den Daten dieser Bestellung dargestellt, ohne eine Notiz anzulegen oder eine E-Mail zu senden."
        ],
        "items": [
          "Verwenden Sie eine Testbestellung oder eine unkritische Bestellung in einer Staging-Umgebung.",
          "Prüfen Sie fehlende Werte, Formatierung, Bedingungen und eigene Meta-Platzhalter.",
          "Sie müssen die ausgewählte Bestellung bearbeiten dürfen."
        ]
      },
      {
        "title": "24. Persönliche Favoriten und zuletzt verwendete Vorlagen",
        "paragraphs": [
          "Jeder Benutzer kann in der Bestellansicht persönliche Favoriten markieren. Zusätzlich speichert das Plugin pro Benutzer die zehn zuletzt erfolgreich verwendeten Vorlagen und zeigt sie weiter oben an. Globale Favoriten bleiben weiterhin für alle Benutzer gemeinsam."
        ],
        "items": [
          "Persönliche Favoriten verändern die Auswahl anderer Mitarbeiter nicht.",
          "Die Liste „zuletzt verwendet“ wird erst nach erfolgreichem Hinzufügen einer Notiz aktualisiert.",
          "Die persönlichen Angaben werden als WordPress-Benutzermetadaten gespeichert."
        ]
      },
      {
        "title": "25. Diagnose-Seite",
        "paragraphs": [
          "Unter <strong>Bestellnotiz-Vorlagen → Diagnose</strong> finden Sie technische Angaben wie WordPress-, PHP- und WooCommerce-Version, HPOS-Status, Status der Kundennotiz-E-Mail, Sprache, Anzahl veröffentlichter Vorlagen, Cache-Status und WP_DEBUG."
        ],
        "items": [
          "Geben Sie diese Werte bei Supportanfragen mit an.",
          "Die Seite zeigt keine Notizinhalte, Kundenadressen oder Bestellpositionen an.",
          "Entwickler können die Diagnose über einen Filter erweitern."
        ]
      },
      {
        "title": "26. Hooks und Filter für Entwickler",
        "paragraphs": [
          "Das Plugin bietet Hooks und Filter für Platzhalter, Platzhalterwerte, erlaubte Meta-Schlüssel, Vorlagenergebnisse, Bedingungen, Vorschauinhalt, endgültigen Notizinhalt, Aktionen vor und nach dem Hinzufügen, Verlaufseinträge und Diagnosewerte. Namen und Parameter sind in der readme.txt dokumentiert."
        ],
        "items": [
          "Validieren, bereinigen und maskieren Sie alle eigenen Daten.",
          "Verwenden Sie WooCommerce-Bestell-APIs statt direkter Zugriffe auf Bestelltabellen.",
          "Achten Sie bei Erweiterungen auf HPOS und die klassische Bestellspeicherung."
        ]
      }
    ]
  },
  "es_ES": {
    "title": "Ayuda detallada de Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Esta ayuda actualizada explica el flujo de trabajo completo de Mailhilfe Order Note Manager for WooCommerce: crear y dar formato a plantillas, utilizar idiomas de plantilla, marcadores de posición y campos meta, editar vistas previas, enviar notas para clientes de forma segura, configurar ajustes y permisos, revisar importaciones y mantener la compatibilidad con HPOS.",
    "sections": [
      {
        "title": "1. Qué hace el plugin",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce permite guardar como plantillas reutilizables las notas de pedido de WooCommerce que se utilizan con frecuencia. Esto evita escribir repetidamente el mismo texto y mantiene coherente la comunicación dentro del historial del pedido.",
          "Una plantilla puede prepararse como nota interna para el personal o como nota para el cliente. El tipo de nota todavía puede cambiarse al utilizar la plantilla dentro de un pedido."
        ],
        "items": [
          "Ejemplos habituales: recordatorios de pago, retrasos en la entrega, registros de llamadas, comprobaciones de direcciones y respuestas de atención al cliente.",
          "Las plantillas admiten categorías, favoritos, ordenación, contador de uso y copias de seguridad en JSON."
        ]
      },
      {
        "title": "2. Crear una plantilla nueva",
        "paragraphs": [
          "Abra <strong>Notas de pedido de Mailhilfe → Añadir nueva</strong>. Introduzca un título claro, escriba el texto de la nota en el editor y elija si el tipo de nota predeterminado debe ser interno o visible para el cliente.",
          "Utilice el título como una descripción breve de la finalidad, por ejemplo, «Recordatorio de pago» o «El cliente llamó por la entrega». Así será más fácil encontrar la plantilla en la pantalla del pedido."
        ],
        "items": [
          "Asigne una o varias categorías cuando tenga muchas plantillas.",
          "Marque como favoritas las plantillas que utiliza con frecuencia.",
          "Publique la plantilla para que esté disponible en los pedidos."
        ]
      },
      {
        "title": "3. Dar formato al texto de la plantilla",
        "paragraphs": [
          "El texto de la plantilla utiliza el editor de WordPress. Puede aplicar párrafos, negrita, cursiva, listas y enlaces. El formato se conserva al crear la nota, pero el contenido se limpia de acuerdo con las reglas de HTML seguro de WordPress.",
          "Utilice el formato con moderación en las notas para clientes. Un párrafo breve o una lista con viñetas suele ser más fácil de leer que un texto largo sin estructura."
        ],
        "items": [
          "Buen ejemplo: un saludo breve, una explicación clara y el siguiente paso.",
          "Evite abreviaturas internas en las notas para clientes.",
          "No incluya comentarios privados del personal en plantillas que puedan utilizarse como notas para clientes."
        ]
      },
      {
        "title": "4. Marcadores de posición",
        "paragraphs": [
          "Los marcadores de posición son palabras entre llaves. Se sustituyen por datos reales del pedido en la vista previa y al añadir la nota al pedido.",
          "Puede combinar texto normal y marcadores de posición. Ejemplo: <code>Hola {customer}, hemos recibido su pedido {order_number}.</code>"
        ],
        "items": [
          "Pedido: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Cliente: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Envío y pago: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Artículos y tienda: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Vista previa antes de añadir una nota",
        "paragraphs": [
          "Abra un pedido de WooCommerce y seleccione una plantilla. La vista previa muestra la nota con los marcadores de posición ya sustituidos por los datos del pedido seleccionado.",
          "Compruebe siempre la vista previa antes de crear la nota. Esto es especialmente importante cuando un marcador de posición no tiene ningún valor en el pedido, por ejemplo, si falta la empresa de envío o el número de teléfono."
        ],
        "items": [
          "Compruebe los nombres, importes, método de envío y lista de artículos.",
          "Compruebe que el tipo de nota seleccionado sea correcto.",
          "Edite primero la plantilla si el mismo texto debe mejorarse para todos los pedidos futuros."
        ]
      },
      {
        "title": "6. Notas internas y notas para clientes",
        "paragraphs": [
          "Las notas internas están destinadas al personal de la tienda y suelen utilizarse para documentación, tareas de seguimiento o historial de servicio. Las notas para clientes pueden ser visibles para el cliente y activar notificaciones por correo electrónico de WooCommerce, según los ajustes de WooCommerce.",
          "Revise cuidadosamente la vista previa editable y el tipo de nota seleccionado. Utilice notas para clientes únicamente con textos que el cliente pueda leer."
        ],
        "items": [
          "Nota interna: «El cliente llamó y confirmó la dirección de entrega».",
          "Nota para el cliente: «Su pedido se está preparando y se enviará en breve».",
          "No incluya nunca contraseñas, comentarios privados ni información exclusiva para proveedores en notas para clientes."
        ]
      },
      {
        "title": "7. Favoritos, búsqueda y ordenación",
        "paragraphs": [
          "Los favoritos ayudan a colocar las plantillas más importantes al principio de la selección. El campo de búsqueda de la pantalla del pedido permite encontrar una plantilla por título, categoría o contenido.",
          "En la lista de plantillas puede cambiar el orden mediante arrastrar y soltar. El orden guardado se utiliza cuando las plantillas se muestran en la pantalla del pedido."
        ],
        "items": [
          "Utilice favoritos para las plantillas de uso diario.",
          "Utilice categorías para grupos temáticos como Pago, Envío, Devoluciones y Soporte.",
          "Mantenga los títulos breves para que los resultados de búsqueda sigan siendo legibles."
        ]
      },
      {
        "title": "8. Importación, exportación y plantillas de demostración",
        "paragraphs": [
          "La exportación JSON crea una copia de seguridad de sus plantillas. Puede utilizarla antes de realizar cambios importantes o para transferir plantillas a otra tienda.",
          "La importación JSON puede crear plantillas y actualizar plantillas existentes con el mismo título o la misma clave interna de demostración. Las plantillas de demostración ofrecen un punto de partida rápido y se crean en el idioma activo."
        ],
        "items": [
          "Exporte las plantillas antes de realizar cambios masivos.",
          "Importe únicamente archivos JSON de una fuente de confianza.",
          "Después de importar, abra varias plantillas y compruebe el formato y los marcadores de posición."
        ]
      },
      {
        "title": "9. Permisos y roles",
        "paragraphs": [
          "El plugin utiliza capacidades separadas para gestionar plantillas y para utilizar plantillas en pedidos. Los administradores y gestores de tienda reciben automáticamente estos permisos durante la activación.",
          "Si utiliza un plugin para editar roles, puede conceder o retirar estos permisos a roles personalizados."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: crear, editar, eliminar e importar/exportar plantillas.",
          "<code>use_mh_order_note_templates</code>: utilizar plantillas en pedidos de WooCommerce.",
          "Los usuarios sin el permiso necesario no ven las funciones de administración relacionadas."
        ]
      },
      {
        "title": "10. Seguridad y compatibilidad con HPOS",
        "paragraphs": [
          "El plugin utiliza nonces de WordPress, comprobaciones de capacidades, saneamiento y escape para las acciones de administración. El contenido de las plantillas se limpia con HTML seguro de WordPress antes de guardarse o utilizarse.",
          "Los datos del pedido se leen mediante las API de pedidos de WooCommerce, en lugar de acceder directamente a las tablas de la base de datos. Esto mantiene la compatibilidad del plugin con HPOS de WooCommerce y con el almacenamiento clásico de pedidos."
        ],
        "items": [
          "Mantenga WooCommerce y WordPress actualizados.",
          "Pruebe el flujo de las notas para clientes después de cambiar los ajustes de correo electrónico de WooCommerce.",
          "Utilice un sitio de pruebas antes de importar un conjunto grande de plantillas."
        ]
      },
      {
        "title": "11. Solución de problemas",
        "paragraphs": [
          "Si las plantillas no aparecen en un pedido, compruebe que WooCommerce esté activo, que la plantilla esté publicada y que el usuario actual tenga permiso para utilizar plantillas.",
          "Si las traducciones no aparecen, compruebe el idioma del sitio y el idioma del usuario en WordPress. El plugin incluye archivos de traducción revisados para todos los idiomas compatibles, incluido el persa. Los demás idiomas deben proporcionarse mediante paquetes de idioma revisados de WordPress.org."
        ],
        "items": [
          "Después de una actualización, vacíe la caché de objetos o de los plugins de caché si todavía se muestra la pantalla de administración anterior.",
          "Si un marcador de posición no cambia, compruebe que esté escrito exactamente como aparece en la lista, incluidas las llaves.",
          "Si las notas para clientes no se envían por correo electrónico, compruebe los ajustes de correo electrónico de WooCommerce para las notificaciones de notas para clientes."
        ]
      },
      {
        "title": "12. Página de ajustes",
        "paragraphs": [
          "Abra <strong>Notas de pedido de Mailhilfe → Ajustes</strong> para elegir el tipo de nota predeterminado, el comportamiento del HTML seguro, la visualización del uso, los favoritos, las opciones de importación JSON y la coincidencia de idiomas. Utilice notas internas como valor predeterminado para trabajar de forma más segura a diario."
        ],
        "items": []
      },
      {
        "title": "13. Idioma de la plantilla y tiendas multilingües",
        "paragraphs": [
          "Cada plantilla puede tener un idioma. Elija <strong>Todos los idiomas</strong> si el mismo texto puede utilizarse en todos los pedidos o seleccione un idioma específico para los textos localizados.",
          "Cuando sea posible, el plugin puede dar preferencia a las plantillas que coincidan con el idioma del pedido, el idioma del usuario o los datos de idioma habituales de plugins multilingües."
        ],
        "items": [
          "Utilice una plantilla neutra para las notas internas del personal.",
          "Cree plantillas separadas para clientes en español, alemán, inglés u otros idiomas de la tienda.",
          "En tiendas con WPML o Polylang, pruebe la detección del idioma del pedido con un pedido de prueba real."
        ]
      },
      {
        "title": "14. Campos personalizados y marcadores de posición meta",
        "paragraphs": [
          "Los marcadores de posición avanzados pueden leer determinados campos meta del pedido o del cliente. Utilice <code>{order_meta:meta_key}</code> para los datos del pedido y <code>{customer_meta:meta_key}</code> para los datos de usuario del cliente.",
          "Por seguridad, se bloquean nombres de clave sensibles que contienen términos como password, token, secret, session, auth y hash. Utilice marcadores meta únicamente cuando sepa qué contiene el campo."
        ],
        "items": [
          "Ejemplo: <code>{order_meta:_tracking_number}</code> para un número de seguimiento guardado por un plugin de envío.",
          "Ejemplo: <code>{order_meta:_billing_vat_id}</code> para un campo de identificación fiscal.",
          "No exponga campos internos o sensibles en notas para clientes."
        ]
      },
      {
        "title": "15. Duplicar plantillas y revisiones",
        "paragraphs": [
          "Utilice la acción de duplicar cuando necesite una plantilla similar con pequeños cambios. La copia se crea como borrador para que pueda revisarse antes de publicarla.",
          "Las revisiones de plantillas permiten comparar versiones anteriores y restaurar un texto previo cuando se ha realizado un cambio por error."
        ],
        "items": [
          "Duplique una plantilla general de envío antes de crear variantes para DHL, UPS o recogida.",
          "Compruebe las revisiones después de realizar cambios importantes en el texto.",
          "Utilice títulos claros para no confundir plantillas similares."
        ]
      },
      {
        "title": "16. Página de permisos",
        "paragraphs": [
          "Abra <strong>Notas de pedido de Mailhilfe → Permisos</strong> para decidir qué roles de WordPress pueden gestionar plantillas y qué roles pueden utilizarlas en los pedidos.",
          "Los administradores conservan los permisos necesarios. Para los demás roles, conceda únicamente los permisos que requieran para su trabajo diario."
        ],
        "items": [
          "Gestionar plantillas: crear, editar, eliminar, importar y exportar plantillas.",
          "Utilizar plantillas: seleccionar una plantilla y añadir una nota en un pedido de WooCommerce.",
          "Conceda permisos de importación y exportación únicamente a usuarios de confianza."
        ]
      },
      {
        "title": "17. Vista previa de la importación",
        "paragraphs": [
          "Las importaciones JSON muestran ahora una vista previa antes de aplicar los cambios. La vista previa indica cuántas plantillas se crearán, actualizarán u omitirán.",
          "Confirme la importación únicamente después de comprobar la vista previa. Esto evita sobrescribir accidentalmente plantillas existentes."
        ],
        "items": [
          "Cree una copia de seguridad mediante exportación antes de importar un conjunto grande.",
          "Importe únicamente archivos JSON de una fuente de confianza.",
          "Después de importar, pruebe al menos una nota para el cliente y una nota interna."
        ]
      },
      {
        "title": "18. Comportamiento del correo electrónico de las notas para clientes",
        "paragraphs": [
          "Las notas para clientes pueden activar notificaciones por correo electrónico de WooCommerce cuando el correo correspondiente está activado. El plugin registra la creación de la nota para el cliente por separado del procesamiento del correo electrónico. Revise la vista previa editable antes de añadir la nota y utilice la página Historial para comprobar el resultado del gestor de correo."
        ],
        "items": []
      },
      {
        "title": "19. Flujo de trabajo recomendado",
        "paragraphs": [
          "Un flujo de trabajo diario seguro consiste en seleccionar una plantilla, revisar la vista previa con los datos sustituidos, editarla si es necesario, verificar el tipo de nota y, por último, añadir la nota.",
          "Pruebe primero las plantillas nuevas en un pedido no crítico o en una tienda de pruebas antes de utilizarlas con clientes reales."
        ],
        "items": [
          "Utilice notas internas para información destinada únicamente al personal.",
          "Utilice notas para clientes únicamente con mensajes que puedan enviarse al cliente.",
          "Revise los marcadores de posición cada vez que cambie una plantilla."
        ]
      },
      {
        "title": "20. Condiciones de las plantillas",
        "paragraphs": [
          "Las condiciones de una plantilla determinan si está disponible para un pedido concreto. Puede restringir las plantillas por estado del pedido, método de pago, método de envío, país de facturación y total mínimo o máximo del pedido. Todas las condiciones configuradas deben cumplirse."
        ],
        "items": [
          "Deje un campo vacío cuando esa condición no deba restringir la plantilla.",
          "Utilice los identificadores técnicos de los métodos de pago y de envío.",
          "Las condiciones se comprueban en la interfaz y de nuevo en el servidor antes de crear una nota."
        ]
      },
      {
        "title": "21. Registro del procesamiento de correo electrónico",
        "paragraphs": [
          "Para las notas para clientes, el plugin registra cuándo WooCommerce informa de que el correo electrónico de la nota se ha procesado y también registra errores técnicos de wp_mail. Un evento procesado confirma que WordPress/WooCommerce entregó el mensaje al sistema de correo, pero no demuestra la entrega final ni que el cliente lo haya leído."
        ],
        "items": [
          "Consulte la página Historial para ver los eventos de correo procesados y fallidos.",
          "Utilice un proveedor SMTP o un servicio de registro de correo cuando necesite información definitiva sobre la entrega.",
          "Las notas internas no activan un correo electrónico de nota para el cliente."
        ]
      },
      {
        "title": "22. Historial central",
        "paragraphs": [
          "Abra <strong>Notas de pedido de Mailhilfe → Historial</strong> para revisar la creación reciente de notas, el uso de plantillas, el procesamiento de correos y los fallos de envío. Cuando están disponibles, las entradas incluyen el pedido, la plantilla, el usuario, el destinatario, el tipo de evento y la hora."
        ],
        "items": [
          "Utilice el historial para soporte, auditoría y solución de problemas.",
          "El historial está separado de las notas de pedido de WooCommerce.",
          "La página muestra las 250 entradas más recientes."
        ]
      },
      {
        "title": "23. Vista previa con pedido de prueba",
        "paragraphs": [
          "En el editor de plantillas, introduzca un ID de pedido de WooCommerce en el área de vista previa de prueba. El contenido actual del editor, incluidos los cambios sin guardar, se muestra con los datos de ese pedido sin crear una nota ni enviar un correo electrónico."
        ],
        "items": [
          "Utilice un pedido de un sitio de pruebas o un pedido de prueba no crítico.",
          "Compruebe valores ausentes, formato, condiciones y marcadores de posición meta personalizados.",
          "Debe tener permiso para editar el pedido seleccionado."
        ]
      },
      {
        "title": "24. Favoritos personales y plantillas utilizadas recientemente",
        "paragraphs": [
          "Cada administrador puede marcar favoritos personales en la pantalla del pedido. El plugin también guarda las diez plantillas utilizadas más recientemente por cada usuario y las sitúa en una posición superior de la selección. Los favoritos globales siguen compartiéndose con todos los usuarios."
        ],
        "items": [
          "Los favoritos personales no modifican la lista de otros usuarios.",
          "La lista reciente se actualiza únicamente después de añadir una nota correctamente.",
          "Los datos personales se guardan como metadatos de usuario de WordPress."
        ]
      },
      {
        "title": "25. Página de diagnóstico",
        "paragraphs": [
          "Abra <strong>Notas de pedido de Mailhilfe → Diagnóstico</strong> para ver información técnica como las versiones de WordPress, PHP y WooCommerce, el estado de HPOS, el estado del correo de notas para clientes, la configuración regional, el número de plantillas publicadas, el estado de la caché y WP_DEBUG."
        ],
        "items": [
          "Copie los valores de diagnóstico cuando solicite soporte.",
          "La página no muestra el contenido de las notas de pedido ni las direcciones de los clientes.",
          "Los desarrolladores pueden añadir filas mediante el filtro de diagnóstico."
        ]
      },
      {
        "title": "26. Acciones y filtros para desarrolladores",
        "paragraphs": [
          "El plugin proporciona acciones y filtros para marcadores de posición, valores de marcadores, claves meta permitidas, resultados de plantillas, condiciones, contenido de la vista previa, contenido final de la nota, acciones antes y después de añadir una nota, registros del historial y diagnóstico. Los nombres y parámetros están documentados en readme.txt."
        ],
        "items": [
          "Valide, sanee y escape todos los datos personalizados.",
          "Utilice las API de pedidos de WooCommerce en lugar de acceder directamente a las tablas de pedidos.",
          "Mantenga las extensiones personalizadas compatibles tanto con HPOS como con el almacenamiento clásico de pedidos."
        ]
      }
    ]
  },
  "fr_FR": {
    "title": "Aide détaillée de Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Cette aide mise à jour décrit l’ensemble du fonctionnement de Mailhilfe Order Note Manager for WooCommerce : création et mise en forme des modèles, langues des modèles, variables et métavariables, modification des aperçus, envoi sécurisé des notes au client, réglages, droits, aperçu des importations et compatibilité HPOS.",
    "sections": [
      {
        "title": "1. Fonctionnement de l’extension",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce vous permet d’enregistrer les notes de commande WooCommerce fréquemment utilisées sous forme de modèles réutilisables. Vous évitez ainsi de saisir plusieurs fois le même texte et conservez une communication cohérente dans l’historique de la commande.",
          "Un modèle peut être préparé comme note interne destinée à l’équipe ou comme note au client. Vous pouvez encore modifier le type de note lorsque vous utilisez le modèle dans une commande."
        ],
        "items": [
          "Exemples courants : rappels de paiement, retards de livraison, comptes rendus d’appels, vérifications d’adresse et réponses du service client.",
          "Les modèles prennent en charge les catégories, les favoris, le tri, un compteur d’utilisation et la sauvegarde au format JSON."
        ]
      },
      {
        "title": "2. Créer un nouveau modèle",
        "paragraphs": [
          "Ouvrez <strong>Notes de commande Mailhilfe → Ajouter</strong>. Saisissez un titre explicite, rédigez le texte de la note dans l’éditeur et choisissez si le type de note par défaut doit être interne ou destiné au client.",
          "Utilisez le titre comme une brève description de l’objectif, par exemple « Rappel de paiement » ou « Appel du client au sujet de la livraison ». Le modèle sera ainsi plus facile à retrouver dans l’écran de commande."
        ],
        "items": [
          "Attribuez une ou plusieurs catégories si vous utilisez de nombreux modèles.",
          "Ajoutez aux favoris les modèles que vous utilisez souvent.",
          "Publiez le modèle pour le rendre disponible dans les commandes."
        ]
      },
      {
        "title": "3. Mettre en forme le texte du modèle",
        "paragraphs": [
          "Le texte du modèle utilise l’éditeur WordPress. Vous pouvez mettre le texte en paragraphes, en gras ou en italique, créer des listes et ajouter des liens. La mise en forme est conservée lors de la création de la note, mais le contenu est nettoyé selon les règles HTML sécurisées de WordPress.",
          "Utilisez la mise en forme avec modération dans les notes au client. Un court paragraphe ou une liste à puces est généralement plus lisible qu’un long texte non structuré."
        ],
        "items": [
          "Bon exemple : une courte formule d’accueil, une explication claire et l’étape suivante.",
          "Évitez les abréviations internes dans les notes au client.",
          "N’insérez pas de commentaires privés de l’équipe dans des modèles susceptibles d’être utilisés comme notes au client."
        ]
      },
      {
        "title": "4. Variables",
        "paragraphs": [
          "Les variables sont des mots placés entre accolades. Elles sont remplacées par les données réelles de la commande dans l’aperçu et lors de l’ajout de la note à la commande.",
          "Vous pouvez combiner du texte normal et des variables. Exemple : <code>Bonjour {customer}, nous avons bien reçu votre commande {order_number}.</code>"
        ],
        "items": [
          "Commande : <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Client : <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Livraison et paiement : <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Articles et boutique : <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Vérifier l’aperçu avant d’ajouter une note",
        "paragraphs": [
          "Ouvrez une commande WooCommerce et sélectionnez un modèle. L’aperçu affiche la note après remplacement des variables par les données de la commande sélectionnée.",
          "Vérifiez toujours l’aperçu avant de créer la note. Cette vérification est particulièrement importante lorsqu’une variable ne possède aucune valeur dans la commande, par exemple si la société de livraison ou le numéro de téléphone est manquant."
        ],
        "items": [
          "Vérifiez les noms, les montants, le mode de livraison et la liste des articles.",
          "Vérifiez que le type de note sélectionné est correct.",
          "Modifiez d’abord le modèle si le même texte doit être amélioré pour toutes les commandes futures."
        ]
      },
      {
        "title": "6. Notes internes et notes au client",
        "paragraphs": [
          "Les notes internes sont destinées à l’équipe de la boutique et servent généralement à la documentation, aux tâches de suivi ou à l’historique du service client. Les notes au client peuvent être visibles par le client et déclencher des notifications par e-mail de WooCommerce, selon vos réglages WooCommerce.",
          "Vérifiez soigneusement l’aperçu modifiable et le type de note sélectionné. N’utilisez une note au client que pour un texte que le client est autorisé à lire."
        ],
        "items": [
          "Note interne : « Le client a appelé, l’adresse de livraison a été confirmée. »",
          "Note au client : « Votre commande est en cours de préparation et sera bientôt expédiée. »",
          "Ne placez jamais de mots de passe, de commentaires privés ou d’informations réservées aux fournisseurs dans une note au client."
        ]
      },
      {
        "title": "7. Favoris, recherche et tri",
        "paragraphs": [
          "Les favoris permettent de placer les modèles les plus importants en haut de la sélection. Le champ de recherche de l’écran de commande permet de retrouver un modèle selon son titre, sa catégorie ou son contenu.",
          "Dans la liste des modèles, vous pouvez modifier l’ordre par glisser-déposer. L’ordre enregistré est utilisé lors de l’affichage des modèles dans l’écran de commande."
        ],
        "items": [
          "Utilisez les favoris pour les modèles employés quotidiennement.",
          "Utilisez les catégories pour regrouper les sujets comme Paiement, Livraison, Retours et Assistance.",
          "Conservez des titres courts afin que les résultats de recherche restent lisibles."
        ]
      },
      {
        "title": "8. Importation, exportation et modèles de démonstration",
        "paragraphs": [
          "L’exportation JSON crée une sauvegarde de vos modèles. Vous pouvez l’utiliser avant des modifications importantes ou pour transférer les modèles vers une autre boutique.",
          "L’importation JSON peut créer des modèles et mettre à jour les modèles existants portant le même titre ou la même clé interne de démonstration. Les modèles de démonstration constituent un point de départ rapide et sont créés dans la langue active."
        ],
        "items": [
          "Effectuez une exportation avant toute modification en masse.",
          "Importez uniquement des fichiers JSON provenant d’une source fiable.",
          "Après l’importation, ouvrez quelques modèles et vérifiez la mise en forme ainsi que les variables."
        ]
      },
      {
        "title": "9. Droits et rôles",
        "paragraphs": [
          "L’extension utilise des capacités distinctes pour gérer les modèles et pour les utiliser dans les commandes. Les administrateurs et les responsables de boutique reçoivent automatiquement ces droits lors de l’activation.",
          "Si vous utilisez une extension de gestion des rôles, vous pouvez accorder ou retirer ces droits aux rôles personnalisés."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code> : créer, modifier, supprimer, importer et exporter les modèles.",
          "<code>use_mh_order_note_templates</code> : utiliser les modèles dans les commandes WooCommerce.",
          "Les utilisateurs qui ne disposent pas du droit nécessaire ne voient pas les fonctions d’administration correspondantes."
        ]
      },
      {
        "title": "10. Sécurité et compatibilité HPOS",
        "paragraphs": [
          "L’extension utilise les nonces WordPress, la vérification des capacités, le nettoyage et l’échappement pour les actions d’administration. Le contenu des modèles est nettoyé avec le HTML sécurisé de WordPress avant d’être enregistré ou utilisé.",
          "Les données de commande sont lues au moyen des API de commande WooCommerce et non par un accès direct aux tables de la base de données. L’extension reste ainsi compatible avec HPOS de WooCommerce et le stockage classique des commandes."
        ],
        "items": [
          "Maintenez WooCommerce et WordPress à jour.",
          "Testez le fonctionnement des notes au client après toute modification des réglages d’e-mail de WooCommerce.",
          "Utilisez un site de préproduction avant d’importer un grand ensemble de modèles."
        ]
      },
      {
        "title": "11. Dépannage",
        "paragraphs": [
          "Si les modèles n’apparaissent pas dans une commande, vérifiez que WooCommerce est actif, que le modèle est publié et que l’utilisateur actuel possède le droit d’utiliser les modèles.",
          "Si les traductions n’apparaissent pas, vérifiez la langue du site et la langue de l’utilisateur dans WordPress. L’extension contient des fichiers de remplacement vérifiés pour toutes les langues prises en charge, y compris le persan. Les autres langues doivent être fournies par des packs linguistiques WordPress.org vérifiés."
        ],
        "items": [
          "Après une mise à jour, videz les caches d’objets ou des extensions de cache si l’ancien écran d’administration reste affiché.",
          "Si une variable reste inchangée, vérifiez qu’elle est écrite exactement comme dans la liste, accolades comprises.",
          "Si les notes au client ne sont pas envoyées par e-mail, vérifiez les réglages d’e-mail WooCommerce relatifs aux notifications de note au client."
        ]
      },
      {
        "title": "12. Page des réglages",
        "paragraphs": [
          "Ouvrez <strong>Notes de commande Mailhilfe → Réglages</strong> pour choisir le type de note par défaut, le comportement du HTML sécurisé, l’affichage de l’utilisation, les favoris, les options d’importation JSON et la correspondance des langues. Utilisez les notes internes comme valeur par défaut pour un travail quotidien plus sûr."
        ],
        "items": []
      },
      {
        "title": "13. Langue des modèles et boutiques multilingues",
        "paragraphs": [
          "Chaque modèle peut posséder une langue. Choisissez <strong>Toutes les langues</strong> si le même texte peut être utilisé pour toutes les commandes, ou sélectionnez une langue précise pour les textes localisés.",
          "Lorsque les informations sont disponibles, l’extension peut privilégier les modèles correspondant à la langue de la commande, à la langue de l’utilisateur ou aux données linguistiques courantes fournies par les extensions multilingues."
        ],
        "items": [
          "Utilisez un modèle neutre pour les notes internes de l’équipe.",
          "Créez des modèles distincts destinés au client pour le français, l’allemand, l’anglais ou les autres langues de la boutique.",
          "Pour les boutiques WPML ou Polylang, testez la détection de la langue de la commande avec une véritable commande de test."
        ]
      },
      {
        "title": "14. Champs personnalisés et métavariables",
        "paragraphs": [
          "Les variables avancées peuvent lire certains champs de métadonnées de la commande ou du client. Utilisez <code>{order_meta:meta_key}</code> pour les données de commande et <code>{customer_meta:meta_key}</code> pour les données utilisateur du client.",
          "Pour des raisons de sécurité, les noms de clés sensibles contenant notamment password, token, secret, session, auth ou hash sont bloqués. Utilisez des métavariables uniquement lorsque vous connaissez le contenu du champ."
        ],
        "items": [
          "Exemple : <code>{order_meta:_tracking_number}</code> pour un numéro de suivi enregistré par une extension de livraison.",
          "Exemple : <code>{order_meta:_billing_vat_id}</code> pour un champ de numéro de TVA.",
          "N’affichez pas de champs internes ou sensibles dans les notes au client."
        ]
      },
      {
        "title": "15. Dupliquer les modèles et utiliser les révisions",
        "paragraphs": [
          "Utilisez l’action de duplication lorsqu’un nouveau modèle doit reprendre un modèle existant avec quelques modifications. La copie est créée comme brouillon afin de pouvoir être vérifiée avant sa publication.",
          "Les révisions de modèles vous permettent de comparer les versions antérieures et de restaurer un ancien texte lorsqu’une modification a été effectuée par erreur."
        ],
        "items": [
          "Dupliquez un modèle de livraison général avant de créer des variantes pour DHL, UPS ou le retrait sur place.",
          "Consultez les révisions après des modifications importantes du texte.",
          "Utilisez des titres explicites pour éviter de confondre des modèles similaires."
        ]
      },
      {
        "title": "16. Page des droits",
        "paragraphs": [
          "Ouvrez <strong>Notes de commande Mailhilfe → Droits</strong> pour définir quels rôles WordPress peuvent gérer les modèles et quels rôles peuvent les utiliser dans les commandes.",
          "Les administrateurs conservent les droits nécessaires. Pour les autres rôles, accordez uniquement les droits requis pour les tâches quotidiennes."
        ],
        "items": [
          "Gérer les modèles : créer, modifier, supprimer, importer et exporter les modèles.",
          "Utiliser les modèles : sélectionner un modèle et ajouter une note dans une commande WooCommerce.",
          "N’accordez les droits d’importation et d’exportation qu’aux utilisateurs de confiance."
        ]
      },
      {
        "title": "17. Aperçu de l’importation",
        "paragraphs": [
          "Les importations JSON affichent désormais un aperçu avant l’application des modifications. L’aperçu indique combien de modèles seront créés, mis à jour ou ignorés.",
          "Ne confirmez l’importation qu’après avoir vérifié l’aperçu. Vous évitez ainsi d’écraser involontairement des modèles existants."
        ],
        "items": [
          "Créez une sauvegarde par exportation avant d’importer un ensemble important.",
          "Importez uniquement des fichiers JSON provenant d’une source fiable.",
          "Après l’importation, testez au moins une note au client et une note interne."
        ]
      },
      {
        "title": "18. Envoi des e-mails de notes au client",
        "paragraphs": [
          "Les notes au client peuvent déclencher des notifications par e-mail de WooCommerce lorsque l’e-mail correspondant est activé. L’extension enregistre la création de la note au client séparément du traitement de l’e-mail. Vérifiez l’aperçu modifiable avant d’ajouter la note et utilisez la page Historique pour contrôler le résultat du gestionnaire d’e-mails."
        ],
        "items": []
      },
      {
        "title": "19. Méthode de travail recommandée",
        "paragraphs": [
          "Une méthode de travail quotidienne sûre consiste à sélectionner un modèle, vérifier l’aperçu après remplacement des variables, modifier l’aperçu si nécessaire, contrôler le type de note, puis ajouter la note.",
          "Pour les nouveaux modèles, effectuez d’abord un test dans une commande sans importance ou sur une boutique de préproduction avant de les utiliser avec de véritables clients."
        ],
        "items": [
          "Utilisez les notes internes pour les informations réservées à l’équipe.",
          "Utilisez les notes au client uniquement pour les messages susceptibles d’être envoyés au client.",
          "Vérifiez les variables chaque fois qu’un modèle est modifié."
        ]
      },
      {
        "title": "20. Conditions des modèles",
        "paragraphs": [
          "Les conditions d’un modèle déterminent s’il est disponible pour une commande donnée. Vous pouvez limiter les modèles selon l’état de la commande, le mode de paiement, le mode de livraison, le pays de facturation ainsi que le montant minimal ou maximal de la commande. Toutes les conditions définies doivent être remplies."
        ],
        "items": [
          "Laissez un champ vide si cette condition ne doit pas limiter le modèle.",
          "Utilisez les identifiants techniques des modes de paiement et de livraison.",
          "Les conditions sont vérifiées dans l’interface, puis de nouveau sur le serveur avant la création d’une note."
        ]
      },
      {
        "title": "21. Journal du traitement des e-mails",
        "paragraphs": [
          "Pour les notes au client, l’extension enregistre le moment où WooCommerce indique que l’e-mail de note au client a été traité, ainsi que les erreurs techniques de wp_mail. Un événement traité confirme que WordPress/WooCommerce a transmis le message au système de messagerie ; il ne prouve ni la livraison finale ni la lecture du message par le client."
        ],
        "items": [
          "Consultez la page Historique pour connaître les événements d’e-mail traités et échoués.",
          "Utilisez un fournisseur SMTP ou un service de journalisation des e-mails si vous avez besoin d’informations fiables sur la livraison.",
          "Les notes internes ne déclenchent pas d’e-mail de note au client."
        ]
      },
      {
        "title": "22. Historique central",
        "paragraphs": [
          "Ouvrez <strong>Notes de commande Mailhilfe → Historique</strong> pour consulter les créations récentes de notes, l’utilisation des modèles, le traitement des e-mails et les échecs d’envoi. Lorsque les informations sont disponibles, les entrées indiquent la commande, le modèle, l’utilisateur, le destinataire, le type d’événement et l’heure."
        ],
        "items": [
          "Utilisez l’historique pour l’assistance, l’audit et le dépannage.",
          "L’historique est distinct des notes de commande WooCommerce.",
          "La page affiche les 250 entrées les plus récentes."
        ]
      },
      {
        "title": "23. Aperçu avec une commande de test",
        "paragraphs": [
          "Dans l’éditeur de modèle, saisissez l’identifiant d’une commande WooCommerce dans la zone d’aperçu de test. Le contenu actuel de l’éditeur, y compris les modifications non enregistrées, est rendu avec les données de cette commande sans créer de note ni envoyer d’e-mail."
        ],
        "items": [
          "Utilisez une commande de préproduction ou une commande de test sans importance.",
          "Vérifiez les valeurs manquantes, la mise en forme, les conditions et les variables de métadonnées personnalisées.",
          "Vous devez disposer du droit de modifier la commande sélectionnée."
        ]
      },
      {
        "title": "24. Favoris personnels et modèles récemment utilisés",
        "paragraphs": [
          "Chaque administrateur peut ajouter des favoris personnels dans l’écran de commande. L’extension mémorise également les dix modèles les plus récemment utilisés par chaque utilisateur et les place plus haut dans la sélection. Les favoris globaux restent partagés entre tous les utilisateurs."
        ],
        "items": [
          "Les favoris personnels ne modifient pas la liste d’un autre utilisateur.",
          "La liste récente est mise à jour uniquement après l’ajout réussi d’une note.",
          "Les données personnelles sont enregistrées comme métadonnées utilisateur WordPress."
        ]
      },
      {
        "title": "25. Page de diagnostic",
        "paragraphs": [
          "Ouvrez <strong>Notes de commande Mailhilfe → Diagnostic</strong> pour consulter des informations techniques telles que les versions de WordPress, PHP et WooCommerce, l’état HPOS, l’état de l’e-mail de note au client, les paramètres régionaux, le nombre de modèles publiés, l’état du cache et WP_DEBUG."
        ],
        "items": [
          "Copiez les valeurs de diagnostic lorsque vous demandez de l’aide.",
          "La page n’affiche ni le contenu des notes de commande ni les adresses des clients.",
          "Les développeurs peuvent ajouter des lignes à l’aide du filtre de diagnostic."
        ]
      },
      {
        "title": "26. Actions et filtres pour les développeurs",
        "paragraphs": [
          "L’extension fournit des actions et des filtres pour les variables, leurs valeurs, les clés de métadonnées autorisées, les résultats des modèles, les conditions, le contenu de l’aperçu, le contenu final de la note, les actions avant et après l’ajout d’une note, les entrées d’historique et le diagnostic. Les noms des points d’accroche et leurs paramètres sont documentés dans readme.txt."
        ],
        "items": [
          "Validez, nettoyez et échappez toutes les données personnalisées.",
          "Utilisez les API de commande WooCommerce plutôt qu’un accès direct aux tables de commandes.",
          "Maintenez la compatibilité de vos extensions personnalisées avec HPOS et le stockage classique des commandes."
        ]
      }
    ]
  },
  "ru_RU": {
    "title": "Подробная справка по Mailhilfe Order Note Manager for WooCommerce",
    "intro": "В этой обновлённой справке описан полный рабочий процесс Mailhilfe Order Note Manager for WooCommerce: создание и форматирование шаблонов, языки шаблонов, заполнители и метазаполнители, редактирование предпросмотра, безопасное добавление примечаний для клиентов, настройки, права доступа, предпросмотр импорта и совместимость с HPOS.",
    "sections": [
      {
        "title": "1. Назначение плагина",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce позволяет сохранять часто используемые примечания к заказам WooCommerce в виде повторно используемых шаблонов. Это избавляет от многократного ввода одинакового текста и помогает поддерживать единообразную коммуникацию в истории заказа.",
          "Шаблон можно подготовить как внутреннее примечание для сотрудников или как примечание для клиента. При использовании шаблона в заказе тип примечания можно изменить."
        ],
        "items": [
          "Типичные примеры: напоминания об оплате, задержки доставки, записи о телефонных разговорах, проверка адреса и ответы службы поддержки.",
          "Шаблоны поддерживают категории, избранное, сортировку, счётчик использования и резервное копирование в JSON."
        ]
      },
      {
        "title": "2. Создание нового шаблона",
        "paragraphs": [
          "Откройте <strong>Примечания Mailhilfe → Добавить</strong>. Введите понятный заголовок, напишите текст примечания в редакторе и выберите, должен ли тип по умолчанию быть внутренним или предназначенным для клиента.",
          "Используйте заголовок как краткое описание назначения, например «Напоминание об оплате» или «Клиент звонил по поводу доставки». Так шаблон будет проще найти на экране заказа."
        ],
        "items": [
          "Если шаблонов много, назначьте одну или несколько категорий.",
          "Добавьте часто используемые шаблоны в избранное.",
          "Опубликуйте шаблон, чтобы он стал доступен в заказах."
        ]
      },
      {
        "title": "3. Форматирование текста шаблона",
        "paragraphs": [
          "Для текста шаблона используется редактор WordPress. Можно создавать абзацы, использовать полужирное и курсивное начертание, списки и ссылки. Форматирование сохраняется при создании примечания, а содержимое очищается по правилам безопасного HTML WordPress.",
          "Используйте форматирование в примечаниях для клиентов умеренно. Короткий абзац или маркированный список обычно читается лучше, чем длинный неструктурированный текст."
        ],
        "items": [
          "Хороший пример: короткое приветствие, одно понятное объяснение и один следующий шаг.",
          "Не используйте внутренние сокращения в примечаниях для клиентов.",
          "Не добавляйте личные комментарии сотрудников в шаблоны, которые могут использоваться как примечания для клиентов."
        ]
      },
      {
        "title": "4. Заполнители",
        "paragraphs": [
          "Заполнители — это слова в фигурных скобках. В предпросмотре и при добавлении примечания к заказу они заменяются реальными данными заказа.",
          "Обычный текст можно сочетать с заполнителями. Пример: <code>Здравствуйте, {customer}! Мы получили ваш заказ {order_number}.</code>"
        ],
        "items": [
          "Заказ: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Клиент: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Доставка и оплата: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Товары и магазин: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Предпросмотр перед добавлением примечания",
        "paragraphs": [
          "Откройте заказ WooCommerce и выберите шаблон. В предпросмотре отображается примечание, в котором заполнители уже заменены данными выбранного заказа.",
          "Всегда проверяйте предпросмотр перед созданием примечания. Это особенно важно, если для заполнителя в заказе нет значения, например отсутствует транспортная компания или номер телефона."
        ],
        "items": [
          "Проверьте имена, суммы, способ доставки и список товаров.",
          "Убедитесь, что выбран правильный тип примечания.",
          "Если текст нужно улучшить для всех будущих заказов, сначала измените сам шаблон."
        ]
      },
      {
        "title": "6. Внутренние примечания и примечания для клиентов",
        "paragraphs": [
          "Внутренние примечания предназначены для сотрудников магазина и обычно используются для документирования, последующих задач или истории обслуживания. Примечания для клиентов могут быть видны клиенту и, в зависимости от настроек WooCommerce, могут запускать email-уведомления.",
          "Внимательно проверяйте редактируемый предпросмотр и выбранный тип примечания. Используйте примечание для клиента только для текста, который клиенту разрешено видеть."
        ],
        "items": [
          "Внутреннее примечание: «Клиент позвонил, адрес доставки подтверждён».",
          "Примечание для клиента: «Ваш заказ готовится и вскоре будет отправлен».",
          "Никогда не добавляйте в примечания для клиентов пароли, личные комментарии или информацию только для поставщиков."
        ]
      },
      {
        "title": "7. Избранное, поиск и сортировка",
        "paragraphs": [
          "Избранное помогает разместить наиболее важные шаблоны в верхней части списка. Поле поиска на экране заказа позволяет находить шаблон по заголовку, категории или содержимому.",
          "В списке шаблонов порядок можно изменить перетаскиванием. Сохранённый порядок используется при отображении шаблонов на экране заказа."
        ],
        "items": [
          "Добавляйте в избранное шаблоны, используемые ежедневно.",
          "Используйте категории для групп по темам, например «Оплата», «Доставка», «Возвраты» и «Поддержка».",
          "Делайте заголовки короткими, чтобы результаты поиска оставались удобочитаемыми."
        ]
      },
      {
        "title": "8. Импорт, экспорт и демонстрационные шаблоны",
        "paragraphs": [
          "Экспорт JSON создаёт резервную копию шаблонов. Его можно использовать перед крупными изменениями или для переноса шаблонов в другой магазин.",
          "Импорт JSON может создавать шаблоны и обновлять существующие шаблоны с тем же заголовком или внутренним демонстрационным ключом. Демонстрационные шаблоны служат быстрой отправной точкой и создаются на активном языке."
        ],
        "items": [
          "Выполняйте экспорт перед массовыми изменениями.",
          "Импортируйте только JSON-файлы из надёжного источника.",
          "После импорта откройте несколько шаблонов и проверьте форматирование и заполнители."
        ]
      },
      {
        "title": "9. Права доступа и роли",
        "paragraphs": [
          "Плагин использует отдельные права для управления шаблонами и их применения в заказах. Администраторы и менеджеры магазина получают эти права автоматически при активации.",
          "Если используется плагин редактора ролей, эти права можно выдавать или отзывать для пользовательских ролей."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: создание, изменение, удаление, импорт и экспорт шаблонов.",
          "<code>use_mh_order_note_templates</code>: использование шаблонов в заказах WooCommerce.",
          "Пользователи без необходимых прав не видят соответствующие функции панели управления."
        ]
      },
      {
        "title": "10. Безопасность и совместимость с HPOS",
        "paragraphs": [
          "Для действий в панели управления плагин использует nonce WordPress, проверку прав, очистку и экранирование данных. Перед сохранением или использованием содержимое шаблона очищается по правилам безопасного HTML WordPress.",
          "Данные заказа считываются через API заказов WooCommerce, а не напрямую из таблиц базы данных. Благодаря этому плагин совместим с HPOS и классическим хранилищем заказов."
        ],
        "items": [
          "Своевременно обновляйте WooCommerce и WordPress.",
          "После изменения настроек email WooCommerce проверьте процесс добавления примечаний для клиентов.",
          "Перед импортом большого набора шаблонов используйте тестовый сайт."
        ]
      },
      {
        "title": "11. Устранение неполадок",
        "paragraphs": [
          "Если шаблоны не отображаются в заказе, убедитесь, что WooCommerce активен, шаблон опубликован, а у текущего пользователя есть право использовать шаблоны.",
          "Если перевод не отображается, проверьте язык сайта и язык пользователя в WordPress. Плагин содержит проверенные встроенные переводы для всех поддерживаемых языков, включая персидский. Для других языков следует использовать проверенные языковые пакеты WordPress.org."
        ],
        "items": [
          "Если после обновления по-прежнему отображается старая панель управления, очистите объектный кеш или кеш плагина.",
          "Если заполнитель не заменяется, проверьте его точное написание, включая фигурные скобки.",
          "Если примечания для клиентов не отправляются по email, проверьте настройки уведомлений WooCommerce для примечаний клиенту."
        ]
      },
      {
        "title": "12. Страница настроек",
        "paragraphs": [
          "Откройте <strong>Примечания Mailhilfe → Настройки</strong>, чтобы выбрать тип примечания по умолчанию, правила безопасного HTML, отображение использования, избранное, параметры импорта JSON и сопоставление языков. Для более безопасной повседневной работы используйте внутренние примечания по умолчанию."
        ],
        "items": []
      },
      {
        "title": "13. Язык шаблонов и многоязычные магазины",
        "paragraphs": [
          "Каждому шаблону можно назначить язык. Выберите <strong>Все языки</strong>, если один и тот же текст подходит для всех заказов, или укажите конкретный язык для локализованного текста.",
          "Если данные доступны, плагин может отдавать предпочтение шаблонам, соответствующим языку заказа, языку пользователя или языковым данным распространённых многоязычных плагинов."
        ],
        "items": [
          "Используйте один нейтральный шаблон для внутренних примечаний сотрудников.",
          "Создавайте отдельные клиентские шаблоны для русского, немецкого, английского и других языков магазина.",
          "В магазинах с WPML или Polylang проверьте определение языка заказа на реальном тестовом заказе."
        ]
      },
      {
        "title": "14. Пользовательские поля и метазаполнители",
        "paragraphs": [
          "Расширенные заполнители могут считывать выбранные метаполя заказа или клиента. Используйте <code>{order_meta:meta_key}</code> для данных заказа и <code>{customer_meta:meta_key}</code> для пользовательских данных клиента.",
          "В целях безопасности блокируются чувствительные названия ключей, содержащие, например, password, token, secret, session, auth или hash. Используйте метазаполнители только тогда, когда известно содержимое поля."
        ],
        "items": [
          "Пример: <code>{order_meta:_tracking_number}</code> для номера отслеживания, сохранённого плагином доставки.",
          "Пример: <code>{order_meta:_billing_vat_id}</code> для поля идентификатора НДС.",
          "Не выводите внутренние или конфиденциальные поля в примечаниях для клиентов."
        ]
      },
      {
        "title": "15. Дублирование шаблонов и редакции",
        "paragraphs": [
          "Используйте дублирование, если нужен похожий шаблон с небольшими изменениями. Копия создаётся как черновик, чтобы её можно было проверить перед публикацией.",
          "Редакции шаблонов позволяют сравнивать предыдущие версии и восстанавливать прежний текст, если изменение было внесено по ошибке."
        ],
        "items": [
          "Дублируйте общий шаблон доставки перед созданием вариантов для DHL, UPS или самовывоза.",
          "После крупных изменений текста проверяйте редакции.",
          "Используйте понятные заголовки, чтобы не путать похожие шаблоны."
        ]
      },
      {
        "title": "16. Страница прав доступа",
        "paragraphs": [
          "Откройте <strong>Примечания Mailhilfe → Права доступа</strong>, чтобы определить, какие роли WordPress могут управлять шаблонами, а какие — использовать их в заказах.",
          "Администраторы сохраняют необходимые права. Другим ролям выдавайте только те права, которые нужны для повседневных задач."
        ],
        "items": [
          "Управление шаблонами: создание, изменение, удаление, импорт и экспорт шаблонов.",
          "Использование шаблонов: выбор шаблона и добавление примечания в заказ WooCommerce.",
          "Предоставляйте права на импорт и экспорт только доверенным пользователям."
        ]
      },
      {
        "title": "17. Предпросмотр импорта",
        "paragraphs": [
          "Перед применением изменений при импорте JSON теперь отображается предпросмотр. Он показывает, сколько шаблонов будет создано, обновлено или пропущено.",
          "Подтверждайте импорт только после проверки предпросмотра. Это помогает избежать случайной перезаписи существующих шаблонов."
        ],
        "items": [
          "Перед импортом большого набора создайте резервную копию экспортом.",
          "Импортируйте только JSON-файлы из надёжного источника.",
          "После импорта проверьте хотя бы одно примечание для клиента и одно внутреннее примечание."
        ]
      },
      {
        "title": "18. Отправка email для примечаний клиенту",
        "paragraphs": [
          "Примечания для клиентов могут запускать email-уведомления WooCommerce, если соответствующее письмо включено. Плагин регистрирует создание примечания для клиента отдельно от обработки email. Перед добавлением примечания проверьте редактируемый предпросмотр, а результат обработчика почты смотрите на странице «История»."
        ],
        "items": []
      },
      {
        "title": "19. Рекомендуемый рабочий процесс",
        "paragraphs": [
          "Безопасный повседневный процесс: выберите шаблон, проверьте предпросмотр после замены заполнителей, при необходимости отредактируйте его, проверьте тип примечания и только затем добавьте примечание.",
          "Новые шаблоны сначала проверяйте в некритичном заказе или тестовом магазине, прежде чем использовать их для реальных клиентов."
        ],
        "items": [
          "Используйте внутренние примечания для информации только для сотрудников.",
          "Используйте примечания для клиентов только для сообщений, которые можно отправлять клиенту.",
          "Проверяйте заполнители после каждого изменения шаблона."
        ]
      },
      {
        "title": "20. Условия шаблонов",
        "paragraphs": [
          "Условия определяют, доступен ли шаблон для конкретного заказа. Шаблоны можно ограничить по статусу заказа, способу оплаты, способу доставки, стране платёжного адреса, а также минимальной или максимальной сумме заказа. Все заданные условия должны выполняться."
        ],
        "items": [
          "Оставьте поле пустым, если это условие не должно ограничивать шаблон.",
          "Используйте технические идентификаторы способов оплаты и доставки.",
          "Условия проверяются в интерфейсе и повторно на сервере перед созданием примечания."
        ]
      },
      {
        "title": "21. Журнал обработки email",
        "paragraphs": [
          "Для примечаний клиенту плагин регистрирует момент, когда WooCommerce сообщает об обработке соответствующего email, а также технические ошибки wp_mail. Событие «обработано» подтверждает, что WordPress/WooCommerce передал сообщение почтовой системе, но не доказывает окончательную доставку или прочтение клиентом."
        ],
        "items": [
          "На странице «История» можно проверить обработанные и неудачные email-события.",
          "Если необходимы надёжные сведения о доставке, используйте SMTP-провайдера или службу журналирования почты.",
          "Внутренние примечания не запускают email для примечаний клиенту."
        ]
      },
      {
        "title": "22. Общая история",
        "paragraphs": [
          "Откройте <strong>Примечания Mailhilfe → История</strong>, чтобы просмотреть недавнее создание примечаний, использование шаблонов, обработку email и ошибки отправки. При наличии данных записи содержат заказ, шаблон, пользователя, получателя, тип события и время."
        ],
        "items": [
          "Используйте историю для поддержки, аудита и устранения неполадок.",
          "История плагина хранится отдельно от примечаний к заказам WooCommerce.",
          "На странице отображаются 250 последних записей."
        ]
      },
      {
        "title": "23. Предпросмотр с тестовым заказом",
        "paragraphs": [
          "В редакторе шаблона введите ID заказа WooCommerce в области тестового предпросмотра. Текущее содержимое редактора, включая несохранённые изменения, будет отображено с данными этого заказа без создания примечания и отправки email."
        ],
        "items": [
          "Используйте заказ на тестовом сайте или некритичный тестовый заказ.",
          "Проверьте отсутствующие значения, форматирование, условия и пользовательские метазаполнители.",
          "У вас должно быть право изменять выбранный заказ."
        ]
      },
      {
        "title": "24. Личное избранное и недавно использованные шаблоны",
        "paragraphs": [
          "Каждый администратор может отмечать личные избранные шаблоны на экране заказа. Плагин также запоминает десять последних использованных шаблонов для каждого пользователя и поднимает их выше в списке. Общее избранное остаётся доступным всем пользователям."
        ],
        "items": [
          "Личное избранное не изменяет список другого пользователя.",
          "Список недавних шаблонов обновляется только после успешного добавления примечания.",
          "Персональные данные сохраняются как метаданные пользователя WordPress."
        ]
      },
      {
        "title": "25. Страница диагностики",
        "paragraphs": [
          "Откройте <strong>Примечания Mailhilfe → Диагностика</strong>, чтобы просмотреть технические сведения: версии WordPress, PHP и WooCommerce, состояние HPOS, состояние email для примечаний клиенту, локаль, количество опубликованных шаблонов, состояние кеша и WP_DEBUG."
        ],
        "items": [
          "При обращении в поддержку скопируйте диагностические значения.",
          "Страница не отображает содержимое примечаний к заказам или адреса клиентов.",
          "Разработчики могут добавлять строки с помощью фильтра диагностики."
        ]
      },
      {
        "title": "26. Хуки и фильтры для разработчиков",
        "paragraphs": [
          "Плагин предоставляет хуки и фильтры для заполнителей, значений заполнителей, разрешённых метаключей, результатов шаблонов, условий, предпросмотра, итогового текста примечания, действий до и после добавления примечания, записей истории и диагностики. Названия хуков и параметры описаны в readme.txt."
        ],
        "items": [
          "Проверяйте, очищайте и экранируйте все пользовательские данные.",
          "Используйте API заказов WooCommerce вместо прямого доступа к таблицам заказов.",
          "Обеспечивайте совместимость пользовательских расширений как с HPOS, так и с классическим хранилищем заказов."
        ]
      }
    ]
  },
  "pt_BR": {
    "title": "Ajuda detalhada do Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Esta ajuda atualizada explica todo o fluxo de trabalho do Mailhilfe Order Note Manager for WooCommerce: criação e formatação de modelos, uso de idiomas de modelo, espaços reservados e espaços reservados de metadados, edição de prévias, envio seguro de notas ao cliente, configurações, permissões, prévias de importação e compatibilidade com HPOS.",
    "sections": [
      {
        "title": "1. O que o plugin faz",
        "paragraphs": [
          "O Mailhilfe Order Note Manager for WooCommerce permite salvar notas de pedidos do WooCommerce usadas com frequência como modelos reutilizáveis. Isso evita digitar o mesmo texto repetidamente e mantém a comunicação no histórico do pedido consistente.",
          "Um modelo pode ser preparado como nota interna para a equipe ou como nota ao cliente. Ainda é possível alterar o tipo de nota ao usar o modelo dentro de um pedido."
        ],
        "items": [
          "Exemplos comuns: lembretes de pagamento, atrasos na entrega, registros de chamadas telefônicas, verificações de endereço e respostas de atendimento.",
          "Os modelos oferecem suporte a categorias, favoritos, ordenação, contador de uso e backup em JSON."
        ]
      },
      {
        "title": "2. Criar um novo modelo",
        "paragraphs": [
          "Abra <strong>Notas de pedido Mailhilfe → Adicionar novo</strong>. Informe um título claro, escreva o texto da nota no editor e escolha se o tipo de nota padrão deve ser interno ou voltado ao cliente.",
          "Use o título como uma descrição curta da finalidade, por exemplo, “Lembrete de pagamento” ou “Cliente ligou sobre a entrega”. Isso facilita encontrar o modelo na tela do pedido."
        ],
        "items": [
          "Atribua uma ou mais categorias quando houver muitos modelos.",
          "Marque como favoritos os modelos usados com frequência.",
          "Publique o modelo para que ele fique disponível nos pedidos."
        ]
      },
      {
        "title": "3. Formatar o texto do modelo",
        "paragraphs": [
          "O texto do modelo usa o editor do WordPress. É possível formatar o conteúdo com parágrafos, negrito, itálico, listas e links. A formatação é mantida quando a nota é criada, mas o conteúdo é limpo de acordo com as regras de HTML seguro do WordPress.",
          "Use a formatação com cuidado em notas ao cliente. Um parágrafo curto ou uma lista com marcadores costuma ser mais fácil de ler do que um texto longo e sem estrutura."
        ],
        "items": [
          "Bom exemplo: uma saudação curta, uma explicação clara e uma próxima etapa.",
          "Evite abreviações internas em notas ao cliente.",
          "Não insira comentários privados da equipe em modelos que possam ser usados como notas ao cliente."
        ]
      },
      {
        "title": "4. Espaços reservados",
        "paragraphs": [
          "Espaços reservados são palavras entre chaves. Eles são substituídos por dados reais do pedido na prévia e quando a nota é adicionada ao pedido.",
          "É possível combinar texto normal e espaços reservados. Exemplo: <code>Olá {customer}, recebemos o seu pedido {order_number}.</code>"
        ],
        "items": [
          "Pedido: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Cliente: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Entrega e pagamento: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Itens e loja: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Prévia antes de adicionar uma nota",
        "paragraphs": [
          "Abra um pedido do WooCommerce e selecione um modelo. A prévia mostra a nota com os espaços reservados já substituídos pelos dados do pedido selecionado.",
          "Sempre verifique a prévia antes de criar a nota. Isso é especialmente importante quando um espaço reservado não tem valor no pedido, por exemplo, quando não há empresa de entrega ou número de telefone."
        ],
        "items": [
          "Verifique nomes, totais, método de entrega e lista de itens.",
          "Confirme se o tipo de nota selecionado está correto.",
          "Edite primeiro o modelo se o mesmo texto precisar ser melhorado para todos os pedidos futuros."
        ]
      },
      {
        "title": "6. Notas internas e notas ao cliente",
        "paragraphs": [
          "Notas internas são destinadas à equipe da loja e normalmente são usadas para documentação, tarefas de acompanhamento ou histórico de atendimento. Notas ao cliente podem ficar visíveis para o cliente e podem acionar notificações por e-mail do WooCommerce, dependendo das configurações do WooCommerce.",
          "Revise cuidadosamente a prévia editável e o tipo de nota selecionado. Use notas ao cliente somente para textos que o cliente pode ler."
        ],
        "items": [
          "Nota interna: “O cliente ligou e confirmou o endereço de entrega.”",
          "Nota ao cliente: “Seu pedido está sendo preparado e será enviado em breve.”",
          "Nunca inclua senhas, comentários privados ou informações exclusivas de fornecedores em notas ao cliente."
        ]
      },
      {
        "title": "7. Favoritos, pesquisa e ordenação",
        "paragraphs": [
          "Os favoritos ajudam a colocar os modelos mais importantes no topo da seleção. O campo de pesquisa na tela do pedido ajuda a encontrar um modelo pelo título, categoria ou conteúdo.",
          "Na lista de modelos, é possível alterar a ordem arrastando e soltando. A ordem salva é usada quando os modelos são exibidos na tela do pedido."
        ],
        "items": [
          "Use favoritos para os modelos utilizados diariamente.",
          "Use categorias para grupos de assuntos como Pagamento, Entrega, Devoluções e Suporte.",
          "Mantenha os títulos curtos para que os resultados da pesquisa continuem legíveis."
        ]
      },
      {
        "title": "8. Importação, exportação e modelos de demonstração",
        "paragraphs": [
          "A exportação JSON cria um backup dos seus modelos. Ela pode ser usada antes de alterações maiores ou para transferir modelos para outra loja.",
          "A importação JSON pode criar modelos e atualizar modelos existentes com o mesmo título ou a mesma chave interna de demonstração. Os modelos de demonstração oferecem um ponto de partida rápido e são criados no idioma ativo."
        ],
        "items": [
          "Exporte antes de fazer alterações em massa.",
          "Importe somente arquivos JSON de uma fonte confiável.",
          "Depois da importação, abra alguns modelos e verifique a formatação e os espaços reservados."
        ]
      },
      {
        "title": "9. Permissões e funções",
        "paragraphs": [
          "O plugin usa capacidades separadas para gerenciar modelos e usar modelos em pedidos. Administradores e gerentes de loja recebem essas permissões automaticamente durante a ativação.",
          "Se você usa um plugin de edição de funções, pode conceder ou remover essas permissões para funções personalizadas."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: criar, editar, excluir, importar e exportar modelos.",
          "<code>use_mh_order_note_templates</code>: usar modelos em pedidos do WooCommerce.",
          "Usuários sem a permissão necessária não veem as funções administrativas correspondentes."
        ]
      },
      {
        "title": "10. Segurança e compatibilidade com HPOS",
        "paragraphs": [
          "O plugin usa nonces do WordPress, verificações de capacidade, higienização e escape nas ações administrativas. O conteúdo do modelo é limpo com HTML seguro do WordPress antes de ser salvo ou usado.",
          "Os dados do pedido são lidos pelas APIs de pedidos do WooCommerce, em vez de acesso direto às tabelas do banco de dados. Isso mantém o plugin compatível com o HPOS do WooCommerce e com o armazenamento clássico de pedidos."
        ],
        "items": [
          "Mantenha o WooCommerce e o WordPress atualizados.",
          "Teste os fluxos de notas ao cliente depois de alterar as configurações de e-mail do WooCommerce.",
          "Use um site de testes antes de importar um conjunto grande de modelos."
        ]
      },
      {
        "title": "11. Solução de problemas",
        "paragraphs": [
          "Se os modelos não aparecerem em um pedido, verifique se o WooCommerce está ativo, se o modelo está publicado e se o usuário atual tem permissão para usar modelos.",
          "Se as traduções não aparecerem, verifique o idioma do site e o idioma do usuário no WordPress. O plugin inclui arquivos de fallback revisados para todos os idiomas compatíveis, inclusive persa. Outros idiomas devem ser fornecidos por pacotes de idioma revisados do WordPress.org."
        ],
        "items": [
          "Depois de uma atualização, limpe os plugins de cache ou cache de objetos se a tela administrativa antiga ainda estiver sendo exibida.",
          "Se um espaço reservado permanecer inalterado, verifique se ele foi escrito exatamente como aparece na lista, incluindo as chaves.",
          "Se as notas ao cliente não forem enviadas por e-mail, verifique as configurações de e-mail do WooCommerce para notificações de notas ao cliente."
        ]
      },
      {
        "title": "12. Página de configurações",
        "paragraphs": [
          "Abra <strong>Notas de pedido Mailhilfe → Configurações</strong> para escolher o tipo de nota padrão, o comportamento do HTML seguro, a exibição de uso, os favoritos, as opções de importação JSON e a correspondência de idioma. Use notas internas como padrão para um trabalho diário mais seguro."
        ],
        "items": []
      },
      {
        "title": "13. Idioma do modelo e lojas multilíngues",
        "paragraphs": [
          "Cada modelo pode ter um idioma. Escolha <strong>Todos os idiomas</strong> se o mesmo texto puder ser usado em todos os pedidos ou selecione um idioma específico para textos localizados.",
          "Quando disponível, o plugin pode dar preferência a modelos que correspondam ao idioma do pedido, ao idioma do usuário ou a dados de idioma comuns de plugins multilíngues."
        ],
        "items": [
          "Use um modelo neutro para notas internas da equipe.",
          "Crie modelos separados voltados ao cliente para português, alemão, inglês ou outros idiomas da loja.",
          "Em lojas com WPML ou Polylang, teste a detecção do idioma do pedido com um pedido de teste real."
        ]
      },
      {
        "title": "14. Campos personalizados e espaços reservados de metadados",
        "paragraphs": [
          "Espaços reservados avançados podem ler campos de metadados selecionados do pedido ou do cliente. Use <code>{order_meta:meta_key}</code> para dados do pedido e <code>{customer_meta:meta_key}</code> para dados de usuário do cliente.",
          "Por segurança, nomes de chaves sensíveis como password, token, secret, session, auth e hash são bloqueados. Use espaços reservados de metadados somente quando souber o que o campo contém."
        ],
        "items": [
          "Exemplo: <code>{order_meta:_tracking_number}</code> para um código de rastreamento salvo por um plugin de entrega.",
          "Exemplo: <code>{order_meta:_billing_vat_id}</code> para um campo de identificação fiscal.",
          "Não exponha campos internos ou sensíveis em notas ao cliente."
        ]
      },
      {
        "title": "15. Duplicar modelos e revisões",
        "paragraphs": [
          "Use a ação de duplicar quando precisar de um modelo semelhante com pequenas alterações. A cópia é criada como rascunho para que possa ser revisada antes da publicação.",
          "As revisões de modelos permitem comparar versões anteriores e restaurar um texto anterior quando uma alteração foi feita por engano."
        ],
        "items": [
          "Duplique um modelo geral de entrega antes de criar variantes para transportadoras ou retirada no local.",
          "Verifique as revisões depois de alterações maiores no texto.",
          "Mantenha os títulos claros para não confundir modelos semelhantes."
        ]
      },
      {
        "title": "16. Página de permissões",
        "paragraphs": [
          "Abra <strong>Notas de pedido Mailhilfe → Permissões</strong> para decidir quais funções do WordPress podem gerenciar modelos e quais podem usar modelos em pedidos.",
          "Os administradores mantêm as permissões necessárias. Para outras funções, conceda somente as permissões exigidas pela tarefa diária."
        ],
        "items": [
          "Gerenciar modelos: criar, editar, excluir, importar e exportar modelos.",
          "Usar modelos: selecionar um modelo e adicionar uma nota em um pedido do WooCommerce.",
          "Conceda permissões de importação e exportação somente a usuários confiáveis."
        ]
      },
      {
        "title": "17. Prévia da importação",
        "paragraphs": [
          "As importações JSON agora mostram uma prévia antes de aplicar as alterações. A prévia informa quantos modelos serão criados, atualizados ou ignorados.",
          "Confirme a importação somente depois de verificar a prévia. Isso evita a substituição acidental de modelos existentes."
        ],
        "items": [
          "Crie um backup de exportação antes de importar um conjunto grande.",
          "Importe somente arquivos JSON de uma fonte confiável.",
          "Depois da importação, teste pelo menos uma nota ao cliente e uma nota interna."
        ]
      },
      {
        "title": "18. Comportamento do e-mail de nota ao cliente",
        "paragraphs": [
          "Notas ao cliente podem acionar notificações por e-mail do WooCommerce quando o e-mail correspondente está ativado. O plugin registra a criação da nota ao cliente separadamente do processamento do e-mail. Revise a prévia editável antes de adicionar a nota e use a página Histórico para verificar o resultado do manipulador de e-mail."
        ],
        "items": []
      },
      {
        "title": "19. Fluxo de trabalho recomendado",
        "paragraphs": [
          "Um fluxo diário seguro é: selecionar um modelo, revisar a prévia com os valores substituídos, editar a prévia se necessário, verificar o tipo de nota e então adicionar a nota.",
          "Para novos modelos, teste-os primeiro em um pedido sem importância ou em uma loja de testes antes de usá-los com clientes reais."
        ],
        "items": [
          "Use notas internas para informações destinadas apenas à equipe.",
          "Use notas ao cliente somente para mensagens que podem ser enviadas ao cliente.",
          "Revise os espaços reservados sempre que um modelo for alterado."
        ]
      },
      {
        "title": "20. Condições do modelo",
        "paragraphs": [
          "As condições do modelo determinam se um modelo está disponível para um pedido específico. É possível restringir modelos por status do pedido, método de pagamento, método de entrega, país de faturamento e total mínimo ou máximo do pedido. Todas as condições configuradas devem corresponder."
        ],
        "items": [
          "Deixe um campo em branco quando essa condição não deve restringir o modelo.",
          "Use os IDs técnicos dos métodos de pagamento e de entrega.",
          "As condições são verificadas na interface e novamente no servidor antes de uma nota ser criada."
        ]
      },
      {
        "title": "21. Registro de processamento de e-mail",
        "paragraphs": [
          "Para notas ao cliente, o plugin registra quando o WooCommerce informa que o e-mail de nota ao cliente foi processado e também registra erros técnicos de wp_mail. Um evento processado confirma que o WordPress/WooCommerce entregou a mensagem ao sistema de e-mail; isso não comprova a entrega final nem que o cliente a leu."
        ],
        "items": [
          "Verifique na página Histórico os eventos de e-mail processados e com falha.",
          "Use um provedor SMTP ou um serviço de registro de e-mails quando forem necessárias informações definitivas de entrega.",
          "Notas internas não acionam e-mails de nota ao cliente."
        ]
      },
      {
        "title": "22. Histórico central",
        "paragraphs": [
          "Abra <strong>Notas de pedido Mailhilfe → Histórico</strong> para revisar a criação recente de notas, o uso de modelos, o processamento de e-mails e as falhas de e-mail. Quando disponíveis, os registros incluem o pedido, o modelo, o usuário, o destinatário, o tipo de evento e o horário."
        ],
        "items": [
          "Use o histórico para suporte, auditoria e solução de problemas.",
          "O histórico é separado das notas de pedido do WooCommerce.",
          "A página exibe os 250 registros mais recentes."
        ]
      },
      {
        "title": "23. Prévia com pedido de teste",
        "paragraphs": [
          "No editor de modelos, informe um ID de pedido do WooCommerce na área de prévia de teste. O conteúdo atual do editor, incluindo alterações não salvas, é renderizado com os dados desse pedido sem criar uma nota nem enviar um e-mail."
        ],
        "items": [
          "Use um pedido de uma loja de testes ou um pedido de teste sem importância.",
          "Verifique valores ausentes, formatação, condições e espaços reservados de metadados personalizados.",
          "Você precisa ter permissão para editar o pedido selecionado."
        ]
      },
      {
        "title": "24. Favoritos pessoais e modelos usados recentemente",
        "paragraphs": [
          "Cada administrador pode marcar favoritos pessoais na tela do pedido. O plugin também armazena os dez modelos usados mais recentemente por cada usuário e dá a eles uma posição mais alta na seleção. Os favoritos globais continuam compartilhados com todos os usuários."
        ],
        "items": [
          "Favoritos pessoais não alteram a lista de outro usuário.",
          "A lista de recentes só é atualizada depois que uma nota é adicionada com sucesso.",
          "Os dados pessoais são armazenados como metadados de usuário do WordPress."
        ]
      },
      {
        "title": "25. Página de diagnóstico",
        "paragraphs": [
          "Abra <strong>Notas de pedido Mailhilfe → Diagnóstico</strong> para ver informações técnicas como versões do WordPress, PHP e WooCommerce, status do HPOS, status do e-mail de nota ao cliente, localidade, quantidade de modelos publicados, status do cache e WP_DEBUG."
        ],
        "items": [
          "Copie os valores de diagnóstico ao solicitar suporte.",
          "A página não exibe o conteúdo das notas do pedido nem endereços de clientes.",
          "Desenvolvedores podem adicionar linhas com o filtro de diagnóstico."
        ]
      },
      {
        "title": "26. Ganchos e filtros para desenvolvedores",
        "paragraphs": [
          "O plugin fornece ganchos e filtros para espaços reservados, valores de espaços reservados, chaves meta permitidas, resultados de modelos, condições, conteúdo da prévia, conteúdo final da nota, ações antes e depois de adicionar uma nota, registros de histórico e diagnóstico. Os nomes dos ganchos e seus parâmetros estão documentados em readme.txt."
        ],
        "items": [
          "Valide, higienize e escape todos os dados personalizados.",
          "Use as APIs de pedidos do WooCommerce em vez de acessar diretamente as tabelas de pedidos.",
          "Mantenha as extensões personalizadas compatíveis com o HPOS e com o armazenamento clássico de pedidos."
        ]
      }
    ]
  },
  "it_IT": {
    "title": "Guida dettagliata di Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Questa guida aggiornata descrive l’intero flusso di lavoro di Mailhilfe Order Note Manager for WooCommerce: creazione e formattazione dei modelli, lingue dei modelli, segnaposto e segnaposto meta, modifica delle anteprime, invio sicuro delle note per il cliente, impostazioni, permessi, anteprima delle importazioni e compatibilità con HPOS.",
    "sections": [
      {
        "title": "1. Funzione del plugin",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce consente di salvare le note degli ordini WooCommerce utilizzate di frequente come modelli riutilizzabili. In questo modo non è necessario digitare ogni volta lo stesso testo e la comunicazione nella cronologia dell’ordine rimane coerente.",
          "Un modello può essere predisposto come nota interna per il personale oppure come nota per il cliente. Il tipo di nota può comunque essere modificato quando il modello viene utilizzato in un ordine."
        ],
        "items": [
          "Esempi tipici: promemoria di pagamento, ritardi di consegna, registrazioni di telefonate, verifiche dell’indirizzo e risposte del servizio clienti.",
          "I modelli supportano categorie, preferiti, ordinamento, contatore di utilizzo e backup JSON."
        ]
      },
      {
        "title": "2. Creare un nuovo modello",
        "paragraphs": [
          "Apri <strong>Note ordine Mailhilfe → Aggiungi nuovo</strong>. Inserisci un titolo chiaro, scrivi il testo della nota nell’editor e scegli se il tipo di nota predefinito deve essere interno o destinato al cliente.",
          "Usa il titolo come breve descrizione dello scopo, ad esempio “Promemoria di pagamento” o “Il cliente ha chiamato per la consegna”. In questo modo il modello sarà più facile da trovare nella schermata dell’ordine."
        ],
        "items": [
          "Assegna una o più categorie quando sono presenti molti modelli.",
          "Contrassegna come preferiti i modelli utilizzati più spesso.",
          "Pubblica il modello affinché diventi disponibile negli ordini."
        ]
      },
      {
        "title": "3. Formattare il testo del modello",
        "paragraphs": [
          "Il testo del modello utilizza l’editor di WordPress. Puoi formattarlo con paragrafi, grassetto, corsivo, elenchi e link. La formattazione viene mantenuta quando viene creata la nota, ma il contenuto viene ripulito secondo le regole HTML sicure di WordPress.",
          "Usa la formattazione con moderazione nelle note per il cliente. Un breve paragrafo o un elenco puntato è generalmente più leggibile di un testo lungo e non strutturato."
        ],
        "items": [
          "Buon esempio: un breve saluto, una spiegazione chiara e un passaggio successivo.",
          "Evita abbreviazioni interne nelle note per il cliente.",
          "Non inserire commenti privati del personale in modelli che potrebbero essere utilizzati come note per il cliente."
        ]
      },
      {
        "title": "4. Segnaposto",
        "paragraphs": [
          "I segnaposto sono parole racchiuse tra parentesi graffe. Nell’anteprima e quando la nota viene aggiunta all’ordine vengono sostituiti con i dati reali dell’ordine.",
          "Puoi combinare testo normale e segnaposto. Esempio: <code>Buongiorno {customer}, abbiamo ricevuto il tuo ordine {order_number}.</code>"
        ],
        "items": [
          "Ordine: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Cliente: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Spedizione e pagamento: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Articoli e negozio: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Anteprima prima di aggiungere una nota",
        "paragraphs": [
          "Apri un ordine WooCommerce e seleziona un modello. L’anteprima mostra la nota con i segnaposto già sostituiti dai dati dell’ordine selezionato.",
          "Controlla sempre l’anteprima prima di creare la nota. Questo è particolarmente importante quando un segnaposto non ha un valore nell’ordine, ad esempio se manca l’azienda di spedizione o il numero di telefono."
        ],
        "items": [
          "Controlla nomi, totali, metodo di spedizione ed elenco degli articoli.",
          "Verifica che il tipo di nota selezionato sia corretto.",
          "Modifica prima il modello se lo stesso testo deve essere migliorato per tutti gli ordini futuri."
        ]
      },
      {
        "title": "6. Note interne e note per il cliente",
        "paragraphs": [
          "Le note interne sono destinate al personale del negozio e vengono normalmente utilizzate per documentazione, attività successive o cronologia dell’assistenza. Le note per il cliente possono essere visibili al cliente e, a seconda delle impostazioni di WooCommerce, possono attivare notifiche email.",
          "Controlla attentamente l’anteprima modificabile e il tipo di nota selezionato. Usa le note per il cliente solo per testi che il cliente è autorizzato a leggere."
        ],
        "items": [
          "Nota interna: “Il cliente ha chiamato, indirizzo di consegna confermato”.",
          "Nota per il cliente: “Il tuo ordine è in preparazione e verrà spedito a breve”.",
          "Non inserire mai password, commenti privati o informazioni riservate ai fornitori nelle note per il cliente."
        ]
      },
      {
        "title": "7. Preferiti, ricerca e ordinamento",
        "paragraphs": [
          "I preferiti consentono di posizionare i modelli più importanti all’inizio della selezione. Il campo di ricerca nella schermata dell’ordine permette di trovare un modello per titolo, categoria o contenuto.",
          "Nell’elenco dei modelli puoi modificare l’ordine tramite trascinamento. L’ordine salvato viene utilizzato quando i modelli vengono visualizzati nella schermata dell’ordine."
        ],
        "items": [
          "Usa i preferiti per i modelli quotidiani.",
          "Usa le categorie per gruppi tematici come Pagamento, Spedizione, Resi e Assistenza.",
          "Mantieni i titoli brevi affinché i risultati della ricerca restino leggibili."
        ]
      },
      {
        "title": "8. Importazione, esportazione e modelli dimostrativi",
        "paragraphs": [
          "L’esportazione JSON crea un backup dei modelli. Puoi utilizzarla prima di modifiche importanti o per trasferire i modelli in un altro negozio.",
          "L’importazione JSON può creare modelli e aggiornare quelli esistenti con lo stesso titolo o la stessa chiave dimostrativa interna. I modelli dimostrativi offrono un punto di partenza rapido e vengono creati nella lingua attiva."
        ],
        "items": [
          "Esegui un’esportazione prima di modifiche in blocco.",
          "Importa solo file JSON provenienti da una fonte attendibile.",
          "Dopo l’importazione, apri alcuni modelli e controlla formattazione e segnaposto."
        ]
      },
      {
        "title": "9. Permessi e ruoli",
        "paragraphs": [
          "Il plugin utilizza capacità separate per la gestione dei modelli e per il loro utilizzo negli ordini. Amministratori e gestori del negozio ricevono automaticamente questi permessi durante l’attivazione.",
          "Se utilizzi un plugin per la gestione dei ruoli, puoi concedere o rimuovere questi permessi per ruoli personalizzati."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: creare, modificare, eliminare, importare ed esportare modelli.",
          "<code>use_mh_order_note_templates</code>: utilizzare i modelli negli ordini WooCommerce.",
          "Gli utenti privi del permesso richiesto non vedono le relative funzioni amministrative."
        ]
      },
      {
        "title": "10. Sicurezza e compatibilità con HPOS",
        "paragraphs": [
          "Il plugin utilizza nonce di WordPress, controlli delle capacità, sanitizzazione ed escaping per le operazioni amministrative. Il contenuto dei modelli viene ripulito con HTML sicuro per WordPress prima di essere salvato o utilizzato.",
          "I dati degli ordini vengono letti tramite le API degli ordini WooCommerce anziché accedendo direttamente alle tabelle del database. In questo modo il plugin rimane compatibile sia con WooCommerce HPOS sia con l’archiviazione classica degli ordini."
        ],
        "items": [
          "Mantieni aggiornati WooCommerce e WordPress.",
          "Verifica il flusso delle note per il cliente dopo aver modificato le impostazioni email di WooCommerce.",
          "Utilizza un sito di staging prima di importare un insieme di modelli di grandi dimensioni."
        ]
      },
      {
        "title": "11. Risoluzione dei problemi",
        "paragraphs": [
          "Se i modelli non vengono visualizzati in un ordine, verifica che WooCommerce sia attivo, che il modello sia pubblicato e che l’utente corrente disponga del permesso per utilizzare i modelli.",
          "Se le traduzioni non vengono visualizzate, controlla la lingua del sito e la lingua dell’utente in WordPress. Il plugin include file di fallback verificati per tutte le lingue supportate, compreso il persiano. Le altre lingue devono essere fornite tramite pacchetti linguistici WordPress.org verificati."
        ],
        "items": [
          "Dopo un aggiornamento, svuota i plugin di cache o object cache se viene ancora mostrata la vecchia schermata amministrativa.",
          "Se un segnaposto rimane invariato, verifica che sia scritto esattamente come indicato, incluse le parentesi graffe.",
          "Se le note per il cliente non vengono inviate via email, controlla le impostazioni email di WooCommerce per le notifiche delle note al cliente."
        ]
      },
      {
        "title": "12. Pagina delle impostazioni",
        "paragraphs": [
          "Apri <strong>Note ordine Mailhilfe → Impostazioni</strong> per scegliere il tipo di nota predefinito, il comportamento dell’HTML sicuro, la visualizzazione degli utilizzi, i preferiti, le opzioni di importazione JSON e la corrispondenza linguistica. Per un lavoro quotidiano più sicuro, usa le note interne come impostazione predefinita."
        ],
        "items": []
      },
      {
        "title": "13. Lingua del modello e negozi multilingue",
        "paragraphs": [
          "Ogni modello può avere una lingua. Scegli <strong>Tutte le lingue</strong> se lo stesso testo può essere utilizzato per ogni ordine oppure seleziona una lingua specifica per i testi localizzati.",
          "Quando possibile, il plugin può dare la precedenza ai modelli che corrispondono alla lingua dell’ordine, alla lingua dell’utente o ai dati linguistici comuni dei plugin multilingue."
        ],
        "items": [
          "Usa un modello neutro per le note interne del personale.",
          "Crea modelli separati destinati ai clienti per italiano, tedesco, inglese o altre lingue del negozio.",
          "Nei negozi WPML o Polylang, verifica il rilevamento della lingua dell’ordine con un vero ordine di prova."
        ]
      },
      {
        "title": "14. Campi personalizzati e segnaposto meta",
        "paragraphs": [
          "I segnaposto avanzati possono leggere campi meta selezionati dell’ordine o del cliente. Usa <code>{order_meta:meta_key}</code> per i dati dell’ordine e <code>{customer_meta:meta_key}</code> per i dati utente del cliente.",
          "Per motivi di sicurezza, le chiavi sensibili contenenti parole come password, token, secret, session, auth e hash sono bloccate. Utilizza i segnaposto meta solo quando sai esattamente cosa contiene il campo."
        ],
        "items": [
          "Esempio: <code>{order_meta:_tracking_number}</code> per un numero di tracciamento salvato da un plugin di spedizione.",
          "Esempio: <code>{order_meta:_billing_vat_id}</code> per un campo partita IVA.",
          "Non esporre campi interni o sensibili nelle note per il cliente."
        ]
      },
      {
        "title": "15. Duplicazione dei modelli e revisioni",
        "paragraphs": [
          "Usa l’azione di duplicazione quando ti serve un modello simile con piccole modifiche. La copia viene creata come bozza in modo da poterla controllare prima della pubblicazione.",
          "Le revisioni dei modelli consentono di confrontare versioni precedenti e ripristinare un testo precedente quando una modifica è stata effettuata per errore."
        ],
        "items": [
          "Duplica un modello generale di spedizione prima di creare varianti per DHL, UPS o ritiro.",
          "Controlla le revisioni dopo modifiche importanti al testo.",
          "Usa titoli chiari per evitare di confondere modelli simili."
        ]
      },
      {
        "title": "16. Pagina dei permessi",
        "paragraphs": [
          "Apri <strong>Note ordine Mailhilfe → Permessi</strong> per decidere quali ruoli WordPress possono gestire i modelli e quali possono utilizzarli negli ordini.",
          "Gli amministratori mantengono i permessi necessari. Per gli altri ruoli, concedi solo i permessi richiesti dalle attività quotidiane."
        ],
        "items": [
          "Gestire i modelli: creare, modificare, eliminare, importare ed esportare modelli.",
          "Usare i modelli: selezionare un modello e aggiungere una nota in un ordine WooCommerce.",
          "Concedi i permessi di importazione/esportazione solo a utenti attendibili."
        ]
      },
      {
        "title": "17. Anteprima dell’importazione",
        "paragraphs": [
          "Le importazioni JSON mostrano un’anteprima prima dell’applicazione delle modifiche. L’anteprima indica quanti modelli verranno creati, aggiornati o ignorati.",
          "Conferma l’importazione solo dopo aver controllato l’anteprima. In questo modo eviti la sovrascrittura accidentale di modelli esistenti."
        ],
        "items": [
          "Crea un backup tramite esportazione prima di importare un insieme di grandi dimensioni.",
          "Importa solo file JSON provenienti da una fonte attendibile.",
          "Dopo l’importazione, prova almeno una nota per il cliente e una nota interna."
        ]
      },
      {
        "title": "18. Comportamento email delle note per il cliente",
        "paragraphs": [
          "Le note per il cliente possono attivare notifiche email di WooCommerce quando l’email corrispondente è abilitata. Il plugin registra separatamente la creazione della nota per il cliente e l’elaborazione dell’email. Controlla l’anteprima modificabile prima di aggiungere la nota e usa la pagina Cronologia per verificare il risultato del gestore email."
        ],
        "items": []
      },
      {
        "title": "19. Flusso di lavoro consigliato",
        "paragraphs": [
          "Un flusso di lavoro quotidiano sicuro consiste nel selezionare un modello, controllare l’anteprima con i dati sostituiti, modificarla se necessario, verificare il tipo di nota e infine aggiungere la nota.",
          "Per i nuovi modelli, esegui prima una prova con un ordine non critico o in un negozio di staging prima di utilizzarli con clienti reali."
        ],
        "items": [
          "Usa le note interne per informazioni riservate al personale.",
          "Usa le note per il cliente solo per messaggi che possono essere inviati al cliente.",
          "Controlla i segnaposto ogni volta che un modello viene modificato."
        ]
      },
      {
        "title": "20. Condizioni del modello",
        "paragraphs": [
          "Le condizioni del modello stabiliscono se un modello è disponibile per un determinato ordine. Puoi limitare i modelli in base a stato dell’ordine, metodo di pagamento, metodo di spedizione, paese di fatturazione e totale minimo o massimo dell’ordine. Tutte le condizioni configurate devono corrispondere."
        ],
        "items": [
          "Lascia vuoto un campo quando la relativa condizione non deve limitare il modello.",
          "Usa gli ID tecnici dei metodi di pagamento e di spedizione.",
          "Le condizioni vengono verificate nell’interfaccia e nuovamente sul server prima della creazione di una nota."
        ]
      },
      {
        "title": "21. Registro dell’elaborazione email",
        "paragraphs": [
          "Per le note per il cliente, il plugin registra quando WooCommerce segnala che l’email della nota è stata elaborata e registra anche gli errori tecnici di wp_mail. Un evento elaborato conferma che WordPress/WooCommerce ha consegnato il messaggio al sistema di posta, ma non dimostra la consegna finale né che il cliente lo abbia letto."
        ],
        "items": [
          "Controlla nella pagina Cronologia gli eventi email elaborati e non riusciti.",
          "Usa un provider SMTP o un servizio di registrazione email quando sono necessarie informazioni definitive sulla consegna.",
          "Le note interne non attivano l’email delle note per il cliente."
        ]
      },
      {
        "title": "22. Cronologia centralizzata",
        "paragraphs": [
          "Apri <strong>Note ordine Mailhilfe → Cronologia</strong> per esaminare le creazioni recenti di note, l’utilizzo dei modelli, l’elaborazione email e gli errori email. Quando disponibili, le voci includono ordine, modello, utente, destinatario, tipo di evento e ora."
        ],
        "items": [
          "Usa la cronologia per assistenza, controlli e risoluzione dei problemi.",
          "La cronologia è separata dalle note dell’ordine WooCommerce.",
          "La pagina mostra le 250 voci più recenti."
        ]
      },
      {
        "title": "23. Anteprima con ordine di prova",
        "paragraphs": [
          "Nell’editor del modello, inserisci un ID ordine WooCommerce nell’area dell’anteprima di prova. Il contenuto corrente dell’editor, incluse le modifiche non salvate, viene visualizzato con i dati di quell’ordine senza creare una nota né inviare un’email."
        ],
        "items": [
          "Usa un ordine di staging o un ordine di prova non critico.",
          "Controlla valori mancanti, formattazione, condizioni e segnaposto meta personalizzati.",
          "Devi disporre del permesso per modificare l’ordine selezionato."
        ]
      },
      {
        "title": "24. Preferiti personali e modelli utilizzati di recente",
        "paragraphs": [
          "Ogni amministratore può contrassegnare preferiti personali nella schermata dell’ordine. Il plugin memorizza inoltre i dieci modelli utilizzati più di recente da ciascun utente e assegna loro una posizione più alta nella selezione. I preferiti globali restano condivisi con tutti gli utenti."
        ],
        "items": [
          "I preferiti personali non modificano l’elenco di un altro utente.",
          "L’elenco dei recenti viene aggiornato solo dopo l’aggiunta corretta di una nota.",
          "I dati personali vengono memorizzati come metadati utente di WordPress."
        ]
      },
      {
        "title": "25. Pagina Diagnostica",
        "paragraphs": [
          "Apri <strong>Note ordine Mailhilfe → Diagnostica</strong> per visualizzare informazioni tecniche come versioni di WordPress, PHP e WooCommerce, stato HPOS, stato dell’email delle note per il cliente, locale, numero di modelli pubblicati, stato della cache e WP_DEBUG."
        ],
        "items": [
          "Copia i valori diagnostici quando richiedi assistenza.",
          "La pagina non mostra il contenuto delle note degli ordini né gli indirizzi dei clienti.",
          "Gli sviluppatori possono aggiungere righe tramite il filtro della diagnostica."
        ]
      },
      {
        "title": "26. Hook e filtri per sviluppatori",
        "paragraphs": [
          "Il plugin fornisce hook e filtri per segnaposto, valori dei segnaposto, chiavi meta consentite, risultati dei modelli, condizioni, contenuto dell’anteprima, contenuto finale della nota, azioni prima e dopo l’aggiunta di una nota, record della cronologia e diagnostica. Nomi e parametri degli hook sono documentati in readme.txt."
        ],
        "items": [
          "Convalida, sanitizza ed esegui l’escaping di tutti i dati personalizzati.",
          "Usa le API degli ordini WooCommerce anziché l’accesso diretto alle tabelle degli ordini.",
          "Mantieni le estensioni personalizzate compatibili sia con HPOS sia con l’archiviazione classica degli ordini."
        ]
      }
    ]
  },
  "hi_IN": {
    "title": "Mailhilfe Order Note Manager for WooCommerce की विस्तृत सहायता",
    "intro": "यह अद्यतन सहायता Mailhilfe Order Note Manager for WooCommerce के पूरे कार्यप्रवाह को समझाती है: टेम्पलेट बनाना और फ़ॉर्मेट करना, टेम्पलेट भाषाएँ, प्लेसहोल्डर और मेटा प्लेसहोल्डर, पूर्वावलोकन संपादित करना, ग्राहक नोट सुरक्षित रूप से भेजना, सेटिंग्स, अनुमतियाँ, आयात पूर्वावलोकन और HPOS संगतता।",
    "sections": [
      {
        "title": "1. प्लगइन क्या करता है",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce आपको बार-बार उपयोग होने वाले WooCommerce ऑर्डर नोट्स को पुनः उपयोग योग्य टेम्पलेट के रूप में सहेजने देता है। इससे एक ही टेक्स्ट बार-बार लिखने की आवश्यकता नहीं रहती और ऑर्डर इतिहास में संचार एकरूप रहता है।",
          "किसी टेम्पलेट को कर्मचारियों के लिए आंतरिक नोट या ग्राहक नोट के रूप में तैयार किया जा सकता है। ऑर्डर में टेम्पलेट उपयोग करते समय भी नोट प्रकार बदला जा सकता है।"
        ],
        "items": [
          "सामान्य उदाहरण: भुगतान अनुस्मारक, डिलीवरी में देरी, फ़ोन कॉल रिकॉर्ड, पते की जाँच और सेवा उत्तर।",
          "टेम्पलेट श्रेणियाँ, पसंदीदा, क्रमबद्धता, उपयोग काउंटर और JSON बैकअप का समर्थन करते हैं।"
        ]
      },
      {
        "title": "2. नया टेम्पलेट बनाएँ",
        "paragraphs": [
          "<strong>Mailhilfe ऑर्डर नोट्स → नया जोड़ें</strong> खोलें। स्पष्ट शीर्षक दर्ज करें, संपादक में नोट टेक्स्ट लिखें और चुनें कि डिफ़ॉल्ट नोट प्रकार आंतरिक होगा या ग्राहक के लिए।",
          "शीर्षक को उद्देश्य का छोटा विवरण रखें, जैसे “भुगतान अनुस्मारक” या “ग्राहक ने डिलीवरी के बारे में फ़ोन किया”। इससे ऑर्डर स्क्रीन में टेम्पलेट ढूँढना आसान होता है।"
        ],
        "items": [
          "बहुत अधिक टेम्पलेट होने पर एक या अधिक श्रेणियाँ असाइन करें।",
          "अक्सर उपयोग होने वाले टेम्पलेट को पसंदीदा के रूप में चिह्नित करें।",
          "टेम्पलेट प्रकाशित करें ताकि वह ऑर्डर में उपलब्ध हो जाए।"
        ]
      },
      {
        "title": "3. टेम्पलेट टेक्स्ट को फ़ॉर्मेट करना",
        "paragraphs": [
          "टेम्पलेट टेक्स्ट WordPress संपादक का उपयोग करता है। आप अनुच्छेद, बोल्ड और इटैलिक टेक्स्ट, सूचियाँ और लिंक उपयोग कर सकते हैं। नोट बनाते समय फ़ॉर्मेटिंग बनी रहती है, लेकिन सामग्री WordPress के सुरक्षित HTML नियमों के अनुसार साफ की जाती है।",
          "ग्राहक नोट्स में फ़ॉर्मेटिंग सावधानी से उपयोग करें। छोटा अनुच्छेद या बुलेट सूची सामान्यतः लंबे असंरचित टेक्स्ट से अधिक पठनीय होती है।"
        ],
        "items": [
          "अच्छा उदाहरण: छोटा अभिवादन, एक स्पष्ट व्याख्या और अगला कदम।",
          "ग्राहक नोट्स में केवल कर्मचारियों के लिए उपयोग होने वाले संक्षेपों से बचें।",
          "ऐसे टेम्पलेट में निजी कर्मचारी टिप्पणियाँ न डालें जो ग्राहक नोट के रूप में उपयोग हो सकते हैं।"
        ]
      },
      {
        "title": "4. प्लेसहोल्डर",
        "paragraphs": [
          "प्लेसहोल्डर घुँघराले कोष्ठकों में लिखे शब्द होते हैं। पूर्वावलोकन में और नोट को ऑर्डर में जोड़ते समय इन्हें वास्तविक ऑर्डर डेटा से बदल दिया जाता है।",
          "आप सामान्य टेक्स्ट और प्लेसहोल्डर को मिला सकते हैं। उदाहरण: <code>नमस्ते {customer}, हमें आपका ऑर्डर {order_number} प्राप्त हो गया है।</code>"
        ],
        "items": [
          "ऑर्डर: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>।",
          "ग्राहक: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>।",
          "शिपिंग और भुगतान: <code>{shipping_method}</code>, <code>{payment_method}</code>।",
          "आइटम और दुकान: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>।"
        ]
      },
      {
        "title": "5. नोट जोड़ने से पहले पूर्वावलोकन",
        "paragraphs": [
          "WooCommerce ऑर्डर खोलें और टेम्पलेट चुनें। पूर्वावलोकन में चयनित ऑर्डर डेटा से बदले हुए प्लेसहोल्डर के साथ नोट दिखाई देता है।",
          "नोट बनाने से पहले पूर्वावलोकन हमेशा जाँचें। यह विशेष रूप से तब महत्वपूर्ण है जब ऑर्डर में किसी प्लेसहोल्डर का मान उपलब्ध न हो, जैसे शिपिंग कंपनी या फ़ोन नंबर।"
        ],
        "items": [
          "नाम, कुल राशि, शिपिंग विधि और आइटम सूची जाँचें।",
          "जाँचें कि चुना गया नोट प्रकार सही है।",
          "यदि वही टेक्स्ट सभी भविष्य के ऑर्डर के लिए सुधारना है तो पहले टेम्पलेट संपादित करें।"
        ]
      },
      {
        "title": "6. आंतरिक नोट और ग्राहक नोट",
        "paragraphs": [
          "आंतरिक नोट दुकान के कर्मचारियों के लिए होते हैं और सामान्यतः दस्तावेज़ीकरण, आगे की कार्रवाई या सेवा इतिहास के लिए उपयोग किए जाते हैं। ग्राहक नोट ग्राहक को दिखाई दे सकते हैं और WooCommerce सेटिंग्स के आधार पर ईमेल सूचना भेज सकते हैं।",
          "संपादन योग्य पूर्वावलोकन और चुने गए नोट प्रकार की सावधानीपूर्वक समीक्षा करें। ग्राहक नोट केवल ऐसे टेक्स्ट के लिए उपयोग करें जिसे ग्राहक पढ़ सकता हो।"
        ],
        "items": [
          "आंतरिक नोट: “ग्राहक ने फ़ोन किया, डिलीवरी पता पुष्ट किया गया।”",
          "ग्राहक नोट: “आपका ऑर्डर तैयार किया जा रहा है और शीघ्र भेजा जाएगा।”",
          "ग्राहक नोट्स में कभी भी पासवर्ड, निजी टिप्पणियाँ या केवल आपूर्तिकर्ता के लिए जानकारी न डालें।"
        ]
      },
      {
        "title": "7. पसंदीदा, खोज और क्रमबद्धता",
        "paragraphs": [
          "पसंदीदा सबसे महत्वपूर्ण टेम्पलेट को चयन सूची के ऊपर रखने में मदद करते हैं। ऑर्डर स्क्रीन का खोज फ़ील्ड शीर्षक, श्रेणी या सामग्री के आधार पर टेम्पलेट ढूँढने में सहायता करता है।",
          "टेम्पलेट सूची में खींचकर और छोड़कर क्रम बदला जा सकता है। सहेजा गया क्रम ऑर्डर स्क्रीन में टेम्पलेट दिखाते समय उपयोग होता है।"
        ],
        "items": [
          "प्रतिदिन उपयोग होने वाले टेम्पलेट को पसंदीदा बनाएँ।",
          "भुगतान, शिपिंग, वापसी और सहायता जैसे विषय समूहों के लिए श्रेणियाँ उपयोग करें।",
          "शीर्षक छोटे रखें ताकि खोज परिणाम आसानी से पढ़े जा सकें।"
        ]
      },
      {
        "title": "8. आयात, निर्यात और डेमो टेम्पलेट",
        "paragraphs": [
          "JSON निर्यात आपके टेम्पलेट का बैकअप बनाता है। बड़े बदलावों से पहले या टेम्पलेट को दूसरी दुकान में स्थानांतरित करने के लिए इसका उपयोग किया जा सकता है।",
          "JSON आयात नए टेम्पलेट बना सकता है और समान शीर्षक या आंतरिक डेमो कुंजी वाले मौजूदा टेम्पलेट अपडेट कर सकता है। डेमो टेम्पलेट शीघ्र शुरुआत के लिए हैं और सक्रिय भाषा में बनाए जाते हैं।"
        ],
        "items": [
          "एक साथ कई बदलाव करने से पहले निर्यात करें।",
          "केवल विश्वसनीय स्रोत की JSON फ़ाइल आयात करें।",
          "आयात के बाद कुछ टेम्पलेट खोलकर फ़ॉर्मेटिंग और प्लेसहोल्डर जाँचें।"
        ]
      },
      {
        "title": "9. अनुमतियाँ और भूमिकाएँ",
        "paragraphs": [
          "प्लगइन टेम्पलेट प्रबंधित करने और ऑर्डर में टेम्पलेट उपयोग करने के लिए अलग-अलग क्षमताएँ उपयोग करता है। सक्रियण के समय प्रशासक और शॉप प्रबंधक ये अनुमतियाँ अपने-आप प्राप्त करते हैं।",
          "यदि आप भूमिका संपादक प्लगइन उपयोग करते हैं तो कस्टम भूमिकाओं को ये अनुमतियाँ दे या हटा सकते हैं।"
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: टेम्पलेट बनाना, संपादित करना, हटाना तथा आयात/निर्यात करना।",
          "<code>use_mh_order_note_templates</code>: WooCommerce ऑर्डर में टेम्पलेट उपयोग करना।",
          "आवश्यक अनुमति के बिना उपयोगकर्ताओं को संबंधित एडमिन कार्य दिखाई नहीं देते।"
        ]
      },
      {
        "title": "10. सुरक्षा और HPOS संगतता",
        "paragraphs": [
          "प्लगइन एडमिन क्रियाओं के लिए WordPress nonce, क्षमता जाँच, सैनिटाइज़िंग और एस्केपिंग उपयोग करता है। सहेजने या उपयोग करने से पहले टेम्पलेट सामग्री को WordPress-सुरक्षित HTML के अनुसार साफ किया जाता है।",
          "ऑर्डर डेटा सीधे डेटाबेस तालिकाओं से पढ़ने के बजाय WooCommerce ऑर्डर API से पढ़ा जाता है। इससे प्लगइन WooCommerce HPOS और पारंपरिक ऑर्डर संग्रहण दोनों के साथ संगत रहता है।"
        ],
        "items": [
          "WooCommerce और WordPress को अद्यतन रखें।",
          "WooCommerce ईमेल सेटिंग्स बदलने के बाद ग्राहक नोट कार्यप्रवाह जाँचें।",
          "बहुत बड़े टेम्पलेट सेट को आयात करने से पहले स्टेजिंग साइट उपयोग करें।"
        ]
      },
      {
        "title": "11. समस्या निवारण",
        "paragraphs": [
          "यदि ऑर्डर में टेम्पलेट दिखाई नहीं देते, तो जाँचें कि WooCommerce सक्रिय है, टेम्पलेट प्रकाशित है और वर्तमान उपयोगकर्ता के पास टेम्पलेट उपयोग करने की अनुमति है।",
          "यदि अनुवाद दिखाई नहीं देते, तो WordPress में साइट भाषा और उपयोगकर्ता भाषा जाँचें। प्लगइन में फ़ारसी सहित सभी समर्थित भाषाओं के लिए समीक्षा किए गए बंडल फ़ॉलबैक शामिल हैं। अन्य भाषाएँ समीक्षा किए गए WordPress.org भाषा पैक से प्रदान की जानी चाहिए।"
        ],
        "items": [
          "अपडेट के बाद पुरानी एडमिन स्क्रीन दिखे तो ऑब्जेक्ट कैश या कैश प्लगइन साफ करें।",
          "यदि प्लेसहोल्डर नहीं बदलता तो सुनिश्चित करें कि वह घुँघराले कोष्ठकों सहित सूची के अनुसार बिल्कुल सही लिखा गया है।",
          "यदि ग्राहक नोट ईमेल नहीं होते तो ग्राहक नोट सूचना के लिए WooCommerce ईमेल सेटिंग्स जाँचें।"
        ]
      },
      {
        "title": "12. सेटिंग्स पृष्ठ",
        "paragraphs": [
          "<strong>Mailhilfe ऑर्डर नोट्स → सेटिंग्स</strong> खोलकर डिफ़ॉल्ट नोट प्रकार, सुरक्षित HTML व्यवहार, उपयोग प्रदर्शन, पसंदीदा, JSON आयात विकल्प और भाषा मिलान चुनें। अधिक सुरक्षित दैनिक कार्य के लिए आंतरिक नोट को डिफ़ॉल्ट रखें।"
        ],
        "items": []
      },
      {
        "title": "13. टेम्पलेट भाषा और बहुभाषी दुकानें",
        "paragraphs": [
          "हर टेम्पलेट की एक टेम्पलेट भाषा हो सकती है। यदि वही टेक्स्ट सभी ऑर्डर के लिए उपयोग किया जा सकता है तो <strong>सभी भाषाएँ</strong> चुनें, अन्यथा स्थानीयकृत टेक्स्ट के लिए विशिष्ट भाषा चुनें।",
          "उपलब्ध होने पर प्लगइन ऑर्डर भाषा, उपयोगकर्ता भाषा या बहुभाषी प्लगइन से प्राप्त सामान्य भाषा डेटा से मेल खाने वाले टेम्पलेट को प्राथमिकता दे सकता है।"
        ],
        "items": [
          "आंतरिक कर्मचारी नोट के लिए एक तटस्थ टेम्पलेट उपयोग करें।",
          "हिंदी, जर्मन, अंग्रेज़ी या अन्य दुकान भाषाओं के लिए अलग ग्राहक-उन्मुख टेम्पलेट बनाएँ।",
          "WPML या Polylang दुकान में वास्तविक परीक्षण ऑर्डर से भाषा पहचान जाँचें।"
        ]
      },
      {
        "title": "14. कस्टम फ़ील्ड और मेटा प्लेसहोल्डर",
        "paragraphs": [
          "उन्नत प्लेसहोल्डर चुने हुए ऑर्डर या ग्राहक मेटा फ़ील्ड पढ़ सकते हैं। ऑर्डर डेटा के लिए <code>{order_meta:meta_key}</code> और ग्राहक उपयोगकर्ता डेटा के लिए <code>{customer_meta:meta_key}</code> उपयोग करें।",
          "सुरक्षा के लिए password, token, secret, session, auth और hash जैसे संवेदनशील कुंजी नाम अवरुद्ध हैं। मेटा प्लेसहोल्डर केवल तब उपयोग करें जब आपको पता हो कि फ़ील्ड में क्या है।"
        ],
        "items": [
          "उदाहरण: शिपिंग प्लगइन द्वारा सहेजे गए ट्रैकिंग नंबर के लिए <code>{order_meta:_tracking_number}</code>।",
          "उदाहरण: VAT ID फ़ील्ड के लिए <code>{order_meta:_billing_vat_id}</code>।",
          "ग्राहक नोट्स में आंतरिक या संवेदनशील फ़ील्ड उजागर न करें।"
        ]
      },
      {
        "title": "15. टेम्पलेट की प्रतिलिपि और संशोधन",
        "paragraphs": [
          "जब थोड़े बदलाव के साथ समान टेम्पलेट चाहिए तो प्रतिलिपि क्रिया उपयोग करें। प्रति ड्राफ़्ट के रूप में बनती है ताकि प्रकाशित करने से पहले उसकी समीक्षा की जा सके।",
          "टेम्पलेट संशोधन आपको पुराने संस्करणों की तुलना करने और गलती से हुए बदलाव के बाद पिछला टेक्स्ट पुनर्स्थापित करने देते हैं।"
        ],
        "items": [
          "DHL, UPS या पिकअप के अलग रूप बनाने से पहले सामान्य शिपिंग टेम्पलेट की प्रतिलिपि बनाएँ।",
          "बड़े टेक्स्ट बदलावों के बाद संशोधन जाँचें।",
          "समान टेम्पलेट में भ्रम से बचने के लिए शीर्षक स्पष्ट रखें।"
        ]
      },
      {
        "title": "16. अनुमतियाँ पृष्ठ",
        "paragraphs": [
          "<strong>Mailhilfe ऑर्डर नोट्स → अनुमतियाँ</strong> खोलकर तय करें कि कौन-सी WordPress भूमिकाएँ टेम्पलेट प्रबंधित कर सकती हैं और कौन-सी भूमिकाएँ ऑर्डर में उनका उपयोग कर सकती हैं।",
          "प्रशासकों के पास आवश्यक अनुमतियाँ बनी रहती हैं। अन्य भूमिकाओं को केवल दैनिक कार्य के लिए आवश्यक अनुमतियाँ दें।"
        ],
        "items": [
          "टेम्पलेट प्रबंधित करें: टेम्पलेट बनाना, संपादित करना, हटाना, आयात और निर्यात करना।",
          "टेम्पलेट उपयोग करें: WooCommerce ऑर्डर में टेम्पलेट चुनना और नोट जोड़ना।",
          "आयात/निर्यात अनुमति केवल विश्वसनीय उपयोगकर्ताओं को दें।"
        ]
      },
      {
        "title": "17. आयात पूर्वावलोकन",
        "paragraphs": [
          "JSON आयात अब बदलाव लागू करने से पहले पूर्वावलोकन दिखाता है। पूर्वावलोकन बताता है कि कितने टेम्पलेट बनाए, अपडेट या छोड़े जाएंगे।",
          "पूर्वावलोकन जाँचने के बाद ही आयात की पुष्टि करें। इससे मौजूदा टेम्पलेट अनजाने में ओवरराइट होने से बचते हैं।"
        ],
        "items": [
          "बड़ा सेट आयात करने से पहले निर्यात बैकअप बनाएँ।",
          "केवल विश्वसनीय स्रोत की JSON फ़ाइल आयात करें।",
          "आयात के बाद कम से कम एक ग्राहक नोट और एक आंतरिक नोट जाँचें।"
        ]
      },
      {
        "title": "18. ग्राहक नोट ईमेल का व्यवहार",
        "paragraphs": [
          "संबंधित ईमेल सक्षम होने पर ग्राहक नोट WooCommerce ईमेल सूचना भेज सकते हैं। प्लगइन ग्राहक नोट बनने और ईमेल संसाधित होने को अलग-अलग दर्ज करता है। नोट जोड़ने से पहले संपादन योग्य पूर्वावलोकन देखें और मेल हैंडलर का परिणाम जाँचने के लिए इतिहास पृष्ठ उपयोग करें।"
        ],
        "items": []
      },
      {
        "title": "19. अनुशंसित कार्यप्रवाह",
        "paragraphs": [
          "सुरक्षित दैनिक कार्यप्रवाह है: टेम्पलेट चुनें, बदला हुआ पूर्वावलोकन जाँचें, आवश्यकता होने पर पूर्वावलोकन संपादित करें, नोट प्रकार सत्यापित करें और फिर नोट जोड़ें।",
          "नए टेम्पलेट को वास्तविक ग्राहकों के साथ उपयोग करने से पहले किसी गैर-महत्वपूर्ण ऑर्डर या स्टेजिंग दुकान में जाँचें।"
        ],
        "items": [
          "केवल कर्मचारियों की जानकारी के लिए आंतरिक नोट उपयोग करें।",
          "ग्राहक नोट केवल उन संदेशों के लिए उपयोग करें जो ग्राहक को भेजे जा सकते हैं।",
          "टेम्पलेट बदलने पर प्लेसहोल्डर फिर से जाँचें।"
        ]
      },
      {
        "title": "20. टेम्पलेट शर्तें",
        "paragraphs": [
          "टेम्पलेट शर्तें तय करती हैं कि कोई टेम्पलेट किसी विशेष ऑर्डर के लिए उपलब्ध होगा या नहीं। आप ऑर्डर स्थिति, भुगतान विधि, शिपिंग विधि, बिलिंग देश और न्यूनतम या अधिकतम ऑर्डर कुल के आधार पर टेम्पलेट सीमित कर सकते हैं। सभी कॉन्फ़िगर की गई शर्तों का मिलना आवश्यक है।"
        ],
        "items": [
          "जिस शर्त से टेम्पलेट सीमित नहीं करना है उसका फ़ील्ड खाली छोड़ें।",
          "भुगतान और शिपिंग विधियों की तकनीकी ID उपयोग करें।",
          "नोट बनने से पहले शर्तें इंटरफ़ेस में और सर्वर पर दोबारा जाँची जाती हैं।"
        ]
      },
      {
        "title": "21. ईमेल संसाधन लॉग",
        "paragraphs": [
          "ग्राहक नोट्स के लिए प्लगइन दर्ज करता है कि WooCommerce ने ग्राहक नोट ईमेल को संसाधित बताया या नहीं और तकनीकी wp_mail त्रुटियाँ भी दर्ज करता है। संसाधित घटना यह पुष्टि करती है कि WordPress/WooCommerce ने संदेश मेल प्रणाली को सौंपा; यह अंतिम डिलीवरी या ग्राहक द्वारा पढ़े जाने का प्रमाण नहीं है।"
        ],
        "items": [
          "संसाधित और विफल ईमेल घटनाओं के लिए इतिहास पृष्ठ देखें।",
          "यदि निश्चित डिलीवरी जानकारी चाहिए तो SMTP प्रदाता या मेल-लॉग सेवा उपयोग करें।",
          "आंतरिक नोट ग्राहक नोट ईमेल सक्रिय नहीं करते।"
        ]
      },
      {
        "title": "22. केंद्रीय इतिहास",
        "paragraphs": [
          "हाल में बने नोट, टेम्पलेट उपयोग, ईमेल संसाधन और ईमेल विफलताएँ देखने के लिए <strong>Mailhilfe ऑर्डर नोट्स → इतिहास</strong> खोलें। उपलब्ध होने पर प्रविष्टियों में ऑर्डर, टेम्पलेट, उपयोगकर्ता, प्राप्तकर्ता, घटना प्रकार और समय शामिल होते हैं।"
        ],
        "items": [
          "सहायता, ऑडिट और समस्या निवारण के लिए इतिहास उपयोग करें।",
          "इतिहास WooCommerce ऑर्डर नोट्स से अलग है।",
          "पृष्ठ नवीनतम 250 प्रविष्टियाँ दिखाता है।"
        ]
      },
      {
        "title": "23. परीक्षण ऑर्डर पूर्वावलोकन",
        "paragraphs": [
          "टेम्पलेट संपादक के परीक्षण पूर्वावलोकन क्षेत्र में WooCommerce ऑर्डर ID दर्ज करें। सहेजे न गए बदलावों सहित वर्तमान संपादक सामग्री उस ऑर्डर के डेटा के साथ दिखाई जाती है, बिना नोट बनाए या ईमेल भेजे।"
        ],
        "items": [
          "स्टेजिंग ऑर्डर या गैर-महत्वपूर्ण परीक्षण ऑर्डर उपयोग करें।",
          "खाली मान, फ़ॉर्मेटिंग, शर्तें और कस्टम मेटा प्लेसहोल्डर जाँचें।",
          "चयनित ऑर्डर संपादित करने की अनुमति आपके पास होनी चाहिए।"
        ]
      },
      {
        "title": "24. व्यक्तिगत पसंदीदा और हाल में उपयोग किए गए टेम्पलेट",
        "paragraphs": [
          "हर प्रशासक ऑर्डर स्क्रीन में व्यक्तिगत पसंदीदा चिह्नित कर सकता है। प्लगइन प्रत्येक उपयोगकर्ता के दस सबसे हाल में उपयोग किए गए टेम्पलेट भी सहेजता है और उन्हें चयन सूची में ऊपर रखता है। वैश्विक पसंदीदा सभी उपयोगकर्ताओं के साथ साझा रहते हैं।"
        ],
        "items": [
          "व्यक्तिगत पसंदीदा दूसरे उपयोगकर्ता की सूची नहीं बदलते।",
          "हाल की सूची केवल नोट सफलतापूर्वक जोड़ने के बाद अपडेट होती है।",
          "व्यक्तिगत डेटा WordPress उपयोगकर्ता मेटाडेटा के रूप में सहेजा जाता है।"
        ]
      },
      {
        "title": "25. निदान पृष्ठ",
        "paragraphs": [
          "WordPress, PHP और WooCommerce संस्करण, HPOS स्थिति, ग्राहक नोट ईमेल स्थिति, लोकेल, प्रकाशित टेम्पलेट संख्या, कैश स्थिति और WP_DEBUG जैसी तकनीकी जानकारी देखने के लिए <strong>Mailhilfe ऑर्डर नोट्स → निदान</strong> खोलें।"
        ],
        "items": [
          "सहायता माँगते समय निदान मान कॉपी करें।",
          "पृष्ठ ऑर्डर नोट सामग्री या ग्राहक पते प्रदर्शित नहीं करता।",
          "डेवलपर निदान फ़िल्टर से अतिरिक्त पंक्तियाँ जोड़ सकते हैं।"
        ]
      },
      {
        "title": "26. डेवलपर हुक और फ़िल्टर",
        "paragraphs": [
          "प्लगइन प्लेसहोल्डर, प्लेसहोल्डर मान, अनुमत मेटा कुंजियाँ, टेम्पलेट परिणाम, शर्तें, पूर्वावलोकन सामग्री, अंतिम नोट सामग्री, नोट जोड़ने से पहले और बाद की क्रियाएँ, इतिहास रिकॉर्ड और निदान के लिए हुक व फ़िल्टर देता है। हुक नाम और पैरामीटर readme.txt में दिए गए हैं।"
        ],
        "items": [
          "सभी कस्टम डेटा को वैलिडेट, सैनिटाइज़ और एस्केप करें।",
          "सीधे ऑर्डर तालिका पहुँच के बजाय WooCommerce ऑर्डर API उपयोग करें।",
          "कस्टम एक्सटेंशन को HPOS और पारंपरिक ऑर्डर संग्रहण दोनों के साथ संगत रखें।"
        ]
      }
    ]
  },
  "zh_CN": {
    "title": "Mailhilfe Order Note Manager for WooCommerce 详细帮助",
    "intro": "本帮助说明 Mailhilfe Order Note Manager for WooCommerce 的完整工作流程，包括创建和格式化模板、使用模板语言、占位符与元数据占位符、编辑预览、安全发送客户备注、设置权限、导入预览以及 HPOS 兼容性。",
    "sections": [
      {
        "title": "1. 插件的作用",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce 可将常用的 WooCommerce 订单备注保存为可重复使用的模板。这样无需反复输入相同内容，并能让订单历史中的沟通记录保持一致。",
          "模板可以设为员工使用的内部备注，也可以设为客户备注。在订单中使用模板时，仍可更改备注类型。"
        ],
        "items": [
          "典型用途包括付款提醒、配送延迟、电话沟通记录、地址核对和客户服务回复。",
          "模板支持分类、收藏、排序、使用次数统计和 JSON 备份。"
        ]
      },
      {
        "title": "2. 创建新模板",
        "paragraphs": [
          "打开 <strong>Mailhilfe 订单备注 → 新建</strong>。输入清晰的标题，在编辑器中填写备注文本，并选择默认备注类型是内部备注还是客户备注。",
          "标题应简要说明用途，例如“付款提醒”或“客户来电询问配送”。这样更容易在订单页面中找到模板。"
        ],
        "items": [
          "模板较多时可分配一个或多个分类。",
          "将经常使用的模板标记为收藏。",
          "发布模板后，它才会在订单中可用。"
        ]
      },
      {
        "title": "3. 设置模板文本格式",
        "paragraphs": [
          "模板文本使用 WordPress 编辑器。您可以使用段落、粗体、斜体、列表和链接。创建备注时会保留格式，但内容会按照 WordPress 的安全 HTML 规则进行清理。",
          "客户备注中的格式应简洁。短段落或项目列表通常比冗长且无结构的文本更易阅读。"
        ],
        "items": [
          "推荐结构：简短问候、明确说明和下一步操作。",
          "客户备注中应避免使用内部缩写。",
          "不要在可能用作客户备注的模板中加入员工私密评论。"
        ]
      },
      {
        "title": "4. 占位符",
        "paragraphs": [
          "占位符是放在花括号中的文字。预览备注以及向订单添加备注时，占位符会被真实订单数据替换。",
          "普通文本可以与占位符组合使用。例如：<code>您好 {customer}，我们已收到您的订单 {order_number}。</code>"
        ],
        "items": [
          "订单：<code>{order_number}</code>、<code>{order_status}</code>、<code>{order_date}</code>、<code>{order_total}</code>。",
          "客户：<code>{customer}</code>、<code>{billing_email}</code>、<code>{billing_phone}</code>。",
          "配送与付款：<code>{shipping_method}</code>、<code>{payment_method}</code>。",
          "商品与商店：<code>{items}</code>、<code>{item_count}</code>、<code>{site_name}</code>。"
        ]
      },
      {
        "title": "5. 添加备注前预览",
        "paragraphs": [
          "打开一个 WooCommerce 订单并选择模板。预览会显示已经用该订单数据替换占位符后的备注。",
          "创建备注前务必检查预览。当订单中没有占位符对应的值时尤其重要，例如配送公司或电话号码缺失。"
        ],
        "items": [
          "检查姓名、金额、配送方式和商品列表。",
          "确认所选备注类型是否正确。",
          "如果相同文本需要对今后的所有订单进行改进，应先编辑模板。"
        ]
      },
      {
        "title": "6. 内部备注与客户备注",
        "paragraphs": [
          "内部备注供商店员工使用，通常用于记录、后续任务或服务历史。客户备注可能对客户可见，并且根据 WooCommerce 设置可能触发电子邮件通知。",
          "请认真检查可编辑预览和所选备注类型。客户备注中只能包含允许客户查看的内容。"
        ],
        "items": [
          "内部备注示例：“客户来电，配送地址已确认。”",
          "客户备注示例：“您的订单正在准备中，将很快发货。”",
          "不要在客户备注中加入密码、私密评论或仅供供应商查看的信息。"
        ]
      },
      {
        "title": "7. 收藏、搜索与排序",
        "paragraphs": [
          "收藏可将最重要的模板放在选择列表前部。订单页面中的搜索框可按标题、分类或内容查找模板。",
          "您可以在模板列表中拖放调整顺序。保存后的顺序会用于订单页面中的模板显示。"
        ],
        "items": [
          "将日常使用的模板设为收藏。",
          "使用分类整理付款、配送、退货和支持等主题。",
          "标题保持简短，以便搜索结果易于阅读。"
        ]
      },
      {
        "title": "8. 导入、导出与演示模板",
        "paragraphs": [
          "JSON 导出会为模板创建备份，可在进行较大更改前使用，也可将模板迁移到另一家商店。",
          "JSON 导入可以创建模板，并更新标题或内部演示键相同的现有模板。演示模板可作为快速起点，并会按当前语言创建。"
        ],
        "items": [
          "批量更改前先导出备份。",
          "仅导入来自可信来源的 JSON 文件。",
          "导入后打开几个模板，检查格式和占位符。"
        ]
      },
      {
        "title": "9. 权限与角色",
        "paragraphs": [
          "插件分别使用“管理模板”和“在订单中使用模板”的权限。激活插件时，管理员和商店经理会自动获得这些权限。",
          "如使用角色编辑器插件，可以为自定义角色授予或移除这些权限。"
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>：创建、编辑、删除和导入/导出模板。",
          "<code>use_mh_order_note_templates</code>：在 WooCommerce 订单中使用模板。",
          "没有所需权限的用户不会看到相关后台功能。"
        ]
      },
      {
        "title": "10. 安全性与 HPOS 兼容性",
        "paragraphs": [
          "插件在后台操作中使用 WordPress nonce、权限检查、数据清理和输出转义。模板内容在保存或使用前会按照 WordPress 安全 HTML 规则进行清理。",
          "订单数据通过 WooCommerce 订单 API 读取，而不是直接访问数据库表，因此兼容 WooCommerce HPOS 和经典订单存储。"
        ],
        "items": [
          "保持 WooCommerce 和 WordPress 为最新版本。",
          "更改 WooCommerce 电子邮件设置后测试客户备注流程。",
          "导入大量模板前先在测试站点中验证。"
        ]
      },
      {
        "title": "11. 故障排除",
        "paragraphs": [
          "如果订单中没有显示模板，请检查 WooCommerce 是否已启用、模板是否已发布，以及当前用户是否有使用模板的权限。",
          "如果没有显示翻译，请检查 WordPress 的站点语言和用户语言。插件为所有受支持的语言（包括波斯语）内置了经过审核的回退文件。其他语言应通过经过审核的 WordPress.org 语言包提供。"
        ],
        "items": [
          "更新后如果仍显示旧后台页面，请清除对象缓存或缓存插件。",
          "如果占位符没有被替换，请确认其拼写与列表完全一致，包括花括号。",
          "如果客户备注没有发送邮件，请检查 WooCommerce 的客户备注邮件通知设置。"
        ]
      },
      {
        "title": "12. 设置页面",
        "paragraphs": [
          "打开 <strong>Mailhilfe 订单备注 → 设置</strong>，可配置默认备注类型、安全 HTML、使用次数显示、收藏、JSON 导入选项和语言匹配。日常使用时建议将内部备注设为默认类型，以降低误发风险。"
        ],
        "items": []
      },
      {
        "title": "13. 模板语言与多语言商店",
        "paragraphs": [
          "每个模板都可以设置模板语言。如果同一文本可用于所有订单，请选择<strong>所有语言</strong>；如需本地化文本，请选择指定语言。",
          "在可用的情况下，插件可优先显示与订单语言、用户语言或多语言插件中的常用语言数据匹配的模板。"
        ],
        "items": [
          "员工内部备注可以使用一个中性模板。",
          "为中文、德语、英语或其他商店语言创建不同的客户模板。",
          "使用 WPML 或 Polylang 时，请通过真实测试订单验证订单语言识别。"
        ]
      },
      {
        "title": "14. 自定义字段与元数据占位符",
        "paragraphs": [
          "高级占位符可读取选定的订单或客户元数据字段。订单数据使用 <code>{order_meta:meta_key}</code>，客户用户数据使用 <code>{customer_meta:meta_key}</code>。",
          "出于安全考虑，包含 password、token、secret、session、auth 和 hash 等敏感词的键名会被阻止。只有在了解字段内容时才应使用元数据占位符。"
        ],
        "items": [
          "示例：<code>{order_meta:_tracking_number}</code> 用于读取配送插件保存的物流单号。",
          "示例：<code>{order_meta:_billing_vat_id}</code> 用于读取增值税识别号字段。",
          "不要在客户备注中暴露内部字段或敏感字段。"
        ]
      },
      {
        "title": "15. 复制模板与修订版本",
        "paragraphs": [
          "需要创建仅有少量差异的模板时，可使用复制操作。副本会以草稿形式创建，以便发布前检查。",
          "模板修订版本可用于比较早期版本，并在误改后恢复以前的文本。"
        ],
        "items": [
          "创建 DHL、UPS 或自提版本前，先复制通用配送模板。",
          "大幅修改文本后检查修订版本。",
          "标题应清晰，避免混淆相似模板。"
        ]
      },
      {
        "title": "16. 权限页面",
        "paragraphs": [
          "打开 <strong>Mailhilfe 订单备注 → 权限</strong>，决定哪些 WordPress 角色可以管理模板，以及哪些角色可以在订单中使用模板。",
          "管理员会保留必要权限。其他角色只应获得日常工作所需的权限。"
        ],
        "items": [
          "管理模板：创建、编辑、删除、导入和导出模板。",
          "使用模板：选择模板并在 WooCommerce 订单中添加备注。",
          "仅向受信任的用户授予导入/导出权限。"
        ]
      },
      {
        "title": "17. 导入预览",
        "paragraphs": [
          "JSON 导入会在执行更改前显示预览。预览会说明将创建、更新或跳过多少个模板。",
          "检查预览后再确认导入，以避免意外覆盖现有模板。"
        ],
        "items": [
          "导入大量模板前先创建导出备份。",
          "仅导入来自可信来源的 JSON 文件。",
          "导入后至少测试一条客户备注和一条内部备注。"
        ]
      },
      {
        "title": "18. 客户备注电子邮件行为",
        "paragraphs": [
          "启用相应邮件后，客户备注可能触发 WooCommerce 电子邮件通知。插件会分别记录客户备注创建和电子邮件处理状态。添加备注前请检查可编辑预览，并在“历史记录”页面查看邮件处理程序的结果。"
        ],
        "items": []
      },
      {
        "title": "19. 推荐工作流程",
        "paragraphs": [
          "安全的日常流程是：选择模板，检查替换后的预览，必要时编辑预览，确认备注类型，然后添加备注。",
          "新模板应先在非关键订单或测试商店中验证，再用于真实客户。"
        ],
        "items": [
          "仅供员工查看的信息使用内部备注。",
          "只有可能发送给客户的内容才使用客户备注。",
          "每次更改模板后都检查占位符。"
        ]
      },
      {
        "title": "20. 模板条件",
        "paragraphs": [
          "模板条件决定模板是否适用于某个订单。您可以按订单状态、付款方式、配送方式、账单国家/地区以及订单最低或最高金额限制模板。所有已配置条件都必须满足。"
        ],
        "items": [
          "某项条件不应限制模板时，将该字段留空。",
          "付款方式和配送方式应使用技术 ID。",
          "创建备注前，系统会在界面和服务器端再次检查条件。"
        ]
      },
      {
        "title": "21. 电子邮件处理日志",
        "paragraphs": [
          "对于客户备注，插件会记录 WooCommerce 报告客户备注邮件已处理的时间，也会记录 wp_mail 技术错误。“已处理”仅表示 WordPress/WooCommerce 已将邮件交给邮件系统，不能证明最终送达或客户已阅读。"
        ],
        "items": [
          "在“历史记录”页面查看已处理和失败的邮件事件。",
          "需要确定送达信息时，请使用 SMTP 服务商或邮件日志服务。",
          "内部备注不会触发客户备注邮件。"
        ]
      },
      {
        "title": "22. 集中历史记录",
        "paragraphs": [
          "打开 <strong>Mailhilfe 订单备注 → 历史记录</strong>，可查看最近的备注创建、模板使用、电子邮件处理和邮件失败记录。可用时，记录会包含订单、模板、用户、收件人、事件类型和时间。"
        ],
        "items": [
          "历史记录可用于支持、审计和故障排除。",
          "该历史记录独立于 WooCommerce 订单备注。",
          "页面显示最近 250 条记录。"
        ]
      },
      {
        "title": "23. 测试订单预览",
        "paragraphs": [
          "在模板编辑器的测试预览区域输入 WooCommerce 订单 ID。系统会使用该订单的数据渲染当前编辑器内容（包括尚未保存的更改），不会创建备注或发送电子邮件。"
        ],
        "items": [
          "使用测试订单或非关键订单。",
          "检查缺失值、格式、条件和自定义元数据占位符。",
          "您必须有权编辑所选订单。"
        ]
      },
      {
        "title": "24. 个人收藏与最近使用的模板",
        "paragraphs": [
          "每位管理员都可以在订单页面标记个人收藏。插件还会为每个用户保存最近使用的 10 个模板，并在选择列表中提高其位置。全局收藏仍由所有用户共享。"
        ],
        "items": [
          "个人收藏不会改变其他用户的列表。",
          "只有成功添加备注后才会更新最近使用列表。",
          "个人数据以 WordPress 用户元数据形式保存。"
        ]
      },
      {
        "title": "25. 诊断页面",
        "paragraphs": [
          "打开 <strong>Mailhilfe 订单备注 → 诊断</strong>，可查看 WordPress、PHP 和 WooCommerce 版本、HPOS 状态、客户备注邮件状态、区域设置、已发布模板数量、缓存状态和 WP_DEBUG 等技术信息。"
        ],
        "items": [
          "请求支持时复制诊断值。",
          "该页面不会显示订单备注内容或客户地址。",
          "开发者可以通过诊断过滤器添加行。"
        ]
      },
      {
        "title": "26. 开发者钩子与过滤器",
        "paragraphs": [
          "插件为占位符、占位符值、允许的元数据键、模板结果、条件、预览内容、最终备注内容、添加备注前后的操作、历史记录和诊断提供钩子与过滤器。钩子名称和参数记录在 readme.txt 中。"
        ],
        "items": [
          "验证、清理并转义所有自定义数据。",
          "使用 WooCommerce 订单 API，不要直接访问订单数据库表。",
          "确保自定义扩展同时兼容 HPOS 和经典订单存储。"
        ]
      }
    ]
  },
  "ja": {
    "title": "Mailhilfe Order Note Manager for WooCommerce 詳細ヘルプ",
    "intro": "このヘルプでは、Mailhilfe Order Note Manager for WooCommerce の全体的な操作手順を説明します。テンプレートの作成と書式設定、テンプレート言語、プレースホルダーとメタプレースホルダー、プレビュー編集、顧客向けメモの安全な送信、設定、権限、インポートプレビュー、HPOS 互換性を扱います。",
    "sections": [
      {
        "title": "1. プラグインの機能",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce を使用すると、よく使う WooCommerce の注文メモを再利用可能なテンプレートとして保存できます。同じ文章を繰り返し入力する必要がなくなり、注文履歴内のコミュニケーションを統一できます。",
          "テンプレートはスタッフ用の内部メモまたは顧客向けメモとして準備できます。注文でテンプレートを使用する際に、メモの種類を変更することもできます。"
        ],
        "items": [
          "一般的な用途: 支払いリマインダー、配送遅延、電話対応記録、住所確認、カスタマーサービスの回答。",
          "テンプレートではカテゴリー、お気に入り、並べ替え、使用回数、JSON バックアップを利用できます。"
        ]
      },
      {
        "title": "2. 新しいテンプレートを作成する",
        "paragraphs": [
          "<strong>Mailhilfe 注文メモ → 新規追加</strong>を開きます。分かりやすいタイトルを入力し、エディターにメモ本文を記入して、既定のメモ種類を内部メモまたは顧客向けメモから選択します。",
          "タイトルには「支払いリマインダー」や「配送について顧客から電話」など、用途を短く記載してください。注文画面でテンプレートを見つけやすくなります。"
        ],
        "items": [
          "テンプレートが多い場合は、1 つ以上のカテゴリーを割り当てます。",
          "よく使うテンプレートをお気に入りに追加します。",
          "注文で使用できるようにテンプレートを公開します。"
        ]
      },
      {
        "title": "3. テンプレート本文の書式設定",
        "paragraphs": [
          "テンプレート本文には WordPress エディターを使用します。段落、太字、斜体、リスト、リンクで文章を整形できます。メモ作成時に書式は保持されますが、内容は WordPress の安全な HTML ルールに従ってサニタイズされます。",
          "顧客向けメモでは書式を控えめに使用してください。長く構造のない文章より、短い段落や箇条書きの方が読みやすくなります。"
        ],
        "items": [
          "良い例: 短い挨拶、明確な説明、次に行うことを 1 つずつ記載します。",
          "顧客向けメモでは社内略語を避けます。",
          "顧客向けメモとして使用する可能性があるテンプレートに、スタッフだけが見るべきコメントを入れないでください。"
        ]
      },
      {
        "title": "4. プレースホルダー",
        "paragraphs": [
          "プレースホルダーは波括弧で囲まれた文字列です。プレビュー表示時と注文にメモを追加する際に、実際の注文データに置き換えられます。",
          "通常の文章とプレースホルダーを組み合わせられます。例: <code>{customer} 様、ご注文 {order_number} を承りました。</code>"
        ],
        "items": [
          "注文: <code>{order_number}</code>、<code>{order_status}</code>、<code>{order_date}</code>、<code>{order_total}</code>。",
          "顧客: <code>{customer}</code>、<code>{billing_email}</code>、<code>{billing_phone}</code>。",
          "配送と支払い: <code>{shipping_method}</code>、<code>{payment_method}</code>。",
          "商品とショップ: <code>{items}</code>、<code>{item_count}</code>、<code>{site_name}</code>。"
        ]
      },
      {
        "title": "5. メモを追加する前のプレビュー",
        "paragraphs": [
          "WooCommerce の注文を開いてテンプレートを選択します。プレビューには、選択した注文データでプレースホルダーを置換したメモが表示されます。",
          "メモを作成する前に必ずプレビューを確認してください。配送会社名や電話番号がないなど、注文にプレースホルダーの値が存在しない場合は特に重要です。"
        ],
        "items": [
          "氏名、合計金額、配送方法、商品一覧を確認します。",
          "選択したメモ種類が正しいか確認します。",
          "今後のすべての注文で同じ文章を改善したい場合は、先にテンプレートを編集します。"
        ]
      },
      {
        "title": "6. 内部メモと顧客向けメモ",
        "paragraphs": [
          "内部メモはショップスタッフ向けで、通常は記録、フォローアップ作業、対応履歴に使用します。顧客向けメモは顧客に表示される可能性があり、WooCommerce の設定によってはメール通知が送信されます。",
          "編集可能なプレビューと選択したメモ種類を慎重に確認してください。顧客向けメモには、顧客が読んでもよい内容だけを使用します。"
        ],
        "items": [
          "内部メモの例: 「顧客から電話あり。配送先住所を確認済み。」",
          "顧客向けメモの例: 「ご注文の準備を進めており、まもなく発送いたします。」",
          "顧客向けメモにパスワード、非公開コメント、仕入先専用情報を記載しないでください。"
        ]
      },
      {
        "title": "7. お気に入り、検索、並べ替え",
        "paragraphs": [
          "お気に入りを使用すると、重要なテンプレートを選択リストの上部に表示できます。注文画面の検索欄では、タイトル、カテゴリー、内容からテンプレートを検索できます。",
          "テンプレート一覧ではドラッグ＆ドロップで順序を変更できます。保存した順序は注文画面のテンプレート表示に反映されます。"
        ],
        "items": [
          "日常的に使用するテンプレートをお気に入りにします。",
          "支払い、配送、返品、サポートなどのテーマ別にカテゴリーを使用します。",
          "検索結果を読みやすくするため、タイトルは短くします。"
        ]
      },
      {
        "title": "8. インポート、エクスポート、デモテンプレート",
        "paragraphs": [
          "JSON エクスポートはテンプレートのバックアップを作成します。大きな変更の前や、別のショップへテンプレートを移行する際に使用できます。",
          "JSON インポートではテンプレートを作成し、同じタイトルまたは内部デモキーを持つ既存テンプレートを更新できます。デモテンプレートは作業開始用の例で、現在の言語で作成されます。"
        ],
        "items": [
          "一括変更の前にエクスポートします。",
          "信頼できる提供元の JSON ファイルだけをインポートします。",
          "インポート後に複数のテンプレートを開き、書式とプレースホルダーを確認します。"
        ]
      },
      {
        "title": "9. 権限と権限グループ",
        "paragraphs": [
          "このプラグインでは、テンプレートの管理権限と注文でテンプレートを使用する権限を分けています。管理者とショップマネージャーには、プラグイン有効化時にこれらの権限が自動的に付与されます。",
          "権限グループ編集プラグインを使用している場合は、独自の権限グループにこれらの権限を付与または削除できます。"
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: テンプレートの作成、編集、削除、インポート、エクスポート。",
          "<code>use_mh_order_note_templates</code>: WooCommerce の注文でテンプレートを使用。",
          "必要な権限を持たないユーザーには、関連する管理機能が表示されません。"
        ]
      },
      {
        "title": "10. セキュリティと HPOS 互換性",
        "paragraphs": [
          "管理操作では WordPress の nonce、権限チェック、サニタイズ、エスケープを使用します。テンプレート内容は保存または使用する前に、WordPress の安全な HTML ルールで処理されます。",
          "注文データはデータベーステーブルへ直接アクセスせず、WooCommerce の注文 API から読み取ります。このため WooCommerce HPOS と従来の注文ストレージの両方に対応します。"
        ],
        "items": [
          "WooCommerce と WordPress を最新の状態に保ちます。",
          "WooCommerce のメール設定を変更した後は、顧客向けメモの流れをテストします。",
          "大量のテンプレートをインポートする前にステージングサイトを使用します。"
        ]
      },
      {
        "title": "11. トラブルシューティング",
        "paragraphs": [
          "注文にテンプレートが表示されない場合は、WooCommerce が有効であること、テンプレートが公開済みであること、現在のユーザーにテンプレート使用権限があることを確認してください。",
          "翻訳が表示されない場合は、WordPress のサイト言語とユーザー言語を確認してください。プラグインには、ペルシア語を含むすべての対応言語のレビュー済みフォールバックファイルが同梱されています。その他の言語は、レビュー済みの WordPress.org 言語パックから提供してください。"
        ],
        "items": [
          "更新後も古い管理画面が表示される場合は、オブジェクトキャッシュまたはキャッシュプラグインを削除します。",
          "プレースホルダーが置換されない場合は、波括弧を含めて一覧と完全に同じ表記か確認します。",
          "顧客向けメモのメールが送信されない場合は、WooCommerce の顧客向けメモ通知メール設定を確認します。"
        ]
      },
      {
        "title": "12. 設定ページ",
        "paragraphs": [
          "<strong>Mailhilfe 注文メモ → 設定</strong>を開き、既定のメモ種類、安全な HTML の動作、使用回数表示、お気に入り、JSON インポート、言語照合を設定します。日常業務での誤送信を減らすため、既定値には内部メモを推奨します。"
        ],
        "items": []
      },
      {
        "title": "13. テンプレート言語と多言語ショップ",
        "paragraphs": [
          "各テンプレートにはテンプレート言語を設定できます。同じ文章をすべての注文で使用できる場合は<strong>すべての言語</strong>を選び、言語別の文章には特定の言語を選択します。",
          "利用可能な場合、注文言語、ユーザー言語、多言語プラグインの一般的な言語データに一致するテンプレートを優先できます。"
        ],
        "items": [
          "スタッフ用の内部メモには、1 つの共通テンプレートを使用できます。",
          "日本語、ドイツ語、英語など、ショップで使用する言語ごとに顧客向けテンプレートを作成します。",
          "WPML または Polylang を使用する場合は、実際のテスト注文で注文言語の検出を確認します。"
        ]
      },
      {
        "title": "14. カスタムフィールドとメタプレースホルダー",
        "paragraphs": [
          "高度なプレースホルダーでは、選択した注文または顧客のメタフィールドを読み取れます。注文データには <code>{order_meta:meta_key}</code>、顧客ユーザーデータには <code>{customer_meta:meta_key}</code> を使用します。",
          "セキュリティ上、password、token、secret、session、auth、hash などの機密語を含むキー名はブロックされます。フィールドの内容を理解している場合にのみ、メタプレースホルダーを使用してください。"
        ],
        "items": [
          "例: 配送プラグインが保存した追跡番号には <code>{order_meta:_tracking_number}</code>。",
          "例: VAT ID フィールドには <code>{order_meta:_billing_vat_id}</code>。",
          "内部情報や機密フィールドを顧客向けメモに表示しないでください。"
        ]
      },
      {
        "title": "15. テンプレートの複製とリビジョン",
        "paragraphs": [
          "一部だけ異なる似たテンプレートが必要な場合は、複製操作を使用します。コピーは下書きとして作成されるため、公開前に確認できます。",
          "テンプレートのリビジョンでは以前の版を比較し、誤って変更した場合に前の文章を復元できます。"
        ],
        "items": [
          "DHL、UPS、店頭受取用を作る前に、共通の配送テンプレートを複製します。",
          "大きな文章変更の後はリビジョンを確認します。",
          "似たテンプレートを混同しないよう、明確なタイトルを付けます。"
        ]
      },
      {
        "title": "16. 権限ページ",
        "paragraphs": [
          "<strong>Mailhilfe 注文メモ → 権限</strong>を開き、どの WordPress 権限グループがテンプレートを管理できるか、どの権限グループが注文でテンプレートを使用できるかを設定します。",
          "管理者には必要な権限が保持されます。その他の権限グループには、日常業務に必要な権限だけを付与します。"
        ],
        "items": [
          "テンプレート管理: 作成、編集、削除、インポート、エクスポート。",
          "テンプレート使用: WooCommerce の注文でテンプレートを選択し、メモを追加。",
          "インポート/エクスポート権限は信頼できるユーザーだけに付与します。"
        ]
      },
      {
        "title": "17. インポートプレビュー",
        "paragraphs": [
          "JSON インポートでは、変更を適用する前にプレビューが表示されます。作成、更新、スキップされるテンプレート数を確認できます。",
          "既存テンプレートの意図しない上書きを防ぐため、プレビューを確認した後にのみインポートを確定してください。"
        ],
        "items": [
          "大量のテンプレートをインポートする前にエクスポートバックアップを作成します。",
          "信頼できる提供元の JSON ファイルだけをインポートします。",
          "インポート後に顧客向けメモと内部メモを少なくとも 1 件ずつテストします。"
        ]
      },
      {
        "title": "18. 顧客向けメモのメール動作",
        "paragraphs": [
          "対応するメールが有効な場合、顧客向けメモによって WooCommerce のメール通知が送信されることがあります。プラグインは顧客向けメモの作成とメール処理状態を別々に記録します。メモ追加前に編集可能なプレビューを確認し、「履歴」ページでメール処理結果を確認してください。"
        ],
        "items": []
      },
      {
        "title": "19. 推奨ワークフロー",
        "paragraphs": [
          "安全な日常手順は、テンプレートを選択し、置換後のプレビューを確認し、必要に応じてプレビューを編集し、メモ種類を確認してからメモを追加することです。",
          "新しいテンプレートは実際の顧客に使用する前に、重要でない注文またはテストショップで検証してください。"
        ],
        "items": [
          "スタッフだけが見る情報には内部メモを使用します。",
          "顧客に送信してよい内容だけを顧客向けメモに使用します。",
          "テンプレートを変更するたびにプレースホルダーを確認します。"
        ]
      },
      {
        "title": "20. テンプレート条件",
        "paragraphs": [
          "テンプレート条件は、特定の注文でテンプレートを利用できるかを決定します。注文ステータス、支払い方法、配送方法、請求先の国、注文合計の下限または上限でテンプレートを制限できます。設定したすべての条件を満たす必要があります。"
        ],
        "items": [
          "条件でテンプレートを制限しない項目は空欄にします。",
          "支払い方法と配送方法には技術的な ID を使用します。",
          "メモ作成前に、画面側とサーバー側の両方で条件が再確認されます。"
        ]
      },
      {
        "title": "21. メール処理ログ",
        "paragraphs": [
          "顧客向けメモでは、WooCommerce が顧客向けメモメールを処理したと報告した時刻と、wp_mail の技術的エラーを記録します。「処理済み」は WordPress/WooCommerce がメールシステムへメッセージを渡したことを示すだけで、最終配信や顧客の開封を証明しません。"
        ],
        "items": [
          "「履歴」ページで処理済みおよび失敗したメールイベントを確認します。",
          "配信結果が必要な場合は、SMTP プロバイダーまたはメールログサービスを使用します。",
          "内部メモでは顧客向けメモメールは送信されません。"
        ]
      },
      {
        "title": "22. 一元化された履歴",
        "paragraphs": [
          "<strong>Mailhilfe 注文メモ → 履歴</strong>を開くと、最近のメモ作成、テンプレート使用、メール処理、メール失敗を確認できます。利用可能な場合、注文、テンプレート、ユーザー、受信者、イベント種類、時刻が記録されます。"
        ],
        "items": [
          "履歴はサポート、監査、トラブルシューティングに使用できます。",
          "この履歴は WooCommerce の注文メモとは別に保存されます。",
          "ページには最新 250 件の記録が表示されます。"
        ]
      },
      {
        "title": "23. テスト注文プレビュー",
        "paragraphs": [
          "テンプレートエディターのテストプレビュー欄に WooCommerce の注文 ID を入力します。未保存の変更を含む現在のエディター内容が、その注文データで表示されます。メモの作成やメール送信は行われません。"
        ],
        "items": [
          "テスト注文または重要でない注文を使用します。",
          "不足値、書式、条件、カスタムメタプレースホルダーを確認します。",
          "選択した注文を編集する権限が必要です。"
        ]
      },
      {
        "title": "24. 個人のお気に入りと最近使用したテンプレート",
        "paragraphs": [
          "各管理ユーザーは注文画面で個人のお気に入りを設定できます。また、ユーザーごとに最近使用した 10 件のテンプレートを保存し、選択リストで上位に表示します。グローバルのお気に入りは引き続き全ユーザーで共有されます。"
        ],
        "items": [
          "個人のお気に入りは他のユーザーの一覧を変更しません。",
          "最近使用した一覧はメモの追加に成功した後だけ更新されます。",
          "個人データは WordPress のユーザーメタとして保存されます。"
        ]
      },
      {
        "title": "25. 診断ページ",
        "paragraphs": [
          "<strong>Mailhilfe 注文メモ → 診断</strong>を開くと、WordPress、PHP、WooCommerce のバージョン、HPOS 状態、顧客向けメモメール状態、ロケール、公開済みテンプレート数、キャッシュ状態、WP_DEBUG などの技術情報を確認できます。"
        ],
        "items": [
          "サポートを依頼する際は診断値をコピーします。",
          "このページには注文メモ本文や顧客住所は表示されません。",
          "開発者は診断フィルターを使用して行を追加できます。"
        ]
      },
      {
        "title": "26. 開発者向けフックとフィルター",
        "paragraphs": [
          "プレースホルダー、プレースホルダー値、許可するメタキー、テンプレート結果、条件、プレビュー内容、最終メモ内容、メモ追加前後のアクション、履歴レコード、診断のためのフックとフィルターを提供します。フック名と引数は readme.txt に記載されています。"
        ],
        "items": [
          "すべてのカスタムデータを検証、サニタイズ、エスケープします。",
          "注文テーブルへ直接アクセスせず、WooCommerce の注文 API を使用します。",
          "独自拡張を HPOS と従来の注文ストレージの両方に対応させます。"
        ]
      }
    ]
  },
  "nl_NL": {
    "title": "Uitgebreide hulp voor Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Deze hulp beschrijft de volledige werkwijze van Mailhilfe Order Note Manager for WooCommerce: sjablonen maken en opmaken, sjabloontalen gebruiken, plaatshouders en metaplaatshouders toepassen, voorbeelden bewerken, klantnotities veilig verzenden, instellingen en rechten beheren, importvoorbeelden controleren en werken met WooCommerce HPOS.",
    "sections": [
      {
        "title": "1. Wat de plugin doet",
        "paragraphs": [
          "Met Mailhilfe Order Note Manager for WooCommerce kun je vaak gebruikte WooCommerce-bestelnotities als herbruikbare sjablonen opslaan. Daardoor hoef je dezelfde tekst niet telkens opnieuw te typen en blijft de communicatie in de bestelgeschiedenis consistent.",
          "Een sjabloon kan worden voorbereid als interne notitie voor medewerkers of als klantnotitie. Bij gebruik in een bestelling kun je het notitietype nog wijzigen."
        ],
        "items": [
          "Typische voorbeelden: betalingsherinneringen, leveringsvertragingen, telefoonnotities, adrescontroles en antwoorden van de klantenservice.",
          "Sjablonen ondersteunen categorieën, favorieten, sortering, een gebruiksteller en JSON-back-ups."
        ]
      },
      {
        "title": "2. Een nieuw sjabloon maken",
        "paragraphs": [
          "Open <strong>Mailhilfe Bestelnotities → Nieuwe toevoegen</strong>. Voer een duidelijke titel in, schrijf de notitietekst in de editor en kies of het standaard notitietype intern of klantgericht moet zijn.",
          "Gebruik de titel als korte omschrijving van het doel, bijvoorbeeld “Betalingsherinnering” of “Klant belde over levering”. Zo is het sjabloon gemakkelijker terug te vinden in het bestelscherm."
        ],
        "items": [
          "Wijs een of meer categorieën toe wanneer je veel sjablonen hebt.",
          "Markeer veelgebruikte sjablonen als favoriet.",
          "Publiceer het sjabloon zodat het in bestellingen beschikbaar wordt."
        ]
      },
      {
        "title": "3. Sjabloontekst opmaken",
        "paragraphs": [
          "De sjabloontekst gebruikt de WordPress-editor. Je kunt tekst opmaken met alinea’s, vet en cursief, lijsten en links. De opmaak blijft behouden wanneer de notitie wordt gemaakt, maar de inhoud wordt opgeschoond volgens de veilige HTML-regels van WordPress.",
          "Gebruik opmaak terughoudend in klantnotities. Een korte alinea of opsomming is meestal beter leesbaar dan een lange, ongestructureerde tekst."
        ],
        "items": [
          "Goed voorbeeld: een korte begroeting, één duidelijke uitleg en één volgende stap.",
          "Vermijd interne afkortingen in klantnotities.",
          "Plaats geen privé-opmerkingen voor medewerkers in sjablonen die als klantnotitie kunnen worden gebruikt."
        ]
      },
      {
        "title": "4. Plaatshouders",
        "paragraphs": [
          "Plaatshouders zijn woorden tussen accolades. Ze worden in het voorbeeld en bij het toevoegen van de notitie vervangen door echte bestelgegevens.",
          "Je kunt gewone tekst en plaatshouders combineren. Voorbeeld: <code>Hallo {customer}, we hebben je bestelling {order_number} ontvangen.</code>"
        ],
        "items": [
          "Bestelling: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Klant: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Verzending en betaling: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Artikelen en winkel: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Voorbeeld controleren voordat je een notitie toevoegt",
        "paragraphs": [
          "Open een WooCommerce-bestelling en selecteer een sjabloon. Het voorbeeld toont de notitie waarbij de plaatshouders al zijn vervangen door gegevens uit de geselecteerde bestelling.",
          "Controleer het voorbeeld altijd voordat je de notitie maakt. Dit is vooral belangrijk wanneer een plaatshouder geen waarde in de bestelling heeft, bijvoorbeeld als een vervoerder of telefoonnummer ontbreekt."
        ],
        "items": [
          "Controleer namen, bedragen, verzendmethode en artikellijst.",
          "Controleer of het geselecteerde notitietype juist is.",
          "Bewerk eerst het sjabloon wanneer dezelfde tekst voor alle toekomstige bestellingen moet worden verbeterd."
        ]
      },
      {
        "title": "6. Interne notities en klantnotities",
        "paragraphs": [
          "Interne notities zijn bedoeld voor winkelmedewerkers en worden normaal gebruikt voor documentatie, vervolgacties of servicegeschiedenis. Klantnotities kunnen zichtbaar zijn voor de klant en kunnen, afhankelijk van je WooCommerce-instellingen, een e-mailmelding activeren.",
          "Controleer het bewerkbare voorbeeld en het geselecteerde notitietype zorgvuldig. Gebruik klantnotities alleen voor tekst die de klant mag lezen."
        ],
        "items": [
          "Interne notitie: “Klant heeft gebeld, afleveradres bevestigd.”",
          "Klantnotitie: “Je bestelling wordt voorbereid en wordt binnenkort verzonden.”",
          "Zet nooit wachtwoorden, privé-opmerkingen of informatie die alleen voor leveranciers bestemd is in klantnotities."
        ]
      },
      {
        "title": "7. Favorieten, zoeken en sorteren",
        "paragraphs": [
          "Met favorieten kun je de belangrijkste sjablonen bovenaan de selectie plaatsen. Het zoekveld in het bestelscherm helpt je een sjabloon te vinden op titel, categorie of inhoud.",
          "In de sjablonenlijst kun je de volgorde met slepen en neerzetten wijzigen. De opgeslagen volgorde wordt gebruikt wanneer sjablonen in het bestelscherm worden getoond."
        ],
        "items": [
          "Gebruik favorieten voor dagelijkse sjablonen.",
          "Gebruik categorieën voor onderwerpen zoals Betaling, Verzending, Retouren en Ondersteuning.",
          "Houd titels kort zodat zoekresultaten goed leesbaar blijven."
        ]
      },
      {
        "title": "8. Importeren, exporteren en demosjablonen",
        "paragraphs": [
          "De JSON-export maakt een back-up van je sjablonen. Je kunt die gebruiken vóór grotere wijzigingen of om sjablonen naar een andere winkel over te zetten.",
          "De JSON-import kan sjablonen maken en bestaande sjablonen met dezelfde titel of interne demosleutel bijwerken. Demosjablonen bieden een snel uitgangspunt en worden in de actieve taal gemaakt."
        ],
        "items": [
          "Exporteer vóór bulkbewerkingen.",
          "Importeer alleen JSON-bestanden uit een vertrouwde bron.",
          "Open na de import enkele sjablonen en controleer de opmaak en plaatshouders."
        ]
      },
      {
        "title": "9. Rechten en rollen",
        "paragraphs": [
          "De plugin gebruikt afzonderlijke rechten voor het beheren van sjablonen en het gebruiken van sjablonen in bestellingen. Beheerders en winkelmanagers ontvangen deze rechten automatisch bij activering.",
          "Wanneer je een plugin voor rolbeheer gebruikt, kun je deze rechten aan aangepaste rollen toekennen of ervan verwijderen."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: sjablonen maken, bewerken, verwijderen, importeren en exporteren.",
          "<code>use_mh_order_note_templates</code>: sjablonen gebruiken in WooCommerce-bestellingen.",
          "Gebruikers zonder het vereiste recht zien de bijbehorende beheerfuncties niet."
        ]
      },
      {
        "title": "10. Beveiliging en HPOS-compatibiliteit",
        "paragraphs": [
          "De plugin gebruikt WordPress-nonces, rechtencontroles, opschoning en escaping voor beheeracties. Sjablooninhoud wordt volgens de veilige HTML-regels van WordPress opgeschoond voordat deze wordt opgeslagen of gebruikt.",
          "Bestelgegevens worden via de WooCommerce-bestel-API’s gelezen in plaats van rechtstreeks uit databasetabellen. Daardoor blijft de plugin compatibel met WooCommerce HPOS en klassieke bestelopslag."
        ],
        "items": [
          "Houd WooCommerce en WordPress bijgewerkt.",
          "Test de werkwijze voor klantnotities nadat je WooCommerce-e-mailinstellingen hebt gewijzigd.",
          "Gebruik een testomgeving voordat je een grote verzameling sjablonen importeert."
        ]
      },
      {
        "title": "11. Problemen oplossen",
        "paragraphs": [
          "Als sjablonen niet in een bestelling verschijnen, controleer dan of WooCommerce actief is, het sjabloon is gepubliceerd en de huidige gebruiker het recht heeft om sjablonen te gebruiken.",
          "Als vertalingen niet verschijnen, controleer dan de sitetaal en gebruikerstaal in WordPress. De plugin bevat gecontroleerde terugvalbestanden voor alle ondersteunde talen, waaronder Perzisch. Andere talen moeten via gecontroleerde WordPress.org-taalpakketten worden geleverd."
        ],
        "items": [
          "Wis na een update de objectcache of cacheplugin wanneer het oude beheerscherm nog wordt getoond.",
          "Als een plaatshouder ongewijzigd blijft, controleer dan of deze exact zoals in de lijst is geschreven, inclusief accolades.",
          "Als klantnotities niet per e-mail worden verzonden, controleer dan de WooCommerce-e-mailinstellingen voor klantnotities."
        ]
      },
      {
        "title": "12. Instellingenpagina",
        "paragraphs": [
          "Open <strong>Mailhilfe Bestelnotities → Instellingen</strong> om het standaard notitietype, veilige HTML, de gebruiksweergave, favorieten, JSON-importopties en taalkoppeling in te stellen. Gebruik interne notities als standaard voor veiliger dagelijks werk."
        ],
        "items": []
      },
      {
        "title": "13. Sjabloontaal en meertalige winkels",
        "paragraphs": [
          "Elk sjabloon kan een sjabloontaal hebben. Kies <strong>Alle talen</strong> als dezelfde tekst voor elke bestelling mag worden gebruikt, of selecteer een specifieke taal voor gelokaliseerde teksten.",
          "Wanneer beschikbaar kan de plugin de voorkeur geven aan sjablonen die overeenkomen met de taal van de bestelling, de gebruikerstaal of algemene taalgegevens van meertalige plugins."
        ],
        "items": [
          "Gebruik één neutraal sjabloon voor interne notities van medewerkers.",
          "Maak afzonderlijke klantgerichte sjablonen voor Nederlands, Duits, Engels of andere winkeltalen.",
          "Test bij WPML- of Polylang-winkels de detectie van de besteltaal met een echte testbestelling."
        ]
      },
      {
        "title": "14. Aangepaste velden en metaplaatshouders",
        "paragraphs": [
          "Geavanceerde plaatshouders kunnen geselecteerde meta-velden van bestellingen of klanten lezen. Gebruik <code>{order_meta:meta_key}</code> voor bestelgegevens en <code>{customer_meta:meta_key}</code> voor gebruikersgegevens van de klant.",
          "Om veiligheidsredenen worden gevoelige sleutelnamen met woorden als password, token, secret, session, auth en hash geblokkeerd. Gebruik metaplaatshouders alleen wanneer je weet welke inhoud het veld bevat."
        ],
        "items": [
          "Voorbeeld: <code>{order_meta:_tracking_number}</code> voor een trackingnummer dat door een verzendplugin is opgeslagen.",
          "Voorbeeld: <code>{order_meta:_billing_vat_id}</code> voor een btw-ID-veld.",
          "Toon geen interne of gevoelige velden in klantnotities."
        ]
      },
      {
        "title": "15. Sjablonen dupliceren en revisies",
        "paragraphs": [
          "Gebruik de actie Dupliceren wanneer je een vergelijkbaar sjabloon met kleine wijzigingen nodig hebt. De kopie wordt als concept gemaakt zodat je deze vóór publicatie kunt controleren.",
          "Met sjabloonrevisies kun je eerdere versies vergelijken en een vorige tekst herstellen wanneer een wijziging per ongeluk is aangebracht."
        ],
        "items": [
          "Dupliceer een algemeen verzendsjabloon voordat je varianten voor DHL, UPS of afhalen maakt.",
          "Controleer revisies na grotere tekstwijzigingen.",
          "Gebruik duidelijke titels zodat vergelijkbare sjablonen niet met elkaar worden verward."
        ]
      },
      {
        "title": "16. Rechtenpagina",
        "paragraphs": [
          "Open <strong>Mailhilfe Bestelnotities → Rechten</strong> om te bepalen welke WordPress-rollen sjablonen mogen beheren en welke rollen sjablonen in bestellingen mogen gebruiken.",
          "Beheerders behouden de vereiste rechten. Ken aan andere rollen alleen de rechten toe die voor hun dagelijkse taak nodig zijn."
        ],
        "items": [
          "Sjablonen beheren: sjablonen maken, bewerken, verwijderen, importeren en exporteren.",
          "Sjablonen gebruiken: een sjabloon selecteren en een notitie aan een WooCommerce-bestelling toevoegen.",
          "Ken import- en exportrechten alleen toe aan vertrouwde gebruikers."
        ]
      },
      {
        "title": "17. Importvoorbeeld",
        "paragraphs": [
          "JSON-imports tonen een voorbeeld voordat wijzigingen worden toegepast. Het voorbeeld geeft aan hoeveel sjablonen worden aangemaakt, bijgewerkt of overgeslagen.",
          "Bevestig de import pas nadat je het voorbeeld hebt gecontroleerd. Zo voorkom je dat bestaande sjablonen onbedoeld worden overschreven."
        ],
        "items": [
          "Maak een exportback-up voordat je een grote verzameling importeert.",
          "Importeer alleen JSON-bestanden uit een vertrouwde bron.",
          "Test na de import minstens één klantnotitie en één interne notitie."
        ]
      },
      {
        "title": "18. E-mailgedrag van klantnotities",
        "paragraphs": [
          "Klantnotities kunnen WooCommerce-e-mailmeldingen activeren wanneer de bijbehorende e-mail is ingeschakeld. De plugin registreert het aanmaken van de klantnotitie afzonderlijk van de e-mailverwerking. Controleer het bewerkbare voorbeeld voordat je de notitie toevoegt en gebruik de pagina Geschiedenis om het resultaat van de e-mailafhandeling te bekijken."
        ],
        "items": []
      },
      {
        "title": "19. Aanbevolen werkwijze",
        "paragraphs": [
          "Een veilige dagelijkse werkwijze is: selecteer een sjabloon, controleer het ingevulde voorbeeld, bewerk het voorbeeld indien nodig, controleer het notitietype en voeg daarna de notitie toe.",
          "Test nieuwe sjablonen eerst in een niet-kritieke bestelling of testwinkel voordat je ze voor echte klanten gebruikt."
        ],
        "items": [
          "Gebruik interne notities voor informatie die alleen voor medewerkers bestemd is.",
          "Gebruik klantnotities alleen voor berichten die naar de klant mogen worden verzonden.",
          "Controleer de plaatshouders telkens wanneer een sjabloon wordt gewijzigd."
        ]
      },
      {
        "title": "20. Sjabloonvoorwaarden",
        "paragraphs": [
          "Sjabloonvoorwaarden bepalen of een sjabloon voor een bepaalde bestelling beschikbaar is. Je kunt sjablonen beperken op bestelstatus, betaalmethode, verzendmethode, factureringsland en minimaal of maximaal bestelbedrag. Aan alle ingestelde voorwaarden moet worden voldaan."
        ],
        "items": [
          "Laat een veld leeg wanneer die voorwaarde het sjabloon niet moet beperken.",
          "Gebruik de technische ID’s van betaal- en verzendmethoden.",
          "Voorwaarden worden in de interface en opnieuw op de server gecontroleerd voordat een notitie wordt gemaakt."
        ]
      },
      {
        "title": "21. Logboek voor e-mailverwerking",
        "paragraphs": [
          "Bij klantnotities registreert de plugin wanneer WooCommerce meldt dat de e-mail voor de klantnotitie is verwerkt. Ook technische fouten van wp_mail worden vastgelegd. Een verwerkte gebeurtenis bevestigt alleen dat WordPress/WooCommerce het bericht aan het mailsysteem heeft doorgegeven; dit bewijst niet dat het bericht uiteindelijk is afgeleverd of door de klant is gelezen."
        ],
        "items": [
          "Controleer op de pagina Geschiedenis de verwerkte en mislukte e-mailgebeurtenissen.",
          "Gebruik een SMTP-provider of e-maillogdienst wanneer definitieve afleverinformatie nodig is.",
          "Interne notities activeren geen e-mail voor klantnotities."
        ]
      },
      {
        "title": "22. Centrale geschiedenis",
        "paragraphs": [
          "Open <strong>Mailhilfe Bestelnotities → Geschiedenis</strong> om recente notitiecreaties, sjabloongebruik, e-mailverwerking en e-mailfouten te bekijken. Vermeldingen bevatten waar beschikbaar de bestelling, het sjabloon, de gebruiker, ontvanger, het gebeurtenistype en het tijdstip."
        ],
        "items": [
          "Gebruik de geschiedenis voor ondersteuning, controle en probleemoplossing.",
          "De geschiedenis staat los van de WooCommerce-bestelnotities.",
          "De pagina toont de 250 meest recente vermeldingen."
        ]
      },
      {
        "title": "23. Voorbeeld met testbestelling",
        "paragraphs": [
          "Voer in de sjablooneditor een WooCommerce-bestelling-ID in het gebied voor het testvoorbeeld in. De huidige editorinhoud, inclusief niet-opgeslagen wijzigingen, wordt met gegevens uit die bestelling weergegeven zonder een notitie te maken of een e-mail te verzenden."
        ],
        "items": [
          "Gebruik een bestelling uit een testomgeving of een niet-kritieke testbestelling.",
          "Controleer ontbrekende waarden, opmaak, voorwaarden en aangepaste metaplaatshouders.",
          "Je moet toestemming hebben om de geselecteerde bestelling te bewerken."
        ]
      },
      {
        "title": "24. Persoonlijke favorieten en recent gebruikte sjablonen",
        "paragraphs": [
          "Elke beheerder kan persoonlijke favorieten in het bestelscherm markeren. De plugin bewaart daarnaast per gebruiker de tien laatst gebruikte sjablonen en plaatst deze hoger in de selectie. Algemene favorieten blijven met alle gebruikers gedeeld."
        ],
        "items": [
          "Persoonlijke favorieten veranderen de lijst van een andere gebruiker niet.",
          "De lijst met recente sjablonen wordt alleen bijgewerkt nadat een notitie succesvol is toegevoegd.",
          "Persoonlijke gegevens worden als WordPress-gebruikersmetadata opgeslagen."
        ]
      },
      {
        "title": "25. Diagnostiekpagina",
        "paragraphs": [
          "Open <strong>Mailhilfe Bestelnotities → Diagnostiek</strong> om technische informatie te bekijken, zoals WordPress-, PHP- en WooCommerce-versies, HPOS-status, e-mailstatus voor klantnotities, locale, aantal gepubliceerde sjablonen, cachestatus en WP_DEBUG."
        ],
        "items": [
          "Kopieer de diagnostische waarden wanneer je ondersteuning aanvraagt.",
          "De pagina toont geen inhoud van bestelnotities of adressen van klanten.",
          "Ontwikkelaars kunnen rijen toevoegen met het diagnostiekfilter."
        ]
      },
      {
        "title": "26. Hooks en filters voor ontwikkelaars",
        "paragraphs": [
          "De plugin biedt hooks en filters voor plaatshouders, plaatshouderwaarden, toegestane metasleutels, sjabloonresultaten, voorwaarden, voorbeeldinhoud, definitieve notitie-inhoud, acties vóór en na het toevoegen van een notitie, geschiedenisrecords en diagnostiek. Hooknamen en parameters zijn gedocumenteerd in readme.txt."
        ],
        "items": [
          "Valideer, schoon op en escape alle aangepaste gegevens.",
          "Gebruik WooCommerce-bestel-API’s in plaats van rechtstreekse toegang tot besteltabellen.",
          "Houd aangepaste uitbreidingen compatibel met zowel HPOS als klassieke bestelopslag."
        ]
      }
    ]
  },
  "pl_PL": {
    "title": "Szczegółowa pomoc dla Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Ta zaktualizowana pomoc objaśnia pełny sposób pracy z wtyczką Mailhilfe Order Note Manager for WooCommerce: tworzenie i formatowanie szablonów, używanie języków szablonów, symboli zastępczych i symboli meta, edytowanie podglądu, bezpieczne wysyłanie notatek dla klientów, ustawienia, uprawnienia, podgląd importu oraz zgodność z HPOS.",
    "sections": [
      {
        "title": "1. Do czego służy wtyczka",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce umożliwia przechowywanie często używanych notatek zamówień WooCommerce jako szablonów wielokrotnego użytku. Pozwala to uniknąć ciągłego wpisywania tego samego tekstu i zachować spójną komunikację w historii zamówienia.",
          "Szablon można przygotować jako wewnętrzną notatkę dla personelu albo notatkę dla klienta. Podczas używania szablonu w zamówieniu nadal można zmienić typ notatki."
        ],
        "items": [
          "Typowe przykłady: przypomnienia o płatności, opóźnienia dostawy, zapisy rozmów telefonicznych, weryfikacja adresu i odpowiedzi działu obsługi.",
          "Szablony obsługują kategorie, ulubione, sortowanie, licznik użycia i kopie zapasowe JSON."
        ]
      },
      {
        "title": "2. Tworzenie nowego szablonu",
        "paragraphs": [
          "Otwórz <strong>Mailhilfe Order Notes → Dodaj nowy</strong>. Wprowadź zrozumiały tytuł, wpisz treść notatki w edytorze i wybierz, czy domyślnym typem ma być notatka wewnętrzna, czy notatka dla klienta.",
          "Tytuł powinien krótko opisywać przeznaczenie szablonu, na przykład „Przypomnienie o płatności” albo „Klient zadzwonił w sprawie dostawy”. Ułatwi to odnalezienie szablonu na ekranie zamówienia."
        ],
        "items": [
          "Jeśli masz wiele szablonów, przypisz im co najmniej jedną kategorię.",
          "Często używane szablony oznacz jako ulubione.",
          "Opublikuj szablon, aby był dostępny w zamówieniach."
        ]
      },
      {
        "title": "3. Formatowanie treści szablonu",
        "paragraphs": [
          "Treść szablonu jest edytowana w edytorze WordPressa. Można używać akapitów, pogrubienia, kursywy, list i odnośników. Formatowanie zostaje zachowane podczas tworzenia notatki, a treść jest oczyszczana zgodnie z bezpiecznymi regułami HTML WordPressa.",
          "W notatkach dla klientów stosuj formatowanie z umiarem. Krótki akapit lub lista punktowana są zwykle łatwiejsze do przeczytania niż długi, nieuporządkowany tekst."
        ],
        "items": [
          "Dobry przykład: krótkie powitanie, jedno jasne wyjaśnienie i jeden kolejny krok.",
          "W notatkach dla klientów unikaj wewnętrznych skrótów.",
          "Nie umieszczaj prywatnych uwag personelu w szablonach, które mogą zostać użyte jako notatki dla klientów."
        ]
      },
      {
        "title": "4. Symbole zastępcze",
        "paragraphs": [
          "Symbole zastępcze to wyrażenia w nawiasach klamrowych. W podglądzie i podczas dodawania notatki do zamówienia są zastępowane rzeczywistymi danymi zamówienia.",
          "Można łączyć zwykły tekst z symbolami zastępczymi. Przykład: <code>Dzień dobry {customer}, otrzymaliśmy zamówienie {order_number}.</code>"
        ],
        "items": [
          "Zamówienie: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Klient: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Wysyłka i płatność: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Produkty i sklep: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Podgląd przed dodaniem notatki",
        "paragraphs": [
          "Otwórz zamówienie WooCommerce i wybierz szablon. Podgląd pokaże notatkę, w której symbole zastępcze zostały już zamienione na dane wybranego zamówienia.",
          "Zawsze sprawdzaj podgląd przed utworzeniem notatki. Jest to szczególnie ważne, gdy dla symbolu zastępczego brakuje wartości w zamówieniu, na przykład nazwy firmy wysyłkowej albo numeru telefonu."
        ],
        "items": [
          "Sprawdź nazwiska, kwoty, metodę wysyłki i listę produktów.",
          "Sprawdź, czy wybrano właściwy typ notatki.",
          "Jeśli ten sam tekst ma zostać poprawiony dla wszystkich przyszłych zamówień, najpierw edytuj szablon."
        ]
      },
      {
        "title": "6. Notatki wewnętrzne i notatki dla klientów",
        "paragraphs": [
          "Notatki wewnętrzne są przeznaczone dla personelu sklepu i zwykle służą do dokumentowania, zapisywania zadań następczych lub historii obsługi. Notatki dla klientów mogą być widoczne dla klienta i, zależnie od ustawień WooCommerce, mogą uruchamiać powiadomienia e-mail.",
          "Dokładnie sprawdź edytowalny podgląd i wybrany typ notatki. Notatek dla klientów używaj wyłącznie do treści, które klient może przeczytać."
        ],
        "items": [
          "Notatka wewnętrzna: „Klient zadzwonił, adres dostawy został potwierdzony”.",
          "Notatka dla klienta: „Zamówienie jest przygotowywane i wkrótce zostanie wysłane”.",
          "W notatkach dla klientów nigdy nie umieszczaj haseł, prywatnych uwag ani informacji przeznaczonych wyłącznie dla dostawcy."
        ]
      },
      {
        "title": "7. Ulubione, wyszukiwanie i sortowanie",
        "paragraphs": [
          "Ulubione pomagają umieścić najważniejsze szablony na początku listy wyboru. Pole wyszukiwania na ekranie zamówienia umożliwia odnalezienie szablonu według tytułu, kategorii lub treści.",
          "Na liście szablonów można zmieniać kolejność metodą przeciągnij i upuść. Zapisana kolejność jest używana podczas wyświetlania szablonów na ekranie zamówienia."
        ],
        "items": [
          "Codziennie używane szablony oznacz jako ulubione.",
          "Używaj kategorii dla grup tematycznych, takich jak Płatność, Wysyłka, Zwroty i Pomoc.",
          "Tytuły powinny być krótkie, aby wyniki wyszukiwania pozostały czytelne."
        ]
      },
      {
        "title": "8. Import, eksport i szablony demonstracyjne",
        "paragraphs": [
          "Eksport JSON tworzy kopię zapasową szablonów. Można z niego skorzystać przed większymi zmianami albo podczas przenoszenia szablonów do innego sklepu.",
          "Import JSON może tworzyć szablony i aktualizować istniejące szablony o tym samym tytule lub wewnętrznym kluczu demonstracyjnym. Szablony demonstracyjne ułatwiają rozpoczęcie pracy i są tworzone w aktywnym języku."
        ],
        "items": [
          "Przed zmianami zbiorczymi wykonaj eksport.",
          "Importuj wyłącznie pliki JSON z zaufanego źródła.",
          "Po imporcie otwórz kilka szablonów i sprawdź formatowanie oraz symbole zastępcze."
        ]
      },
      {
        "title": "9. Uprawnienia i role",
        "paragraphs": [
          "Wtyczka używa oddzielnych uprawnień do zarządzania szablonami oraz do używania ich w zamówieniach. Administratorzy i kierownicy sklepu otrzymują te uprawnienia automatycznie podczas aktywacji.",
          "Jeśli używasz wtyczki do edycji ról, możesz przyznawać lub odbierać te uprawnienia rolom niestandardowym."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: tworzenie, edycja, usuwanie oraz import i eksport szablonów.",
          "<code>use_mh_order_note_templates</code>: używanie szablonów w zamówieniach WooCommerce.",
          "Użytkownicy bez wymaganych uprawnień nie widzą odpowiednich funkcji administracyjnych."
        ]
      },
      {
        "title": "10. Bezpieczeństwo i zgodność z HPOS",
        "paragraphs": [
          "Wtyczka używa tokenów jednorazowych WordPressa, kontroli uprawnień, oczyszczania i kodowania danych dla działań administracyjnych. Treść szablonu jest oczyszczana za pomocą bezpiecznych reguł HTML WordPressa przed zapisaniem lub użyciem.",
          "Dane zamówienia są odczytywane przez interfejsy API zamówień WooCommerce zamiast przez bezpośredni dostęp do tabel bazy danych. Dzięki temu wtyczka jest zgodna zarówno z WooCommerce HPOS, jak i klasycznym sposobem przechowywania zamówień."
        ],
        "items": [
          "Regularnie aktualizuj WooCommerce i WordPressa.",
          "Po zmianie ustawień wiadomości e-mail WooCommerce przetestuj obsługę notatek dla klientów.",
          "Przed importem dużego zestawu szablonów użyj witryny testowej."
        ]
      },
      {
        "title": "11. Rozwiązywanie problemów",
        "paragraphs": [
          "Jeśli szablony nie pojawiają się w zamówieniu, sprawdź, czy WooCommerce jest aktywny, szablon został opublikowany, a bieżący użytkownik ma uprawnienie do używania szablonów.",
          "Jeśli tłumaczenia się nie pojawiają, sprawdź język witryny i język użytkownika w WordPressie. Wtyczka zawiera sprawdzone pliki zapasowe dla wszystkich obsługiwanych języków, w tym perskiego. Inne języki powinny być dostarczane przez sprawdzone pakiety językowe WordPress.org."
        ],
        "items": [
          "Jeśli po aktualizacji nadal jest wyświetlany stary ekran administracyjny, wyczyść pamięć podręczną obiektów lub wtyczki cache.",
          "Jeśli symbol zastępczy pozostaje bez zmian, sprawdź, czy został zapisany dokładnie tak jak na liście, łącznie z nawiasami klamrowymi.",
          "Jeśli notatki dla klientów nie są wysyłane e-mailem, sprawdź ustawienia wiadomości e-mail WooCommerce dla powiadomień o notatkach klientów."
        ]
      },
      {
        "title": "12. Strona ustawień",
        "paragraphs": [
          "Otwórz <strong>Mailhilfe Order Notes → Ustawienia</strong>, aby wybrać domyślny typ notatki, obsługę bezpiecznego HTML, wyświetlanie użycia, ulubione, opcje importu JSON i dopasowanie języka. Dla bezpiecznej codziennej pracy jako domyślne wybierz notatki wewnętrzne."
        ],
        "items": []
      },
      {
        "title": "13. Język szablonu i sklepy wielojęzyczne",
        "paragraphs": [
          "Każdy szablon może mieć przypisany język. Wybierz <strong>Wszystkie języki</strong>, jeśli ten sam tekst może być używany dla każdego zamówienia, albo wybierz konkretny język dla treści zlokalizowanej.",
          "Gdy jest to możliwe, wtyczka może preferować szablony zgodne z językiem zamówienia, językiem użytkownika lub typowymi danymi językowymi z wtyczek wielojęzycznych."
        ],
        "items": [
          "Dla wewnętrznych notatek personelu użyj jednego neutralnego szablonu.",
          "Utwórz oddzielne szablony dla klientów w języku polskim, niemieckim, angielskim lub innych językach sklepu.",
          "W sklepach korzystających z WPML lub Polylang przetestuj wykrywanie języka na rzeczywistym zamówieniu testowym."
        ]
      },
      {
        "title": "14. Pola niestandardowe i symbole zastępcze meta",
        "paragraphs": [
          "Zaawansowane symbole zastępcze mogą odczytywać wybrane pola meta zamówienia lub klienta. Użyj <code>{order_meta:meta_key}</code> dla danych zamówienia i <code>{customer_meta:meta_key}</code> dla danych użytkownika klienta.",
          "Ze względów bezpieczeństwa blokowane są wrażliwe nazwy kluczy zawierające między innymi password, token, secret, session, auth i hash. Symboli meta używaj tylko wtedy, gdy wiesz, co zawiera dane pole."
        ],
        "items": [
          "Przykład: <code>{order_meta:_tracking_number}</code> dla numeru przesyłki zapisanego przez wtyczkę wysyłkową.",
          "Przykład: <code>{order_meta:_billing_vat_id}</code> dla pola numeru VAT.",
          "Nie ujawniaj wewnętrznych ani wrażliwych pól w notatkach dla klientów."
        ]
      },
      {
        "title": "15. Duplikowanie szablonów i wersje",
        "paragraphs": [
          "Użyj funkcji duplikowania, gdy potrzebujesz podobnego szablonu z niewielkimi zmianami. Kopia jest tworzona jako szkic, aby można ją było sprawdzić przed opublikowaniem.",
          "Wersje szablonów pozwalają porównać wcześniejsze treści i przywrócić poprzedni tekst, jeśli zmiana została wprowadzona przez pomyłkę."
        ],
        "items": [
          "Przed utworzeniem wariantów dla DHL, UPS lub odbioru osobistego zduplikuj ogólny szablon wysyłki.",
          "Po większych zmianach tekstu sprawdź wersje.",
          "Używaj jasnych tytułów, aby podobne szablony nie były ze sobą mylone."
        ]
      },
      {
        "title": "16. Strona uprawnień",
        "paragraphs": [
          "Otwórz <strong>Mailhilfe Order Notes → Uprawnienia</strong>, aby określić, które role WordPressa mogą zarządzać szablonami, a które mogą używać ich w zamówieniach.",
          "Administratorzy zachowują wymagane uprawnienia. Pozostałym rolom przyznawaj wyłącznie uprawnienia niezbędne do codziennych zadań."
        ],
        "items": [
          "Zarządzanie szablonami: tworzenie, edycja, usuwanie, import i eksport szablonów.",
          "Używanie szablonów: wybór szablonu i dodawanie notatki w zamówieniu WooCommerce.",
          "Uprawnienia do importu i eksportu przyznawaj wyłącznie zaufanym użytkownikom."
        ]
      },
      {
        "title": "17. Podgląd importu",
        "paragraphs": [
          "Import JSON wyświetla teraz podgląd przed zastosowaniem zmian. Podgląd informuje, ile szablonów zostanie utworzonych, zaktualizowanych lub pominiętych.",
          "Potwierdź import dopiero po sprawdzeniu podglądu. Zapobiega to przypadkowemu nadpisaniu istniejących szablonów."
        ],
        "items": [
          "Przed importem dużego zestawu utwórz kopię zapasową przez eksport.",
          "Importuj wyłącznie pliki JSON z zaufanego źródła.",
          "Po imporcie przetestuj co najmniej jedną notatkę dla klienta i jedną notatkę wewnętrzną."
        ]
      },
      {
        "title": "18. Obsługa wiadomości e-mail dla notatek klientów",
        "paragraphs": [
          "Notatki dla klientów mogą uruchamiać powiadomienia e-mail WooCommerce, jeśli odpowiednia wiadomość jest włączona. Wtyczka zapisuje utworzenie notatki dla klienta oddzielnie od przetwarzania wiadomości e-mail. Przed dodaniem notatki sprawdź edytowalny podgląd, a wynik obsługi poczty zweryfikuj na stronie Historia."
        ],
        "items": []
      },
      {
        "title": "19. Zalecany sposób pracy",
        "paragraphs": [
          "Bezpieczny codzienny sposób pracy wygląda następująco: wybierz szablon, sprawdź podgląd z podstawionymi danymi, w razie potrzeby edytuj podgląd, zweryfikuj typ notatki, a następnie dodaj notatkę.",
          "Nowe szablony najpierw przetestuj na nieistotnym zamówieniu lub w sklepie testowym, zanim użyjesz ich dla rzeczywistych klientów."
        ],
        "items": [
          "Informacje przeznaczone wyłącznie dla personelu zapisuj jako notatki wewnętrzne.",
          "Notatek dla klientów używaj wyłącznie do wiadomości, które mogą zostać wysłane klientowi.",
          "Po każdej zmianie szablonu sprawdź symbole zastępcze."
        ]
      },
      {
        "title": "20. Warunki szablonów",
        "paragraphs": [
          "Warunki szablonu określają, czy szablon jest dostępny dla danego zamówienia. Szablony można ograniczać według statusu zamówienia, metody płatności, metody wysyłki, kraju rozliczeniowego oraz minimalnej lub maksymalnej wartości zamówienia. Wszystkie skonfigurowane warunki muszą być spełnione."
        ],
        "items": [
          "Pozostaw pole puste, jeśli dany warunek nie powinien ograniczać szablonu.",
          "Używaj technicznych identyfikatorów metod płatności i wysyłki.",
          "Warunki są sprawdzane w interfejsie oraz ponownie na serwerze przed utworzeniem notatki."
        ]
      },
      {
        "title": "21. Dziennik przetwarzania wiadomości e-mail",
        "paragraphs": [
          "Dla notatek klientów wtyczka zapisuje moment, w którym WooCommerce zgłasza przetworzenie wiadomości e-mail z notatką dla klienta, a także techniczne błędy wp_mail. Zdarzenie przetworzenia potwierdza, że WordPress lub WooCommerce przekazał wiadomość do systemu pocztowego. Nie dowodzi ono końcowego doręczenia ani przeczytania wiadomości przez klienta."
        ],
        "items": [
          "Na stronie Historia sprawdzaj zdarzenia pomyślnego przetworzenia i błędy wiadomości e-mail.",
          "Jeśli potrzebujesz pewnych informacji o doręczeniu, użyj dostawcy SMTP lub usługi rejestrowania poczty.",
          "Notatki wewnętrzne nie uruchamiają wiadomości e-mail z notatką dla klienta."
        ]
      },
      {
        "title": "22. Centralna historia",
        "paragraphs": [
          "Otwórz <strong>Mailhilfe Order Notes → Historia</strong>, aby przejrzeć ostatnio utworzone notatki, użycie szablonów, przetwarzanie wiadomości e-mail i błędy wysyłki. Wpisy zawierają, jeśli dane są dostępne, zamówienie, szablon, użytkownika, odbiorcę, typ zdarzenia i czas."
        ],
        "items": [
          "Używaj historii do obsługi zgłoszeń, audytu i rozwiązywania problemów.",
          "Historia jest oddzielona od notatek zamówień WooCommerce.",
          "Strona wyświetla 250 najnowszych wpisów."
        ]
      },
      {
        "title": "23. Podgląd z zamówieniem testowym",
        "paragraphs": [
          "W edytorze szablonu wprowadź identyfikator zamówienia WooCommerce w obszarze podglądu testowego. Bieżąca treść edytora, łącznie z niezapisanymi zmianami, zostanie wyrenderowana z danymi tego zamówienia bez tworzenia notatki i bez wysyłania wiadomości e-mail."
        ],
        "items": [
          "Użyj zamówienia w witrynie testowej albo nieistotnego zamówienia testowego.",
          "Sprawdź brakujące wartości, formatowanie, warunki i niestandardowe symbole meta.",
          "Musisz mieć uprawnienie do edycji wybranego zamówienia."
        ]
      },
      {
        "title": "24. Osobiste ulubione i ostatnio używane szablony",
        "paragraphs": [
          "Każdy administrator może oznaczać osobiste ulubione na ekranie zamówienia. Wtyczka zapisuje także dziesięć ostatnio używanych szablonów każdego użytkownika i umieszcza je wyżej na liście wyboru. Globalne ulubione pozostają wspólne dla wszystkich użytkowników."
        ],
        "items": [
          "Osobiste ulubione nie zmieniają list innych użytkowników.",
          "Lista ostatnich szablonów jest aktualizowana dopiero po pomyślnym dodaniu notatki.",
          "Dane osobiste są przechowywane jako metadane użytkownika WordPressa."
        ]
      },
      {
        "title": "25. Strona diagnostyki",
        "paragraphs": [
          "Otwórz <strong>Mailhilfe Order Notes → Diagnostyka</strong>, aby wyświetlić informacje techniczne, takie jak wersje WordPressa, PHP i WooCommerce, status HPOS, status wiadomości e-mail z notatkami dla klientów, ustawienia regionalne, liczba opublikowanych szablonów, status pamięci podręcznej i WP_DEBUG."
        ],
        "items": [
          "Podczas zgłaszania problemu skopiuj wartości diagnostyczne.",
          "Strona nie wyświetla treści notatek zamówień ani adresów klientów.",
          "Programiści mogą dodawać wiersze za pomocą filtra diagnostyki."
        ]
      },
      {
        "title": "26. Hooki i filtry dla programistów",
        "paragraphs": [
          "Wtyczka udostępnia hooki i filtry dla symboli zastępczych, ich wartości, dozwolonych kluczy meta, wyników szablonów, warunków, treści podglądu, końcowej treści notatki, działań przed dodaniem notatki i po jej dodaniu, wpisów historii oraz diagnostyki. Nazwy hooków i ich parametry są opisane w pliku readme.txt."
        ],
        "items": [
          "Sprawdzaj, oczyszczaj i koduj wszystkie dane niestandardowe.",
          "Korzystaj z interfejsów API zamówień WooCommerce zamiast bezpośredniego dostępu do tabel zamówień.",
          "Dbaj o zgodność rozszerzeń zarówno z HPOS, jak i klasycznym sposobem przechowywania zamówień."
        ]
      }
    ]
  },
  "tr_TR": {
    "title": "Mailhilfe Order Note Manager for WooCommerce için ayrıntılı yardım",
    "intro": "Bu güncel yardım, Mailhilfe Order Note Manager for WooCommerce adı altındaki tüm iş akışını açıklar: şablon oluşturma ve biçimlendirme, şablon dilleri, yer tutucular ve meta yer tutucular, önizlemeleri düzenleme, müşteri notlarını güvenli biçimde gönderme, ayarlar, yetkiler, içe aktarma önizlemeleri ve HPOS uyumluluğu.",
    "sections": [
      {
        "title": "1. Eklentinin işlevi",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce, sık kullanılan WooCommerce sipariş notlarını yeniden kullanılabilir şablonlar olarak saklamanızı sağlar. Böylece aynı metni tekrar tekrar yazmanız gerekmez ve sipariş geçmişindeki iletişim tutarlı kalır.",
          "Bir şablon, personel için dahili not veya müşteriye yönelik not olarak hazırlanabilir. Şablonu bir siparişte kullanırken not türünü yine değiştirebilirsiniz."
        ],
        "items": [
          "Yaygın örnekler: ödeme hatırlatmaları, teslimat gecikmeleri, telefon görüşmesi kayıtları, adres kontrolleri ve hizmet yanıtları.",
          "Şablonlar kategorileri, favorileri, sıralamayı, kullanım sayacını ve JSON yedeklemesini destekler."
        ]
      },
      {
        "title": "2. Yeni şablon oluşturma",
        "paragraphs": [
          "<strong>Mailhilfe Sipariş Notları → Yeni ekle</strong> bölümünü açın. Açıklayıcı bir başlık girin, not metnini düzenleyicide yazın ve varsayılan not türünün dahili mi yoksa müşteriye yönelik mi olacağını seçin.",
          "Başlığı amacın kısa açıklaması olarak kullanın; örneğin “Ödeme hatırlatması” veya “Müşteri teslimat hakkında aradı”. Bu, sipariş ekranında şablonu bulmayı kolaylaştırır."
        ],
        "items": [
          "Çok sayıda şablonunuz varsa bir veya daha fazla kategori atayın.",
          "Sık kullanılan şablonları favori olarak işaretleyin.",
          "Şablonun siparişlerde kullanılabilmesi için yayımlayın."
        ]
      },
      {
        "title": "3. Şablon metnini biçimlendirme",
        "paragraphs": [
          "Şablon metni WordPress düzenleyicisini kullanır. Metni paragraflar, kalın ve italik yazı, listeler ve bağlantılarla biçimlendirebilirsiniz. Not oluşturulduğunda biçimlendirme korunur, ancak içerik WordPress’in güvenli HTML kurallarıyla temizlenir.",
          "Müşteri notlarında biçimlendirmeyi ölçülü kullanın. Kısa bir paragraf veya madde işaretli liste, uzun ve yapılandırılmamış bir metinden genellikle daha kolay okunur."
        ],
        "items": [
          "İyi örnek: kısa bir selamlama, açık bir açıklama ve sonraki adım.",
          "Müşteri notlarında kurum içi kısaltmalar kullanmayın.",
          "Müşteri notu olarak kullanılabilecek şablonlara özel personel yorumları eklemeyin."
        ]
      },
      {
        "title": "4. Yer tutucular",
        "paragraphs": [
          "Yer tutucular, süslü parantez içindeki sözcüklerdir. Önizlemede ve not siparişe eklendiğinde gerçek sipariş verileriyle değiştirilirler.",
          "Normal metni ve yer tutucuları birlikte kullanabilirsiniz. Örnek: <code>Merhaba {customer}, {order_number} numaralı siparişinizi aldık.</code>"
        ],
        "items": [
          "Sipariş: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Müşteri: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Gönderim ve ödeme: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Ürünler ve mağaza: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Not eklemeden önce önizleme",
        "paragraphs": [
          "Bir WooCommerce siparişini açın ve şablon seçin. Önizleme, yer tutucular seçilen sipariş verileriyle değiştirilmiş hâlde notu gösterir.",
          "Notu oluşturmadan önce önizlemeyi her zaman kontrol edin. Özellikle gönderim şirketi veya telefon numarası gibi bir yer tutucunun siparişte değeri yoksa bu önemlidir."
        ],
        "items": [
          "Adları, toplamları, gönderim yöntemini ve ürün listesini kontrol edin.",
          "Seçilen not türünün doğru olduğunu kontrol edin.",
          "Aynı metnin gelecekteki tüm siparişler için iyileştirilmesi gerekiyorsa önce şablonu düzenleyin."
        ]
      },
      {
        "title": "6. Dahili notlar ve müşteri notları",
        "paragraphs": [
          "Dahili notlar mağaza personeli içindir ve genellikle belgeleme, takip görevleri veya hizmet geçmişi amacıyla kullanılır. Müşteri notları müşteriye görünebilir ve WooCommerce ayarlarınıza bağlı olarak e-posta bildirimlerini tetikleyebilir.",
          "Düzenlenebilir önizlemeyi ve seçilen not türünü dikkatle inceleyin. Müşteri notlarını yalnızca müşterinin okuyabileceği metinler için kullanın."
        ],
        "items": [
          "Dahili not: “Müşteri aradı, teslimat adresi doğrulandı.”",
          "Müşteri notu: “Siparişiniz hazırlanıyor ve kısa süre içinde gönderilecektir.”",
          "Müşteri notlarına hiçbir zaman parola, özel yorum veya yalnızca tedarikçiye ait bilgi eklemeyin."
        ]
      },
      {
        "title": "7. Favoriler, arama ve sıralama",
        "paragraphs": [
          "Favoriler, en önemli şablonları seçimin üst kısmına yerleştirmenize yardımcı olur. Sipariş ekranındaki arama alanı, başlığa, kategoriye veya içeriğe göre şablon bulmanızı sağlar.",
          "Şablon listesinde sürükleyip bırakarak sıralamayı değiştirebilirsiniz. Kaydedilen sıra, şablonlar sipariş ekranında gösterilirken kullanılır."
        ],
        "items": [
          "Günlük kullanılan şablonlar için favorileri kullanın.",
          "Ödeme, Gönderim, İadeler ve Destek gibi konu grupları için kategorileri kullanın.",
          "Arama sonuçlarının okunabilir kalması için başlıkları kısa tutun."
        ]
      },
      {
        "title": "8. İçe aktarma, dışa aktarma ve demo şablonları",
        "paragraphs": [
          "JSON dışa aktarma, şablonlarınızın yedeğini oluşturur. Bunu büyük değişikliklerden önce veya şablonları başka bir mağazaya taşımak için kullanabilirsiniz.",
          "JSON içe aktarma yeni şablonlar oluşturabilir ve aynı başlığa veya dahili demo anahtarına sahip mevcut şablonları güncelleyebilir. Demo şablonları hızlı bir başlangıç sağlar ve etkin dilde oluşturulur."
        ],
        "items": [
          "Toplu değişikliklerden önce dışa aktarın.",
          "Yalnızca güvenilir bir kaynaktan gelen JSON dosyalarını içe aktarın.",
          "İçe aktarmadan sonra birkaç şablonu açıp biçimlendirmeyi ve yer tutucuları kontrol edin."
        ]
      },
      {
        "title": "9. Yetkiler ve roller",
        "paragraphs": [
          "Eklenti, şablonları yönetmek ve siparişlerde kullanmak için ayrı yetkiler kullanır. Yöneticiler ve mağaza yöneticileri bu yetkileri etkinleştirme sırasında otomatik olarak alır.",
          "Bir rol düzenleyici eklentisi kullanıyorsanız özel roller için bu yetkileri verebilir veya kaldırabilirsiniz."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: şablon oluşturma, düzenleme, silme ve içe/dışa aktarma.",
          "<code>use_mh_order_note_templates</code>: WooCommerce siparişlerinde şablon kullanma.",
          "Gerekli yetkisi olmayan kullanıcılar ilgili yönetim işlevlerini görmez."
        ]
      },
      {
        "title": "10. Güvenlik ve HPOS uyumluluğu",
        "paragraphs": [
          "Eklenti, yönetim işlemleri için WordPress nonce değerleri, yetki kontrolleri, veri temizleme ve çıktı kaçışını kullanır. Şablon içeriği kaydedilmeden veya kullanılmadan önce WordPress’in güvenli HTML kurallarıyla temizlenir.",
          "Sipariş verileri, veritabanı tablolarına doğrudan erişmek yerine WooCommerce sipariş API’leri üzerinden okunur. Bu, eklentiyi WooCommerce HPOS ve klasik sipariş depolamasıyla uyumlu tutar."
        ],
        "items": [
          "WooCommerce ve WordPress’i güncel tutun.",
          "WooCommerce e-posta ayarlarını değiştirdikten sonra müşteri notu iş akışlarını test edin.",
          "Büyük bir şablon kümesini içe aktarmadan önce bir hazırlık sitesi kullanın."
        ]
      },
      {
        "title": "11. Sorun giderme",
        "paragraphs": [
          "Şablonlar bir siparişte görünmüyorsa WooCommerce’in etkin, şablonun yayımlanmış ve geçerli kullanıcının şablon kullanma yetkisine sahip olduğunu kontrol edin.",
          "Çeviriler görünmüyorsa WordPress’te site dilini ve kullanıcı dilini kontrol edin. Eklenti, Farsça dâhil desteklenen tüm diller için incelenmiş paketlenmiş yedek çeviri dosyaları içerir. Diğer diller, incelenmiş WordPress.org dil paketleriyle sağlanmalıdır."
        ],
        "items": [
          "Güncellemeden sonra eski yönetim ekranı hâlâ görünüyorsa nesne/önbellek eklentilerinin önbelleğini temizleyin.",
          "Bir yer tutucu değişmeden kalıyorsa süslü parantezler dâhil listelendiği biçimde tam olarak yazıldığını doğrulayın.",
          "Müşteri notları e-posta ile gönderilmiyorsa müşteri notu bildirimleri için WooCommerce e-posta ayarlarını kontrol edin."
        ]
      },
      {
        "title": "12. Ayarlar sayfası",
        "paragraphs": [
          "Varsayılan not türünü, güvenli HTML davranışını, kullanım görünümünü, favorileri, JSON içe aktarma seçeneklerini ve dil eşleştirmeyi seçmek için <strong>Mailhilfe Sipariş Notları → Ayarlar</strong> bölümünü açın. Daha güvenli günlük kullanım için varsayılan olarak dahili notları tercih edin."
        ],
        "items": []
      },
      {
        "title": "13. Şablon dili ve çok dilli mağazalar",
        "paragraphs": [
          "Her şablonun bir şablon dili olabilir. Aynı metin her sipariş için kullanılabiliyorsa <strong>Tüm diller</strong> seçeneğini, yerelleştirilmiş metinler için ise belirli bir dili seçin.",
          "Mümkün olduğunda eklenti, sipariş diliyle, kullanıcı diliyle veya çok dilli eklentilerden gelen yaygın dil verileriyle eşleşen şablonları tercih edebilir."
        ],
        "items": [
          "Personel için dahili notlarda tek bir tarafsız şablon kullanın.",
          "Almanca, İngilizce veya mağazanın diğer dilleri için ayrı müşteriye yönelik şablonlar oluşturun.",
          "WPML veya Polylang kullanılan mağazalarda sipariş dili algılamasını gerçek bir test siparişiyle sınayın."
        ]
      },
      {
        "title": "14. Özel alanlar ve meta yer tutucular",
        "paragraphs": [
          "Gelişmiş yer tutucular seçilen sipariş veya müşteri meta alanlarını okuyabilir. Sipariş verileri için <code>{order_meta:meta_key}</code>, müşteri kullanıcı verileri için <code>{customer_meta:meta_key}</code> kullanın.",
          "Güvenlik amacıyla password, token, secret, session, auth ve hash gibi hassas anahtar adları engellenir. Meta yer tutucuları yalnızca alanın ne içerdiğini biliyorsanız kullanın."
        ],
        "items": [
          "Örnek: bir gönderim eklentisinin kaydettiği takip numarası için <code>{order_meta:_tracking_number}</code>.",
          "Örnek: KDV numarası alanı için <code>{order_meta:_billing_vat_id}</code>.",
          "Müşteri notlarında dahili veya hassas alanları göstermeyin."
        ]
      },
      {
        "title": "15. Şablonları çoğaltma ve revizyonlar",
        "paragraphs": [
          "Küçük değişikliklerle benzer bir şablona ihtiyacınız olduğunda çoğaltma işlemini kullanın. Kopya, yayımlanmadan önce incelenebilmesi için taslak olarak oluşturulur.",
          "Şablon revizyonları, önceki sürümleri karşılaştırmanızı ve yanlışlıkla yapılan bir değişiklikte eski metni geri yüklemenizi sağlar."
        ],
        "items": [
          "DHL, UPS veya mağazadan teslim alma varyantları oluşturmadan önce genel gönderim şablonunu çoğaltın.",
          "Büyük metin değişikliklerinden sonra revizyonları kontrol edin.",
          "Benzer şablonların karışmaması için başlıkları açık tutun."
        ]
      },
      {
        "title": "16. Yetkiler sayfası",
        "paragraphs": [
          "Hangi WordPress rollerinin şablonları yönetebileceğini ve hangi rollerin siparişlerde şablon kullanabileceğini belirlemek için <strong>Mailhilfe Sipariş Notları → Yetkiler</strong> bölümünü açın.",
          "Yöneticiler gerekli yetkileri korur. Diğer roller için yalnızca günlük görev için gereken yetkileri verin."
        ],
        "items": [
          "Şablonları yönet: şablon oluşturma, düzenleme, silme, içe aktarma ve dışa aktarma.",
          "Şablonları kullan: bir şablon seçme ve WooCommerce siparişine not ekleme.",
          "İçe/dışa aktarma yetkilerini yalnızca güvenilir kullanıcılara verin."
        ]
      },
      {
        "title": "17. İçe aktarma önizlemesi",
        "paragraphs": [
          "JSON içe aktarımları artık değişiklikler uygulanmadan önce bir önizleme gösterir. Önizleme, kaç şablonun oluşturulacağını, güncelleneceğini veya atlanacağını bildirir.",
          "İçe aktarmayı yalnızca önizlemeyi kontrol ettikten sonra onaylayın. Bu, mevcut şablonların yanlışlıkla üzerine yazılmasını önler."
        ],
        "items": [
          "Büyük bir kümeyi içe aktarmadan önce dışa aktarma yedeği oluşturun.",
          "Yalnızca güvenilir bir kaynaktan gelen JSON dosyalarını içe aktarın.",
          "İçe aktarmadan sonra en az bir müşteri notunu ve bir dahili notu test edin."
        ]
      },
      {
        "title": "18. Müşteri notu e-posta davranışı",
        "paragraphs": [
          "İlgili e-posta etkinse müşteri notları WooCommerce e-posta bildirimlerini tetikleyebilir. Eklenti, müşteri notunun oluşturulmasını e-posta işlemesinden ayrı kaydeder. Notu eklemeden önce düzenlenebilir önizlemeyi inceleyin ve posta işleyicisinin sonucunu kontrol etmek için Geçmiş sayfasını kullanın."
        ],
        "items": []
      },
      {
        "title": "19. Önerilen iş akışı",
        "paragraphs": [
          "Güvenli bir günlük iş akışı şöyledir: şablon seçin, değiştirilmiş önizlemeyi inceleyin, gerekirse önizlemeyi düzenleyin, not türünü doğrulayın ve ardından notu ekleyin.",
          "Yeni şablonları gerçek müşterilerle kullanmadan önce kritik olmayan bir siparişte veya hazırlık mağazasında test edin."
        ],
        "items": [
          "Yalnızca personelin görebileceği bilgiler için dahili notları kullanın.",
          "Müşteri notlarını yalnızca müşteriye gönderilebilecek iletiler için kullanın.",
          "Bir şablon değiştirildiğinde yer tutucuları yeniden kontrol edin."
        ]
      },
      {
        "title": "20. Şablon koşulları",
        "paragraphs": [
          "Şablon koşulları, bir şablonun belirli bir sipariş için kullanılabilir olup olmadığını belirler. Şablonları sipariş durumu, ödeme yöntemi, gönderim yöntemi, fatura ülkesi ve en düşük ya da en yüksek sipariş toplamına göre sınırlayabilirsiniz. Yapılandırılan tüm koşullar eşleşmelidir."
        ],
        "items": [
          "Bir koşul şablonu sınırlamamalıysa ilgili alanı boş bırakın.",
          "Ödeme ve gönderim yöntemlerinin teknik kimliklerini kullanın.",
          "Koşullar arayüzde ve not oluşturulmadan önce sunucuda tekrar kontrol edilir."
        ]
      },
      {
        "title": "21. E-posta işleme günlüğü",
        "paragraphs": [
          "Müşteri notları için eklenti, WooCommerce müşteri notu e-postasını işlenmiş olarak bildirdiğinde bunu kaydeder ve teknik wp_mail hatalarını da kaydeder. İşlenmiş olay, WordPress/WooCommerce’in iletiyi posta sistemine teslim ettiğini doğrular; nihai teslimatı veya müşterinin iletiyi okuduğunu kanıtlamaz."
        ],
        "items": [
          "İşlenen ve başarısız e-posta olayları için Geçmiş sayfasını kontrol edin.",
          "Kesin teslimat bilgisi gerektiğinde bir SMTP sağlayıcısı veya posta günlüğü hizmeti kullanın.",
          "Dahili notlar müşteri notu e-postasını tetiklemez."
        ]
      },
      {
        "title": "22. Merkezi geçmiş",
        "paragraphs": [
          "Son not oluşturma işlemlerini, şablon kullanımını, e-posta işlemesini ve e-posta hatalarını incelemek için <strong>Mailhilfe Sipariş Notları → Geçmiş</strong> bölümünü açın. Kayıtlar, mevcut olduğunda sipariş, şablon, kullanıcı, alıcı, olay türü ve zamanı içerir."
        ],
        "items": [
          "Geçmişi destek, denetim ve sorun giderme için kullanın.",
          "Geçmiş, WooCommerce sipariş notlarından ayrıdır.",
          "Sayfa en yeni 250 kaydı gösterir."
        ]
      },
      {
        "title": "23. Test siparişi önizlemesi",
        "paragraphs": [
          "Şablon düzenleyicisinde test önizleme alanına bir WooCommerce sipariş kimliği girin. Kaydedilmemiş değişiklikler dâhil geçerli düzenleyici içeriği, not oluşturmadan veya e-posta göndermeden bu siparişin verileriyle işlenir."
        ],
        "items": [
          "Bir hazırlık siparişi veya kritik olmayan bir test siparişi kullanın.",
          "Eksik değerleri, biçimlendirmeyi, koşulları ve özel meta yer tutucuları kontrol edin.",
          "Seçilen siparişi düzenleme yetkiniz olmalıdır."
        ]
      },
      {
        "title": "24. Kişisel favoriler ve son kullanılan şablonlar",
        "paragraphs": [
          "Her yönetici sipariş ekranında kişisel favorileri işaretleyebilir. Eklenti ayrıca her kullanıcı için son kullanılan on şablonu saklar ve seçimde bunlara daha üst bir konum verir. Genel favoriler tüm kullanıcılarla paylaşılmaya devam eder."
        ],
        "items": [
          "Kişisel favoriler başka bir kullanıcının listesini değiştirmez.",
          "Son kullanılanlar listesi yalnızca bir not başarıyla eklendikten sonra güncellenir.",
          "Kişisel veriler WordPress kullanıcı meta verisi olarak saklanır."
        ]
      },
      {
        "title": "25. Tanılama sayfası",
        "paragraphs": [
          "WordPress, PHP ve WooCommerce sürümleri, HPOS durumu, müşteri notu e-posta durumu, yerel ayar, yayımlanmış şablon sayısı, önbellek durumu ve WP_DEBUG gibi teknik bilgileri görüntülemek için <strong>Mailhilfe Sipariş Notları → Tanılama</strong> bölümünü açın."
        ],
        "items": [
          "Destek isterken tanılama değerlerini kopyalayın.",
          "Sayfa sipariş notu içeriğini veya müşteri adreslerini göstermez.",
          "Geliştiriciler tanılama filtresiyle satır ekleyebilir."
        ]
      },
      {
        "title": "26. Geliştirici kancaları ve filtreleri",
        "paragraphs": [
          "Eklenti; yer tutucular, yer tutucu değerleri, izin verilen meta anahtarları, şablon sonuçları, koşullar, önizleme içeriği, son not içeriği, not eklemeden önceki ve sonraki işlemler, geçmiş kayıtları ve tanılama için kancalar ve filtreler sağlar. Kanca adları ve parametreleri readme.txt dosyasında belgelenmiştir."
        ],
        "items": [
          "Tüm özel verileri doğrulayın, temizleyin ve kaçış uygulayın.",
          "Sipariş tablolarına doğrudan erişmek yerine WooCommerce sipariş API’lerini kullanın.",
          "Özel uzantıları hem HPOS hem de klasik sipariş depolamasıyla uyumlu tutun."
        ]
      }
    ]
  },
  "fa_IR": {
    "title": "راهنمای کامل Mailhilfe Order Note Manager for WooCommerce",
    "intro": "این راهنمای به‌روز، روند کامل کار با Mailhilfe Order Note Manager for WooCommerce را توضیح می‌دهد: ایجاد و قالب‌بندی الگوها، استفاده از زبان الگو، جای‌نگهدارها و جای‌نگهدارهای متا، ویرایش پیش‌نمایش، ارسال ایمن یادداشت‌های مشتری، تنظیمات، دسترسی‌ها، پیش‌نمایش درون‌ریزی و سازگاری با HPOS.",
    "sections": [
      {
        "title": "۱. افزونه چه کاری انجام می‌دهد",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce به شما امکان می‌دهد یادداشت‌های پرکاربرد سفارش‌های ووکامرس را به‌صورت الگوهای قابل استفاده مجدد ذخیره کنید. به این ترتیب نیازی به تایپ چندباره متن‌های یکسان نیست و ارتباطات ثبت‌شده در تاریخچه سفارش یکدست می‌ماند.",
          "هر الگو می‌تواند به‌صورت یادداشت داخلی کارکنان یا یادداشت مشتری آماده شود. هنگام استفاده از الگو در یک سفارش همچنان می‌توانید نوع یادداشت را تغییر دهید."
        ],
        "items": [
          "نمونه‌های معمول: یادآوری پرداخت، تأخیر در ارسال، ثبت تماس تلفنی، بررسی نشانی و پاسخ‌های پشتیبانی.",
          "الگوها از دسته‌بندی، برگزیده‌ها، مرتب‌سازی، شمارنده استفاده و پشتیبان‌گیری JSON پشتیبانی می‌کنند."
        ]
      },
      {
        "title": "۲. ایجاد الگوی تازه",
        "paragraphs": [
          "<strong>یادداشت‌های سفارش Mailhilfe ← افزودن</strong> را باز کنید. یک عنوان روشن وارد کنید، متن یادداشت را در ویرایشگر بنویسید و مشخص کنید نوع پیش‌فرض یادداشت داخلی باشد یا برای مشتری.",
          "عنوان را به‌صورت توضیحی کوتاه درباره کاربرد الگو انتخاب کنید؛ برای نمونه «یادآوری پرداخت» یا «تماس مشتری درباره تحویل». این کار پیدا کردن الگو در صفحه سفارش را آسان‌تر می‌کند."
        ],
        "items": [
          "اگر الگوهای زیادی دارید، یک یا چند دسته به الگو اختصاص دهید.",
          "الگوهای پرکاربرد را به‌عنوان برگزیده علامت‌گذاری کنید.",
          "الگو را منتشر کنید تا در سفارش‌ها در دسترس قرار گیرد."
        ]
      },
      {
        "title": "۳. قالب‌بندی متن الگو",
        "paragraphs": [
          "متن الگو از ویرایشگر وردپرس استفاده می‌کند. می‌توانید متن را با بندها، نوشته پررنگ و کج، فهرست‌ها و پیوندها قالب‌بندی کنید. قالب‌بندی هنگام ایجاد یادداشت حفظ می‌شود، اما محتوا با قواعد HTML امن وردپرس پاک‌سازی خواهد شد.",
          "در یادداشت‌های مشتری از قالب‌بندی با دقت استفاده کنید. معمولاً یک بند کوتاه یا فهرست نشانه‌دار از متن بلند و بدون ساختار خواناتر است."
        ],
        "items": [
          "نمونه مناسب: یک سلام کوتاه، یک توضیح روشن و یک گام بعدی.",
          "در یادداشت‌های مشتری از مخفف‌های داخلی خودداری کنید.",
          "نظرهای خصوصی کارکنان را در الگوهایی که ممکن است به‌عنوان یادداشت مشتری استفاده شوند وارد نکنید."
        ]
      },
      {
        "title": "۴. جای‌نگهدارها",
        "paragraphs": [
          "جای‌نگهدارها واژه‌هایی میان آکولاد هستند. در پیش‌نمایش و هنگام افزودن یادداشت به سفارش، آن‌ها با داده‌های واقعی سفارش جایگزین می‌شوند.",
          "می‌توانید متن عادی و جای‌نگهدارها را با هم ترکیب کنید. نمونه: <code>سلام {customer}، سفارش {order_number} شما را دریافت کردیم.</code>"
        ],
        "items": [
          "سفارش: <code>{order_number}</code>، <code>{order_status}</code>، <code>{order_date}</code>، <code>{order_total}</code>.",
          "مشتری: <code>{customer}</code>، <code>{billing_email}</code>، <code>{billing_phone}</code>.",
          "حمل‌ونقل و پرداخت: <code>{shipping_method}</code>، <code>{payment_method}</code>.",
          "اقلام و فروشگاه: <code>{items}</code>، <code>{item_count}</code>، <code>{site_name}</code>."
        ]
      },
      {
        "title": "۵. پیش‌نمایش پیش از افزودن یادداشت",
        "paragraphs": [
          "یک سفارش ووکامرس را باز و یک الگو انتخاب کنید. پیش‌نمایش، یادداشت را در حالی نشان می‌دهد که جای‌نگهدارها با داده‌های سفارش انتخاب‌شده جایگزین شده‌اند.",
          "همیشه پیش از ایجاد یادداشت، پیش‌نمایش را بررسی کنید. این موضوع به‌ویژه زمانی مهم است که جای‌نگهداری در سفارش مقدار ندارد؛ برای نمونه وقتی نام شرکت حمل‌ونقل یا شماره تلفن ثبت نشده است."
        ],
        "items": [
          "نام‌ها، مبلغ‌ها، روش حمل‌ونقل و فهرست اقلام را بررسی کنید.",
          "بررسی کنید نوع یادداشت انتخاب‌شده درست باشد.",
          "اگر متن باید برای همه سفارش‌های آینده بهتر شود، ابتدا خود الگو را ویرایش کنید."
        ]
      },
      {
        "title": "۶. یادداشت داخلی و یادداشت مشتری",
        "paragraphs": [
          "یادداشت‌های داخلی برای کارکنان فروشگاه هستند و معمولاً برای مستندسازی، کارهای پیگیری یا سابقه پشتیبانی استفاده می‌شوند. یادداشت‌های مشتری ممکن است برای مشتری قابل مشاهده باشند و بسته به تنظیمات ووکامرس، اعلان ایمیلی ووکامرس را فعال کنند.",
          "پیش‌نمایش قابل ویرایش و نوع یادداشت انتخاب‌شده را با دقت بررسی کنید. یادداشت مشتری را فقط برای متنی به‌کار ببرید که مشتری مجاز به خواندن آن است."
        ],
        "items": [
          "یادداشت داخلی: «مشتری تماس گرفت؛ نشانی تحویل تأیید شد.»",
          "یادداشت مشتری: «سفارش شما در حال آماده‌سازی است و به‌زودی ارسال می‌شود.»",
          "هرگز گذرواژه، نظر خصوصی یا اطلاعات ویژه تأمین‌کننده را در یادداشت مشتری قرار ندهید."
        ]
      },
      {
        "title": "۷. برگزیده‌ها، جست‌وجو و مرتب‌سازی",
        "paragraphs": [
          "برگزیده‌ها کمک می‌کنند مهم‌ترین الگوها در ابتدای فهرست انتخاب قرار گیرند. کادر جست‌وجو در صفحه سفارش به شما امکان می‌دهد الگو را بر اساس عنوان، دسته یا محتوا پیدا کنید.",
          "در فهرست الگوها می‌توانید ترتیب را با کشیدن و رها کردن تغییر دهید. ترتیب ذخیره‌شده هنگام نمایش الگوها در صفحه سفارش استفاده می‌شود."
        ],
        "items": [
          "برای الگوهای روزمره از برگزیده‌ها استفاده کنید.",
          "برای گروه‌های موضوعی مانند پرداخت، حمل‌ونقل، مرجوعی و پشتیبانی از دسته‌ها استفاده کنید.",
          "عنوان‌ها را کوتاه نگه دارید تا نتایج جست‌وجو خوانا بمانند."
        ]
      },
      {
        "title": "۸. درون‌ریزی، برون‌بری و الگوهای نمایشی",
        "paragraphs": [
          "برون‌بری JSON یک نسخه پشتیبان از الگوهای شما ایجاد می‌کند. می‌توانید پیش از تغییرات گسترده یا برای انتقال الگوها به فروشگاهی دیگر از آن استفاده کنید.",
          "درون‌ریزی JSON می‌تواند الگوهای تازه ایجاد کند و الگوهای موجود با عنوان یا کلید نمایشی داخلی یکسان را به‌روزرسانی کند. الگوهای نمایشی یک نقطه شروع سریع فراهم می‌کنند و به زبان فعال ساخته می‌شوند."
        ],
        "items": [
          "پیش از تغییرات گروهی، برون‌بری تهیه کنید.",
          "فقط فایل‌های JSON از منبع مورد اعتماد را درون‌ریزی کنید.",
          "پس از درون‌ریزی، چند الگو را باز و قالب‌بندی و جای‌نگهدارها را بررسی کنید."
        ]
      },
      {
        "title": "۹. دسترسی‌ها و نقش‌ها",
        "paragraphs": [
          "افزونه برای مدیریت الگوها و استفاده از الگوها در سفارش‌ها، دسترسی‌های جداگانه دارد. مدیران و مدیران فروشگاه هنگام فعال‌سازی این دسترسی‌ها را به‌طور خودکار دریافت می‌کنند.",
          "اگر از افزونه ویرایش نقش استفاده می‌کنید، می‌توانید این دسترسی‌ها را برای نقش‌های سفارشی اضافه یا حذف کنید."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: ایجاد، ویرایش، حذف و درون‌ریزی/برون‌بری الگوها.",
          "<code>use_mh_order_note_templates</code>: استفاده از الگوها در سفارش‌های ووکامرس.",
          "کاربرانی که دسترسی لازم را ندارند، بخش‌های مدیریتی مربوط را نمی‌بینند."
        ]
      },
      {
        "title": "۱۰. امنیت و سازگاری با HPOS",
        "paragraphs": [
          "افزونه برای عملیات مدیریتی از نانس‌های وردپرس، بررسی دسترسی، پاک‌سازی و ایمن‌سازی خروجی استفاده می‌کند. محتوای الگو پیش از ذخیره یا استفاده با HTML امن وردپرس پاک‌سازی می‌شود.",
          "داده‌های سفارش از طریق APIهای سفارش ووکامرس خوانده می‌شوند، نه با دسترسی مستقیم به جدول‌های پایگاه داده. به همین دلیل افزونه با HPOS ووکامرس و شیوه کلاسیک ذخیره سفارش سازگار می‌ماند."
        ],
        "items": [
          "ووکامرس و وردپرس را به‌روز نگه دارید.",
          "پس از تغییر تنظیمات ایمیل ووکامرس، روند یادداشت مشتری را آزمایش کنید.",
          "پیش از درون‌ریزی مجموعه بزرگی از الگوها، از یک سایت آزمایشی استفاده کنید."
        ]
      },
      {
        "title": "۱۱. رفع اشکال",
        "paragraphs": [
          "اگر الگوها در سفارش نمایش داده نمی‌شوند، بررسی کنید ووکامرس فعال باشد، الگو منتشر شده باشد و کاربر فعلی اجازه استفاده از الگوها را داشته باشد.",
          "اگر ترجمه‌ها نمایش داده نمی‌شوند، زبان سایت و زبان کاربر را در وردپرس بررسی کنید. افزونه برای همه زبان‌های پشتیبانی‌شده، از جمله فارسی، فایل‌های جایگزین داخلی بازبینی‌شده دارد. زبان‌های دیگر باید از بسته‌های زبانی بازبینی‌شده WordPress.org تأمین شوند."
        ],
        "items": [
          "اگر پس از به‌روزرسانی همچنان صفحه مدیریتی قدیمی دیده می‌شود، کش شیء یا افزونه‌های کش را پاک کنید.",
          "اگر جای‌نگهداری بدون تغییر باقی ماند، بررسی کنید دقیقاً مطابق فهرست و همراه با آکولاد نوشته شده باشد.",
          "اگر یادداشت مشتری ایمیل نمی‌شود، تنظیمات ایمیل ووکامرس برای اعلان یادداشت مشتری را بررسی کنید."
        ]
      },
      {
        "title": "۱۲. صفحه تنظیمات",
        "paragraphs": [
          "<strong>یادداشت‌های سفارش Mailhilfe ← تنظیمات</strong> را باز کنید تا نوع پیش‌فرض یادداشت، رفتار HTML امن، نمایش میزان استفاده، برگزیده‌ها، گزینه‌های درون‌ریزی JSON و تطبیق زبان را انتخاب کنید. برای کار روزمره ایمن‌تر، یادداشت داخلی را به‌عنوان پیش‌فرض انتخاب کنید."
        ],
        "items": []
      },
      {
        "title": "۱۳. زبان الگو و فروشگاه‌های چندزبانه",
        "paragraphs": [
          "هر الگو می‌تواند یک زبان داشته باشد. اگر همان متن برای همه سفارش‌ها قابل استفاده است، <strong>همه زبان‌ها</strong> را انتخاب کنید؛ در غیر این صورت برای متن محلی‌شده یک زبان مشخص برگزینید.",
          "در صورت وجود داده مناسب، افزونه می‌تواند الگوهای هم‌زبان با سفارش، کاربر یا اطلاعات زبانی افزونه‌های چندزبانه را ترجیح دهد."
        ],
        "items": [
          "برای یادداشت‌های داخلی کارکنان از یک الگوی خنثی استفاده کنید.",
          "برای مشتریان، الگوهای جداگانه فارسی، آلمانی، انگلیسی یا زبان‌های دیگر بسازید.",
          "در فروشگاه‌های WPML یا Polylang، تشخیص زبان سفارش را با یک سفارش آزمایشی واقعی بررسی کنید."
        ]
      },
      {
        "title": "۱۴. فیلدهای سفارشی و جای‌نگهدارهای متا",
        "paragraphs": [
          "جای‌نگهدارهای پیشرفته می‌توانند فیلدهای متای انتخاب‌شده سفارش یا مشتری را بخوانند. برای داده سفارش از <code>{order_meta:meta_key}</code> و برای داده کاربری مشتری از <code>{customer_meta:meta_key}</code> استفاده کنید.",
          "برای امنیت، نام کلیدهای حساس مانند password، token، secret، session، auth و hash مسدود هستند. فقط زمانی از جای‌نگهدار متا استفاده کنید که محتوای فیلد را می‌شناسید."
        ],
        "items": [
          "نمونه: <code>{order_meta:_tracking_number}</code> برای شماره رهگیری ذخیره‌شده توسط افزونه حمل‌ونقل.",
          "نمونه: <code>{order_meta:_billing_vat_id}</code> برای فیلد شناسه مالیات بر ارزش افزوده.",
          "فیلدهای داخلی یا حساس را در یادداشت مشتری نمایش ندهید."
        ]
      },
      {
        "title": "۱۵. تکثیر الگوها و بازنگری‌ها",
        "paragraphs": [
          "هنگامی که به الگویی مشابه با تغییرات کوچک نیاز دارید از عمل تکثیر استفاده کنید. رونوشت به‌صورت پیش‌نویس ایجاد می‌شود تا پیش از انتشار بررسی شود.",
          "بازنگری‌های الگو به شما امکان می‌دهند نسخه‌های پیشین را مقایسه کنید و اگر تغییری اشتباه بود، متن قبلی را بازگردانید."
        ],
        "items": [
          "پیش از ساخت نسخه‌های DHL، UPS یا تحویل حضوری، یک الگوی عمومی حمل‌ونقل را تکثیر کنید.",
          "پس از تغییرات بزرگ متن، بازنگری‌ها را بررسی کنید.",
          "عنوان‌ها را روشن انتخاب کنید تا الگوهای مشابه با هم اشتباه نشوند."
        ]
      },
      {
        "title": "۱۶. صفحه دسترسی‌ها",
        "paragraphs": [
          "<strong>یادداشت‌های سفارش Mailhilfe ← دسترسی‌ها</strong> را باز کنید تا مشخص کنید کدام نقش‌های وردپرس می‌توانند الگوها را مدیریت کنند و کدام نقش‌ها اجازه استفاده از الگوها در سفارش‌ها را دارند.",
          "مدیران دسترسی‌های لازم را حفظ می‌کنند. برای نقش‌های دیگر فقط دسترسی‌های مورد نیاز کار روزمره را اعطا کنید."
        ],
        "items": [
          "مدیریت الگوها: ایجاد، ویرایش، حذف، درون‌ریزی و برون‌بری الگوها.",
          "استفاده از الگوها: انتخاب الگو و افزودن یادداشت در سفارش ووکامرس.",
          "دسترسی درون‌ریزی/برون‌بری را فقط به کاربران مورد اعتماد بدهید."
        ]
      },
      {
        "title": "۱۷. پیش‌نمایش درون‌ریزی",
        "paragraphs": [
          "درون‌ریزی‌های JSON اکنون پیش از اعمال تغییرات، پیش‌نمایش نشان می‌دهند. پیش‌نمایش مشخص می‌کند چند الگو ایجاد، به‌روزرسانی یا نادیده گرفته خواهند شد.",
          "فقط پس از بررسی پیش‌نمایش، درون‌ریزی را تأیید کنید. این کار از بازنویسی ناخواسته الگوهای موجود جلوگیری می‌کند."
        ],
        "items": [
          "پیش از درون‌ریزی مجموعه بزرگ، یک نسخه پشتیبان برون‌بری ایجاد کنید.",
          "فقط فایل‌های JSON از منبع مورد اعتماد را درون‌ریزی کنید.",
          "پس از درون‌ریزی، دست‌کم یک یادداشت مشتری و یک یادداشت داخلی را آزمایش کنید."
        ]
      },
      {
        "title": "۱۸. رفتار ایمیل یادداشت مشتری",
        "paragraphs": [
          "وقتی ایمیل مربوط در ووکامرس فعال باشد، یادداشت مشتری می‌تواند اعلان ایمیلی ووکامرس را فعال کند. افزونه ایجاد یادداشت مشتری را جدا از پردازش ایمیل ثبت می‌کند. پیش از افزودن یادداشت، پیش‌نمایش قابل ویرایش را بررسی کنید و برای مشاهده نتیجه پردازش ایمیل از صفحه تاریخچه استفاده کنید."
        ],
        "items": []
      },
      {
        "title": "۱۹. روند کاری پیشنهادی",
        "paragraphs": [
          "روند ایمن روزانه چنین است: یک الگو انتخاب کنید، پیش‌نمایش جایگزین‌شده را بررسی کنید، در صورت نیاز آن را ویرایش کنید، نوع یادداشت را تأیید و سپس یادداشت را اضافه کنید.",
          "الگوهای تازه را پیش از استفاده برای مشتریان واقعی، ابتدا در یک سفارش کم‌اهمیت یا فروشگاه آزمایشی بررسی کنید."
        ],
        "items": [
          "برای اطلاعات ویژه کارکنان از یادداشت داخلی استفاده کنید.",
          "یادداشت مشتری را فقط برای پیام‌هایی به‌کار ببرید که ممکن است برای مشتری ارسال شوند.",
          "هر بار که الگو تغییر می‌کند، جای‌نگهدارها را بررسی کنید."
        ]
      },
      {
        "title": "۲۰. شرایط الگو",
        "paragraphs": [
          "شرایط الگو تعیین می‌کنند یک الگو برای سفارش مشخص در دسترس باشد یا نه. می‌توانید الگوها را بر اساس وضعیت سفارش، روش پرداخت، روش حمل‌ونقل، کشور صورتحساب و حداقل یا حداکثر مبلغ سفارش محدود کنید. همه شرایط تنظیم‌شده باید برقرار باشند."
        ],
        "items": [
          "اگر یک شرط نباید الگو را محدود کند، فیلد آن را خالی بگذارید.",
          "از شناسه‌های فنی روش‌های پرداخت و حمل‌ونقل استفاده کنید.",
          "شرایط در رابط کاربری و دوباره در سرور، پیش از ایجاد یادداشت بررسی می‌شوند."
        ]
      },
      {
        "title": "۲۱. گزارش پردازش ایمیل",
        "paragraphs": [
          "برای یادداشت‌های مشتری، افزونه زمانی را ثبت می‌کند که ووکامرس ایمیل یادداشت مشتری را پردازش‌شده گزارش می‌دهد و خطاهای فنی wp_mail را نیز ثبت می‌کند. رویداد پردازش‌شده تأیید می‌کند وردپرس/ووکامرس پیام را به سامانه ایمیل تحویل داده‌اند؛ اما تحویل نهایی یا خواندن پیام توسط مشتری را ثابت نمی‌کند."
        ],
        "items": [
          "برای رویدادهای ایمیل پردازش‌شده و ناموفق، صفحه تاریخچه را بررسی کنید.",
          "اگر اطلاعات قطعی تحویل لازم است، از ارائه‌دهنده SMTP یا سرویس ثبت ایمیل استفاده کنید.",
          "یادداشت‌های داخلی ایمیل یادداشت مشتری را فعال نمی‌کنند."
        ]
      },
      {
        "title": "۲۲. تاریخچه مرکزی",
        "paragraphs": [
          "<strong>یادداشت‌های سفارش Mailhilfe ← تاریخچه</strong> را باز کنید تا ایجاد یادداشت‌های اخیر، استفاده از الگو، پردازش ایمیل و خطاهای ایمیل را بررسی کنید. در صورت وجود، هر ورودی شامل سفارش، الگو، کاربر، گیرنده، نوع رویداد و زمان است."
        ],
        "items": [
          "از تاریخچه برای پشتیبانی، حسابرسی و رفع اشکال استفاده کنید.",
          "تاریخچه افزونه از یادداشت‌های سفارش ووکامرس جدا است.",
          "صفحه ۲۵۰ ورودی اخیر را نمایش می‌دهد."
        ]
      },
      {
        "title": "۲۳. پیش‌نمایش سفارش آزمایشی",
        "paragraphs": [
          "در ویرایشگر الگو، شناسه یک سفارش ووکامرس را در بخش پیش‌نمایش آزمایشی وارد کنید. محتوای فعلی ویرایشگر، از جمله تغییرات ذخیره‌نشده، با داده‌های آن سفارش نمایش داده می‌شود، بدون آنکه یادداشتی ایجاد یا ایمیلی ارسال شود."
        ],
        "items": [
          "از یک سفارش سایت آزمایشی یا سفارش کم‌اهمیت استفاده کنید.",
          "مقادیر خالی، قالب‌بندی، شرایط و جای‌نگهدارهای متای سفارشی را بررسی کنید.",
          "باید اجازه ویرایش سفارش انتخاب‌شده را داشته باشید."
        ]
      },
      {
        "title": "۲۴. برگزیده‌های شخصی و الگوهای اخیراً استفاده‌شده",
        "paragraphs": [
          "هر مدیر می‌تواند در صفحه سفارش، برگزیده‌های شخصی خود را علامت‌گذاری کند. افزونه همچنین ده الگوی اخیر هر کاربر را ذخیره می‌کند و جایگاه بالاتری در فهرست به آن‌ها می‌دهد. برگزیده‌های عمومی همچنان میان همه کاربران مشترک هستند."
        ],
        "items": [
          "برگزیده‌های شخصی فهرست کاربر دیگر را تغییر نمی‌دهند.",
          "فهرست اخیر فقط پس از افزودن موفق یادداشت به‌روزرسانی می‌شود.",
          "داده‌های شخصی به‌صورت فراداده کاربر وردپرس ذخیره می‌شوند."
        ]
      },
      {
        "title": "۲۵. صفحه عیب‌یابی",
        "paragraphs": [
          "<strong>یادداشت‌های سفارش Mailhilfe ← عیب‌یابی</strong> را باز کنید تا اطلاعات فنی مانند نسخه‌های وردپرس، PHP و ووکامرس، وضعیت HPOS، وضعیت ایمیل یادداشت مشتری، زبان، تعداد الگوهای منتشرشده، وضعیت کش و WP_DEBUG را ببینید."
        ],
        "items": [
          "هنگام درخواست پشتیبانی، مقادیر عیب‌یابی را کپی کنید.",
          "این صفحه محتوای یادداشت سفارش یا نشانی مشتریان را نمایش نمی‌دهد.",
          "توسعه‌دهندگان می‌توانند با فیلتر عیب‌یابی ردیف‌های بیشتری اضافه کنند."
        ]
      },
      {
        "title": "۲۶. هوک‌ها و فیلترهای توسعه‌دهندگان",
        "paragraphs": [
          "افزونه برای جای‌نگهدارها، مقدارهای جای‌نگهدار، کلیدهای متای مجاز، نتایج الگو، شرایط، محتوای پیش‌نمایش، محتوای نهایی یادداشت، اقدامات پیش و پس از افزودن یادداشت، رکوردهای تاریخچه و عیب‌یابی، هوک و فیلتر ارائه می‌دهد. نام و پارامترهای هوک‌ها در readme.txt مستند شده‌اند."
        ],
        "items": [
          "همه داده‌های سفارشی را اعتبارسنجی، پاک‌سازی و ایمن‌سازی خروجی کنید.",
          "به‌جای دسترسی مستقیم به جدول سفارش‌ها از APIهای سفارش ووکامرس استفاده کنید.",
          "افزونه‌های سفارشی را با HPOS و ذخیره‌سازی کلاسیک سفارش سازگار نگه دارید."
        ]
      }
    ]
  },
  "vi": {
    "title": "Hướng dẫn chi tiết cho Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Hướng dẫn cập nhật này giải thích toàn bộ quy trình làm việc của Mailhilfe Order Note Manager for WooCommerce: tạo và định dạng mẫu, sử dụng ngôn ngữ mẫu, biến giữ chỗ và biến meta, chỉnh sửa bản xem trước, gửi ghi chú cho khách hàng an toàn, cài đặt, quyền, xem trước dữ liệu nhập và khả năng tương thích với HPOS.",
    "sections": [
      {
        "title": "1. Chức năng của plugin",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce cho phép bạn lưu các ghi chú đơn hàng WooCommerce thường dùng thành mẫu có thể tái sử dụng. Nhờ đó, bạn không phải nhập lại cùng một nội dung nhiều lần và việc trao đổi trong lịch sử đơn hàng luôn nhất quán.",
          "Mỗi mẫu có thể được chuẩn bị dưới dạng ghi chú nội bộ cho nhân viên hoặc ghi chú cho khách hàng. Bạn vẫn có thể thay đổi loại ghi chú khi sử dụng mẫu trong một đơn hàng."
        ],
        "items": [
          "Ví dụ thường gặp: nhắc thanh toán, chậm giao hàng, ghi lại cuộc gọi, kiểm tra địa chỉ và phản hồi chăm sóc khách hàng.",
          "Mẫu hỗ trợ danh mục, mục yêu thích, sắp xếp, bộ đếm sử dụng và sao lưu JSON."
        ]
      },
      {
        "title": "2. Tạo mẫu mới",
        "paragraphs": [
          "Mở <strong>Ghi chú đơn hàng Mailhilfe → Thêm mới</strong>. Nhập tiêu đề rõ ràng, viết nội dung ghi chú trong trình soạn thảo và chọn loại ghi chú mặc định là nội bộ hay dành cho khách hàng.",
          "Dùng tiêu đề như một mô tả ngắn về mục đích, chẳng hạn “Nhắc thanh toán” hoặc “Khách hàng gọi về việc giao hàng”. Điều này giúp tìm mẫu dễ dàng hơn trên màn hình đơn hàng."
        ],
        "items": [
          "Gán một hoặc nhiều danh mục khi bạn có nhiều mẫu.",
          "Đánh dấu các mẫu thường dùng là yêu thích.",
          "Xuất bản mẫu để mẫu xuất hiện trong đơn hàng."
        ]
      },
      {
        "title": "3. Định dạng nội dung mẫu",
        "paragraphs": [
          "Nội dung mẫu sử dụng trình soạn thảo WordPress. Bạn có thể định dạng bằng đoạn văn, chữ đậm, chữ nghiêng, danh sách và liên kết. Định dạng được giữ lại khi tạo ghi chú, nhưng nội dung sẽ được làm sạch theo các quy tắc HTML an toàn của WordPress.",
          "Hãy định dạng ghi chú cho khách hàng một cách vừa phải. Một đoạn ngắn hoặc danh sách gạch đầu dòng thường dễ đọc hơn một khối văn bản dài không có cấu trúc."
        ],
        "items": [
          "Ví dụ tốt: lời chào ngắn, một giải thích rõ ràng và một bước tiếp theo.",
          "Tránh dùng chữ viết tắt nội bộ trong ghi chú cho khách hàng.",
          "Không đưa nhận xét riêng của nhân viên vào mẫu có thể được dùng làm ghi chú cho khách hàng."
        ]
      },
      {
        "title": "4. Biến giữ chỗ",
        "paragraphs": [
          "Biến giữ chỗ là các từ nằm trong dấu ngoặc nhọn. Chúng được thay bằng dữ liệu thực của đơn hàng trong bản xem trước và khi ghi chú được thêm vào đơn hàng.",
          "Bạn có thể kết hợp văn bản thông thường với biến giữ chỗ. Ví dụ: <code>Xin chào {customer}, chúng tôi đã nhận được đơn hàng {order_number} của bạn.</code>"
        ],
        "items": [
          "Đơn hàng: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Khách hàng: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Giao hàng và thanh toán: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Mặt hàng và cửa hàng: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Xem trước trước khi thêm ghi chú",
        "paragraphs": [
          "Mở một đơn hàng WooCommerce và chọn mẫu. Bản xem trước hiển thị ghi chú sau khi các biến giữ chỗ đã được thay bằng dữ liệu của đơn hàng được chọn.",
          "Luôn kiểm tra bản xem trước trước khi tạo ghi chú. Điều này đặc biệt quan trọng khi một biến giữ chỗ không có giá trị trong đơn hàng, ví dụ khi thiếu tên công ty giao hàng hoặc số điện thoại."
        ],
        "items": [
          "Kiểm tra tên, tổng tiền, phương thức giao hàng và danh sách mặt hàng.",
          "Kiểm tra loại ghi chú đã chọn có chính xác hay không.",
          "Hãy sửa mẫu trước nếu cùng nội dung cần được cải thiện cho mọi đơn hàng trong tương lai."
        ]
      },
      {
        "title": "6. Ghi chú nội bộ và ghi chú cho khách hàng",
        "paragraphs": [
          "Ghi chú nội bộ dành cho nhân viên cửa hàng và thường được dùng để lưu tài liệu, công việc cần theo dõi hoặc lịch sử chăm sóc. Ghi chú cho khách hàng có thể hiển thị cho khách hàng và có thể kích hoạt email thông báo WooCommerce tùy theo cài đặt WooCommerce của bạn.",
          "Hãy kiểm tra kỹ bản xem trước có thể chỉnh sửa và loại ghi chú đã chọn. Chỉ dùng ghi chú cho khách hàng với nội dung mà khách hàng được phép đọc."
        ],
        "items": [
          "Ghi chú nội bộ: “Khách hàng đã gọi, địa chỉ giao hàng đã được xác nhận.”",
          "Ghi chú cho khách hàng: “Đơn hàng của bạn đang được chuẩn bị và sẽ sớm được gửi đi.”",
          "Không bao giờ đưa mật khẩu, nhận xét riêng hoặc thông tin chỉ dành cho nhà cung cấp vào ghi chú cho khách hàng."
        ]
      },
      {
        "title": "7. Yêu thích, tìm kiếm và sắp xếp",
        "paragraphs": [
          "Mục yêu thích giúp đưa các mẫu quan trọng nhất lên đầu danh sách chọn. Ô tìm kiếm trên màn hình đơn hàng giúp bạn tìm mẫu theo tiêu đề, danh mục hoặc nội dung.",
          "Trong danh sách mẫu, bạn có thể thay đổi thứ tự bằng thao tác kéo và thả. Thứ tự đã lưu sẽ được dùng khi hiển thị mẫu trên màn hình đơn hàng."
        ],
        "items": [
          "Dùng mục yêu thích cho các mẫu sử dụng hằng ngày.",
          "Dùng danh mục cho các nhóm chủ đề như Thanh toán, Giao hàng, Trả hàng và Hỗ trợ.",
          "Giữ tiêu đề ngắn để kết quả tìm kiếm dễ đọc."
        ]
      },
      {
        "title": "8. Nhập, xuất và mẫu minh họa",
        "paragraphs": [
          "Chức năng xuất JSON tạo bản sao lưu cho các mẫu của bạn. Bạn có thể dùng bản sao lưu trước khi thực hiện thay đổi lớn hoặc để chuyển mẫu sang cửa hàng khác.",
          "Chức năng nhập JSON có thể tạo mẫu mới và cập nhật mẫu hiện có có cùng tiêu đề hoặc khóa minh họa nội bộ. Mẫu minh họa cung cấp điểm bắt đầu nhanh và được tạo bằng ngôn ngữ đang hoạt động."
        ],
        "items": [
          "Xuất bản sao lưu trước khi thay đổi hàng loạt.",
          "Chỉ nhập tệp JSON từ nguồn đáng tin cậy.",
          "Sau khi nhập, hãy mở một vài mẫu để kiểm tra định dạng và biến giữ chỗ."
        ]
      },
      {
        "title": "9. Quyền và vai trò",
        "paragraphs": [
          "Plugin sử dụng các quyền riêng biệt cho việc quản lý mẫu và sử dụng mẫu trong đơn hàng. Quản trị viên và quản lý cửa hàng tự động nhận các quyền này khi kích hoạt plugin.",
          "Nếu dùng plugin chỉnh sửa vai trò, bạn có thể cấp hoặc thu hồi các quyền này cho vai trò tùy chỉnh."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: tạo, sửa, xóa và nhập/xuất mẫu.",
          "<code>use_mh_order_note_templates</code>: sử dụng mẫu trong đơn hàng WooCommerce.",
          "Người dùng không có quyền cần thiết sẽ không thấy các chức năng quản trị liên quan."
        ]
      },
      {
        "title": "10. Bảo mật và khả năng tương thích HPOS",
        "paragraphs": [
          "Plugin sử dụng nonce của WordPress, kiểm tra quyền, làm sạch dữ liệu và thoát dữ liệu đầu ra cho các thao tác quản trị. Nội dung mẫu được làm sạch bằng HTML an toàn của WordPress trước khi lưu hoặc sử dụng.",
          "Dữ liệu đơn hàng được đọc qua API đơn hàng WooCommerce thay vì truy cập trực tiếp bảng cơ sở dữ liệu. Nhờ đó, plugin tương thích với cả WooCommerce HPOS và cơ chế lưu trữ đơn hàng cổ điển."
        ],
        "items": [
          "Luôn cập nhật WooCommerce và WordPress.",
          "Kiểm tra lại quy trình ghi chú cho khách hàng sau khi thay đổi cài đặt email WooCommerce.",
          "Dùng trang thử nghiệm trước khi nhập một bộ mẫu lớn."
        ]
      },
      {
        "title": "11. Khắc phục sự cố",
        "paragraphs": [
          "Nếu mẫu không xuất hiện trong đơn hàng, hãy kiểm tra WooCommerce đang hoạt động, mẫu đã được xuất bản và người dùng hiện tại có quyền sử dụng mẫu.",
          "Nếu bản dịch không xuất hiện, hãy kiểm tra ngôn ngữ trang web và ngôn ngữ người dùng trong WordPress. Plugin bao gồm các tệp dự phòng đã được kiểm tra cho mọi ngôn ngữ được hỗ trợ, bao gồm tiếng Việt. Các ngôn ngữ khác nên được cung cấp qua gói ngôn ngữ WordPress.org đã được duyệt."
        ],
        "items": [
          "Sau khi cập nhật, hãy xóa bộ nhớ đệm đối tượng hoặc plugin bộ nhớ đệm nếu màn hình quản trị cũ vẫn còn hiển thị.",
          "Nếu biến giữ chỗ không được thay, hãy kiểm tra biến được viết chính xác như trong danh sách, bao gồm cả dấu ngoặc nhọn.",
          "Nếu ghi chú cho khách hàng không được gửi qua email, hãy kiểm tra cài đặt email WooCommerce dành cho thông báo ghi chú khách hàng."
        ]
      },
      {
        "title": "12. Trang cài đặt",
        "paragraphs": [
          "Mở <strong>Ghi chú đơn hàng Mailhilfe → Cài đặt</strong> để chọn loại ghi chú mặc định, cách xử lý HTML an toàn, hiển thị lượt sử dụng, mục yêu thích, tùy chọn nhập JSON và đối chiếu ngôn ngữ. Hãy dùng ghi chú nội bộ làm mặc định để an toàn hơn trong công việc hằng ngày."
        ],
        "items": []
      },
      {
        "title": "13. Ngôn ngữ mẫu và cửa hàng đa ngôn ngữ",
        "paragraphs": [
          "Mỗi mẫu có thể có một ngôn ngữ. Chọn <strong>Tất cả ngôn ngữ</strong> nếu cùng nội dung có thể dùng cho mọi đơn hàng, hoặc chọn một ngôn ngữ cụ thể cho nội dung đã bản địa hóa.",
          "Khi có dữ liệu phù hợp, plugin có thể ưu tiên mẫu khớp với ngôn ngữ đơn hàng, ngôn ngữ người dùng hoặc dữ liệu ngôn ngữ phổ biến từ plugin đa ngôn ngữ."
        ],
        "items": [
          "Dùng một mẫu trung lập cho ghi chú nội bộ của nhân viên.",
          "Tạo mẫu riêng dành cho khách hàng bằng tiếng Việt, tiếng Anh hoặc các ngôn ngữ khác của cửa hàng.",
          "Đối với cửa hàng dùng WPML hoặc Polylang, hãy kiểm tra khả năng nhận diện ngôn ngữ đơn hàng bằng một đơn hàng thử nghiệm thực tế."
        ]
      },
      {
        "title": "14. Trường tùy chỉnh và biến meta",
        "paragraphs": [
          "Các biến giữ chỗ nâng cao có thể đọc một số trường meta của đơn hàng hoặc khách hàng. Dùng <code>{order_meta:meta_key}</code> cho dữ liệu đơn hàng và <code>{customer_meta:meta_key}</code> cho dữ liệu người dùng của khách hàng.",
          "Vì lý do bảo mật, các tên khóa nhạy cảm như password, token, secret, session, auth và hash bị chặn. Chỉ dùng biến meta khi bạn biết rõ trường đó chứa gì."
        ],
        "items": [
          "Ví dụ: <code>{order_meta:_tracking_number}</code> cho mã theo dõi do plugin giao hàng lưu.",
          "Ví dụ: <code>{order_meta:_billing_vat_id}</code> cho trường mã số thuế VAT.",
          "Không để lộ các trường nội bộ hoặc nhạy cảm trong ghi chú cho khách hàng."
        ]
      },
      {
        "title": "15. Nhân bản mẫu và bản sửa đổi",
        "paragraphs": [
          "Dùng thao tác nhân bản khi bạn cần một mẫu tương tự chỉ với vài thay đổi nhỏ. Bản sao được tạo dưới dạng bản nháp để có thể kiểm tra trước khi xuất bản.",
          "Bản sửa đổi của mẫu cho phép bạn so sánh các phiên bản trước và khôi phục nội dung cũ nếu vô tình thực hiện thay đổi sai."
        ],
        "items": [
          "Nhân bản một mẫu giao hàng chung trước khi tạo các biến thể cho DHL, UPS hoặc nhận tại cửa hàng.",
          "Kiểm tra bản sửa đổi sau khi thay đổi nội dung lớn.",
          "Đặt tiêu đề rõ ràng để tránh nhầm lẫn giữa các mẫu tương tự."
        ]
      },
      {
        "title": "16. Trang quyền",
        "paragraphs": [
          "Mở <strong>Ghi chú đơn hàng Mailhilfe → Quyền</strong> để quyết định vai trò WordPress nào được quản lý mẫu và vai trò nào được sử dụng mẫu trong đơn hàng.",
          "Quản trị viên vẫn giữ các quyền bắt buộc. Với vai trò khác, chỉ cấp những quyền cần thiết cho công việc hằng ngày."
        ],
        "items": [
          "Quản lý mẫu: tạo, sửa, xóa, nhập và xuất mẫu.",
          "Sử dụng mẫu: chọn mẫu và thêm ghi chú trong đơn hàng WooCommerce.",
          "Chỉ cấp quyền nhập/xuất cho người dùng đáng tin cậy."
        ]
      },
      {
        "title": "17. Xem trước dữ liệu nhập",
        "paragraphs": [
          "Tính năng nhập JSON hiện hiển thị bản xem trước trước khi áp dụng thay đổi. Bản xem trước cho biết số mẫu sẽ được tạo, cập nhật hoặc bỏ qua.",
          "Chỉ xác nhận nhập sau khi kiểm tra bản xem trước. Điều này giúp ngăn việc vô tình ghi đè mẫu hiện có."
        ],
        "items": [
          "Tạo bản sao lưu bằng chức năng xuất trước khi nhập một bộ mẫu lớn.",
          "Chỉ nhập tệp JSON từ nguồn đáng tin cậy.",
          "Sau khi nhập, hãy thử ít nhất một ghi chú cho khách hàng và một ghi chú nội bộ."
        ]
      },
      {
        "title": "18. Hoạt động email của ghi chú cho khách hàng",
        "paragraphs": [
          "Ghi chú cho khách hàng có thể kích hoạt email thông báo WooCommerce khi email tương ứng được bật. Plugin ghi nhận việc tạo ghi chú cho khách hàng riêng với quá trình xử lý email. Hãy kiểm tra bản xem trước có thể chỉnh sửa trước khi thêm ghi chú và dùng trang Lịch sử để xem kết quả của trình xử lý thư."
        ],
        "items": []
      },
      {
        "title": "19. Quy trình làm việc được đề xuất",
        "paragraphs": [
          "Quy trình hằng ngày an toàn là: chọn mẫu, kiểm tra bản xem trước đã thay dữ liệu, chỉnh sửa nếu cần, xác nhận loại ghi chú rồi mới thêm ghi chú.",
          "Với mẫu mới, hãy thử trước trên một đơn hàng không quan trọng hoặc cửa hàng thử nghiệm trước khi dùng cho khách hàng thật."
        ],
        "items": [
          "Dùng ghi chú nội bộ cho thông tin chỉ dành cho nhân viên.",
          "Chỉ dùng ghi chú cho khách hàng với thông điệp có thể được gửi đến khách hàng.",
          "Kiểm tra lại biến giữ chỗ mỗi khi mẫu được thay đổi."
        ]
      },
      {
        "title": "20. Điều kiện mẫu",
        "paragraphs": [
          "Điều kiện mẫu quyết định mẫu có khả dụng cho một đơn hàng cụ thể hay không. Bạn có thể giới hạn mẫu theo trạng thái đơn hàng, phương thức thanh toán, phương thức giao hàng, quốc gia thanh toán và tổng đơn hàng tối thiểu hoặc tối đa. Tất cả điều kiện đã cấu hình đều phải khớp."
        ],
        "items": [
          "Để trống một trường nếu điều kiện đó không nên giới hạn mẫu.",
          "Dùng ID kỹ thuật của phương thức thanh toán và giao hàng.",
          "Điều kiện được kiểm tra trên giao diện và kiểm tra lại trên máy chủ trước khi tạo ghi chú."
        ]
      },
      {
        "title": "21. Nhật ký xử lý email",
        "paragraphs": [
          "Đối với ghi chú cho khách hàng, plugin ghi lại thời điểm WooCommerce báo email ghi chú khách hàng đã được xử lý và cũng ghi lại lỗi kỹ thuật của wp_mail. Sự kiện đã xử lý xác nhận rằng WordPress/WooCommerce đã chuyển thư cho hệ thống gửi thư; sự kiện này không chứng minh thư đã được giao cuối cùng hoặc khách hàng đã đọc thư."
        ],
        "items": [
          "Kiểm tra trang Lịch sử để xem các sự kiện email đã xử lý và thất bại.",
          "Dùng nhà cung cấp SMTP hoặc dịch vụ nhật ký thư khi cần thông tin giao thư chắc chắn.",
          "Ghi chú nội bộ không kích hoạt email ghi chú cho khách hàng."
        ]
      },
      {
        "title": "22. Lịch sử tập trung",
        "paragraphs": [
          "Mở <strong>Ghi chú đơn hàng Mailhilfe → Lịch sử</strong> để xem việc tạo ghi chú gần đây, sử dụng mẫu, xử lý email và lỗi email. Khi có dữ liệu, mỗi mục gồm đơn hàng, mẫu, người dùng, người nhận, loại sự kiện và thời gian."
        ],
        "items": [
          "Dùng lịch sử cho hỗ trợ, kiểm tra và khắc phục sự cố.",
          "Lịch sử này tách biệt với ghi chú đơn hàng WooCommerce.",
          "Trang hiển thị 250 mục gần đây nhất."
        ]
      },
      {
        "title": "23. Xem trước với đơn hàng thử nghiệm",
        "paragraphs": [
          "Trong trình soạn thảo mẫu, nhập ID đơn hàng WooCommerce vào khu vực xem trước thử nghiệm. Nội dung hiện tại của trình soạn thảo, kể cả thay đổi chưa lưu, sẽ được hiển thị với dữ liệu của đơn hàng đó mà không tạo ghi chú hoặc gửi email."
        ],
        "items": [
          "Dùng đơn hàng trên trang thử nghiệm hoặc một đơn hàng thử không quan trọng.",
          "Kiểm tra giá trị bị thiếu, định dạng, điều kiện và biến meta tùy chỉnh.",
          "Bạn phải có quyền sửa đơn hàng đã chọn."
        ]
      },
      {
        "title": "24. Yêu thích cá nhân và mẫu dùng gần đây",
        "paragraphs": [
          "Mỗi quản trị viên có thể đánh dấu mục yêu thích cá nhân trên màn hình đơn hàng. Plugin cũng lưu mười mẫu được mỗi người dùng sử dụng gần đây nhất và đưa chúng lên vị trí cao hơn trong danh sách chọn. Mục yêu thích toàn cục vẫn được chia sẻ với mọi người dùng."
        ],
        "items": [
          "Mục yêu thích cá nhân không thay đổi danh sách của người dùng khác.",
          "Danh sách gần đây chỉ được cập nhật sau khi thêm ghi chú thành công.",
          "Dữ liệu cá nhân được lưu dưới dạng metadata người dùng WordPress."
        ]
      },
      {
        "title": "25. Trang chẩn đoán",
        "paragraphs": [
          "Mở <strong>Ghi chú đơn hàng Mailhilfe → Chẩn đoán</strong> để xem thông tin kỹ thuật như phiên bản WordPress, PHP và WooCommerce, trạng thái HPOS, trạng thái email ghi chú khách hàng, locale, số mẫu đã xuất bản, trạng thái bộ nhớ đệm và WP_DEBUG."
        ],
        "items": [
          "Sao chép các giá trị chẩn đoán khi yêu cầu hỗ trợ.",
          "Trang này không hiển thị nội dung ghi chú đơn hàng hoặc địa chỉ khách hàng.",
          "Nhà phát triển có thể thêm hàng dữ liệu bằng bộ lọc chẩn đoán."
        ]
      },
      {
        "title": "26. Hook và bộ lọc dành cho nhà phát triển",
        "paragraphs": [
          "Plugin cung cấp hook và bộ lọc cho biến giữ chỗ, giá trị biến giữ chỗ, khóa meta được phép, kết quả mẫu, điều kiện, nội dung xem trước, nội dung ghi chú cuối cùng, thao tác trước và sau khi thêm ghi chú, bản ghi lịch sử và chẩn đoán. Tên hook và tham số được ghi trong readme.txt."
        ],
        "items": [
          "Xác thực, làm sạch và thoát toàn bộ dữ liệu tùy chỉnh.",
          "Dùng API đơn hàng WooCommerce thay vì truy cập trực tiếp bảng đơn hàng.",
          "Giữ tiện ích mở rộng tùy chỉnh tương thích với cả HPOS và cơ chế lưu trữ đơn hàng cổ điển."
        ]
      }
    ]
  },
  "cs_CZ": {
    "title": "Podrobná nápověda k Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Tato aktualizovaná nápověda vysvětluje celý pracovní postup v pluginu Mailhilfe Order Note Manager for WooCommerce: vytváření a formátování šablon, používání jazyků šablon, zástupných symbolů a meta zástupných symbolů, úpravu náhledů, bezpečné odesílání poznámek zákazníkům, nastavení, oprávnění, náhled importu a kompatibilitu s HPOS.",
    "sections": [
      {
        "title": "1. K čemu plugin slouží",
        "paragraphs": [
          "Mailhilfe Order Note Manager for WooCommerce umožňuje ukládat často používané poznámky k objednávkám WooCommerce jako opakovaně použitelné šablony. Nemusíte tak stále psát stejný text a komunikace v historii objednávky zůstává jednotná.",
          "Šablonu lze připravit jako interní poznámku pro zaměstnance nebo jako poznámku pro zákazníka. Při použití šablony v objednávce můžete typ poznámky stále změnit."
        ],
        "items": [
          "Typické příklady: připomínky plateb, zpoždění dodávky, záznamy telefonátů, kontrola adresy a odpovědi zákaznické podpory.",
          "Šablony podporují kategorie, oblíbené položky, řazení, počítadlo použití a zálohování do JSON."
        ]
      },
      {
        "title": "2. Vytvoření nové šablony",
        "paragraphs": [
          "Otevřete <strong>Poznámky k objednávkám Mailhilfe → Přidat novou</strong>. Zadejte srozumitelný název, napište text poznámky v editoru a zvolte, zda má být výchozím typem interní poznámka, nebo poznámka pro zákazníka.",
          "Název používejte jako krátký popis účelu, například „Připomínka platby“ nebo „Zákazník volal ohledně dodání“. Šablonu pak na obrazovce objednávky snáze najdete."
        ],
        "items": [
          "Pokud máte mnoho šablon, přiřaďte jim jednu nebo více kategorií.",
          "Často používané šablony označte jako oblíbené.",
          "Šablonu publikujte, aby byla dostupná v objednávkách."
        ]
      },
      {
        "title": "3. Formátování textu šablony",
        "paragraphs": [
          "Text šablony se upravuje ve WordPress editoru. Můžete používat odstavce, tučné písmo, kurzívu, seznamy a odkazy. Formátování se při vytvoření poznámky zachová, ale obsah se vyčistí podle pravidel bezpečného HTML ve WordPressu.",
          "V poznámkách pro zákazníky používejte formátování střídmě. Krátký odstavec nebo odrážkový seznam se obvykle čte lépe než dlouhý nestrukturovaný text."
        ],
        "items": [
          "Dobrý příklad: krátký pozdrav, jedno jasné vysvětlení a jeden další krok.",
          "V poznámkách pro zákazníky nepoužívejte interní zkratky.",
          "Do šablon, které lze použít jako poznámky pro zákazníky, nevkládejte soukromé komentáře zaměstnanců."
        ]
      },
      {
        "title": "4. Zástupné symboly",
        "paragraphs": [
          "Zástupné symboly jsou výrazy ve složených závorkách. V náhledu a při přidání poznámky k objednávce se nahradí skutečnými údaji objednávky.",
          "Běžný text můžete kombinovat se zástupnými symboly. Příklad: <code>Dobrý den, {customer}, přijali jsme vaši objednávku {order_number}.</code>"
        ],
        "items": [
          "Objednávka: <code>{order_number}</code>, <code>{order_status}</code>, <code>{order_date}</code>, <code>{order_total}</code>.",
          "Zákazník: <code>{customer}</code>, <code>{billing_email}</code>, <code>{billing_phone}</code>.",
          "Doprava a platba: <code>{shipping_method}</code>, <code>{payment_method}</code>.",
          "Položky a obchod: <code>{items}</code>, <code>{item_count}</code>, <code>{site_name}</code>."
        ]
      },
      {
        "title": "5. Náhled před přidáním poznámky",
        "paragraphs": [
          "Otevřete objednávku WooCommerce a vyberte šablonu. Náhled zobrazí poznámku, ve které již byly zástupné symboly nahrazeny údaji z vybrané objednávky.",
          "Před vytvořením poznámky náhled vždy zkontrolujte. Je to zvlášť důležité, pokud některý zástupný symbol nemá v objednávce hodnotu, například když chybí společnost pro dodání nebo telefonní číslo."
        ],
        "items": [
          "Zkontrolujte jména, částky, způsob dopravy a seznam položek.",
          "Zkontrolujte, zda je vybrán správný typ poznámky.",
          "Pokud se má stejný text zlepšit pro všechny budoucí objednávky, upravte nejprve samotnou šablonu."
        ]
      },
      {
        "title": "6. Interní poznámky a poznámky pro zákazníky",
        "paragraphs": [
          "Interní poznámky jsou určeny zaměstnancům obchodu a běžně slouží k dokumentaci, následným úkolům nebo historii podpory. Poznámky pro zákazníky mohou být zákazníkovi viditelné a podle nastavení WooCommerce mohou spustit e-mailové oznámení.",
          "Pečlivě zkontrolujte upravitelný náhled i vybraný typ poznámky. Jako poznámku pro zákazníka používejte pouze text, který zákazník smí číst."
        ],
        "items": [
          "Interní poznámka: „Zákazník volal, dodací adresa potvrzena.“",
          "Poznámka pro zákazníka: „Vaši objednávku připravujeme a brzy ji odešleme.“",
          "Do poznámek pro zákazníky nikdy nevkládejte hesla, soukromé komentáře ani informace určené pouze dodavatelům."
        ]
      },
      {
        "title": "7. Oblíbené položky, hledání a řazení",
        "paragraphs": [
          "Oblíbené položky pomáhají zobrazit nejdůležitější šablony v horní části výběru. Vyhledávací pole na obrazovce objednávky umožňuje najít šablonu podle názvu, kategorie nebo obsahu.",
          "V seznamu šablon můžete změnit pořadí přetažením. Uložené pořadí se použije při zobrazení šablon na obrazovce objednávky."
        ],
        "items": [
          "Oblíbené položky používejte pro každodenní šablony.",
          "Kategorie používejte pro tematické skupiny, například Platby, Doprava, Vrácení a Podpora.",
          "Názvy udržujte krátké, aby výsledky hledání zůstaly přehledné."
        ]
      },
      {
        "title": "8. Import, export a ukázkové šablony",
        "paragraphs": [
          "Export JSON vytvoří zálohu vašich šablon. Můžete jej použít před většími změnami nebo pro přenos šablon do jiného obchodu.",
          "Import JSON může vytvářet nové šablony a aktualizovat existující šablony se stejným názvem nebo interním klíčem ukázky. Ukázkové šablony poskytují rychlý výchozí bod a vytvoří se v aktivním jazyce."
        ],
        "items": [
          "Před hromadnými změnami proveďte export.",
          "Importujte pouze soubory JSON z důvěryhodného zdroje.",
          "Po importu otevřete několik šablon a zkontrolujte formátování a zástupné symboly."
        ]
      },
      {
        "title": "9. Oprávnění a role",
        "paragraphs": [
          "Plugin používá samostatná oprávnění pro správu šablon a jejich používání v objednávkách. Administrátoři a správci obchodu tato oprávnění při aktivaci získají automaticky.",
          "Pokud používáte plugin pro úpravu rolí, můžete tato oprávnění přidělit vlastním rolím nebo jim je odebrat."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>: vytváření, úpravy, mazání a import/export šablon.",
          "<code>use_mh_order_note_templates</code>: používání šablon v objednávkách WooCommerce.",
          "Uživatelé bez potřebného oprávnění související funkce administrace neuvidí."
        ]
      },
      {
        "title": "10. Zabezpečení a kompatibilita s HPOS",
        "paragraphs": [
          "Plugin pro akce v administraci používá nonce, kontroly oprávnění, sanitizaci a escapování. Obsah šablony se před uložením nebo použitím vyčistí pomocí pravidel bezpečného HTML ve WordPressu.",
          "Údaje objednávky se načítají přes API objednávek WooCommerce, nikoli přímým přístupem k databázovým tabulkám. Plugin je tak kompatibilní s WooCommerce HPOS i klasickým ukládáním objednávek."
        ],
        "items": [
          "Udržujte WooCommerce a WordPress aktualizované.",
          "Po změně nastavení e-mailů WooCommerce otestujte pracovní postup s poznámkami pro zákazníky.",
          "Před importem velkého množství šablon použijte testovací web."
        ]
      },
      {
        "title": "11. Řešení problémů",
        "paragraphs": [
          "Pokud se šablony v objednávce nezobrazují, ověřte, že je WooCommerce aktivní, šablona je publikovaná a aktuální uživatel má oprávnění šablony používat.",
          "Pokud se nezobrazují překlady, zkontrolujte jazyk webu a jazyk uživatele ve WordPressu. Plugin obsahuje zkontrolované vestavěné záložní soubory pro všechny podporované jazyky, včetně perštiny, vietnamštiny a češtiny. Ostatní jazyky by měly být poskytovány prostřednictvím zkontrolovaných jazykových balíčků WordPress.org."
        ],
        "items": [
          "Pokud se po aktualizaci stále zobrazuje stará administrace, vymažte objektovou cache nebo cache pluginu.",
          "Pokud zástupný symbol zůstane nezměněn, ověřte, že je zapsán přesně podle seznamu, včetně složených závorek.",
          "Pokud se poznámky pro zákazníky neodesílají e-mailem, zkontrolujte nastavení e-mailového oznámení o poznámce zákazníka ve WooCommerce."
        ]
      },
      {
        "title": "12. Stránka nastavení",
        "paragraphs": [
          "Otevřete <strong>Poznámky k objednávkám Mailhilfe → Nastavení</strong> a zvolte výchozí typ poznámky, chování bezpečného HTML, zobrazení použití, oblíbené položky, možnosti importu JSON a porovnávání jazyků. Pro bezpečnější každodenní práci použijte jako výchozí interní poznámky."
        ],
        "items": []
      },
      {
        "title": "13. Jazyk šablony a vícejazyčné obchody",
        "paragraphs": [
          "Každá šablona může mít přiřazený jazyk. Pokud lze stejný text použít pro každou objednávku, zvolte <strong>Všechny jazyky</strong>; pro lokalizované texty vyberte konkrétní jazyk.",
          "Pokud jsou potřebné údaje dostupné, plugin může upřednostnit šablony odpovídající jazyku objednávky, jazyku uživatele nebo běžným jazykovým údajům z vícejazyčných pluginů."
        ],
        "items": [
          "Pro interní poznámky zaměstnanců použijte jednu neutrální šablonu.",
          "Pro zákazníky vytvořte samostatné šablony v češtině, němčině, angličtině nebo dalších jazycích obchodu.",
          "V obchodech s WPML nebo Polylang otestujte rozpoznávání jazyka objednávky na skutečné testovací objednávce."
        ]
      },
      {
        "title": "14. Vlastní pole a meta zástupné symboly",
        "paragraphs": [
          "Pokročilé zástupné symboly mohou načítat vybraná meta pole objednávky nebo zákazníka. Pro údaje objednávky použijte <code>{order_meta:meta_key}</code> a pro uživatelské údaje zákazníka <code>{customer_meta:meta_key}</code>.",
          "Z bezpečnostních důvodů jsou blokovány citlivé názvy klíčů obsahující například password, token, secret, session, auth nebo hash. Meta zástupné symboly používejte pouze tehdy, když víte, co dané pole obsahuje."
        ],
        "items": [
          "Příklad: <code>{order_meta:_tracking_number}</code> pro číslo zásilky uložené pluginem dopravy.",
          "Příklad: <code>{order_meta:_billing_vat_id}</code> pro pole DIČ.",
          "V poznámkách pro zákazníky nezveřejňujte interní ani citlivá pole."
        ]
      },
      {
        "title": "15. Duplikování šablon a revize",
        "paragraphs": [
          "Funkci duplikování použijte, když potřebujete podobnou šablonu s drobnými změnami. Kopie se vytvoří jako koncept, abyste ji mohli před publikováním zkontrolovat.",
          "Revize šablon umožňují porovnat dřívější verze a obnovit předchozí text, pokud byla změna provedena omylem."
        ],
        "items": [
          "Před vytvořením variant pro DHL, UPS nebo osobní odběr duplikujte obecnou šablonu dopravy.",
          "Po větších úpravách textu zkontrolujte revize.",
          "Používejte jasné názvy, aby se podobné šablony nezaměňovaly."
        ]
      },
      {
        "title": "16. Stránka oprávnění",
        "paragraphs": [
          "Otevřete <strong>Poznámky k objednávkám Mailhilfe → Oprávnění</strong> a určete, které role WordPressu smějí spravovat šablony a které je smějí používat v objednávkách.",
          "Administrátorům potřebná oprávnění zůstávají. Ostatním rolím přidělte pouze oprávnění potřebná pro jejich každodenní úkoly."
        ],
        "items": [
          "Správa šablon: vytváření, úpravy, mazání, import a export šablon.",
          "Používání šablon: výběr šablony a přidání poznámky v objednávce WooCommerce.",
          "Oprávnění k importu a exportu přidělujte pouze důvěryhodným uživatelům."
        ]
      },
      {
        "title": "17. Náhled importu",
        "paragraphs": [
          "Importy JSON nyní před provedením změn zobrazují náhled. Náhled uvádí, kolik šablon bude vytvořeno, aktualizováno nebo přeskočeno.",
          "Import potvrďte až po kontrole náhledu. Zabráníte tak nechtěnému přepsání existujících šablon."
        ],
        "items": [
          "Před importem velké sady vytvořte zálohu exportem.",
          "Importujte pouze soubory JSON z důvěryhodného zdroje.",
          "Po importu otestujte alespoň jednu poznámku pro zákazníka a jednu interní poznámku."
        ]
      },
      {
        "title": "18. Chování e-mailů s poznámkami pro zákazníky",
        "paragraphs": [
          "Poznámky pro zákazníky mohou spustit e-mailová oznámení WooCommerce, pokud je příslušný e-mail povolen. Plugin zaznamenává vytvoření poznámky pro zákazníka odděleně od zpracování e-mailu. Před přidáním poznámky zkontrolujte upravitelný náhled a na stránce Historie ověřte výsledek zpracování e-mailu."
        ],
        "items": []
      },
      {
        "title": "19. Doporučený pracovní postup",
        "paragraphs": [
          "Bezpečný každodenní postup je následující: vyberte šablonu, zkontrolujte náhled s nahrazenými hodnotami, podle potřeby jej upravte, ověřte typ poznámky a teprve poté poznámku přidejte.",
          "Nové šablony nejprve otestujte v nekritické objednávce nebo v testovacím obchodě, než je použijete u skutečných zákazníků."
        ],
        "items": [
          "Interní poznámky používejte pro informace určené pouze zaměstnancům.",
          "Poznámky pro zákazníky používejte pouze pro zprávy, které mohou být zákazníkovi odeslány.",
          "Po každé změně šablony zkontrolujte zástupné symboly."
        ]
      },
      {
        "title": "20. Podmínky šablony",
        "paragraphs": [
          "Podmínky šablony určují, zda je šablona pro konkrétní objednávku dostupná. Šablony můžete omezit podle stavu objednávky, platební metody, způsobu dopravy, fakturační země a minimální nebo maximální hodnoty objednávky. Všechny nastavené podmínky musí být splněny."
        ],
        "items": [
          "Pokud daná podmínka nemá šablonu omezovat, ponechte pole prázdné.",
          "Používejte technická ID platebních metod a způsobů dopravy.",
          "Podmínky se kontrolují v rozhraní a znovu na serveru před vytvořením poznámky."
        ]
      },
      {
        "title": "21. Protokol zpracování e-mailů",
        "paragraphs": [
          "U poznámek pro zákazníky plugin zaznamenává, kdy WooCommerce oznámí zpracování e-mailu s poznámkou pro zákazníka, a také technické chyby wp_mail. Událost „zpracováno“ potvrzuje, že WordPress/WooCommerce předal zprávu poštovnímu systému; neprokazuje konečné doručení ani přečtení zákazníkem."
        ],
        "items": [
          "Na stránce Historie kontrolujte zpracované a neúspěšné e-mailové události.",
          "Pokud potřebujete spolehlivou informaci o doručení, použijte poskytovatele SMTP nebo službu pro protokolování e-mailů.",
          "Interní poznámky nespouštějí e-mail s poznámkou pro zákazníka."
        ]
      },
      {
        "title": "22. Centrální historie",
        "paragraphs": [
          "Otevřete <strong>Poznámky k objednávkám Mailhilfe → Historie</strong> a zkontrolujte nedávné vytvoření poznámek, použití šablon, zpracování e-mailů a chyby e-mailů. Pokud jsou údaje k dispozici, záznamy obsahují objednávku, šablonu, uživatele, příjemce, typ události a čas."
        ],
        "items": [
          "Historii používejte pro podporu, audit a řešení problémů.",
          "Historie je oddělená od poznámek objednávek WooCommerce.",
          "Stránka zobrazuje 250 nejnovějších záznamů."
        ]
      },
      {
        "title": "23. Náhled testovací objednávky",
        "paragraphs": [
          "V editoru šablony zadejte do oblasti testovacího náhledu ID objednávky WooCommerce. Aktuální obsah editoru včetně neuložených změn se vykreslí s údaji z této objednávky, aniž by se vytvořila poznámka nebo odeslal e-mail."
        ],
        "items": [
          "Použijte testovací objednávku nebo nekritickou objednávku.",
          "Zkontrolujte chybějící hodnoty, formátování, podmínky a vlastní meta zástupné symboly.",
          "Musíte mít oprávnění upravovat vybranou objednávku."
        ]
      },
      {
        "title": "24. Osobní oblíbené položky a naposledy použité šablony",
        "paragraphs": [
          "Každý administrátor může na obrazovce objednávky označovat osobní oblíbené položky. Plugin také ukládá deset šablon, které každý uživatel použil naposledy, a zobrazuje je ve výběru výše. Globální oblíbené položky zůstávají sdílené se všemi uživateli."
        ],
        "items": [
          "Osobní oblíbené položky nemění seznam jiného uživatele.",
          "Seznam nedávných šablon se aktualizuje pouze po úspěšném přidání poznámky.",
          "Osobní údaje se ukládají jako uživatelská metadata WordPressu."
        ]
      },
      {
        "title": "25. Stránka diagnostiky",
        "paragraphs": [
          "Otevřete <strong>Poznámky k objednávkám Mailhilfe → Diagnostika</strong> a zobrazte technické informace, například verze WordPressu, PHP a WooCommerce, stav HPOS, stav e-mailů s poznámkami pro zákazníky, locale, počet publikovaných šablon, stav cache a WP_DEBUG."
        ],
        "items": [
          "Při žádosti o podporu zkopírujte diagnostické hodnoty.",
          "Stránka nezobrazuje obsah poznámek k objednávkám ani adresy zákazníků.",
          "Vývojáři mohou pomocí filtru diagnostiky přidat další řádky."
        ]
      },
      {
        "title": "26. Hooky a filtry pro vývojáře",
        "paragraphs": [
          "Plugin poskytuje hooky a filtry pro zástupné symboly, hodnoty zástupných symbolů, povolené meta klíče, výsledky šablon, podmínky, obsah náhledu, konečný obsah poznámky, akce před přidáním poznámky a po něm, záznamy historie a diagnostiku. Názvy hooků a parametry jsou popsány v souboru readme.txt."
        ],
        "items": [
          "Veškerá vlastní data validujte, sanitizujte a escapujte.",
          "Namísto přímého přístupu k tabulkám objednávek používejte API objednávek WooCommerce.",
          "Vlastní rozšíření udržujte kompatibilní s HPOS i klasickým ukládáním objednávek."
        ]
      }
    ]
  }
}
JSON;
		$content = json_decode( $json, true );
		return is_array( $content ) ? $content : array();
	}
}
