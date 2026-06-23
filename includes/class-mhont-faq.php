<?php
/**
 * FAQ page in English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the built-in FAQ in English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech.
 */
final class MHONT_FAQ {

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu' ) );
	}

	/**
	 * Adds FAQ submenu.
	 *
	 * @return void
	 */
	public static function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE,
			self::text( 'page_title' ),
			self::text( 'menu_title' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-faq',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Returns FAQ page URL.
	 *
	 * @return string
	 */
	public static function url() {
		return admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE . '&page=mhont-faq' );
	}

	/**
	 * Renders the FAQ page.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_die( esc_html( self::text( 'permission_error' ) ), '', array( 'response' => 403 ) );
		}

		$content = self::get_content();
		?>
		<div class="wrap mhont-help-page mhont-faq-page">
			<h1><?php echo esc_html( $content['title'] ); ?></h1>
			<p class="description"><?php echo esc_html( $content['intro'] ); ?></p>

			<div class="mhont-help-grid">
				<?php foreach ( $content['items'] as $item ) : ?>
					<div class="mhont-tool-card mhont-help-card">
						<h2><?php echo esc_html( $item['question'] ); ?></h2>
						<p><?php echo wp_kses_post( self::format_answer( $item['answer'] ) ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Formats placeholder examples as code safely.
	 *
	 * @param string $answer Answer text.
	 * @return string
	 */
	private static function format_answer( $answer ) {
		$answer = (string) $answer;
		$answer = preg_replace( '/(\{[a-z0-9_.:-]+\})/i', '<code>$1</code>', $answer );
		return is_string( $answer ) ? $answer : '';
	}

	/**
	 * Returns a localized single text.
	 *
	 * @param string $key Text key.
	 * @return string
	 */
	private static function text( $key ) {
		$content = self::get_content();

		if ( 'page_title' === $key ) {
			return $content['title'];
		}

		if ( 'menu_title' === $key ) {
			return $content['menu'];
		}

		if ( 'permission_error' === $key ) {
			return $content['permission'];
		}

		return '';
	}

	/**
	 * Returns FAQ content for current locale.
	 *
	 * @return array<string,mixed>
	 */
	private static function get_content() {
		$sets   = self::get_content_sets();
		$locale = self::get_locale();

		return isset( $sets[ $locale ] ) ? $sets[ $locale ] : $sets['en_US'];
	}

	/**
	 * Gets the best matching supported locale.
	 *
	 * @return string
	 */
	private static function get_locale() {
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
	 * Returns the bundled English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech FAQ sets.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private static function get_content_sets() {
		$json = <<<'JSON'
{
  "en_US": {
    "menu": "FAQ",
    "title": "Frequently Asked Questions",
    "intro": "Answers to common questions about Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "You are not allowed to manage note templates.",
    "items": [
      {
        "question": "What is Mailhilfe Order Note Manager for WooCommerce used for?",
        "answer": "The plugin creates reusable WooCommerce order note templates. Staff can select a template inside an order, preview the replaced placeholders and add the result as an internal note or customer note."
      },
      {
        "question": "Where do I create and manage templates?",
        "answer": "Open Mailhilfe Order Notes in the WordPress admin menu. There you can create, edit, delete, categorize, mark as favorite and sort templates by drag and drop."
      },
      {
        "question": "How do placeholders work?",
        "answer": "Placeholders such as {order_number}, {customer}, {billing_email}, {order_total} and {items} are replaced with real order data in the preview and when the note is added."
      },
      {
        "question": "Can I format template texts?",
        "answer": "Yes. The WordPress editor supports paragraphs, bold and italic text, lists and links. The plugin keeps safe HTML and removes unsafe markup before saving or importing."
      },
      {
        "question": "What is the difference between internal notes and customer notes?",
        "answer": "Internal notes are intended for the shop team only. Customer notes may be visible to the customer and can trigger WooCommerce email notifications, depending on your WooCommerce settings."
      },
      {
        "question": "How should customer notes be handled safely?",
        "answer": "Customer notes can leave the internal admin area. Staff should check placeholders, personal data, wording and the selected note type before adding anything that may reach the customer."
      },
      {
        "question": "How do favorites, search and sorting help?",
        "answer": "Favorites keep important templates easy to reach. Search filters long template lists in the order screen. Drag-and-drop sorting controls the order in which templates are shown."
      },
      {
        "question": "How does JSON import and export work?",
        "answer": "The export creates a JSON file with template titles, content, note types, categories, favorites, sorting and usage data. Import can restore or transfer templates to another shop."
      },
      {
        "question": "How are demo templates installed?",
        "answer": "Open the import/export page and choose the demo template action. Demo templates are created in the matching bundled language, including Persian, Vietnamese and Czech; otherwise the reviewed English set is used."
      },
      {
        "question": "Which roles can use the plugin?",
        "answer": "Administrators and shop managers receive the plugin capabilities automatically. The plugin separates permission to manage templates from permission to use templates in orders."
      },
      {
        "question": "Is the plugin HPOS compatible?",
        "answer": "Yes. The plugin declares WooCommerce HPOS compatibility and uses WooCommerce order APIs instead of direct access to order database tables."
      },
      {
        "question": "Does the plugin add public frontend links?",
        "answer": "No. The plugin works in the WordPress admin and WooCommerce order screens. It does not add powered-by links or public promotional links to the storefront."
      },
      {
        "question": "What should I check if the language is wrong?",
        "answer": "Check Settings > General > Site Language and the language in your user profile. The plugin includes reviewed bundled fallback files for all supported languages, including Persian, Vietnamese and Czech. Other languages should be supplied through reviewed WordPress.org language packs."
      },
      {
        "question": "Can I edit the preview before adding the note?",
        "answer": "Yes. After selecting a template, the preview contains the replaced order data and can be edited before the note is saved. The edited preview is the final note that will be added to the order."
      },
      {
        "question": "How are customer-note creation and email processing recorded?",
        "answer": "Customer notes can trigger WooCommerce email notifications when the corresponding email is enabled. The plugin records creation of the customer note separately from email processing. Review the editable preview before adding the note and use the History page to check the mail handler result."
      },
      {
        "question": "How do template languages work?",
        "answer": "Each template can be assigned to all languages or to a specific bundled language. In multilingual shops the plugin tries to prefer templates matching the order language, user language or site language."
      },
      {
        "question": "Can I use custom order or customer meta fields?",
        "answer": "Yes. Advanced placeholders such as {order_meta:meta_key} and {customer_meta:meta_key} can read custom fields. Sensitive keys containing words such as password, token, secret, session, auth or hash are blocked."
      },
      {
        "question": "Can staff accidentally expose private information?",
        "answer": "The plugin cannot know the business meaning of every placeholder. Always review customer notes before saving them, especially when using meta placeholders or customer-specific data."
      },
      {
        "question": "What happens when HTML formatting is disabled?",
        "answer": "If the setting for HTML formatting is disabled, formatted template content is converted to safe plain text before the note is stored. This is useful for shops that want very simple notes only."
      },
      {
        "question": "How does the import preview protect existing templates?",
        "answer": "The import preview shows how many templates will be created, updated or skipped before the final import is executed. This helps avoid unwanted overwrites."
      },
      {
        "question": "Can I duplicate a template?",
        "answer": "Yes. Use the Duplicate action in the template list. The copy is created as a draft and keeps the content, categories, favorite status and note type, while the usage counter starts at zero."
      },
      {
        "question": "Can I restrict JSON imports?",
        "answer": "Yes. The settings page can disable JSON imports. Even when enabled, imports require the proper capability, nonce verification and a valid JSON file."
      },
      {
        "question": "How are permissions managed?",
        "answer": "Use the permissions page to grant or remove the plugin capabilities for roles. Administrators keep access so the plugin cannot accidentally lock out the site owner."
      },
      {
        "question": "Does the plugin support revisions?",
        "answer": "Yes. Template content is mirrored into the WordPress post content so WordPress revisions can track changes when revisions are available."
      },
      {
        "question": "What should I do before using a template for real customer messages?",
        "answer": "Create the template, select a test order, check the preview, verify all placeholders and confirm the selected note type before adding the note. For critical messages, use an internal note first as a test."
      },
      {
        "question": "Why does the customer note email status matter?",
        "answer": "WooCommerce can send an email when a customer note is added. Check the WooCommerce email configuration and review processing information on the History and Diagnostics pages."
      },
      {
        "question": "Can I use the plugin in a staging shop?",
        "answer": "Yes. JSON export and import are useful for moving templates between staging and production. Check customer note settings after moving templates because WooCommerce email settings may differ."
      },
      {
        "question": "What data is removed during uninstall?",
        "answer": "The uninstall routine removes the plugin's own templates and plugin options. WooCommerce order notes already added to orders belong to WooCommerce order history and are not removed by uninstalling the plugin."
      },
      {
        "question": "How should I report a problem?",
        "answer": "Document the WordPress version, WooCommerce version, whether HPOS is enabled, the active language, the selected template and the exact steps that caused the problem."
      },
      {
        "question": "Template conditions",
        "answer": "Template conditions decide whether a template is available for a particular order. You can restrict templates by order status, payment method, shipping method, billing country and minimum or maximum order total. All configured conditions must match. Leave a field empty when that condition should not restrict the template."
      },
      {
        "question": "Email processing log",
        "answer": "For customer notes, the plugin records when WooCommerce reports the customer-note email as processed and also records technical wp_mail errors. A processed event confirms that WordPress/WooCommerce handed the message to the mail system; it does not prove final delivery or that the customer read it. Check the History page for processed and failed email events."
      },
      {
        "question": "Central history",
        "answer": "Open <strong>Mailhilfe Order Notes → History</strong> to review recent note creation, template usage, email processing and email failures. Entries include the order, template, user, recipient, event type and time when available. Use the history for support, auditing and troubleshooting."
      },
      {
        "question": "Test-order preview",
        "answer": "In the template editor, enter a WooCommerce order ID in the test preview area. The current editor content, including unsaved changes, is rendered with data from that order without creating a note or sending an email. Use a staging order or a non-critical test order."
      },
      {
        "question": "Personal favorites and recently used templates",
        "answer": "Each administrator can mark personal favorites in the order screen. The plugin also stores the ten most recently used templates for each user and gives them a higher position in the selection. Global favorites remain shared with all users. Personal favorites do not change another user’s list."
      },
      {
        "question": "Diagnostics page",
        "answer": "Open <strong>Mailhilfe Order Notes → Diagnostics</strong> to view technical information such as WordPress, PHP and WooCommerce versions, HPOS status, customer-note email status, locale, published-template count, cache status and WP_DEBUG. Copy the diagnostic values when requesting support."
      },
      {
        "question": "Developer hooks and filters",
        "answer": "The plugin provides hooks and filters for placeholders, placeholder values, allowed meta keys, template results, conditions, preview content, final note content, actions before and after adding a note, history records and diagnostics. Hook names and parameters are documented in readme.txt. Validate, sanitize and escape all custom data."
      }
    ]
  },
  "de_DE": {
    "menu": "FAQ",
    "title": "Häufig gestellte Fragen",
    "intro": "Antworten auf häufige Fragen zu Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Du bist nicht berechtigt, Notizvorlagen zu verwalten.",
    "items": [
      {
        "question": "Wofür wird Mailhilfe Order Note Manager for WooCommerce verwendet?",
        "answer": "Das Plugin erstellt wiederverwendbare WooCommerce-Bestellnotiz-Vorlagen. Mitarbeiter können eine Vorlage direkt in einer Bestellung auswählen, die ersetzten Platzhalter in der Vorschau prüfen und das Ergebnis als interne Notiz oder Kundennotiz hinzufügen."
      },
      {
        "question": "Wo erstelle und verwalte ich Vorlagen?",
        "answer": "Öffne im WordPress-Adminmenü Bestellnotiz-Vorlagen. Dort kannst du Vorlagen erstellen, bearbeiten, löschen, kategorisieren, als Favorit markieren und per Drag-and-Drop sortieren."
      },
      {
        "question": "Wie funktionieren Platzhalter?",
        "answer": "Platzhalter wie {order_number}, {customer}, {billing_email}, {order_total} und {items} werden in der Vorschau und beim Hinzufügen der Notiz durch echte Bestelldaten ersetzt."
      },
      {
        "question": "Kann ich Vorlagentexte formatieren?",
        "answer": "Ja. Der WordPress-Editor unterstützt Absätze, Fett- und Kursivschrift, Listen und Links. Das Plugin übernimmt sicheres HTML und entfernt unsichere Auszeichnungen vor dem Speichern oder Importieren."
      },
      {
        "question": "Was ist der Unterschied zwischen internen Notizen und Kundennotizen?",
        "answer": "Interne Notizen sind nur für das Shop-Team gedacht. Kundennotizen können für Kunden sichtbar sein und je nach WooCommerce-Einstellungen E-Mail-Benachrichtigungen auslösen."
      },
      {
        "question": "Wie sollten Kundennotizen sicher verwendet werden?",
        "answer": "Kundennotizen können den internen Adminbereich verlassen. Mitarbeiter sollten deshalb Platzhalter, personenbezogene Daten, Formulierungen und den ausgewählten Notiztyp prüfen, bevor etwas an Kunden gelangt."
      },
      {
        "question": "Wie helfen Favoriten, Suche und Sortierung?",
        "answer": "Favoriten halten wichtige Vorlagen schnell erreichbar. Die Suche filtert lange Vorlagenlisten in der Bestellung. Die Drag-and-Drop-Sortierung bestimmt die Reihenfolge der angezeigten Vorlagen."
      },
      {
        "question": "Wie funktioniert Import und Export als JSON?",
        "answer": "Der Export erzeugt eine JSON-Datei mit Titeln, Inhalten, Notiztypen, Kategorien, Favoriten, Sortierung und Nutzungsdaten. Der Import kann Vorlagen wiederherstellen oder in einen anderen Shop übertragen."
      },
      {
        "question": "Wie werden Demo-Vorlagen installiert?",
        "answer": "Öffne die Import-/Exportseite und wähle die Demo-Vorlagen-Aktion. Demo-Vorlagen werden in der passenden gebündelten Sprache, einschließlich Persisch, erstellt. Andernfalls wird der geprüfte englische Vorlagensatz verwendet."
      },
      {
        "question": "Welche Rollen können das Plugin verwenden?",
        "answer": "Administratoren und Shop-Manager erhalten die Plugin-Rechte automatisch. Das Plugin trennt das Recht zur Verwaltung von Vorlagen vom Recht zur Nutzung in Bestellungen."
      },
      {
        "question": "Ist das Plugin HPOS-kompatibel?",
        "answer": "Ja. Das Plugin deklariert die WooCommerce-HPOS-Kompatibilität und nutzt WooCommerce-Bestell-APIs statt direkter Zugriffe auf Bestelltabellen."
      },
      {
        "question": "Fügt das Plugin öffentliche Frontend-Links ein?",
        "answer": "Nein. Das Plugin arbeitet im WordPress-Adminbereich und in WooCommerce-Bestellungen. Es fügt keine Powered-by-Links oder öffentlichen Werbelinks im Shop-Frontend ein."
      },
      {
        "question": "Was prüfe ich, wenn die Sprache falsch ist?",
        "answer": "Prüfe Einstellungen > Allgemein > Sprache der Website und die Sprache in deinem Benutzerprofil. Das Plugin enthält geprüfte Übersetzungen für alle unterstützten Sprachen, einschließlich Persisch. Weitere Sprachen sollten über geprüfte WordPress.org-Sprachpakete installiert werden."
      },
      {
        "question": "Kann ich die Vorschau vor dem Hinzufügen der Notiz bearbeiten?",
        "answer": "Ja. Nach Auswahl einer Vorlage enthält die Vorschau die ersetzten Bestelldaten und kann vor dem Speichern bearbeitet werden. Die bearbeitete Vorschau ist die endgültige Notiz, die zur Bestellung hinzugefügt wird."
      },
      {
        "question": "Wie werden die Erstellung einer Kundennotiz und die E-Mail-Verarbeitung protokolliert?",
        "answer": "Kundennotizen können WooCommerce-E-Mail-Benachrichtigungen auslösen, wenn die entsprechende E-Mail aktiviert ist. Das Plugin protokolliert die Erstellung der Kundennotiz getrennt von der E-Mail-Verarbeitung. Prüfe vor dem Hinzufügen die bearbeitbare Vorschau und kontrolliere das Ergebnis der Mailverarbeitung auf der Seite „Verlauf“."
      },
      {
        "question": "Wie funktioniert die Vorlagensprache?",
        "answer": "Jede Vorlage kann für alle Sprachen oder für eine bestimmte Sprache festgelegt werden. In mehrsprachigen Shops versucht das Plugin, Vorlagen passend zur Bestellsprache, Benutzersprache oder Website-Sprache zu bevorzugen."
      },
      {
        "question": "Kann ich eigene Bestell- oder Kunden-Metafelder verwenden?",
        "answer": "Ja. Erweiterte Platzhalter wie {order_meta:meta_key} und {customer_meta:meta_key} können eigene Felder auslesen. Sensible Schlüssel mit Begriffen wie password, token, secret, session, auth oder hash werden blockiert."
      },
      {
        "question": "Können Mitarbeiter versehentlich private Informationen veröffentlichen?",
        "answer": "Das Plugin kann die fachliche Bedeutung jedes Platzhalters nicht kennen. Prüfe Kundennotizen daher immer vor dem Speichern, besonders bei Meta-Platzhaltern oder kundenspezifischen Daten."
      },
      {
        "question": "Was passiert, wenn HTML-Formatierung deaktiviert ist?",
        "answer": "Wenn die Einstellung für HTML-Formatierung deaktiviert ist, wird formatierter Vorlageninhalt vor dem Speichern in sicheren Klartext umgewandelt. Das ist sinnvoll für Shops, die nur sehr einfache Notizen wünschen."
      },
      {
        "question": "Wie schützt die Import-Vorschau bestehende Vorlagen?",
        "answer": "Die Import-Vorschau zeigt vor dem endgültigen Import, wie viele Vorlagen neu erstellt, aktualisiert oder übersprungen werden. Dadurch werden unbeabsichtigte Überschreibungen vermieden."
      },
      {
        "question": "Kann ich eine Vorlage duplizieren?",
        "answer": "Ja. Verwende die Aktion Duplizieren in der Vorlagenliste. Die Kopie wird als Entwurf erstellt und übernimmt Inhalt, Kategorien, Favorit und Notiztyp; der Nutzungszähler beginnt bei null."
      },
      {
        "question": "Kann ich JSON-Importe einschränken?",
        "answer": "Ja. Auf der Einstellungsseite können JSON-Importe deaktiviert werden. Auch bei aktivierter Funktion benötigen Importe die passende Berechtigung, Nonce-Prüfung und eine gültige JSON-Datei."
      },
      {
        "question": "Wie werden Berechtigungen verwaltet?",
        "answer": "Über die Berechtigungsseite können Plugin-Rechte für Rollen vergeben oder entfernt werden. Administratoren behalten Zugriff, damit der Website-Betreiber sich nicht versehentlich aussperrt."
      },
      {
        "question": "Unterstützt das Plugin Revisionen?",
        "answer": "Ja. Der Vorlageninhalt wird zusätzlich im WordPress-Beitragsinhalt gespiegelt, damit WordPress-Revisionen Änderungen nachverfolgen können, wenn Revisionen verfügbar sind."
      },
      {
        "question": "Was sollte ich vor echten Kundennachrichten prüfen?",
        "answer": "Erstelle die Vorlage, wähle eine Testbestellung, prüfe die Vorschau, kontrolliere alle Platzhalter und den ausgewählten Notiztyp und füge erst dann die Notiz hinzu. Bei kritischen Nachrichten zuerst intern testen."
      },
      {
        "question": "Warum ist der E-Mail-Status der Kundennotiz wichtig?",
        "answer": "WooCommerce kann eine E-Mail senden, wenn eine Kundennotiz hinzugefügt wird. Prüfe die WooCommerce-E-Mail-Konfiguration sowie die Informationen im Verlauf und in der Diagnose."
      },
      {
        "question": "Kann ich das Plugin in einem Staging-Shop verwenden?",
        "answer": "Ja. JSON-Export und Import eignen sich gut, um Vorlagen zwischen Staging und Live-Shop zu übertragen. Prüfe danach die WooCommerce-E-Mail-Einstellungen, da diese je Shop unterschiedlich sein können."
      },
      {
        "question": "Welche Daten werden bei der Deinstallation entfernt?",
        "answer": "Die Deinstallation entfernt die eigenen Vorlagen und Plugin-Optionen. Bereits zu Bestellungen hinzugefügte WooCommerce-Bestellnotizen gehören zur Bestellhistorie und werden durch die Deinstallation nicht gelöscht."
      },
      {
        "question": "Wie sollte ich ein Problem melden?",
        "answer": "Notiere WordPress-Version, WooCommerce-Version, ob HPOS aktiv ist, die aktive Sprache, die ausgewählte Vorlage und die genauen Schritte, die zum Problem geführt haben."
      },
      {
        "question": "Bedingungen für Vorlagen",
        "answer": "Vorlagenbedingungen bestimmen, ob eine Vorlage für eine bestimmte Bestellung verfügbar ist. Vorlagen können nach Bestellstatus, Zahlungsart, Versandart, Rechnungsland sowie Mindest- und Höchstbestellwert eingeschränkt werden. Alle eingetragenen Bedingungen müssen erfüllt sein. Lass ein Feld leer, wenn diese Bedingung die Vorlage nicht einschränken soll."
      },
      {
        "question": "Protokollierung der E-Mail-Verarbeitung",
        "answer": "Bei Kundennotizen protokolliert das Plugin, wenn WooCommerce die Kundennotiz-E-Mail als verarbeitet meldet. Technische Fehler von wp_mail werden ebenfalls erfasst. „Verarbeitet“ bestätigt nur die Übergabe an das E-Mail-System und nicht die endgültige Zustellung oder das Lesen durch den Kunden. Prüfe auf der Seite „Verlauf“ verarbeitete und fehlgeschlagene E-Mail-Ereignisse."
      },
      {
        "question": "Zentraler Verlauf",
        "answer": "Öffne <strong>Bestellnotiz-Vorlagen → Verlauf</strong>, um das Erstellen von Notizen, die Vorlagennutzung, verarbeitete E-Mails und E-Mail-Fehler zentral einzusehen. Soweit vorhanden werden Bestellung, Vorlage, Benutzer, Empfänger, Ereignistyp und Zeitpunkt angezeigt. Nutze den Verlauf für Support, Nachvollziehbarkeit und Fehlersuche."
      },
      {
        "question": "Vorschau mit Testbestellung",
        "answer": "Gib im Vorlageneditor im Bereich für die Testvorschau eine WooCommerce-Bestell-ID ein. Der aktuelle Editorinhalt einschließlich noch nicht gespeicherter Änderungen wird mit den Daten dieser Bestellung dargestellt, ohne eine Notiz anzulegen oder eine E-Mail zu senden. Verwende eine Testbestellung oder eine unkritische Bestellung in einer Staging-Umgebung."
      },
      {
        "question": "Persönliche Favoriten und zuletzt verwendete Vorlagen",
        "answer": "Jeder Benutzer kann in der Bestellansicht persönliche Favoriten markieren. Zusätzlich speichert das Plugin pro Benutzer die zehn zuletzt erfolgreich verwendeten Vorlagen und zeigt sie weiter oben an. Globale Favoriten bleiben weiterhin für alle Benutzer gemeinsam. Persönliche Favoriten verändern die Auswahl anderer Mitarbeiter nicht."
      },
      {
        "question": "Diagnose-Seite",
        "answer": "Unter <strong>Bestellnotiz-Vorlagen → Diagnose</strong> findest du technische Angaben wie WordPress-, PHP- und WooCommerce-Version, HPOS-Status, Status der Kundennotiz-E-Mail, Sprache, Anzahl veröffentlichter Vorlagen, Cache-Status und WP_DEBUG. Gib diese Werte bei Supportanfragen mit an."
      },
      {
        "question": "Hooks und Filter für Entwickler",
        "answer": "Das Plugin bietet Hooks und Filter für Platzhalter, Platzhalterwerte, erlaubte Meta-Schlüssel, Vorlagenergebnisse, Bedingungen, Vorschauinhalt, endgültigen Notizinhalt, Aktionen vor und nach dem Hinzufügen, Verlaufseinträge und Diagnosewerte. Namen und Parameter sind in der readme.txt dokumentiert. Validiere, bereinige und maskiere alle eigenen Daten."
      }
    ]
  },
  "de_DE_formal": {
    "menu": "FAQ",
    "title": "Häufig gestellte Fragen",
    "intro": "Antworten auf häufige Fragen zu Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Sie sind nicht berechtigt, Notizvorlagen zu verwalten.",
    "items": [
      {
        "question": "Wofür wird Mailhilfe Order Note Manager for WooCommerce verwendet?",
        "answer": "Das Plugin erstellt wiederverwendbare WooCommerce-Bestellnotiz-Vorlagen. Mitarbeiter können eine Vorlage direkt in einer Bestellung auswählen, die ersetzten Platzhalter in der Vorschau prüfen und das Ergebnis als interne Notiz oder Kundennotiz hinzufügen."
      },
      {
        "question": "Wo erstelle und verwalte ich Vorlagen?",
        "answer": "Öffnen Sie im WordPress-Adminmenü Bestellnotiz-Vorlagen. Dort können Sie Vorlagen erstellen, bearbeiten, löschen, kategorisieren, als Favorit markieren und per Drag-and-Drop sortieren."
      },
      {
        "question": "Wie funktionieren Platzhalter?",
        "answer": "Platzhalter wie {order_number}, {customer}, {billing_email}, {order_total} und {items} werden in der Vorschau und beim Hinzufügen der Notiz durch echte Bestelldaten ersetzt."
      },
      {
        "question": "Kann ich Vorlagentexte formatieren?",
        "answer": "Ja. Der WordPress-Editor unterstützt Absätze, Fett- und Kursivschrift, Listen und Links. Das Plugin übernimmt sicheres HTML und entfernt unsichere Auszeichnungen vor dem Speichern oder Importieren."
      },
      {
        "question": "Was ist der Unterschied zwischen internen Notizen und Kundennotizen?",
        "answer": "Interne Notizen sind nur für das Shop-Team gedacht. Kundennotizen können für Kunden sichtbar sein und je nach WooCommerce-Einstellungen E-Mail-Benachrichtigungen auslösen."
      },
      {
        "question": "Wie sollten Kundennotizen sicher verwendet werden?",
        "answer": "Kundennotizen können den internen Adminbereich verlassen. Mitarbeiter sollten deshalb Platzhalter, personenbezogene Daten, Formulierungen und den ausgewählten Notiztyp prüfen, bevor etwas an Kunden gelangt."
      },
      {
        "question": "Wie helfen Favoriten, Suche und Sortierung?",
        "answer": "Favoriten halten wichtige Vorlagen schnell erreichbar. Die Suche filtert lange Vorlagenlisten in der Bestellung. Die Drag-and-Drop-Sortierung bestimmt die Reihenfolge der angezeigten Vorlagen."
      },
      {
        "question": "Wie funktioniert Import und Export als JSON?",
        "answer": "Der Export erzeugt eine JSON-Datei mit Titeln, Inhalten, Notiztypen, Kategorien, Favoriten, Sortierung und Nutzungsdaten. Der Import kann Vorlagen wiederherstellen oder in einen anderen Shop übertragen."
      },
      {
        "question": "Wie werden Demo-Vorlagen installiert?",
        "answer": "Öffnen Sie die Import-/Exportseite und wählen Sie die Demo-Vorlagen-Aktion. Demo-Vorlagen werden in der passenden gebündelten Sprache, einschließlich Persisch, erstellt. Andernfalls wird der geprüfte englische Vorlagensatz verwendet."
      },
      {
        "question": "Welche Rollen können das Plugin verwenden?",
        "answer": "Administratoren und Shop-Manager erhalten die Plugin-Rechte automatisch. Das Plugin trennt das Recht zur Verwaltung von Vorlagen vom Recht zur Nutzung in Bestellungen."
      },
      {
        "question": "Ist das Plugin HPOS-kompatibel?",
        "answer": "Ja. Das Plugin deklariert die WooCommerce-HPOS-Kompatibilität und nutzt WooCommerce-Bestell-APIs statt direkter Zugriffe auf Bestelltabellen."
      },
      {
        "question": "Fügt das Plugin öffentliche Frontend-Links ein?",
        "answer": "Nein. Das Plugin arbeitet im WordPress-Adminbereich und in WooCommerce-Bestellungen. Es fügt keine Powered-by-Links oder öffentlichen Werbelinks im Shop-Frontend ein."
      },
      {
        "question": "Was prüfe ich, wenn die Sprache falsch ist?",
        "answer": "Prüfen Sie Einstellungen > Allgemein > Sprache der Website und die Sprache in Ihrem Benutzerprofil. Das Plugin enthält geprüfte Übersetzungen für alle unterstützten Sprachen, einschließlich Persisch. Weitere Sprachen sollten über geprüfte WordPress.org-Sprachpakete installiert werden."
      },
      {
        "question": "Kann ich die Vorschau vor dem Hinzufügen der Notiz bearbeiten?",
        "answer": "Ja. Nach Auswahl einer Vorlage enthält die Vorschau die ersetzten Bestelldaten und kann vor dem Speichern bearbeitet werden. Die bearbeitete Vorschau ist die endgültige Notiz, die zur Bestellung hinzugefügt wird."
      },
      {
        "question": "Wie werden die Erstellung einer Kundennotiz und die E-Mail-Verarbeitung protokolliert?",
        "answer": "Kundennotizen können WooCommerce-E-Mail-Benachrichtigungen auslösen, wenn die entsprechende E-Mail aktiviert ist. Das Plugin protokolliert die Erstellung der Kundennotiz getrennt von der E-Mail-Verarbeitung. Prüfen Sie vor dem Hinzufügen die bearbeitbare Vorschau und kontrollieren Sie das Ergebnis der Mailverarbeitung auf der Seite „Verlauf“."
      },
      {
        "question": "Wie funktioniert die Vorlagensprache?",
        "answer": "Jede Vorlage kann für alle Sprachen oder für eine bestimmte Sprache festgelegt werden. In mehrsprachigen Shops versucht das Plugin, Vorlagen passend zur Bestellsprache, Benutzersprache oder Website-Sprache zu bevorzugen."
      },
      {
        "question": "Kann ich eigene Bestell- oder Kunden-Metafelder verwenden?",
        "answer": "Ja. Erweiterte Platzhalter wie {order_meta:meta_key} und {customer_meta:meta_key} können eigene Felder auslesen. Sensible Schlüssel mit Begriffen wie password, token, secret, session, auth oder hash werden blockiert."
      },
      {
        "question": "Können Mitarbeiter versehentlich private Informationen veröffentlichen?",
        "answer": "Das Plugin kann die fachliche Bedeutung jedes Platzhalters nicht kennen. Prüfen Sie Kundennotizen daher immer vor dem Speichern, besonders bei Meta-Platzhaltern oder kundenspezifischen Daten."
      },
      {
        "question": "Was passiert, wenn HTML-Formatierung deaktiviert ist?",
        "answer": "Wenn die Einstellung für HTML-Formatierung deaktiviert ist, wird formatierter Vorlageninhalt vor dem Speichern in sicheren Klartext umgewandelt. Das ist sinnvoll für Shops, die nur sehr einfache Notizen wünschen."
      },
      {
        "question": "Wie schützt die Import-Vorschau bestehende Vorlagen?",
        "answer": "Die Import-Vorschau zeigt vor dem endgültigen Import, wie viele Vorlagen neu erstellt, aktualisiert oder übersprungen werden. Dadurch werden unbeabsichtigte Überschreibungen vermieden."
      },
      {
        "question": "Kann ich eine Vorlage duplizieren?",
        "answer": "Ja. Verwenden Sie die Aktion Duplizieren in der Vorlagenliste. Die Kopie wird als Entwurf erstellt und übernimmt Inhalt, Kategorien, Favorit und Notiztyp; der Nutzungszähler beginnt bei null."
      },
      {
        "question": "Kann ich JSON-Importe einschränken?",
        "answer": "Ja. Auf der Einstellungsseite können JSON-Importe deaktiviert werden. Auch bei aktivierter Funktion benötigen Importe die passende Berechtigung, Nonce-Prüfung und eine gültige JSON-Datei."
      },
      {
        "question": "Wie werden Berechtigungen verwaltet?",
        "answer": "Über die Berechtigungsseite können Plugin-Rechte für Rollen vergeben oder entfernt werden. Administratoren behalten Zugriff, damit der Website-Betreiber sich nicht versehentlich aussperrt."
      },
      {
        "question": "Unterstützt das Plugin Revisionen?",
        "answer": "Ja. Der Vorlageninhalt wird zusätzlich im WordPress-Beitragsinhalt gespiegelt, damit WordPress-Revisionen Änderungen nachverfolgen können, wenn Revisionen verfügbar sind."
      },
      {
        "question": "Was sollte ich vor echten Kundennachrichten prüfen?",
        "answer": "Erstellen Sie die Vorlage, wählen Sie eine Testbestellung, prüfen Sie die Vorschau, kontrollieren Sie alle Platzhalter und den ausgewählten Notiztyp und fügen Sie erst dann die Notiz hinzu. Bei kritischen Nachrichten zuerst intern testen."
      },
      {
        "question": "Warum ist der E-Mail-Status der Kundennotiz wichtig?",
        "answer": "WooCommerce kann eine E-Mail senden, wenn eine Kundennotiz hinzugefügt wird. Prüfen Sie die WooCommerce-E-Mail-Konfiguration sowie die Informationen im Verlauf und in der Diagnose."
      },
      {
        "question": "Kann ich das Plugin in einem Staging-Shop verwenden?",
        "answer": "Ja. JSON-Export und Import eignen sich gut, um Vorlagen zwischen Staging und Live-Shop zu übertragen. Prüfen Sie danach die WooCommerce-E-Mail-Einstellungen, da diese je Shop unterschiedlich sein können."
      },
      {
        "question": "Welche Daten werden bei der Deinstallation entfernt?",
        "answer": "Die Deinstallation entfernt die eigenen Vorlagen und Plugin-Optionen. Bereits zu Bestellungen hinzugefügte WooCommerce-Bestellnotizen gehören zur Bestellhistorie und werden durch die Deinstallation nicht gelöscht."
      },
      {
        "question": "Wie sollte ich ein Problem melden?",
        "answer": "Notieren Sie WordPress-Version, WooCommerce-Version, ob HPOS aktiv ist, die aktive Sprache, die ausgewählte Vorlage und die genauen Schritte, die zum Problem geführt haben."
      },
      {
        "question": "Bedingungen für Vorlagen",
        "answer": "Vorlagenbedingungen bestimmen, ob eine Vorlage für eine bestimmte Bestellung verfügbar ist. Vorlagen können nach Bestellstatus, Zahlungsart, Versandart, Rechnungsland sowie Mindest- und Höchstbestellwert eingeschränkt werden. Alle eingetragenen Bedingungen müssen erfüllt sein. Lassen Sie ein Feld leer, wenn diese Bedingung die Vorlage nicht einschränken soll."
      },
      {
        "question": "Protokollierung der E-Mail-Verarbeitung",
        "answer": "Bei Kundennotizen protokolliert das Plugin, wenn WooCommerce die Kundennotiz-E-Mail als verarbeitet meldet. Technische Fehler von wp_mail werden ebenfalls erfasst. „Verarbeitet“ bestätigt nur die Übergabe an das E-Mail-System und nicht die endgültige Zustellung oder das Lesen durch den Kunden. Prüfen Sie auf der Seite „Verlauf“ verarbeitete und fehlgeschlagene E-Mail-Ereignisse."
      },
      {
        "question": "Zentraler Verlauf",
        "answer": "Öffnen Sie <strong>Bestellnotiz-Vorlagen → Verlauf</strong>, um das Erstellen von Notizen, die Vorlagennutzung, verarbeitete E-Mails und E-Mail-Fehler zentral einzusehen. Soweit vorhanden werden Bestellung, Vorlage, Benutzer, Empfänger, Ereignistyp und Zeitpunkt angezeigt. Nutzen Sie den Verlauf für Support, Nachvollziehbarkeit und Fehlersuche."
      },
      {
        "question": "Vorschau mit Testbestellung",
        "answer": "Geben Sie im Vorlageneditor im Bereich für die Testvorschau eine WooCommerce-Bestell-ID ein. Der aktuelle Editorinhalt einschließlich noch nicht gespeicherter Änderungen wird mit den Daten dieser Bestellung dargestellt, ohne eine Notiz anzulegen oder eine E-Mail zu senden. Verwenden Sie eine Testbestellung oder eine unkritische Bestellung in einer Staging-Umgebung."
      },
      {
        "question": "Persönliche Favoriten und zuletzt verwendete Vorlagen",
        "answer": "Jeder Benutzer kann in der Bestellansicht persönliche Favoriten markieren. Zusätzlich speichert das Plugin pro Benutzer die zehn zuletzt erfolgreich verwendeten Vorlagen und zeigt sie weiter oben an. Globale Favoriten bleiben weiterhin für alle Benutzer gemeinsam. Persönliche Favoriten verändern die Auswahl anderer Mitarbeiter nicht."
      },
      {
        "question": "Diagnose-Seite",
        "answer": "Unter <strong>Bestellnotiz-Vorlagen → Diagnose</strong> finden Sie technische Angaben wie WordPress-, PHP- und WooCommerce-Version, HPOS-Status, Status der Kundennotiz-E-Mail, Sprache, Anzahl veröffentlichter Vorlagen, Cache-Status und WP_DEBUG. Geben Sie diese Werte bei Supportanfragen mit an."
      },
      {
        "question": "Hooks und Filter für Entwickler",
        "answer": "Das Plugin bietet Hooks und Filter für Platzhalter, Platzhalterwerte, erlaubte Meta-Schlüssel, Vorlagenergebnisse, Bedingungen, Vorschauinhalt, endgültigen Notizinhalt, Aktionen vor und nach dem Hinzufügen, Verlaufseinträge und Diagnosewerte. Namen und Parameter sind in der readme.txt dokumentiert. Validieren, bereinigen und maskieren Sie alle eigenen Daten."
      }
    ]
  },
  "es_ES": {
    "menu": "FAQ",
    "title": "Preguntas frecuentes",
    "intro": "Respuestas a preguntas habituales sobre Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "No tiene permisos para gestionar plantillas de notas.",
    "items": [
      {
        "question": "¿Para qué sirve Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "El plugin crea plantillas reutilizables para notas de pedido de WooCommerce. El personal puede seleccionar una plantilla dentro de un pedido, revisar la vista previa con los marcadores de posición sustituidos y añadir el resultado como nota interna o nota para el cliente."
      },
      {
        "question": "¿Dónde se crean y gestionan las plantillas?",
        "answer": "Abra Notas de pedido de Mailhilfe en el menú de administración de WordPress. Allí puede crear, editar, eliminar y categorizar plantillas, marcarlas como favoritas y ordenarlas mediante arrastrar y soltar."
      },
      {
        "question": "¿Cómo funcionan los marcadores de posición?",
        "answer": "Los marcadores de posición como {order_number}, {customer}, {billing_email}, {order_total} y {items} se sustituyen por datos reales del pedido en la vista previa y al añadir la nota."
      },
      {
        "question": "¿Puedo dar formato a los textos de las plantillas?",
        "answer": "Sí. El editor de WordPress admite párrafos, negrita, cursiva, listas y enlaces. El plugin conserva el HTML seguro y elimina el código no seguro antes de guardar o importar."
      },
      {
        "question": "¿Cuál es la diferencia entre las notas internas y las notas para clientes?",
        "answer": "Las notas internas están destinadas únicamente al equipo de la tienda. Las notas para clientes pueden ser visibles para el cliente y activar notificaciones por correo electrónico de WooCommerce, según los ajustes de WooCommerce."
      },
      {
        "question": "¿Cómo deben utilizarse de forma segura las notas para clientes?",
        "answer": "Las notas para clientes pueden salir del área interna de administración. El personal debe comprobar los marcadores de posición, los datos personales, la redacción y el tipo de nota seleccionado antes de añadir cualquier contenido que pueda llegar al cliente."
      },
      {
        "question": "¿Cómo ayudan los favoritos, la búsqueda y la ordenación?",
        "answer": "Los favoritos permiten acceder fácilmente a las plantillas importantes. La búsqueda filtra listas largas de plantillas en la pantalla del pedido. La ordenación mediante arrastrar y soltar determina el orden en que se muestran las plantillas."
      },
      {
        "question": "¿Cómo funcionan la importación y la exportación JSON?",
        "answer": "La exportación crea un archivo JSON con títulos, contenidos, tipos de nota, categorías, favoritos, ordenación y datos de uso de las plantillas. La importación puede restaurar plantillas o transferirlas a otra tienda."
      },
      {
        "question": "¿Cómo se instalan las plantillas de demostración?",
        "answer": "Abra la página de importación y exportación y elija la acción para instalar plantillas de demostración. Las plantillas se crean en el idioma incluido correspondiente, incluido el persa; de lo contrario se utiliza el conjunto revisado en inglés."
      },
      {
        "question": "¿Qué roles pueden utilizar el plugin?",
        "answer": "Los administradores y gestores de tienda reciben automáticamente las capacidades del plugin. El plugin separa el permiso para gestionar plantillas del permiso para utilizarlas en pedidos."
      },
      {
        "question": "¿Es compatible el plugin con HPOS?",
        "answer": "Sí. El plugin declara compatibilidad con HPOS de WooCommerce y utiliza las API de pedidos de WooCommerce en lugar de acceder directamente a las tablas de pedidos de la base de datos."
      },
      {
        "question": "¿Añade el plugin enlaces públicos en la tienda?",
        "answer": "No. El plugin funciona en la administración de WordPress y en las pantallas de pedidos de WooCommerce. No añade enlaces de atribución ni enlaces promocionales públicos a la tienda."
      },
      {
        "question": "¿Qué debo comprobar si aparece un idioma incorrecto?",
        "answer": "Compruebe Ajustes > Generales > Idioma del sitio y el idioma de su perfil de usuario. El plugin incluye traducciones revisadas para todos los idiomas compatibles, incluido el persa. Los demás idiomas deben utilizar paquetes de idioma revisados de WordPress.org."
      },
      {
        "question": "¿Puedo editar la vista previa antes de añadir la nota?",
        "answer": "Sí. Después de seleccionar una plantilla, la vista previa contiene los datos del pedido ya sustituidos y puede editarse antes de guardar la nota. La vista previa editada es la nota final que se añadirá al pedido."
      },
      {
        "question": "¿Cómo se registran la creación de notas para clientes y el procesamiento del correo electrónico?",
        "answer": "Las notas para clientes pueden activar notificaciones por correo electrónico de WooCommerce cuando el correo correspondiente está activado. El plugin registra la creación de la nota por separado del procesamiento del correo. Revise la vista previa editable antes de añadir la nota y utilice la página Historial para comprobar el resultado del gestor de correo."
      },
      {
        "question": "¿Cómo funcionan los idiomas de las plantillas?",
        "answer": "Cada plantilla puede asignarse a todos los idiomas o a un idioma incluido específico. En tiendas multilingües, el plugin intenta dar preferencia a las plantillas que coinciden con el idioma del pedido, del usuario o del sitio."
      },
      {
        "question": "¿Puedo utilizar campos meta personalizados del pedido o del cliente?",
        "answer": "Sí. Los marcadores avanzados como {order_meta:meta_key} y {customer_meta:meta_key} pueden leer campos personalizados. Se bloquean las claves sensibles que contienen palabras como password, token, secret, session, auth o hash."
      },
      {
        "question": "¿Puede el personal exponer información privada por error?",
        "answer": "El plugin no puede conocer el significado comercial de cada marcador de posición. Revise siempre las notas para clientes antes de guardarlas, especialmente al utilizar marcadores meta o datos específicos del cliente."
      },
      {
        "question": "¿Qué ocurre cuando se desactiva el formato HTML?",
        "answer": "Si se desactiva el ajuste de formato HTML, el contenido con formato de la plantilla se convierte en texto plano seguro antes de guardar la nota. Esto resulta útil para tiendas que solo desean notas muy sencillas."
      },
      {
        "question": "¿Cómo protege la vista previa de la importación las plantillas existentes?",
        "answer": "La vista previa de la importación muestra cuántas plantillas se crearán, actualizarán u omitirán antes de ejecutar la importación definitiva. Esto ayuda a evitar sobrescrituras no deseadas."
      },
      {
        "question": "¿Puedo duplicar una plantilla?",
        "answer": "Sí. Utilice la acción Duplicar en la lista de plantillas. La copia se crea como borrador y conserva el contenido, las categorías, el estado de favorito y el tipo de nota, mientras que el contador de uso comienza en cero."
      },
      {
        "question": "¿Puedo restringir las importaciones JSON?",
        "answer": "Sí. La página de ajustes puede desactivar las importaciones JSON. Aunque estén activadas, las importaciones requieren la capacidad adecuada, la verificación del nonce y un archivo JSON válido."
      },
      {
        "question": "¿Cómo se gestionan los permisos?",
        "answer": "Utilice la página de permisos para conceder o retirar las capacidades del plugin a los roles. Los administradores conservan el acceso para que el plugin no pueda bloquear accidentalmente al propietario del sitio."
      },
      {
        "question": "¿Admite revisiones el plugin?",
        "answer": "Sí. El contenido de la plantilla se refleja en el contenido de la entrada de WordPress para que las revisiones de WordPress puedan registrar los cambios cuando estén disponibles."
      },
      {
        "question": "¿Qué debo hacer antes de utilizar una plantilla para mensajes reales a clientes?",
        "answer": "Cree la plantilla, seleccione un pedido de prueba, compruebe la vista previa, verifique todos los marcadores de posición y confirme el tipo de nota seleccionado antes de añadirla. Para mensajes importantes, utilice primero una nota interna como prueba."
      },
      {
        "question": "¿Por qué es importante el estado del correo electrónico de una nota para el cliente?",
        "answer": "WooCommerce puede enviar un correo electrónico al añadir una nota para el cliente. Compruebe la configuración de correo electrónico de WooCommerce y revise la información de procesamiento en las páginas Historial y Diagnóstico."
      },
      {
        "question": "¿Puedo utilizar el plugin en una tienda de pruebas?",
        "answer": "Sí. La exportación y la importación JSON son útiles para mover plantillas entre entornos de pruebas y producción. Compruebe los ajustes de notas para clientes después de mover las plantillas, porque los ajustes de correo electrónico de WooCommerce pueden ser diferentes."
      },
      {
        "question": "¿Qué datos se eliminan al desinstalar el plugin?",
        "answer": "La rutina de desinstalación elimina las plantillas y opciones propias del plugin. Las notas de WooCommerce ya añadidas a pedidos pertenecen al historial de pedidos de WooCommerce y no se eliminan al desinstalar el plugin."
      },
      {
        "question": "¿Cómo debo informar de un problema?",
        "answer": "Documente la versión de WordPress, la versión de WooCommerce, si HPOS está activado, el idioma activo, la plantilla seleccionada y los pasos exactos que provocaron el problema."
      },
      {
        "question": "Condiciones de las plantillas",
        "answer": "Las condiciones determinan si una plantilla está disponible para un pedido concreto. Puede restringir las plantillas por estado del pedido, método de pago, método de envío, país de facturación y total mínimo o máximo del pedido. Todas las condiciones configuradas deben cumplirse. Deje un campo vacío cuando esa condición no deba restringir la plantilla."
      },
      {
        "question": "Registro del procesamiento de correo electrónico",
        "answer": "Para las notas para clientes, el plugin registra cuándo WooCommerce informa de que el correo de la nota se ha procesado y también registra errores técnicos de wp_mail. Un evento procesado confirma que WordPress/WooCommerce entregó el mensaje al sistema de correo, pero no demuestra la entrega final ni que el cliente lo haya leído. Consulte la página Historial para ver los eventos procesados y fallidos."
      },
      {
        "question": "Historial central",
        "answer": "Abra <strong>Notas de pedido de Mailhilfe → Historial</strong> para revisar la creación reciente de notas, el uso de plantillas, el procesamiento de correos y los fallos de envío. Cuando están disponibles, las entradas incluyen el pedido, la plantilla, el usuario, el destinatario, el tipo de evento y la hora. Utilice el historial para soporte, auditoría y solución de problemas."
      },
      {
        "question": "Vista previa con pedido de prueba",
        "answer": "En el editor de plantillas, introduzca un ID de pedido de WooCommerce en el área de vista previa de prueba. El contenido actual del editor, incluidos los cambios sin guardar, se muestra con los datos de ese pedido sin crear una nota ni enviar un correo electrónico. Utilice un pedido de un sitio de pruebas o un pedido de prueba no crítico."
      },
      {
        "question": "Favoritos personales y plantillas utilizadas recientemente",
        "answer": "Cada administrador puede marcar favoritos personales en la pantalla del pedido. El plugin también guarda las diez plantillas utilizadas más recientemente por cada usuario y las sitúa en una posición superior de la selección. Los favoritos globales siguen compartiéndose con todos los usuarios. Los favoritos personales no modifican la lista de otros usuarios."
      },
      {
        "question": "Página de diagnóstico",
        "answer": "Abra <strong>Notas de pedido de Mailhilfe → Diagnóstico</strong> para ver información técnica como las versiones de WordPress, PHP y WooCommerce, el estado de HPOS, el estado del correo de notas para clientes, la configuración regional, el número de plantillas publicadas, el estado de la caché y WP_DEBUG. Copie los valores de diagnóstico cuando solicite soporte."
      },
      {
        "question": "Acciones y filtros para desarrolladores",
        "answer": "El plugin proporciona acciones y filtros para marcadores de posición, valores de marcadores, claves meta permitidas, resultados de plantillas, condiciones, contenido de la vista previa, contenido final de la nota, acciones antes y después de añadir una nota, registros del historial y diagnóstico. Los nombres y parámetros están documentados en readme.txt. Valide, sanee y escape todos los datos personalizados."
      }
    ]
  },
  "fr_FR": {
    "menu": "FAQ",
    "title": "Foire aux questions",
    "intro": "Réponses aux questions courantes concernant Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Vous n’avez pas l’autorisation de gérer les modèles de notes.",
    "items": [
      {
        "question": "À quoi sert Mailhilfe Order Note Manager for WooCommerce ?",
        "answer": "L’extension crée des modèles réutilisables de notes de commande WooCommerce. L’équipe peut sélectionner un modèle dans une commande, afficher un aperçu après remplacement des variables, puis ajouter le résultat comme note interne ou note au client."
      },
      {
        "question": "Où puis-je créer et gérer les modèles ?",
        "answer": "Ouvrez Notes de commande Mailhilfe dans le menu d’administration de WordPress. Vous pouvez y créer, modifier, supprimer, classer, ajouter aux favoris et trier les modèles par glisser-déposer."
      },
      {
        "question": "Comment fonctionnent les variables ?",
        "answer": "Les variables comme {order_number}, {customer}, {billing_email}, {order_total} et {items} sont remplacées par les données réelles de la commande dans l’aperçu et lors de l’ajout de la note."
      },
      {
        "question": "Puis-je mettre en forme le texte des modèles ?",
        "answer": "Oui. L’éditeur WordPress permet d’utiliser des paragraphes, du texte en gras ou en italique, des listes et des liens. L’extension conserve le HTML sécurisé et supprime les balises dangereuses avant l’enregistrement ou l’importation."
      },
      {
        "question": "Quelle est la différence entre une note interne et une note au client ?",
        "answer": "Les notes internes sont réservées à l’équipe de la boutique. Les notes au client peuvent être visibles par le client et déclencher des notifications par e-mail de WooCommerce, selon vos réglages WooCommerce."
      },
      {
        "question": "Comment utiliser les notes au client en toute sécurité ?",
        "answer": "Les notes au client peuvent quitter l’espace d’administration interne. Avant d’ajouter un contenu susceptible d’être transmis au client, l’équipe doit vérifier les variables, les données personnelles, la formulation et le type de note sélectionné."
      },
      {
        "question": "À quoi servent les favoris, la recherche et le tri ?",
        "answer": "Les favoris permettent d’accéder rapidement aux modèles importants. La recherche filtre les longues listes de modèles dans l’écran de commande. Le tri par glisser-déposer détermine l’ordre d’affichage des modèles."
      },
      {
        "question": "Comment fonctionnent l’importation et l’exportation JSON ?",
        "answer": "L’exportation crée un fichier JSON contenant les titres, le contenu, les types de notes, les catégories, les favoris, le tri et les données d’utilisation des modèles. L’importation permet de restaurer les modèles ou de les transférer vers une autre boutique."
      },
      {
        "question": "Comment installer les modèles de démonstration ?",
        "answer": "Ouvrez la page d’importation/exportation et choisissez l’action relative aux modèles de démonstration. Les modèles sont créés dans la langue incluse correspondante, y compris le persan ; sinon, l’ensemble anglais vérifié est utilisé."
      },
      {
        "question": "Quels rôles peuvent utiliser l’extension ?",
        "answer": "Les administrateurs et les responsables de boutique reçoivent automatiquement les capacités de l’extension. Le droit de gérer les modèles est distinct du droit de les utiliser dans les commandes."
      },
      {
        "question": "L’extension est-elle compatible avec HPOS ?",
        "answer": "Oui. L’extension déclare sa compatibilité avec HPOS de WooCommerce et utilise les API de commande WooCommerce plutôt qu’un accès direct aux tables de commandes de la base de données."
      },
      {
        "question": "L’extension ajoute-t-elle des liens publics sur le site ?",
        "answer": "Non. L’extension fonctionne dans l’administration WordPress et dans les écrans de commande WooCommerce. Elle n’ajoute aucun lien promotionnel ni aucune mention publique sur la boutique."
      },
      {
        "question": "Que dois-je vérifier si la langue affichée est incorrecte ?",
        "answer": "Vérifiez Réglages > Général > Langue du site ainsi que la langue de votre profil utilisateur. L’extension contient des traductions vérifiées pour toutes les langues prises en charge, y compris le persan. Les autres langues doivent utiliser des packs linguistiques WordPress.org vérifiés."
      },
      {
        "question": "Puis-je modifier l’aperçu avant d’ajouter la note ?",
        "answer": "Oui. Après la sélection d’un modèle, l’aperçu contient les données de commande remplacées et peut être modifié avant l’enregistrement de la note. L’aperçu modifié constitue la note finale ajoutée à la commande."
      },
      {
        "question": "Comment la création des notes au client et le traitement des e-mails sont-ils enregistrés ?",
        "answer": "Les notes au client peuvent déclencher des notifications par e-mail de WooCommerce lorsque l’e-mail correspondant est activé. L’extension enregistre la création de la note au client séparément du traitement de l’e-mail. Vérifiez l’aperçu modifiable avant d’ajouter la note et consultez la page Historique pour contrôler le résultat du gestionnaire d’e-mails."
      },
      {
        "question": "Comment fonctionnent les langues des modèles ?",
        "answer": "Chaque modèle peut être attribué à toutes les langues ou à une langue précise prise en charge. Dans les boutiques multilingues, l’extension tente de privilégier les modèles correspondant à la langue de la commande, de l’utilisateur ou du site."
      },
      {
        "question": "Puis-je utiliser des champs de métadonnées personnalisés de commande ou de client ?",
        "answer": "Oui. Les variables avancées comme {order_meta:meta_key} et {customer_meta:meta_key} peuvent lire des champs personnalisés. Les clés sensibles contenant notamment password, token, secret, session, auth ou hash sont bloquées."
      },
      {
        "question": "L’équipe peut-elle divulguer accidentellement des informations privées ?",
        "answer": "L’extension ne peut pas connaître la signification métier de chaque variable. Vérifiez toujours les notes au client avant de les enregistrer, en particulier lorsque vous utilisez des variables de métadonnées ou des données propres au client."
      },
      {
        "question": "Que se passe-t-il lorsque la mise en forme HTML est désactivée ?",
        "answer": "Lorsque le réglage de mise en forme HTML est désactivé, le contenu mis en forme du modèle est converti en texte brut sécurisé avant l’enregistrement de la note. Cette option convient aux boutiques qui souhaitent uniquement des notes très simples."
      },
      {
        "question": "Comment l’aperçu de l’importation protège-t-il les modèles existants ?",
        "answer": "L’aperçu de l’importation indique combien de modèles seront créés, mis à jour ou ignorés avant l’exécution définitive. Il permet d’éviter les remplacements indésirables."
      },
      {
        "question": "Puis-je dupliquer un modèle ?",
        "answer": "Oui. Utilisez l’action Dupliquer dans la liste des modèles. La copie est créée comme brouillon et conserve le contenu, les catégories, le statut de favori et le type de note, tandis que le compteur d’utilisation repart de zéro."
      },
      {
        "question": "Puis-je limiter les importations JSON ?",
        "answer": "Oui. La page des réglages permet de désactiver les importations JSON. Même lorsqu’elles sont activées, elles nécessitent la capacité appropriée, une vérification de nonce et un fichier JSON valide."
      },
      {
        "question": "Comment les droits sont-ils gérés ?",
        "answer": "Utilisez la page des droits pour accorder ou retirer les capacités de l’extension aux différents rôles. Les administrateurs conservent l’accès afin d’éviter que le propriétaire du site soit accidentellement exclu."
      },
      {
        "question": "L’extension prend-elle en charge les révisions ?",
        "answer": "Oui. Le contenu du modèle est reproduit dans le contenu de publication WordPress afin que les révisions WordPress puissent suivre les modifications lorsqu’elles sont disponibles."
      },
      {
        "question": "Que dois-je faire avant d’utiliser un modèle pour de véritables messages aux clients ?",
        "answer": "Créez le modèle, sélectionnez une commande de test, vérifiez l’aperçu et toutes les variables, puis confirmez le type de note sélectionné avant d’ajouter la note. Pour les messages importants, effectuez d’abord un essai avec une note interne."
      },
      {
        "question": "Pourquoi le statut de l’e-mail de note au client est-il important ?",
        "answer": "WooCommerce peut envoyer un e-mail lors de l’ajout d’une note au client. Vérifiez la configuration des e-mails WooCommerce et consultez les informations de traitement dans les pages Historique et Diagnostic."
      },
      {
        "question": "Puis-je utiliser l’extension sur une boutique de préproduction ?",
        "answer": "Oui. L’exportation et l’importation JSON sont utiles pour déplacer les modèles entre la préproduction et la production. Vérifiez les réglages des notes au client après le transfert, car les réglages d’e-mail WooCommerce peuvent différer."
      },
      {
        "question": "Quelles données sont supprimées lors de la désinstallation ?",
        "answer": "La procédure de désinstallation supprime les modèles et les options propres à l’extension. Les notes de commande WooCommerce déjà ajoutées appartiennent à l’historique de commande WooCommerce et ne sont pas supprimées lors de la désinstallation de l’extension."
      },
      {
        "question": "Comment signaler un problème ?",
        "answer": "Indiquez la version de WordPress, la version de WooCommerce, l’état d’activation de HPOS, la langue active, le modèle sélectionné et les étapes exactes ayant provoqué le problème."
      },
      {
        "question": "Conditions des modèles",
        "answer": "Les conditions d’un modèle déterminent s’il est disponible pour une commande donnée. Vous pouvez limiter les modèles selon l’état de la commande, le mode de paiement, le mode de livraison, le pays de facturation ainsi que le montant minimal ou maximal de la commande. Toutes les conditions définies doivent être remplies. Laissez un champ vide si cette condition ne doit pas limiter le modèle."
      },
      {
        "question": "Journal du traitement des e-mails",
        "answer": "Pour les notes au client, l’extension enregistre le moment où WooCommerce indique que l’e-mail de note au client a été traité, ainsi que les erreurs techniques de wp_mail. Un événement traité confirme que WordPress/WooCommerce a transmis le message au système de messagerie ; il ne prouve ni la livraison finale ni la lecture du message par le client. Consultez la page Historique pour connaître les événements traités et échoués."
      },
      {
        "question": "Historique central",
        "answer": "Ouvrez <strong>Notes de commande Mailhilfe → Historique</strong> pour consulter les créations récentes de notes, l’utilisation des modèles, le traitement des e-mails et les échecs d’envoi. Lorsque les informations sont disponibles, les entrées indiquent la commande, le modèle, l’utilisateur, le destinataire, le type d’événement et l’heure. Utilisez l’historique pour l’assistance, l’audit et le dépannage."
      },
      {
        "question": "Aperçu avec une commande de test",
        "answer": "Dans l’éditeur de modèle, saisissez l’identifiant d’une commande WooCommerce dans la zone d’aperçu de test. Le contenu actuel de l’éditeur, y compris les modifications non enregistrées, est rendu avec les données de cette commande sans créer de note ni envoyer d’e-mail. Utilisez une commande de préproduction ou une commande de test sans importance."
      },
      {
        "question": "Favoris personnels et modèles récemment utilisés",
        "answer": "Chaque administrateur peut ajouter des favoris personnels dans l’écran de commande. L’extension mémorise également les dix modèles les plus récemment utilisés par chaque utilisateur et les place plus haut dans la sélection. Les favoris globaux restent partagés entre tous les utilisateurs. Les favoris personnels ne modifient pas la liste d’un autre utilisateur."
      },
      {
        "question": "Page de diagnostic",
        "answer": "Ouvrez <strong>Notes de commande Mailhilfe → Diagnostic</strong> pour consulter des informations techniques telles que les versions de WordPress, PHP et WooCommerce, l’état HPOS, l’état de l’e-mail de note au client, les paramètres régionaux, le nombre de modèles publiés, l’état du cache et WP_DEBUG. Copiez les valeurs de diagnostic lorsque vous demandez de l’aide."
      },
      {
        "question": "Actions et filtres pour les développeurs",
        "answer": "L’extension fournit des actions et des filtres pour les variables, leurs valeurs, les clés de métadonnées autorisées, les résultats des modèles, les conditions, le contenu de l’aperçu, le contenu final de la note, les actions avant et après l’ajout d’une note, les entrées d’historique et le diagnostic. Les noms des points d’accroche et leurs paramètres sont documentés dans readme.txt. Validez, nettoyez et échappez toutes les données personnalisées."
      }
    ]
  },
  "ru_RU": {
    "menu": "FAQ",
    "title": "Часто задаваемые вопросы",
    "intro": "Ответы на распространённые вопросы о Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "У вас нет прав на управление шаблонами примечаний.",
    "items": [
      {
        "question": "Для чего используется Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "Плагин создаёт повторно используемые шаблоны примечаний к заказам WooCommerce. Сотрудники могут выбрать шаблон в заказе, проверить предпросмотр с заменёнными заполнителями и добавить результат как внутреннее примечание или примечание для клиента."
      },
      {
        "question": "Где создавать и управлять шаблонами?",
        "answer": "Откройте «Примечания Mailhilfe» в меню администратора WordPress. Там можно создавать, изменять, удалять, распределять по категориям, добавлять в избранное и сортировать шаблоны перетаскиванием."
      },
      {
        "question": "Как работают заполнители?",
        "answer": "Заполнители, например {order_number}, {customer}, {billing_email}, {order_total} и {items}, заменяются реальными данными заказа в предпросмотре и при добавлении примечания."
      },
      {
        "question": "Можно ли форматировать текст шаблона?",
        "answer": "Да. Редактор WordPress поддерживает абзацы, полужирный и курсивный текст, списки и ссылки. Плагин сохраняет безопасный HTML и удаляет небезопасную разметку перед сохранением или импортом."
      },
      {
        "question": "Чем внутренние примечания отличаются от примечаний для клиентов?",
        "answer": "Внутренние примечания предназначены только для команды магазина. Примечания для клиентов могут быть видны клиенту и, в зависимости от настроек WooCommerce, могут запускать email-уведомления."
      },
      {
        "question": "Как безопасно работать с примечаниями для клиентов?",
        "answer": "Примечания для клиентов могут покидать внутреннюю область администрирования. Перед добавлением текста сотрудники должны проверить заполнители, персональные данные, формулировки и выбранный тип примечания."
      },
      {
        "question": "Как помогают избранное, поиск и сортировка?",
        "answer": "Избранное оставляет важные шаблоны под рукой. Поиск фильтрует длинные списки шаблонов на экране заказа. Сортировка перетаскиванием определяет порядок отображения шаблонов."
      },
      {
        "question": "Как работают импорт и экспорт JSON?",
        "answer": "Экспорт создаёт JSON-файл с заголовками, содержимым, типами примечаний, категориями, избранным, сортировкой и данными об использовании. Импорт позволяет восстановить шаблоны или перенести их в другой магазин."
      },
      {
        "question": "Как установить демонстрационные шаблоны?",
        "answer": "Откройте страницу импорта и экспорта и выберите установку демонстрационных шаблонов. Шаблоны создаются на соответствующем встроенном языке, включая персидский; в остальных случаях используется проверенный английский набор."
      },
      {
        "question": "Какие роли могут использовать плагин?",
        "answer": "Администраторы и менеджеры магазина получают права плагина автоматически. Право управлять шаблонами отделено от права использовать шаблоны в заказах."
      },
      {
        "question": "Совместим ли плагин с HPOS?",
        "answer": "Да. Плагин объявляет совместимость с WooCommerce HPOS и использует API заказов WooCommerce вместо прямого доступа к таблицам заказов в базе данных."
      },
      {
        "question": "Добавляет ли плагин публичные ссылки на витрину?",
        "answer": "Нет. Плагин работает в панели управления WordPress и на экранах заказов WooCommerce. Он не добавляет на витрину рекламные ссылки или ссылки «работает на…»."
      },
      {
        "question": "Что проверить, если отображается неправильный язык?",
        "answer": "Проверьте «Настройки → Общие → Язык сайта» и язык в профиле пользователя. Плагин содержит проверенные встроенные переводы для всех поддерживаемых языков, включая персидский. Для других языков используйте проверенные языковые пакеты WordPress.org."
      },
      {
        "question": "Можно ли изменить предпросмотр перед добавлением примечания?",
        "answer": "Да. После выбора шаблона предпросмотр содержит заменённые данные заказа и может быть отредактирован перед сохранением. Отредактированный предпросмотр становится итоговым примечанием, которое будет добавлено к заказу."
      },
      {
        "question": "Как регистрируются создание примечания для клиента и обработка email?",
        "answer": "Примечания для клиентов могут запускать email-уведомления WooCommerce, если соответствующее письмо включено. Плагин регистрирует создание примечания отдельно от обработки email. Перед добавлением проверьте редактируемый предпросмотр, а результат обработчика почты смотрите на странице «История»."
      },
      {
        "question": "Как работают языки шаблонов?",
        "answer": "Шаблон можно назначить всем языкам или конкретному поддерживаемому языку. В многоязычных магазинах плагин старается отдавать предпочтение шаблонам, соответствующим языку заказа, пользователя или сайта."
      },
      {
        "question": "Можно ли использовать пользовательские метаполя заказа или клиента?",
        "answer": "Да. Расширенные заполнители, например {order_meta:meta_key} и {customer_meta:meta_key}, могут считывать пользовательские поля. Чувствительные ключи, содержащие слова password, token, secret, session, auth или hash, блокируются."
      },
      {
        "question": "Могут ли сотрудники случайно раскрыть личную информацию?",
        "answer": "Плагин не может знать деловое назначение каждого заполнителя. Всегда проверяйте примечания для клиентов перед сохранением, особенно при использовании метазаполнителей или данных конкретного клиента."
      },
      {
        "question": "Что происходит, если HTML-форматирование отключено?",
        "answer": "Если HTML-форматирование отключено в настройках, форматированный текст шаблона преобразуется в безопасный обычный текст перед сохранением примечания. Это удобно для магазинов, которым нужны только простые примечания."
      },
      {
        "question": "Как предпросмотр импорта защищает существующие шаблоны?",
        "answer": "До окончательного импорта предпросмотр показывает, сколько шаблонов будет создано, обновлено или пропущено. Это помогает избежать нежелательной перезаписи."
      },
      {
        "question": "Можно ли дублировать шаблон?",
        "answer": "Да. Используйте действие «Дублировать» в списке шаблонов. Копия создаётся как черновик и сохраняет содержимое, категории, статус избранного и тип примечания, а счётчик использования начинается с нуля."
      },
      {
        "question": "Можно ли ограничить импорт JSON?",
        "answer": "Да. На странице настроек импорт JSON можно отключить. Даже если он включён, для импорта требуются соответствующие права, проверка nonce и корректный JSON-файл."
      },
      {
        "question": "Как управляются права доступа?",
        "answer": "На странице прав можно выдавать или отзывать права плагина для ролей. Администраторы сохраняют доступ, чтобы владелец сайта не мог случайно потерять управление плагином."
      },
      {
        "question": "Поддерживает ли плагин редакции?",
        "answer": "Да. Содержимое шаблона дублируется в содержимое записи WordPress, поэтому при доступности редакций WordPress может отслеживать изменения."
      },
      {
        "question": "Что сделать перед использованием шаблона для реальных сообщений клиентам?",
        "answer": "Создайте шаблон, выберите тестовый заказ, проверьте предпросмотр, все заполнители и выбранный тип примечания. Для критически важных сообщений сначала выполните проверку как внутреннее примечание."
      },
      {
        "question": "Почему важен статус email для примечания клиенту?",
        "answer": "WooCommerce может отправлять email при добавлении примечания для клиента. Проверьте конфигурацию писем WooCommerce и сведения об обработке на страницах «История» и «Диагностика»."
      },
      {
        "question": "Можно ли использовать плагин в тестовом магазине?",
        "answer": "Да. Экспорт и импорт JSON удобны для переноса шаблонов между тестовой и рабочей средой. После переноса проверьте настройки примечаний для клиентов, поскольку настройки email WooCommerce могут отличаться."
      },
      {
        "question": "Какие данные удаляются при деинсталляции?",
        "answer": "Процедура деинсталляции удаляет собственные шаблоны и настройки плагина. Примечания WooCommerce, уже добавленные к заказам, относятся к истории заказов WooCommerce и при удалении плагина не удаляются."
      },
      {
        "question": "Как сообщить о проблеме?",
        "answer": "Укажите версии WordPress и WooCommerce, состояние HPOS, активный язык, выбранный шаблон и точную последовательность действий, которая привела к проблеме."
      },
      {
        "question": "Условия шаблонов",
        "answer": "Условия определяют, доступен ли шаблон для конкретного заказа. Шаблон можно ограничить по статусу заказа, способу оплаты, способу доставки, стране платёжного адреса и минимальной или максимальной сумме заказа. Все заданные условия должны выполняться. Оставьте поле пустым, если соответствующее условие не должно ограничивать шаблон."
      },
      {
        "question": "Журнал обработки email",
        "answer": "Для примечаний клиенту плагин регистрирует момент, когда WooCommerce сообщает об обработке email, а также технические ошибки wp_mail. Событие «обработано» подтверждает передачу сообщения почтовой системе WordPress/WooCommerce, но не доказывает окончательную доставку или прочтение клиентом. Обработанные и неудачные события доступны на странице «История»."
      },
      {
        "question": "Общая история",
        "answer": "Откройте <strong>Примечания Mailhilfe → История</strong>, чтобы просмотреть недавнее создание примечаний, использование шаблонов, обработку email и ошибки отправки. При наличии данных записи содержат заказ, шаблон, пользователя, получателя, тип события и время. Используйте историю для поддержки, аудита и устранения неполадок."
      },
      {
        "question": "Предпросмотр с тестовым заказом",
        "answer": "В редакторе шаблона введите ID заказа WooCommerce в области тестового предпросмотра. Текущее содержимое редактора, включая несохранённые изменения, будет отображено с данными этого заказа без создания примечания и отправки email. Используйте заказ на тестовом сайте или некритичный тестовый заказ."
      },
      {
        "question": "Личное избранное и недавно использованные шаблоны",
        "answer": "Каждый администратор может отмечать личные избранные шаблоны на экране заказа. Плагин также хранит десять последних использованных шаблонов для каждого пользователя и поднимает их выше в списке. Общее избранное остаётся доступным всем пользователям. Личное избранное не изменяет список другого пользователя."
      },
      {
        "question": "Страница диагностики",
        "answer": "Откройте <strong>Примечания Mailhilfe → Диагностика</strong>, чтобы просмотреть версии WordPress, PHP и WooCommerce, состояние HPOS, состояние email для примечаний клиенту, локаль, количество опубликованных шаблонов, состояние кеша и WP_DEBUG. При обращении в поддержку скопируйте диагностические значения."
      },
      {
        "question": "Хуки и фильтры для разработчиков",
        "answer": "Плагин предоставляет хуки и фильтры для заполнителей, значений заполнителей, разрешённых метаключей, результатов шаблонов, условий, предпросмотра, итогового текста примечания, действий до и после добавления примечания, записей истории и диагностики. Названия хуков и параметры описаны в readme.txt. Проверяйте, очищайте и экранируйте все пользовательские данные."
      }
    ]
  },
  "pt_BR": {
    "menu": "FAQ",
    "title": "Perguntas frequentes",
    "intro": "Respostas para perguntas comuns sobre o Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Você não tem permissão para gerenciar modelos de notas.",
    "items": [
      {
        "question": "Para que serve o Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "O plugin cria modelos reutilizáveis de notas de pedidos do WooCommerce. A equipe pode selecionar um modelo dentro de um pedido, visualizar os espaços reservados substituídos e adicionar o resultado como nota interna ou nota ao cliente."
      },
      {
        "question": "Onde posso criar e gerenciar modelos?",
        "answer": "Abra Notas de pedido Mailhilfe no menu administrativo do WordPress. Lá você pode criar, editar, excluir, categorizar, marcar como favorito e ordenar modelos arrastando e soltando."
      },
      {
        "question": "Como funcionam os espaços reservados?",
        "answer": "Espaços reservados como {order_number}, {customer}, {billing_email}, {order_total} e {items} são substituídos por dados reais do pedido na prévia e quando a nota é adicionada."
      },
      {
        "question": "Posso formatar os textos dos modelos?",
        "answer": "Sim. O editor do WordPress oferece suporte a parágrafos, negrito, itálico, listas e links. O plugin mantém o HTML seguro e remove marcações inseguras antes de salvar ou importar."
      },
      {
        "question": "Qual é a diferença entre notas internas e notas ao cliente?",
        "answer": "Notas internas são destinadas somente à equipe da loja. Notas ao cliente podem ficar visíveis para o cliente e podem acionar notificações por e-mail do WooCommerce, dependendo das configurações do WooCommerce."
      },
      {
        "question": "Como as notas ao cliente devem ser tratadas com segurança?",
        "answer": "Notas ao cliente podem sair da área administrativa interna. A equipe deve verificar os espaços reservados, os dados pessoais, a redação e o tipo de nota selecionado antes de adicionar qualquer conteúdo que possa chegar ao cliente."
      },
      {
        "question": "Como favoritos, pesquisa e ordenação ajudam?",
        "answer": "Os favoritos mantêm os modelos importantes ao alcance. A pesquisa filtra listas longas de modelos na tela do pedido. A ordenação por arrastar e soltar controla a ordem em que os modelos são exibidos."
      },
      {
        "question": "Como funcionam a importação e a exportação JSON?",
        "answer": "A exportação cria um arquivo JSON com títulos, conteúdos, tipos de nota, categorias, favoritos, ordenação e dados de uso dos modelos. A importação pode restaurar ou transferir modelos para outra loja."
      },
      {
        "question": "Como os modelos de demonstração são instalados?",
        "answer": "Abra a página de importação/exportação e escolha a ação de modelos de demonstração. Os modelos são criados no idioma incluído correspondente, inclusive persa; caso contrário, é usado o conjunto revisado em inglês."
      },
      {
        "question": "Quais funções podem usar o plugin?",
        "answer": "Administradores e gerentes de loja recebem automaticamente as capacidades do plugin. O plugin separa a permissão para gerenciar modelos da permissão para usar modelos em pedidos."
      },
      {
        "question": "O plugin é compatível com HPOS?",
        "answer": "Sim. O plugin declara compatibilidade com o HPOS do WooCommerce e usa as APIs de pedidos do WooCommerce, em vez de acessar diretamente as tabelas de pedidos no banco de dados."
      },
      {
        "question": "O plugin adiciona links públicos no site?",
        "answer": "Não. O plugin funciona na área administrativa do WordPress e nas telas de pedidos do WooCommerce. Ele não adiciona links de crédito nem links promocionais públicos à loja."
      },
      {
        "question": "O que devo verificar se o idioma estiver errado?",
        "answer": "Verifique Configurações > Geral > Idioma do site e o idioma no seu perfil de usuário. O plugin inclui traduções revisadas para todos os idiomas compatíveis, inclusive persa. Outros idiomas devem usar pacotes de idioma revisados do WordPress.org."
      },
      {
        "question": "Posso editar a prévia antes de adicionar a nota?",
        "answer": "Sim. Depois de selecionar um modelo, a prévia contém os dados do pedido já substituídos e pode ser editada antes de salvar a nota. A prévia editada será a nota final adicionada ao pedido."
      },
      {
        "question": "Como a criação de notas ao cliente e o processamento de e-mails são registrados?",
        "answer": "Notas ao cliente podem acionar notificações por e-mail do WooCommerce quando o e-mail correspondente está ativado. O plugin registra a criação da nota ao cliente separadamente do processamento do e-mail. Revise a prévia editável antes de adicionar a nota e use a página Histórico para verificar o resultado do manipulador de e-mail."
      },
      {
        "question": "Como funcionam os idiomas dos modelos?",
        "answer": "Cada modelo pode ser atribuído a todos os idiomas ou a um idioma específico incluído no plugin. Em lojas multilíngues, o plugin tenta dar preferência a modelos que correspondam ao idioma do pedido, ao idioma do usuário ou ao idioma do site."
      },
      {
        "question": "Posso usar campos personalizados do pedido ou do cliente?",
        "answer": "Sim. Espaços reservados avançados como {order_meta:meta_key} e {customer_meta:meta_key} podem ler campos personalizados. Chaves sensíveis que contenham palavras como password, token, secret, session, auth ou hash são bloqueadas."
      },
      {
        "question": "A equipe pode expor informações privadas por engano?",
        "answer": "O plugin não consegue conhecer o significado comercial de cada espaço reservado. Sempre revise as notas ao cliente antes de salvá-las, especialmente ao usar espaços reservados de metadados ou dados específicos do cliente."
      },
      {
        "question": "O que acontece quando a formatação HTML está desativada?",
        "answer": "Se a configuração de formatação HTML estiver desativada, o conteúdo formatado do modelo será convertido em texto simples seguro antes de a nota ser armazenada. Isso é útil para lojas que desejam apenas notas muito simples."
      },
      {
        "question": "Como a prévia da importação protege os modelos existentes?",
        "answer": "A prévia da importação mostra quantos modelos serão criados, atualizados ou ignorados antes da execução da importação final. Isso ajuda a evitar substituições indesejadas."
      },
      {
        "question": "Posso duplicar um modelo?",
        "answer": "Sim. Use a ação Duplicar na lista de modelos. A cópia é criada como rascunho e mantém o conteúdo, as categorias, o status de favorito e o tipo de nota, enquanto o contador de uso começa em zero."
      },
      {
        "question": "Posso restringir importações JSON?",
        "answer": "Sim. A página de configurações pode desativar as importações JSON. Mesmo quando estão ativadas, as importações exigem a capacidade adequada, verificação de nonce e um arquivo JSON válido."
      },
      {
        "question": "Como as permissões são gerenciadas?",
        "answer": "Use a página de permissões para conceder ou remover as capacidades do plugin para cada função. Os administradores mantêm o acesso para que o plugin não bloqueie acidentalmente o proprietário do site."
      },
      {
        "question": "O plugin oferece suporte a revisões?",
        "answer": "Sim. O conteúdo do modelo é espelhado no conteúdo do post do WordPress para que as revisões do WordPress possam acompanhar alterações quando esse recurso estiver disponível."
      },
      {
        "question": "O que devo fazer antes de usar um modelo em mensagens reais para clientes?",
        "answer": "Crie o modelo, selecione um pedido de teste, verifique a prévia, confirme todos os espaços reservados e confira o tipo de nota selecionado antes de adicionar a nota. Para mensagens críticas, use primeiro uma nota interna como teste."
      },
      {
        "question": "Por que o status do e-mail da nota ao cliente é importante?",
        "answer": "O WooCommerce pode enviar um e-mail quando uma nota ao cliente é adicionada. Verifique a configuração de e-mail do WooCommerce e revise as informações de processamento nas páginas Histórico e Diagnóstico."
      },
      {
        "question": "Posso usar o plugin em uma loja de testes?",
        "answer": "Sim. A exportação e a importação JSON são úteis para mover modelos entre ambientes de testes e produção. Verifique as configurações de notas ao cliente depois de mover os modelos, pois as configurações de e-mail do WooCommerce podem ser diferentes."
      },
      {
        "question": "Quais dados são removidos durante a desinstalação?",
        "answer": "A rotina de desinstalação remove os modelos próprios do plugin e as opções do plugin. As notas de pedidos do WooCommerce já adicionadas aos pedidos pertencem ao histórico de pedidos do WooCommerce e não são removidas ao desinstalar o plugin."
      },
      {
        "question": "Como devo relatar um problema?",
        "answer": "Registre a versão do WordPress, a versão do WooCommerce, se o HPOS está ativado, o idioma ativo, o modelo selecionado e as etapas exatas que causaram o problema."
      },
      {
        "question": "Condições do modelo",
        "answer": "As condições do modelo determinam se um modelo está disponível para um pedido específico. É possível restringir modelos por status do pedido, método de pagamento, método de entrega, país de faturamento e total mínimo ou máximo do pedido. Todas as condições configuradas devem corresponder. Deixe um campo em branco quando essa condição não deve restringir o modelo."
      },
      {
        "question": "Registro de processamento de e-mail",
        "answer": "Para notas ao cliente, o plugin registra quando o WooCommerce informa que o e-mail de nota ao cliente foi processado e também registra erros técnicos de wp_mail. Um evento processado confirma que o WordPress/WooCommerce entregou a mensagem ao sistema de e-mail; isso não comprova a entrega final nem que o cliente a leu. Verifique na página Histórico os eventos de e-mail processados e com falha."
      },
      {
        "question": "Histórico central",
        "answer": "Abra <strong>Notas de pedido Mailhilfe → Histórico</strong> para revisar a criação recente de notas, o uso de modelos, o processamento de e-mails e as falhas de e-mail. Quando disponíveis, os registros incluem o pedido, o modelo, o usuário, o destinatário, o tipo de evento e o horário. Use o histórico para suporte, auditoria e solução de problemas."
      },
      {
        "question": "Prévia com pedido de teste",
        "answer": "No editor de modelos, informe um ID de pedido do WooCommerce na área de prévia de teste. O conteúdo atual do editor, incluindo alterações não salvas, é renderizado com os dados desse pedido sem criar uma nota nem enviar um e-mail. Use um pedido de uma loja de testes ou um pedido de teste sem importância."
      },
      {
        "question": "Favoritos pessoais e modelos usados recentemente",
        "answer": "Cada administrador pode marcar favoritos pessoais na tela do pedido. O plugin também armazena os dez modelos usados mais recentemente por cada usuário e dá a eles uma posição mais alta na seleção. Os favoritos globais continuam compartilhados com todos os usuários. Favoritos pessoais não alteram a lista de outro usuário."
      },
      {
        "question": "Página de diagnóstico",
        "answer": "Abra <strong>Notas de pedido Mailhilfe → Diagnóstico</strong> para ver informações técnicas como versões do WordPress, PHP e WooCommerce, status do HPOS, status do e-mail de nota ao cliente, localidade, quantidade de modelos publicados, status do cache e WP_DEBUG. Copie os valores de diagnóstico ao solicitar suporte."
      },
      {
        "question": "Ganchos e filtros para desenvolvedores",
        "answer": "O plugin fornece ganchos e filtros para espaços reservados, valores de espaços reservados, chaves meta permitidas, resultados de modelos, condições, conteúdo da prévia, conteúdo final da nota, ações antes e depois de adicionar uma nota, registros de histórico e diagnóstico. Os nomes dos ganchos e seus parâmetros estão documentados em readme.txt. Valide, higienize e escape todos os dados personalizados."
      }
    ]
  },
  "it_IT": {
    "menu": "FAQ",
    "title": "Domande frequenti",
    "intro": "Risposte alle domande più comuni su Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Non disponi dei permessi per gestire i modelli di nota.",
    "items": [
      {
        "question": "A cosa serve Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "Il plugin crea modelli riutilizzabili per le note degli ordini WooCommerce. Il personale può selezionare un modello all’interno di un ordine, visualizzare l’anteprima con i segnaposto sostituiti e aggiungere il risultato come nota interna o nota per il cliente."
      },
      {
        "question": "Dove posso creare e gestire i modelli?",
        "answer": "Apri Note ordine Mailhilfe nel menu di amministrazione di WordPress. Da qui puoi creare, modificare, eliminare, categorizzare, contrassegnare come preferiti e ordinare i modelli tramite trascinamento."
      },
      {
        "question": "Come funzionano i segnaposto?",
        "answer": "I segnaposto come {order_number}, {customer}, {billing_email}, {order_total} e {items} vengono sostituiti con i dati reali dell’ordine nell’anteprima e quando la nota viene aggiunta."
      },
      {
        "question": "Posso formattare i testi dei modelli?",
        "answer": "Sì. L’editor di WordPress supporta paragrafi, grassetto, corsivo, elenchi e link. Il plugin conserva l’HTML sicuro e rimuove il markup non sicuro prima del salvataggio o dell’importazione."
      },
      {
        "question": "Qual è la differenza tra note interne e note per il cliente?",
        "answer": "Le note interne sono destinate esclusivamente al team del negozio. Le note per il cliente possono essere visibili al cliente e, a seconda delle impostazioni di WooCommerce, possono attivare notifiche email."
      },
      {
        "question": "Come devo gestire in sicurezza le note per il cliente?",
        "answer": "Le note per il cliente possono uscire dall’area amministrativa interna. Prima di aggiungere un testo che potrebbe raggiungere il cliente, il personale deve controllare segnaposto, dati personali, formulazione e tipo di nota selezionato."
      },
      {
        "question": "In che modo preferiti, ricerca e ordinamento sono utili?",
        "answer": "I preferiti mantengono facilmente accessibili i modelli importanti. La ricerca filtra gli elenchi lunghi nella schermata dell’ordine. L’ordinamento tramite trascinamento controlla l’ordine di visualizzazione dei modelli."
      },
      {
        "question": "Come funzionano importazione ed esportazione JSON?",
        "answer": "L’esportazione crea un file JSON con titoli, contenuti, tipi di nota, categorie, preferiti, ordinamento e dati di utilizzo dei modelli. L’importazione può ripristinare i modelli o trasferirli in un altro negozio."
      },
      {
        "question": "Come si installano i modelli dimostrativi?",
        "answer": "Apri la pagina di importazione/esportazione e scegli l’azione per i modelli dimostrativi. I modelli vengono creati nella lingua inclusa corrispondente, compreso il persiano; altrimenti viene utilizzato il set inglese verificato."
      },
      {
        "question": "Quali ruoli possono utilizzare il plugin?",
        "answer": "Amministratori e gestori del negozio ricevono automaticamente le capacità del plugin. Il permesso per gestire i modelli è separato dal permesso per utilizzarli negli ordini."
      },
      {
        "question": "Il plugin è compatibile con HPOS?",
        "answer": "Sì. Il plugin dichiara la compatibilità con WooCommerce HPOS e utilizza le API degli ordini WooCommerce anziché accedere direttamente alle tabelle degli ordini nel database."
      },
      {
        "question": "Il plugin aggiunge link pubblici nel frontend?",
        "answer": "No. Il plugin funziona nell’area di amministrazione di WordPress e nelle schermate degli ordini WooCommerce. Non aggiunge link “powered by” o link promozionali pubblici al negozio."
      },
      {
        "question": "Cosa devo controllare se la lingua non è corretta?",
        "answer": "Controlla Impostazioni > Generali > Lingua del sito e la lingua nel tuo profilo utente. Il plugin include traduzioni verificate per tutte le lingue supportate, compreso il persiano. Le altre lingue devono utilizzare pacchetti linguistici WordPress.org verificati."
      },
      {
        "question": "Posso modificare l’anteprima prima di aggiungere la nota?",
        "answer": "Sì. Dopo aver selezionato un modello, l’anteprima contiene i dati dell’ordine già sostituiti e può essere modificata prima del salvataggio. L’anteprima modificata è la nota finale che verrà aggiunta all’ordine."
      },
      {
        "question": "Come vengono registrate la creazione della nota per il cliente e l’elaborazione email?",
        "answer": "Le note per il cliente possono attivare notifiche email di WooCommerce quando l’email corrispondente è abilitata. Il plugin registra separatamente la creazione della nota e l’elaborazione dell’email. Controlla l’anteprima modificabile prima di aggiungere la nota e usa la pagina Cronologia per verificare il risultato del gestore email."
      },
      {
        "question": "Come funzionano le lingue dei modelli?",
        "answer": "Ogni modello può essere assegnato a tutte le lingue oppure a una specifica lingua inclusa. Nei negozi multilingue il plugin tenta di preferire i modelli che corrispondono alla lingua dell’ordine, dell’utente o del sito."
      },
      {
        "question": "Posso utilizzare campi meta personalizzati dell’ordine o del cliente?",
        "answer": "Sì. I segnaposto avanzati come {order_meta:meta_key} e {customer_meta:meta_key} possono leggere campi personalizzati. Le chiavi sensibili contenenti parole come password, token, secret, session, auth o hash vengono bloccate."
      },
      {
        "question": "Il personale può esporre accidentalmente informazioni private?",
        "answer": "Il plugin non può conoscere il significato aziendale di ogni segnaposto. Controlla sempre le note per il cliente prima di salvarle, soprattutto quando utilizzi segnaposto meta o dati specifici del cliente."
      },
      {
        "question": "Cosa accade quando la formattazione HTML è disabilitata?",
        "answer": "Se l’impostazione della formattazione HTML è disabilitata, il contenuto formattato del modello viene convertito in testo semplice sicuro prima di essere memorizzato. Questa opzione è utile per i negozi che desiderano note molto semplici."
      },
      {
        "question": "In che modo l’anteprima dell’importazione protegge i modelli esistenti?",
        "answer": "Prima dell’importazione finale, l’anteprima mostra quanti modelli verranno creati, aggiornati o ignorati. In questo modo si riduce il rischio di sovrascritture indesiderate."
      },
      {
        "question": "Posso duplicare un modello?",
        "answer": "Sì. Usa l’azione Duplica nell’elenco dei modelli. La copia viene creata come bozza e conserva contenuto, categorie, stato di preferito e tipo di nota, mentre il contatore di utilizzo riparte da zero."
      },
      {
        "question": "Posso limitare le importazioni JSON?",
        "answer": "Sì. La pagina delle impostazioni può disabilitare le importazioni JSON. Anche quando sono abilitate, le importazioni richiedono la capacità appropriata, la verifica del nonce e un file JSON valido."
      },
      {
        "question": "Come vengono gestiti i permessi?",
        "answer": "Utilizza la pagina Permessi per concedere o rimuovere le capacità del plugin per i ruoli. Gli amministratori mantengono l’accesso, così il plugin non può escludere accidentalmente il proprietario del sito."
      },
      {
        "question": "Il plugin supporta le revisioni?",
        "answer": "Sì. Il contenuto del modello viene copiato nel contenuto del post WordPress affinché le revisioni possano tenere traccia delle modifiche quando sono disponibili."
      },
      {
        "question": "Cosa devo fare prima di utilizzare un modello per messaggi reali ai clienti?",
        "answer": "Crea il modello, seleziona un ordine di prova, controlla l’anteprima, verifica tutti i segnaposto e conferma il tipo di nota selezionato prima di aggiungerla. Per i messaggi critici, utilizza prima una nota interna come prova."
      },
      {
        "question": "Perché è importante lo stato dell’email della nota per il cliente?",
        "answer": "WooCommerce può inviare un’email quando viene aggiunta una nota per il cliente. Controlla la configurazione email di WooCommerce e le informazioni di elaborazione nelle pagine Cronologia e Diagnostica."
      },
      {
        "question": "Posso utilizzare il plugin in un negozio di staging?",
        "answer": "Sì. Esportazione e importazione JSON sono utili per spostare i modelli tra staging e produzione. Dopo il trasferimento, controlla le impostazioni delle note per il cliente perché le impostazioni email di WooCommerce possono essere diverse."
      },
      {
        "question": "Quali dati vengono rimossi durante la disinstallazione?",
        "answer": "La procedura di disinstallazione rimuove i modelli e le opzioni propri del plugin. Le note WooCommerce già aggiunte agli ordini appartengono alla cronologia degli ordini e non vengono rimosse disinstallando il plugin."
      },
      {
        "question": "Come devo segnalare un problema?",
        "answer": "Documenta la versione di WordPress, la versione di WooCommerce, se HPOS è abilitato, la lingua attiva, il modello selezionato e i passaggi esatti che hanno causato il problema."
      },
      {
        "question": "Condizioni del modello",
        "answer": "Le condizioni del modello stabiliscono se un modello è disponibile per un determinato ordine. Puoi limitare i modelli in base a stato dell’ordine, metodo di pagamento, metodo di spedizione, paese di fatturazione e totale minimo o massimo dell’ordine. Tutte le condizioni configurate devono corrispondere. Lascia vuoto un campo quando la relativa condizione non deve limitare il modello."
      },
      {
        "question": "Registro dell’elaborazione email",
        "answer": "Per le note per il cliente, il plugin registra quando WooCommerce segnala che l’email della nota è stata elaborata e registra anche gli errori tecnici di wp_mail. Un evento elaborato conferma che WordPress/WooCommerce ha consegnato il messaggio al sistema di posta, ma non dimostra la consegna finale né che il cliente lo abbia letto. Controlla nella pagina Cronologia gli eventi email elaborati e non riusciti."
      },
      {
        "question": "Cronologia centralizzata",
        "answer": "Apri <strong>Note ordine Mailhilfe → Cronologia</strong> per esaminare le creazioni recenti di note, l’utilizzo dei modelli, l’elaborazione email e gli errori email. Quando disponibili, le voci includono ordine, modello, utente, destinatario, tipo di evento e ora. Usa la cronologia per assistenza, controlli e risoluzione dei problemi."
      },
      {
        "question": "Anteprima con ordine di prova",
        "answer": "Nell’editor del modello, inserisci un ID ordine WooCommerce nell’area dell’anteprima di prova. Il contenuto corrente dell’editor, incluse le modifiche non salvate, viene visualizzato con i dati di quell’ordine senza creare una nota né inviare un’email. Usa un ordine di staging o un ordine di prova non critico."
      },
      {
        "question": "Preferiti personali e modelli utilizzati di recente",
        "answer": "Ogni amministratore può contrassegnare preferiti personali nella schermata dell’ordine. Il plugin memorizza inoltre i dieci modelli utilizzati più di recente da ciascun utente e assegna loro una posizione più alta nella selezione. I preferiti globali restano condivisi con tutti gli utenti. I preferiti personali non modificano l’elenco di un altro utente."
      },
      {
        "question": "Pagina Diagnostica",
        "answer": "Apri <strong>Note ordine Mailhilfe → Diagnostica</strong> per visualizzare informazioni tecniche come versioni di WordPress, PHP e WooCommerce, stato HPOS, stato dell’email delle note per il cliente, locale, numero di modelli pubblicati, stato della cache e WP_DEBUG. Copia i valori diagnostici quando richiedi assistenza."
      },
      {
        "question": "Hook e filtri per sviluppatori",
        "answer": "Il plugin fornisce hook e filtri per segnaposto, valori dei segnaposto, chiavi meta consentite, risultati dei modelli, condizioni, contenuto dell’anteprima, contenuto finale della nota, azioni prima e dopo l’aggiunta di una nota, record della cronologia e diagnostica. Nomi e parametri degli hook sono documentati in readme.txt. Convalida, sanitizza ed esegui l’escaping di tutti i dati personalizzati."
      }
    ]
  },
  "hi_IN": {
    "menu": "अक्सर पूछे जाने वाले प्रश्न",
    "title": "अक्सर पूछे जाने वाले प्रश्न",
    "intro": "Mailhilfe Order Note Manager for WooCommerce के बारे में सामान्य प्रश्नों के उत्तर।",
    "permission": "आपको नोट टेम्पलेट प्रबंधित करने की अनुमति नहीं है।",
    "items": [
      {
        "question": "Mailhilfe Order Note Manager for WooCommerce किस काम आता है?",
        "answer": "यह प्लगइन पुनः उपयोग योग्य WooCommerce ऑर्डर नोट टेम्पलेट बनाता है। कर्मचारी ऑर्डर के भीतर टेम्पलेट चुन सकते हैं, बदले हुए प्लेसहोल्डर का पूर्वावलोकन देख सकते हैं और परिणाम को आंतरिक नोट या ग्राहक नोट के रूप में जोड़ सकते हैं।"
      },
      {
        "question": "मैं टेम्पलेट कहाँ बनाऊँ और प्रबंधित करूँ?",
        "answer": "WordPress एडमिन मेनू में Mailhilfe ऑर्डर नोट्स खोलें। वहाँ आप टेम्पलेट बना, संपादित, हटा, श्रेणीबद्ध, पसंदीदा के रूप में चिह्नित और खींचकर क्रमबद्ध कर सकते हैं।"
      },
      {
        "question": "प्लेसहोल्डर कैसे काम करते हैं?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} और {items} जैसे प्लेसहोल्डर पूर्वावलोकन में तथा नोट जोड़ते समय वास्तविक ऑर्डर डेटा से बदले जाते हैं।"
      },
      {
        "question": "क्या मैं टेम्पलेट टेक्स्ट को फ़ॉर्मेट कर सकता हूँ?",
        "answer": "हाँ। WordPress संपादक अनुच्छेद, बोल्ड और इटैलिक टेक्स्ट, सूचियाँ और लिंक का समर्थन करता है। प्लगइन सुरक्षित HTML रखता है और सहेजने या आयात करने से पहले असुरक्षित मार्कअप हटा देता है।"
      },
      {
        "question": "आंतरिक नोट और ग्राहक नोट में क्या अंतर है?",
        "answer": "आंतरिक नोट केवल दुकान टीम के लिए होते हैं। WooCommerce सेटिंग्स के आधार पर ग्राहक नोट ग्राहक को दिखाई दे सकते हैं और ईमेल सूचना भेज सकते हैं।"
      },
      {
        "question": "ग्राहक नोट सुरक्षित रूप से कैसे उपयोग किए जाएँ?",
        "answer": "ग्राहक नोट आंतरिक एडमिन क्षेत्र से बाहर जा सकते हैं। कर्मचारी ऐसा कुछ जोड़ने से पहले प्लेसहोल्डर, व्यक्तिगत डेटा, भाषा और चुना गया नोट प्रकार जाँचें जो ग्राहक तक पहुँच सकता है।"
      },
      {
        "question": "पसंदीदा, खोज और क्रमबद्धता कैसे मदद करते हैं?",
        "answer": "पसंदीदा महत्वपूर्ण टेम्पलेट को आसानी से उपलब्ध रखते हैं। खोज ऑर्डर स्क्रीन में लंबी टेम्पलेट सूची फ़िल्टर करती है। खींचकर क्रमबद्ध करने से टेम्पलेट दिखने का क्रम नियंत्रित होता है।"
      },
      {
        "question": "JSON आयात और निर्यात कैसे काम करते हैं?",
        "answer": "निर्यात टेम्पलेट शीर्षक, सामग्री, नोट प्रकार, श्रेणियाँ, पसंदीदा, क्रम और उपयोग डेटा वाली JSON फ़ाइल बनाता है। आयात टेम्पलेट को पुनर्स्थापित या दूसरी दुकान में स्थानांतरित कर सकता है।"
      },
      {
        "question": "डेमो टेम्पलेट कैसे इंस्टॉल होते हैं?",
        "answer": "आयात/निर्यात पृष्ठ खोलें और डेमो टेम्पलेट क्रिया चुनें। डेमो टेम्पलेट फ़ारसी सहित मेल खाने वाली बंडल भाषा में बनाए जाते हैं; अन्यथा समीक्षा किया गया अंग्रेज़ी सेट उपयोग होता है।"
      },
      {
        "question": "कौन-सी भूमिकाएँ प्लगइन उपयोग कर सकती हैं?",
        "answer": "प्रशासक और शॉप प्रबंधक प्लगइन क्षमताएँ अपने-आप प्राप्त करते हैं। प्लगइन टेम्पलेट प्रबंधित करने और ऑर्डर में टेम्पलेट उपयोग करने की अनुमतियाँ अलग रखता है।"
      },
      {
        "question": "क्या प्लगइन HPOS के साथ संगत है?",
        "answer": "हाँ। प्लगइन WooCommerce HPOS संगतता घोषित करता है और सीधे ऑर्डर डेटाबेस तालिकाओं के बजाय WooCommerce ऑर्डर API उपयोग करता है।"
      },
      {
        "question": "क्या प्लगइन सार्वजनिक फ्रंटएंड लिंक जोड़ता है?",
        "answer": "नहीं। प्लगइन WordPress एडमिन और WooCommerce ऑर्डर स्क्रीन में काम करता है। यह स्टोरफ्रंट पर powered-by लिंक या सार्वजनिक प्रचार लिंक नहीं जोड़ता।"
      },
      {
        "question": "यदि भाषा गलत हो तो मुझे क्या जाँचना चाहिए?",
        "answer": "सेटिंग्स > सामान्य > साइट भाषा और अपने उपयोगकर्ता प्रोफ़ाइल की भाषा जाँचें। प्लगइन में फ़ारसी सहित सभी समर्थित भाषाओं के लिए समीक्षा किए गए अनुवाद शामिल हैं। अन्य भाषाओं के लिए समीक्षा किए गए WordPress.org भाषा पैक उपयोग करें।"
      },
      {
        "question": "क्या नोट जोड़ने से पहले पूर्वावलोकन संपादित किया जा सकता है?",
        "answer": "हाँ। टेम्पलेट चुनने के बाद पूर्वावलोकन में बदला हुआ ऑर्डर डेटा दिखाई देता है और नोट सहेजने से पहले उसे संपादित किया जा सकता है। संपादित पूर्वावलोकन ही ऑर्डर में जोड़ा जाने वाला अंतिम नोट होता है।"
      },
      {
        "question": "ग्राहक नोट निर्माण और ईमेल संसाधन कैसे दर्ज होते हैं?",
        "answer": "संबंधित ईमेल सक्षम होने पर ग्राहक नोट WooCommerce ईमेल सूचना भेज सकते हैं। प्लगइन ग्राहक नोट निर्माण और ईमेल संसाधन को अलग-अलग दर्ज करता है। नोट जोड़ने से पहले संपादन योग्य पूर्वावलोकन देखें और मेल हैंडलर परिणाम के लिए इतिहास पृष्ठ उपयोग करें।"
      },
      {
        "question": "टेम्पलेट भाषाएँ कैसे काम करती हैं?",
        "answer": "हर टेम्पलेट सभी भाषाओं या किसी विशिष्ट बंडल भाषा को सौंपा जा सकता है। बहुभाषी दुकान में प्लगइन ऑर्डर भाषा, उपयोगकर्ता भाषा या साइट भाषा से मेल खाने वाले टेम्पलेट को प्राथमिकता देने का प्रयास करता है।"
      },
      {
        "question": "क्या मैं कस्टम ऑर्डर या ग्राहक मेटा फ़ील्ड उपयोग कर सकता हूँ?",
        "answer": "हाँ। {order_meta:meta_key} और {customer_meta:meta_key} जैसे उन्नत प्लेसहोल्डर कस्टम फ़ील्ड पढ़ सकते हैं। password, token, secret, session, auth या hash जैसे शब्दों वाली संवेदनशील कुंजियाँ अवरुद्ध होती हैं।"
      },
      {
        "question": "क्या कर्मचारी गलती से निजी जानकारी उजागर कर सकते हैं?",
        "answer": "प्लगइन हर प्लेसहोल्डर का व्यावसायिक अर्थ नहीं जान सकता। ग्राहक नोट सहेजने से पहले हमेशा समीक्षा करें, विशेषकर मेटा प्लेसहोल्डर या ग्राहक-विशिष्ट डेटा उपयोग करते समय।"
      },
      {
        "question": "HTML फ़ॉर्मेटिंग बंद होने पर क्या होता है?",
        "answer": "यदि HTML फ़ॉर्मेटिंग सेटिंग बंद है, तो फ़ॉर्मेट किया हुआ टेम्पलेट टेक्स्ट नोट सहेजने से पहले सुरक्षित सादे टेक्स्ट में बदल दिया जाता है। यह उन दुकानों के लिए उपयोगी है जिन्हें केवल सरल नोट चाहिए।"
      },
      {
        "question": "आयात पूर्वावलोकन मौजूदा टेम्पलेट को कैसे सुरक्षित रखता है?",
        "answer": "अंतिम आयात से पहले आयात पूर्वावलोकन दिखाता है कि कितने टेम्पलेट बनाए, अपडेट या छोड़े जाएंगे। इससे अनचाहे ओवरराइट से बचने में मदद मिलती है।"
      },
      {
        "question": "क्या मैं टेम्पलेट की प्रतिलिपि बना सकता हूँ?",
        "answer": "हाँ। टेम्पलेट सूची में प्रतिलिपि क्रिया उपयोग करें। प्रति ड्राफ़्ट के रूप में बनती है और सामग्री, श्रेणियाँ, पसंदीदा स्थिति व नोट प्रकार रखती है, जबकि उपयोग काउंटर शून्य से शुरू होता है।"
      },
      {
        "question": "क्या JSON आयात सीमित किया जा सकता है?",
        "answer": "हाँ। सेटिंग्स पृष्ठ JSON आयात बंद कर सकता है। सक्षम होने पर भी आयात के लिए उचित क्षमता, nonce सत्यापन और वैध JSON फ़ाइल आवश्यक है।"
      },
      {
        "question": "अनुमतियाँ कैसे प्रबंधित होती हैं?",
        "answer": "भूमिकाओं के लिए प्लगइन क्षमताएँ देने या हटाने के लिए अनुमतियाँ पृष्ठ उपयोग करें। प्रशासकों की पहुँच बनी रहती है ताकि साइट स्वामी गलती से लॉक आउट न हो।"
      },
      {
        "question": "क्या प्लगइन संशोधन का समर्थन करता है?",
        "answer": "हाँ। टेम्पलेट सामग्री WordPress पोस्ट सामग्री में भी सहेजी जाती है, इसलिए उपलब्ध होने पर WordPress संशोधन बदलावों को ट्रैक कर सकते हैं।"
      },
      {
        "question": "वास्तविक ग्राहक संदेश के लिए टेम्पलेट उपयोग करने से पहले क्या करना चाहिए?",
        "answer": "टेम्पलेट बनाएँ, परीक्षण ऑर्डर चुनें, पूर्वावलोकन जाँचें, सभी प्लेसहोल्डर सत्यापित करें और नोट जोड़ने से पहले चुना गया नोट प्रकार पुष्ट करें। महत्वपूर्ण संदेश के लिए पहले आंतरिक नोट के रूप में परीक्षण करें।"
      },
      {
        "question": "ग्राहक नोट ईमेल स्थिति क्यों महत्वपूर्ण है?",
        "answer": "ग्राहक नोट जोड़ने पर WooCommerce ईमेल भेज सकता है। WooCommerce ईमेल कॉन्फ़िगरेशन जाँचें और इतिहास तथा निदान पृष्ठों पर संसाधन जानकारी देखें।"
      },
      {
        "question": "क्या प्लगइन स्टेजिंग दुकान में उपयोग किया जा सकता है?",
        "answer": "हाँ। JSON निर्यात और आयात टेम्पलेट को स्टेजिंग और उत्पादन के बीच स्थानांतरित करने के लिए उपयोगी हैं। स्थानांतरण के बाद ग्राहक नोट सेटिंग्स जाँचें क्योंकि WooCommerce ईमेल सेटिंग्स अलग हो सकती हैं।"
      },
      {
        "question": "अनइंस्टॉल करते समय कौन-सा डेटा हटता है?",
        "answer": "अनइंस्टॉल प्रक्रिया प्लगइन के अपने टेम्पलेट और विकल्प हटाती है। ऑर्डर में पहले से जोड़े गए WooCommerce ऑर्डर नोट WooCommerce ऑर्डर इतिहास का हिस्सा हैं और प्लगइन हटाने से नहीं मिटते।"
      },
      {
        "question": "समस्या की रिपोर्ट कैसे करनी चाहिए?",
        "answer": "WordPress संस्करण, WooCommerce संस्करण, HPOS सक्षम है या नहीं, सक्रिय भाषा, चुना गया टेम्पलेट और समस्या उत्पन्न करने वाले सटीक चरण लिखें।"
      },
      {
        "question": "टेम्पलेट शर्तें",
        "answer": "टेम्पलेट शर्तें तय करती हैं कि कोई टेम्पलेट किसी विशेष ऑर्डर के लिए उपलब्ध है या नहीं। आप ऑर्डर स्थिति, भुगतान विधि, शिपिंग विधि, बिलिंग देश और न्यूनतम या अधिकतम ऑर्डर कुल से टेम्पलेट सीमित कर सकते हैं। सभी कॉन्फ़िगर की गई शर्तों का मिलना आवश्यक है। जिस शर्त से टेम्पलेट सीमित नहीं करना है उसका फ़ील्ड खाली छोड़ें।"
      },
      {
        "question": "ईमेल संसाधन लॉग",
        "answer": "ग्राहक नोट्स के लिए प्लगइन दर्ज करता है कि WooCommerce ने ग्राहक नोट ईमेल को संसाधित बताया या नहीं और तकनीकी wp_mail त्रुटियाँ भी दर्ज करता है। संसाधित घटना पुष्टि करती है कि WordPress/WooCommerce ने संदेश मेल प्रणाली को सौंपा, लेकिन यह अंतिम डिलीवरी या ग्राहक द्वारा पढ़े जाने का प्रमाण नहीं है। संसाधित और विफल ईमेल घटनाओं के लिए इतिहास पृष्ठ देखें।"
      },
      {
        "question": "केंद्रीय इतिहास",
        "answer": "हाल में बने नोट, टेम्पलेट उपयोग, ईमेल संसाधन और ईमेल विफलताएँ देखने के लिए <strong>Mailhilfe ऑर्डर नोट्स → इतिहास</strong> खोलें। उपलब्ध होने पर प्रविष्टियों में ऑर्डर, टेम्पलेट, उपयोगकर्ता, प्राप्तकर्ता, घटना प्रकार और समय शामिल होते हैं। सहायता, ऑडिट और समस्या निवारण के लिए इतिहास उपयोग करें।"
      },
      {
        "question": "परीक्षण ऑर्डर पूर्वावलोकन",
        "answer": "टेम्पलेट संपादक के परीक्षण पूर्वावलोकन क्षेत्र में WooCommerce ऑर्डर ID दर्ज करें। सहेजे न गए बदलावों सहित वर्तमान सामग्री उस ऑर्डर डेटा के साथ दिखाई जाती है, बिना नोट बनाए या ईमेल भेजे। स्टेजिंग या गैर-महत्वपूर्ण परीक्षण ऑर्डर उपयोग करें।"
      },
      {
        "question": "व्यक्तिगत पसंदीदा और हाल में उपयोग किए गए टेम्पलेट",
        "answer": "हर प्रशासक ऑर्डर स्क्रीन में व्यक्तिगत पसंदीदा चिह्नित कर सकता है। प्लगइन प्रत्येक उपयोगकर्ता के दस सबसे हाल में उपयोग किए गए टेम्पलेट भी सहेजता है और उन्हें चयन सूची में ऊपर रखता है। वैश्विक पसंदीदा सभी उपयोगकर्ताओं के साथ साझा रहते हैं। व्यक्तिगत पसंदीदा दूसरे उपयोगकर्ता की सूची नहीं बदलते।"
      },
      {
        "question": "निदान पृष्ठ",
        "answer": "WordPress, PHP और WooCommerce संस्करण, HPOS स्थिति, ग्राहक नोट ईमेल स्थिति, लोकेल, प्रकाशित टेम्पलेट संख्या, कैश स्थिति और WP_DEBUG जैसी तकनीकी जानकारी देखने के लिए <strong>Mailhilfe ऑर्डर नोट्स → निदान</strong> खोलें। सहायता माँगते समय निदान मान कॉपी करें।"
      },
      {
        "question": "डेवलपर हुक और फ़िल्टर",
        "answer": "प्लगइन प्लेसहोल्डर, प्लेसहोल्डर मान, अनुमत मेटा कुंजियाँ, टेम्पलेट परिणाम, शर्तें, पूर्वावलोकन सामग्री, अंतिम नोट सामग्री, नोट जोड़ने से पहले और बाद की क्रियाएँ, इतिहास रिकॉर्ड और निदान के लिए हुक व फ़िल्टर देता है। हुक नाम और पैरामीटर readme.txt में दिए गए हैं। सभी कस्टम डेटा को वैलिडेट, सैनिटाइज़ और एस्केप करें।"
      }
    ]
  },
  "zh_CN": {
    "menu": "常见问题",
    "title": "常见问题解答",
    "intro": "关于 Mailhilfe Order Note Manager for WooCommerce 的常见问题及答案。",
    "permission": "您无权管理备注模板。",
    "items": [
      {
        "question": "Mailhilfe Order Note Manager for WooCommerce 有什么用途？",
        "answer": "该插件用于创建可重复使用的 WooCommerce 订单备注模板。员工可以在订单中选择模板，预览替换占位符后的内容，并将结果添加为内部备注或客户备注。"
      },
      {
        "question": "在哪里创建和管理模板？",
        "answer": "在 WordPress 后台菜单中打开“Mailhilfe 订单备注”。您可以在此创建、编辑、删除和分类模板，将模板设为收藏，并通过拖放排序。"
      },
      {
        "question": "占位符如何工作？",
        "answer": "{order_number}、{customer}、{billing_email}、{order_total} 和 {items} 等占位符会在预览以及添加备注时被真实订单数据替换。"
      },
      {
        "question": "可以设置模板文本格式吗？",
        "answer": "可以。WordPress 编辑器支持段落、粗体、斜体、列表和链接。插件会保留安全 HTML，并在保存或导入前删除不安全的标记。"
      },
      {
        "question": "内部备注与客户备注有什么区别？",
        "answer": "内部备注仅供商店团队使用。客户备注可能对客户可见，并且根据 WooCommerce 设置可能触发电子邮件通知。"
      },
      {
        "question": "如何安全使用客户备注？",
        "answer": "客户备注可能离开内部后台。添加可能发送给客户的内容前，员工应检查占位符、个人数据、措辞和所选备注类型。"
      },
      {
        "question": "收藏、搜索和排序有什么帮助？",
        "answer": "收藏可让重要模板更容易找到；搜索可在订单页面筛选较长的模板列表；拖放排序可控制模板显示顺序。"
      },
      {
        "question": "JSON 导入和导出如何工作？",
        "answer": "导出会创建包含模板标题、内容、备注类型、分类、收藏、排序和使用数据的 JSON 文件。导入可恢复模板或将模板迁移到另一家商店。"
      },
      {
        "question": "如何安装演示模板？",
        "answer": "打开导入/导出页面并选择演示模板操作。演示模板会使用匹配的内置语言（包括波斯语）创建；否则使用经过审核的英语模板。"
      },
      {
        "question": "哪些角色可以使用该插件？",
        "answer": "管理员和商店经理会自动获得插件权限。插件将“管理模板”权限与“在订单中使用模板”权限分开。"
      },
      {
        "question": "插件兼容 HPOS 吗？",
        "answer": "兼容。插件声明支持 WooCommerce HPOS，并通过 WooCommerce 订单 API 访问订单，而不是直接访问订单数据库表。"
      },
      {
        "question": "插件会在前台添加公开链接吗？",
        "answer": "不会。插件仅在 WordPress 后台和 WooCommerce 订单页面工作，不会向商店前台添加“Powered by”链接或公开推广链接。"
      },
      {
        "question": "语言不正确时应检查什么？",
        "answer": "请检查“设置 > 常规 > 站点语言”和用户个人资料中的语言。插件内置所有受支持语言（包括波斯语）的审核翻译。其他语言应使用经过审核的 WordPress.org 语言包。"
      },
      {
        "question": "添加备注前可以编辑预览吗？",
        "answer": "可以。选择模板后，预览会包含替换后的订单数据，并可在保存备注前编辑。编辑后的预览就是最终添加到订单中的备注。"
      },
      {
        "question": "如何记录客户备注创建和电子邮件处理？",
        "answer": "启用相应邮件后，客户备注可能触发 WooCommerce 电子邮件通知。插件会分别记录客户备注创建和邮件处理状态。添加备注前请检查可编辑预览，并在“历史记录”页面查看邮件处理结果。"
      },
      {
        "question": "模板语言如何工作？",
        "answer": "每个模板都可以分配给所有语言或某个内置语言。在多语言商店中，插件会优先匹配订单语言、用户语言或站点语言的模板。"
      },
      {
        "question": "可以使用订单或客户的自定义元数据字段吗？",
        "answer": "可以。{order_meta:meta_key} 和 {customer_meta:meta_key} 等高级占位符可读取自定义字段。包含 password、token、secret、session、auth 或 hash 等词的敏感键会被阻止。"
      },
      {
        "question": "员工会不会意外泄露私密信息？",
        "answer": "插件无法判断每个占位符的业务含义。保存客户备注前务必检查内容，尤其是在使用元数据占位符或客户专属数据时。"
      },
      {
        "question": "禁用 HTML 格式后会怎样？",
        "answer": "禁用 HTML 格式设置后，带格式的模板内容会在保存备注前转换为安全纯文本，适合只需要简单备注的商店。"
      },
      {
        "question": "导入预览如何保护现有模板？",
        "answer": "执行最终导入前，导入预览会显示将创建、更新或跳过多少个模板，从而帮助避免不必要的覆盖。"
      },
      {
        "question": "可以复制模板吗？",
        "answer": "可以。在模板列表中使用“复制”操作。副本以草稿创建，并保留内容、分类、收藏状态和备注类型，但使用次数从零开始。"
      },
      {
        "question": "可以限制 JSON 导入吗？",
        "answer": "可以。可在设置页面禁用 JSON 导入。即使启用，导入仍需要相应权限、nonce 验证和有效的 JSON 文件。"
      },
      {
        "question": "如何管理权限？",
        "answer": "使用权限页面为角色授予或移除插件权限。管理员会保留访问权限，避免意外将站点所有者锁定在插件之外。"
      },
      {
        "question": "插件支持修订版本吗？",
        "answer": "支持。模板内容会同步到 WordPress 文章内容，因此在修订功能可用时，WordPress 修订版本可以跟踪更改。"
      },
      {
        "question": "将模板用于真实客户消息前应做什么？",
        "answer": "创建模板后，选择测试订单，检查预览和所有占位符，并确认备注类型后再添加。对于关键消息，可先用内部备注进行测试。"
      },
      {
        "question": "为什么客户备注电子邮件状态很重要？",
        "answer": "添加客户备注时 WooCommerce 可能发送电子邮件。请检查 WooCommerce 邮件配置，并查看“历史记录”和“诊断”页面中的处理信息。"
      },
      {
        "question": "可以在测试商店中使用插件吗？",
        "answer": "可以。JSON 导入和导出适合在测试环境与生产环境之间迁移模板。迁移后请检查客户备注设置，因为不同环境的 WooCommerce 邮件设置可能不同。"
      },
      {
        "question": "卸载时会删除哪些数据？",
        "answer": "卸载程序会删除插件自身的模板和插件选项。已经添加到订单中的 WooCommerce 订单备注属于 WooCommerce 订单历史，卸载插件时不会删除。"
      },
      {
        "question": "如何报告问题？",
        "answer": "请记录 WordPress 版本、WooCommerce 版本、是否启用 HPOS、当前语言、所选模板，以及触发问题的确切步骤。"
      },
      {
        "question": "模板条件",
        "answer": "模板条件决定模板是否适用于某个订单。您可以按订单状态、付款方式、配送方式、账单国家/地区以及订单最低或最高金额限制模板。所有已配置条件都必须匹配。不需要限制模板的字段请留空。"
      },
      {
        "question": "电子邮件处理日志",
        "answer": "对于客户备注，插件会记录 WooCommerce 报告客户备注邮件已处理的时间，也会记录 wp_mail 技术错误。“已处理”表示 WordPress/WooCommerce 已将邮件交给邮件系统，但不能证明最终送达或客户已阅读。请在“历史记录”页面查看已处理和失败的邮件事件。"
      },
      {
        "question": "集中历史记录",
        "answer": "打开 <strong>Mailhilfe 订单备注 → 历史记录</strong>，查看最近的备注创建、模板使用、电子邮件处理和邮件失败记录。可用时，记录包含订单、模板、用户、收件人、事件类型和时间，可用于支持、审计和故障排除。"
      },
      {
        "question": "测试订单预览",
        "answer": "在模板编辑器的测试预览区域输入 WooCommerce 订单 ID。系统会使用该订单的数据渲染当前编辑器内容（包括未保存的更改），不会创建备注或发送电子邮件。请使用测试订单或非关键订单。"
      },
      {
        "question": "个人收藏与最近使用的模板",
        "answer": "每位管理员都可以在订单页面标记个人收藏。插件还会为每个用户保存最近使用的 10 个模板，并在选择列表中提高其位置。全局收藏仍由所有用户共享，个人收藏不会改变其他用户的列表。"
      },
      {
        "question": "诊断页面",
        "answer": "打开 <strong>Mailhilfe 订单备注 → 诊断</strong>，可查看 WordPress、PHP 和 WooCommerce 版本、HPOS 状态、客户备注邮件状态、区域设置、已发布模板数量、缓存状态和 WP_DEBUG 等技术信息。请求支持时请复制这些诊断值。"
      },
      {
        "question": "开发者钩子与过滤器",
        "answer": "插件为占位符、占位符值、允许的元数据键、模板结果、条件、预览内容、最终备注内容、添加备注前后的操作、历史记录和诊断提供钩子与过滤器。钩子名称和参数记录在 readme.txt 中。请验证、清理并转义所有自定义数据。"
      }
    ]
  },
  "ja": {
    "menu": "よくある質問",
    "title": "よくある質問",
    "intro": "Mailhilfe Order Note Manager for WooCommerce に関するよくある質問と回答です。",
    "permission": "メモテンプレートを管理する権限がありません。",
    "items": [
      {
        "question": "Mailhilfe Order Note Manager for WooCommerce は何に使用しますか？",
        "answer": "再利用可能な WooCommerce 注文メモテンプレートを作成するプラグインです。スタッフは注文内でテンプレートを選択し、プレースホルダー置換後の内容をプレビューして、内部メモまたは顧客向けメモとして追加できます。"
      },
      {
        "question": "テンプレートはどこで作成、管理できますか？",
        "answer": "WordPress 管理メニューの「Mailhilfe 注文メモ」を開きます。テンプレートの作成、編集、削除、カテゴリー分け、お気に入り設定、ドラッグ＆ドロップによる並べ替えができます。"
      },
      {
        "question": "プレースホルダーはどのように動作しますか？",
        "answer": "{order_number}、{customer}、{billing_email}、{order_total}、{items} などのプレースホルダーは、プレビュー表示時とメモ追加時に実際の注文データへ置き換えられます。"
      },
      {
        "question": "テンプレート本文に書式を設定できますか？",
        "answer": "はい。WordPress エディターでは段落、太字、斜体、リスト、リンクを使用できます。プラグインは安全な HTML を保持し、保存またはインポート前に安全でないマークアップを削除します。"
      },
      {
        "question": "内部メモと顧客向けメモの違いは何ですか？",
        "answer": "内部メモはショップチームだけが使用します。顧客向けメモは顧客に表示される可能性があり、WooCommerce の設定によってはメール通知が送信されます。"
      },
      {
        "question": "顧客向けメモを安全に使用するにはどうすればよいですか？",
        "answer": "顧客向けメモは管理画面の外へ送信される可能性があります。顧客に届く可能性がある内容を追加する前に、プレースホルダー、個人情報、表現、選択したメモ種類を確認してください。"
      },
      {
        "question": "お気に入り、検索、並べ替えはどのように役立ちますか？",
        "answer": "お気に入りで重要なテンプレートへすぐにアクセスできます。検索は注文画面の長いテンプレート一覧を絞り込みます。ドラッグ＆ドロップによる並べ替えで表示順を変更できます。"
      },
      {
        "question": "JSON のインポートとエクスポートはどのように動作しますか？",
        "answer": "エクスポートでは、テンプレートのタイトル、内容、メモ種類、カテゴリー、お気に入り、並び順、使用データを含む JSON ファイルを作成します。インポートではテンプレートを復元したり、別のショップへ移行したりできます。"
      },
      {
        "question": "デモテンプレートはどのようにインストールしますか？",
        "answer": "インポート/エクスポートページを開き、デモテンプレート操作を選択します。デモテンプレートは、ペルシア語を含む一致する同梱言語で作成されます。一致しない場合はレビュー済みの英語版を使用します。"
      },
      {
        "question": "どの権限グループがプラグインを使用できますか？",
        "answer": "管理者とショップマネージャーにはプラグイン権限が自動的に付与されます。テンプレートを管理する権限と、注文でテンプレートを使用する権限は分かれています。"
      },
      {
        "question": "HPOS に対応していますか？",
        "answer": "はい。WooCommerce HPOS への対応を宣言しており、注文データベーステーブルへ直接アクセスせず WooCommerce の注文 API を使用します。"
      },
      {
        "question": "公開サイト側にリンクを追加しますか？",
        "answer": "いいえ。プラグインは WordPress 管理画面と WooCommerce 注文画面でのみ動作します。ショップの公開画面に提供元リンクや宣伝リンクを追加しません。"
      },
      {
        "question": "表示言語が正しくない場合は何を確認すればよいですか？",
        "answer": "「設定 → 一般 → サイトの言語」とユーザープロフィールの言語を確認してください。ペルシア語を含むすべての対応言語のレビュー済み翻訳が同梱されています。その他の言語には、レビュー済みの WordPress.org 言語パックを使用してください。"
      },
      {
        "question": "メモを追加する前にプレビューを編集できますか？",
        "answer": "はい。テンプレートを選択すると、注文データを置換したプレビューが表示され、保存前に編集できます。編集後のプレビューが注文に追加される最終メモになります。"
      },
      {
        "question": "顧客向けメモの作成とメール処理はどのように記録されますか？",
        "answer": "対応するメールが有効な場合、顧客向けメモによって WooCommerce のメール通知が送信されることがあります。プラグインは顧客向けメモの作成とメール処理を別々に記録します。メモ追加前に編集可能なプレビューを確認し、「履歴」ページでメール処理結果を確認してください。"
      },
      {
        "question": "テンプレート言語はどのように動作しますか？",
        "answer": "各テンプレートは、すべての言語または同梱されている特定の言語に割り当てられます。多言語ショップでは、注文言語、ユーザー言語、サイト言語に一致するテンプレートを優先します。"
      },
      {
        "question": "注文または顧客のカスタムメタフィールドを使用できますか？",
        "answer": "はい。{order_meta:meta_key} や {customer_meta:meta_key} などの高度なプレースホルダーでカスタムフィールドを読み取れます。password、token、secret、session、auth、hash などを含む機密キーはブロックされます。"
      },
      {
        "question": "スタッフが誤って非公開情報を表示する可能性はありますか？",
        "answer": "プラグインは各プレースホルダーの業務上の意味を判断できません。特にメタプレースホルダーや顧客固有データを使用する場合は、顧客向けメモを保存する前に必ず確認してください。"
      },
      {
        "question": "HTML 書式を無効にするとどうなりますか？",
        "answer": "HTML 書式設定を無効にすると、書式付きのテンプレート内容はメモ保存前に安全なプレーンテキストへ変換されます。単純なメモだけを使用したいショップに適しています。"
      },
      {
        "question": "インポートプレビューは既存テンプレートをどのように保護しますか？",
        "answer": "最終インポートを実行する前に、作成、更新、スキップされるテンプレート数が表示されます。意図しない上書きを避けるのに役立ちます。"
      },
      {
        "question": "テンプレートを複製できますか？",
        "answer": "はい。テンプレート一覧の「複製」を使用します。コピーは下書きとして作成され、内容、カテゴリー、お気に入り状態、メモ種類を保持しますが、使用回数は 0 から始まります。"
      },
      {
        "question": "JSON インポートを制限できますか？",
        "answer": "はい。設定ページで JSON インポートを無効にできます。有効な場合でも、適切な権限、nonce 検証、有効な JSON ファイルが必要です。"
      },
      {
        "question": "権限はどのように管理しますか？",
        "answer": "権限ページで権限グループごとにプラグイン権限を付与または削除します。サイト所有者が誤って締め出されないよう、管理者のアクセス権は保持されます。"
      },
      {
        "question": "リビジョンに対応していますか？",
        "answer": "はい。テンプレート内容は WordPress の投稿本文にも同期されるため、リビジョン機能が利用可能な場合は変更履歴を追跡できます。"
      },
      {
        "question": "実際の顧客メッセージにテンプレートを使用する前に何をすべきですか？",
        "answer": "テンプレートを作成し、テスト注文を選択して、プレビューとすべてのプレースホルダーを確認し、選択したメモ種類を確定してから追加します。重要なメッセージは、最初に内部メモでテストしてください。"
      },
      {
        "question": "顧客向けメモのメールステータスが重要なのはなぜですか？",
        "answer": "顧客向けメモを追加すると WooCommerce がメールを送信する場合があります。WooCommerce のメール設定を確認し、「履歴」と「診断」ページで処理情報を確認してください。"
      },
      {
        "question": "ステージングショップで使用できますか？",
        "answer": "はい。JSON のエクスポートとインポートは、ステージング環境と本番環境の間でテンプレートを移動するのに適しています。WooCommerce のメール設定が異なる場合があるため、移行後に顧客向けメモ設定を確認してください。"
      },
      {
        "question": "アンインストール時に削除されるデータは何ですか？",
        "answer": "アンインストール処理では、プラグイン独自のテンプレートと設定を削除します。すでに注文へ追加された WooCommerce 注文メモは注文履歴の一部であり、プラグインをアンインストールしても削除されません。"
      },
      {
        "question": "問題はどのように報告すればよいですか？",
        "answer": "WordPress バージョン、WooCommerce バージョン、HPOS の有効状態、使用言語、選択したテンプレート、問題が発生した正確な手順を記録してください。"
      },
      {
        "question": "テンプレート条件",
        "answer": "テンプレート条件は、特定の注文でテンプレートを使用できるかを決定します。注文ステータス、支払い方法、配送方法、請求先の国、注文合計の下限または上限で制限できます。設定したすべての条件を満たす必要があります。制限しない条件は空欄にしてください。"
      },
      {
        "question": "メール処理ログ",
        "answer": "顧客向けメモでは、WooCommerce が顧客向けメモメールを処理した時刻と、wp_mail の技術的エラーを記録します。処理済みイベントは WordPress/WooCommerce がメールシステムへメッセージを渡したことを示しますが、最終配信や顧客の開封を証明しません。「履歴」ページで処理済みおよび失敗したメールイベントを確認してください。"
      },
      {
        "question": "一元化された履歴",
        "answer": "<strong>Mailhilfe 注文メモ → 履歴</strong>を開くと、最近のメモ作成、テンプレート使用、メール処理、メール失敗を確認できます。利用可能な場合、注文、テンプレート、ユーザー、受信者、イベント種類、時刻が記録されます。サポート、監査、トラブルシューティングに使用できます。"
      },
      {
        "question": "テスト注文プレビュー",
        "answer": "テンプレートエディターのテストプレビュー欄に WooCommerce の注文 ID を入力します。未保存の変更を含む現在のエディター内容が、その注文データで表示されます。メモ作成やメール送信は行われません。ステージング注文または重要でないテスト注文を使用してください。"
      },
      {
        "question": "個人のお気に入りと最近使用したテンプレート",
        "answer": "各管理ユーザーは注文画面で個人のお気に入りを設定できます。また、ユーザーごとに最近使用した 10 件のテンプレートを保存し、選択リストで上位に表示します。グローバルのお気に入りは全ユーザーで共有されます。個人のお気に入りは他のユーザーの一覧を変更しません。"
      },
      {
        "question": "診断ページ",
        "answer": "<strong>Mailhilfe 注文メモ → 診断</strong>を開くと、WordPress、PHP、WooCommerce のバージョン、HPOS 状態、顧客向けメモメール状態、ロケール、公開済みテンプレート数、キャッシュ状態、WP_DEBUG などの技術情報を確認できます。サポート依頼時に診断値をコピーしてください。"
      },
      {
        "question": "開発者向けフックとフィルター",
        "answer": "プレースホルダー、プレースホルダー値、許可するメタキー、テンプレート結果、条件、プレビュー内容、最終メモ内容、メモ追加前後のアクション、履歴レコード、診断向けのフックとフィルターを提供します。フック名と引数は readme.txt に記載されています。すべてのカスタムデータを検証、サニタイズ、エスケープしてください。"
      }
    ]
  },
  "nl_NL": {
    "menu": "Veelgestelde vragen",
    "title": "Veelgestelde vragen",
    "intro": "Antwoorden op veelgestelde vragen over Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Je hebt geen toestemming om notitiesjablonen te beheren.",
    "items": [
      {
        "question": "Waarvoor wordt Mailhilfe Order Note Manager for WooCommerce gebruikt?",
        "answer": "De plugin maakt herbruikbare sjablonen voor WooCommerce-bestelnotities. Medewerkers kunnen in een bestelling een sjabloon selecteren, de ingevulde plaatshouders vooraf bekijken en het resultaat als interne notitie of klantnotitie toevoegen."
      },
      {
        "question": "Waar maak en beheer ik sjablonen?",
        "answer": "Open Mailhilfe Bestelnotities in het WordPress-beheermenu. Daar kun je sjablonen maken, bewerken, verwijderen, categoriseren, als favoriet markeren en met slepen en neerzetten sorteren."
      },
      {
        "question": "Hoe werken plaatshouders?",
        "answer": "Plaatshouders zoals {order_number}, {customer}, {billing_email}, {order_total} en {items} worden in het voorbeeld en bij het toevoegen van de notitie vervangen door echte bestelgegevens."
      },
      {
        "question": "Kan ik sjabloonteksten opmaken?",
        "answer": "Ja. De WordPress-editor ondersteunt alinea’s, vet en cursief, lijsten en links. De plugin behoudt veilige HTML en verwijdert onveilige opmaak vóór opslaan of importeren."
      },
      {
        "question": "Wat is het verschil tussen interne notities en klantnotities?",
        "answer": "Interne notities zijn alleen voor het winkelteam bedoeld. Klantnotities kunnen zichtbaar zijn voor de klant en kunnen, afhankelijk van je WooCommerce-instellingen, een e-mailmelding activeren."
      },
      {
        "question": "Hoe ga ik veilig om met klantnotities?",
        "answer": "Klantnotities kunnen het interne beheergebied verlaten. Medewerkers moeten plaatshouders, persoonsgegevens, formulering en het geselecteerde notitietype controleren voordat zij iets toevoegen dat de klant kan bereiken."
      },
      {
        "question": "Hoe helpen favorieten, zoeken en sorteren?",
        "answer": "Met favorieten blijven belangrijke sjablonen gemakkelijk bereikbaar. Zoeken filtert lange sjablonenlijsten in het bestelscherm. De volgorde van sjablonen wordt bepaald met slepen en neerzetten."
      },
      {
        "question": "Hoe werken JSON-import en -export?",
        "answer": "De export maakt een JSON-bestand met sjabloontitels, inhoud, notitietypen, categorieën, favorieten, sortering en gebruiksgegevens. Met import kun je sjablonen herstellen of naar een andere winkel overzetten."
      },
      {
        "question": "Hoe worden demosjablonen geïnstalleerd?",
        "answer": "Open de import-/exportpagina en kies de actie voor demosjablonen. Demosjablonen worden in de overeenkomstige ingebouwde taal gemaakt, waaronder Perzisch; anders wordt de gecontroleerde Engelse set gebruikt."
      },
      {
        "question": "Welke rollen kunnen de plugin gebruiken?",
        "answer": "Beheerders en winkelmanagers ontvangen de pluginrechten automatisch. De plugin scheidt het recht om sjablonen te beheren van het recht om sjablonen in bestellingen te gebruiken."
      },
      {
        "question": "Is de plugin compatibel met HPOS?",
        "answer": "Ja. De plugin verklaart compatibiliteit met WooCommerce HPOS en gebruikt WooCommerce-bestel-API’s in plaats van rechtstreekse toegang tot besteltabellen in de database."
      },
      {
        "question": "Voegt de plugin openbare links aan de voorkant toe?",
        "answer": "Nee. De plugin werkt in het WordPress-beheer en in WooCommerce-bestelschermen. Er worden geen powered-by-links of openbare promotielinks aan de winkel toegevoegd."
      },
      {
        "question": "Wat moet ik controleren als de taal niet klopt?",
        "answer": "Controleer Instellingen > Algemeen > Sitetaal en de taal in je gebruikersprofiel. De plugin bevat gecontroleerde vertalingen voor alle ondersteunde talen, waaronder Perzisch. Andere talen moeten gecontroleerde WordPress.org-taalpakketten gebruiken."
      },
      {
        "question": "Kan ik het voorbeeld bewerken voordat ik de notitie toevoeg?",
        "answer": "Ja. Na het selecteren van een sjabloon bevat het voorbeeld de ingevulde bestelgegevens en kun je het bewerken voordat de notitie wordt opgeslagen. Het bewerkte voorbeeld is de uiteindelijke notitie die aan de bestelling wordt toegevoegd."
      },
      {
        "question": "Hoe worden het aanmaken van klantnotities en de e-mailverwerking geregistreerd?",
        "answer": "Klantnotities kunnen WooCommerce-e-mailmeldingen activeren wanneer de bijbehorende e-mail is ingeschakeld. De plugin registreert het aanmaken van de klantnotitie afzonderlijk van de e-mailverwerking. Controleer het bewerkbare voorbeeld voordat je de notitie toevoegt en gebruik de pagina Geschiedenis om het resultaat van de e-mailafhandeling te bekijken."
      },
      {
        "question": "Hoe werken sjabloontalen?",
        "answer": "Elk sjabloon kan aan alle talen of aan een specifieke ingebouwde taal worden toegewezen. In meertalige winkels probeert de plugin de voorkeur te geven aan sjablonen die overeenkomen met de taal van de bestelling, de gebruiker of de site."
      },
      {
        "question": "Kan ik aangepaste meta-velden van bestellingen of klanten gebruiken?",
        "answer": "Ja. Geavanceerde plaatshouders zoals {order_meta:meta_key} en {customer_meta:meta_key} kunnen aangepaste velden lezen. Gevoelige sleutels met woorden als password, token, secret, session, auth of hash worden geblokkeerd."
      },
      {
        "question": "Kunnen medewerkers per ongeluk privégegevens openbaar maken?",
        "answer": "De plugin kan de zakelijke betekenis van iedere plaatshouder niet kennen. Controleer klantnotities altijd voordat je ze opslaat, vooral wanneer je metaplaatshouders of klantspecifieke gegevens gebruikt."
      },
      {
        "question": "Wat gebeurt er als HTML-opmaak is uitgeschakeld?",
        "answer": "Als de instelling voor HTML-opmaak is uitgeschakeld, wordt opgemaakte sjablooninhoud omgezet naar veilige platte tekst voordat de notitie wordt opgeslagen. Dit is handig voor winkels die alleen eenvoudige notities willen gebruiken."
      },
      {
        "question": "Hoe beschermt het importvoorbeeld bestaande sjablonen?",
        "answer": "Het importvoorbeeld toont hoeveel sjablonen worden aangemaakt, bijgewerkt of overgeslagen voordat de definitieve import wordt uitgevoerd. Dit helpt ongewenst overschrijven te voorkomen."
      },
      {
        "question": "Kan ik een sjabloon dupliceren?",
        "answer": "Ja. Gebruik de actie Dupliceren in de sjablonenlijst. De kopie wordt als concept gemaakt en behoudt de inhoud, categorieën, favorietenstatus en het notitietype; de gebruiksteller begint opnieuw bij nul."
      },
      {
        "question": "Kan ik JSON-imports beperken?",
        "answer": "Ja. Op de instellingenpagina kun je JSON-imports uitschakelen. Ook wanneer import is ingeschakeld, zijn het juiste recht, nonce-controle en een geldig JSON-bestand vereist."
      },
      {
        "question": "Hoe worden rechten beheerd?",
        "answer": "Gebruik de rechtenpagina om pluginrechten voor rollen toe te kennen of te verwijderen. Beheerders behouden toegang zodat de plugin de site-eigenaar niet per ongeluk kan buitensluiten."
      },
      {
        "question": "Ondersteunt de plugin revisies?",
        "answer": "Ja. Sjablooninhoud wordt naar de WordPress-berichtinhoud gespiegeld zodat WordPress-revisies wijzigingen kunnen volgen wanneer revisies beschikbaar zijn."
      },
      {
        "question": "Wat moet ik doen voordat ik een sjabloon voor echte klantberichten gebruik?",
        "answer": "Maak het sjabloon, selecteer een testbestelling, controleer het voorbeeld, controleer alle plaatshouders en bevestig het geselecteerde notitietype voordat je de notitie toevoegt. Gebruik voor kritieke berichten eerst een interne notitie als test."
      },
      {
        "question": "Waarom is de e-mailstatus van een klantnotitie belangrijk?",
        "answer": "WooCommerce kan een e-mail verzenden wanneer een klantnotitie wordt toegevoegd. Controleer de WooCommerce-e-mailconfiguratie en bekijk de verwerkingsinformatie op de pagina’s Geschiedenis en Diagnostiek."
      },
      {
        "question": "Kan ik de plugin in een testwinkel gebruiken?",
        "answer": "Ja. JSON-export en -import zijn handig om sjablonen tussen test- en productieomgevingen te verplaatsen. Controleer na het verplaatsen de instellingen voor klantnotities, omdat WooCommerce-e-mailinstellingen kunnen verschillen."
      },
      {
        "question": "Welke gegevens worden bij de-installatie verwijderd?",
        "answer": "De de-installatieroutine verwijdert de eigen sjablonen en instellingen van de plugin. WooCommerce-bestelnotities die al aan bestellingen zijn toegevoegd, behoren tot de WooCommerce-bestelgeschiedenis en worden niet verwijderd wanneer de plugin wordt gede-installeerd."
      },
      {
        "question": "Hoe meld ik een probleem?",
        "answer": "Noteer de WordPress-versie, WooCommerce-versie, of HPOS is ingeschakeld, de actieve taal, het geselecteerde sjabloon en de exacte stappen die het probleem veroorzaakten."
      },
      {
        "question": "Sjabloonvoorwaarden",
        "answer": "Sjabloonvoorwaarden bepalen of een sjabloon voor een bepaalde bestelling beschikbaar is. Je kunt sjablonen beperken op bestelstatus, betaalmethode, verzendmethode, factureringsland en minimaal of maximaal bestelbedrag. Aan alle ingestelde voorwaarden moet worden voldaan. Laat een veld leeg wanneer die voorwaarde het sjabloon niet moet beperken."
      },
      {
        "question": "Logboek voor e-mailverwerking",
        "answer": "Bij klantnotities registreert de plugin wanneer WooCommerce meldt dat de e-mail voor de klantnotitie is verwerkt en registreert deze ook technische fouten van wp_mail. Een verwerkte gebeurtenis bevestigt dat WordPress/WooCommerce het bericht aan het mailsysteem heeft doorgegeven; dit bewijst niet dat het bericht uiteindelijk is afgeleverd of door de klant is gelezen. Controleer de pagina Geschiedenis voor verwerkte en mislukte e-mailgebeurtenissen."
      },
      {
        "question": "Centrale geschiedenis",
        "answer": "Open <strong>Mailhilfe Bestelnotities → Geschiedenis</strong> om recente notitiecreaties, sjabloongebruik, e-mailverwerking en e-mailfouten te bekijken. Vermeldingen bevatten waar beschikbaar de bestelling, het sjabloon, de gebruiker, ontvanger, het gebeurtenistype en het tijdstip. Gebruik de geschiedenis voor ondersteuning, controle en probleemoplossing."
      },
      {
        "question": "Voorbeeld met testbestelling",
        "answer": "Voer in de sjablooneditor een WooCommerce-bestelling-ID in het gebied voor het testvoorbeeld in. De huidige editorinhoud, inclusief niet-opgeslagen wijzigingen, wordt met gegevens uit die bestelling weergegeven zonder een notitie te maken of een e-mail te verzenden. Gebruik een bestelling uit een testomgeving of een niet-kritieke testbestelling."
      },
      {
        "question": "Persoonlijke favorieten en recent gebruikte sjablonen",
        "answer": "Elke beheerder kan persoonlijke favorieten in het bestelscherm markeren. De plugin bewaart daarnaast per gebruiker de tien laatst gebruikte sjablonen en plaatst deze hoger in de selectie. Algemene favorieten blijven met alle gebruikers gedeeld. Persoonlijke favorieten veranderen de lijst van een andere gebruiker niet."
      },
      {
        "question": "Diagnostiekpagina",
        "answer": "Open <strong>Mailhilfe Bestelnotities → Diagnostiek</strong> om technische informatie te bekijken, zoals WordPress-, PHP- en WooCommerce-versies, HPOS-status, e-mailstatus voor klantnotities, locale, aantal gepubliceerde sjablonen, cachestatus en WP_DEBUG. Kopieer de diagnostische waarden wanneer je ondersteuning aanvraagt."
      },
      {
        "question": "Hooks en filters voor ontwikkelaars",
        "answer": "De plugin biedt hooks en filters voor plaatshouders, plaatshouderwaarden, toegestane metasleutels, sjabloonresultaten, voorwaarden, voorbeeldinhoud, definitieve notitie-inhoud, acties vóór en na het toevoegen van een notitie, geschiedenisrecords en diagnostiek. Hooknamen en parameters zijn gedocumenteerd in readme.txt. Valideer, schoon op en escape alle aangepaste gegevens."
      }
    ]
  },
  "pl_PL": {
    "menu": "FAQ",
    "title": "Najczęściej zadawane pytania",
    "intro": "Odpowiedzi na częste pytania dotyczące Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Nie masz uprawnień do zarządzania szablonami notatek.",
    "items": [
      {
        "question": "Do czego służy Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "Wtyczka tworzy szablony notatek zamówień WooCommerce wielokrotnego użytku. Personel może wybrać szablon w zamówieniu, sprawdzić podgląd z podstawionymi symbolami zastępczymi i dodać wynik jako notatkę wewnętrzną lub notatkę dla klienta."
      },
      {
        "question": "Gdzie można tworzyć i zarządzać szablonami?",
        "answer": "Otwórz pozycję Mailhilfe Order Notes w menu administracyjnym WordPressa. Można tam tworzyć, edytować, usuwać, kategoryzować, oznaczać jako ulubione oraz sortować szablony metodą przeciągnij i upuść."
      },
      {
        "question": "Jak działają symbole zastępcze?",
        "answer": "Symbole zastępcze, takie jak {order_number}, {customer}, {billing_email}, {order_total} i {items}, są zastępowane rzeczywistymi danymi zamówienia w podglądzie oraz podczas dodawania notatki."
      },
      {
        "question": "Czy można formatować treść szablonów?",
        "answer": "Tak. Edytor WordPressa obsługuje akapity, pogrubienie, kursywę, listy i odnośniki. Wtyczka zachowuje bezpieczny kod HTML, a przed zapisaniem lub importem usuwa niebezpieczne znaczniki."
      },
      {
        "question": "Jaka jest różnica między notatką wewnętrzną a notatką dla klienta?",
        "answer": "Notatki wewnętrzne są przeznaczone wyłącznie dla zespołu sklepu. Notatki dla klientów mogą być widoczne dla klienta i, zależnie od ustawień WooCommerce, mogą uruchamiać powiadomienia e-mail."
      },
      {
        "question": "Jak bezpiecznie używać notatek dla klientów?",
        "answer": "Notatki dla klientów mogą opuścić wewnętrzny obszar administracyjny. Przed dodaniem treści, która może dotrzeć do klienta, personel powinien sprawdzić symbole zastępcze, dane osobowe, sformułowania i wybrany typ notatki."
      },
      {
        "question": "W jaki sposób pomagają ulubione, wyszukiwanie i sortowanie?",
        "answer": "Ulubione ułatwiają dostęp do ważnych szablonów. Wyszukiwanie filtruje długie listy szablonów na ekranie zamówienia. Sortowanie metodą przeciągnij i upuść określa kolejność wyświetlania szablonów."
      },
      {
        "question": "Jak działa import i eksport JSON?",
        "answer": "Eksport tworzy plik JSON zawierający tytuły, treść, typy notatek, kategorie, ulubione, kolejność i dane użycia szablonów. Import pozwala odtworzyć szablony lub przenieść je do innego sklepu."
      },
      {
        "question": "Jak zainstalować szablony demonstracyjne?",
        "answer": "Otwórz stronę importu i eksportu, a następnie wybierz instalację szablonów demonstracyjnych. Szablony są tworzone w pasującym wbudowanym języku, w tym po persku; w przeciwnym razie używany jest sprawdzony zestaw angielski."
      },
      {
        "question": "Które role mogą używać wtyczki?",
        "answer": "Administratorzy i kierownicy sklepu automatycznie otrzymują uprawnienia wtyczki. Wtyczka rozdziela uprawnienie do zarządzania szablonami od uprawnienia do używania szablonów w zamówieniach."
      },
      {
        "question": "Czy wtyczka jest zgodna z HPOS?",
        "answer": "Tak. Wtyczka deklaruje zgodność z WooCommerce HPOS i używa interfejsów API zamówień WooCommerce zamiast bezpośredniego dostępu do tabel zamówień w bazie danych."
      },
      {
        "question": "Czy wtyczka dodaje publiczne odnośniki w interfejsie sklepu?",
        "answer": "Nie. Wtyczka działa w obszarze administracyjnym WordPressa i na ekranach zamówień WooCommerce. Nie dodaje do sklepu odnośników „powered by” ani publicznych odnośników promocyjnych."
      },
      {
        "question": "Co sprawdzić, jeśli wyświetlany jest niewłaściwy język?",
        "answer": "Sprawdź Ustawienia > Ogólne > Język witryny oraz język w profilu użytkownika. Wtyczka zawiera sprawdzone tłumaczenia dla wszystkich obsługiwanych języków, w tym perskiego. Dla innych języków należy używać sprawdzonych pakietów językowych WordPress.org."
      },
      {
        "question": "Czy można edytować podgląd przed dodaniem notatki?",
        "answer": "Tak. Po wybraniu szablonu podgląd zawiera podstawione dane zamówienia i można go edytować przed zapisaniem notatki. Edytowany podgląd jest ostateczną treścią dodawaną do zamówienia."
      },
      {
        "question": "Jak rejestrowane jest utworzenie notatki dla klienta i przetwarzanie wiadomości e-mail?",
        "answer": "Notatki dla klientów mogą uruchamiać powiadomienia e-mail WooCommerce, jeśli odpowiednia wiadomość jest włączona. Wtyczka zapisuje utworzenie notatki dla klienta oddzielnie od przetwarzania wiadomości e-mail. Przed dodaniem notatki sprawdź edytowalny podgląd, a wynik obsługi poczty zweryfikuj na stronie Historia."
      },
      {
        "question": "Jak działają języki szablonów?",
        "answer": "Każdy szablon można przypisać do wszystkich języków albo do konkretnego dołączonego języka. W sklepach wielojęzycznych wtyczka próbuje preferować szablony zgodne z językiem zamówienia, użytkownika lub witryny."
      },
      {
        "question": "Czy można używać niestandardowych pól meta zamówienia lub klienta?",
        "answer": "Tak. Zaawansowane symbole zastępcze, takie jak {order_meta:meta_key} i {customer_meta:meta_key}, mogą odczytywać pola niestandardowe. Blokowane są wrażliwe klucze zawierające między innymi słowa password, token, secret, session, auth lub hash."
      },
      {
        "question": "Czy personel może przypadkowo ujawnić prywatne informacje?",
        "answer": "Wtyczka nie zna biznesowego znaczenia każdego symbolu zastępczego. Zawsze sprawdzaj notatki dla klientów przed zapisaniem, szczególnie podczas używania symboli meta lub danych właściwych dla konkretnego klienta."
      },
      {
        "question": "Co się dzieje po wyłączeniu formatowania HTML?",
        "answer": "Gdy formatowanie HTML jest wyłączone, sformatowana treść szablonu jest przed zapisaniem notatki zamieniana na bezpieczny zwykły tekst. Jest to przydatne w sklepach, które chcą używać wyłącznie bardzo prostych notatek."
      },
      {
        "question": "W jaki sposób podgląd importu chroni istniejące szablony?",
        "answer": "Podgląd importu pokazuje przed ostatecznym wykonaniem, ile szablonów zostanie utworzonych, zaktualizowanych lub pominiętych. Pomaga to uniknąć niepożądanego nadpisania."
      },
      {
        "question": "Czy można zduplikować szablon?",
        "answer": "Tak. Użyj działania Duplikuj na liście szablonów. Kopia jest tworzona jako szkic i zachowuje treść, kategorie, stan ulubionego oraz typ notatki, natomiast licznik użycia zaczyna od zera."
      },
      {
        "question": "Czy można ograniczyć import plików JSON?",
        "answer": "Tak. Na stronie ustawień można wyłączyć import JSON. Nawet po jego włączeniu import wymaga odpowiedniego uprawnienia, sprawdzenia tokenu jednorazowego i prawidłowego pliku JSON."
      },
      {
        "question": "Jak zarządza się uprawnieniami?",
        "answer": "Na stronie uprawnień można przyznawać lub odbierać rolom uprawnienia wtyczki. Administratorzy zachowują dostęp, dzięki czemu wtyczka nie może przypadkowo zablokować właściciela witryny."
      },
      {
        "question": "Czy wtyczka obsługuje wersje?",
        "answer": "Tak. Treść szablonu jest kopiowana do treści wpisu WordPressa, dzięki czemu wersje WordPressa mogą śledzić zmiany, jeśli obsługa wersji jest dostępna."
      },
      {
        "question": "Co zrobić przed użyciem szablonu w rzeczywistej wiadomości do klienta?",
        "answer": "Utwórz szablon, wybierz zamówienie testowe, sprawdź podgląd, zweryfikuj wszystkie symbole zastępcze i potwierdź wybrany typ notatki przed jej dodaniem. W przypadku ważnych wiadomości najpierw wykonaj test jako notatkę wewnętrzną."
      },
      {
        "question": "Dlaczego status wiadomości e-mail z notatką dla klienta jest ważny?",
        "answer": "WooCommerce może wysłać wiadomość e-mail po dodaniu notatki dla klienta. Sprawdź konfigurację wiadomości e-mail WooCommerce oraz informacje o przetwarzaniu na stronach Historia i Diagnostyka."
      },
      {
        "question": "Czy można używać wtyczki w sklepie testowym?",
        "answer": "Tak. Eksport i import JSON są przydatne podczas przenoszenia szablonów między środowiskiem testowym a produkcyjnym. Po przeniesieniu szablonów sprawdź ustawienia notatek klientów, ponieważ ustawienia poczty WooCommerce mogą się różnić."
      },
      {
        "question": "Jakie dane są usuwane podczas odinstalowywania?",
        "answer": "Procedura odinstalowywania usuwa własne szablony i opcje wtyczki. Notatki WooCommerce dodane wcześniej do zamówień należą do historii zamówienia i nie są usuwane podczas odinstalowywania wtyczki."
      },
      {
        "question": "Jak zgłosić problem?",
        "answer": "Zanotuj wersję WordPressa, wersję WooCommerce, stan HPOS, aktywny język, wybrany szablon oraz dokładne kroki, które doprowadziły do problemu."
      },
      {
        "question": "Warunki szablonów",
        "answer": "Warunki szablonu określają, czy szablon jest dostępny dla danego zamówienia. Szablony można ograniczać według statusu zamówienia, metody płatności, metody wysyłki, kraju rozliczeniowego oraz minimalnej lub maksymalnej wartości zamówienia. Wszystkie skonfigurowane warunki muszą być spełnione. Pozostaw pole puste, jeśli dany warunek nie powinien ograniczać szablonu."
      },
      {
        "question": "Dziennik przetwarzania wiadomości e-mail",
        "answer": "Dla notatek klientów wtyczka zapisuje moment, w którym WooCommerce zgłasza przetworzenie wiadomości e-mail z notatką dla klienta, a także techniczne błędy wp_mail. Zdarzenie przetworzenia potwierdza, że WordPress lub WooCommerce przekazał wiadomość do systemu pocztowego. Nie dowodzi końcowego doręczenia ani przeczytania wiadomości przez klienta. Na stronie Historia sprawdzaj zdarzenia pomyślnego przetworzenia i błędy wiadomości e-mail."
      },
      {
        "question": "Centralna historia",
        "answer": "Otwórz <strong>Mailhilfe Order Notes → Historia</strong>, aby przejrzeć ostatnio utworzone notatki, użycie szablonów, przetwarzanie wiadomości e-mail i błędy wysyłki. Wpisy zawierają, jeśli dane są dostępne, zamówienie, szablon, użytkownika, odbiorcę, typ zdarzenia i czas. Używaj historii do obsługi zgłoszeń, audytu i rozwiązywania problemów."
      },
      {
        "question": "Podgląd z zamówieniem testowym",
        "answer": "W edytorze szablonu wprowadź identyfikator zamówienia WooCommerce w obszarze podglądu testowego. Bieżąca treść edytora, łącznie z niezapisanymi zmianami, zostanie wyrenderowana z danymi tego zamówienia bez tworzenia notatki i bez wysyłania wiadomości e-mail. Użyj zamówienia w witrynie testowej albo nieistotnego zamówienia testowego."
      },
      {
        "question": "Osobiste ulubione i ostatnio używane szablony",
        "answer": "Każdy administrator może oznaczać osobiste ulubione na ekranie zamówienia. Wtyczka zapisuje także dziesięć ostatnio używanych szablonów każdego użytkownika i umieszcza je wyżej na liście wyboru. Globalne ulubione pozostają wspólne dla wszystkich użytkowników. Osobiste ulubione nie zmieniają list innych użytkowników."
      },
      {
        "question": "Strona diagnostyki",
        "answer": "Otwórz <strong>Mailhilfe Order Notes → Diagnostyka</strong>, aby wyświetlić informacje techniczne, takie jak wersje WordPressa, PHP i WooCommerce, status HPOS, status wiadomości e-mail z notatkami dla klientów, ustawienia regionalne, liczba opublikowanych szablonów, status pamięci podręcznej i WP_DEBUG. Podczas zgłaszania problemu skopiuj wartości diagnostyczne."
      },
      {
        "question": "Hooki i filtry dla programistów",
        "answer": "Wtyczka udostępnia hooki i filtry dla symboli zastępczych, ich wartości, dozwolonych kluczy meta, wyników szablonów, warunków, treści podglądu, końcowej treści notatki, działań przed dodaniem notatki i po jej dodaniu, wpisów historii oraz diagnostyki. Nazwy hooków i parametry są opisane w pliku readme.txt. Sprawdzaj, oczyszczaj i koduj wszystkie dane niestandardowe."
      }
    ]
  },
  "tr_TR": {
    "menu": "SSS",
    "title": "Sık Sorulan Sorular",
    "intro": "Mailhilfe Order Note Manager for WooCommerce hakkında sık sorulan soruların yanıtları.",
    "permission": "Not şablonlarını yönetme yetkiniz yok.",
    "items": [
      {
        "question": "Mailhilfe Order Note Manager for WooCommerce ne için kullanılır?",
        "answer": "Eklenti, yeniden kullanılabilir WooCommerce sipariş notu şablonları oluşturur. Personel sipariş içinde bir şablon seçebilir, değiştirilmiş yer tutucuların önizlemesini görebilir ve sonucu dahili not veya müşteri notu olarak ekleyebilir."
      },
      {
        "question": "Şablonları nerede oluşturup yönetebilirim?",
        "answer": "WordPress yönetim menüsünde Mailhilfe Sipariş Notları bölümünü açın. Burada şablon oluşturabilir, düzenleyebilir, silebilir, kategorilere ayırabilir, favori olarak işaretleyebilir ve sürükleyip bırakarak sıralayabilirsiniz."
      },
      {
        "question": "Yer tutucular nasıl çalışır?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} ve {items} gibi yer tutucular, önizlemede ve not eklendiğinde gerçek sipariş verileriyle değiştirilir."
      },
      {
        "question": "Şablon metinlerini biçimlendirebilir miyim?",
        "answer": "Evet. WordPress düzenleyicisi paragrafları, kalın ve italik metni, listeleri ve bağlantıları destekler. Eklenti güvenli HTML’yi korur ve kaydetmeden veya içe aktarmadan önce güvenli olmayan işaretlemeyi kaldırır."
      },
      {
        "question": "Dahili notlar ile müşteri notları arasındaki fark nedir?",
        "answer": "Dahili notlar yalnızca mağaza ekibi içindir. Müşteri notları müşteriye görünebilir ve WooCommerce ayarlarınıza bağlı olarak e-posta bildirimlerini tetikleyebilir."
      },
      {
        "question": "Müşteri notları güvenli biçimde nasıl kullanılmalıdır?",
        "answer": "Müşteri notları dahili yönetim alanının dışına çıkabilir. Personel, müşteriye ulaşabilecek bir içerik eklemeden önce yer tutucuları, kişisel verileri, ifadeleri ve seçilen not türünü kontrol etmelidir."
      },
      {
        "question": "Favoriler, arama ve sıralama nasıl yardımcı olur?",
        "answer": "Favoriler önemli şablonlara kolay erişim sağlar. Arama, sipariş ekranındaki uzun şablon listelerini filtreler. Sürükle ve bırak sıralaması, şablonların gösterilme sırasını belirler."
      },
      {
        "question": "JSON içe ve dışa aktarma nasıl çalışır?",
        "answer": "Dışa aktarma; şablon başlıkları, içerik, not türleri, kategoriler, favoriler, sıralama ve kullanım verilerini içeren bir JSON dosyası oluşturur. İçe aktarma şablonları geri yükleyebilir veya başka bir mağazaya taşıyabilir."
      },
      {
        "question": "Demo şablonları nasıl yüklenir?",
        "answer": "İçe/dışa aktarma sayfasını açın ve demo şablonu işlemini seçin. Demo şablonları, Farsça dâhil eşleşen paketlenmiş dilde oluşturulur; aksi durumda incelenmiş İngilizce küme kullanılır."
      },
      {
        "question": "Hangi roller eklentiyi kullanabilir?",
        "answer": "Yöneticiler ve mağaza yöneticileri eklenti yetkilerini otomatik olarak alır. Eklenti, şablonları yönetme yetkisini siparişlerde şablon kullanma yetkisinden ayırır."
      },
      {
        "question": "Eklenti HPOS ile uyumlu mu?",
        "answer": "Evet. Eklenti WooCommerce HPOS uyumluluğunu bildirir ve sipariş veritabanı tablolarına doğrudan erişmek yerine WooCommerce sipariş API’lerini kullanır."
      },
      {
        "question": "Eklenti herkese açık ön yüz bağlantıları ekler mi?",
        "answer": "Hayır. Eklenti WordPress yönetiminde ve WooCommerce sipariş ekranlarında çalışır. Mağaza ön yüzüne “powered by” bağlantıları veya herkese açık tanıtım bağlantıları eklemez."
      },
      {
        "question": "Dil yanlışsa neleri kontrol etmeliyim?",
        "answer": "Ayarlar > Genel > Site dili bölümünü ve kullanıcı profilinizdeki dili kontrol edin. Eklenti, Farsça dâhil desteklenen tüm diller için incelenmiş çeviriler içerir. Diğer dillerde incelenmiş WordPress.org dil paketleri kullanılmalıdır."
      },
      {
        "question": "Notu eklemeden önce önizlemeyi düzenleyebilir miyim?",
        "answer": "Evet. Bir şablon seçildikten sonra önizleme değiştirilmiş sipariş verilerini içerir ve not kaydedilmeden önce düzenlenebilir. Düzenlenen önizleme, siparişe eklenecek son nottur."
      },
      {
        "question": "Müşteri notu oluşturma ve e-posta işlemesi nasıl kaydedilir?",
        "answer": "İlgili e-posta etkinse müşteri notları WooCommerce e-posta bildirimlerini tetikleyebilir. Eklenti, müşteri notunun oluşturulmasını e-posta işlemesinden ayrı kaydeder. Notu eklemeden önce düzenlenebilir önizlemeyi inceleyin ve posta işleyicisinin sonucunu kontrol etmek için Geçmiş sayfasını kullanın."
      },
      {
        "question": "Şablon dilleri nasıl çalışır?",
        "answer": "Her şablon tüm dillere veya paketlenmiş belirli bir dile atanabilir. Eklenti çok dilli mağazalarda sipariş dili, kullanıcı dili veya site diliyle eşleşen şablonları tercih etmeye çalışır."
      },
      {
        "question": "Özel sipariş veya müşteri meta alanlarını kullanabilir miyim?",
        "answer": "Evet. {order_meta:meta_key} ve {customer_meta:meta_key} gibi gelişmiş yer tutucular özel alanları okuyabilir. password, token, secret, session, auth veya hash gibi sözcükler içeren hassas anahtarlar engellenir."
      },
      {
        "question": "Personel yanlışlıkla özel bilgileri açığa çıkarabilir mi?",
        "answer": "Eklenti her yer tutucunun iş anlamını bilemez. Özellikle meta yer tutucular veya müşteriye özel veriler kullanırken müşteri notlarını kaydetmeden önce her zaman inceleyin."
      },
      {
        "question": "HTML biçimlendirmesi devre dışı bırakıldığında ne olur?",
        "answer": "HTML biçimlendirme ayarı devre dışıysa biçimlendirilmiş şablon içeriği not saklanmadan önce güvenli düz metne dönüştürülür. Bu, yalnızca çok basit notlar isteyen mağazalar için kullanışlıdır."
      },
      {
        "question": "İçe aktarma önizlemesi mevcut şablonları nasıl korur?",
        "answer": "İçe aktarma önizlemesi, son içe aktarma çalıştırılmadan önce kaç şablonun oluşturulacağını, güncelleneceğini veya atlanacağını gösterir. Bu, istenmeyen üzerine yazmaları önlemeye yardımcı olur."
      },
      {
        "question": "Bir şablonu çoğaltabilir miyim?",
        "answer": "Evet. Şablon listesindeki Çoğalt işlemini kullanın. Kopya taslak olarak oluşturulur ve içeriği, kategorileri, favori durumunu ve not türünü korur; kullanım sayacı ise sıfırdan başlar."
      },
      {
        "question": "JSON içe aktarımlarını sınırlandırabilir miyim?",
        "answer": "Evet. Ayarlar sayfası JSON içe aktarımlarını devre dışı bırakabilir. Etkin olduğunda bile içe aktarma için doğru yetki, nonce doğrulaması ve geçerli bir JSON dosyası gerekir."
      },
      {
        "question": "Yetkiler nasıl yönetilir?",
        "answer": "Rollere eklenti yetkileri vermek veya kaldırmak için Yetkiler sayfasını kullanın. Eklentinin site sahibini yanlışlıkla dışarıda bırakmaması için yöneticiler erişimi korur."
      },
      {
        "question": "Eklenti revizyonları destekliyor mu?",
        "answer": "Evet. Şablon içeriği WordPress yazı içeriğine yansıtılır; böylece revizyonlar kullanılabiliyorsa WordPress revizyonları değişiklikleri izleyebilir."
      },
      {
        "question": "Gerçek müşteri iletileri için bir şablon kullanmadan önce ne yapmalıyım?",
        "answer": "Şablonu oluşturun, bir test siparişi seçin, önizlemeyi kontrol edin, tüm yer tutucuları doğrulayın ve notu eklemeden önce seçilen not türünü onaylayın. Kritik iletilerde önce dahili notu test olarak kullanın."
      },
      {
        "question": "Müşteri notu e-posta durumu neden önemlidir?",
        "answer": "Bir müşteri notu eklendiğinde WooCommerce e-posta gönderebilir. WooCommerce e-posta yapılandırmasını kontrol edin ve Geçmiş ile Tanılama sayfalarındaki işleme bilgilerini inceleyin."
      },
      {
        "question": "Eklentiyi bir hazırlık mağazasında kullanabilir miyim?",
        "answer": "Evet. JSON dışa ve içe aktarma, şablonları hazırlık ile üretim ortamları arasında taşımak için kullanışlıdır. WooCommerce e-posta ayarları farklı olabileceğinden şablonları taşıdıktan sonra müşteri notu ayarlarını kontrol edin."
      },
      {
        "question": "Kaldırma sırasında hangi veriler silinir?",
        "answer": "Kaldırma işlemi eklentinin kendi şablonlarını ve eklenti seçeneklerini siler. Siparişlere daha önce eklenen WooCommerce sipariş notları WooCommerce sipariş geçmişine aittir ve eklenti kaldırıldığında silinmez."
      },
      {
        "question": "Bir sorunu nasıl bildirmeliyim?",
        "answer": "WordPress sürümünü, WooCommerce sürümünü, HPOS’un etkin olup olmadığını, etkin dili, seçilen şablonu ve soruna neden olan tam adımları belgeleyin."
      },
      {
        "question": "Şablon koşulları",
        "answer": "Şablon koşulları, bir şablonun belirli bir sipariş için kullanılabilir olup olmadığını belirler. Şablonları sipariş durumu, ödeme yöntemi, gönderim yöntemi, fatura ülkesi ve en düşük veya en yüksek sipariş toplamına göre sınırlandırabilirsiniz. Yapılandırılan tüm koşullar eşleşmelidir. Bir koşul şablonu sınırlamamalıysa alanı boş bırakın."
      },
      {
        "question": "E-posta işleme günlüğü",
        "answer": "Müşteri notları için eklenti, WooCommerce müşteri notu e-postasını işlenmiş olarak bildirdiğinde bunu kaydeder ve teknik wp_mail hatalarını da kaydeder. İşlenmiş olay, WordPress/WooCommerce’in iletiyi posta sistemine verdiğini doğrular; nihai teslimatı veya müşterinin iletiyi okuduğunu kanıtlamaz. İşlenen ve başarısız e-posta olayları için Geçmiş sayfasını kontrol edin."
      },
      {
        "question": "Merkezi geçmiş",
        "answer": "Son not oluşturma işlemlerini, şablon kullanımını, e-posta işlemesini ve e-posta hatalarını incelemek için <strong>Mailhilfe Sipariş Notları → Geçmiş</strong> bölümünü açın. Kayıtlar, mevcut olduğunda sipariş, şablon, kullanıcı, alıcı, olay türü ve zamanı içerir. Geçmişi destek, denetim ve sorun giderme için kullanın."
      },
      {
        "question": "Test siparişi önizlemesi",
        "answer": "Şablon düzenleyicisinde test önizleme alanına bir WooCommerce sipariş kimliği girin. Kaydedilmemiş değişiklikler dâhil geçerli düzenleyici içeriği, not oluşturmadan veya e-posta göndermeden bu siparişin verileriyle işlenir. Bir hazırlık siparişi veya kritik olmayan bir test siparişi kullanın."
      },
      {
        "question": "Kişisel favoriler ve son kullanılan şablonlar",
        "answer": "Her yönetici sipariş ekranında kişisel favorileri işaretleyebilir. Eklenti ayrıca her kullanıcı için son kullanılan on şablonu saklar ve seçimde bunlara daha üst bir konum verir. Genel favoriler tüm kullanıcılarla paylaşılmaya devam eder. Kişisel favoriler başka bir kullanıcının listesini değiştirmez."
      },
      {
        "question": "Tanılama sayfası",
        "answer": "WordPress, PHP ve WooCommerce sürümleri, HPOS durumu, müşteri notu e-posta durumu, yerel ayar, yayımlanmış şablon sayısı, önbellek durumu ve WP_DEBUG gibi teknik bilgileri görüntülemek için <strong>Mailhilfe Sipariş Notları → Tanılama</strong> bölümünü açın. Destek isterken tanılama değerlerini kopyalayın."
      },
      {
        "question": "Geliştirici kancaları ve filtreleri",
        "answer": "Eklenti; yer tutucular, yer tutucu değerleri, izin verilen meta anahtarları, şablon sonuçları, koşullar, önizleme içeriği, son not içeriği, not eklemeden önceki ve sonraki işlemler, geçmiş kayıtları ve tanılama için kancalar ve filtreler sağlar. Kanca adları ve parametreleri readme.txt dosyasında belgelenmiştir. Tüm özel verileri doğrulayın, temizleyin ve kaçış uygulayın."
      }
    ]
  },
  "fa_IR": {
    "menu": "پرسش‌های متداول",
    "title": "پرسش‌های متداول",
    "intro": "پاسخ پرسش‌های رایج درباره Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "شما اجازه مدیریت الگوهای یادداشت را ندارید.",
    "items": [
      {
        "question": "Mailhilfe Order Note Manager for WooCommerce چه کاربردی دارد؟",
        "answer": "این افزونه الگوهای قابل استفاده مجدد برای یادداشت سفارش ووکامرس ایجاد می‌کند. کارکنان می‌توانند درون سفارش یک الگو انتخاب کنند، جای‌نگهدارهای جایگزین‌شده را در پیش‌نمایش ببینند و نتیجه را به‌صورت یادداشت داخلی یا یادداشت مشتری اضافه کنند."
      },
      {
        "question": "الگوها را کجا ایجاد و مدیریت کنم؟",
        "answer": "از منوی مدیریت وردپرس، «یادداشت‌های سفارش Mailhilfe» را باز کنید. در آنجا می‌توانید الگوها را ایجاد، ویرایش، حذف، دسته‌بندی، برگزیده و با کشیدن و رها کردن مرتب کنید."
      },
      {
        "question": "جای‌نگهدارها چگونه کار می‌کنند؟",
        "answer": "جای‌نگهدارهایی مانند {order_number}، {customer}، {billing_email}، {order_total} و {items} در پیش‌نمایش و هنگام افزودن یادداشت با داده واقعی سفارش جایگزین می‌شوند."
      },
      {
        "question": "آیا می‌توان متن الگوها را قالب‌بندی کرد؟",
        "answer": "بله. ویرایشگر وردپرس از بندها، نوشته پررنگ و کج، فهرست‌ها و پیوندها پشتیبانی می‌کند. افزونه HTML امن را نگه می‌دارد و نشانه‌گذاری ناامن را پیش از ذخیره یا درون‌ریزی حذف می‌کند."
      },
      {
        "question": "تفاوت یادداشت داخلی و یادداشت مشتری چیست؟",
        "answer": "یادداشت داخلی فقط برای تیم فروشگاه است. یادداشت مشتری ممکن است برای مشتری قابل مشاهده باشد و بسته به تنظیمات ووکامرس، اعلان ایمیلی ایجاد کند."
      },
      {
        "question": "چگونه یادداشت‌های مشتری را ایمن استفاده کنم؟",
        "answer": "یادداشت مشتری می‌تواند از محیط داخلی مدیریت خارج شود. پیش از افزودن هر متنی که ممکن است به مشتری برسد، کارکنان باید جای‌نگهدارها، داده شخصی، نگارش و نوع یادداشت انتخاب‌شده را بررسی کنند."
      },
      {
        "question": "برگزیده‌ها، جست‌وجو و مرتب‌سازی چه کمکی می‌کنند؟",
        "answer": "برگزیده‌ها دسترسی به الگوهای مهم را آسان می‌کنند. جست‌وجو فهرست‌های بلند الگو را در صفحه سفارش پالایش می‌کند. مرتب‌سازی با کشیدن و رها کردن، ترتیب نمایش الگوها را تعیین می‌کند."
      },
      {
        "question": "درون‌ریزی و برون‌بری JSON چگونه کار می‌کند؟",
        "answer": "برون‌بری یک فایل JSON شامل عنوان، محتوا، نوع یادداشت، دسته‌ها، برگزیده‌ها، ترتیب و داده استفاده الگوها ایجاد می‌کند. درون‌ریزی می‌تواند الگوها را بازیابی یا به فروشگاه دیگری منتقل کند."
      },
      {
        "question": "الگوهای نمایشی چگونه نصب می‌شوند؟",
        "answer": "صفحه درون‌ریزی/برون‌بری را باز و گزینه الگوهای نمایشی را انتخاب کنید. الگوهای نمایشی به زبان داخلی منطبق، از جمله فارسی، ایجاد می‌شوند؛ در غیر این صورت مجموعه انگلیسی بازبینی‌شده استفاده می‌شود."
      },
      {
        "question": "کدام نقش‌ها می‌توانند از افزونه استفاده کنند؟",
        "answer": "مدیران و مدیران فروشگاه دسترسی‌های افزونه را به‌طور خودکار دریافت می‌کنند. افزونه دسترسی مدیریت الگوها را از دسترسی استفاده از الگوها در سفارش‌ها جدا می‌کند."
      },
      {
        "question": "آیا افزونه با HPOS سازگار است؟",
        "answer": "بله. افزونه سازگاری با HPOS ووکامرس را اعلام می‌کند و به‌جای دسترسی مستقیم به جدول‌های پایگاه داده سفارش، از APIهای سفارش ووکامرس استفاده می‌کند."
      },
      {
        "question": "آیا افزونه پیوند عمومی در بخش کاربری فروشگاه اضافه می‌کند؟",
        "answer": "خیر. افزونه در مدیریت وردپرس و صفحه سفارش ووکامرس کار می‌کند و هیچ پیوند تبلیغاتی عمومی یا «ساخته‌شده با» به فروشگاه اضافه نمی‌کند."
      },
      {
        "question": "اگر زبان اشتباه است چه چیزی را بررسی کنم؟",
        "answer": "در تنظیمات عمومی وردپرس، زبان سایت و زبان نمایه کاربری خود را بررسی کنید. افزونه برای همه زبان‌های پشتیبانی‌شده، از جمله فارسی، ترجمه‌های داخلی بازبینی‌شده دارد. زبان‌های دیگر باید از بسته‌های زبانی بازبینی‌شده WordPress.org استفاده کنند."
      },
      {
        "question": "آیا می‌توان پیش‌نمایش را پیش از افزودن یادداشت ویرایش کرد؟",
        "answer": "بله. پس از انتخاب الگو، پیش‌نمایش شامل داده جایگزین‌شده سفارش است و پیش از ذخیره یادداشت قابل ویرایش خواهد بود. پیش‌نمایش ویرایش‌شده همان یادداشت نهایی است که به سفارش افزوده می‌شود."
      },
      {
        "question": "ایجاد یادداشت مشتری و پردازش ایمیل چگونه ثبت می‌شود؟",
        "answer": "اگر ایمیل مربوط فعال باشد، یادداشت مشتری می‌تواند اعلان ایمیلی ووکامرس را فعال کند. افزونه ایجاد یادداشت مشتری را جدا از پردازش ایمیل ثبت می‌کند. پیش‌نمایش قابل ویرایش را بررسی و نتیجه پردازش ایمیل را در صفحه تاریخچه مشاهده کنید."
      },
      {
        "question": "زبان الگوها چگونه کار می‌کند؟",
        "answer": "هر الگو می‌تواند برای همه زبان‌ها یا یک زبان داخلی مشخص تنظیم شود. در فروشگاه چندزبانه، افزونه تلاش می‌کند الگوهای هم‌زبان با سفارش، کاربر یا سایت را ترجیح دهد."
      },
      {
        "question": "آیا می‌توان از فیلدهای متای سفارشی سفارش یا مشتری استفاده کرد؟",
        "answer": "بله. جای‌نگهدارهای پیشرفته مانند {order_meta:meta_key} و {customer_meta:meta_key} می‌توانند فیلدهای سفارشی را بخوانند. کلیدهای حساس شامل واژه‌هایی مانند password، token، secret، session، auth یا hash مسدود هستند."
      },
      {
        "question": "آیا کارکنان ممکن است ناخواسته اطلاعات خصوصی را افشا کنند؟",
        "answer": "افزونه نمی‌تواند معنای تجاری همه جای‌نگهدارها را تشخیص دهد. به‌ویژه هنگام استفاده از جای‌نگهدارهای متا یا داده ویژه مشتری، همیشه یادداشت مشتری را پیش از ذخیره بررسی کنید."
      },
      {
        "question": "اگر قالب‌بندی HTML غیرفعال باشد چه می‌شود؟",
        "answer": "اگر تنظیم قالب‌بندی HTML غیرفعال باشد، محتوای قالب‌بندی‌شده الگو پیش از ذخیره یادداشت به متن ساده و امن تبدیل می‌شود. این گزینه برای فروشگاه‌هایی مناسب است که فقط یادداشت‌های بسیار ساده می‌خواهند."
      },
      {
        "question": "پیش‌نمایش درون‌ریزی چگونه از الگوهای موجود محافظت می‌کند؟",
        "answer": "پیش‌نمایش درون‌ریزی پیش از اجرای نهایی نشان می‌دهد چند الگو ایجاد، به‌روزرسانی یا نادیده گرفته خواهند شد. این کار از بازنویسی ناخواسته جلوگیری می‌کند."
      },
      {
        "question": "آیا می‌توان یک الگو را تکثیر کرد؟",
        "answer": "بله. در فهرست الگوها از گزینه تکثیر استفاده کنید. رونوشت به‌صورت پیش‌نویس ایجاد می‌شود و محتوا، دسته‌ها، وضعیت برگزیده و نوع یادداشت را حفظ می‌کند؛ اما شمارنده استفاده از صفر آغاز می‌شود."
      },
      {
        "question": "آیا می‌توان درون‌ریزی JSON را محدود کرد؟",
        "answer": "بله. صفحه تنظیمات می‌تواند درون‌ریزی JSON را غیرفعال کند. حتی در حالت فعال، درون‌ریزی به دسترسی مناسب، تأیید نانس و فایل JSON معتبر نیاز دارد."
      },
      {
        "question": "دسترسی‌ها چگونه مدیریت می‌شوند؟",
        "answer": "از صفحه دسترسی‌ها برای افزودن یا حذف دسترسی‌های افزونه از نقش‌ها استفاده کنید. مدیران دسترسی خود را حفظ می‌کنند تا افزونه نتواند ناخواسته مالک سایت را مسدود کند."
      },
      {
        "question": "آیا افزونه از بازنگری‌ها پشتیبانی می‌کند؟",
        "answer": "بله. محتوای الگو در محتوای نوشته وردپرس نیز ذخیره می‌شود تا در صورت در دسترس بودن بازنگری‌ها، تغییرات قابل پیگیری باشند."
      },
      {
        "question": "پیش از استفاده از الگو برای پیام واقعی مشتری چه کنم؟",
        "answer": "الگو را ایجاد کنید، یک سفارش آزمایشی انتخاب کنید، پیش‌نمایش و همه جای‌نگهدارها را بررسی کنید و پیش از افزودن یادداشت، نوع یادداشت را تأیید کنید. برای پیام‌های حساس ابتدا یک یادداشت داخلی آزمایشی بسازید."
      },
      {
        "question": "چرا وضعیت ایمیل یادداشت مشتری مهم است؟",
        "answer": "ووکامرس می‌تواند هنگام افزودن یادداشت مشتری ایمیل ارسال کند. تنظیمات ایمیل ووکامرس و اطلاعات پردازش در صفحه‌های تاریخچه و عیب‌یابی را بررسی کنید."
      },
      {
        "question": "آیا می‌توان افزونه را در فروشگاه آزمایشی استفاده کرد؟",
        "answer": "بله. درون‌ریزی و برون‌بری JSON برای انتقال الگوها میان محیط آزمایشی و اصلی مفید است. پس از انتقال، تنظیمات یادداشت مشتری را بررسی کنید، زیرا تنظیمات ایمیل ووکامرس ممکن است متفاوت باشد."
      },
      {
        "question": "هنگام حذف افزونه چه داده‌ای پاک می‌شود؟",
        "answer": "روال حذف، الگوها و گزینه‌های متعلق به افزونه را پاک می‌کند. یادداشت‌های ووکامرس که قبلاً به سفارش‌ها افزوده شده‌اند بخشی از تاریخچه سفارش هستند و با حذف افزونه پاک نمی‌شوند."
      },
      {
        "question": "چگونه یک مشکل را گزارش کنم؟",
        "answer": "نسخه وردپرس و ووکامرس، فعال بودن HPOS، زبان فعال، الگوی انتخاب‌شده و مراحل دقیق ایجاد مشکل را ثبت کنید."
      },
      {
        "question": "شرایط الگو",
        "answer": "شرایط الگو تعیین می‌کنند الگو برای یک سفارش خاص در دسترس باشد یا نه. می‌توانید الگوها را بر اساس وضعیت سفارش، روش پرداخت، روش حمل‌ونقل، کشور صورتحساب و حداقل یا حداکثر مبلغ سفارش محدود کنید. همه شرایط تنظیم‌شده باید برقرار باشند. اگر شرطی نباید الگو را محدود کند، فیلد آن را خالی بگذارید."
      },
      {
        "question": "گزارش پردازش ایمیل",
        "answer": "برای یادداشت مشتری، افزونه زمانی را ثبت می‌کند که ووکامرس ایمیل یادداشت مشتری را پردازش‌شده اعلام کند و خطاهای فنی wp_mail را نیز ثبت می‌کند. رویداد پردازش‌شده فقط تحویل پیام به سامانه ایمیل توسط وردپرس/ووکامرس را تأیید می‌کند و تحویل نهایی یا خواندن پیام را ثابت نمی‌کند. رویدادهای موفق و ناموفق را در تاریخچه بررسی کنید."
      },
      {
        "question": "تاریخچه مرکزی",
        "answer": "<strong>یادداشت‌های سفارش Mailhilfe ← تاریخچه</strong> را باز کنید تا ایجاد یادداشت، استفاده از الگو، پردازش ایمیل و خطاهای ایمیل را ببینید. ورودی‌ها در صورت وجود شامل سفارش، الگو، کاربر، گیرنده، نوع رویداد و زمان هستند. از تاریخچه برای پشتیبانی، حسابرسی و رفع اشکال استفاده کنید."
      },
      {
        "question": "پیش‌نمایش سفارش آزمایشی",
        "answer": "در ویرایشگر الگو، شناسه یک سفارش ووکامرس را در بخش پیش‌نمایش آزمایشی وارد کنید. محتوای فعلی ویرایشگر، از جمله تغییرات ذخیره‌نشده، با داده‌های آن سفارش نمایش داده می‌شود، بدون ایجاد یادداشت یا ارسال ایمیل. از سفارش آزمایشی یا کم‌اهمیت استفاده کنید."
      },
      {
        "question": "برگزیده‌های شخصی و الگوهای اخیراً استفاده‌شده",
        "answer": "هر مدیر می‌تواند در صفحه سفارش برگزیده شخصی مشخص کند. افزونه ده الگوی اخیر هر کاربر را نیز ذخیره و در فهرست بالاتر نمایش می‌دهد. برگزیده‌های عمومی میان همه کاربران مشترک می‌مانند و برگزیده شخصی فهرست کاربر دیگر را تغییر نمی‌دهد."
      },
      {
        "question": "صفحه عیب‌یابی",
        "answer": "<strong>یادداشت‌های سفارش Mailhilfe ← عیب‌یابی</strong> را باز کنید تا اطلاعاتی مانند نسخه‌های وردپرس، PHP و ووکامرس، وضعیت HPOS، وضعیت ایمیل یادداشت مشتری، زبان، تعداد الگوهای منتشرشده، وضعیت کش و WP_DEBUG را ببینید. هنگام درخواست پشتیبانی مقادیر عیب‌یابی را کپی کنید."
      },
      {
        "question": "هوک‌ها و فیلترهای توسعه‌دهندگان",
        "answer": "افزونه برای جای‌نگهدارها، مقادیر جای‌نگهدار، کلیدهای متای مجاز، نتایج الگو، شرایط، محتوای پیش‌نمایش، متن نهایی یادداشت، عملیات پیش و پس از افزودن یادداشت، رکوردهای تاریخچه و عیب‌یابی، هوک و فیلتر ارائه می‌دهد. نام و پارامترها در readme.txt مستند شده‌اند. همه داده‌های سفارشی را اعتبارسنجی، پاک‌سازی و ایمن‌سازی خروجی کنید."
      }
    ]
  },
  "vi": {
    "menu": "Câu hỏi thường gặp",
    "title": "Câu hỏi thường gặp",
    "intro": "Câu trả lời cho các câu hỏi phổ biến về Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Bạn không được phép quản lý mẫu ghi chú.",
    "items": [
      {
        "question": "Mailhilfe Order Note Manager for WooCommerce dùng để làm gì?",
        "answer": "Plugin tạo các mẫu ghi chú đơn hàng WooCommerce có thể tái sử dụng. Nhân viên có thể chọn mẫu trong đơn hàng, xem trước các biến đã được thay và thêm kết quả dưới dạng ghi chú nội bộ hoặc ghi chú cho khách hàng."
      },
      {
        "question": "Tôi tạo và quản lý mẫu ở đâu?",
        "answer": "Mở Ghi chú đơn hàng Mailhilfe trong menu quản trị WordPress. Tại đó, bạn có thể tạo, sửa, xóa, phân loại, đánh dấu yêu thích và sắp xếp mẫu bằng thao tác kéo và thả."
      },
      {
        "question": "Biến giữ chỗ hoạt động như thế nào?",
        "answer": "Các biến như {order_number}, {customer}, {billing_email}, {order_total} và {items} được thay bằng dữ liệu thực của đơn hàng trong bản xem trước và khi thêm ghi chú."
      },
      {
        "question": "Tôi có thể định dạng nội dung mẫu không?",
        "answer": "Có. Trình soạn thảo WordPress hỗ trợ đoạn văn, chữ đậm, chữ nghiêng, danh sách và liên kết. Plugin giữ HTML an toàn và loại bỏ mã đánh dấu không an toàn trước khi lưu hoặc nhập."
      },
      {
        "question": "Ghi chú nội bộ khác ghi chú cho khách hàng như thế nào?",
        "answer": "Ghi chú nội bộ chỉ dành cho đội ngũ cửa hàng. Ghi chú cho khách hàng có thể hiển thị cho khách hàng và có thể kích hoạt email thông báo WooCommerce, tùy theo cài đặt WooCommerce của bạn."
      },
      {
        "question": "Làm thế nào để xử lý ghi chú cho khách hàng an toàn?",
        "answer": "Ghi chú cho khách hàng có thể rời khỏi khu vực quản trị nội bộ. Nhân viên nên kiểm tra biến giữ chỗ, dữ liệu cá nhân, cách diễn đạt và loại ghi chú đã chọn trước khi thêm bất kỳ nội dung nào có thể đến tay khách hàng."
      },
      {
        "question": "Yêu thích, tìm kiếm và sắp xếp có tác dụng gì?",
        "answer": "Mục yêu thích giúp truy cập nhanh các mẫu quan trọng. Tìm kiếm lọc danh sách mẫu dài trên màn hình đơn hàng. Sắp xếp bằng kéo và thả quyết định thứ tự hiển thị mẫu."
      },
      {
        "question": "Nhập và xuất JSON hoạt động như thế nào?",
        "answer": "Chức năng xuất tạo một tệp JSON chứa tiêu đề, nội dung, loại ghi chú, danh mục, mục yêu thích, thứ tự và dữ liệu sử dụng của mẫu. Chức năng nhập có thể khôi phục hoặc chuyển mẫu sang cửa hàng khác."
      },
      {
        "question": "Mẫu minh họa được cài đặt như thế nào?",
        "answer": "Mở trang nhập/xuất và chọn thao tác mẫu minh họa. Mẫu minh họa được tạo bằng ngôn ngữ đi kèm phù hợp, bao gồm tiếng Việt; nếu không khớp, bộ tiếng Anh đã được kiểm tra sẽ được dùng."
      },
      {
        "question": "Vai trò nào có thể dùng plugin?",
        "answer": "Quản trị viên và quản lý cửa hàng tự động nhận các quyền của plugin. Plugin tách quyền quản lý mẫu khỏi quyền sử dụng mẫu trong đơn hàng."
      },
      {
        "question": "Plugin có tương thích HPOS không?",
        "answer": "Có. Plugin khai báo khả năng tương thích với WooCommerce HPOS và sử dụng API đơn hàng WooCommerce thay vì truy cập trực tiếp các bảng cơ sở dữ liệu đơn hàng."
      },
      {
        "question": "Plugin có thêm liên kết công khai ở giao diện cửa hàng không?",
        "answer": "Không. Plugin hoạt động trong khu vực quản trị WordPress và màn hình đơn hàng WooCommerce. Plugin không thêm liên kết “powered by” hoặc liên kết quảng bá công khai vào cửa hàng."
      },
      {
        "question": "Tôi nên kiểm tra gì nếu ngôn ngữ hiển thị sai?",
        "answer": "Kiểm tra Cài đặt > Tổng quan > Ngôn ngữ trang web và ngôn ngữ trong hồ sơ người dùng. Plugin bao gồm các tệp dự phòng đã được kiểm tra cho mọi ngôn ngữ được hỗ trợ, bao gồm tiếng Việt. Các ngôn ngữ khác nên được cung cấp qua gói ngôn ngữ WordPress.org đã được duyệt."
      },
      {
        "question": "Tôi có thể sửa bản xem trước trước khi thêm ghi chú không?",
        "answer": "Có. Sau khi chọn mẫu, bản xem trước chứa dữ liệu đơn hàng đã được thay và có thể được sửa trước khi lưu ghi chú. Bản xem trước đã sửa chính là ghi chú cuối cùng được thêm vào đơn hàng."
      },
      {
        "question": "Việc tạo ghi chú cho khách hàng và xử lý email được ghi lại như thế nào?",
        "answer": "Ghi chú cho khách hàng có thể kích hoạt email WooCommerce khi email tương ứng được bật. Plugin ghi nhận việc tạo ghi chú cho khách hàng riêng với quá trình xử lý email. Hãy kiểm tra bản xem trước có thể chỉnh sửa trước khi thêm ghi chú và dùng trang Lịch sử để xem kết quả của trình xử lý thư."
      },
      {
        "question": "Ngôn ngữ mẫu hoạt động như thế nào?",
        "answer": "Mỗi mẫu có thể được gán cho tất cả ngôn ngữ hoặc một ngôn ngữ đi kèm cụ thể. Trong cửa hàng đa ngôn ngữ, plugin cố gắng ưu tiên mẫu khớp với ngôn ngữ đơn hàng, ngôn ngữ người dùng hoặc ngôn ngữ trang web."
      },
      {
        "question": "Tôi có thể dùng trường meta tùy chỉnh của đơn hàng hoặc khách hàng không?",
        "answer": "Có. Các biến nâng cao như {order_meta:meta_key} và {customer_meta:meta_key} có thể đọc trường tùy chỉnh. Các khóa nhạy cảm chứa những từ như password, token, secret, session, auth hoặc hash bị chặn."
      },
      {
        "question": "Nhân viên có thể vô tình để lộ thông tin riêng tư không?",
        "answer": "Plugin không thể biết ý nghĩa nghiệp vụ của mọi biến giữ chỗ. Luôn kiểm tra ghi chú cho khách hàng trước khi lưu, đặc biệt khi dùng biến meta hoặc dữ liệu riêng của khách hàng."
      },
      {
        "question": "Điều gì xảy ra khi định dạng HTML bị tắt?",
        "answer": "Nếu cài đặt định dạng HTML bị tắt, nội dung mẫu có định dạng sẽ được chuyển thành văn bản thuần an toàn trước khi lưu ghi chú. Tùy chọn này phù hợp với cửa hàng chỉ muốn ghi chú rất đơn giản."
      },
      {
        "question": "Bản xem trước dữ liệu nhập bảo vệ mẫu hiện có như thế nào?",
        "answer": "Bản xem trước cho biết số mẫu sẽ được tạo, cập nhật hoặc bỏ qua trước khi thực hiện lần nhập cuối cùng. Điều này giúp tránh ghi đè ngoài ý muốn."
      },
      {
        "question": "Tôi có thể nhân bản mẫu không?",
        "answer": "Có. Dùng thao tác Nhân bản trong danh sách mẫu. Bản sao được tạo dưới dạng bản nháp và giữ nội dung, danh mục, trạng thái yêu thích và loại ghi chú, trong khi bộ đếm sử dụng bắt đầu từ 0."
      },
      {
        "question": "Tôi có thể hạn chế nhập JSON không?",
        "answer": "Có. Trang cài đặt có thể tắt chức năng nhập JSON. Ngay cả khi được bật, việc nhập vẫn yêu cầu quyền phù hợp, xác minh nonce và tệp JSON hợp lệ."
      },
      {
        "question": "Quyền được quản lý như thế nào?",
        "answer": "Dùng trang Quyền để cấp hoặc thu hồi quyền của plugin cho từng vai trò. Quản trị viên vẫn giữ quyền truy cập để plugin không vô tình khóa chủ sở hữu trang web."
      },
      {
        "question": "Plugin có hỗ trợ bản sửa đổi không?",
        "answer": "Có. Nội dung mẫu được sao chép vào nội dung bài viết WordPress để bản sửa đổi WordPress có thể theo dõi thay đổi khi tính năng bản sửa đổi khả dụng."
      },
      {
        "question": "Tôi nên làm gì trước khi dùng mẫu cho thông điệp gửi khách hàng thật?",
        "answer": "Tạo mẫu, chọn một đơn hàng thử nghiệm, kiểm tra bản xem trước, xác minh tất cả biến giữ chỗ và xác nhận loại ghi chú đã chọn trước khi thêm ghi chú. Với thông điệp quan trọng, trước tiên hãy dùng một ghi chú nội bộ để thử nghiệm."
      },
      {
        "question": "Tại sao trạng thái email của ghi chú cho khách hàng lại quan trọng?",
        "answer": "WooCommerce có thể gửi email khi ghi chú cho khách hàng được thêm. Hãy kiểm tra cấu hình email WooCommerce và xem thông tin xử lý trên trang Lịch sử và Chẩn đoán."
      },
      {
        "question": "Tôi có thể dùng plugin trong cửa hàng thử nghiệm không?",
        "answer": "Có. Xuất và nhập JSON hữu ích để chuyển mẫu giữa môi trường thử nghiệm và môi trường sản xuất. Kiểm tra cài đặt ghi chú cho khách hàng sau khi chuyển mẫu vì cài đặt email WooCommerce có thể khác nhau."
      },
      {
        "question": "Dữ liệu nào bị xóa khi gỡ cài đặt?",
        "answer": "Quy trình gỡ cài đặt xóa mẫu và tùy chọn riêng của plugin. Các ghi chú đơn hàng WooCommerce đã được thêm trước đó thuộc lịch sử đơn hàng WooCommerce và không bị xóa khi gỡ plugin."
      },
      {
        "question": "Tôi nên báo cáo sự cố như thế nào?",
        "answer": "Ghi lại phiên bản WordPress, phiên bản WooCommerce, HPOS có được bật hay không, ngôn ngữ đang hoạt động, mẫu đã chọn và các bước chính xác gây ra sự cố."
      },
      {
        "question": "Điều kiện mẫu",
        "answer": "Điều kiện mẫu quyết định mẫu có khả dụng cho một đơn hàng cụ thể hay không. Bạn có thể giới hạn mẫu theo trạng thái đơn hàng, phương thức thanh toán, phương thức giao hàng, quốc gia thanh toán và tổng đơn hàng tối thiểu hoặc tối đa. Tất cả điều kiện đã cấu hình đều phải khớp. Để trống trường khi điều kiện đó không nên giới hạn mẫu."
      },
      {
        "question": "Nhật ký xử lý email",
        "answer": "Đối với ghi chú cho khách hàng, plugin ghi lại khi WooCommerce báo email ghi chú khách hàng đã được xử lý và cũng ghi lại lỗi kỹ thuật của wp_mail. Sự kiện đã xử lý xác nhận WordPress/WooCommerce đã chuyển thư cho hệ thống gửi thư; sự kiện này không chứng minh thư đã được giao cuối cùng hoặc khách hàng đã đọc. Hãy kiểm tra Lịch sử để xem các sự kiện email đã xử lý và thất bại."
      },
      {
        "question": "Lịch sử tập trung",
        "answer": "Mở <strong>Ghi chú đơn hàng Mailhilfe → Lịch sử</strong> để xem việc tạo ghi chú gần đây, sử dụng mẫu, xử lý email và lỗi email. Khi có dữ liệu, mục lịch sử gồm đơn hàng, mẫu, người dùng, người nhận, loại sự kiện và thời gian. Dùng lịch sử cho hỗ trợ, kiểm tra và khắc phục sự cố."
      },
      {
        "question": "Xem trước với đơn hàng thử nghiệm",
        "answer": "Trong trình soạn thảo mẫu, nhập ID đơn hàng WooCommerce vào khu vực xem trước thử nghiệm. Nội dung hiện tại của trình soạn thảo, kể cả thay đổi chưa lưu, sẽ được hiển thị với dữ liệu của đơn hàng đó mà không tạo ghi chú hoặc gửi email. Hãy dùng đơn hàng trên trang thử nghiệm hoặc đơn hàng thử không quan trọng."
      },
      {
        "question": "Yêu thích cá nhân và mẫu dùng gần đây",
        "answer": "Mỗi quản trị viên có thể đánh dấu mục yêu thích cá nhân trên màn hình đơn hàng. Plugin cũng lưu mười mẫu được mỗi người dùng sử dụng gần đây nhất và đưa chúng lên vị trí cao hơn trong danh sách. Mục yêu thích toàn cục vẫn được chia sẻ; mục yêu thích cá nhân không thay đổi danh sách của người dùng khác."
      },
      {
        "question": "Trang chẩn đoán",
        "answer": "Mở <strong>Ghi chú đơn hàng Mailhilfe → Chẩn đoán</strong> để xem thông tin kỹ thuật như phiên bản WordPress, PHP và WooCommerce, trạng thái HPOS, trạng thái email ghi chú khách hàng, locale, số mẫu đã xuất bản, trạng thái bộ nhớ đệm và WP_DEBUG. Sao chép các giá trị chẩn đoán khi yêu cầu hỗ trợ."
      },
      {
        "question": "Hook và bộ lọc dành cho nhà phát triển",
        "answer": "Plugin cung cấp hook và bộ lọc cho biến giữ chỗ, giá trị biến giữ chỗ, khóa meta được phép, kết quả mẫu, điều kiện, nội dung xem trước, nội dung ghi chú cuối cùng, thao tác trước và sau khi thêm ghi chú, bản ghi lịch sử và chẩn đoán. Tên hook và tham số được ghi trong readme.txt. Hãy xác thực, làm sạch và thoát toàn bộ dữ liệu tùy chỉnh."
      }
    ]
  },
  "cs_CZ": {
    "menu": "Časté dotazy",
    "title": "Často kladené otázky",
    "intro": "Odpovědi na časté otázky k pluginu Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Nemáte oprávnění spravovat šablony poznámek.",
    "items": [
      {
        "question": "K čemu slouží Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "Plugin vytváří opakovaně použitelné šablony poznámek k objednávkám WooCommerce. Zaměstnanci mohou v objednávce vybrat šablonu, zobrazit náhled s nahrazenými zástupnými symboly a přidat výsledek jako interní poznámku nebo poznámku pro zákazníka."
      },
      {
        "question": "Kde se šablony vytvářejí a spravují?",
        "answer": "V administraci WordPressu otevřete Poznámky k objednávkám Mailhilfe. Zde můžete šablony vytvářet, upravovat, mazat, řadit do kategorií, označovat jako oblíbené a měnit jejich pořadí přetažením."
      },
      {
        "question": "Jak fungují zástupné symboly?",
        "answer": "Zástupné symboly jako {order_number}, {customer}, {billing_email}, {order_total} a {items} se v náhledu a při přidání poznámky nahradí skutečnými údaji objednávky."
      },
      {
        "question": "Lze texty šablon formátovat?",
        "answer": "Ano. WordPress editor podporuje odstavce, tučné písmo, kurzívu, seznamy a odkazy. Plugin zachová bezpečné HTML a před uložením nebo importem odstraní nebezpečné značky."
      },
      {
        "question": "Jaký je rozdíl mezi interní poznámkou a poznámkou pro zákazníka?",
        "answer": "Interní poznámky jsou určeny pouze týmu obchodu. Poznámky pro zákazníky mohou být zákazníkovi viditelné a podle nastavení WooCommerce mohou spustit e-mailové oznámení."
      },
      {
        "question": "Jak bezpečně pracovat s poznámkami pro zákazníky?",
        "answer": "Poznámky pro zákazníky mohou opustit interní administraci. Zaměstnanci by měli před přidáním obsahu, který se může dostat k zákazníkovi, zkontrolovat zástupné symboly, osobní údaje, formulaci i vybraný typ poznámky."
      },
      {
        "question": "Jak pomáhají oblíbené položky, hledání a řazení?",
        "answer": "Oblíbené položky udržují důležité šablony snadno dostupné. Hledání filtruje dlouhé seznamy šablon na obrazovce objednávky. Řazení přetažením určuje pořadí, ve kterém se šablony zobrazují."
      },
      {
        "question": "Jak funguje import a export JSON?",
        "answer": "Export vytvoří soubor JSON s názvy šablon, obsahem, typy poznámek, kategoriemi, oblíbenými položkami, řazením a údaji o použití. Import může šablony obnovit nebo přenést do jiného obchodu."
      },
      {
        "question": "Jak se instalují ukázkové šablony?",
        "answer": "Otevřete stránku importu/exportu a zvolte akci pro ukázkové šablony. Ukázkové šablony se vytvoří v odpovídajícím vestavěném jazyce, včetně perštiny, vietnamštiny a češtiny; jinak se použije zkontrolovaná anglická sada."
      },
      {
        "question": "Které role mohou plugin používat?",
        "answer": "Administrátoři a správci obchodu získají oprávnění pluginu automaticky. Plugin odděluje oprávnění spravovat šablony od oprávnění používat šablony v objednávkách."
      },
      {
        "question": "Je plugin kompatibilní s HPOS?",
        "answer": "Ano. Plugin deklaruje kompatibilitu s WooCommerce HPOS a používá API objednávek WooCommerce namísto přímého přístupu k databázovým tabulkám objednávek."
      },
      {
        "question": "Přidává plugin veřejné odkazy do frontendu?",
        "answer": "Ne. Plugin pracuje v administraci WordPressu a na obrazovkách objednávek WooCommerce. Do obchodu nepřidává odkazy „powered by“ ani veřejné propagační odkazy."
      },
      {
        "question": "Co zkontrolovat, pokud se zobrazuje nesprávný jazyk?",
        "answer": "Zkontrolujte Nastavení > Obecné > Jazyk webu a jazyk ve svém uživatelském profilu. Plugin obsahuje zkontrolované vestavěné záložní soubory pro všechny podporované jazyky, včetně perštiny, vietnamštiny a češtiny. Ostatní jazyky by měly být poskytovány prostřednictvím zkontrolovaných jazykových balíčků WordPress.org."
      },
      {
        "question": "Lze před přidáním poznámky upravit náhled?",
        "answer": "Ano. Po výběru šablony obsahuje náhled nahrazené údaje objednávky a před uložením poznámky jej lze upravit. Upravený náhled je konečnou poznámkou, která se přidá k objednávce."
      },
      {
        "question": "Jak se zaznamenává vytvoření poznámky pro zákazníka a zpracování e-mailu?",
        "answer": "Poznámky pro zákazníky mohou spustit e-mailová oznámení WooCommerce, pokud je příslušný e-mail povolen. Plugin zaznamenává vytvoření poznámky pro zákazníka odděleně od zpracování e-mailu. Před přidáním poznámky zkontrolujte upravitelný náhled a na stránce Historie ověřte výsledek zpracování e-mailu."
      },
      {
        "question": "Jak fungují jazyky šablon?",
        "answer": "Každou šablonu lze přiřadit všem jazykům nebo konkrétnímu vestavěnému jazyku. Ve vícejazyčných obchodech se plugin snaží upřednostnit šablony odpovídající jazyku objednávky, uživatele nebo webu."
      },
      {
        "question": "Lze použít vlastní meta pole objednávky nebo zákazníka?",
        "answer": "Ano. Pokročilé zástupné symboly jako {order_meta:meta_key} a {customer_meta:meta_key} mohou načítat vlastní pole. Citlivé klíče obsahující slova password, token, secret, session, auth nebo hash jsou blokovány."
      },
      {
        "question": "Mohou zaměstnanci omylem zveřejnit soukromé informace?",
        "answer": "Plugin nemůže znát obchodní význam každého zástupného symbolu. Před uložením vždy zkontrolujte poznámky pro zákazníky, zejména při použití meta zástupných symbolů nebo údajů konkrétního zákazníka."
      },
      {
        "question": "Co se stane, když je formátování HTML vypnuto?",
        "answer": "Pokud je formátování HTML vypnuto, formátovaný obsah šablony se před uložením poznámky převede na bezpečný prostý text. To je vhodné pro obchody, které chtějí pouze velmi jednoduché poznámky."
      },
      {
        "question": "Jak náhled importu chrání existující šablony?",
        "answer": "Náhled importu před konečným provedením ukáže, kolik šablon bude vytvořeno, aktualizováno nebo přeskočeno. Pomáhá tak zabránit nechtěnému přepsání."
      },
      {
        "question": "Lze šablonu duplikovat?",
        "answer": "Ano. V seznamu šablon použijte akci Duplikovat. Kopie se vytvoří jako koncept a zachová obsah, kategorie, stav oblíbené položky a typ poznámky; počítadlo použití začne od nuly."
      },
      {
        "question": "Lze omezit importy JSON?",
        "answer": "Ano. Na stránce nastavení lze importy JSON zakázat. I když jsou povoleny, import vyžaduje odpovídající oprávnění, ověření nonce a platný soubor JSON."
      },
      {
        "question": "Jak se spravují oprávnění?",
        "answer": "Na stránce oprávnění přidělujte nebo odebírejte oprávnění pluginu jednotlivým rolím. Administrátorům přístup zůstává, aby plugin nemohl omylem zablokovat vlastníka webu."
      },
      {
        "question": "Podporuje plugin revize?",
        "answer": "Ano. Obsah šablony se zrcadlí do obsahu příspěvku WordPressu, takže pokud jsou revize dostupné, mohou sledovat změny."
      },
      {
        "question": "Co udělat před použitím šablony pro skutečné zprávy zákazníkům?",
        "answer": "Vytvořte šablonu, vyberte testovací objednávku, zkontrolujte náhled, ověřte všechny zástupné symboly a před přidáním poznámky potvrďte vybraný typ. U důležitých zpráv nejprve použijte jako test interní poznámku."
      },
      {
        "question": "Proč je důležitý stav e-mailu s poznámkou pro zákazníka?",
        "answer": "WooCommerce může při přidání poznámky pro zákazníka odeslat e-mail. Zkontrolujte konfiguraci e-mailů WooCommerce a informace o zpracování na stránkách Historie a Diagnostika."
      },
      {
        "question": "Lze plugin používat v testovacím obchodě?",
        "answer": "Ano. Export a import JSON jsou vhodné pro přenos šablon mezi testovacím a produkčním webem. Po přenosu zkontrolujte nastavení poznámek pro zákazníky, protože nastavení e-mailů WooCommerce se může lišit."
      },
      {
        "question": "Která data se při odinstalaci odstraní?",
        "answer": "Odinstalační rutina odstraní vlastní šablony a nastavení pluginu. Poznámky WooCommerce již přidané k objednávkám patří do historie objednávky WooCommerce a odinstalací pluginu se neodstraní."
      },
      {
        "question": "Jak nahlásit problém?",
        "answer": "Uveďte verzi WordPressu, verzi WooCommerce, zda je zapnutý HPOS, aktivní jazyk, vybranou šablonu a přesné kroky, které problém způsobily."
      },
      {
        "question": "Podmínky šablony",
        "answer": "Podmínky šablony určují, zda je šablona pro konkrétní objednávku dostupná. Šablony lze omezit podle stavu objednávky, platební metody, způsobu dopravy, fakturační země a minimální nebo maximální hodnoty objednávky. Všechny nastavené podmínky musí být splněny. Pokud daná podmínka nemá šablonu omezovat, ponechte pole prázdné."
      },
      {
        "question": "Protokol zpracování e-mailů",
        "answer": "U poznámek pro zákazníky plugin zaznamenává, kdy WooCommerce oznámí zpracování e-mailu s poznámkou pro zákazníka, a také technické chyby wp_mail. Událost „zpracováno“ potvrzuje, že WordPress/WooCommerce předal zprávu poštovnímu systému; neprokazuje konečné doručení ani přečtení zákazníkem. Zpracované a neúspěšné e-mailové události kontrolujte na stránce Historie."
      },
      {
        "question": "Centrální historie",
        "answer": "Otevřete <strong>Poznámky k objednávkám Mailhilfe → Historie</strong> a zkontrolujte nedávné vytvoření poznámek, použití šablon, zpracování e-mailů a chyby e-mailů. Pokud jsou údaje dostupné, záznamy obsahují objednávku, šablonu, uživatele, příjemce, typ události a čas. Historii používejte pro podporu, audit a řešení problémů."
      },
      {
        "question": "Náhled testovací objednávky",
        "answer": "V editoru šablony zadejte do oblasti testovacího náhledu ID objednávky WooCommerce. Aktuální obsah editoru včetně neuložených změn se vykreslí s údaji z této objednávky, aniž by se vytvořila poznámka nebo odeslal e-mail. Použijte testovací nebo nekritickou objednávku."
      },
      {
        "question": "Osobní oblíbené položky a naposledy použité šablony",
        "answer": "Každý administrátor může na obrazovce objednávky označovat osobní oblíbené položky. Plugin také ukládá deset šablon, které každý uživatel použil naposledy, a zobrazuje je ve výběru výše. Globální oblíbené položky zůstávají sdílené se všemi uživateli. Osobní oblíbené položky nemění seznam jiného uživatele."
      },
      {
        "question": "Stránka diagnostiky",
        "answer": "Otevřete <strong>Poznámky k objednávkám Mailhilfe → Diagnostika</strong> a zobrazte technické informace, například verze WordPressu, PHP a WooCommerce, stav HPOS, stav e-mailů s poznámkami pro zákazníky, locale, počet publikovaných šablon, stav cache a WP_DEBUG. Při žádosti o podporu diagnostické hodnoty zkopírujte."
      },
      {
        "question": "Hooky a filtry pro vývojáře",
        "answer": "Plugin poskytuje hooky a filtry pro zástupné symboly, jejich hodnoty, povolené meta klíče, výsledky šablon, podmínky, obsah náhledu, konečný obsah poznámky, akce před přidáním poznámky a po něm, záznamy historie a diagnostiku. Názvy hooků a parametry jsou popsány v souboru readme.txt. Veškerá vlastní data validujte, sanitizujte a escapujte."
      }
    ]
  }
}
JSON;

		$sets = json_decode( $json, true );
		return is_array( $sets ) ? $sets : array();
	}
}
