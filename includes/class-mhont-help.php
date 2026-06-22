<?php
/**
 * Multilingual detailed help page.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders built-in multilingual help.
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
	 * Renders the multilingual help page.
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
		$map     = array(
			'de' => 'de_DE', 'fr' => 'fr_FR', 'es' => 'es_ES', 'it' => 'it_IT', 'pt' => 'pt_BR',
			'nl' => 'nl_NL', 'pl' => 'pl_PL', 'ru' => 'ru_RU', 'zh' => 'zh_CN', 'ja' => 'ja',
			'ko' => 'ko_KR', 'tr' => 'tr_TR', 'ar' => 'ar', 'hi' => 'hi_IN', 'id' => 'id_ID',
			'vi' => 'vi', 'th' => 'th', 'uk' => 'uk', 'sv' => 'sv_SE', 'da' => 'da_DK',
		);

		foreach ( $locales as $locale ) {
			if ( ! is_string( $locale ) || '' === $locale ) {
				continue;
			}

			$normalized = str_replace( '-', '_', $locale );
			if ( 'de_DE_formal' === $normalized ) {
				return 'de_DE';
			}

			$language = strtolower( strtok( $normalized, '_' ) );
			if ( isset( $map[ $language ] ) ) {
				return $map[ $language ];
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
		"permission_error": "Sie sind nicht berechtigt, Notizvorlagen zu verwalten."
	},
	"fr_FR": {
		"page_title": "Aide de Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Aide",
		"permission_error": "Vous n’êtes pas autorisé à gérer les modèles de notes."
	},
	"es_ES": {
		"page_title": "Ayuda de Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Ayuda",
		"permission_error": "No tienes permiso para gestionar las plantillas de notas."
	},
	"it_IT": {
		"page_title": "Guida di Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Aiuto",
		"permission_error": "Non hai il permesso di gestire i modelli di note."
	},
	"pt_BR": {
		"page_title": "Ajuda do Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Ajuda",
		"permission_error": "Você não tem permissão para gerenciar modelos de notas."
	},
	"nl_NL": {
		"page_title": "Hulp voor Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Hulp",
		"permission_error": "Je mag geen notitiesjablonen beheren."
	},
	"pl_PL": {
		"page_title": "Pomoc Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Pomoc",
		"permission_error": "Nie masz uprawnień do zarządzania szablonami notatek."
	},
	"ru_RU": {
		"page_title": "Справка Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Справка",
		"permission_error": "У вас нет прав для управления шаблонами заметок."
	},
	"zh_CN": {
		"page_title": "Mailhilfe Order Note Manager for WooCommerce 帮助",
		"menu_title": "帮助",
		"permission_error": "您无权管理备注模板。"
	},
	"ja": {
		"page_title": "Mailhilfe Order Note Manager for WooCommerce ヘルプ",
		"menu_title": "ヘルプ",
		"permission_error": "メモテンプレートを管理する権限がありません。"
	},
	"ko_KR": {
		"page_title": "Mailhilfe Order Note Manager for WooCommerce 도움말",
		"menu_title": "도움말",
		"permission_error": "메모 템플릿을 관리할 권한이 없습니다."
	},
	"tr_TR": {
		"page_title": "Mailhilfe Order Note Manager for WooCommerce yardımı",
		"menu_title": "Yardım",
		"permission_error": "Not şablonlarını yönetme izniniz yok."
	},
	"ar": {
		"page_title": "مساعدة Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "المساعدة",
		"permission_error": "ليست لديك صلاحية إدارة قوالب الملاحظات."
	},
	"hi_IN": {
		"page_title": "Mailhilfe Order Note Manager for WooCommerce सहायता",
		"menu_title": "सहायता",
		"permission_error": "आपको नोट टेम्पलेट प्रबंधित करने की अनुमति नहीं है।"
	},
	"id_ID": {
		"page_title": "Bantuan Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Bantuan",
		"permission_error": "Anda tidak diizinkan mengelola templat catatan."
	},
	"vi": {
		"page_title": "Trợ giúp Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Trợ giúp",
		"permission_error": "Bạn không được phép quản lý mẫu ghi chú."
	},
	"th": {
		"page_title": "วิธีใช้ Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "วิธีใช้",
		"permission_error": "คุณไม่มีสิทธิ์จัดการเทมเพลตบันทึก"
	},
	"uk": {
		"page_title": "Довідка Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Довідка",
		"permission_error": "У вас немає прав для керування шаблонами нотаток."
	},
	"sv_SE": {
		"page_title": "Hjälp för Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Hjälp",
		"permission_error": "Du har inte behörighet att hantera notismallar."
	},
	"da_DK": {
		"page_title": "Hjælp til Mailhilfe Order Note Manager for WooCommerce",
		"menu_title": "Hjælp",
		"permission_error": "Du har ikke tilladelse til at administrere noteskabeloner."
	}
}
JSON;
		$texts = json_decode( $json, true );
		return is_array( $texts ) ? $texts : array();
	}

	/**
	 * Returns all localized help content sets.
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
          "If translations do not appear, check the site language and user language in WordPress. The plugin includes built-in fallback files for the supported languages."
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
          "Bestelldaten werden über WooCommerce-Order-APIs gelesen und nicht direkt aus alten Datenbanktabellen abgefragt. Dadurch bleibt das Plugin mit WooCommerce HPOS und klassischer Bestellspeicherung kompatibel."
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
          "Wenn Übersetzungen nicht erscheinen, prüfen Sie die Website-Sprache und die Benutzersprache in WordPress. Das Plugin enthält Fallback-Dateien für die unterstützten Sprachen."
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
          "Über WordPress-Versionen können frühere Texte verglichen und bei Bedarf wiederhergestellt werden."
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
          "Verwenden Sie WooCommerce-Order-APIs statt direkter Zugriffe auf Bestelltabellen.",
          "Achten Sie bei Erweiterungen auf HPOS und die klassische Bestellspeicherung."
        ]
      }
    ]
  },
  "fr_FR": {
    "title": "Aide détaillée pour Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Cette aide explique le flux complet : créer des modèles formatés, utiliser les variables, ajouter des notes dans les commandes WooCommerce, importer/exporter en JSON, gérer les droits, utiliser HPOS et travailler en sécurité. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Ce que fait le plugin",
        "paragraphs": [
          "Enregistrez les textes utilisés souvent comme modèles réutilisables afin de garder l’historique des commandes cohérent.",
          "Cette aide explique le flux complet : créer des modèles formatés, utiliser les variables, ajouter des notes dans les commandes WooCommerce, importer/exporter en JSON, gérer les droits, utiliser HPOS et travailler en sécurité."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Créer un modèle",
        "paragraphs": [
          "Ouvrez le menu des modèles, ajoutez un titre clair, choisissez le type de note et publiez le modèle.",
          "Ouvrez le menu des modèles, ajoutez un titre clair, choisissez le type de note et publiez le modèle."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Mettre en forme le texte",
        "paragraphs": [
          "Utilisez l’éditeur WordPress pour les paragraphes, listes, gras, italique et liens. Le HTML est nettoyé avant l’enregistrement.",
          "Utilisez l’éditeur WordPress pour les paragraphes, listes, gras, italique et liens. Le HTML est nettoyé avant l’enregistrement."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Utiliser les variables",
        "paragraphs": [
          "Les variables entre accolades sont remplacées par les données réelles de la commande dans l’aperçu et dans la note.",
          "Les variables entre accolades sont remplacées par les données réelles de la commande dans l’aperçu et dans la note."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Vérifier l’aperçu",
        "paragraphs": [
          "Vérifiez toujours le nom, le total, les articles et le type de note avant d’ajouter la note.",
          "Vérifiez toujours le nom, le total, les articles et le type de note avant d’ajouter la note."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Notes internes et notes client",
        "paragraphs": [
          "Les notes internes sont réservées au personnel. Les notes client peuvent être visibles et déclencher des e-mails WooCommerce.",
          "Les notes internes sont réservées au personnel. Les notes client peuvent être visibles et déclencher des e-mails WooCommerce."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Favoris, recherche et tri",
        "paragraphs": [
          "Utilisez les favoris pour les modèles quotidiens, la recherche pour retrouver rapidement un texte et le glisser-déposer pour définir l’ordre.",
          "Utilisez les favoris pour les modèles quotidiens, la recherche pour retrouver rapidement un texte et le glisser-déposer pour définir l’ordre."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Import, export et modèles de démonstration",
        "paragraphs": [
          "Exportez un fichier JSON comme sauvegarde. Importez uniquement des fichiers de confiance. Les modèles de démonstration sont créés dans la langue active.",
          "Exportez un fichier JSON comme sauvegarde. Importez uniquement des fichiers de confiance. Les modèles de démonstration sont créés dans la langue active."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Rôles et droits",
        "paragraphs": [
          "Les administrateurs et responsables de boutique obtiennent les droits automatiquement. Les rôles personnalisés peuvent être adaptés.",
          "Les administrateurs et responsables de boutique obtiennent les droits automatiquement. Les rôles personnalisés peuvent être adaptés."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Sécurité et compatibilité HPOS",
        "paragraphs": [
          "Les actions utilisent nonces, contrôles de droits, nettoyage et échappement. Les commandes sont lues via les API WooCommerce pour HPOS.",
          "Les actions utilisent nonces, contrôles de droits, nettoyage et échappement. Les commandes sont lues via les API WooCommerce pour HPOS."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Dépannage",
        "paragraphs": [
          "Si un élément ne fonctionne pas, vérifiez WooCommerce, le statut publié, les droits utilisateur, la langue du site et le cache.",
          "Cette aide explique le flux complet : créer des modèles formatés, utiliser les variables, ajouter des notes dans les commandes WooCommerce, importer/exporter en JSON, gérer les droits, utiliser HPOS et travailler en sécurité."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Page des réglages",
        "paragraphs": [
          "Ouvrez <strong>Mailhilfe Order Notes → Réglages</strong> pour choisir le type de note par défaut, le HTML sécurisé, l’affichage de l’utilisation, les favoris, l’import JSON et la correspondance des langues. Utilisez de préférence les notes internes au quotidien."
        ],
        "items": []
      },
      {
        "title": "13. Langue du modèle et boutiques multilingues",
        "paragraphs": [
          "Chaque modèle peut recevoir une langue. Choisissez <strong>Toutes les langues</strong> pour un texte utilisable partout, ou une langue précise pour les messages client traduits.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Champs personnalisés et métadonnées",
        "paragraphs": [
          "Les espaces réservés <code>{order_meta:meta_key}</code> et <code>{customer_meta:meta_key}</code> peuvent insérer des métadonnées sélectionnées. Les clés sensibles comme password, token ou secret sont bloquées.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Dupliquer et révisions",
        "paragraphs": [
          "L’action de duplication crée une copie en brouillon. Les révisions WordPress aident à comparer et restaurer d’anciennes versions.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Autorisations",
        "paragraphs": [
          "La page <strong>Permissions</strong> permet de définir quelles rôles gèrent les modèles et quelles rôles les utilisent dans les commandes.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Aperçu d’importation",
        "paragraphs": [
          "L’import JSON affiche un aperçu avec les modèles créés, mis à jour ou ignorés avant l’application définitive.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. État de l’e-mail de note client",
        "paragraphs": [
          "Les notes client peuvent déclencher des notifications par e-mail WooCommerce lorsque l’e-mail correspondant est activé. L’extension enregistre la création de la note séparément du traitement de l’e-mail. Vérifiez l’aperçu modifiable avant d’ajouter la note et consultez la page Historique pour contrôler le résultat du traitement."
        ],
        "items": []
      },
      {
        "title": "19. Flux de travail recommandé",
        "paragraphs": [
          "Sélectionnez un modèle, vérifiez l’aperçu remplacé, modifiez-le si nécessaire, contrôlez le type de note puis ajoutez la note.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Conditions des modèles",
        "paragraphs": [
          "Les conditions déterminent si un modèle est disponible pour une commande. Vous pouvez limiter un modèle selon l’état de la commande, le moyen de paiement, le mode de livraison, le pays de facturation et un montant minimal ou maximal. Toutes les conditions renseignées doivent être remplies."
        ],
        "items": [
          "Laissez un champ vide pour ne pas appliquer cette restriction.",
          "Utilisez les identifiants techniques des moyens de paiement et de livraison.",
          "Les conditions sont vérifiées dans l’interface puis à nouveau côté serveur."
        ]
      },
      {
        "title": "21. Journal du traitement des e-mails",
        "paragraphs": [
          "Pour les notes client, l’extension enregistre le traitement signalé par WooCommerce et les erreurs techniques de wp_mail. Un état « traité » confirme la remise au système de messagerie, mais pas la livraison finale ni la lecture par le client."
        ],
        "items": [
          "Consultez la page Historique pour les événements traités ou échoués.",
          "Utilisez un fournisseur SMTP pour obtenir des informations de livraison plus précises.",
          "Les notes internes ne déclenchent pas l’e-mail de note client."
        ]
      },
      {
        "title": "22. Historique central",
        "paragraphs": [
          "Ouvrez <strong>Mailhilfe Order Notes → Historique</strong> pour voir les notes créées, l’utilisation des modèles, le traitement des e-mails et les erreurs. La commande, le modèle, l’utilisateur, le destinataire, le type d’événement et l’heure sont affichés lorsqu’ils sont disponibles."
        ],
        "items": [
          "Utilisez l’historique pour l’assistance, l’audit et le dépannage.",
          "Cet historique est séparé des notes de commande WooCommerce.",
          "Les 250 entrées les plus récentes sont affichées."
        ]
      },
      {
        "title": "23. Aperçu avec une commande de test",
        "paragraphs": [
          "Dans l’éditeur du modèle, saisissez l’identifiant d’une commande WooCommerce. Le contenu actuel, même non enregistré, est affiché avec les données de cette commande sans créer de note ni envoyer d’e-mail."
        ],
        "items": [
          "Utilisez une commande de test ou un site de préproduction.",
          "Contrôlez les valeurs manquantes, la mise en forme, les conditions et les métadonnées.",
          "Vous devez être autorisé à modifier la commande choisie."
        ]
      },
      {
        "title": "24. Favoris personnels et modèles récents",
        "paragraphs": [
          "Chaque utilisateur peut marquer ses favoris personnels dans la commande. L’extension mémorise aussi les dix derniers modèles utilisés avec succès par utilisateur et les place plus haut. Les favoris globaux restent communs à tous."
        ],
        "items": [
          "Les favoris personnels n’affectent pas les autres utilisateurs.",
          "La liste récente est actualisée uniquement après l’ajout réussi d’une note.",
          "Ces données sont stockées dans les métadonnées utilisateur WordPress."
        ]
      },
      {
        "title": "25. Page de diagnostic",
        "paragraphs": [
          "Ouvrez <strong>Mailhilfe Order Notes → Diagnostic</strong> pour voir les versions WordPress, PHP et WooCommerce, l’état HPOS, l’e-mail de note client, la langue, le nombre de modèles publiés, le cache et WP_DEBUG."
        ],
        "items": [
          "Communiquez ces informations lors d’une demande d’assistance.",
          "Aucun contenu de note ni adresse client n’est affiché.",
          "Les développeurs peuvent ajouter des lignes avec le filtre de diagnostic."
        ]
      },
      {
        "title": "26. Actions et filtres pour développeurs",
        "paragraphs": [
          "Des actions et filtres permettent d’étendre les variables, leurs valeurs, les clés de métadonnées autorisées, les résultats de modèles, les conditions, l’aperçu, le contenu final, les actions avant/après ajout, l’historique et le diagnostic. Les noms sont documentés dans readme.txt."
        ],
        "items": [
          "Validez, nettoyez et échappez toutes les données personnalisées.",
          "Utilisez les API de commande WooCommerce plutôt que les tables directement.",
          "Préservez la compatibilité HPOS et le stockage classique."
        ]
      }
    ]
  },
  "es_ES": {
    "title": "Ayuda detallada de Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Esta ayuda explica el flujo completo: crear plantillas con formato, usar marcadores, añadir notas en pedidos de WooCommerce, importar/exportar JSON, gestionar permisos, usar HPOS y trabajar de forma segura. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Qué hace el plugin",
        "paragraphs": [
          "Guarde textos frecuentes como plantillas reutilizables para mantener coherente el historial de pedidos.",
          "Esta ayuda explica el flujo completo: crear plantillas con formato, usar marcadores, añadir notas en pedidos de WooCommerce, importar/exportar JSON, gestionar permisos, usar HPOS y trabajar de forma segura."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Crear una plantilla",
        "paragraphs": [
          "Abra el menú de plantillas, añada un título claro, elija el tipo de nota y publique la plantilla.",
          "Abra el menú de plantillas, añada un título claro, elija el tipo de nota y publique la plantilla."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Formatear el texto",
        "paragraphs": [
          "Use el editor de WordPress para párrafos, listas, negrita, cursiva y enlaces. El HTML se limpia antes de guardar.",
          "Use el editor de WordPress para párrafos, listas, negrita, cursiva y enlaces. El HTML se limpia antes de guardar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Usar marcadores",
        "paragraphs": [
          "Los marcadores entre llaves se sustituyen por datos reales del pedido en la vista previa y en la nota.",
          "Los marcadores entre llaves se sustituyen por datos reales del pedido en la vista previa y en la nota."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Comprobar la vista previa",
        "paragraphs": [
          "Compruebe siempre nombre, total, artículos y tipo de nota antes de añadirla.",
          "Compruebe siempre nombre, total, artículos y tipo de nota antes de añadirla."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Notas internas y notas de cliente",
        "paragraphs": [
          "Las notas internas son para el personal. Las notas de cliente pueden ser visibles y activar correos de WooCommerce.",
          "Las notas internas son para el personal. Las notas de cliente pueden ser visibles y activar correos de WooCommerce."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Favoritos, búsqueda y ordenación",
        "paragraphs": [
          "Use favoritos para plantillas diarias, búsqueda para encontrar textos y arrastrar y soltar para ordenar.",
          "Use favoritos para plantillas diarias, búsqueda para encontrar textos y arrastrar y soltar para ordenar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Importación, exportación y plantillas demo",
        "paragraphs": [
          "Exporte JSON como copia de seguridad. Importe solo archivos de confianza. Las plantillas demo se crean en el idioma activo.",
          "Exporte JSON como copia de seguridad. Importe solo archivos de confianza. Las plantillas demo se crean en el idioma activo."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Roles y permisos",
        "paragraphs": [
          "Administradores y gestores de tienda reciben permisos automáticamente. Los roles personalizados pueden ajustarse.",
          "Administradores y gestores de tienda reciben permisos automáticamente. Los roles personalizados pueden ajustarse."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Seguridad y compatibilidad HPOS",
        "paragraphs": [
          "Las acciones usan nonces, permisos, saneamiento y escape. Los pedidos se leen mediante APIs de WooCommerce para HPOS.",
          "Las acciones usan nonces, permisos, saneamiento y escape. Los pedidos se leen mediante APIs de WooCommerce para HPOS."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Solución de problemas",
        "paragraphs": [
          "Si algo falla, revise WooCommerce, publicación, permisos, idioma del sitio y caché.",
          "Esta ayuda explica el flujo completo: crear plantillas con formato, usar marcadores, añadir notas en pedidos de WooCommerce, importar/exportar JSON, gestionar permisos, usar HPOS y trabajar de forma segura."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Página de ajustes",
        "paragraphs": [
          "Abra <strong>Mailhilfe Order Notes → Ajustes</strong> para elegir el tipo de nota predeterminado, el HTML seguro, la visualización de uso, los favoritos, la importación JSON y la coincidencia de idioma. Use notas internas como opción predeterminada para el trabajo diario."
        ],
        "items": []
      },
      {
        "title": "13. Idioma de la plantilla y tiendas multilingües",
        "paragraphs": [
          "Cada plantilla puede tener un idioma. Elige <strong>Todos los idiomas</strong> para textos generales o un idioma concreto para mensajes traducidos al cliente.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Campos personalizados y metadatos",
        "paragraphs": [
          "Los marcadores <code>{order_meta:meta_key}</code> y <code>{customer_meta:meta_key}</code> insertan metadatos seleccionados. Se bloquean claves sensibles como password, token o secret.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Duplicar y revisiones",
        "paragraphs": [
          "La acción de duplicar crea una copia como borrador. Las revisiones de WordPress permiten comparar y restaurar versiones anteriores.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Permisos",
        "paragraphs": [
          "La página <strong>Permisos</strong> define qué roles gestionan plantillas y qué roles las usan en pedidos.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Vista previa de importación",
        "paragraphs": [
          "La importación JSON muestra una vista previa con plantillas creadas, actualizadas u omitidas antes de aplicar cambios.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Estado del correo de nota al cliente",
        "paragraphs": [
          "Las notas al cliente pueden activar notificaciones por correo de WooCommerce cuando el correo correspondiente está habilitado. El plugin registra la creación de la nota por separado del procesamiento del correo. Revise la vista previa editable antes de añadirla y consulte la página Historial para comprobar el resultado."
        ],
        "items": []
      },
      {
        "title": "19. Flujo recomendado",
        "paragraphs": [
          "Selecciona una plantilla, revisa la vista previa, edítala si hace falta, confirma el tipo de nota y añade la nota.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Condiciones de las plantillas",
        "paragraphs": [
          "Las condiciones determinan si una plantilla está disponible para un pedido. Se puede limitar por estado, método de pago, método de envío, país de facturación y total mínimo o máximo. Todas las condiciones configuradas deben cumplirse."
        ],
        "items": [
          "Deja un campo vacío si no debe limitar la plantilla.",
          "Usa los identificadores técnicos de pago y envío.",
          "Las condiciones se comprueban en la interfaz y de nuevo en el servidor."
        ]
      },
      {
        "title": "21. Registro del procesamiento del correo",
        "paragraphs": [
          "Para las notas de cliente, el plugin registra cuando WooCommerce informa que el correo se ha procesado y también los errores técnicos de wp_mail. «Procesado» confirma la entrega al sistema de correo, no la recepción final ni la lectura."
        ],
        "items": [
          "Consulta Historial para ver eventos procesados y fallidos.",
          "Usa un proveedor SMTP para obtener información de entrega más precisa.",
          "Las notas internas no generan correos de nota al cliente."
        ]
      },
      {
        "title": "22. Historial central",
        "paragraphs": [
          "Abre <strong>Mailhilfe Order Notes → Historial</strong> para revisar notas creadas, uso de plantillas, procesamiento y fallos de correo. Cuando están disponibles se muestran pedido, plantilla, usuario, destinatario, tipo de evento y fecha."
        ],
        "items": [
          "Úsalo para soporte, auditoría y diagnóstico.",
          "El historial es independiente de las notas de pedido de WooCommerce.",
          "Se muestran las 250 entradas más recientes."
        ]
      },
      {
        "title": "23. Vista previa con pedido de prueba",
        "paragraphs": [
          "En el editor introduce el ID de un pedido de WooCommerce. El contenido actual, incluso sin guardar, se previsualiza con los datos de ese pedido sin crear una nota ni enviar un correo."
        ],
        "items": [
          "Usa un pedido de prueba o un sitio de staging.",
          "Comprueba valores vacíos, formato, condiciones y metadatos personalizados.",
          "Debes tener permiso para editar el pedido seleccionado."
        ]
      },
      {
        "title": "24. Favoritos personales y plantillas recientes",
        "paragraphs": [
          "Cada usuario puede marcar favoritos personales en el pedido. El plugin también guarda las diez últimas plantillas usadas correctamente por cada usuario y las coloca más arriba. Los favoritos globales siguen siendo compartidos."
        ],
        "items": [
          "Los favoritos personales no afectan a otros usuarios.",
          "La lista reciente solo se actualiza después de añadir una nota correctamente.",
          "Los datos se guardan como metadatos de usuario de WordPress."
        ]
      },
      {
        "title": "25. Página de diagnóstico",
        "paragraphs": [
          "Abre <strong>Mailhilfe Order Notes → Diagnóstico</strong> para ver versiones de WordPress, PHP y WooCommerce, estado de HPOS, correo de nota al cliente, idioma, número de plantillas, caché y WP_DEBUG."
        ],
        "items": [
          "Incluye estos datos al solicitar soporte.",
          "No se muestran contenidos de notas ni direcciones de clientes.",
          "Los desarrolladores pueden ampliar las filas mediante un filtro."
        ]
      },
      {
        "title": "26. Acciones y filtros para desarrolladores",
        "paragraphs": [
          "El plugin ofrece acciones y filtros para marcadores, valores, claves meta permitidas, resultados, condiciones, vista previa, contenido final, acciones antes y después de añadir, historial y diagnóstico. Los nombres están documentados en readme.txt."
        ],
        "items": [
          "Valida, sanea y escapa todos los datos personalizados.",
          "Usa las API de pedidos de WooCommerce y no las tablas directamente.",
          "Mantén la compatibilidad con HPOS y el almacenamiento clásico."
        ]
      }
    ]
  },
  "it_IT": {
    "title": "Guida dettagliata per Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Cosa fa il plugin",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Creare un modello",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Formattare il testo",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Usare i segnaposto",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Controllare l’anteprima",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Note interne e note cliente",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Preferiti, ricerca e ordinamento",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Importazione, esportazione e modelli demo",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Ruoli e permessi",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Sicurezza e compatibilità HPOS",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Risoluzione dei problemi",
        "paragraphs": [
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza.",
          "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Pagina impostazioni",
        "paragraphs": [
          "Apri <strong>Mailhilfe Order Notes → Impostazioni</strong> per scegliere il tipo di nota predefinito, l’HTML sicuro, la visualizzazione dell’utilizzo, i preferiti, l’importazione JSON e la corrispondenza della lingua. Usa le note interne come impostazione predefinita."
        ],
        "items": []
      },
      {
        "title": "13. Lingua del modello e negozi multilingue",
        "paragraphs": [
          "Ogni modello può avere una lingua. Scegli <strong>Tutte le lingue</strong> per testi generali o una lingua specifica per messaggi cliente tradotti.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Campi personalizzati e metadati",
        "paragraphs": [
          "I segnaposto <code>{order_meta:meta_key}</code> e <code>{customer_meta:meta_key}</code> inseriscono metadati selezionati. Chiavi sensibili come password, token o secret sono bloccate.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Duplicazione e revisioni",
        "paragraphs": [
          "La duplicazione crea una copia in bozza. Le revisioni di WordPress aiutano a confrontare e ripristinare versioni precedenti.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Permessi",
        "paragraphs": [
          "La pagina <strong>Permessi</strong> stabilisce quali ruoli gestiscono i modelli e quali li usano negli ordini.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Anteprima importazione",
        "paragraphs": [
          "L’import JSON mostra un’anteprima dei modelli creati, aggiornati o saltati prima di applicare le modifiche.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Stato email nota cliente",
        "paragraphs": [
          "Le note cliente possono attivare notifiche e-mail di WooCommerce quando l’e-mail corrispondente è abilitata. Il plugin registra la creazione della nota separatamente dall’elaborazione dell’e-mail. Controlla l’anteprima modificabile prima di aggiungerla e usa la pagina Cronologia per verificare il risultato."
        ],
        "items": []
      },
      {
        "title": "19. Flusso consigliato",
        "paragraphs": [
          "Seleziona un modello, controlla l’anteprima, modificala se necessario, verifica il tipo di nota e aggiungi la nota.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Condizioni dei modelli",
        "paragraphs": [
          "Le condizioni stabiliscono se un modello è disponibile per un ordine. È possibile limitarlo per stato dell’ordine, metodo di pagamento, metodo di spedizione, paese di fatturazione e totale minimo o massimo. Tutte le condizioni impostate devono corrispondere."
        ],
        "items": [
          "Lascia un campo vuoto se non deve limitare il modello.",
          "Usa gli ID tecnici dei metodi di pagamento e spedizione.",
          "Le condizioni vengono controllate nell’interfaccia e di nuovo sul server."
        ]
      },
      {
        "title": "21. Registro dell’elaborazione e-mail",
        "paragraphs": [
          "Per le note cliente il plugin registra quando WooCommerce segnala l’e-mail come elaborata e gli errori tecnici di wp_mail. «Elaborata» indica il passaggio al sistema di posta, non la consegna finale o la lettura."
        ],
        "items": [
          "Controlla la pagina Cronologia per gli eventi elaborati o non riusciti.",
          "Usa un servizio SMTP per informazioni di consegna più precise.",
          "Le note interne non attivano l’e-mail della nota cliente."
        ]
      },
      {
        "title": "22. Cronologia centrale",
        "paragraphs": [
          "Apri <strong>Mailhilfe Order Notes → Cronologia</strong> per vedere note create, utilizzo dei modelli, elaborazione e errori e-mail. Quando disponibili vengono mostrati ordine, modello, utente, destinatario, tipo di evento e ora."
        ],
        "items": [
          "Usa la cronologia per assistenza, controllo e risoluzione dei problemi.",
          "È separata dalle note ordine di WooCommerce.",
          "Sono visualizzate le 250 voci più recenti."
        ]
      },
      {
        "title": "23. Anteprima con ordine di prova",
        "paragraphs": [
          "Nell’editor inserisci l’ID di un ordine WooCommerce. Il contenuto corrente, anche non salvato, viene mostrato con i dati dell’ordine senza creare note o inviare e-mail."
        ],
        "items": [
          "Usa un ordine di prova o un sito di staging.",
          "Controlla valori mancanti, formattazione, condizioni e metadati personalizzati.",
          "Devi poter modificare l’ordine selezionato."
        ]
      },
      {
        "title": "24. Preferiti personali e modelli recenti",
        "paragraphs": [
          "Ogni utente può impostare preferiti personali nella schermata ordine. Il plugin memorizza anche gli ultimi dieci modelli usati con successo e li mostra più in alto. I preferiti globali restano condivisi."
        ],
        "items": [
          "I preferiti personali non modificano l’elenco degli altri utenti.",
          "L’elenco recente si aggiorna solo dopo l’aggiunta riuscita di una nota.",
          "I dati sono salvati come metadati utente WordPress."
        ]
      },
      {
        "title": "25. Pagina diagnostica",
        "paragraphs": [
          "Apri <strong>Mailhilfe Order Notes → Diagnostica</strong> per vedere versioni WordPress, PHP e WooCommerce, stato HPOS, e-mail nota cliente, lingua, numero di modelli pubblicati, cache e WP_DEBUG."
        ],
        "items": [
          "Fornisci questi dati nelle richieste di assistenza.",
          "Non vengono mostrati contenuti delle note o indirizzi dei clienti.",
          "Gli sviluppatori possono aggiungere righe con il filtro diagnostico."
        ]
      },
      {
        "title": "26. Hook e filtri per sviluppatori",
        "paragraphs": [
          "Sono disponibili hook e filtri per segnaposto, valori, chiavi meta consentite, risultati dei modelli, condizioni, anteprima, contenuto finale, azioni prima/dopo l’aggiunta, cronologia e diagnostica. I nomi sono documentati in readme.txt."
        ],
        "items": [
          "Valida, sanifica ed esegui l’escape dei dati personalizzati.",
          "Usa le API ordini WooCommerce invece delle tabelle dirette.",
          "Mantieni la compatibilità con HPOS e archiviazione classica."
        ]
      }
    ]
  },
  "pt_BR": {
    "title": "Ajuda detalhada do Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. O que o plugin faz",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Criar um modelo",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Formatar o texto",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Usar espaços reservados",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Verificar a prévia",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Notas internas e notas do cliente",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Favoritos, busca e ordenação",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Importação, exportação e modelos de demonstração",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Funções e permissões",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Segurança e compatibilidade HPOS",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Solução de problemas",
        "paragraphs": [
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança.",
          "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Página de configurações",
        "paragraphs": [
          "Abra <strong>Mailhilfe Order Notes → Configurações</strong> para escolher o tipo de nota padrão, HTML seguro, exibição de uso, favoritos, importação JSON e correspondência de idioma. Use notas internas como padrão no trabalho diário."
        ],
        "items": []
      },
      {
        "title": "13. Idioma do modelo e lojas multilíngues",
        "paragraphs": [
          "Cada modelo pode ter um idioma. Escolha <strong>Todos os idiomas</strong> para textos gerais ou um idioma específico para mensagens traduzidas ao cliente.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Campos personalizados e metadados",
        "paragraphs": [
          "Os placeholders <code>{order_meta:meta_key}</code> e <code>{customer_meta:meta_key}</code> inserem metadados selecionados. Chaves sensíveis como password, token ou secret são bloqueadas.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Duplicar e revisões",
        "paragraphs": [
          "A ação de duplicar cria uma cópia como rascunho. As revisões do WordPress ajudam a comparar e restaurar versões anteriores.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Permissões",
        "paragraphs": [
          "A página <strong>Permissões</strong> define quais funções gerenciam modelos e quais podem usá-los nos pedidos.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Prévia de importação",
        "paragraphs": [
          "A importação JSON mostra uma prévia com modelos criados, atualizados ou ignorados antes da aplicação.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Status do e-mail de nota ao cliente",
        "paragraphs": [
          "As notas ao cliente podem acionar notificações por e-mail do WooCommerce quando o e-mail correspondente está ativado. O plugin registra a criação da nota separadamente do processamento do e-mail. Revise a prévia editável antes de adicionar a nota e consulte a página Histórico para conferir o resultado."
        ],
        "items": []
      },
      {
        "title": "19. Fluxo recomendado",
        "paragraphs": [
          "Selecione um modelo, revise a prévia, edite se necessário, confirme o tipo de nota e adicione a nota.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Condições dos modelos",
        "paragraphs": [
          "As condições definem se um modelo fica disponível para um pedido. É possível restringir por status, forma de pagamento, método de envio, país de cobrança e total mínimo ou máximo. Todas as condições preenchidas devem corresponder."
        ],
        "items": [
          "Deixe um campo vazio quando ele não deve restringir o modelo.",
          "Use os IDs técnicos das formas de pagamento e envio.",
          "As condições são verificadas na interface e novamente no servidor."
        ]
      },
      {
        "title": "21. Registro do processamento de e-mail",
        "paragraphs": [
          "Para notas do cliente, o plugin registra quando o WooCommerce informa que o e-mail foi processado e também erros técnicos do wp_mail. “Processado” confirma o envio ao sistema de e-mail, não a entrega final nem a leitura."
        ],
        "items": [
          "Consulte Histórico para eventos processados e com falha.",
          "Use um provedor SMTP para obter informações de entrega mais precisas.",
          "Notas internas não disparam o e-mail de nota do cliente."
        ]
      },
      {
        "title": "22. Histórico central",
        "paragraphs": [
          "Abra <strong>Mailhilfe Order Notes → Histórico</strong> para revisar notas criadas, uso de modelos, processamento e falhas de e-mail. Quando disponíveis são exibidos pedido, modelo, usuário, destinatário, tipo de evento e horário."
        ],
        "items": [
          "Use o histórico para suporte, auditoria e solução de problemas.",
          "Ele é separado das notas de pedido do WooCommerce.",
          "São exibidas as 250 entradas mais recentes."
        ]
      },
      {
        "title": "23. Prévia com pedido de teste",
        "paragraphs": [
          "No editor, informe o ID de um pedido WooCommerce. O conteúdo atual, inclusive alterações ainda não salvas, é exibido com os dados do pedido sem criar nota nem enviar e-mail."
        ],
        "items": [
          "Use um pedido de teste ou um ambiente de staging.",
          "Verifique valores ausentes, formatação, condições e metadados personalizados.",
          "Você precisa ter permissão para editar o pedido escolhido."
        ]
      },
      {
        "title": "24. Favoritos pessoais e modelos recentes",
        "paragraphs": [
          "Cada usuário pode marcar favoritos pessoais na tela do pedido. O plugin também guarda os dez últimos modelos usados com sucesso por usuário e os posiciona mais acima. Favoritos globais continuam compartilhados."
        ],
        "items": [
          "Favoritos pessoais não alteram a lista de outros usuários.",
          "A lista recente só é atualizada após uma nota ser adicionada com sucesso.",
          "Os dados são armazenados como metadados de usuário do WordPress."
        ]
      },
      {
        "title": "25. Página de diagnóstico",
        "paragraphs": [
          "Abra <strong>Mailhilfe Order Notes → Diagnóstico</strong> para ver versões do WordPress, PHP e WooCommerce, status do HPOS, e-mail de nota do cliente, idioma, quantidade de modelos, cache e WP_DEBUG."
        ],
        "items": [
          "Inclua esses dados ao solicitar suporte.",
          "A página não mostra conteúdo de notas nem endereços de clientes.",
          "Desenvolvedores podem adicionar linhas por meio do filtro de diagnóstico."
        ]
      },
      {
        "title": "26. Hooks e filtros para desenvolvedores",
        "paragraphs": [
          "O plugin oferece hooks e filtros para placeholders, valores, chaves meta permitidas, resultados, condições, prévia, conteúdo final, ações antes/depois da inclusão, histórico e diagnóstico. Os nomes estão documentados no readme.txt."
        ],
        "items": [
          "Valide, higienize e escape todos os dados personalizados.",
          "Use as APIs de pedidos do WooCommerce em vez de tabelas diretas.",
          "Mantenha compatibilidade com HPOS e armazenamento clássico."
        ]
      }
    ]
  },
  "nl_NL": {
    "title": "Uitgebreide hulp voor Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Wat de plugin doet",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Een sjabloon maken",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Tekst opmaken",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Placeholders gebruiken",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Voorbeeld controleren",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Interne notities en klantnotities",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Favorieten, zoeken en sorteren",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Import, export en demosjablonen",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Rollen en rechten",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Beveiliging en HPOS-compatibiliteit",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Problemen oplossen",
        "paragraphs": [
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken.",
          "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Instellingenpagina",
        "paragraphs": [
          "Open <strong>Mailhilfe Order Notes → Instellingen</strong> om het standaardnotitietype, veilige HTML, gebruiksweergave, favorieten, JSON-import en taalkoppeling te kiezen. Gebruik interne notities als standaard voor dagelijks werk."
        ],
        "items": []
      },
      {
        "title": "13. Sjabloontaal en meertalige winkels",
        "paragraphs": [
          "Elke sjabloon kan een taal krijgen. Kies <strong>Alle talen</strong> voor algemene teksten of een specifieke taal voor vertaalde klantberichten.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Aangepaste velden en metadata",
        "paragraphs": [
          "De placeholders <code>{order_meta:meta_key}</code> en <code>{customer_meta:meta_key}</code> voegen geselecteerde metadata in. Gevoelige sleutels zoals password, token of secret worden geblokkeerd.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Dupliceren en revisies",
        "paragraphs": [
          "Dupliceren maakt een kopie als concept. WordPress-revisies helpen eerdere versies te vergelijken en te herstellen.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Rechten",
        "paragraphs": [
          "Op de pagina <strong>Rechten</strong> bepaal je welke rollen sjablonen beheren en welke ze in bestellingen gebruiken.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Importvoorbeeld",
        "paragraphs": [
          "JSON-import toont eerst een voorbeeld met gemaakte, bijgewerkte of overgeslagen sjablonen.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. E-mailstatus klantnotitie",
        "paragraphs": [
          "Klantnotities kunnen WooCommerce-e-mailmeldingen activeren wanneer de bijbehorende e-mail is ingeschakeld. De plugin registreert het aanmaken van de klantnotitie apart van de e-mailverwerking. Controleer de bewerkbare voorbeeldweergave en bekijk het resultaat op de pagina Geschiedenis."
        ],
        "items": []
      },
      {
        "title": "19. Aanbevolen werkwijze",
        "paragraphs": [
          "Kies een sjabloon, controleer de vervangen preview, bewerk indien nodig, controleer het notitietype en voeg de notitie toe.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Voorwaarden voor sjablonen",
        "paragraphs": [
          "Voorwaarden bepalen of een sjabloon voor een bestelling beschikbaar is. Je kunt beperken op bestelstatus, betaalmethode, verzendmethode, factuurland en minimum- of maximumbedrag. Alle ingevulde voorwaarden moeten overeenkomen."
        ],
        "items": [
          "Laat een veld leeg als het sjabloon daarop niet beperkt moet worden.",
          "Gebruik de technische ID’s van betaal- en verzendmethoden.",
          "Voorwaarden worden in de interface en opnieuw op de server gecontroleerd."
        ]
      },
      {
        "title": "21. Logboek voor e-mailverwerking",
        "paragraphs": [
          "Bij klantnotities registreert de plugin wanneer WooCommerce meldt dat de e-mail is verwerkt en ook technische wp_mail-fouten. “Verwerkt” bevestigt overdracht aan het mailsysteem, niet de uiteindelijke bezorging of het lezen."
        ],
        "items": [
          "Bekijk de pagina Geschiedenis voor verwerkte en mislukte gebeurtenissen.",
          "Gebruik een SMTP-provider voor nauwkeurigere bezorginformatie.",
          "Interne notities activeren geen e-mail voor klantnotities."
        ]
      },
      {
        "title": "22. Centrale geschiedenis",
        "paragraphs": [
          "Open <strong>Mailhilfe Order Notes → Geschiedenis</strong> voor gemaakte notities, sjabloongebruik, e-mailverwerking en fouten. Indien beschikbaar worden bestelling, sjabloon, gebruiker, ontvanger, gebeurtenistype en tijd weergegeven."
        ],
        "items": [
          "Gebruik de geschiedenis voor ondersteuning, controle en probleemoplossing.",
          "Deze staat los van WooCommerce-bestelnotities.",
          "De 250 nieuwste vermeldingen worden getoond."
        ]
      },
      {
        "title": "23. Voorbeeld met testbestelling",
        "paragraphs": [
          "Voer in de sjablooneditor een WooCommerce-bestel-ID in. De huidige inhoud, ook niet-opgeslagen wijzigingen, wordt met bestelgegevens weergegeven zonder een notitie te maken of e-mail te sturen."
        ],
        "items": [
          "Gebruik een testbestelling of stagingomgeving.",
          "Controleer ontbrekende waarden, opmaak, voorwaarden en aangepaste metadata.",
          "Je moet de gekozen bestelling mogen bewerken."
        ]
      },
      {
        "title": "24. Persoonlijke favorieten en recente sjablonen",
        "paragraphs": [
          "Elke gebruiker kan persoonlijke favorieten markeren in de bestelling. De plugin bewaart ook de tien laatst succesvol gebruikte sjablonen per gebruiker en zet ze hoger. Globale favorieten blijven gedeeld."
        ],
        "items": [
          "Persoonlijke favorieten beïnvloeden andere gebruikers niet.",
          "De recente lijst wordt alleen bijgewerkt na een succesvol toegevoegde notitie.",
          "De gegevens worden als WordPress-gebruikersmeta opgeslagen."
        ]
      },
      {
        "title": "25. Diagnosepagina",
        "paragraphs": [
          "Open <strong>Mailhilfe Order Notes → Diagnose</strong> voor WordPress-, PHP- en WooCommerce-versies, HPOS-status, klantnotitie-e-mail, taal, aantal gepubliceerde sjablonen, cache en WP_DEBUG."
        ],
        "items": [
          "Vermeld deze gegevens bij een supportvraag.",
          "Er worden geen notitie-inhouden of klantadressen getoond.",
          "Ontwikkelaars kunnen rijen toevoegen via het diagnosefilter."
        ]
      },
      {
        "title": "26. Hooks en filters voor ontwikkelaars",
        "paragraphs": [
          "Er zijn hooks en filters voor placeholders, waarden, toegestane metasleutels, sjabloonresultaten, voorwaarden, voorbeeld, definitieve inhoud, acties vóór/na toevoegen, geschiedenis en diagnose. Namen staan in readme.txt."
        ],
        "items": [
          "Valideer, sanitize en escape alle aangepaste gegevens.",
          "Gebruik WooCommerce-order-API’s in plaats van directe tabellen.",
          "Behoud compatibiliteit met HPOS en klassieke opslag."
        ]
      }
    ]
  },
  "pl_PL": {
    "title": "Szczegółowa pomoc Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Do czego służy wtyczka",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Tworzenie szablonu",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Formatowanie tekstu",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Używanie symboli zastępczych",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Sprawdzenie podglądu",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Notatki wewnętrzne i dla klienta",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Ulubione, wyszukiwanie i sortowanie",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Import, eksport i szablony demo",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Role i uprawnienia",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Bezpieczeństwo i zgodność HPOS",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Rozwiązywanie problemów",
        "paragraphs": [
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę.",
          "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Strona ustawień",
        "paragraphs": [
          "Otwórz <strong>Mailhilfe Order Notes → Ustawienia</strong>, aby wybrać domyślny typ notatki, bezpieczny HTML, wyświetlanie użycia, ulubione, import JSON i dopasowanie języka. W codziennej pracy używaj domyślnie notatek wewnętrznych."
        ],
        "items": []
      },
      {
        "title": "13. Język szablonu i sklepy wielojęzyczne",
        "paragraphs": [
          "Każdy szablon może mieć język. Wybierz <strong>Wszystkie języki</strong> dla tekstów ogólnych albo konkretny język dla przetłumaczonych wiadomości do klienta.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Pola własne i metadane",
        "paragraphs": [
          "Symbole <code>{order_meta:meta_key}</code> i <code>{customer_meta:meta_key}</code> wstawiają wybrane metadane. Wrażliwe klucze, takie jak password, token lub secret, są blokowane.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Duplikowanie i wersje",
        "paragraphs": [
          "Duplikowanie tworzy kopię jako szkic. Wersje WordPress pomagają porównać i przywrócić wcześniejsze treści.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Uprawnienia",
        "paragraphs": [
          "Strona <strong>Uprawnienia</strong> określa, które role zarządzają szablonami, a które używają ich w zamówieniach.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Podgląd importu",
        "paragraphs": [
          "Import JSON pokazuje podgląd szablonów tworzonych, aktualizowanych lub pomijanych przed zastosowaniem zmian.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Status e-maila notatki klienta",
        "paragraphs": [
          "Notatki dla klienta mogą uruchamiać powiadomienia e-mail WooCommerce, jeśli odpowiednia wiadomość jest włączona. Wtyczka zapisuje utworzenie notatki oddzielnie od przetwarzania e-maila. Przed dodaniem sprawdź edytowalny podgląd, a wynik przetwarzania na stronie Historia."
        ],
        "items": []
      },
      {
        "title": "19. Zalecany przebieg pracy",
        "paragraphs": [
          "Wybierz szablon, sprawdź podgląd, edytuj w razie potrzeby, potwierdź typ notatki i dodaj notatkę.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Warunki szablonów",
        "paragraphs": [
          "Warunki określają, czy szablon jest dostępny dla zamówienia. Można ograniczyć go według statusu, metody płatności, metody wysyłki, kraju rozliczeniowego oraz minimalnej lub maksymalnej wartości. Wszystkie ustawione warunki muszą być spełnione."
        ],
        "items": [
          "Pozostaw pole puste, jeśli nie ma ograniczać szablonu.",
          "Używaj technicznych identyfikatorów metod płatności i wysyłki.",
          "Warunki są sprawdzane w interfejsie i ponownie po stronie serwera."
        ]
      },
      {
        "title": "21. Rejestr przetwarzania e-maili",
        "paragraphs": [
          "Dla notatek klienta wtyczka zapisuje, kiedy WooCommerce zgłasza przetworzenie wiadomości, oraz błędy techniczne wp_mail. „Przetworzono” oznacza przekazanie do systemu pocztowego, a nie ostateczne doręczenie lub odczytanie."
        ],
        "items": [
          "Sprawdź stronę Historia, aby zobaczyć zdarzenia udane i nieudane.",
          "Dokładniejsze dane o doręczeniu wymagają dostawcy SMTP.",
          "Notatki wewnętrzne nie uruchamiają e-maila notatki klienta."
        ]
      },
      {
        "title": "22. Centralna historia",
        "paragraphs": [
          "Otwórz <strong>Mailhilfe Order Notes → Historia</strong>, aby przeglądać utworzone notatki, użycie szablonów, przetwarzanie i błędy e-mail. Gdy są dostępne, widoczne są zamówienie, szablon, użytkownik, odbiorca, typ zdarzenia i czas."
        ],
        "items": [
          "Historia pomaga w obsłudze, audycie i diagnostyce.",
          "Jest oddzielona od notatek zamówienia WooCommerce.",
          "Wyświetlane jest 250 najnowszych wpisów."
        ]
      },
      {
        "title": "23. Podgląd z zamówieniem testowym",
        "paragraphs": [
          "W edytorze wpisz identyfikator zamówienia WooCommerce. Bieżąca treść, także niezapisana, zostanie wyświetlona z danymi zamówienia bez tworzenia notatki i wysyłania e-maila."
        ],
        "items": [
          "Używaj zamówienia testowego lub środowiska staging.",
          "Sprawdź brakujące wartości, formatowanie, warunki i własne metadane.",
          "Musisz mieć prawo edycji wybranego zamówienia."
        ]
      },
      {
        "title": "24. Osobiste ulubione i ostatnio używane szablony",
        "paragraphs": [
          "Każdy użytkownik może oznaczać osobiste ulubione w zamówieniu. Wtyczka zapisuje również dziesięć ostatnio poprawnie użytych szablonów i umieszcza je wyżej. Ulubione globalne pozostają wspólne."
        ],
        "items": [
          "Osobiste ulubione nie wpływają na innych użytkowników.",
          "Lista ostatnich aktualizuje się dopiero po pomyślnym dodaniu notatki.",
          "Dane są przechowywane jako metadane użytkownika WordPress."
        ]
      },
      {
        "title": "25. Strona diagnostyczna",
        "paragraphs": [
          "Otwórz <strong>Mailhilfe Order Notes → Diagnostyka</strong>, aby zobaczyć wersje WordPress, PHP i WooCommerce, status HPOS, e-mail notatki klienta, język, liczbę szablonów, pamięć podręczną i WP_DEBUG."
        ],
        "items": [
          "Dołącz te dane do zgłoszenia pomocy.",
          "Strona nie pokazuje treści notatek ani adresów klientów.",
          "Programiści mogą dodawać wiersze filtrem diagnostycznym."
        ]
      },
      {
        "title": "26. Hooki i filtry dla programistów",
        "paragraphs": [
          "Dostępne są hooki i filtry dla symboli zastępczych, wartości, dozwolonych kluczy meta, wyników szablonów, warunków, podglądu, treści końcowej, działań przed/po dodaniu, historii i diagnostyki. Nazwy opisano w readme.txt."
        ],
        "items": [
          "Waliduj, oczyszczaj i escapuj własne dane.",
          "Używaj API zamówień WooCommerce zamiast bezpośrednich tabel.",
          "Zachowaj zgodność z HPOS i klasycznym magazynem."
        ]
      }
    ]
  },
  "ru_RU": {
    "title": "Подробная справка Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Что делает плагин",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Создание шаблона",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Форматирование текста",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Использование заполнителей",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Проверка предварительного просмотра",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Внутренние и клиентские заметки",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Избранное, поиск и сортировка",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Импорт, экспорт и демо-шаблоны",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Роли и права",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Безопасность и совместимость HPOS",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Устранение неполадок",
        "paragraphs": [
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу.",
          "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Страница настроек",
        "paragraphs": [
          "Откройте <strong>Mailhilfe Order Notes → Настройки</strong>, чтобы выбрать тип заметки по умолчанию, безопасный HTML, отображение использования, избранное, импорт JSON и сопоставление языков. Для повседневной работы используйте внутренние заметки."
        ],
        "items": []
      },
      {
        "title": "13. Язык шаблона и многоязычные магазины",
        "paragraphs": [
          "Каждому шаблону можно назначить язык. Выберите <strong>Все языки</strong> для универсального текста или конкретный язык для переведенных сообщений клиенту.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Пользовательские поля и метаданные",
        "paragraphs": [
          "Заполнители <code>{order_meta:meta_key}</code> и <code>{customer_meta:meta_key}</code> вставляют выбранные метаданные. Чувствительные ключи, такие как password, token или secret, блокируются.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Дублирование и редакции",
        "paragraphs": [
          "Дублирование создает копию как черновик. Редакции WordPress помогают сравнивать и восстанавливать предыдущие версии.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Разрешения",
        "paragraphs": [
          "Страница <strong>Разрешения</strong> определяет, какие роли управляют шаблонами и какие используют их в заказах.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Предпросмотр импорта",
        "paragraphs": [
          "Импорт JSON сначала показывает, какие шаблоны будут созданы, обновлены или пропущены.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Статус письма заметки клиенту",
        "paragraphs": [
          "Заметки клиенту могут запускать уведомления WooCommerce по электронной почте, если соответствующее письмо включено. Плагин отдельно фиксирует создание заметки и обработку письма. Перед добавлением проверьте редактируемый предпросмотр, а результат обработки — на странице истории."
        ],
        "items": []
      },
      {
        "title": "19. Рекомендуемый порядок",
        "paragraphs": [
          "Выберите шаблон, проверьте предпросмотр, отредактируйте при необходимости, подтвердите тип заметки и добавьте ее.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Условия шаблонов",
        "paragraphs": [
          "Условия определяют, доступен ли шаблон для конкретного заказа. Ограничения можно задать по статусу, способу оплаты, способу доставки, стране выставления счёта и минимальной или максимальной сумме. Все заполненные условия должны совпасть."
        ],
        "items": [
          "Оставьте поле пустым, если оно не должно ограничивать шаблон.",
          "Используйте технические идентификаторы способов оплаты и доставки.",
          "Условия проверяются в интерфейсе и повторно на сервере."
        ]
      },
      {
        "title": "21. Журнал обработки электронной почты",
        "paragraphs": [
          "Для клиентских заметок плагин записывает сообщение WooCommerce об обработке письма и технические ошибки wp_mail. Статус «обработано» подтверждает передачу почтовой системе, но не окончательную доставку и не прочтение."
        ],
        "items": [
          "Смотрите обработанные и неудачные события на странице истории.",
          "Для точных данных о доставке используйте SMTP-сервис.",
          "Внутренние заметки не запускают письмо клиентской заметки."
        ]
      },
      {
        "title": "22. Центральная история",
        "paragraphs": [
          "Откройте <strong>Mailhilfe Order Notes → История</strong>, чтобы увидеть создание заметок, использование шаблонов, обработку и ошибки писем. При наличии отображаются заказ, шаблон, пользователь, получатель, тип события и время."
        ],
        "items": [
          "Используйте историю для поддержки, аудита и диагностики.",
          "Она отделена от заметок заказа WooCommerce.",
          "Показываются последние 250 записей."
        ]
      },
      {
        "title": "23. Предпросмотр с тестовым заказом",
        "paragraphs": [
          "В редакторе укажите ID заказа WooCommerce. Текущий текст, включая несохранённые изменения, будет показан с данными заказа без создания заметки и отправки письма."
        ],
        "items": [
          "Используйте тестовый заказ или staging-сайт.",
          "Проверьте отсутствующие значения, форматирование, условия и пользовательские метаданные.",
          "Нужно иметь право редактировать выбранный заказ."
        ]
      },
      {
        "title": "24. Личное избранное и недавние шаблоны",
        "paragraphs": [
          "Каждый пользователь может отметить личные избранные шаблоны в заказе. Плагин также хранит десять последних успешно использованных шаблонов и поднимает их выше. Глобальное избранное остаётся общим."
        ],
        "items": [
          "Личное избранное не влияет на других пользователей.",
          "Недавний список обновляется только после успешного добавления заметки.",
          "Данные хранятся в метаданных пользователя WordPress."
        ]
      },
      {
        "title": "25. Страница диагностики",
        "paragraphs": [
          "Откройте <strong>Mailhilfe Order Notes → Диагностика</strong>, чтобы увидеть версии WordPress, PHP и WooCommerce, статус HPOS, состояние письма клиентской заметки, язык, число шаблонов, кэш и WP_DEBUG."
        ],
        "items": [
          "Указывайте эти данные при обращении в поддержку.",
          "Содержимое заметок и адреса клиентов не показываются.",
          "Разработчики могут добавлять строки фильтром диагностики."
        ]
      },
      {
        "title": "26. Хуки и фильтры для разработчиков",
        "paragraphs": [
          "Плагин предоставляет хуки и фильтры для заполнителей, значений, разрешённых мета-ключей, результатов шаблонов, условий, предпросмотра, итогового текста, действий до/после добавления, истории и диагностики. Имена описаны в readme.txt."
        ],
        "items": [
          "Проверяйте, очищайте и экранируйте пользовательские данные.",
          "Используйте API заказов WooCommerce, а не прямой доступ к таблицам.",
          "Сохраняйте совместимость с HPOS и классическим хранением."
        ]
      }
    ]
  },
  "zh_CN": {
    "title": "Mailhilfe Order Note Manager for WooCommerce 详细帮助",
    "intro": "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。 Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. 插件作用",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. 创建模板",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. 设置文本格式",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. 使用占位符",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. 检查预览",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. 内部备注和客户备注",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. 收藏、搜索和排序",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. 导入、导出和演示模板",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. 角色和权限",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. 安全性和 HPOS 兼容性",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. 故障排除",
        "paragraphs": [
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。",
          "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. 设置页面",
        "paragraphs": [
          "打开 <strong>Mailhilfe Order Notes → 设置</strong>，可配置默认备注类型、安全 HTML、使用次数显示、收藏、JSON 导入和语言匹配。日常操作建议默认使用内部备注。"
        ],
        "items": []
      },
      {
        "title": "13. 模板语言和多语言商店",
        "paragraphs": [
          "每个模板都可以设置语言。选择 <strong>所有语言</strong> 表示通用文本，或选择具体语言用于已翻译的客户消息。",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. 自定义字段和元数据",
        "paragraphs": [
          "占位符 <code>{order_meta:meta_key}</code> 和 <code>{customer_meta:meta_key}</code> 可插入选定的元数据。password、token、secret 等敏感键会被阻止。",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. 复制和修订",
        "paragraphs": [
          "复制操作会创建草稿副本。WordPress 修订可帮助比较并恢复旧版本。",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. 权限",
        "paragraphs": [
          "<strong>权限</strong> 页面用于设置哪些角色可以管理模板，哪些角色可以在订单中使用模板。",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. 导入预览",
        "paragraphs": [
          "JSON 导入会先显示将创建、更新或跳过的模板数量。",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. 客户备注邮件状态",
        "paragraphs": [
          "启用相应邮件后，客户备注可能触发 WooCommerce 邮件通知。插件会分别记录客户备注的创建和邮件处理结果。添加备注前请检查可编辑预览，并在“历史记录”页面查看邮件处理结果。"
        ],
        "items": []
      },
      {
        "title": "19. 推荐流程",
        "paragraphs": [
          "选择模板，检查替换后的预览，需要时编辑，确认备注类型，然后添加备注。",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. 模板条件",
        "paragraphs": [
          "模板条件决定某个模板是否可用于订单。可按订单状态、付款方式、配送方式、账单国家以及最低或最高订单金额进行限制。所有已设置的条件都必须匹配。"
        ],
        "items": [
          "不需要限制的条件请留空。",
          "付款和配送方式请使用技术 ID。",
          "条件会在界面中检查，并在创建备注前由服务器再次检查。"
        ]
      },
      {
        "title": "21. 邮件处理日志",
        "paragraphs": [
          "对于客户备注，插件会记录 WooCommerce 报告的邮件处理事件以及 wp_mail 技术错误。“已处理”只表示邮件已交给邮件系统，并不证明最终送达或客户已阅读。"
        ],
        "items": [
          "在“历史记录”页面查看已处理和失败事件。",
          "需要准确送达信息时请使用 SMTP 服务商。",
          "内部备注不会触发客户备注邮件。"
        ]
      },
      {
        "title": "22. 集中历史记录",
        "paragraphs": [
          "打开 <strong>Mailhilfe Order Notes → 历史记录</strong>，可查看备注创建、模板使用、邮件处理和邮件失败。可用时会显示订单、模板、用户、收件人、事件类型和时间。"
        ],
        "items": [
          "可用于支持、审计和故障排除。",
          "该历史记录与 WooCommerce 订单备注分开。",
          "页面显示最近 250 条记录。"
        ]
      },
      {
        "title": "23. 测试订单预览",
        "paragraphs": [
          "在模板编辑器中输入 WooCommerce 订单 ID。当前编辑内容（包括未保存的更改）会使用该订单数据生成预览，不会创建备注或发送邮件。"
        ],
        "items": [
          "请使用测试订单或预发布环境。",
          "检查缺失值、格式、条件和自定义元数据占位符。",
          "您必须有权编辑所选订单。"
        ]
      },
      {
        "title": "24. 个人收藏和最近使用的模板",
        "paragraphs": [
          "每位用户都可以在订单页面标记个人收藏。插件还会为每个用户保存最近成功使用的十个模板并优先显示。全局收藏仍对所有用户共享。"
        ],
        "items": [
          "个人收藏不会影响其他用户。",
          "只有成功添加备注后才会更新最近列表。",
          "数据存储为 WordPress 用户元数据。"
        ]
      },
      {
        "title": "25. 诊断页面",
        "paragraphs": [
          "打开 <strong>Mailhilfe Order Notes → 诊断</strong>，可查看 WordPress、PHP 和 WooCommerce 版本、HPOS 状态、客户备注邮件状态、语言、已发布模板数量、缓存和 WP_DEBUG。"
        ],
        "items": [
          "请求支持时请提供这些信息。",
          "页面不会显示备注内容或客户地址。",
          "开发者可通过诊断过滤器添加项目。"
        ]
      },
      {
        "title": "26. 开发者钩子和过滤器",
        "paragraphs": [
          "插件为占位符、值、允许的元键、模板结果、条件、预览、最终备注内容、添加前后操作、历史记录和诊断提供钩子与过滤器。名称和参数记录在 readme.txt 中。"
        ],
        "items": [
          "验证、清理并转义所有自定义数据。",
          "使用 WooCommerce 订单 API，不要直接访问订单表。",
          "保持对 HPOS 和经典订单存储的兼容性。"
        ]
      }
    ]
  },
  "ja": {
    "title": "Mailhilfe Order Note Manager for WooCommerce 詳細ヘルプ",
    "intro": "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。 Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. プラグインの役割",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. テンプレートを作成する",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. テキストを書式設定する",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. プレースホルダーを使う",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. プレビューを確認する",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. 内部メモと顧客メモ",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. お気に入り、検索、並べ替え",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. インポート、エクスポート、デモテンプレート",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. ロールと権限",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. セキュリティと HPOS 互換性",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. トラブルシューティング",
        "paragraphs": [
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。",
          "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. 設定ページ",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → 設定</strong> で、既定のメモ種類、安全な HTML、使用回数表示、お気に入り、JSON インポート、言語照合を設定できます。日常作業では内部メモを既定にしてください。"
        ],
        "items": []
      },
      {
        "title": "13. テンプレート言語と多言語ショップ",
        "paragraphs": [
          "各テンプレートに言語を設定できます。共通テキストは <strong>すべての言語</strong>、翻訳済みの顧客向け文面は特定の言語を選びます。",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. カスタムフィールドとメタデータ",
        "paragraphs": [
          "<code>{order_meta:meta_key}</code> と <code>{customer_meta:meta_key}</code> は選択したメタデータを挿入します。password、token、secret などの機密キーはブロックされます。",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. 複製とリビジョン",
        "paragraphs": [
          "複製は下書きコピーを作成します。WordPress のリビジョンで過去の内容を比較・復元できます。",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. 権限",
        "paragraphs": [
          "<strong>権限</strong> ページで、どの権限グループがテンプレートを管理または注文で使用できるかを設定します。",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. インポートプレビュー",
        "paragraphs": [
          "JSON インポートでは、適用前に作成・更新・スキップされるテンプレート数を確認できます。",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. 顧客メモメールの状態",
        "paragraphs": [
          "対応するメールが有効な場合、顧客メモによって WooCommerce のメール通知が送信されることがあります。プラグインは顧客メモの作成とメール処理を別々に記録します。追加前に編集可能なプレビューを確認し、履歴ページでメール処理結果を確認してください。"
        ],
        "items": []
      },
      {
        "title": "19. 推奨ワークフロー",
        "paragraphs": [
          "テンプレートを選択し、置換後のプレビューを確認し、必要に応じて編集し、メモ種類を確認してから追加します。",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. テンプレート条件",
        "paragraphs": [
          "条件はテンプレートを注文で利用できるか決定します。注文ステータス、支払方法、配送方法、請求先国、最小・最大注文金額で制限できます。設定した条件はすべて一致する必要があります。"
        ],
        "items": [
          "制限しない項目は空欄にします。",
          "支払方法と配送方法は技術 ID を使用します。",
          "条件は画面上と、メモ作成前のサーバー側で再確認されます。"
        ]
      },
      {
        "title": "21. メール処理ログ",
        "paragraphs": [
          "顧客メモでは、WooCommerce がメールを処理したイベントと wp_mail の技術的エラーを記録します。「処理済み」はメールシステムへの引き渡しを示すだけで、最終配信や既読を保証しません。"
        ],
        "items": [
          "履歴ページで処理済み・失敗イベントを確認します。",
          "正確な配信情報には SMTP サービスを利用してください。",
          "内部メモは顧客メモメールを送信しません。"
        ]
      },
      {
        "title": "22. 一元化された履歴",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → 履歴</strong>で、メモ作成、テンプレート利用、メール処理、メール失敗を確認できます。利用可能な場合は注文、テンプレート、ユーザー、受信者、イベント種別、時刻が表示されます。"
        ],
        "items": [
          "サポート、監査、トラブルシューティングに利用できます。",
          "WooCommerce の注文メモとは別に保存されます。",
          "最新 250 件を表示します。"
        ]
      },
      {
        "title": "23. テスト注文プレビュー",
        "paragraphs": [
          "テンプレートエディターに WooCommerce 注文 ID を入力すると、未保存の変更を含む現在の内容を注文データでプレビューできます。メモ作成やメール送信は行いません。"
        ],
        "items": [
          "テスト注文またはステージング環境を使用します。",
          "空の値、書式、条件、カスタムメタを確認します。",
          "選択した注文を編集する権限が必要です。"
        ]
      },
      {
        "title": "24. 個人のお気に入りと最近使用したテンプレート",
        "paragraphs": [
          "各ユーザーは注文画面で個人のお気に入りを設定できます。また、正常に使用した最新 10 件をユーザーごとに保存して上位に表示します。グローバルなお気に入りは全員で共有されます。"
        ],
        "items": [
          "個人のお気に入りは他のユーザーに影響しません。",
          "最近の一覧はメモの追加成功後に更新されます。",
          "データは WordPress のユーザーメタに保存されます。"
        ]
      },
      {
        "title": "25. 診断ページ",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → 診断</strong>で WordPress、PHP、WooCommerce のバージョン、HPOS、顧客メモメール、言語、テンプレート数、キャッシュ、WP_DEBUG を確認できます。"
        ],
        "items": [
          "サポート依頼時にこれらの値を提供してください。",
          "メモ内容や顧客住所は表示されません。",
          "開発者は診断フィルターで項目を追加できます。"
        ]
      },
      {
        "title": "26. 開発者向けフックとフィルター",
        "paragraphs": [
          "プレースホルダー、値、許可メタキー、テンプレート結果、条件、プレビュー、最終内容、追加前後、履歴、診断を拡張するフックとフィルターがあります。名前と引数は readme.txt に記載されています。"
        ],
        "items": [
          "独自データは検証、サニタイズ、エスケープしてください。",
          "注文テーブルへの直接アクセスではなく WooCommerce API を使用します。",
          "HPOS と従来の保存方式の両方に対応してください。"
        ]
      }
    ]
  },
  "ko_KR": {
    "title": "Mailhilfe Order Note Manager for WooCommerce 상세 도움말",
    "intro": "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. 플러그인의 기능",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. 템플릿 만들기",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. 텍스트 서식 지정",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. 자리표시자 사용",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. 미리보기 확인",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. 내부 메모와 고객 메모",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. 즐겨찾기, 검색 및 정렬",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. 가져오기, 내보내기 및 데모 템플릿",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. 역할과 권한",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. 보안 및 HPOS 호환성",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. 문제 해결",
        "paragraphs": [
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다.",
          "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. 설정 페이지",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → 설정</strong>에서 기본 메모 유형, 안전한 HTML, 사용 횟수 표시, 즐겨찾기, JSON 가져오기 및 언어 일치를 설정할 수 있습니다. 일상 작업에서는 내부 메모를 기본값으로 사용하세요."
        ],
        "items": []
      },
      {
        "title": "13. 템플릿 언어와 다국어 상점",
        "paragraphs": [
          "각 템플릿에 언어를 지정할 수 있습니다. 공통 문구는 <strong>모든 언어</strong>, 번역된 고객 메시지는 특정 언어를 선택합니다.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. 사용자 정의 필드와 메타데이터",
        "paragraphs": [
          "<code>{order_meta:meta_key}</code> 및 <code>{customer_meta:meta_key}</code>는 선택한 메타데이터를 삽입합니다. password, token, secret 같은 민감한 키는 차단됩니다.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. 복제와 리비전",
        "paragraphs": [
          "복제는 초안 복사본을 만듭니다. WordPress 리비전으로 이전 버전을 비교하고 복원할 수 있습니다.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. 권한",
        "paragraphs": [
          "<strong>권한</strong> 페이지에서 어떤 역할이 템플릿을 관리하거나 주문에서 사용할 수 있는지 정합니다.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. 가져오기 미리보기",
        "paragraphs": [
          "JSON 가져오기는 적용 전에 생성, 업데이트 또는 건너뛸 템플릿 수를 보여줍니다.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. 고객 메모 이메일 상태",
        "paragraphs": [
          "해당 이메일이 활성화되어 있으면 고객 메모가 WooCommerce 이메일 알림을 트리거할 수 있습니다. 플러그인은 고객 메모 생성과 이메일 처리 결과를 별도로 기록합니다. 메모를 추가하기 전에 편집 가능한 미리보기를 확인하고 기록 페이지에서 처리 결과를 확인하세요."
        ],
        "items": []
      },
      {
        "title": "19. 권장 절차",
        "paragraphs": [
          "템플릿을 선택하고 치환된 미리보기를 확인한 뒤 필요하면 수정하고 메모 유형을 확인한 후 추가합니다.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. 템플릿 조건",
        "paragraphs": [
          "조건은 특정 주문에서 템플릿을 사용할 수 있는지 결정합니다. 주문 상태, 결제 방법, 배송 방법, 청구 국가, 최소 또는 최대 주문 금액으로 제한할 수 있으며 설정된 모든 조건이 일치해야 합니다."
        ],
        "items": [
          "제한하지 않을 조건은 비워 두세요.",
          "결제 및 배송 방법의 기술 ID를 사용하세요.",
          "조건은 화면과 메모 생성 전 서버에서 다시 확인됩니다."
        ]
      },
      {
        "title": "21. 이메일 처리 기록",
        "paragraphs": [
          "고객 메모의 경우 WooCommerce가 이메일 처리를 보고한 이벤트와 wp_mail 기술 오류를 기록합니다. “처리됨”은 메일 시스템으로 전달되었다는 뜻이며 최종 배달이나 읽음을 보장하지 않습니다."
        ],
        "items": [
          "기록 페이지에서 처리 및 실패 이벤트를 확인하세요.",
          "정확한 배달 정보에는 SMTP 서비스를 사용하세요.",
          "내부 메모는 고객 메모 이메일을 보내지 않습니다."
        ]
      },
      {
        "title": "22. 중앙 기록",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → 기록</strong>에서 메모 생성, 템플릿 사용, 이메일 처리 및 실패를 확인할 수 있습니다. 가능한 경우 주문, 템플릿, 사용자, 수신자, 이벤트 종류와 시간이 표시됩니다."
        ],
        "items": [
          "지원, 감사 및 문제 해결에 사용하세요.",
          "WooCommerce 주문 메모와 별도로 저장됩니다.",
          "최근 250개 항목을 표시합니다."
        ]
      },
      {
        "title": "23. 테스트 주문 미리보기",
        "paragraphs": [
          "템플릿 편집기에 WooCommerce 주문 ID를 입력하면 저장하지 않은 변경을 포함한 현재 내용을 주문 데이터로 미리 볼 수 있습니다. 메모를 만들거나 이메일을 보내지 않습니다."
        ],
        "items": [
          "테스트 주문이나 스테이징 환경을 사용하세요.",
          "누락 값, 서식, 조건 및 사용자 정의 메타를 확인하세요.",
          "선택한 주문을 편집할 권한이 필요합니다."
        ]
      },
      {
        "title": "24. 개인 즐겨찾기와 최근 사용 템플릿",
        "paragraphs": [
          "각 사용자는 주문 화면에서 개인 즐겨찾기를 설정할 수 있습니다. 또한 사용자별로 성공적으로 사용한 최근 10개 템플릿을 저장해 위에 표시합니다. 전역 즐겨찾기는 모두에게 공유됩니다."
        ],
        "items": [
          "개인 즐겨찾기는 다른 사용자에게 영향을 주지 않습니다.",
          "최근 목록은 메모가 성공적으로 추가된 후에만 갱신됩니다.",
          "데이터는 WordPress 사용자 메타에 저장됩니다."
        ]
      },
      {
        "title": "25. 진단 페이지",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → 진단</strong>에서 WordPress, PHP, WooCommerce 버전, HPOS, 고객 메모 이메일, 언어, 템플릿 수, 캐시 및 WP_DEBUG를 확인할 수 있습니다."
        ],
        "items": [
          "지원 요청 시 이 정보를 제공하세요.",
          "메모 내용이나 고객 주소는 표시하지 않습니다.",
          "개발자는 진단 필터로 항목을 추가할 수 있습니다."
        ]
      },
      {
        "title": "26. 개발자용 훅과 필터",
        "paragraphs": [
          "플레이스홀더, 값, 허용 메타 키, 템플릿 결과, 조건, 미리보기, 최종 내용, 추가 전후 작업, 기록 및 진단을 확장하는 훅과 필터를 제공합니다. 이름과 매개변수는 readme.txt에 문서화되어 있습니다."
        ],
        "items": [
          "사용자 정의 데이터는 검증, 정리 및 이스케이프하세요.",
          "주문 테이블 직접 접근 대신 WooCommerce 주문 API를 사용하세요.",
          "HPOS와 기존 저장 방식 모두와 호환되게 하세요."
        ]
      }
    ]
  },
  "tr_TR": {
    "title": "Mailhilfe Order Note Manager for WooCommerce ayrıntılı yardımı",
    "intro": "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Eklenti ne yapar",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Şablon oluşturma",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Metni biçimlendirme",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Yer tutucuları kullanma",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Önizlemeyi kontrol etme",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Dahili notlar ve müşteri notları",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Favoriler, arama ve sıralama",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. İçe aktarma, dışa aktarma ve demo şablonlar",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Roller ve izinler",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Güvenlik ve HPOS uyumluluğu",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Sorun giderme",
        "paragraphs": [
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar.",
          "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Ayarlar sayfası",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → Ayarlar</strong> bölümünde varsayılan not türünü, güvenli HTML’yi, kullanım görünümünü, favorileri, JSON içe aktarmayı ve dil eşleştirmeyi ayarlayın. Günlük kullanımda dahili notları varsayılan yapın."
        ],
        "items": []
      },
      {
        "title": "13. Şablon dili ve çok dilli mağazalar",
        "paragraphs": [
          "Her şablona bir dil atanabilir. Genel metinler için <strong>Tüm diller</strong>, çevrilmiş müşteri mesajları için belirli bir dil seçin.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Özel alanlar ve meta veriler",
        "paragraphs": [
          "<code>{order_meta:meta_key}</code> ve <code>{customer_meta:meta_key}</code> seçili meta verileri ekler. password, token veya secret gibi hassas anahtarlar engellenir.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Çoğaltma ve revizyonlar",
        "paragraphs": [
          "Çoğaltma işlemi taslak bir kopya oluşturur. WordPress revizyonları eski sürümleri karşılaştırıp geri yüklemeye yardımcı olur.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. İzinler",
        "paragraphs": [
          "<strong>İzinler</strong> sayfası hangi rollerin şablonları yöneteceğini ve siparişlerde kullanacağını belirler.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. İçe aktarma önizlemesi",
        "paragraphs": [
          "JSON içe aktarma, değişikliklerden önce oluşturulacak, güncellenecek veya atlanacak şablonları gösterir.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Müşteri notu e-posta durumu",
        "paragraphs": [
          "İlgili e-posta etkinse müşteri notları WooCommerce e-posta bildirimlerini tetikleyebilir. Eklenti, müşteri notunun oluşturulmasını e-posta işleme sonucundan ayrı kaydeder. Notu eklemeden önce düzenlenebilir önizlemeyi kontrol edin ve sonucu Geçmiş sayfasından inceleyin."
        ],
        "items": []
      },
      {
        "title": "19. Önerilen iş akışı",
        "paragraphs": [
          "Şablonu seçin, değiştirilmiş önizlemeyi kontrol edin, gerekiyorsa düzenleyin, not türünü doğrulayın ve notu ekleyin.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Şablon koşulları",
        "paragraphs": [
          "Koşullar bir şablonun siparişte kullanılabilir olup olmadığını belirler. Sipariş durumu, ödeme yöntemi, gönderim yöntemi, fatura ülkesi ve minimum veya maksimum toplam ile sınırlandırabilirsiniz. Girilen tüm koşullar eşleşmelidir."
        ],
        "items": [
          "Kısıtlama uygulanmayacak alanı boş bırakın.",
          "Ödeme ve gönderim yöntemlerinin teknik kimliklerini kullanın.",
          "Koşullar arayüzde ve not oluşturulmadan önce sunucuda tekrar kontrol edilir."
        ]
      },
      {
        "title": "21. E-posta işleme günlüğü",
        "paragraphs": [
          "Müşteri notlarında eklenti, WooCommerce’in e-postayı işlediğini bildirdiği olayları ve wp_mail teknik hatalarını kaydeder. “İşlendi” durumu posta sistemine aktarımı gösterir; nihai teslimatı veya okunmayı kanıtlamaz."
        ],
        "items": [
          "İşlenen ve başarısız olaylar için Geçmiş sayfasını kontrol edin.",
          "Kesin teslimat bilgisi için SMTP hizmeti kullanın.",
          "Dahili notlar müşteri notu e-postası göndermez."
        ]
      },
      {
        "title": "22. Merkezi geçmiş",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → Geçmiş</strong> sayfasında not oluşturma, şablon kullanımı, e-posta işleme ve hataları görebilirsiniz. Varsa sipariş, şablon, kullanıcı, alıcı, olay türü ve zaman gösterilir."
        ],
        "items": [
          "Destek, denetim ve sorun giderme için kullanın.",
          "WooCommerce sipariş notlarından ayrıdır.",
          "En son 250 kayıt gösterilir."
        ]
      },
      {
        "title": "23. Test siparişi önizlemesi",
        "paragraphs": [
          "Şablon düzenleyicisinde bir WooCommerce sipariş kimliği girin. Kaydedilmemiş değişiklikler dahil mevcut içerik, not oluşturmadan veya e-posta göndermeden sipariş verileriyle gösterilir."
        ],
        "items": [
          "Test siparişi veya hazırlık sitesi kullanın.",
          "Eksik değerleri, biçimlendirmeyi, koşulları ve özel metayı kontrol edin.",
          "Seçilen siparişi düzenleme yetkiniz olmalıdır."
        ]
      },
      {
        "title": "24. Kişisel favoriler ve son kullanılan şablonlar",
        "paragraphs": [
          "Her kullanıcı sipariş ekranında kişisel favoriler belirleyebilir. Eklenti ayrıca kullanıcı başına başarıyla kullanılan son on şablonu kaydeder ve üstte gösterir. Genel favoriler herkesle paylaşılır."
        ],
        "items": [
          "Kişisel favoriler diğer kullanıcıları etkilemez.",
          "Son kullanılanlar yalnızca not başarıyla eklendikten sonra güncellenir.",
          "Veriler WordPress kullanıcı metası olarak saklanır."
        ]
      },
      {
        "title": "25. Tanılama sayfası",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → Tanılama</strong> altında WordPress, PHP ve WooCommerce sürümleri, HPOS, müşteri notu e-postası, dil, şablon sayısı, önbellek ve WP_DEBUG bilgileri bulunur."
        ],
        "items": [
          "Destek isterken bu bilgileri paylaşın.",
          "Not içeriği veya müşteri adresleri gösterilmez.",
          "Geliştiriciler tanılama filtresiyle satır ekleyebilir."
        ]
      },
      {
        "title": "26. Geliştirici kancaları ve filtreleri",
        "paragraphs": [
          "Yer tutucular, değerler, izin verilen meta anahtarları, şablon sonuçları, koşullar, önizleme, son içerik, ekleme öncesi/sonrası, geçmiş ve tanılama için kancalar ve filtreler vardır. Adlar readme.txt dosyasında belgelenmiştir."
        ],
        "items": [
          "Özel verileri doğrulayın, temizleyin ve kaçışlayın.",
          "Doğrudan tablolara erişmek yerine WooCommerce sipariş API’lerini kullanın.",
          "HPOS ve klasik depolamayla uyumluluğu koruyun."
        ]
      }
    ]
  },
  "ar": {
    "title": "مساعدة مفصلة لـ Mailhilfe Order Note Manager for WooCommerce",
    "intro": "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. وظيفة الإضافة",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. إنشاء قالب",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. تنسيق النص",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. استخدام العناصر النائبة",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. فحص المعاينة",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. ملاحظات داخلية وملاحظات للعميل",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. المفضلة والبحث والترتيب",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. الاستيراد والتصدير وقوالب العرض",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. الأدوار والصلاحيات",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. الأمان وتوافق HPOS",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. استكشاف الأخطاء",
        "paragraphs": [
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن.",
          "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. صفحة الإعدادات",
        "paragraphs": [
          "افتح <strong>Mailhilfe Order Notes → الإعدادات</strong> لاختيار نوع الملاحظة الافتراضي وHTML الآمن وعرض الاستخدام والمفضلة واستيراد JSON ومطابقة اللغة. استخدم الملاحظات الداخلية افتراضيًا في العمل اليومي."
        ],
        "items": []
      },
      {
        "title": "13. لغة القالب والمتاجر متعددة اللغات",
        "paragraphs": [
          "يمكن تعيين لغة لكل قالب. اختر <strong>كل اللغات</strong> للنص العام أو لغة محددة للرسائل المترجمة للعميل.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. الحقول المخصصة والبيانات الوصفية",
        "paragraphs": [
          "تدرج العناصر <code>{order_meta:meta_key}</code> و <code>{customer_meta:meta_key}</code> بيانات وصفية محددة. يتم حظر المفاتيح الحساسة مثل password و token و secret.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. النسخ والمراجعات",
        "paragraphs": [
          "تنشئ عملية النسخ نسخة كمسودة. تساعد مراجعات WordPress في مقارنة الإصدارات السابقة واستعادتها.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. الصلاحيات",
        "paragraphs": [
          "تحدد صفحة <strong>الصلاحيات</strong> الأدوار التي تدير القوالب والأدوار التي تستخدمها في الطلبات.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. معاينة الاستيراد",
        "paragraphs": [
          "يعرض استيراد JSON معاينة للقوالب التي سيتم إنشاؤها أو تحديثها أو تخطيها قبل التطبيق.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. حالة بريد ملاحظة العميل",
        "paragraphs": [
          "قد تؤدي ملاحظات العملاء إلى تشغيل إشعارات WooCommerce عبر البريد الإلكتروني عند تفعيل الرسالة المقابلة. تسجل الإضافة إنشاء ملاحظة العميل بصورة منفصلة عن معالجة البريد. راجع المعاينة القابلة للتحرير قبل إضافة الملاحظة وتحقق من النتيجة في صفحة السجل."
        ],
        "items": []
      },
      {
        "title": "19. سير العمل الموصى به",
        "paragraphs": [
          "اختر قالبًا، راجع المعاينة بعد الاستبدال، عدّلها عند الحاجة، تحقق من نوع الملاحظة ثم أضفها.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. شروط القوالب",
        "paragraphs": [
          "تحدد الشروط ما إذا كان القالب متاحًا لطلب معين. يمكن التقييد حسب حالة الطلب وطريقة الدفع وطريقة الشحن وبلد الفوترة والحد الأدنى أو الأقصى للإجمالي. يجب أن تتطابق جميع الشروط المدخلة."
        ],
        "items": [
          "اترك الحقل فارغًا إذا لم يكن مطلوبًا أن يقيّد القالب.",
          "استخدم المعرّفات التقنية لطرق الدفع والشحن.",
          "يتم فحص الشروط في الواجهة ثم مرة أخرى على الخادم."
        ]
      },
      {
        "title": "21. سجل معالجة البريد الإلكتروني",
        "paragraphs": [
          "في ملاحظات العميل تسجل الإضافة عندما يبلغ WooCommerce عن معالجة البريد، كما تسجل أخطاء wp_mail التقنية. تعني «تمت المعالجة» تسليم الرسالة لنظام البريد فقط، ولا تثبت وصولها النهائي أو قراءتها."
        ],
        "items": [
          "راجع صفحة السجل للأحداث المعالجة والفاشلة.",
          "استخدم مزود SMTP للحصول على معلومات تسليم أدق.",
          "الملاحظات الداخلية لا ترسل بريد ملاحظة العميل."
        ]
      },
      {
        "title": "22. السجل المركزي",
        "paragraphs": [
          "افتح <strong>Mailhilfe Order Notes ← السجل</strong> لمراجعة إنشاء الملاحظات واستخدام القوالب ومعالجة البريد والأخطاء. عند توفرها تظهر بيانات الطلب والقالب والمستخدم والمستلم ونوع الحدث والوقت."
        ],
        "items": [
          "استخدم السجل للدعم والتدقيق واستكشاف الأخطاء.",
          "السجل منفصل عن ملاحظات طلب WooCommerce.",
          "يتم عرض أحدث 250 إدخالًا."
        ]
      },
      {
        "title": "23. معاينة باستخدام طلب اختبار",
        "paragraphs": [
          "أدخل معرّف طلب WooCommerce في محرر القالب. تتم معاينة المحتوى الحالي، بما في ذلك التغييرات غير المحفوظة، ببيانات الطلب دون إنشاء ملاحظة أو إرسال بريد."
        ],
        "items": [
          "استخدم طلب اختبار أو موقعًا تجريبيًا.",
          "تحقق من القيم المفقودة والتنسيق والشروط والبيانات الوصفية.",
          "يجب أن تملك صلاحية تعديل الطلب المحدد."
        ]
      },
      {
        "title": "24. المفضلة الشخصية والقوالب المستخدمة حديثًا",
        "paragraphs": [
          "يمكن لكل مستخدم تعيين مفضلات شخصية في شاشة الطلب. كما تحفظ الإضافة آخر عشرة قوالب تم استخدامها بنجاح لكل مستخدم وتعرضها أولًا. تبقى المفضلات العامة مشتركة."
        ],
        "items": [
          "لا تؤثر المفضلات الشخصية على المستخدمين الآخرين.",
          "لا يتم تحديث القائمة الحديثة إلا بعد إضافة الملاحظة بنجاح.",
          "تُخزن البيانات كبيانات وصفية لمستخدم WordPress."
        ]
      },
      {
        "title": "25. صفحة التشخيص",
        "paragraphs": [
          "افتح <strong>Mailhilfe Order Notes ← التشخيص</strong> لعرض إصدارات WordPress وPHP وWooCommerce وحالة HPOS وبريد ملاحظة العميل واللغة وعدد القوالب والتخزين المؤقت وWP_DEBUG."
        ],
        "items": [
          "أرسل هذه المعلومات عند طلب الدعم.",
          "لا تعرض الصفحة محتوى الملاحظات أو عناوين العملاء.",
          "يمكن للمطورين إضافة صفوف عبر مرشح التشخيص."
        ]
      },
      {
        "title": "26. الخطافات والمرشحات للمطورين",
        "paragraphs": [
          "توفر الإضافة خطافات ومرشحات للعناصر النائبة والقيم ومفاتيح البيانات الوصفية المسموحة ونتائج القوالب والشروط والمعاينة والمحتوى النهائي والإجراءات قبل/بعد الإضافة والسجل والتشخيص. الأسماء موثقة في readme.txt."
        ],
        "items": [
          "تحقق من البيانات المخصصة ونظفها وهربها.",
          "استخدم واجهات طلبات WooCommerce بدل الوصول المباشر للجداول.",
          "حافظ على التوافق مع HPOS والتخزين التقليدي."
        ]
      }
    ]
  },
  "hi_IN": {
    "title": "Mailhilfe Order Note Manager for WooCommerce की विस्तृत सहायता",
    "intro": "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग। Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. प्लगइन क्या करता है",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. टेम्पलेट बनाना",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. पाठ को स्वरूपित करना",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. प्लेसहोल्डर उपयोग करना",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. पूर्वावलोकन जांचना",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. आंतरिक नोट और ग्राहक नोट",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. पसंदीदा, खोज और क्रम",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. आयात, निर्यात और डेमो टेम्पलेट",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. भूमिकाएं और अनुमतियां",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. सुरक्षा और HPOS अनुकूलता",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. समस्या समाधान",
        "paragraphs": [
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।",
          "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग।"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. सेटिंग पेज",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → सेटिंग</strong> में डिफ़ॉल्ट नोट प्रकार, सुरक्षित HTML, उपयोग प्रदर्शन, पसंदीदा, JSON आयात और भाषा मिलान चुनें। दैनिक काम के लिए आंतरिक नोट को डिफ़ॉल्ट रखें।"
        ],
        "items": []
      },
      {
        "title": "13. टेम्पलेट भाषा और बहुभाषी दुकानें",
        "paragraphs": [
          "हर टेम्पलेट की भाषा चुनी जा सकती है। सामान्य पाठ के लिए <strong>सभी भाषाएँ</strong> और अनुवादित ग्राहक संदेशों के लिए विशिष्ट भाषा चुनें।",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. कस्टम फ़ील्ड और मेटाडेटा",
        "paragraphs": [
          "<code>{order_meta:meta_key}</code> और <code>{customer_meta:meta_key}</code> चुने हुए मेटाडेटा जोड़ते हैं। password, token या secret जैसी संवेदनशील कुंजियाँ रोकी जाती हैं।",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. डुप्लिकेट और संशोधन",
        "paragraphs": [
          "डुप्लिकेट क्रिया ड्राफ्ट कॉपी बनाती है। WordPress revisions पुराने संस्करणों की तुलना और पुनर्स्थापना में मदद करते हैं।",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. अनुमतियाँ",
        "paragraphs": [
          "<strong>Permissions</strong> पेज तय करता है कि कौन-सी भूमिकाएँ टेम्पलेट प्रबंधित करेंगी और कौन उन्हें ऑर्डर में उपयोग करेंगी।",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. आयात पूर्वावलोकन",
        "paragraphs": [
          "JSON आयात लागू करने से पहले बनाए, अपडेट या छोड़े जाने वाले टेम्पलेट दिखाता है।",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. ग्राहक नोट ईमेल स्थिति",
        "paragraphs": [
          "यदि संबंधित ईमेल सक्षम है तो ग्राहक नोट WooCommerce ईमेल सूचना शुरू कर सकते हैं। प्लगइन ग्राहक नोट बनने और ईमेल प्रोसेसिंग के परिणाम को अलग-अलग दर्ज करता है। नोट जोड़ने से पहले संपादन योग्य पूर्वावलोकन देखें और इतिहास पृष्ठ पर परिणाम जाँचें।"
        ],
        "items": []
      },
      {
        "title": "19. अनुशंसित कार्यप्रवाह",
        "paragraphs": [
          "टेम्पलेट चुनें, बदला हुआ पूर्वावलोकन जाँचें, आवश्यकता हो तो संपादित करें, नोट प्रकार जाँचें और नोट जोड़ें.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. टेम्पलेट की शर्तें",
        "paragraphs": [
          "शर्तें तय करती हैं कि कोई टेम्पलेट किसी ऑर्डर के लिए उपलब्ध है या नहीं। इसे ऑर्डर स्थिति, भुगतान विधि, शिपिंग विधि, बिलिंग देश और न्यूनतम या अधिकतम कुल राशि से सीमित किया जा सकता है। सभी भरी हुई शर्तें पूरी होनी चाहिए।"
        ],
        "items": [
          "जिस शर्त से सीमा नहीं लगानी हो उसे खाली छोड़ें।",
          "भुगतान और शिपिंग विधियों के तकनीकी ID उपयोग करें।",
          "शर्तें इंटरफ़ेस और नोट बनाने से पहले सर्वर पर दोबारा जांची जाती हैं।"
        ]
      },
      {
        "title": "21. ईमेल प्रोसेसिंग लॉग",
        "paragraphs": [
          "ग्राहक नोट के लिए प्लगइन WooCommerce द्वारा ईमेल प्रोसेस होने की सूचना और wp_mail की तकनीकी त्रुटियां दर्ज करता है। “प्रोसेस्ड” केवल मेल सिस्टम को सौंपे जाने की पुष्टि है, अंतिम डिलीवरी या पढ़े जाने की नहीं।"
        ],
        "items": [
          "प्रोसेस्ड और विफल घटनाओं के लिए इतिहास पेज देखें।",
          "सटीक डिलीवरी जानकारी के लिए SMTP सेवा उपयोग करें।",
          "आंतरिक नोट ग्राहक नोट ईमेल नहीं भेजते।"
        ]
      },
      {
        "title": "22. केंद्रीय इतिहास",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → इतिहास</strong> में नोट निर्माण, टेम्पलेट उपयोग, ईमेल प्रोसेसिंग और विफलताएं देखें। उपलब्ध होने पर ऑर्डर, टेम्पलेट, उपयोगकर्ता, प्राप्तकर्ता, घटना प्रकार और समय दिखता है।"
        ],
        "items": [
          "समर्थन, ऑडिट और समस्या समाधान के लिए उपयोग करें।",
          "यह WooCommerce ऑर्डर नोट से अलग है।",
          "सबसे हाल की 250 प्रविष्टियां दिखाई जाती हैं।"
        ]
      },
      {
        "title": "23. टेस्ट ऑर्डर पूर्वावलोकन",
        "paragraphs": [
          "टेम्पलेट एडिटर में WooCommerce ऑर्डर ID दर्ज करें। सहेजे बिना किए गए बदलावों सहित वर्तमान सामग्री ऑर्डर डेटा से दिखाई जाएगी, बिना नोट बनाए या ईमेल भेजे।"
        ],
        "items": [
          "टेस्ट ऑर्डर या स्टेजिंग साइट उपयोग करें।",
          "खाली मान, फॉर्मेटिंग, शर्तें और कस्टम मेटा जांचें।",
          "चुने गए ऑर्डर को संपादित करने की अनुमति आवश्यक है।"
        ]
      },
      {
        "title": "24. व्यक्तिगत पसंदीदा और हाल के टेम्पलेट",
        "paragraphs": [
          "हर उपयोगकर्ता ऑर्डर स्क्रीन में व्यक्तिगत पसंदीदा चुन सकता है। प्लगइन प्रति उपयोगकर्ता सफलतापूर्वक उपयोग किए गए अंतिम दस टेम्पलेट भी सहेजता और ऊपर दिखाता है। वैश्विक पसंदीदा सभी के लिए साझा रहते हैं।"
        ],
        "items": [
          "व्यक्तिगत पसंदीदा दूसरे उपयोगकर्ताओं को प्रभावित नहीं करते।",
          "हाल की सूची केवल नोट सफलतापूर्वक जुड़ने पर अपडेट होती है।",
          "डेटा WordPress उपयोगकर्ता मेटा में सहेजा जाता है।"
        ]
      },
      {
        "title": "25. निदान पेज",
        "paragraphs": [
          "<strong>Mailhilfe Order Notes → निदान</strong> में WordPress, PHP और WooCommerce संस्करण, HPOS, ग्राहक नोट ईमेल, भाषा, टेम्पलेट संख्या, कैश और WP_DEBUG देखें।"
        ],
        "items": [
          "समर्थन मांगते समय ये जानकारी दें।",
          "नोट सामग्री या ग्राहक पते नहीं दिखाए जाते।",
          "डेवलपर निदान फ़िल्टर से पंक्तियां जोड़ सकते हैं।"
        ]
      },
      {
        "title": "26. डेवलपर हुक और फ़िल्टर",
        "paragraphs": [
          "प्लेसहोल्डर, मान, अनुमत मेटा कुंजी, टेम्पलेट परिणाम, शर्तें, पूर्वावलोकन, अंतिम सामग्री, जोड़ने से पहले/बाद, इतिहास और निदान के लिए हुक और फ़िल्टर उपलब्ध हैं। नाम readme.txt में दिए हैं।"
        ],
        "items": [
          "कस्टम डेटा को मान्य, स्वच्छ और एस्केप करें।",
          "सीधे टेबल के बजाय WooCommerce ऑर्डर API उपयोग करें।",
          "HPOS और क्लासिक स्टोरेज दोनों के साथ संगतता रखें।"
        ]
      }
    ]
  },
  "id_ID": {
    "title": "Bantuan lengkap Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Fungsi plugin",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Membuat templat",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Memformat teks",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Menggunakan placeholder",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Memeriksa pratinjau",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Catatan internal dan catatan pelanggan",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Favorit, pencarian, dan pengurutan",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Impor, ekspor, dan templat demo",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Peran dan izin",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Keamanan dan kompatibilitas HPOS",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Pemecahan masalah",
        "paragraphs": [
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman.",
          "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Halaman pengaturan",
        "paragraphs": [
          "Buka <strong>Mailhilfe Order Notes → Pengaturan</strong> untuk memilih tipe catatan default, HTML aman, tampilan penggunaan, favorit, impor JSON, dan pencocokan bahasa. Gunakan catatan internal sebagai default untuk pekerjaan sehari-hari."
        ],
        "items": []
      },
      {
        "title": "13. Bahasa templat dan toko multibahasa",
        "paragraphs": [
          "Setiap templat dapat memiliki bahasa. Pilih <strong>Semua bahasa</strong> untuk teks umum atau bahasa tertentu untuk pesan pelanggan yang diterjemahkan.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Kolom khusus dan metadata",
        "paragraphs": [
          "Placeholder <code>{order_meta:meta_key}</code> dan <code>{customer_meta:meta_key}</code> memasukkan metadata terpilih. Kunci sensitif seperti password, token, atau secret diblokir.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Duplikasi dan revisi",
        "paragraphs": [
          "Duplikasi membuat salinan sebagai draf. Revisi WordPress membantu membandingkan dan memulihkan versi lama.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Izin",
        "paragraphs": [
          "Halaman <strong>Izin</strong> menentukan peran mana yang mengelola templat dan mana yang menggunakannya di pesanan.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Pratinjau impor",
        "paragraphs": [
          "Impor JSON menampilkan pratinjau templat yang dibuat, diperbarui, atau dilewati sebelum diterapkan.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Status email catatan pelanggan",
        "paragraphs": [
          "Catatan pelanggan dapat memicu pemberitahuan email WooCommerce jika email terkait diaktifkan. Plugin mencatat pembuatan catatan pelanggan secara terpisah dari pemrosesan email. Tinjau pratinjau yang dapat diedit sebelum menambahkan catatan dan periksa hasilnya di halaman Riwayat."
        ],
        "items": []
      },
      {
        "title": "19. Alur kerja yang disarankan",
        "paragraphs": [
          "Pilih templat, periksa pratinjau, edit bila perlu, pastikan tipe catatan, lalu tambahkan catatan.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Kondisi templat",
        "paragraphs": [
          "Kondisi menentukan apakah templat tersedia untuk suatu pesanan. Templat dapat dibatasi berdasarkan status, metode pembayaran, metode pengiriman, negara penagihan, serta total minimum atau maksimum. Semua kondisi yang diisi harus cocok."
        ],
        "items": [
          "Kosongkan kolom jika tidak ingin membatasi templat.",
          "Gunakan ID teknis metode pembayaran dan pengiriman.",
          "Kondisi diperiksa di antarmuka dan kembali di server."
        ]
      },
      {
        "title": "21. Log pemrosesan email",
        "paragraphs": [
          "Untuk catatan pelanggan, plugin mencatat saat WooCommerce melaporkan email telah diproses dan kesalahan teknis wp_mail. “Diproses” hanya menegaskan penyerahan ke sistem email, bukan pengiriman akhir atau bahwa email dibaca."
        ],
        "items": [
          "Lihat halaman Riwayat untuk kejadian diproses dan gagal.",
          "Gunakan layanan SMTP untuk informasi pengiriman yang lebih akurat.",
          "Catatan internal tidak memicu email catatan pelanggan."
        ]
      },
      {
        "title": "22. Riwayat terpusat",
        "paragraphs": [
          "Buka <strong>Mailhilfe Order Notes → Riwayat</strong> untuk melihat pembuatan catatan, penggunaan templat, pemrosesan email, dan kegagalan. Jika tersedia, pesanan, templat, pengguna, penerima, jenis kejadian, dan waktu ditampilkan."
        ],
        "items": [
          "Gunakan untuk dukungan, audit, dan pemecahan masalah.",
          "Riwayat ini terpisah dari catatan pesanan WooCommerce.",
          "Menampilkan 250 entri terbaru."
        ]
      },
      {
        "title": "23. Pratinjau dengan pesanan uji",
        "paragraphs": [
          "Masukkan ID pesanan WooCommerce di editor templat. Konten saat ini, termasuk perubahan yang belum disimpan, ditampilkan dengan data pesanan tanpa membuat catatan atau mengirim email."
        ],
        "items": [
          "Gunakan pesanan uji atau situs staging.",
          "Periksa nilai kosong, format, kondisi, dan meta khusus.",
          "Anda harus memiliki izin mengedit pesanan yang dipilih."
        ]
      },
      {
        "title": "24. Favorit pribadi dan templat terbaru",
        "paragraphs": [
          "Setiap pengguna dapat menandai favorit pribadi di layar pesanan. Plugin juga menyimpan sepuluh templat terakhir yang berhasil digunakan per pengguna dan menampilkannya lebih atas. Favorit global tetap dibagikan."
        ],
        "items": [
          "Favorit pribadi tidak memengaruhi pengguna lain.",
          "Daftar terbaru hanya diperbarui setelah catatan berhasil ditambahkan.",
          "Data disimpan sebagai metadata pengguna WordPress."
        ]
      },
      {
        "title": "25. Halaman diagnostik",
        "paragraphs": [
          "Buka <strong>Mailhilfe Order Notes → Diagnostik</strong> untuk melihat versi WordPress, PHP dan WooCommerce, HPOS, email catatan pelanggan, bahasa, jumlah templat, cache, dan WP_DEBUG."
        ],
        "items": [
          "Sertakan data ini saat meminta dukungan.",
          "Konten catatan dan alamat pelanggan tidak ditampilkan.",
          "Pengembang dapat menambah baris melalui filter diagnostik."
        ]
      },
      {
        "title": "26. Hook dan filter untuk pengembang",
        "paragraphs": [
          "Plugin menyediakan hook dan filter untuk placeholder, nilai, kunci meta yang diizinkan, hasil templat, kondisi, pratinjau, konten akhir, tindakan sebelum/sesudah penambahan, riwayat, dan diagnostik. Nama didokumentasikan di readme.txt."
        ],
        "items": [
          "Validasi, sanitasi, dan escape semua data khusus.",
          "Gunakan API pesanan WooCommerce, bukan akses tabel langsung.",
          "Pertahankan kompatibilitas dengan HPOS dan penyimpanan klasik."
        ]
      }
    ]
  },
  "vi": {
    "title": "Trợ giúp chi tiết Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Plugin làm gì",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Tạo mẫu",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Định dạng văn bản",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Sử dụng biến giữ chỗ",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Kiểm tra bản xem trước",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Ghi chú nội bộ và ghi chú khách hàng",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Yêu thích, tìm kiếm và sắp xếp",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Nhập, xuất và mẫu demo",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Vai trò và quyền",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Bảo mật và tương thích HPOS",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Khắc phục sự cố",
        "paragraphs": [
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn.",
          "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Trang cài đặt",
        "paragraphs": [
          "Mở <strong>Mailhilfe Order Notes → Cài đặt</strong> để chọn loại ghi chú mặc định, HTML an toàn, hiển thị lượt dùng, mục yêu thích, nhập JSON và khớp ngôn ngữ. Nên dùng ghi chú nội bộ làm mặc định hằng ngày."
        ],
        "items": []
      },
      {
        "title": "13. Ngôn ngữ mẫu và cửa hàng đa ngôn ngữ",
        "paragraphs": [
          "Mỗi mẫu có thể có một ngôn ngữ. Chọn <strong>Tất cả ngôn ngữ</strong> cho nội dung chung hoặc một ngôn ngữ cụ thể cho thông báo khách hàng đã dịch.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Trường tùy chỉnh và siêu dữ liệu",
        "paragraphs": [
          "Placeholder <code>{order_meta:meta_key}</code> và <code>{customer_meta:meta_key}</code> chèn siêu dữ liệu đã chọn. Các khóa nhạy cảm như password, token hoặc secret bị chặn.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Nhân bản và phiên bản",
        "paragraphs": [
          "Hành động nhân bản tạo một bản nháp. Revisions của WordPress giúp so sánh và khôi phục phiên bản cũ.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Quyền",
        "paragraphs": [
          "Trang <strong>Quyền</strong> xác định vai trò nào quản lý mẫu và vai trò nào dùng mẫu trong đơn hàng.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Xem trước nhập",
        "paragraphs": [
          "Nhập JSON hiển thị trước các mẫu sẽ được tạo, cập nhật hoặc bỏ qua trước khi áp dụng.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Trạng thái email ghi chú khách hàng",
        "paragraphs": [
          "Ghi chú khách hàng có thể kích hoạt email thông báo WooCommerce khi email tương ứng được bật. Plugin ghi riêng việc tạo ghi chú và kết quả xử lý email. Hãy kiểm tra bản xem trước có thể chỉnh sửa trước khi thêm và xem kết quả trên trang Lịch sử."
        ],
        "items": []
      },
      {
        "title": "19. Quy trình đề xuất",
        "paragraphs": [
          "Chọn mẫu, kiểm tra bản xem trước đã thay thế, chỉnh sửa nếu cần, xác nhận loại ghi chú rồi thêm ghi chú.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Điều kiện mẫu",
        "paragraphs": [
          "Điều kiện quyết định mẫu có sẵn cho một đơn hàng hay không. Có thể giới hạn theo trạng thái đơn hàng, phương thức thanh toán, phương thức giao hàng, quốc gia thanh toán và tổng tối thiểu hoặc tối đa. Tất cả điều kiện đã nhập phải phù hợp."
        ],
        "items": [
          "Để trống trường nếu không muốn giới hạn mẫu.",
          "Dùng ID kỹ thuật của phương thức thanh toán và giao hàng.",
          "Điều kiện được kiểm tra trên giao diện và lại trên máy chủ."
        ]
      },
      {
        "title": "21. Nhật ký xử lý email",
        "paragraphs": [
          "Với ghi chú khách hàng, plugin ghi lại khi WooCommerce báo email đã được xử lý và các lỗi kỹ thuật wp_mail. “Đã xử lý” chỉ xác nhận chuyển sang hệ thống thư, không chứng minh đã giao cuối cùng hoặc đã đọc."
        ],
        "items": [
          "Xem trang Lịch sử để biết sự kiện đã xử lý và thất bại.",
          "Dùng dịch vụ SMTP để có thông tin giao thư chính xác hơn.",
          "Ghi chú nội bộ không kích hoạt email ghi chú khách hàng."
        ]
      },
      {
        "title": "22. Lịch sử tập trung",
        "paragraphs": [
          "Mở <strong>Mailhilfe Order Notes → Lịch sử</strong> để xem việc tạo ghi chú, sử dụng mẫu, xử lý và lỗi email. Khi có, đơn hàng, mẫu, người dùng, người nhận, loại sự kiện và thời gian sẽ được hiển thị."
        ],
        "items": [
          "Dùng cho hỗ trợ, kiểm tra và khắc phục sự cố.",
          "Lịch sử này tách biệt với ghi chú đơn hàng WooCommerce.",
          "Hiển thị 250 mục mới nhất."
        ]
      },
      {
        "title": "23. Xem trước bằng đơn hàng thử",
        "paragraphs": [
          "Nhập ID đơn hàng WooCommerce trong trình sửa mẫu. Nội dung hiện tại, kể cả thay đổi chưa lưu, được hiển thị với dữ liệu đơn hàng mà không tạo ghi chú hoặc gửi email."
        ],
        "items": [
          "Dùng đơn hàng thử hoặc trang staging.",
          "Kiểm tra giá trị thiếu, định dạng, điều kiện và meta tùy chỉnh.",
          "Bạn phải có quyền sửa đơn hàng đã chọn."
        ]
      },
      {
        "title": "24. Yêu thích cá nhân và mẫu dùng gần đây",
        "paragraphs": [
          "Mỗi người dùng có thể đánh dấu yêu thích cá nhân trong màn hình đơn hàng. Plugin cũng lưu mười mẫu được dùng thành công gần nhất theo người dùng và đưa lên trên. Yêu thích chung vẫn được chia sẻ."
        ],
        "items": [
          "Yêu thích cá nhân không ảnh hưởng người dùng khác.",
          "Danh sách gần đây chỉ cập nhật sau khi thêm ghi chú thành công.",
          "Dữ liệu được lưu dưới dạng meta người dùng WordPress."
        ]
      },
      {
        "title": "25. Trang chẩn đoán",
        "paragraphs": [
          "Mở <strong>Mailhilfe Order Notes → Chẩn đoán</strong> để xem phiên bản WordPress, PHP, WooCommerce, trạng thái HPOS, email ghi chú khách hàng, ngôn ngữ, số mẫu, bộ nhớ đệm và WP_DEBUG."
        ],
        "items": [
          "Cung cấp các giá trị này khi yêu cầu hỗ trợ.",
          "Trang không hiển thị nội dung ghi chú hoặc địa chỉ khách hàng.",
          "Nhà phát triển có thể thêm hàng qua bộ lọc chẩn đoán."
        ]
      },
      {
        "title": "26. Hook và bộ lọc cho nhà phát triển",
        "paragraphs": [
          "Plugin cung cấp hook và bộ lọc cho biến giữ chỗ, giá trị, khóa meta được phép, kết quả mẫu, điều kiện, xem trước, nội dung cuối, hành động trước/sau khi thêm, lịch sử và chẩn đoán. Tên được ghi trong readme.txt."
        ],
        "items": [
          "Xác thực, làm sạch và escape dữ liệu tùy chỉnh.",
          "Dùng API đơn hàng WooCommerce thay vì truy cập bảng trực tiếp.",
          "Giữ tương thích với HPOS và lưu trữ cổ điển."
        ]
      }
    ]
  },
  "th": {
    "title": "วิธีใช้ Mailhilfe Order Note Manager for WooCommerce แบบละเอียด",
    "intro": "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. ปลั๊กอินทำอะไร",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. สร้างเทมเพลต",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. จัดรูปแบบข้อความ",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. ใช้ตัวยึดตำแหน่ง",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. ตรวจสอบตัวอย่าง",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. บันทึกภายในและบันทึกลูกค้า",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. รายการโปรด ค้นหา และจัดเรียง",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. นำเข้า ส่งออก และเทมเพลตตัวอย่าง",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. บทบาทและสิทธิ์",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. ความปลอดภัยและความเข้ากันได้กับ HPOS",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. การแก้ไขปัญหา",
        "paragraphs": [
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย",
          "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย"
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. หน้าการตั้งค่า",
        "paragraphs": [
          "เปิด <strong>Mailhilfe Order Notes → การตั้งค่า</strong> เพื่อเลือกชนิดบันทึกเริ่มต้น HTML ที่ปลอดภัย การแสดงจำนวนการใช้ รายการโปรด การนำเข้า JSON และการจับคู่ภาษา ควรใช้บันทึกภายในเป็นค่าเริ่มต้นในการทำงานประจำวัน"
        ],
        "items": []
      },
      {
        "title": "13. ภาษาของเทมเพลตและร้านหลายภาษา",
        "paragraphs": [
          "แต่ละเทมเพลตกำหนดภาษาได้ เลือก <strong>ทุกภาษา</strong> สำหรับข้อความทั่วไป หรือเลือกภาษาเฉพาะสำหรับข้อความลูกค้าที่แปลแล้ว",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. ฟิลด์กำหนดเองและเมตาดาต้า",
        "paragraphs": [
          "ตัวแทน <code>{order_meta:meta_key}</code> และ <code>{customer_meta:meta_key}</code> จะแทรกเมตาดาต้าที่เลือก คีย์อ่อนไหวเช่น password, token หรือ secret จะถูกบล็อก",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. ทำซ้ำและรุ่นแก้ไข",
        "paragraphs": [
          "การทำซ้ำจะสร้างสำเนาแบบร่าง รุ่นแก้ไขของ WordPress ช่วยเปรียบเทียบและกู้คืนเวอร์ชันเก่า",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. สิทธิ์",
        "paragraphs": [
          "หน้า <strong>สิทธิ์</strong> กำหนดว่าบทบาทใดจัดการเทมเพลตและบทบาทใดใช้เทมเพลตในคำสั่งซื้อ",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. ตัวอย่างก่อนนำเข้า",
        "paragraphs": [
          "การนำเข้า JSON จะแสดงตัวอย่างเทมเพลตที่จะสร้าง อัปเดต หรือข้ามก่อนนำไปใช้",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. สถานะอีเมลบันทึกลูกค้า",
        "paragraphs": [
          "บันทึกลูกค้าอาจกระตุ้นการแจ้งเตือนอีเมลของ WooCommerce เมื่อเปิดใช้งานอีเมลที่เกี่ยวข้อง ปลั๊กอินบันทึกการสร้างบันทึกลูกค้าแยกจากผลการประมวลผลอีเมล ตรวจสอบตัวอย่างที่แก้ไขได้ก่อนเพิ่มบันทึกและดูผลลัพธ์ในหน้าประวัติ"
        ],
        "items": []
      },
      {
        "title": "19. ขั้นตอนที่แนะนำ",
        "paragraphs": [
          "เลือกเทมเพลต ตรวจตัวอย่างที่แทนค่าแล้ว แก้ไขถ้าจำเป็น ตรวจชนิดบันทึก แล้วเพิ่มบันทึก",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. เงื่อนไขของเทมเพลต",
        "paragraphs": [
          "เงื่อนไขกำหนดว่าเทมเพลตพร้อมใช้กับคำสั่งซื้อหรือไม่ สามารถจำกัดตามสถานะคำสั่งซื้อ วิธีชำระเงิน วิธีจัดส่ง ประเทศที่ออกใบแจ้งหนี้ และยอดขั้นต่ำหรือสูงสุด เงื่อนไขที่กรอกทั้งหมดต้องตรงกัน"
        ],
        "items": [
          "ปล่อยช่องว่างหากไม่ต้องการใช้เป็นข้อจำกัด",
          "ใช้ ID ทางเทคนิคของวิธีชำระเงินและจัดส่ง",
          "ตรวจสอบเงื่อนไขทั้งในหน้าจอและฝั่งเซิร์ฟเวอร์อีกครั้ง"
        ]
      },
      {
        "title": "21. บันทึกการประมวลผลอีเมล",
        "paragraphs": [
          "สำหรับบันทึกลูกค้า ปลั๊กอินจะบันทึกเมื่อ WooCommerce รายงานว่าอีเมลถูกประมวลผลและบันทึกข้อผิดพลาดทางเทคนิคของ wp_mail สถานะ “ประมวลผลแล้ว” หมายถึงส่งต่อให้ระบบอีเมล ไม่ได้ยืนยันการส่งถึงหรือการอ่าน"
        ],
        "items": [
          "ดูเหตุการณ์สำเร็จและล้มเหลวในหน้าประวัติ",
          "ใช้ผู้ให้บริการ SMTP หากต้องการข้อมูลการส่งที่ละเอียดขึ้น",
          "บันทึกภายในไม่ส่งอีเมลบันทึกลูกค้า"
        ]
      },
      {
        "title": "22. ประวัติส่วนกลาง",
        "paragraphs": [
          "เปิด <strong>Mailhilfe Order Notes → ประวัติ</strong> เพื่อดูการสร้างบันทึก การใช้เทมเพลต การประมวลผลและความล้มเหลวของอีเมล หากมีจะแสดงคำสั่งซื้อ เทมเพลต ผู้ใช้ ผู้รับ ประเภทเหตุการณ์ และเวลา"
        ],
        "items": [
          "ใช้สำหรับการสนับสนุน การตรวจสอบ และแก้ปัญหา",
          "แยกจากบันทึกคำสั่งซื้อของ WooCommerce",
          "แสดง 250 รายการล่าสุด"
        ]
      },
      {
        "title": "23. ตัวอย่างด้วยคำสั่งซื้อทดสอบ",
        "paragraphs": [
          "ป้อน ID คำสั่งซื้อ WooCommerce ในตัวแก้ไขเทมเพลต เนื้อหาปัจจุบันรวมถึงการเปลี่ยนแปลงที่ยังไม่บันทึกจะแสดงด้วยข้อมูลคำสั่งซื้อ โดยไม่สร้างบันทึกหรือส่งอีเมล"
        ],
        "items": [
          "ใช้คำสั่งซื้อทดสอบหรือเว็บไซต์ staging",
          "ตรวจสอบค่าที่ขาด รูปแบบ เงื่อนไข และเมตาที่กำหนดเอง",
          "ต้องมีสิทธิ์แก้ไขคำสั่งซื้อที่เลือก"
        ]
      },
      {
        "title": "24. รายการโปรดส่วนตัวและเทมเพลตล่าสุด",
        "paragraphs": [
          "ผู้ใช้แต่ละคนสามารถกำหนดรายการโปรดส่วนตัวในหน้าคำสั่งซื้อ ปลั๊กอินยังบันทึกเทมเพลตสิบรายการล่าสุดที่ใช้สำเร็จต่อผู้ใช้และแสดงไว้ด้านบน รายการโปรดส่วนกลางยังคงใช้ร่วมกัน"
        ],
        "items": [
          "รายการโปรดส่วนตัวไม่กระทบผู้ใช้อื่น",
          "รายการล่าสุดจะอัปเดตหลังเพิ่มบันทึกสำเร็จเท่านั้น",
          "ข้อมูลเก็บเป็นเมตาผู้ใช้ WordPress"
        ]
      },
      {
        "title": "25. หน้าวินิจฉัย",
        "paragraphs": [
          "เปิด <strong>Mailhilfe Order Notes → วินิจฉัย</strong> เพื่อดูเวอร์ชัน WordPress, PHP และ WooCommerce, สถานะ HPOS, อีเมลบันทึกลูกค้า, ภาษา, จำนวนเทมเพลต, แคช และ WP_DEBUG"
        ],
        "items": [
          "ส่งข้อมูลเหล่านี้เมื่อขอความช่วยเหลือ",
          "ไม่แสดงเนื้อหาบันทึกหรือที่อยู่ลูกค้า",
          "นักพัฒนาสามารถเพิ่มแถวผ่านตัวกรองวินิจฉัย"
        ]
      },
      {
        "title": "26. ฮุกและตัวกรองสำหรับนักพัฒนา",
        "paragraphs": [
          "มีฮุกและตัวกรองสำหรับตัวยึดตำแหน่ง ค่า คีย์เมตาที่อนุญาต ผลลัพธ์เทมเพลต เงื่อนไข ตัวอย่าง เนื้อหาสุดท้าย การทำงานก่อน/หลังเพิ่ม ประวัติ และวินิจฉัย ชื่อระบุใน readme.txt"
        ],
        "items": [
          "ตรวจสอบ ทำความสะอาด และ escape ข้อมูลที่กำหนดเอง",
          "ใช้ WooCommerce Order API แทนการเข้าถึงตารางโดยตรง",
          "รักษาความเข้ากันได้กับ HPOS และการจัดเก็บแบบเดิม"
        ]
      }
    ]
  },
  "uk": {
    "title": "Детальна довідка Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Що робить плагін",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Створення шаблону",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Форматування тексту",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Використання заповнювачів",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Перевірка попереднього перегляду",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Внутрішні та клієнтські нотатки",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Обране, пошук і сортування",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Імпорт, експорт і демо-шаблони",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Ролі та права",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Безпека і сумісність HPOS",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Усунення несправностей",
        "paragraphs": [
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу.",
          "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Сторінка налаштувань",
        "paragraphs": [
          "Відкрийте <strong>Mailhilfe Order Notes → Налаштування</strong>, щоб вибрати стандартний тип нотатки, безпечний HTML, показ використання, обране, імпорт JSON і зіставлення мов. Для щоденної роботи використовуйте внутрішні нотатки."
        ],
        "items": []
      },
      {
        "title": "13. Мова шаблону та багатомовні магазини",
        "paragraphs": [
          "Кожному шаблону можна призначити мову. Виберіть <strong>Усі мови</strong> для універсального тексту або конкретну мову для перекладених повідомлень клієнту.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Власні поля і метадані",
        "paragraphs": [
          "Заповнювачі <code>{order_meta:meta_key}</code> і <code>{customer_meta:meta_key}</code> вставляють вибрані метадані. Чутливі ключі, такі як password, token або secret, блокуються.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Дублювання та редакції",
        "paragraphs": [
          "Дублювання створює копію як чернетку. Редакції WordPress допомагають порівнювати та відновлювати старі версії.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Дозволи",
        "paragraphs": [
          "Сторінка <strong>Дозволи</strong> визначає, які ролі керують шаблонами, а які використовують їх у замовленнях.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Попередній перегляд імпорту",
        "paragraphs": [
          "Імпорт JSON спочатку показує, які шаблони буде створено, оновлено або пропущено.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. Стан e-mail нотатки клієнту",
        "paragraphs": [
          "Нотатки клієнту можуть запускати сповіщення WooCommerce електронною поштою, якщо відповідний лист увімкнено. Плагін окремо фіксує створення нотатки та обробку листа. Перед додаванням перевірте редагований попередній перегляд, а результат — на сторінці історії."
        ],
        "items": []
      },
      {
        "title": "19. Рекомендований порядок",
        "paragraphs": [
          "Виберіть шаблон, перевірте попередній перегляд, відредагуйте за потреби, підтвердьте тип нотатки й додайте її.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Умови шаблонів",
        "paragraphs": [
          "Умови визначають, чи доступний шаблон для певного замовлення. Можна обмежити за статусом, способом оплати, способом доставки, країною виставлення рахунку та мінімальною або максимальною сумою. Усі задані умови мають збігатися."
        ],
        "items": [
          "Залиште поле порожнім, якщо воно не повинно обмежувати шаблон.",
          "Використовуйте технічні ідентифікатори оплати та доставки.",
          "Умови перевіряються в інтерфейсі й повторно на сервері."
        ]
      },
      {
        "title": "21. Журнал обробки електронної пошти",
        "paragraphs": [
          "Для клієнтських нотаток плагін записує повідомлення WooCommerce про обробку листа та технічні помилки wp_mail. «Оброблено» означає передачу поштовій системі, але не підтверджує остаточну доставку або прочитання."
        ],
        "items": [
          "Переглядайте оброблені й невдалі події на сторінці історії.",
          "Для точних даних про доставку використовуйте SMTP-сервіс.",
          "Внутрішні нотатки не запускають лист клієнтської нотатки."
        ]
      },
      {
        "title": "22. Центральна історія",
        "paragraphs": [
          "Відкрийте <strong>Mailhilfe Order Notes → Історія</strong>, щоб переглянути створення нотаток, використання шаблонів, обробку та помилки листів. За наявності показуються замовлення, шаблон, користувач, отримувач, тип події та час."
        ],
        "items": [
          "Використовуйте для підтримки, аудиту й діагностики.",
          "Історія відокремлена від нотаток WooCommerce.",
          "Відображаються останні 250 записів."
        ]
      },
      {
        "title": "23. Попередній перегляд із тестовим замовленням",
        "paragraphs": [
          "У редакторі введіть ID замовлення WooCommerce. Поточний вміст, включно з незбереженими змінами, буде показано з даними замовлення без створення нотатки або надсилання листа."
        ],
        "items": [
          "Використовуйте тестове замовлення або staging-сайт.",
          "Перевіряйте відсутні значення, форматування, умови й власні метадані.",
          "Потрібне право редагувати вибране замовлення."
        ]
      },
      {
        "title": "24. Особисте обране та нещодавні шаблони",
        "paragraphs": [
          "Кожен користувач може позначати особисте обране в замовленні. Плагін також зберігає десять останніх успішно використаних шаблонів і піднімає їх вище. Глобальне обране залишається спільним."
        ],
        "items": [
          "Особисте обране не впливає на інших користувачів.",
          "Список нещодавніх оновлюється лише після успішного додавання нотатки.",
          "Дані зберігаються як метадані користувача WordPress."
        ]
      },
      {
        "title": "25. Сторінка діагностики",
        "paragraphs": [
          "Відкрийте <strong>Mailhilfe Order Notes → Діагностика</strong>, щоб побачити версії WordPress, PHP і WooCommerce, статус HPOS, лист клієнтської нотатки, мову, кількість шаблонів, кеш і WP_DEBUG."
        ],
        "items": [
          "Додавайте ці дані до запиту підтримки.",
          "Вміст нотаток і адреси клієнтів не показуються.",
          "Розробники можуть додати рядки фільтром діагностики."
        ]
      },
      {
        "title": "26. Хуки й фільтри для розробників",
        "paragraphs": [
          "Плагін має хуки й фільтри для заповнювачів, значень, дозволених мета-ключів, результатів шаблонів, умов, перегляду, фінального вмісту, дій до/після додавання, історії та діагностики. Назви описані в readme.txt."
        ],
        "items": [
          "Перевіряйте, очищуйте й екрануйте власні дані.",
          "Використовуйте API замовлень WooCommerce замість прямого доступу до таблиць.",
          "Зберігайте сумісність з HPOS і класичним сховищем."
        ]
      }
    ]
  },
  "sv_SE": {
    "title": "Utförlig hjälp för Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Vad tillägget gör",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Skapa en mall",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Formatera text",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Använda platshållare",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Kontrollera förhandsvisning",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Interna notiser och kundnotiser",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Favoriter, sökning och sortering",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Import, export och demomallar",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Roller och behörigheter",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Säkerhet och HPOS-kompatibilitet",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Felsökning",
        "paragraphs": [
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning.",
          "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Inställningssida",
        "paragraphs": [
          "Öppna <strong>Mailhilfe Order Notes → Inställningar</strong> för att välja standardtyp, säker HTML, användningsvisning, favoriter, JSON-import och språkmatchning. Använd interna notiser som standard i det dagliga arbetet."
        ],
        "items": []
      },
      {
        "title": "13. Mallens språk och flerspråkiga butiker",
        "paragraphs": [
          "Varje mall kan ha ett språk. Välj <strong>Alla språk</strong> för generell text eller ett specifikt språk för översatta kundmeddelanden.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Egna fält och metadata",
        "paragraphs": [
          "Platshållarna <code>{order_meta:meta_key}</code> och <code>{customer_meta:meta_key}</code> infogar vald metadata. Känsliga nycklar som password, token eller secret blockeras.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Duplicera och revideringar",
        "paragraphs": [
          "Duplicering skapar en kopia som utkast. WordPress-revideringar hjälper dig jämföra och återställa äldre versioner.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Behörigheter",
        "paragraphs": [
          "Sidan <strong>Behörigheter</strong> anger vilka roller som hanterar mallar och vilka som använder dem i ordrar.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Importförhandsvisning",
        "paragraphs": [
          "JSON-import visar först vilka mallar som skapas, uppdateras eller hoppas över.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. E-poststatus för kundnotis",
        "paragraphs": [
          "Kundanteningar kan utlösa WooCommerce-e-postmeddelanden när motsvarande e-post är aktiverad. Pluginet registrerar att kundanteckningen skapades separat från e-postbehandlingen. Kontrollera den redigerbara förhandsvisningen och resultatet på sidan Historik."
        ],
        "items": []
      },
      {
        "title": "19. Rekommenderat arbetsflöde",
        "paragraphs": [
          "Välj en mall, granska förhandsvisningen, redigera vid behov, kontrollera notistypen och lägg till notisen.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Villkor för mallar",
        "paragraphs": [
          "Villkor avgör om en mall är tillgänglig för en order. Du kan begränsa efter orderstatus, betalningsmetod, leveransmetod, faktureringsland och minsta eller högsta orderbelopp. Alla angivna villkor måste stämma."
        ],
        "items": [
          "Lämna ett fält tomt om det inte ska begränsa mallen.",
          "Använd tekniska ID:n för betalnings- och leveransmetoder.",
          "Villkoren kontrolleras i gränssnittet och igen på servern."
        ]
      },
      {
        "title": "21. Logg för e-postbearbetning",
        "paragraphs": [
          "För kundnotiser registrerar tillägget när WooCommerce rapporterar att e-post har bearbetats samt tekniska wp_mail-fel. ”Bearbetad” betyder överlämnad till e-postsystemet, inte slutlig leverans eller läsning."
        ],
        "items": [
          "Se sidan Historik för bearbetade och misslyckade händelser.",
          "Använd en SMTP-leverantör för mer exakt leveransinformation.",
          "Interna notiser utlöser inte e-post för kundnotis."
        ]
      },
      {
        "title": "22. Central historik",
        "paragraphs": [
          "Öppna <strong>Mailhilfe Order Notes → Historik</strong> för skapade notiser, mallanvändning, e-postbearbetning och fel. Om tillgängligt visas order, mall, användare, mottagare, händelsetyp och tid."
        ],
        "items": [
          "Använd historiken för support, granskning och felsökning.",
          "Den är separat från WooCommerce-orderanteckningar.",
          "De 250 senaste posterna visas."
        ]
      },
      {
        "title": "23. Förhandsvisning med testorder",
        "paragraphs": [
          "Ange ett WooCommerce-order-ID i mallredigeraren. Aktuellt innehåll, även osparade ändringar, visas med orderdata utan att skapa en notis eller skicka e-post."
        ],
        "items": [
          "Använd en testorder eller stagingmiljö.",
          "Kontrollera saknade värden, formatering, villkor och egen metadata.",
          "Du måste ha rätt att redigera den valda ordern."
        ]
      },
      {
        "title": "24. Personliga favoriter och nyligen använda mallar",
        "paragraphs": [
          "Varje användare kan markera personliga favoriter på ordersidan. Tillägget sparar också de tio senast framgångsrikt använda mallarna och placerar dem högre. Globala favoriter är fortsatt gemensamma."
        ],
        "items": [
          "Personliga favoriter påverkar inte andra användare.",
          "Listan uppdateras först efter att en notis har lagts till.",
          "Data sparas som WordPress-användarmetadata."
        ]
      },
      {
        "title": "25. Diagnossida",
        "paragraphs": [
          "Öppna <strong>Mailhilfe Order Notes → Diagnos</strong> för WordPress-, PHP- och WooCommerce-versioner, HPOS, kundnotis-e-post, språk, antal mallar, cache och WP_DEBUG."
        ],
        "items": [
          "Ange dessa uppgifter vid supportförfrågningar.",
          "Inget notisinnehåll eller kundadresser visas.",
          "Utvecklare kan lägga till rader via diagnosfiltret."
        ]
      },
      {
        "title": "26. Hooks och filter för utvecklare",
        "paragraphs": [
          "Tillägget har hooks och filter för platshållare, värden, tillåtna metanycklar, mallresultat, villkor, förhandsvisning, slutligt innehåll, åtgärder före/efter tillägg, historik och diagnos. Namnen dokumenteras i readme.txt."
        ],
        "items": [
          "Validera, sanera och escape:a egna data.",
          "Använd WooCommerce order-API i stället för direkt tabellåtkomst.",
          "Behåll kompatibilitet med HPOS och klassisk lagring."
        ]
      }
    ]
  },
  "da_DK": {
    "title": "Udførlig hjælp til Mailhilfe Order Note Manager for WooCommerce",
    "intro": "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug. Mailhilfe Order Note Manager for WooCommerce.",
    "sections": [
      {
        "title": "1. Hvad pluginet gør",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "2. Opret en skabelon",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "3. Formater teksten",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "4. Brug pladsholdere",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "<code>{order_number}</code>, <code>{customer}</code>, <code>{order_total}</code>",
          "<code>{payment_method}</code>, <code>{shipping_method}</code>, <code>{items}</code>",
          "<code>{billing_email}</code>, <code>{billing_phone}</code>, <code>{site_name}</code>"
        ]
      },
      {
        "title": "5. Kontrollér forhåndsvisning",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "6. Interne noter og kundenoter",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "<strong>Internal note</strong> / <strong>Customer note</strong>",
          "Check customer notes carefully before sending."
        ]
      },
      {
        "title": "7. Favoritter, søgning og sortering",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "8. Import, eksport og demoskabeloner",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "9. Roller og tilladelser",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "<code>manage_mh_order_note_templates</code>",
          "<code>use_mh_order_note_templates</code>"
        ]
      },
      {
        "title": "10. Sikkerhed og HPOS-kompatibilitet",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "11. Fejlfinding",
        "paragraphs": [
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug.",
          "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug."
        ],
        "items": [
          "Use clear titles and categories.",
          "Check the preview before adding the note.",
          "Keep templates short and easy to understand."
        ]
      },
      {
        "title": "12. Indstillingsside",
        "paragraphs": [
          "Åbn <strong>Mailhilfe Order Notes → Indstillinger</strong> for at vælge standardnotetype, sikker HTML, brugsvisning, favoritter, JSON-import og sprogmatchning. Brug interne noter som standard i det daglige arbejde."
        ],
        "items": []
      },
      {
        "title": "13. Skabelonsprog og flersprogede butikker",
        "paragraphs": [
          "Hver skabelon kan have et sprog. Vælg <strong>Alle sprog</strong> til generelle tekster eller et bestemt sprog til oversatte kundebeskeder.",
          "For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible."
        ],
        "items": [
          "Use “All languages” for staff-only notes.",
          "Use a specific language for customer messages.",
          "Test the language selection with a real test order."
        ]
      },
      {
        "title": "14. Brugerdefinerede felter og metadata",
        "paragraphs": [
          "Pladsholderne <code>{order_meta:meta_key}</code> og <code>{customer_meta:meta_key}</code> indsætter valgte metadata. Følsomme nøgler som password, token eller secret blokeres.",
          "Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving."
        ],
        "items": [
          "Example: <code>{order_meta:_tracking_number}</code>.",
          "Example: <code>{order_meta:_billing_vat_id}</code>.",
          "Do not use sensitive data in customer notes."
        ]
      },
      {
        "title": "15. Duplikering og revisioner",
        "paragraphs": [
          "Duplikering opretter en kopi som kladde. WordPress-revisioner hjælper med at sammenligne og gendanne tidligere versioner.",
          "Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish."
        ],
        "items": [
          "The duplicate starts as a draft.",
          "Usage counters are not copied.",
          "Review the draft before publishing."
        ]
      },
      {
        "title": "16. Rettigheder",
        "paragraphs": [
          "Siden <strong>Rettigheder</strong> bestemmer, hvilke roller der administrerer skabeloner, og hvilke der bruger dem i ordrer.",
          "Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates."
        ],
        "items": [
          "Manage templates: create, edit, delete, import and export.",
          "Use templates: add notes in WooCommerce orders.",
          "Administrators keep the required rights."
        ]
      },
      {
        "title": "17. Importforhåndsvisning",
        "paragraphs": [
          "JSON-import viser først, hvilke skabeloner der oprettes, opdateres eller springes over.",
          "Confirm the import only after checking the preview and create an export backup before importing larger template sets."
        ],
        "items": [
          "New templates are listed separately from updates.",
          "Skipped entries should be reviewed.",
          "Import only trusted JSON files."
        ]
      },
      {
        "title": "18. E-mailstatus for kundenote",
        "paragraphs": [
          "Kundenoter kan udløse WooCommerce-e-mails, når den tilsvarende e-mail er aktiveret. Pluginet registrerer oprettelsen af kundenoten separat fra e-mailbehandlingen. Kontrollér den redigerbare forhåndsvisning, og se resultatet på siden Historik."
        ],
        "items": []
      },
      {
        "title": "19. Anbefalet arbejdsgang",
        "paragraphs": [
          "Vælg en skabelon, kontroller forhåndsvisningen, rediger om nødvendigt, bekræft notetypen og tilføj noten.",
          "For new templates, test placeholders and formatting before using them in real customer communication."
        ],
        "items": [
          "Internal notes are for staff information.",
          "Customer notes must be suitable for the customer to read.",
          "Review placeholders after each template change."
        ]
      },
      {
        "title": "20. Betingelser for skabeloner",
        "paragraphs": [
          "Betingelser afgør, om en skabelon er tilgængelig for en ordre. Den kan begrænses efter ordrestatus, betalingsmetode, leveringsmetode, faktureringsland og minimum eller maksimum total. Alle udfyldte betingelser skal passe."
        ],
        "items": [
          "Lad et felt være tomt, hvis det ikke skal begrænse skabelonen.",
          "Brug de tekniske ID’er for betalings- og leveringsmetoder.",
          "Betingelser kontrolleres i grænsefladen og igen på serveren."
        ]
      },
      {
        "title": "21. Log over e-mailbehandling",
        "paragraphs": [
          "For kundenoter registrerer pluginet, når WooCommerce melder e-mailen behandlet, samt tekniske wp_mail-fejl. “Behandlet” bekræfter overførsel til mailsystemet, ikke endelig levering eller læsning."
        ],
        "items": [
          "Se siden Historik for behandlede og mislykkede hændelser.",
          "Brug en SMTP-udbyder for mere præcise leveringsoplysninger.",
          "Interne noter udløser ikke e-mail for kundenoter."
        ]
      },
      {
        "title": "22. Central historik",
        "paragraphs": [
          "Åbn <strong>Mailhilfe Order Notes → Historik</strong> for oprettede noter, skabelonbrug, e-mailbehandling og fejl. Når de findes, vises ordre, skabelon, bruger, modtager, hændelsestype og tidspunkt."
        ],
        "items": [
          "Brug historikken til support, kontrol og fejlfinding.",
          "Den er adskilt fra WooCommerce-ordrenoter.",
          "De seneste 250 poster vises."
        ]
      },
      {
        "title": "23. Forhåndsvisning med testordre",
        "paragraphs": [
          "Indtast et WooCommerce-ordre-ID i skabeloneditoren. Det aktuelle indhold, også ikke-gemte ændringer, vises med ordredata uden at oprette en note eller sende e-mail."
        ],
        "items": [
          "Brug en testordre eller staging-side.",
          "Kontrollér manglende værdier, formatering, betingelser og egne metadata.",
          "Du skal have ret til at redigere den valgte ordre."
        ]
      },
      {
        "title": "24. Personlige favoritter og senest brugte skabeloner",
        "paragraphs": [
          "Hver bruger kan markere personlige favoritter på ordresiden. Pluginet gemmer også de ti senest anvendte skabeloner pr. bruger og viser dem højere. Globale favoritter er fortsat fælles."
        ],
        "items": [
          "Personlige favoritter påvirker ikke andre brugere.",
          "Listen opdateres kun efter en note er tilføjet korrekt.",
          "Data gemmes som WordPress-brugermetadata."
        ]
      },
      {
        "title": "25. Diagnoseside",
        "paragraphs": [
          "Åbn <strong>Mailhilfe Order Notes → Diagnose</strong> for WordPress-, PHP- og WooCommerce-versioner, HPOS, kundenote-e-mail, sprog, antal skabeloner, cache og WP_DEBUG."
        ],
        "items": [
          "Medtag disse oplysninger ved support.",
          "Notetekst og kundeadresser vises ikke.",
          "Udviklere kan tilføje rækker via diagnosefilteret."
        ]
      },
      {
        "title": "26. Hooks og filtre for udviklere",
        "paragraphs": [
          "Pluginet tilbyder hooks og filtre for pladsholdere, værdier, tilladte metanøgler, skabelonresultater, betingelser, forhåndsvisning, endeligt indhold, handlinger før/efter tilføjelse, historik og diagnose. Navnene er dokumenteret i readme.txt."
        ],
        "items": [
          "Validér, rens og escape egne data.",
          "Brug WooCommerce ordre-API’er i stedet for direkte tabeladgang.",
          "Bevar kompatibilitet med HPOS og klassisk lagring."
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
