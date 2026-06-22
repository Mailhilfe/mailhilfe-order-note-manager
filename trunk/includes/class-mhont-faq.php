<?php
/**
 * Multilingual FAQ page.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders built-in multilingual FAQ.
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
		$answer = preg_replace( '/(\{[a-z0-9_]+\})/', '<code>$1</code>', $answer );
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
	 * Returns all localized FAQ sets.
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
        "answer": "Open the import/export page and choose the demo template action. Demo templates are created in the current admin language when that language is bundled with the plugin."
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
        "answer": "Check Settings > General > Site Language and the language in your user profile. The plugin includes bundled translations and locale fallbacks for direct ZIP installations."
      },
      {
        "question": "Can I edit the preview before adding the note?",
        "answer": "Yes. After selecting a template, the preview contains the replaced order data and can be edited before the note is saved. The edited preview is the final note that will be added to the order."
      },
      {
        "question": "When is a customer notification recorded?",
        "answer": "When a template is added as a customer note, the plugin also adds an internal log note with date, time, current user and template name. This helps the shop team see when a customer-visible message was created."
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
        "answer": "Öffnen Sie die Import-/Exportseite und wählen Sie die Demo-Vorlagen-Aktion. Demo-Vorlagen werden in der aktuellen Adminsprache erstellt, wenn diese Sprache im Plugin enthalten ist."
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
        "answer": "Prüfen Sie Einstellungen > Allgemein > Sprache der Website und die Sprache in Ihrem Benutzerprofil. Das Plugin enthält gebündelte Übersetzungen und Locale-Fallbacks für direkte ZIP-Installationen."
      },
      {
        "question": "Kann ich die Vorschau vor dem Hinzufügen der Notiz bearbeiten?",
        "answer": "Ja. Nach Auswahl einer Vorlage enthält die Vorschau die ersetzten Bestelldaten und kann vor dem Speichern bearbeitet werden. Die bearbeitete Vorschau ist die endgültige Notiz, die zur Bestellung hinzugefügt wird."
      },
      {
        "question": "Wann wird eine Kundenbenachrichtigung protokolliert?",
        "answer": "Wenn eine Vorlage als Kundennotiz hinzugefügt wird, erstellt das Plugin zusätzlich eine interne Protokollnotiz mit Datum, Uhrzeit, aktuellem Benutzer und Vorlagenname. So sieht das Shop-Team, wann eine kunden sichtbare Nachricht erstellt wurde."
      },
      {
        "question": "Wie funktioniert die Vorlagensprache?",
        "answer": "Jede Vorlage kann für alle Sprachen oder für eine bestimmte enthaltene Sprache festgelegt werden. In mehrsprachigen Shops versucht das Plugin, Vorlagen passend zur Bestellsprache, Benutzersprache oder Website-Sprache zu bevorzugen."
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
        "answer": "Ja. Der Vorlageninhalt wird zusätzlich im WordPress-Beitragsinhalt gespiegelt, damit WordPress-Revisions Änderungen nachverfolgen können, wenn Revisionen verfügbar sind."
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
  "fr_FR": {
    "menu": "FAQ",
    "title": "Foire aux questions",
    "intro": "Réponses aux questions fréquentes sur Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Vous n’êtes pas autorisé à gérer les modèles de notes.",
    "items": [
      {
        "question": "À quoi sert Mailhilfe Order Note Manager for WooCommerce ?",
        "answer": "L’extension crée des modèles réutilisables pour les notes de commande WooCommerce. Le personnel peut choisir un modèle dans une commande, vérifier l’aperçu et ajouter le résultat comme note interne ou note client."
      },
      {
        "question": "Où gérer les modèles ?",
        "answer": "Ouvrez Modèles de notes de commande dans l’administration WordPress. Vous pouvez créer, modifier, supprimer, classer, marquer comme favori et trier les modèles."
      },
      {
        "question": "Comment fonctionnent les espaces réservés ?",
        "answer": "Les espaces réservés comme {order_number}, {customer}, {billing_email}, {order_total} et {items} sont remplacés par les vraies données de la commande."
      },
      {
        "question": "Puis-je formater le texte ?",
        "answer": "Oui. L’éditeur WordPress permet les paragraphes, le gras, l’italique, les listes et les liens. Le HTML non sûr est supprimé."
      },
      {
        "question": "Note interne ou note client ?",
        "answer": "Les notes internes sont destinées à l’équipe de la boutique. Les notes client peuvent être visibles par le client et déclencher des e-mails WooCommerce."
      },
      {
        "question": "Pourquoi un avertissement pour les notes client ?",
        "answer": "L’avertissement rappelle de vérifier les données personnelles, les espaces réservés et le texte avant qu’une note puisse atteindre le client."
      },
      {
        "question": "À quoi servent favoris, recherche et tri ?",
        "answer": "Les favoris rendent les modèles importants plus accessibles, la recherche filtre les longues listes et le tri glisser-déposer définit l’ordre."
      },
      {
        "question": "Comment utiliser l’import/export JSON ?",
        "answer": "L’export crée un fichier JSON avec les modèles et leurs données. L’import permet de restaurer ou transférer ces modèles."
      },
      {
        "question": "Les modèles de démonstration sont-ils traduits ?",
        "answer": "Oui. Ils sont créés dans la langue actuelle de l’administration lorsqu’elle est fournie avec l’extension."
      },
      {
        "question": "L’extension est-elle compatible HPOS ?",
        "answer": "Oui. Elle utilise les API de commande WooCommerce et déclare la compatibilité HPOS."
      },
      {
        "question": "Vérifier l’aperçu",
        "answer": "Vérifiez toujours le nom, le total, les articles et le type de note avant d’ajouter la note. Vérifiez toujours le nom, le total, les articles et le type de note avant d’ajouter la note. Use clear titles and categories."
      },
      {
        "question": "État de l’e-mail de note client",
        "answer": "Lorsqu’une note client est sélectionnée, le plugin affiche un avertissement et tente d’indiquer si l’e-mail WooCommerce de note client est actif. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Langue du modèle et boutiques multilingues",
        "answer": "Chaque modèle peut recevoir une langue. Choisissez <strong>Toutes les langues</strong> pour un texte utilisable partout, ou une langue précise pour les messages client traduits. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Champs personnalisés et métadonnées",
        "answer": "Les espaces réservés <code>{order_meta:meta_key}</code> et <code>{customer_meta:meta_key}</code> peuvent insérer des métadonnées sélectionnées. Les clés sensibles comme password, token ou secret sont bloquées. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Notes internes et notes client",
        "answer": "Les notes internes sont réservées au personnel. Les notes client peuvent être visibles et déclencher des e-mails WooCommerce. Les notes internes sont réservées au personnel. Les notes client peuvent être visibles et déclencher des e-mails WooCommerce. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Page des réglages",
        "answer": "Ouvrez <strong>Mailhilfe Order Notes → Réglages</strong> pour définir le comportement par défaut du plugin. Vous pouvez choisir le type de note par défaut, autoriser le HTML, contrôler les avertissements de notes client, l’affichage du compteur d’utilisation et les imports JSON. Utilisez les notes internes comme valeur par défaut pour un travail quotidien plus sûr. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Aperçu d’importation",
        "answer": "L’import JSON affiche un aperçu avec les modèles créés, mis à jour ou ignorés avant l’application définitive. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Dupliquer et révisions",
        "answer": "L’action de duplication crée une copie en brouillon. Les révisions WordPress aident à comparer et restaurer d’anciennes versions. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Autorisations",
        "answer": "La page <strong>Permissions</strong> permet de définir quelles rôles gèrent les modèles et quelles rôles les utilisent dans les commandes. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Flux de travail recommandé",
        "answer": "Sélectionnez un modèle, vérifiez l’aperçu remplacé, modifiez-le si nécessaire, contrôlez le type de note puis ajoutez la note. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Puis-je utiliser l’extension sur un site de préproduction ?",
        "answer": "Oui. Utilisez l’export/import JSON et une commande de test pour vérifier modèles, conditions, variables et e-mails avant le passage en production."
      },
      {
        "question": "Quelles données sont supprimées lors de la désinstallation ?",
        "answer": "La suppression retire les modèles, catégories, réglages, table d’historique, favoris personnels et modèles récents. Les notes WooCommerce déjà ajoutées restent dans l’historique des commandes."
      },
      {
        "question": "Comment signaler un problème ?",
        "answer": "Indiquez les versions WordPress, WooCommerce, PHP et du plugin, l’état HPOS, la langue, les étapes et le message d’erreur. Ajoutez les valeurs de diagnostic sans données personnelles client."
      },
      {
        "question": "Conditions des modèles",
        "answer": "Les conditions déterminent si un modèle est disponible pour une commande. Vous pouvez limiter un modèle selon l’état de la commande, le moyen de paiement, le mode de livraison, le pays de facturation et un montant minimal ou maximal. Toutes les conditions renseignées doivent être remplies. Laissez un champ vide pour ne pas appliquer cette restriction."
      },
      {
        "question": "Journal du traitement des e-mails",
        "answer": "Pour les notes client, l’extension enregistre le traitement signalé par WooCommerce et les erreurs techniques de wp_mail. Un état « traité » confirme la remise au système de messagerie, mais pas la livraison finale ni la lecture par le client. Consultez la page Historique pour les événements traités ou échoués."
      },
      {
        "question": "Historique central",
        "answer": "Ouvrez <strong>Mailhilfe Order Notes → Historique</strong> pour voir les notes créées, l’utilisation des modèles, le traitement des e-mails et les erreurs. La commande, le modèle, l’utilisateur, le destinataire, le type d’événement et l’heure sont affichés lorsqu’ils sont disponibles. Utilisez l’historique pour l’assistance, l’audit et le dépannage."
      },
      {
        "question": "Aperçu avec une commande de test",
        "answer": "Dans l’éditeur du modèle, saisissez l’identifiant d’une commande WooCommerce. Le contenu actuel, même non enregistré, est affiché avec les données de cette commande sans créer de note ni envoyer d’e-mail. Utilisez une commande de test ou un site de préproduction."
      },
      {
        "question": "Favoris personnels et modèles récents",
        "answer": "Chaque utilisateur peut marquer ses favoris personnels dans la commande. L’extension mémorise aussi les dix derniers modèles utilisés avec succès par utilisateur et les place plus haut. Les favoris globaux restent communs à tous. Les favoris personnels n’affectent pas les autres utilisateurs."
      },
      {
        "question": "Page de diagnostic",
        "answer": "Ouvrez <strong>Mailhilfe Order Notes → Diagnostic</strong> pour voir les versions WordPress, PHP et WooCommerce, l’état HPOS, l’e-mail de note client, la langue, le nombre de modèles publiés, le cache et WP_DEBUG. Communiquez ces informations lors d’une demande d’assistance."
      },
      {
        "question": "Actions et filtres pour développeurs",
        "answer": "Des actions et filtres permettent d’étendre les variables, leurs valeurs, les clés de métadonnées autorisées, les résultats de modèles, les conditions, l’aperçu, le contenu final, les actions avant/après ajout, l’historique et le diagnostic. Les noms sont documentés dans readme.txt. Validez, nettoyez et échappez toutes les données personnalisées."
      }
    ]
  },
  "es_ES": {
    "menu": "FAQ",
    "title": "Preguntas frecuentes",
    "intro": "Respuestas a preguntas comunes sobre Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "No tienes permiso para gestionar plantillas de notas.",
    "items": [
      {
        "question": "¿Para qué sirve Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "El plugin crea plantillas reutilizables para notas de pedidos de WooCommerce. El personal puede elegir una plantilla en un pedido, revisar la vista previa y añadirla como nota interna o nota para el cliente."
      },
      {
        "question": "¿Dónde se gestionan las plantillas?",
        "answer": "Abra Plantillas de notas de pedido en el administrador de WordPress. Allí puede crear, editar, borrar, categorizar, marcar como favorita y ordenar plantillas."
      },
      {
        "question": "¿Cómo funcionan los marcadores?",
        "answer": "Marcadores como {order_number}, {customer}, {billing_email}, {order_total} y {items} se sustituyen por datos reales del pedido."
      },
      {
        "question": "¿Se puede formatear el texto?",
        "answer": "Sí. El editor de WordPress permite párrafos, negrita, cursiva, listas y enlaces. El HTML inseguro se elimina."
      },
      {
        "question": "¿Nota interna o nota para el cliente?",
        "answer": "Las notas internas son para el equipo de la tienda. Las notas para el cliente pueden ser visibles para el cliente y activar correos de WooCommerce."
      },
      {
        "question": "¿Por qué aparece una advertencia en notas de cliente?",
        "answer": "La advertencia recuerda revisar datos personales, marcadores y redacción antes de que la nota pueda llegar al cliente."
      },
      {
        "question": "¿Para qué sirven favoritos, búsqueda y ordenación?",
        "answer": "Los favoritos facilitan el acceso a plantillas importantes, la búsqueda filtra listas largas y la ordenación por arrastrar define el orden."
      },
      {
        "question": "¿Cómo funciona importar/exportar JSON?",
        "answer": "La exportación crea un archivo JSON con las plantillas y sus datos. La importación permite restaurarlas o trasladarlas a otra tienda."
      },
      {
        "question": "¿Hay plantillas de demostración traducidas?",
        "answer": "Sí. Se crean en el idioma actual del administrador cuando el idioma está incluido en el plugin."
      },
      {
        "question": "¿Es compatible con HPOS?",
        "answer": "Sí. El plugin usa las API de pedidos de WooCommerce y declara compatibilidad con HPOS."
      },
      {
        "question": "Comprobar la vista previa",
        "answer": "Compruebe siempre nombre, total, artículos y tipo de nota antes de añadirla. Compruebe siempre nombre, total, artículos y tipo de nota antes de añadirla. Use clear titles and categories."
      },
      {
        "question": "Estado del correo de nota al cliente",
        "answer": "Al seleccionar una nota al cliente, el plugin muestra una advertencia e intenta indicar si el correo WooCommerce de nota al cliente está activo. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Idioma de la plantilla y tiendas multilingües",
        "answer": "Cada plantilla puede tener un idioma. Elige <strong>Todos los idiomas</strong> para textos generales o un idioma concreto para mensajes traducidos al cliente. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Campos personalizados y metadatos",
        "answer": "Los marcadores <code>{order_meta:meta_key}</code> y <code>{customer_meta:meta_key}</code> insertan metadatos seleccionados. Se bloquean claves sensibles como password, token o secret. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Notas internas y notas de cliente",
        "answer": "Las notas internas son para el personal. Las notas de cliente pueden ser visibles y activar correos de WooCommerce. Las notas internas son para el personal. Las notas de cliente pueden ser visibles y activar correos de WooCommerce. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Página de ajustes",
        "answer": "Abre <strong>Mailhilfe Order Notes → Ajustes</strong> para definir el comportamiento predeterminado del plugin. Puedes elegir el tipo de nota predeterminado, permitir HTML, controlar avisos de notas al cliente, contadores de uso e importaciones JSON. Usa notas internas como valor predeterminado para trabajar con más seguridad. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Vista previa de importación",
        "answer": "La importación JSON muestra una vista previa con plantillas creadas, actualizadas u omitidas antes de aplicar cambios. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Duplicar y revisiones",
        "answer": "La acción de duplicar crea una copia como borrador. Las revisiones de WordPress permiten comparar y restaurar versiones anteriores. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Permisos",
        "answer": "La página <strong>Permisos</strong> define qué roles gestionan plantillas y qué roles las usan en pedidos. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Flujo recomendado",
        "answer": "Selecciona una plantilla, revisa la vista previa, edítala si hace falta, confirma el tipo de nota y añade la nota. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "¿Puedo usar el plugin en una tienda de staging?",
        "answer": "Sí. Usa exportación/importación JSON y un pedido de prueba para comprobar plantillas, condiciones, marcadores y correos antes de pasar a producción."
      },
      {
        "question": "¿Qué datos se eliminan al desinstalar?",
        "answer": "Se eliminan plantillas, categorías, ajustes, tabla de historial, favoritos personales y plantillas recientes. Las notas de WooCommerce ya añadidas permanecen en el historial del pedido."
      },
      {
        "question": "¿Cómo debo informar de un problema?",
        "answer": "Incluye versiones de WordPress, WooCommerce, PHP y del plugin, estado de HPOS, idioma, pasos exactos y error. Añade los valores de diagnóstico sin datos personales de clientes."
      },
      {
        "question": "Condiciones de las plantillas",
        "answer": "Las condiciones determinan si una plantilla está disponible para un pedido. Se puede limitar por estado, método de pago, método de envío, país de facturación y total mínimo o máximo. Todas las condiciones configuradas deben cumplirse. Deja un campo vacío si no debe limitar la plantilla."
      },
      {
        "question": "Registro del procesamiento del correo",
        "answer": "Para las notas de cliente, el plugin registra cuando WooCommerce informa que el correo se ha procesado y también los errores técnicos de wp_mail. «Procesado» confirma la entrega al sistema de correo, no la recepción final ni la lectura. Consulta Historial para ver eventos procesados y fallidos."
      },
      {
        "question": "Historial central",
        "answer": "Abre <strong>Mailhilfe Order Notes → Historial</strong> para revisar notas creadas, uso de plantillas, procesamiento y fallos de correo. Cuando están disponibles se muestran pedido, plantilla, usuario, destinatario, tipo de evento y fecha. Úsalo para soporte, auditoría y diagnóstico."
      },
      {
        "question": "Vista previa con pedido de prueba",
        "answer": "En el editor introduce el ID de un pedido de WooCommerce. El contenido actual, incluso sin guardar, se previsualiza con los datos de ese pedido sin crear una nota ni enviar un correo. Usa un pedido de prueba o un sitio de staging."
      },
      {
        "question": "Favoritos personales y plantillas recientes",
        "answer": "Cada usuario puede marcar favoritos personales en el pedido. El plugin también guarda las diez últimas plantillas usadas correctamente por cada usuario y las coloca más arriba. Los favoritos globales siguen siendo compartidos. Los favoritos personales no afectan a otros usuarios."
      },
      {
        "question": "Página de diagnóstico",
        "answer": "Abre <strong>Mailhilfe Order Notes → Diagnóstico</strong> para ver versiones de WordPress, PHP y WooCommerce, estado de HPOS, correo de nota al cliente, idioma, número de plantillas, caché y WP_DEBUG. Incluye estos datos al solicitar soporte."
      },
      {
        "question": "Acciones y filtros para desarrolladores",
        "answer": "El plugin ofrece acciones y filtros para marcadores, valores, claves meta permitidas, resultados, condiciones, vista previa, contenido final, acciones antes y después de añadir, historial y diagnóstico. Los nombres están documentados en readme.txt. Valida, sanea y escapa todos los datos personalizados."
      }
    ]
  },
  "it_IT": {
    "menu": "FAQ",
    "title": "Domande frequenti",
    "intro": "Risposte alle domande comuni su Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Non hai il permesso di gestire i modelli di note.",
    "items": [
      {
        "question": "A cosa serve Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "Il plugin crea modelli riutilizzabili per le note degli ordini WooCommerce. Il personale può scegliere un modello nell’ordine, controllare l’anteprima e aggiungerlo come nota interna o nota cliente."
      },
      {
        "question": "Dove si gestiscono i modelli?",
        "answer": "Apri Modelli note ordine nell’area amministrativa di WordPress. Puoi creare, modificare, eliminare, categorizzare, contrassegnare come preferito e ordinare i modelli."
      },
      {
        "question": "Come funzionano i segnaposto?",
        "answer": "Segnaposto come {order_number}, {customer}, {billing_email}, {order_total} e {items} vengono sostituiti dai dati reali dell’ordine."
      },
      {
        "question": "Posso formattare il testo?",
        "answer": "Sì. L’editor WordPress supporta paragrafi, grassetto, corsivo, elenchi e link. L’HTML non sicuro viene rimosso."
      },
      {
        "question": "Nota interna o nota cliente?",
        "answer": "Le note interne sono per il team del negozio. Le note cliente possono essere visibili al cliente e attivare e-mail WooCommerce."
      },
      {
        "question": "Perché c’è un avviso per le note cliente?",
        "answer": "L’avviso ricorda di controllare dati personali, segnaposto e formulazioni prima che una nota possa raggiungere il cliente."
      },
      {
        "question": "A cosa servono preferiti, ricerca e ordinamento?",
        "answer": "I preferiti rendono più accessibili i modelli importanti, la ricerca filtra liste lunghe e l’ordinamento drag-and-drop definisce l’ordine."
      },
      {
        "question": "Come funziona import/export JSON?",
        "answer": "L’esportazione crea un file JSON con modelli e dati. L’importazione consente di ripristinarli o trasferirli in un altro negozio."
      },
      {
        "question": "I modelli demo sono tradotti?",
        "answer": "Sì. Vengono creati nella lingua amministrativa corrente quando inclusa nel plugin."
      },
      {
        "question": "È compatibile con HPOS?",
        "answer": "Sì. Il plugin usa le API ordini WooCommerce e dichiara la compatibilità HPOS."
      },
      {
        "question": "Controllare l’anteprima",
        "answer": "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza. Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza. Use clear titles and categories."
      },
      {
        "question": "Stato email nota cliente",
        "answer": "Quando viene scelta una nota cliente, il plugin mostra un avviso e prova a indicare se l’email WooCommerce della nota cliente è attiva. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Lingua del modello e negozi multilingue",
        "answer": "Ogni modello può avere una lingua. Scegli <strong>Tutte le lingue</strong> per testi generali o una lingua specifica per messaggi cliente tradotti. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Campi personalizzati e metadati",
        "answer": "I segnaposto <code>{order_meta:meta_key}</code> e <code>{customer_meta:meta_key}</code> inseriscono metadati selezionati. Chiavi sensibili come password, token o secret sono bloccate. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Note interne e note cliente",
        "answer": "Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza. Questa guida spiega il flusso completo: creare modelli formattati, usare segnaposto, aggiungere note agli ordini WooCommerce, importare/esportare JSON, gestire i permessi, usare HPOS e lavorare in sicurezza. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Pagina impostazioni",
        "answer": "Apri <strong>Mailhilfe Order Notes → Impostazioni</strong> per definire il comportamento predefinito del plugin. Puoi scegliere il tipo di nota predefinito, consentire HTML, gestire avvisi per note cliente, contatori d’uso e import JSON. Usa le note interne come impostazione predefinita per una maggiore sicurezza. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Anteprima importazione",
        "answer": "L’import JSON mostra un’anteprima dei modelli creati, aggiornati o saltati prima di applicare le modifiche. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Duplicazione e revisioni",
        "answer": "La duplicazione crea una copia in bozza. Le revisioni di WordPress aiutano a confrontare e ripristinare versioni precedenti. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Permessi",
        "answer": "La pagina <strong>Permessi</strong> stabilisce quali ruoli gestiscono i modelli e quali li usano negli ordini. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Flusso consigliato",
        "answer": "Seleziona un modello, controlla l’anteprima, modificala se necessario, verifica il tipo di nota e aggiungi la nota. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Posso usare il plugin in un negozio di staging?",
        "answer": "Sì. Usa import/export JSON e un ordine di prova per verificare modelli, condizioni, segnaposto ed e-mail prima della produzione."
      },
      {
        "question": "Quali dati vengono rimossi con la disinstallazione?",
        "answer": "Vengono rimossi modelli, categorie, impostazioni, tabella cronologia, preferiti personali e modelli recenti. Le note WooCommerce già aggiunte restano nella cronologia ordine."
      },
      {
        "question": "Come devo segnalare un problema?",
        "answer": "Indica versioni di WordPress, WooCommerce, PHP e plugin, stato HPOS, lingua, passaggi esatti ed errore. Aggiungi i dati diagnostici senza dati personali dei clienti."
      },
      {
        "question": "Condizioni dei modelli",
        "answer": "Le condizioni stabiliscono se un modello è disponibile per un ordine. È possibile limitarlo per stato dell’ordine, metodo di pagamento, metodo di spedizione, paese di fatturazione e totale minimo o massimo. Tutte le condizioni impostate devono corrispondere. Lascia un campo vuoto se non deve limitare il modello."
      },
      {
        "question": "Registro dell’elaborazione e-mail",
        "answer": "Per le note cliente il plugin registra quando WooCommerce segnala l’e-mail come elaborata e gli errori tecnici di wp_mail. «Elaborata» indica il passaggio al sistema di posta, non la consegna finale o la lettura. Controlla la pagina Cronologia per gli eventi elaborati o non riusciti."
      },
      {
        "question": "Cronologia centrale",
        "answer": "Apri <strong>Mailhilfe Order Notes → Cronologia</strong> per vedere note create, utilizzo dei modelli, elaborazione e errori e-mail. Quando disponibili vengono mostrati ordine, modello, utente, destinatario, tipo di evento e ora. Usa la cronologia per assistenza, controllo e risoluzione dei problemi."
      },
      {
        "question": "Anteprima con ordine di prova",
        "answer": "Nell’editor inserisci l’ID di un ordine WooCommerce. Il contenuto corrente, anche non salvato, viene mostrato con i dati dell’ordine senza creare note o inviare e-mail. Usa un ordine di prova o un sito di staging."
      },
      {
        "question": "Preferiti personali e modelli recenti",
        "answer": "Ogni utente può impostare preferiti personali nella schermata ordine. Il plugin memorizza anche gli ultimi dieci modelli usati con successo e li mostra più in alto. I preferiti globali restano condivisi. I preferiti personali non modificano l’elenco degli altri utenti."
      },
      {
        "question": "Pagina diagnostica",
        "answer": "Apri <strong>Mailhilfe Order Notes → Diagnostica</strong> per vedere versioni WordPress, PHP e WooCommerce, stato HPOS, e-mail nota cliente, lingua, numero di modelli pubblicati, cache e WP_DEBUG. Fornisci questi dati nelle richieste di assistenza."
      },
      {
        "question": "Hook e filtri per sviluppatori",
        "answer": "Sono disponibili hook e filtri per segnaposto, valori, chiavi meta consentite, risultati dei modelli, condizioni, anteprima, contenuto finale, azioni prima/dopo l’aggiunta, cronologia e diagnostica. I nomi sono documentati in readme.txt. Valida, sanifica ed esegui l’escape dei dati personalizzati."
      }
    ]
  },
  "pt_BR": {
    "menu": "FAQ",
    "title": "Perguntas frequentes",
    "intro": "Respostas para perguntas comuns sobre Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Você não tem permissão para gerenciar modelos de notas.",
    "items": [
      {
        "question": "Para que serve o Mailhilfe Order Note Manager for WooCommerce?",
        "answer": "O plugin cria modelos reutilizáveis para notas de pedidos WooCommerce. A equipe pode escolher um modelo dentro do pedido, revisar a prévia e adicionar como nota interna ou nota para o cliente."
      },
      {
        "question": "Onde gerencio os modelos?",
        "answer": "Abra Modelos de notas de pedido no admin do WordPress. Você pode criar, editar, excluir, categorizar, marcar como favorito e ordenar modelos."
      },
      {
        "question": "Como funcionam os placeholders?",
        "answer": "Placeholders como {order_number}, {customer}, {billing_email}, {order_total} e {items} são substituídos pelos dados reais do pedido."
      },
      {
        "question": "Posso formatar o texto?",
        "answer": "Sim. O editor do WordPress permite parágrafos, negrito, itálico, listas e links. HTML inseguro é removido."
      },
      {
        "question": "Nota interna ou nota para cliente?",
        "answer": "Notas internas são para a equipe da loja. Notas para cliente podem ficar visíveis ao cliente e acionar e-mails do WooCommerce."
      },
      {
        "question": "Por que há aviso em notas para cliente?",
        "answer": "O aviso lembra de revisar dados pessoais, placeholders e texto antes que a nota possa chegar ao cliente."
      },
      {
        "question": "Para que servem favoritos, busca e ordenação?",
        "answer": "Favoritos facilitam o acesso a modelos importantes, a busca filtra listas longas e a ordenação por arrastar define a ordem."
      },
      {
        "question": "Como funciona importar/exportar JSON?",
        "answer": "A exportação cria um arquivo JSON com modelos e dados. A importação permite restaurar ou transferir para outra loja."
      },
      {
        "question": "Há modelos de demonstração traduzidos?",
        "answer": "Sim. Eles são criados no idioma atual do admin quando esse idioma está incluído no plugin."
      },
      {
        "question": "É compatível com HPOS?",
        "answer": "Sim. O plugin usa as APIs de pedidos do WooCommerce e declara compatibilidade com HPOS."
      },
      {
        "question": "Verificar a prévia",
        "answer": "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança. Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança. Use clear titles and categories."
      },
      {
        "question": "Status do e-mail de nota ao cliente",
        "answer": "Ao selecionar uma nota ao cliente, o plugin mostra um aviso e tenta indicar se o e-mail WooCommerce de nota ao cliente está ativo. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Idioma do modelo e lojas multilíngues",
        "answer": "Cada modelo pode ter um idioma. Escolha <strong>Todos os idiomas</strong> para textos gerais ou um idioma específico para mensagens traduzidas ao cliente. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Campos personalizados e metadados",
        "answer": "Os placeholders <code>{order_meta:meta_key}</code> e <code>{customer_meta:meta_key}</code> inserem metadados selecionados. Chaves sensíveis como password, token ou secret são bloqueadas. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Notas internas e notas do cliente",
        "answer": "Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança. Esta ajuda explica o fluxo completo: criar modelos formatados, usar espaços reservados, adicionar notas em pedidos WooCommerce, importar/exportar JSON, gerenciar permissões, usar HPOS e trabalhar com segurança. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Página de configurações",
        "answer": "Abra <strong>Mailhilfe Order Notes → Configurações</strong> para definir o comportamento padrão do plugin. Você pode escolher o tipo de nota padrão, permitir HTML, controlar avisos de notas ao cliente, contadores de uso e importações JSON. Use notas internas como padrão para um fluxo diário mais seguro. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Prévia de importação",
        "answer": "A importação JSON mostra uma prévia com modelos criados, atualizados ou ignorados antes da aplicação. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Duplicar e revisões",
        "answer": "A ação de duplicar cria uma cópia como rascunho. As revisões do WordPress ajudam a comparar e restaurar versões anteriores. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Permissões",
        "answer": "A página <strong>Permissões</strong> define quais funções gerenciam modelos e quais podem usá-los nos pedidos. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Fluxo recomendado",
        "answer": "Selecione um modelo, revise a prévia, edite se necessário, confirme o tipo de nota e adicione a nota. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Posso usar o plugin em uma loja de staging?",
        "answer": "Sim. Use exportação/importação JSON e um pedido de teste para conferir modelos, condições, placeholders e e-mails antes da produção."
      },
      {
        "question": "Quais dados são removidos na desinstalação?",
        "answer": "São removidos modelos, categorias, configurações, tabela de histórico, favoritos pessoais e modelos recentes. Notas WooCommerce já adicionadas permanecem no histórico do pedido."
      },
      {
        "question": "Como devo relatar um problema?",
        "answer": "Informe versões do WordPress, WooCommerce, PHP e plugin, status do HPOS, idioma, passos exatos e erro. Inclua os dados de diagnóstico sem informações pessoais de clientes."
      },
      {
        "question": "Condições dos modelos",
        "answer": "As condições definem se um modelo fica disponível para um pedido. É possível restringir por status, forma de pagamento, método de envio, país de cobrança e total mínimo ou máximo. Todas as condições preenchidas devem corresponder. Deixe um campo vazio quando ele não deve restringir o modelo."
      },
      {
        "question": "Registro do processamento de e-mail",
        "answer": "Para notas do cliente, o plugin registra quando o WooCommerce informa que o e-mail foi processado e também erros técnicos do wp_mail. “Processado” confirma o envio ao sistema de e-mail, não a entrega final nem a leitura. Consulte Histórico para eventos processados e com falha."
      },
      {
        "question": "Histórico central",
        "answer": "Abra <strong>Mailhilfe Order Notes → Histórico</strong> para revisar notas criadas, uso de modelos, processamento e falhas de e-mail. Quando disponíveis são exibidos pedido, modelo, usuário, destinatário, tipo de evento e horário. Use o histórico para suporte, auditoria e solução de problemas."
      },
      {
        "question": "Prévia com pedido de teste",
        "answer": "No editor, informe o ID de um pedido WooCommerce. O conteúdo atual, inclusive alterações ainda não salvas, é exibido com os dados do pedido sem criar nota nem enviar e-mail. Use um pedido de teste ou um ambiente de staging."
      },
      {
        "question": "Favoritos pessoais e modelos recentes",
        "answer": "Cada usuário pode marcar favoritos pessoais na tela do pedido. O plugin também guarda os dez últimos modelos usados com sucesso por usuário e os posiciona mais acima. Favoritos globais continuam compartilhados. Favoritos pessoais não alteram a lista de outros usuários."
      },
      {
        "question": "Página de diagnóstico",
        "answer": "Abra <strong>Mailhilfe Order Notes → Diagnóstico</strong> para ver versões do WordPress, PHP e WooCommerce, status do HPOS, e-mail de nota do cliente, idioma, quantidade de modelos, cache e WP_DEBUG. Inclua esses dados ao solicitar suporte."
      },
      {
        "question": "Hooks e filtros para desenvolvedores",
        "answer": "O plugin oferece hooks e filtros para placeholders, valores, chaves meta permitidas, resultados, condições, prévia, conteúdo final, ações antes/depois da inclusão, histórico e diagnóstico. Os nomes estão documentados no readme.txt. Valide, higienize e escape todos os dados personalizados."
      }
    ]
  },
  "nl_NL": {
    "menu": "FAQ",
    "title": "Veelgestelde vragen",
    "intro": "Antwoorden op veelgestelde vragen over Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Je mag geen notitiesjablonen beheren.",
    "items": [
      {
        "question": "Waarvoor is Mailhilfe Order Note Manager for WooCommerce bedoeld?",
        "answer": "De plugin maakt herbruikbare WooCommerce-bestelnotitiesjablonen. Medewerkers kunnen in een bestelling een sjabloon kiezen, de preview controleren en het resultaat als interne notitie of klantnotitie toevoegen."
      },
      {
        "question": "Waar beheer ik sjablonen?",
        "answer": "Open Bestelnotitiesjablonen in het WordPress-beheer. Daar kun je sjablonen maken, bewerken, verwijderen, categoriseren, als favoriet markeren en sorteren."
      },
      {
        "question": "Hoe werken placeholders?",
        "answer": "Placeholders zoals {order_number}, {customer}, {billing_email}, {order_total} en {items} worden vervangen door echte bestelgegevens."
      },
      {
        "question": "Kan ik tekst opmaken?",
        "answer": "Ja. De WordPress-editor ondersteunt alinea’s, vet, cursief, lijsten en links. Onveilige HTML wordt verwijderd."
      },
      {
        "question": "Interne notitie of klantnotitie?",
        "answer": "Interne notities zijn voor het winkelteam. Klantnotities kunnen zichtbaar zijn voor de klant en WooCommerce-e-mails activeren."
      },
      {
        "question": "Waarom is er een waarschuwing bij klantnotities?",
        "answer": "De waarschuwing herinnert eraan persoonlijke gegevens, placeholders en tekst te controleren voordat een notitie de klant kan bereiken."
      },
      {
        "question": "Waarvoor dienen favorieten, zoeken en sorteren?",
        "answer": "Favorieten maken belangrijke sjablonen snel bereikbaar, zoeken filtert lange lijsten en slepen bepaalt de volgorde."
      },
      {
        "question": "Hoe werkt JSON import/export?",
        "answer": "Export maakt een JSON-bestand met sjablonen en gegevens. Import kan deze herstellen of naar een andere winkel verplaatsen."
      },
      {
        "question": "Zijn demosjablonen vertaald?",
        "answer": "Ja. Ze worden gemaakt in de huidige beheertaal wanneer die taal is meegeleverd."
      },
      {
        "question": "Is de plugin HPOS-compatibel?",
        "answer": "Ja. De plugin gebruikt WooCommerce order-API’s en verklaart HPOS-compatibiliteit."
      },
      {
        "question": "Voorbeeld controleren",
        "answer": "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken. Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken. Use clear titles and categories."
      },
      {
        "question": "E-mailstatus klantnotitie",
        "answer": "Bij een klantnotitie toont de plugin een waarschuwing en probeert te tonen of de WooCommerce-mail voor klantnotities actief is. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Sjabloontaal en meertalige winkels",
        "answer": "Elke sjabloon kan een taal krijgen. Kies <strong>Alle talen</strong> voor algemene teksten of een specifieke taal voor vertaalde klantberichten. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Aangepaste velden en metadata",
        "answer": "De placeholders <code>{order_meta:meta_key}</code> en <code>{customer_meta:meta_key}</code> voegen geselecteerde metadata in. Gevoelige sleutels zoals password, token of secret worden geblokkeerd. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Interne notities en klantnotities",
        "answer": "Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken. Deze hulp legt de volledige workflow uit: opgemaakte sjablonen maken, placeholders gebruiken, notities toevoegen aan WooCommerce-bestellingen, JSON importeren/exporteren, rechten beheren, HPOS gebruiken en veilig werken. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Instellingenpagina",
        "answer": "Open <strong>Mailhilfe Order Notes → Instellingen</strong> om het standaardgedrag van de plugin te bepalen. Je kunt het standaardnotitietype kiezen, HTML toestaan, waarschuwingen voor klantnotities, gebruikstellers en JSON-import beheren. Gebruik interne notities als standaard voor veiliger dagelijks werk. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Importvoorbeeld",
        "answer": "JSON-import toont eerst een voorbeeld met gemaakte, bijgewerkte of overgeslagen sjablonen. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Dupliceren en revisies",
        "answer": "Dupliceren maakt een kopie als concept. WordPress-revisies helpen eerdere versies te vergelijken en te herstellen. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Rechten",
        "answer": "Op de pagina <strong>Rechten</strong> bepaal je welke rollen sjablonen beheren en welke ze in bestellingen gebruiken. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Aanbevolen werkwijze",
        "answer": "Kies een sjabloon, controleer de vervangen preview, bewerk indien nodig, controleer het notitietype en voeg de notitie toe. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Kan ik de plugin in een stagingwinkel gebruiken?",
        "answer": "Ja. Gebruik JSON-import/export en een testbestelling om sjablonen, voorwaarden, placeholders en e-mailinstellingen te controleren vóór productie."
      },
      {
        "question": "Welke gegevens worden bij verwijderen gewist?",
        "answer": "Sjablonen, categorieën, instellingen, de geschiedenistabel, persoonlijke favorieten en recente sjablonen worden verwijderd. Bestaande WooCommerce-orderregels blijven in de ordergeschiedenis."
      },
      {
        "question": "Hoe meld ik een probleem?",
        "answer": "Vermeld WordPress-, WooCommerce-, PHP- en pluginversie, HPOS-status, taal, exacte stappen en foutmelding. Voeg diagnosewaarden toe zonder persoonlijke klantgegevens."
      },
      {
        "question": "Voorwaarden voor sjablonen",
        "answer": "Voorwaarden bepalen of een sjabloon voor een bestelling beschikbaar is. Je kunt beperken op bestelstatus, betaalmethode, verzendmethode, factuurland en minimum- of maximumbedrag. Alle ingevulde voorwaarden moeten overeenkomen. Laat een veld leeg als het sjabloon daarop niet beperkt moet worden."
      },
      {
        "question": "Logboek voor e-mailverwerking",
        "answer": "Bij klantnotities registreert de plugin wanneer WooCommerce meldt dat de e-mail is verwerkt en ook technische wp_mail-fouten. “Verwerkt” bevestigt overdracht aan het mailsysteem, niet de uiteindelijke bezorging of het lezen. Bekijk de pagina Geschiedenis voor verwerkte en mislukte gebeurtenissen."
      },
      {
        "question": "Centrale geschiedenis",
        "answer": "Open <strong>Mailhilfe Order Notes → Geschiedenis</strong> voor gemaakte notities, sjabloongebruik, e-mailverwerking en fouten. Indien beschikbaar worden bestelling, sjabloon, gebruiker, ontvanger, gebeurtenistype en tijd weergegeven. Gebruik de geschiedenis voor ondersteuning, controle en probleemoplossing."
      },
      {
        "question": "Voorbeeld met testbestelling",
        "answer": "Voer in de sjablooneditor een WooCommerce-bestel-ID in. De huidige inhoud, ook niet-opgeslagen wijzigingen, wordt met bestelgegevens weergegeven zonder een notitie te maken of e-mail te sturen. Gebruik een testbestelling of stagingomgeving."
      },
      {
        "question": "Persoonlijke favorieten en recente sjablonen",
        "answer": "Elke gebruiker kan persoonlijke favorieten markeren in de bestelling. De plugin bewaart ook de tien laatst succesvol gebruikte sjablonen per gebruiker en zet ze hoger. Globale favorieten blijven gedeeld. Persoonlijke favorieten beïnvloeden andere gebruikers niet."
      },
      {
        "question": "Diagnosepagina",
        "answer": "Open <strong>Mailhilfe Order Notes → Diagnose</strong> voor WordPress-, PHP- en WooCommerce-versies, HPOS-status, klantnotitie-e-mail, taal, aantal gepubliceerde sjablonen, cache en WP_DEBUG. Vermeld deze gegevens bij een supportvraag."
      },
      {
        "question": "Hooks en filters voor ontwikkelaars",
        "answer": "Er zijn hooks en filters voor placeholders, waarden, toegestane metasleutels, sjabloonresultaten, voorwaarden, voorbeeld, definitieve inhoud, acties vóór/na toevoegen, geschiedenis en diagnose. Namen staan in readme.txt. Valideer, sanitize en escape alle aangepaste gegevens."
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
        "answer": "Wtyczka tworzy wielokrotnego użytku szablony notatek zamówień WooCommerce. Pracownik może wybrać szablon w zamówieniu, sprawdzić podgląd i dodać wynik jako notatkę wewnętrzną lub dla klienta."
      },
      {
        "question": "Gdzie zarządza się szablonami?",
        "answer": "Otwórz Szablony notatek zamówień w panelu WordPress. Możesz tworzyć, edytować, usuwać, kategoryzować, oznaczać jako ulubione i sortować szablony."
      },
      {
        "question": "Jak działają symbole zastępcze?",
        "answer": "Symbole takie jak {order_number}, {customer}, {billing_email}, {order_total} i {items} są zastępowane rzeczywistymi danymi zamówienia."
      },
      {
        "question": "Czy można formatować tekst?",
        "answer": "Tak. Edytor WordPress obsługuje akapity, pogrubienie, kursywę, listy i linki. Niebezpieczny HTML jest usuwany."
      },
      {
        "question": "Notatka wewnętrzna czy dla klienta?",
        "answer": "Notatki wewnętrzne są dla zespołu sklepu. Notatki dla klienta mogą być widoczne dla klienta i uruchamiać e-maile WooCommerce."
      },
      {
        "question": "Dlaczego jest ostrzeżenie przy notatkach dla klienta?",
        "answer": "Ostrzeżenie przypomina o sprawdzeniu danych osobowych, symboli zastępczych i treści, zanim notatka trafi do klienta."
      },
      {
        "question": "Do czego służą ulubione, wyszukiwanie i sortowanie?",
        "answer": "Ulubione ułatwiają dostęp do ważnych szablonów, wyszukiwanie filtruje długie listy, a przeciąganie ustala kolejność."
      },
      {
        "question": "Jak działa import/eksport JSON?",
        "answer": "Eksport tworzy plik JSON z szablonami i danymi. Import pozwala je przywrócić lub przenieść do innego sklepu."
      },
      {
        "question": "Czy szablony demo są tłumaczone?",
        "answer": "Tak. Są tworzone w bieżącym języku administracji, jeśli ten język jest dołączony do wtyczki."
      },
      {
        "question": "Czy wtyczka jest zgodna z HPOS?",
        "answer": "Tak. Wtyczka używa API zamówień WooCommerce i deklaruje zgodność z HPOS."
      },
      {
        "question": "Sprawdzenie podglądu",
        "answer": "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę. Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę. Use clear titles and categories."
      },
      {
        "question": "Status e-maila notatki klienta",
        "answer": "Po wybraniu notatki klienta wtyczka pokazuje ostrzeżenie i próbuje wskazać, czy e-mail WooCommerce dla notatki klienta jest aktywny. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Język szablonu i sklepy wielojęzyczne",
        "answer": "Każdy szablon może mieć język. Wybierz <strong>Wszystkie języki</strong> dla tekstów ogólnych albo konkretny język dla przetłumaczonych wiadomości do klienta. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Pola własne i metadane",
        "answer": "Symbole <code>{order_meta:meta_key}</code> i <code>{customer_meta:meta_key}</code> wstawiają wybrane metadane. Wrażliwe klucze, takie jak password, token lub secret, są blokowane. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Notatki wewnętrzne i dla klienta",
        "answer": "Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę. Ta pomoc opisuje cały proces: tworzenie formatowanych szablonów, używanie symboli zastępczych, dodawanie notatek do zamówień WooCommerce, import/eksport JSON, uprawnienia, HPOS i bezpieczną pracę. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Strona ustawień",
        "answer": "Otwórz <strong>Mailhilfe Order Notes → Ustawienia</strong>, aby określić domyślne działanie wtyczki. Możesz wybrać domyślny typ notatki, zezwolić na HTML, kontrolować ostrzeżenia notatek klienta, liczniki użycia i import JSON. Dla bezpiecznej pracy używaj notatek wewnętrznych jako domyślnych. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Podgląd importu",
        "answer": "Import JSON pokazuje podgląd szablonów tworzonych, aktualizowanych lub pomijanych przed zastosowaniem zmian. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Duplikowanie i wersje",
        "answer": "Duplikowanie tworzy kopię jako szkic. Wersje WordPress pomagają porównać i przywrócić wcześniejsze treści. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Uprawnienia",
        "answer": "Strona <strong>Uprawnienia</strong> określa, które role zarządzają szablonami, a które używają ich w zamówieniach. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Zalecany przebieg pracy",
        "answer": "Wybierz szablon, sprawdź podgląd, edytuj w razie potrzeby, potwierdź typ notatki i dodaj notatkę. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Czy mogę używać wtyczki w sklepie staging?",
        "answer": "Tak. Użyj eksportu/importu JSON i zamówienia testowego, aby sprawdzić szablony, warunki, symbole zastępcze i e-maile przed wdrożeniem."
      },
      {
        "question": "Jakie dane są usuwane podczas odinstalowania?",
        "answer": "Usuwane są szablony, kategorie, ustawienia, tabela historii, osobiste ulubione i ostatnie szablony. Istniejące notatki WooCommerce pozostają w historii zamówień."
      },
      {
        "question": "Jak zgłosić problem?",
        "answer": "Podaj wersje WordPress, WooCommerce, PHP i wtyczki, stan HPOS, język, dokładne kroki i błąd. Dołącz dane diagnostyczne bez danych osobowych klientów."
      },
      {
        "question": "Warunki szablonów",
        "answer": "Warunki określają, czy szablon jest dostępny dla zamówienia. Można ograniczyć go według statusu, metody płatności, metody wysyłki, kraju rozliczeniowego oraz minimalnej lub maksymalnej wartości. Wszystkie ustawione warunki muszą być spełnione. Pozostaw pole puste, jeśli nie ma ograniczać szablonu."
      },
      {
        "question": "Rejestr przetwarzania e-maili",
        "answer": "Dla notatek klienta wtyczka zapisuje, kiedy WooCommerce zgłasza przetworzenie wiadomości, oraz błędy techniczne wp_mail. „Przetworzono” oznacza przekazanie do systemu pocztowego, a nie ostateczne doręczenie lub odczytanie. Sprawdź stronę Historia, aby zobaczyć zdarzenia udane i nieudane."
      },
      {
        "question": "Centralna historia",
        "answer": "Otwórz <strong>Mailhilfe Order Notes → Historia</strong>, aby przeglądać utworzone notatki, użycie szablonów, przetwarzanie i błędy e-mail. Gdy są dostępne, widoczne są zamówienie, szablon, użytkownik, odbiorca, typ zdarzenia i czas. Historia pomaga w obsłudze, audycie i diagnostyce."
      },
      {
        "question": "Podgląd z zamówieniem testowym",
        "answer": "W edytorze wpisz identyfikator zamówienia WooCommerce. Bieżąca treść, także niezapisana, zostanie wyświetlona z danymi zamówienia bez tworzenia notatki i wysyłania e-maila. Używaj zamówienia testowego lub środowiska staging."
      },
      {
        "question": "Osobiste ulubione i ostatnio używane szablony",
        "answer": "Każdy użytkownik może oznaczać osobiste ulubione w zamówieniu. Wtyczka zapisuje również dziesięć ostatnio poprawnie użytych szablonów i umieszcza je wyżej. Ulubione globalne pozostają wspólne. Osobiste ulubione nie wpływają na innych użytkowników."
      },
      {
        "question": "Strona diagnostyczna",
        "answer": "Otwórz <strong>Mailhilfe Order Notes → Diagnostyka</strong>, aby zobaczyć wersje WordPress, PHP i WooCommerce, status HPOS, e-mail notatki klienta, język, liczbę szablonów, pamięć podręczną i WP_DEBUG. Dołącz te dane do zgłoszenia pomocy."
      },
      {
        "question": "Hooki i filtry dla programistów",
        "answer": "Dostępne są hooki i filtry dla symboli zastępczych, wartości, dozwolonych kluczy meta, wyników szablonów, warunków, podglądu, treści końcowej, działań przed/po dodaniu, historii i diagnostyki. Nazwy opisano w readme.txt. Waliduj, oczyszczaj i escapuj własne dane."
      }
    ]
  },
  "ru_RU": {
    "menu": "FAQ",
    "title": "Часто задаваемые вопросы",
    "intro": "Ответы на частые вопросы о Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "У вас нет прав для управления шаблонами заметок.",
    "items": [
      {
        "question": "Для чего нужен плагин?",
        "answer": "Он создает повторно используемые шаблоны заметок WooCommerce. Сотрудник выбирает шаблон в заказе, проверяет предварительный просмотр и добавляет внутреннюю заметку или заметку для клиента."
      },
      {
        "question": "Где управлять шаблонами?",
        "answer": "В меню шаблонов заметок можно создавать, изменять, удалять, распределять по категориям, отмечать избранные и сортировать шаблоны."
      },
      {
        "question": "Как работают заполнители?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} и {items} заменяются реальными данными заказа."
      },
      {
        "question": "Можно ли форматировать текст?",
        "answer": "Да. Поддерживаются абзацы, жирный и курсивный текст, списки и ссылки. Небезопасный HTML удаляется."
      },
      {
        "question": "Внутренняя заметка или клиентская?",
        "answer": "Внутренние заметки предназначены для команды магазина. Клиентские заметки могут быть видны покупателю и отправляться по электронной почте WooCommerce."
      },
      {
        "question": "Совместим ли HPOS?",
        "answer": "Да. Используются API заказов WooCommerce и объявлена совместимость HPOS."
      },
      {
        "question": "Как работает JSON?",
        "answer": "Экспорт создает файл JSON, импорт восстанавливает или переносит шаблоны."
      },
      {
        "question": "Есть ли публичные ссылки?",
        "answer": "Нет. Плагин не добавляет публичные ссылки на витрину."
      },
      {
        "question": "Проверка предварительного просмотра",
        "answer": "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу. Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу. Use clear titles and categories."
      },
      {
        "question": "Статус письма заметки клиенту",
        "answer": "При выборе заметки клиенту плагин показывает предупреждение и пытается определить, активно ли письмо WooCommerce для заметок клиенту. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Язык шаблона и многоязычные магазины",
        "answer": "Каждому шаблону можно назначить язык. Выберите <strong>Все языки</strong> для универсального текста или конкретный язык для переведенных сообщений клиенту. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Пользовательские поля и метаданные",
        "answer": "Заполнители <code>{order_meta:meta_key}</code> и <code>{customer_meta:meta_key}</code> вставляют выбранные метаданные. Чувствительные ключи, такие как password, token или secret, блокируются. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Внутренние и клиентские заметки",
        "answer": "Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу. Эта справка описывает полный процесс: создание форматированных шаблонов, использование заполнителей, добавление заметок в заказы WooCommerce, импорт/экспорт JSON, права, HPOS и безопасную работу. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Страница настроек",
        "answer": "Откройте <strong>Mailhilfe Order Notes → Настройки</strong>, чтобы задать поведение плагина по умолчанию. Можно выбрать тип заметки, разрешить HTML, управлять предупреждениями для заметок клиенту, счетчиками использования и импортом JSON. Для безопасной работы используйте внутренние заметки как тип по умолчанию. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Предпросмотр импорта",
        "answer": "Импорт JSON сначала показывает, какие шаблоны будут созданы, обновлены или пропущены. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Дублирование и редакции",
        "answer": "Дублирование создает копию как черновик. Редакции WordPress помогают сравнивать и восстанавливать предыдущие версии. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Разрешения",
        "answer": "Страница <strong>Разрешения</strong> определяет, какие роли управляют шаблонами и какие используют их в заказах. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Рекомендуемый порядок",
        "answer": "Выберите шаблон, проверьте предпросмотр, отредактируйте при необходимости, подтвердите тип заметки и добавьте ее. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Можно ли использовать плагин на staging-сайте?",
        "answer": "Да. Используйте JSON-экспорт/импорт и тестовый заказ, чтобы проверить шаблоны, условия, заполнители и почту до переноса в рабочий магазин."
      },
      {
        "question": "Какие данные удаляются при деинсталляции?",
        "answer": "Удаляются шаблоны, категории, настройки, таблица истории, личное избранное и недавние шаблоны. Уже созданные заметки WooCommerce остаются в истории заказов."
      },
      {
        "question": "Как сообщить о проблеме?",
        "answer": "Укажите версии WordPress, WooCommerce, PHP и плагина, HPOS, язык, точные шаги и ошибку. Добавьте диагностику без персональных данных клиентов."
      },
      {
        "question": "Условия шаблонов",
        "answer": "Условия определяют, доступен ли шаблон для конкретного заказа. Ограничения можно задать по статусу, способу оплаты, способу доставки, стране выставления счёта и минимальной или максимальной сумме. Все заполненные условия должны совпасть. Оставьте поле пустым, если оно не должно ограничивать шаблон."
      },
      {
        "question": "Журнал обработки электронной почты",
        "answer": "Для клиентских заметок плагин записывает сообщение WooCommerce об обработке письма и технические ошибки wp_mail. Статус «обработано» подтверждает передачу почтовой системе, но не окончательную доставку и не прочтение. Смотрите обработанные и неудачные события на странице истории."
      },
      {
        "question": "Центральная история",
        "answer": "Откройте <strong>Mailhilfe Order Notes → История</strong>, чтобы увидеть создание заметок, использование шаблонов, обработку и ошибки писем. При наличии отображаются заказ, шаблон, пользователь, получатель, тип события и время. Используйте историю для поддержки, аудита и диагностики."
      },
      {
        "question": "Предпросмотр с тестовым заказом",
        "answer": "В редакторе укажите ID заказа WooCommerce. Текущий текст, включая несохранённые изменения, будет показан с данными заказа без создания заметки и отправки письма. Используйте тестовый заказ или staging-сайт."
      },
      {
        "question": "Личное избранное и недавние шаблоны",
        "answer": "Каждый пользователь может отметить личные избранные шаблоны в заказе. Плагин также хранит десять последних успешно использованных шаблонов и поднимает их выше. Глобальное избранное остаётся общим. Личное избранное не влияет на других пользователей."
      },
      {
        "question": "Страница диагностики",
        "answer": "Откройте <strong>Mailhilfe Order Notes → Диагностика</strong>, чтобы увидеть версии WordPress, PHP и WooCommerce, статус HPOS, состояние письма клиентской заметки, язык, число шаблонов, кэш и WP_DEBUG. Указывайте эти данные при обращении в поддержку."
      },
      {
        "question": "Хуки и фильтры для разработчиков",
        "answer": "Плагин предоставляет хуки и фильтры для заполнителей, значений, разрешённых мета-ключей, результатов шаблонов, условий, предпросмотра, итогового текста, действий до/после добавления, истории и диагностики. Имена описаны в readme.txt. Проверяйте, очищайте и экранируйте пользовательские данные."
      }
    ]
  },
  "zh_CN": {
    "menu": "FAQ",
    "title": "常见问题",
    "intro": "关于 Mailhilfe Order Note Manager for WooCommerce 的常见问题解答。",
    "permission": "您无权管理备注模板。",
    "items": [
      {
        "question": "插件有什么用途？",
        "answer": "它创建可重复使用的 WooCommerce 订单备注模板。员工可在订单中选择模板，查看预览，并添加为内部备注或客户备注。"
      },
      {
        "question": "在哪里管理模板？",
        "answer": "在订单备注模板菜单中可以创建、编辑、删除、分类、收藏并排序模板。"
      },
      {
        "question": "占位符如何工作？",
        "answer": "{order_number}、{customer}、{billing_email}、{order_total} 和 {items} 会替换为真实订单数据。"
      },
      {
        "question": "文本可以格式化吗？",
        "answer": "可以。支持段落、粗体、斜体、列表和链接，不安全的 HTML 会被移除。"
      },
      {
        "question": "内部备注和客户备注有什么区别？",
        "answer": "内部备注仅供店铺团队使用。客户备注可能对客户可见，并可能触发 WooCommerce 邮件。"
      },
      {
        "question": "是否兼容 HPOS？",
        "answer": "是。插件使用 WooCommerce 订单 API，并声明 HPOS 兼容。"
      },
      {
        "question": "JSON 导入导出如何工作？",
        "answer": "导出会创建 JSON 文件，导入可恢复模板或转移到其他商店。"
      },
      {
        "question": "会添加公共链接吗？",
        "answer": "不会。插件不会在前台添加 powered-by 或推广链接。"
      },
      {
        "question": "检查预览",
        "answer": "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。 本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。 Use clear titles and categories."
      },
      {
        "question": "客户备注邮件状态",
        "answer": "选择客户备注时，插件会显示警告，并尝试显示 WooCommerce 客户备注邮件是否启用。 If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "模板语言和多语言商店",
        "answer": "每个模板都可以设置语言。选择 <strong>所有语言</strong> 表示通用文本，或选择具体语言用于已翻译的客户消息。 For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "自定义字段和元数据",
        "answer": "占位符 <code>{order_meta:meta_key}</code> 和 <code>{customer_meta:meta_key}</code> 可插入选定的元数据。password、token、secret 等敏感键会被阻止。 Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "内部备注和客户备注",
        "answer": "本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。 本帮助说明完整流程：创建带格式的模板、使用占位符、在 WooCommerce 订单中添加备注、导入/导出 JSON、管理权限、使用 HPOS 并安全操作。 <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "设置页面",
        "answer": "打开 <strong>Mailhilfe Order Notes → 设置</strong> 来定义插件的默认行为。您可以选择默认备注类型、是否允许 HTML、控制客户备注警告、使用计数器和 JSON 导入。 为了日常操作更安全，建议默认使用内部备注。 Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "导入预览",
        "answer": "JSON 导入会先显示将创建、更新或跳过的模板数量。 Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "复制和修订",
        "answer": "复制操作会创建草稿副本。WordPress 修订可帮助比较并恢复旧版本。 Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "权限",
        "answer": "<strong>权限</strong> 页面用于设置哪些角色可以管理模板，哪些角色可以在订单中使用模板。 Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "推荐流程",
        "answer": "选择模板，检查替换后的预览，需要时编辑，确认备注类型，然后添加备注。 For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "可以在预发布商店中使用插件吗？",
        "answer": "可以。上线前请使用 JSON 导入/导出和测试订单检查模板、条件、占位符及邮件设置。"
      },
      {
        "question": "卸载时会删除哪些数据？",
        "answer": "会删除模板、分类、设置、历史记录表、个人收藏和最近模板。已经添加的 WooCommerce 订单备注仍保留在订单历史中。"
      },
      {
        "question": "应如何报告问题？",
        "answer": "请提供 WordPress、WooCommerce、PHP 和插件版本、HPOS 状态、语言、完整步骤和错误信息。可附诊断值，但不要包含客户个人数据。"
      },
      {
        "question": "模板条件",
        "answer": "模板条件决定某个模板是否可用于订单。可按订单状态、付款方式、配送方式、账单国家以及最低或最高订单金额进行限制。所有已设置的条件都必须匹配。 不需要限制的条件请留空。"
      },
      {
        "question": "邮件处理日志",
        "answer": "对于客户备注，插件会记录 WooCommerce 报告的邮件处理事件以及 wp_mail 技术错误。“已处理”只表示邮件已交给邮件系统，并不证明最终送达或客户已阅读。 在“历史记录”页面查看已处理和失败事件。"
      },
      {
        "question": "集中历史记录",
        "answer": "打开 <strong>Mailhilfe Order Notes → 历史记录</strong>，可查看备注创建、模板使用、邮件处理和邮件失败。可用时会显示订单、模板、用户、收件人、事件类型和时间。 可用于支持、审计和故障排除。"
      },
      {
        "question": "测试订单预览",
        "answer": "在模板编辑器中输入 WooCommerce 订单 ID。当前编辑内容（包括未保存的更改）会使用该订单数据生成预览，不会创建备注或发送邮件。 请使用测试订单或预发布环境。"
      },
      {
        "question": "个人收藏和最近使用的模板",
        "answer": "每位用户都可以在订单页面标记个人收藏。插件还会为每个用户保存最近成功使用的十个模板并优先显示。全局收藏仍对所有用户共享。 个人收藏不会影响其他用户。"
      },
      {
        "question": "诊断页面",
        "answer": "打开 <strong>Mailhilfe Order Notes → 诊断</strong>，可查看 WordPress、PHP 和 WooCommerce 版本、HPOS 状态、客户备注邮件状态、语言、已发布模板数量、缓存和 WP_DEBUG。 请求支持时请提供这些信息。"
      },
      {
        "question": "开发者钩子和过滤器",
        "answer": "插件为占位符、值、允许的元键、模板结果、条件、预览、最终备注内容、添加前后操作、历史记录和诊断提供钩子与过滤器。名称和参数记录在 readme.txt 中。 验证、清理并转义所有自定义数据。"
      }
    ]
  },
  "ja": {
    "menu": "FAQ",
    "title": "よくある質問",
    "intro": "Mailhilfe Order Note Manager for WooCommerce に関するよくある質問への回答です。",
    "permission": "メモテンプレートを管理する権限がありません。",
    "items": [
      {
        "question": "何に使うプラグインですか？",
        "answer": "WooCommerce 注文メモの再利用可能なテンプレートを作成します。スタッフは注文内でテンプレートを選び、プレビューを確認し、内部メモまたは顧客メモとして追加できます。"
      },
      {
        "question": "テンプレートはどこで管理しますか？",
        "answer": "注文メモテンプレートのメニューで、作成、編集、削除、カテゴリ設定、お気に入り、並べ替えができます。"
      },
      {
        "question": "プレースホルダーはどう働きますか？",
        "answer": "{order_number}、{customer}、{billing_email}、{order_total}、{items} は実際の注文データに置き換えられます。"
      },
      {
        "question": "テキストは装飾できますか？",
        "answer": "はい。段落、太字、斜体、リスト、リンクを使用できます。安全でない HTML は削除されます。"
      },
      {
        "question": "内部メモと顧客メモの違いは？",
        "answer": "内部メモはショップチーム向けです。顧客メモは顧客に表示され、WooCommerce メールを送る場合があります。"
      },
      {
        "question": "HPOS に対応していますか？",
        "answer": "はい。WooCommerce 注文 API を使用し、HPOS 互換を宣言しています。"
      },
      {
        "question": "JSON はどう使いますか？",
        "answer": "エクスポートで JSON ファイルを作成し、インポートで復元または別ショップへ移行できます。"
      },
      {
        "question": "公開リンクは追加されますか？",
        "answer": "いいえ。ストアフロントに powered-by などの公開リンクは追加しません。"
      },
      {
        "question": "プレビューを確認する",
        "answer": "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。 このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。 Use clear titles and categories."
      },
      {
        "question": "顧客メモメールの状態",
        "answer": "顧客メモを選択すると警告が表示され、WooCommerce の顧客メモメールが有効かどうかを表示しようとします。 If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "テンプレート言語と多言語ショップ",
        "answer": "各テンプレートに言語を設定できます。共通テキストは <strong>すべての言語</strong>、翻訳済みの顧客向け文面は特定の言語を選びます。 For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "カスタムフィールドとメタデータ",
        "answer": "<code>{order_meta:meta_key}</code> と <code>{customer_meta:meta_key}</code> は選択したメタデータを挿入します。password、token、secret などの機密キーはブロックされます。 Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "内部メモと顧客メモ",
        "answer": "このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。 このヘルプでは、書式付きテンプレートの作成、プレースホルダーの使用、WooCommerce 注文へのメモ追加、JSON のインポート/エクスポート、権限、HPOS、安全な運用まで説明します。 <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "設定ページ",
        "answer": "<strong>Mailhilfe Order Notes → 設定</strong> を開き、プラグインの既定動作を設定します。既定のメモ種類、HTML の許可、顧客メモ警告、使用回数、JSON インポートを管理できます。 安全な日常運用には内部メモを既定にすることを推奨します。 Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "インポートプレビュー",
        "answer": "JSON インポートでは、適用前に作成・更新・スキップされるテンプレート数を確認できます。 Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "複製とリビジョン",
        "answer": "複製は下書きコピーを作成します。WordPress のリビジョンで過去の内容を比較・復元できます。 Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "権限",
        "answer": "<strong>権限</strong> ページで、どの権限グループがテンプレートを管理または注文で使用できるかを設定します。 Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "推奨ワークフロー",
        "answer": "テンプレートを選択し、置換後のプレビューを確認し、必要に応じて編集し、メモ種類を確認してから追加します。 For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "ステージングショップで使用できますか？",
        "answer": "はい。JSON のインポート/エクスポートとテスト注文で、テンプレート、条件、プレースホルダー、メール設定を本番前に確認してください。"
      },
      {
        "question": "アンインストール時に何が削除されますか？",
        "answer": "テンプレート、カテゴリー、設定、履歴テーブル、個人のお気に入り、最近のテンプレートが削除されます。既存の WooCommerce 注文メモは注文履歴に残ります。"
      },
      {
        "question": "問題はどのように報告すればよいですか？",
        "answer": "WordPress、WooCommerce、PHP、プラグインのバージョン、HPOS、言語、手順、エラーを記載してください。診断値を添え、顧客の個人情報は含めないでください。"
      },
      {
        "question": "テンプレート条件",
        "answer": "条件はテンプレートを注文で利用できるか決定します。注文ステータス、支払方法、配送方法、請求先国、最小・最大注文金額で制限できます。設定した条件はすべて一致する必要があります。 制限しない項目は空欄にします。"
      },
      {
        "question": "メール処理ログ",
        "answer": "顧客メモでは、WooCommerce がメールを処理したイベントと wp_mail の技術的エラーを記録します。「処理済み」はメールシステムへの引き渡しを示すだけで、最終配信や既読を保証しません。 履歴ページで処理済み・失敗イベントを確認します。"
      },
      {
        "question": "一元化された履歴",
        "answer": "<strong>Mailhilfe Order Notes → 履歴</strong>で、メモ作成、テンプレート利用、メール処理、メール失敗を確認できます。利用可能な場合は注文、テンプレート、ユーザー、受信者、イベント種別、時刻が表示されます。 サポート、監査、トラブルシューティングに利用できます。"
      },
      {
        "question": "テスト注文プレビュー",
        "answer": "テンプレートエディターに WooCommerce 注文 ID を入力すると、未保存の変更を含む現在の内容を注文データでプレビューできます。メモ作成やメール送信は行いません。 テスト注文またはステージング環境を使用します。"
      },
      {
        "question": "個人のお気に入りと最近使用したテンプレート",
        "answer": "各ユーザーは注文画面で個人のお気に入りを設定できます。また、正常に使用した最新 10 件をユーザーごとに保存して上位に表示します。グローバルなお気に入りは全員で共有されます。 個人のお気に入りは他のユーザーに影響しません。"
      },
      {
        "question": "診断ページ",
        "answer": "<strong>Mailhilfe Order Notes → 診断</strong>で WordPress、PHP、WooCommerce のバージョン、HPOS、顧客メモメール、言語、テンプレート数、キャッシュ、WP_DEBUG を確認できます。 サポート依頼時にこれらの値を提供してください。"
      },
      {
        "question": "開発者向けフックとフィルター",
        "answer": "プレースホルダー、値、許可メタキー、テンプレート結果、条件、プレビュー、最終内容、追加前後、履歴、診断を拡張するフックとフィルターがあります。名前と引数は readme.txt に記載されています。 独自データは検証、サニタイズ、エスケープしてください。"
      }
    ]
  },
  "ko_KR": {
    "menu": "FAQ",
    "title": "자주 묻는 질문",
    "intro": "Mailhilfe Order Note Manager for WooCommerce에 대한 일반적인 질문과 답변입니다.",
    "permission": "메모 템플릿을 관리할 권한이 없습니다。",
    "items": [
      {
        "question": "무엇에 사용하는 플러그인인가요?",
        "answer": "WooCommerce 주문 메모용 재사용 템플릿을 만듭니다. 직원은 주문에서 템플릿을 선택하고 미리 보기를 확인한 뒤 내부 메모 또는 고객 메모로 추가할 수 있습니다."
      },
      {
        "question": "템플릿은 어디에서 관리하나요?",
        "answer": "주문 메모 템플릿 메뉴에서 생성, 편집, 삭제, 분류, 즐겨찾기 및 정렬을 할 수 있습니다."
      },
      {
        "question": "자리표시는 어떻게 작동하나요?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total}, {items}는 실제 주문 데이터로 대체됩니다."
      },
      {
        "question": "텍스트 서식이 가능한가요?",
        "answer": "예. 단락, 굵게, 기울임, 목록, 링크를 지원하며 안전하지 않은 HTML은 제거됩니다."
      },
      {
        "question": "내부 메모와 고객 메모의 차이는?",
        "answer": "내부 메모는 쇼핑몰 팀용입니다. 고객 메모는 고객에게 보일 수 있으며 WooCommerce 이메일을 보낼 수 있습니다."
      },
      {
        "question": "HPOS와 호환되나요?",
        "answer": "예. WooCommerce 주문 API를 사용하며 HPOS 호환성을 선언합니다."
      },
      {
        "question": "JSON은 어떻게 사용하나요?",
        "answer": "내보내기는 JSON 파일을 만들고 가져오기는 템플릿을 복원하거나 다른 쇼핑몰로 이전합니다."
      },
      {
        "question": "공개 링크를 추가하나요?",
        "answer": "아니요. 프런트엔드에 powered-by 또는 홍보 링크를 추가하지 않습니다."
      },
      {
        "question": "미리보기 확인",
        "answer": "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다. 이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다. Use clear titles and categories."
      },
      {
        "question": "고객 메모 이메일 상태",
        "answer": "고객 메모를 선택하면 경고가 표시되고 WooCommerce 고객 메모 이메일이 활성화되어 있는지 표시하려고 합니다. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "템플릿 언어와 다국어 상점",
        "answer": "각 템플릿에 언어를 지정할 수 있습니다. 공통 문구는 <strong>모든 언어</strong>, 번역된 고객 메시지는 특정 언어를 선택합니다. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "사용자 정의 필드와 메타데이터",
        "answer": "<code>{order_meta:meta_key}</code> 및 <code>{customer_meta:meta_key}</code>는 선택한 메타데이터를 삽입합니다. password, token, secret 같은 민감한 키는 차단됩니다. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "내부 메모와 고객 메모",
        "answer": "이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다. 이 도움말은 서식 있는 템플릿 생성, 자리표시자 사용, WooCommerce 주문에 메모 추가, JSON 가져오기/내보내기, 권한, HPOS 및 안전한 사용 방법을 설명합니다. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "설정 페이지",
        "answer": "<strong>Mailhilfe Order Notes → 설정</strong>을 열어 플러그인의 기본 동작을 지정합니다. 기본 메모 유형, HTML 허용, 고객 메모 경고, 사용 횟수 및 JSON 가져오기를 관리할 수 있습니다. 안전한 일상 작업을 위해 내부 메모를 기본값으로 사용하는 것이 좋습니다. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "가져오기 미리보기",
        "answer": "JSON 가져오기는 적용 전에 생성, 업데이트 또는 건너뛸 템플릿 수를 보여줍니다. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "복제와 리비전",
        "answer": "복제는 초안 복사본을 만듭니다. WordPress 리비전으로 이전 버전을 비교하고 복원할 수 있습니다. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "권한",
        "answer": "<strong>권한</strong> 페이지에서 어떤 역할이 템플릿을 관리하거나 주문에서 사용할 수 있는지 정합니다. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "권장 절차",
        "answer": "템플릿을 선택하고 치환된 미리보기를 확인한 뒤 필요하면 수정하고 메모 유형을 확인한 후 추가합니다. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "스테이징 상점에서 사용할 수 있나요?",
        "answer": "예. JSON 가져오기/내보내기와 테스트 주문으로 템플릿, 조건, 플레이스홀더, 이메일 설정을 운영 전 확인하세요."
      },
      {
        "question": "제거 시 어떤 데이터가 삭제되나요?",
        "answer": "템플릿, 카테고리, 설정, 기록 테이블, 개인 즐겨찾기와 최근 템플릿이 삭제됩니다. 기존 WooCommerce 주문 메모는 주문 기록에 남습니다."
      },
      {
        "question": "문제를 어떻게 보고해야 하나요?",
        "answer": "WordPress, WooCommerce, PHP, 플러그인 버전, HPOS, 언어, 정확한 단계와 오류를 알려주세요. 진단 값을 포함하되 고객 개인정보는 제외하세요."
      },
      {
        "question": "템플릿 조건",
        "answer": "조건은 특정 주문에서 템플릿을 사용할 수 있는지 결정합니다. 주문 상태, 결제 방법, 배송 방법, 청구 국가, 최소 또는 최대 주문 금액으로 제한할 수 있으며 설정된 모든 조건이 일치해야 합니다. 제한하지 않을 조건은 비워 두세요."
      },
      {
        "question": "이메일 처리 기록",
        "answer": "고객 메모의 경우 WooCommerce가 이메일 처리를 보고한 이벤트와 wp_mail 기술 오류를 기록합니다. “처리됨”은 메일 시스템으로 전달되었다는 뜻이며 최종 배달이나 읽음을 보장하지 않습니다. 기록 페이지에서 처리 및 실패 이벤트를 확인하세요."
      },
      {
        "question": "중앙 기록",
        "answer": "<strong>Mailhilfe Order Notes → 기록</strong>에서 메모 생성, 템플릿 사용, 이메일 처리 및 실패를 확인할 수 있습니다. 가능한 경우 주문, 템플릿, 사용자, 수신자, 이벤트 종류와 시간이 표시됩니다. 지원, 감사 및 문제 해결에 사용하세요."
      },
      {
        "question": "테스트 주문 미리보기",
        "answer": "템플릿 편집기에 WooCommerce 주문 ID를 입력하면 저장하지 않은 변경을 포함한 현재 내용을 주문 데이터로 미리 볼 수 있습니다. 메모를 만들거나 이메일을 보내지 않습니다. 테스트 주문이나 스테이징 환경을 사용하세요."
      },
      {
        "question": "개인 즐겨찾기와 최근 사용 템플릿",
        "answer": "각 사용자는 주문 화면에서 개인 즐겨찾기를 설정할 수 있습니다. 또한 사용자별로 성공적으로 사용한 최근 10개 템플릿을 저장해 위에 표시합니다. 전역 즐겨찾기는 모두에게 공유됩니다. 개인 즐겨찾기는 다른 사용자에게 영향을 주지 않습니다."
      },
      {
        "question": "진단 페이지",
        "answer": "<strong>Mailhilfe Order Notes → 진단</strong>에서 WordPress, PHP, WooCommerce 버전, HPOS, 고객 메모 이메일, 언어, 템플릿 수, 캐시 및 WP_DEBUG를 확인할 수 있습니다. 지원 요청 시 이 정보를 제공하세요."
      },
      {
        "question": "개발자용 훅과 필터",
        "answer": "플레이스홀더, 값, 허용 메타 키, 템플릿 결과, 조건, 미리보기, 최종 내용, 추가 전후 작업, 기록 및 진단을 확장하는 훅과 필터를 제공합니다. 이름과 매개변수는 readme.txt에 문서화되어 있습니다. 사용자 정의 데이터는 검증, 정리 및 이스케이프하세요."
      }
    ]
  },
  "tr_TR": {
    "menu": "SSS",
    "title": "Sık sorulan sorular",
    "intro": "Mailhilfe Order Note Manager for WooCommerce hakkında sık sorulan soruların yanıtları.",
    "permission": "Not şablonlarını yönetme izniniz yok.",
    "items": [
      {
        "question": "Bu eklenti ne işe yarar?",
        "answer": "WooCommerce sipariş notları için yeniden kullanılabilir şablonlar oluşturur. Personel siparişte şablon seçer, önizlemeyi kontrol eder ve iç not veya müşteri notu olarak ekler."
      },
      {
        "question": "Şablonları nerede yönetirim?",
        "answer": "Sipariş notu şablonları menüsünde oluşturma, düzenleme, silme, kategori, favori ve sıralama işlemleri yapılır."
      },
      {
        "question": "Yer tutucular nasıl çalışır?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} ve {items} gerçek sipariş verileriyle değiştirilir."
      },
      {
        "question": "Metin biçimlendirilebilir mi?",
        "answer": "Evet. Paragraf, kalın, italik, liste ve bağlantılar desteklenir. Güvensiz HTML kaldırılır."
      },
      {
        "question": "Dahili not ve müşteri notu farkı nedir?",
        "answer": "Dahili notlar mağaza ekibi içindir. Müşteri notları müşteriye görünebilir ve WooCommerce e-postası gönderebilir."
      },
      {
        "question": "HPOS uyumlu mu?",
        "answer": "Evet. WooCommerce sipariş API’lerini kullanır ve HPOS uyumluluğu bildirir."
      },
      {
        "question": "JSON nasıl kullanılır?",
        "answer": "Dışa aktarma JSON dosyası oluşturur, içe aktarma şablonları geri yükler veya başka mağazaya taşır."
      },
      {
        "question": "Genel bağlantı ekler mi?",
        "answer": "Hayır. Mağaza ön yüzüne powered-by veya tanıtım bağlantısı eklemez."
      },
      {
        "question": "Önizlemeyi kontrol etme",
        "answer": "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar. Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar. Use clear titles and categories."
      },
      {
        "question": "Müşteri notu e-posta durumu",
        "answer": "Müşteri notu seçildiğinde eklenti bir uyarı gösterir ve WooCommerce müşteri notu e-postasının etkin olup olmadığını göstermeye çalışır. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Şablon dili ve çok dilli mağazalar",
        "answer": "Her şablona bir dil atanabilir. Genel metinler için <strong>Tüm diller</strong>, çevrilmiş müşteri mesajları için belirli bir dil seçin. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Özel alanlar ve meta veriler",
        "answer": "<code>{order_meta:meta_key}</code> ve <code>{customer_meta:meta_key}</code> seçili meta verileri ekler. password, token veya secret gibi hassas anahtarlar engellenir. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Dahili notlar ve müşteri notları",
        "answer": "Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar. Bu yardım, biçimlendirilmiş şablon oluşturma, yer tutucu kullanma, WooCommerce siparişlerine not ekleme, JSON içe/dışa aktarma, izinler, HPOS ve güvenli kullanım sürecini açıklar. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Ayarlar sayfası",
        "answer": "<strong>Mailhilfe Order Notes → Ayarlar</strong> bölümünü açarak eklentinin varsayılan davranışını belirleyin. Varsayılan not türünü, HTML iznini, müşteri notu uyarılarını, kullanım sayaçlarını ve JSON içe aktarmayı yönetebilirsiniz. Güvenli günlük kullanım için dahili notları varsayılan yapın. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "İçe aktarma önizlemesi",
        "answer": "JSON içe aktarma, değişikliklerden önce oluşturulacak, güncellenecek veya atlanacak şablonları gösterir. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Çoğaltma ve revizyonlar",
        "answer": "Çoğaltma işlemi taslak bir kopya oluşturur. WordPress revizyonları eski sürümleri karşılaştırıp geri yüklemeye yardımcı olur. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "İzinler",
        "answer": "<strong>İzinler</strong> sayfası hangi rollerin şablonları yöneteceğini ve siparişlerde kullanacağını belirler. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Önerilen iş akışı",
        "answer": "Şablonu seçin, değiştirilmiş önizlemeyi kontrol edin, gerekiyorsa düzenleyin, not türünü doğrulayın ve notu ekleyin. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Eklentiyi hazırlık mağazasında kullanabilir miyim?",
        "answer": "Evet. Canlıya almadan önce JSON içe/dışa aktarma ve test siparişiyle şablonları, koşulları, yer tutucuları ve e-postayı kontrol edin."
      },
      {
        "question": "Kaldırma sırasında hangi veriler silinir?",
        "answer": "Şablonlar, kategoriler, ayarlar, geçmiş tablosu, kişisel favoriler ve son şablonlar silinir. Mevcut WooCommerce sipariş notları sipariş geçmişinde kalır."
      },
      {
        "question": "Bir sorunu nasıl bildirmeliyim?",
        "answer": "WordPress, WooCommerce, PHP ve eklenti sürümlerini, HPOS durumunu, dili, adımları ve hatayı belirtin. Tanılama değerlerini ekleyin, müşteri kişisel verilerini eklemeyin."
      },
      {
        "question": "Şablon koşulları",
        "answer": "Koşullar bir şablonun siparişte kullanılabilir olup olmadığını belirler. Sipariş durumu, ödeme yöntemi, gönderim yöntemi, fatura ülkesi ve minimum veya maksimum toplam ile sınırlandırabilirsiniz. Girilen tüm koşullar eşleşmelidir. Kısıtlama uygulanmayacak alanı boş bırakın."
      },
      {
        "question": "E-posta işleme günlüğü",
        "answer": "Müşteri notlarında eklenti, WooCommerce’in e-postayı işlediğini bildirdiği olayları ve wp_mail teknik hatalarını kaydeder. “İşlendi” durumu posta sistemine aktarımı gösterir; nihai teslimatı veya okunmayı kanıtlamaz. İşlenen ve başarısız olaylar için Geçmiş sayfasını kontrol edin."
      },
      {
        "question": "Merkezi geçmiş",
        "answer": "<strong>Mailhilfe Order Notes → Geçmiş</strong> sayfasında not oluşturma, şablon kullanımı, e-posta işleme ve hataları görebilirsiniz. Varsa sipariş, şablon, kullanıcı, alıcı, olay türü ve zaman gösterilir. Destek, denetim ve sorun giderme için kullanın."
      },
      {
        "question": "Test siparişi önizlemesi",
        "answer": "Şablon düzenleyicisinde bir WooCommerce sipariş kimliği girin. Kaydedilmemiş değişiklikler dahil mevcut içerik, not oluşturmadan veya e-posta göndermeden sipariş verileriyle gösterilir. Test siparişi veya hazırlık sitesi kullanın."
      },
      {
        "question": "Kişisel favoriler ve son kullanılan şablonlar",
        "answer": "Her kullanıcı sipariş ekranında kişisel favoriler belirleyebilir. Eklenti ayrıca kullanıcı başına başarıyla kullanılan son on şablonu kaydeder ve üstte gösterir. Genel favoriler herkesle paylaşılır. Kişisel favoriler diğer kullanıcıları etkilemez."
      },
      {
        "question": "Tanılama sayfası",
        "answer": "<strong>Mailhilfe Order Notes → Tanılama</strong> altında WordPress, PHP ve WooCommerce sürümleri, HPOS, müşteri notu e-postası, dil, şablon sayısı, önbellek ve WP_DEBUG bilgileri bulunur. Destek isterken bu bilgileri paylaşın."
      },
      {
        "question": "Geliştirici kancaları ve filtreleri",
        "answer": "Yer tutucular, değerler, izin verilen meta anahtarları, şablon sonuçları, koşullar, önizleme, son içerik, ekleme öncesi/sonrası, geçmiş ve tanılama için kancalar ve filtreler vardır. Adlar readme.txt dosyasında belgelenmiştir. Özel verileri doğrulayın, temizleyin ve kaçışlayın."
      }
    ]
  },
  "ar": {
    "menu": "الأسئلة الشائعة",
    "title": "الأسئلة الشائعة",
    "intro": "إجابات عن الأسئلة الشائعة حول Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "لا تملك صلاحية إدارة قوالب الملاحظات.",
    "items": [
      {
        "question": "ما فائدة الإضافة؟",
        "answer": "تنشئ قوالب قابلة لإعادة الاستخدام لملاحظات طلبات WooCommerce. يمكن للموظف اختيار قالب داخل الطلب، معاينة النتيجة، ثم إضافتها كملاحظة داخلية أو ملاحظة للعميل."
      },
      {
        "question": "أين تتم إدارة القوالب؟",
        "answer": "من قائمة قوالب ملاحظات الطلب يمكن إنشاء القوالب وتعديلها وحذفها وتصنيفها وتمييزها كمفضلة وترتيبها."
      },
      {
        "question": "كيف تعمل العناصر النائبة؟",
        "answer": "يتم استبدال {order_number} و {customer} و {billing_email} و {order_total} و {items} ببيانات الطلب الحقيقية."
      },
      {
        "question": "هل يمكن تنسيق النص؟",
        "answer": "نعم. تدعم المحرر الفقرات والخط العريض والمائل والقوائم والروابط، ويتم إزالة HTML غير الآمن."
      },
      {
        "question": "ما الفرق بين الملاحظة الداخلية وملاحظة العميل؟",
        "answer": "الملاحظات الداخلية لفريق المتجر. ملاحظات العميل قد تكون مرئية للعميل وقد ترسل بريد WooCommerce."
      },
      {
        "question": "هل تدعم HPOS؟",
        "answer": "نعم. تستخدم واجهات WooCommerce للطلبات وتعلن التوافق مع HPOS."
      },
      {
        "question": "كيف يعمل JSON؟",
        "answer": "ينشئ التصدير ملف JSON، ويستعيد الاستيراد القوالب أو ينقلها إلى متجر آخر."
      },
      {
        "question": "هل تضيف روابط عامة؟",
        "answer": "لا. لا تضيف روابط powered-by أو روابط ترويجية في واجهة المتجر."
      },
      {
        "question": "فحص المعاينة",
        "answer": "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن. تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن. Use clear titles and categories."
      },
      {
        "question": "حالة بريد ملاحظة العميل",
        "answer": "عند اختيار ملاحظة للعميل، تعرض الإضافة تحذيرًا وتحاول إظهار ما إذا كان بريد WooCommerce لملاحظات العميل مفعّلًا. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "لغة القالب والمتاجر متعددة اللغات",
        "answer": "يمكن تعيين لغة لكل قالب. اختر <strong>كل اللغات</strong> للنص العام أو لغة محددة للرسائل المترجمة للعميل. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "الحقول المخصصة والبيانات الوصفية",
        "answer": "تدرج العناصر <code>{order_meta:meta_key}</code> و <code>{customer_meta:meta_key}</code> بيانات وصفية محددة. يتم حظر المفاتيح الحساسة مثل password و token و secret. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "ملاحظات داخلية وملاحظات للعميل",
        "answer": "تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن. تشرح هذه المساعدة سير العمل الكامل: إنشاء قوالب منسقة، استخدام العناصر النائبة، إضافة ملاحظات إلى طلبات WooCommerce، استيراد/تصدير JSON، إدارة الصلاحيات، HPOS والاستخدام الآمن. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "صفحة الإعدادات",
        "answer": "افتح <strong>Mailhilfe Order Notes → الإعدادات</strong> لتحديد السلوك الافتراضي للإضافة. يمكنك اختيار نوع الملاحظة الافتراضي، والسماح بـ HTML، والتحكم في تحذيرات ملاحظات العملاء، وعدادات الاستخدام، واستيراد JSON. لعمل يومي أكثر أمانًا اجعل الملاحظات الداخلية هي الافتراضية. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "معاينة الاستيراد",
        "answer": "يعرض استيراد JSON معاينة للقوالب التي سيتم إنشاؤها أو تحديثها أو تخطيها قبل التطبيق. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "النسخ والمراجعات",
        "answer": "تنشئ عملية النسخ نسخة كمسودة. تساعد مراجعات WordPress في مقارنة الإصدارات السابقة واستعادتها. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "الصلاحيات",
        "answer": "تحدد صفحة <strong>الصلاحيات</strong> الأدوار التي تدير القوالب والأدوار التي تستخدمها في الطلبات. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "سير العمل الموصى به",
        "answer": "اختر قالبًا، راجع المعاينة بعد الاستبدال، عدّلها عند الحاجة، تحقق من نوع الملاحظة ثم أضفها. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "هل يمكن استخدام الإضافة في متجر تجريبي؟",
        "answer": "نعم. استخدم استيراد/تصدير JSON وطلب اختبار لفحص القوالب والشروط والعناصر النائبة وإعدادات البريد قبل الموقع الفعلي."
      },
      {
        "question": "ما البيانات التي تُحذف عند إزالة الإضافة؟",
        "answer": "تُحذف القوالب والتصنيفات والإعدادات وجدول السجل والمفضلة الشخصية والقوالب الحديثة. تبقى ملاحظات WooCommerce الموجودة في سجل الطلب."
      },
      {
        "question": "كيف أبلغ عن مشكلة؟",
        "answer": "اذكر إصدارات WordPress وWooCommerce وPHP والإضافة وحالة HPOS واللغة والخطوات والخطأ. أرفق قيم التشخيص دون بيانات العملاء الشخصية."
      },
      {
        "question": "شروط القوالب",
        "answer": "تحدد الشروط ما إذا كان القالب متاحًا لطلب معين. يمكن التقييد حسب حالة الطلب وطريقة الدفع وطريقة الشحن وبلد الفوترة والحد الأدنى أو الأقصى للإجمالي. يجب أن تتطابق جميع الشروط المدخلة. اترك الحقل فارغًا إذا لم يكن مطلوبًا أن يقيّد القالب."
      },
      {
        "question": "سجل معالجة البريد الإلكتروني",
        "answer": "في ملاحظات العميل تسجل الإضافة عندما يبلغ WooCommerce عن معالجة البريد، كما تسجل أخطاء wp_mail التقنية. تعني «تمت المعالجة» تسليم الرسالة لنظام البريد فقط، ولا تثبت وصولها النهائي أو قراءتها. راجع صفحة السجل للأحداث المعالجة والفاشلة."
      },
      {
        "question": "السجل المركزي",
        "answer": "افتح <strong>Mailhilfe Order Notes ← السجل</strong> لمراجعة إنشاء الملاحظات واستخدام القوالب ومعالجة البريد والأخطاء. عند توفرها تظهر بيانات الطلب والقالب والمستخدم والمستلم ونوع الحدث والوقت. استخدم السجل للدعم والتدقيق واستكشاف الأخطاء."
      },
      {
        "question": "معاينة باستخدام طلب اختبار",
        "answer": "أدخل معرّف طلب WooCommerce في محرر القالب. تتم معاينة المحتوى الحالي، بما في ذلك التغييرات غير المحفوظة، ببيانات الطلب دون إنشاء ملاحظة أو إرسال بريد. استخدم طلب اختبار أو موقعًا تجريبيًا."
      },
      {
        "question": "المفضلة الشخصية والقوالب المستخدمة حديثًا",
        "answer": "يمكن لكل مستخدم تعيين مفضلات شخصية في شاشة الطلب. كما تحفظ الإضافة آخر عشرة قوالب تم استخدامها بنجاح لكل مستخدم وتعرضها أولًا. تبقى المفضلات العامة مشتركة. لا تؤثر المفضلات الشخصية على المستخدمين الآخرين."
      },
      {
        "question": "صفحة التشخيص",
        "answer": "افتح <strong>Mailhilfe Order Notes ← التشخيص</strong> لعرض إصدارات WordPress وPHP وWooCommerce وحالة HPOS وبريد ملاحظة العميل واللغة وعدد القوالب والتخزين المؤقت وWP_DEBUG. أرسل هذه المعلومات عند طلب الدعم."
      },
      {
        "question": "الخطافات والمرشحات للمطورين",
        "answer": "توفر الإضافة خطافات ومرشحات للعناصر النائبة والقيم ومفاتيح البيانات الوصفية المسموحة ونتائج القوالب والشروط والمعاينة والمحتوى النهائي والإجراءات قبل/بعد الإضافة والسجل والتشخيص. الأسماء موثقة في readme.txt. تحقق من البيانات المخصصة ونظفها وهربها."
      }
    ]
  },
  "hi_IN": {
    "menu": "FAQ",
    "title": "अक्सर पूछे जाने वाले प्रश्न",
    "intro": "Mailhilfe Order Note Manager for WooCommerce के बारे में सामान्य प्रश्नों के उत्तर।",
    "permission": "आपको नोट टेम्पलेट प्रबंधित करने की अनुमति नहीं है।",
    "items": [
      {
        "question": "यह प्लगइन किस लिए है?",
        "answer": "यह WooCommerce ऑर्डर नोट के लिए दोबारा उपयोग योग्य टेम्पलेट बनाता है। कर्मचारी ऑर्डर में टेम्पलेट चुनकर पूर्वावलोकन देख सकते हैं और उसे आंतरिक नोट या ग्राहक नोट के रूप में जोड़ सकते हैं।"
      },
      {
        "question": "टेम्पलेट कहाँ प्रबंधित होते हैं?",
        "answer": "ऑर्डर नोट टेम्पलेट मेनू में टेम्पलेट बनाएं, संपादित करें, हटाएं, श्रेणी दें, पसंदीदा करें और क्रम बदलें।"
      },
      {
        "question": "प्लेसहोल्डर कैसे काम करते हैं?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} और {items} वास्तविक ऑर्डर डेटा से बदल जाते हैं।"
      },
      {
        "question": "क्या टेक्स्ट फॉर्मेट हो सकता है?",
        "answer": "हाँ। पैराग्राफ, बोल्ड, इटैलिक, सूचियाँ और लिंक समर्थित हैं। असुरक्षित HTML हटाया जाता है।"
      },
      {
        "question": "आंतरिक नोट और ग्राहक नोट में क्या अंतर है?",
        "answer": "आंतरिक नोट दुकान टीम के लिए हैं। ग्राहक नोट ग्राहक को दिख सकते हैं और WooCommerce ईमेल भेज सकते हैं।"
      },
      {
        "question": "क्या यह HPOS संगत है?",
        "answer": "हाँ। यह WooCommerce ऑर्डर API का उपयोग करता है और HPOS संगतता घोषित करता है।"
      },
      {
        "question": "JSON कैसे काम करता है?",
        "answer": "निर्यात JSON फ़ाइल बनाता है, आयात टेम्पलेट पुनर्स्थापित या दूसरी दुकान में स्थानांतरित करता है।"
      },
      {
        "question": "क्या सार्वजनिक लिंक जोड़े जाते हैं?",
        "answer": "नहीं। यह फ्रंटएंड में powered-by या प्रचार लिंक नहीं जोड़ता।"
      },
      {
        "question": "पूर्वावलोकन जांचना",
        "answer": "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग। यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग। Use clear titles and categories."
      },
      {
        "question": "ग्राहक नोट ईमेल स्थिति",
        "answer": "ग्राहक नोट चुनने पर प्लगइन चेतावनी दिखाता है और WooCommerce customer note ईमेल सक्रिय है या नहीं यह बताने की कोशिश करता है। If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "टेम्पलेट भाषा और बहुभाषी दुकानें",
        "answer": "हर टेम्पलेट की भाषा चुनी जा सकती है। सामान्य पाठ के लिए <strong>सभी भाषाएँ</strong> और अनुवादित ग्राहक संदेशों के लिए विशिष्ट भाषा चुनें। For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "कस्टम फ़ील्ड और मेटाडेटा",
        "answer": "<code>{order_meta:meta_key}</code> और <code>{customer_meta:meta_key}</code> चुने हुए मेटाडेटा जोड़ते हैं। password, token या secret जैसी संवेदनशील कुंजियाँ रोकी जाती हैं। Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "आंतरिक नोट और ग्राहक नोट",
        "answer": "यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग। यह सहायता पूरा कार्यप्रवाह समझाती है: स्वरूपित टेम्पलेट बनाना, प्लेसहोल्डर उपयोग करना, WooCommerce ऑर्डर में नोट जोड़ना, JSON आयात/निर्यात, अनुमतियां, HPOS और सुरक्षित उपयोग। <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "सेटिंग पेज",
        "answer": "<strong>Mailhilfe Order Notes → Settings</strong> खोलकर प्लगइन का डिफ़ॉल्ट व्यवहार तय करें। आप डिफ़ॉल्ट नोट प्रकार, HTML अनुमति, ग्राहक नोट चेतावनी, उपयोग काउंटर और JSON आयात नियंत्रित कर सकते हैं। सुरक्षित दैनिक कार्य के लिए आंतरिक नोट को डिफ़ॉल्ट रखें। Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "आयात पूर्वावलोकन",
        "answer": "JSON आयात लागू करने से पहले बनाए, अपडेट या छोड़े जाने वाले टेम्पलेट दिखाता है। Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "डुप्लिकेट और संशोधन",
        "answer": "डुप्लिकेट क्रिया ड्राफ्ट कॉपी बनाती है। WordPress revisions पुराने संस्करणों की तुलना और पुनर्स्थापना में मदद करते हैं। Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "अनुमतियाँ",
        "answer": "<strong>Permissions</strong> पेज तय करता है कि कौन-सी भूमिकाएँ टेम्पलेट प्रबंधित करेंगी और कौन उन्हें ऑर्डर में उपयोग करेंगी। Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "अनुशंसित कार्यप्रवाह",
        "answer": "टेम्पलेट चुनें, बदला हुआ पूर्वावलोकन जाँचें, आवश्यकता हो तो संपादित करें, नोट प्रकार जाँचें और नोट जोड़ें. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "क्या प्लगइन को स्टेजिंग दुकान में उपयोग कर सकता हूं?",
        "answer": "हाँ। लाइव साइट से पहले JSON आयात/निर्यात और टेस्ट ऑर्डर से टेम्पलेट, शर्तें, प्लेसहोल्डर और ईमेल सेटिंग जांचें।"
      },
      {
        "question": "अनइंस्टॉल पर कौन-सा डेटा हटता है?",
        "answer": "टेम्पलेट, श्रेणियां, सेटिंग, इतिहास तालिका, व्यक्तिगत पसंदीदा और हाल के टेम्पलेट हटते हैं। मौजूदा WooCommerce ऑर्डर नोट इतिहास में रहते हैं।"
      },
      {
        "question": "समस्या कैसे रिपोर्ट करनी चाहिए?",
        "answer": "WordPress, WooCommerce, PHP और प्लगइन संस्करण, HPOS, भाषा, सटीक कदम और त्रुटि दें। निदान मान जोड़ें, लेकिन ग्राहक का निजी डेटा नहीं।"
      },
      {
        "question": "टेम्पलेट की शर्तें",
        "answer": "शर्तें तय करती हैं कि कोई टेम्पलेट किसी ऑर्डर के लिए उपलब्ध है या नहीं। इसे ऑर्डर स्थिति, भुगतान विधि, शिपिंग विधि, बिलिंग देश और न्यूनतम या अधिकतम कुल राशि से सीमित किया जा सकता है। सभी भरी हुई शर्तें पूरी होनी चाहिए। जिस शर्त से सीमा नहीं लगानी हो उसे खाली छोड़ें।"
      },
      {
        "question": "ईमेल प्रोसेसिंग लॉग",
        "answer": "ग्राहक नोट के लिए प्लगइन WooCommerce द्वारा ईमेल प्रोसेस होने की सूचना और wp_mail की तकनीकी त्रुटियां दर्ज करता है। “प्रोसेस्ड” केवल मेल सिस्टम को सौंपे जाने की पुष्टि है, अंतिम डिलीवरी या पढ़े जाने की नहीं। प्रोसेस्ड और विफल घटनाओं के लिए इतिहास पेज देखें।"
      },
      {
        "question": "केंद्रीय इतिहास",
        "answer": "<strong>Mailhilfe Order Notes → इतिहास</strong> में नोट निर्माण, टेम्पलेट उपयोग, ईमेल प्रोसेसिंग और विफलताएं देखें। उपलब्ध होने पर ऑर्डर, टेम्पलेट, उपयोगकर्ता, प्राप्तकर्ता, घटना प्रकार और समय दिखता है। समर्थन, ऑडिट और समस्या समाधान के लिए उपयोग करें।"
      },
      {
        "question": "टेस्ट ऑर्डर पूर्वावलोकन",
        "answer": "टेम्पलेट एडिटर में WooCommerce ऑर्डर ID दर्ज करें। सहेजे बिना किए गए बदलावों सहित वर्तमान सामग्री ऑर्डर डेटा से दिखाई जाएगी, बिना नोट बनाए या ईमेल भेजे। टेस्ट ऑर्डर या स्टेजिंग साइट उपयोग करें।"
      },
      {
        "question": "व्यक्तिगत पसंदीदा और हाल के टेम्पलेट",
        "answer": "हर उपयोगकर्ता ऑर्डर स्क्रीन में व्यक्तिगत पसंदीदा चुन सकता है। प्लगइन प्रति उपयोगकर्ता सफलतापूर्वक उपयोग किए गए अंतिम दस टेम्पलेट भी सहेजता और ऊपर दिखाता है। वैश्विक पसंदीदा सभी के लिए साझा रहते हैं। व्यक्तिगत पसंदीदा दूसरे उपयोगकर्ताओं को प्रभावित नहीं करते।"
      },
      {
        "question": "निदान पेज",
        "answer": "<strong>Mailhilfe Order Notes → निदान</strong> में WordPress, PHP और WooCommerce संस्करण, HPOS, ग्राहक नोट ईमेल, भाषा, टेम्पलेट संख्या, कैश और WP_DEBUG देखें। समर्थन मांगते समय ये जानकारी दें।"
      },
      {
        "question": "डेवलपर हुक और फ़िल्टर",
        "answer": "प्लेसहोल्डर, मान, अनुमत मेटा कुंजी, टेम्पलेट परिणाम, शर्तें, पूर्वावलोकन, अंतिम सामग्री, जोड़ने से पहले/बाद, इतिहास और निदान के लिए हुक और फ़िल्टर उपलब्ध हैं। नाम readme.txt में दिए हैं। कस्टम डेटा को मान्य, स्वच्छ और एस्केप करें।"
      }
    ]
  },
  "id_ID": {
    "menu": "FAQ",
    "title": "Pertanyaan yang sering diajukan",
    "intro": "Jawaban atas pertanyaan umum tentang Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Anda tidak diizinkan mengelola templat catatan.",
    "items": [
      {
        "question": "Untuk apa plugin ini?",
        "answer": "Plugin membuat templat catatan pesanan WooCommerce yang dapat digunakan kembali. Staf dapat memilih templat di pesanan, melihat pratinjau, lalu menambahkannya sebagai catatan internal atau catatan pelanggan."
      },
      {
        "question": "Di mana templat dikelola?",
        "answer": "Di menu templat catatan pesanan Anda dapat membuat, mengedit, menghapus, memberi kategori, menandai favorit, dan mengurutkan templat."
      },
      {
        "question": "Bagaimana placeholder bekerja?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total}, dan {items} diganti dengan data pesanan nyata."
      },
      {
        "question": "Bisakah teks diformat?",
        "answer": "Ya. Paragraf, tebal, miring, daftar, dan tautan didukung. HTML tidak aman dihapus."
      },
      {
        "question": "Apa beda catatan internal dan pelanggan?",
        "answer": "Catatan internal untuk tim toko. Catatan pelanggan dapat terlihat oleh pelanggan dan memicu email WooCommerce."
      },
      {
        "question": "Apakah kompatibel HPOS?",
        "answer": "Ya. Plugin memakai API pesanan WooCommerce dan menyatakan kompatibilitas HPOS."
      },
      {
        "question": "Bagaimana JSON digunakan?",
        "answer": "Ekspor membuat file JSON, impor memulihkan atau memindahkan templat ke toko lain."
      },
      {
        "question": "Apakah menambah tautan publik?",
        "answer": "Tidak. Plugin tidak menambah tautan powered-by atau promosi di frontend."
      },
      {
        "question": "Memeriksa pratinjau",
        "answer": "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman. Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman. Use clear titles and categories."
      },
      {
        "question": "Status email catatan pelanggan",
        "answer": "Saat catatan pelanggan dipilih, plugin menampilkan peringatan dan mencoba menunjukkan apakah email catatan pelanggan WooCommerce aktif. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Bahasa templat dan toko multibahasa",
        "answer": "Setiap templat dapat memiliki bahasa. Pilih <strong>Semua bahasa</strong> untuk teks umum atau bahasa tertentu untuk pesan pelanggan yang diterjemahkan. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Kolom khusus dan metadata",
        "answer": "Placeholder <code>{order_meta:meta_key}</code> dan <code>{customer_meta:meta_key}</code> memasukkan metadata terpilih. Kunci sensitif seperti password, token, atau secret diblokir. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Catatan internal dan catatan pelanggan",
        "answer": "Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman. Bantuan ini menjelaskan alur lengkap: membuat templat berformat, memakai placeholder, menambahkan catatan pada pesanan WooCommerce, impor/ekspor JSON, izin, HPOS, dan penggunaan yang aman. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Halaman pengaturan",
        "answer": "Buka <strong>Mailhilfe Order Notes → Pengaturan</strong> untuk menentukan perilaku default plugin. Anda dapat memilih tipe catatan default, mengizinkan HTML, mengatur peringatan catatan pelanggan, penghitung penggunaan, dan impor JSON. Gunakan catatan internal sebagai default agar pekerjaan harian lebih aman. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Pratinjau impor",
        "answer": "Impor JSON menampilkan pratinjau templat yang dibuat, diperbarui, atau dilewati sebelum diterapkan. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Duplikasi dan revisi",
        "answer": "Duplikasi membuat salinan sebagai draf. Revisi WordPress membantu membandingkan dan memulihkan versi lama. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Izin",
        "answer": "Halaman <strong>Izin</strong> menentukan peran mana yang mengelola templat dan mana yang menggunakannya di pesanan. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Alur kerja yang disarankan",
        "answer": "Pilih templat, periksa pratinjau, edit bila perlu, pastikan tipe catatan, lalu tambahkan catatan. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Bisakah plugin digunakan di toko staging?",
        "answer": "Ya. Gunakan impor/ekspor JSON dan pesanan uji untuk memeriksa templat, kondisi, placeholder, serta email sebelum produksi."
      },
      {
        "question": "Data apa yang dihapus saat uninstall?",
        "answer": "Templat, kategori, pengaturan, tabel riwayat, favorit pribadi, dan templat terbaru dihapus. Catatan pesanan WooCommerce yang sudah ada tetap di riwayat pesanan."
      },
      {
        "question": "Bagaimana cara melaporkan masalah?",
        "answer": "Sertakan versi WordPress, WooCommerce, PHP dan plugin, status HPOS, bahasa, langkah tepat, dan pesan kesalahan. Tambahkan data diagnostik tanpa data pribadi pelanggan."
      },
      {
        "question": "Kondisi templat",
        "answer": "Kondisi menentukan apakah templat tersedia untuk suatu pesanan. Templat dapat dibatasi berdasarkan status, metode pembayaran, metode pengiriman, negara penagihan, serta total minimum atau maksimum. Semua kondisi yang diisi harus cocok. Kosongkan kolom jika tidak ingin membatasi templat."
      },
      {
        "question": "Log pemrosesan email",
        "answer": "Untuk catatan pelanggan, plugin mencatat saat WooCommerce melaporkan email telah diproses dan kesalahan teknis wp_mail. “Diproses” hanya menegaskan penyerahan ke sistem email, bukan pengiriman akhir atau bahwa email dibaca. Lihat halaman Riwayat untuk kejadian diproses dan gagal."
      },
      {
        "question": "Riwayat terpusat",
        "answer": "Buka <strong>Mailhilfe Order Notes → Riwayat</strong> untuk melihat pembuatan catatan, penggunaan templat, pemrosesan email, dan kegagalan. Jika tersedia, pesanan, templat, pengguna, penerima, jenis kejadian, dan waktu ditampilkan. Gunakan untuk dukungan, audit, dan pemecahan masalah."
      },
      {
        "question": "Pratinjau dengan pesanan uji",
        "answer": "Masukkan ID pesanan WooCommerce di editor templat. Konten saat ini, termasuk perubahan yang belum disimpan, ditampilkan dengan data pesanan tanpa membuat catatan atau mengirim email. Gunakan pesanan uji atau situs staging."
      },
      {
        "question": "Favorit pribadi dan templat terbaru",
        "answer": "Setiap pengguna dapat menandai favorit pribadi di layar pesanan. Plugin juga menyimpan sepuluh templat terakhir yang berhasil digunakan per pengguna dan menampilkannya lebih atas. Favorit global tetap dibagikan. Favorit pribadi tidak memengaruhi pengguna lain."
      },
      {
        "question": "Halaman diagnostik",
        "answer": "Buka <strong>Mailhilfe Order Notes → Diagnostik</strong> untuk melihat versi WordPress, PHP dan WooCommerce, HPOS, email catatan pelanggan, bahasa, jumlah templat, cache, dan WP_DEBUG. Sertakan data ini saat meminta dukungan."
      },
      {
        "question": "Hook dan filter untuk pengembang",
        "answer": "Plugin menyediakan hook dan filter untuk placeholder, nilai, kunci meta yang diizinkan, hasil templat, kondisi, pratinjau, konten akhir, tindakan sebelum/sesudah penambahan, riwayat, dan diagnostik. Nama didokumentasikan di readme.txt. Validasi, sanitasi, dan escape semua data khusus."
      }
    ]
  },
  "vi": {
    "menu": "FAQ",
    "title": "Câu hỏi thường gặp",
    "intro": "Câu trả lời cho các câu hỏi phổ biến về Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Bạn không có quyền quản lý mẫu ghi chú.",
    "items": [
      {
        "question": "Plugin dùng để làm gì?",
        "answer": "Plugin tạo mẫu ghi chú đơn hàng WooCommerce có thể tái sử dụng. Nhân viên chọn mẫu trong đơn hàng, xem trước và thêm làm ghi chú nội bộ hoặc ghi chú cho khách hàng."
      },
      {
        "question": "Quản lý mẫu ở đâu?",
        "answer": "Trong menu mẫu ghi chú đơn hàng, bạn có thể tạo, sửa, xóa, phân loại, đánh dấu yêu thích và sắp xếp mẫu."
      },
      {
        "question": "Trình giữ chỗ hoạt động thế nào?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} và {items} được thay bằng dữ liệu đơn hàng thật."
      },
      {
        "question": "Có định dạng văn bản được không?",
        "answer": "Có. Hỗ trợ đoạn văn, in đậm, in nghiêng, danh sách và liên kết. HTML không an toàn bị xóa."
      },
      {
        "question": "Ghi chú nội bộ và khách hàng khác gì?",
        "answer": "Ghi chú nội bộ dành cho nhóm cửa hàng. Ghi chú khách hàng có thể hiển thị cho khách và kích hoạt email WooCommerce."
      },
      {
        "question": "Có tương thích HPOS không?",
        "answer": "Có. Plugin dùng API đơn hàng WooCommerce và khai báo tương thích HPOS."
      },
      {
        "question": "JSON hoạt động thế nào?",
        "answer": "Xuất tạo tệp JSON, nhập khôi phục hoặc chuyển mẫu sang cửa hàng khác."
      },
      {
        "question": "Có thêm liên kết công khai không?",
        "answer": "Không. Plugin không thêm liên kết powered-by hoặc quảng cáo ở giao diện cửa hàng."
      },
      {
        "question": "Kiểm tra bản xem trước",
        "answer": "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn. Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn. Use clear titles and categories."
      },
      {
        "question": "Trạng thái email ghi chú khách hàng",
        "answer": "Khi chọn ghi chú khách hàng, plugin hiển thị cảnh báo và cố gắng cho biết email ghi chú khách hàng của WooCommerce có đang bật không. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Ngôn ngữ mẫu và cửa hàng đa ngôn ngữ",
        "answer": "Mỗi mẫu có thể có một ngôn ngữ. Chọn <strong>Tất cả ngôn ngữ</strong> cho nội dung chung hoặc một ngôn ngữ cụ thể cho thông báo khách hàng đã dịch. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Trường tùy chỉnh và siêu dữ liệu",
        "answer": "Placeholder <code>{order_meta:meta_key}</code> và <code>{customer_meta:meta_key}</code> chèn siêu dữ liệu đã chọn. Các khóa nhạy cảm như password, token hoặc secret bị chặn. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Ghi chú nội bộ và ghi chú khách hàng",
        "answer": "Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn. Phần trợ giúp này giải thích toàn bộ quy trình: tạo mẫu có định dạng, dùng biến giữ chỗ, thêm ghi chú vào đơn hàng WooCommerce, nhập/xuất JSON, quyền, HPOS và sử dụng an toàn. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Trang cài đặt",
        "answer": "Mở <strong>Mailhilfe Order Notes → Cài đặt</strong> để đặt hành vi mặc định của plugin. Bạn có thể chọn loại ghi chú mặc định, cho phép HTML, kiểm soát cảnh báo ghi chú khách hàng, bộ đếm sử dụng và nhập JSON. Nên dùng ghi chú nội bộ làm mặc định để làm việc hằng ngày an toàn hơn. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Xem trước nhập",
        "answer": "Nhập JSON hiển thị trước các mẫu sẽ được tạo, cập nhật hoặc bỏ qua trước khi áp dụng. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Nhân bản và phiên bản",
        "answer": "Hành động nhân bản tạo một bản nháp. Revisions của WordPress giúp so sánh và khôi phục phiên bản cũ. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Quyền",
        "answer": "Trang <strong>Quyền</strong> xác định vai trò nào quản lý mẫu và vai trò nào dùng mẫu trong đơn hàng. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Quy trình đề xuất",
        "answer": "Chọn mẫu, kiểm tra bản xem trước đã thay thế, chỉnh sửa nếu cần, xác nhận loại ghi chú rồi thêm ghi chú. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Có thể dùng plugin trên cửa hàng staging không?",
        "answer": "Có. Dùng nhập/xuất JSON và đơn hàng thử để kiểm tra mẫu, điều kiện, biến giữ chỗ và email trước khi đưa lên trang thật."
      },
      {
        "question": "Dữ liệu nào bị xóa khi gỡ plugin?",
        "answer": "Mẫu, danh mục, cài đặt, bảng lịch sử, yêu thích cá nhân và mẫu gần đây bị xóa. Ghi chú WooCommerce đã tạo vẫn nằm trong lịch sử đơn hàng."
      },
      {
        "question": "Nên báo cáo sự cố như thế nào?",
        "answer": "Cung cấp phiên bản WordPress, WooCommerce, PHP và plugin, HPOS, ngôn ngữ, các bước chính xác và lỗi. Thêm dữ liệu chẩn đoán nhưng không có dữ liệu cá nhân khách hàng."
      },
      {
        "question": "Điều kiện mẫu",
        "answer": "Điều kiện quyết định mẫu có sẵn cho một đơn hàng hay không. Có thể giới hạn theo trạng thái đơn hàng, phương thức thanh toán, phương thức giao hàng, quốc gia thanh toán và tổng tối thiểu hoặc tối đa. Tất cả điều kiện đã nhập phải phù hợp. Để trống trường nếu không muốn giới hạn mẫu."
      },
      {
        "question": "Nhật ký xử lý email",
        "answer": "Với ghi chú khách hàng, plugin ghi lại khi WooCommerce báo email đã được xử lý và các lỗi kỹ thuật wp_mail. “Đã xử lý” chỉ xác nhận chuyển sang hệ thống thư, không chứng minh đã giao cuối cùng hoặc đã đọc. Xem trang Lịch sử để biết sự kiện đã xử lý và thất bại."
      },
      {
        "question": "Lịch sử tập trung",
        "answer": "Mở <strong>Mailhilfe Order Notes → Lịch sử</strong> để xem việc tạo ghi chú, sử dụng mẫu, xử lý và lỗi email. Khi có, đơn hàng, mẫu, người dùng, người nhận, loại sự kiện và thời gian sẽ được hiển thị. Dùng cho hỗ trợ, kiểm tra và khắc phục sự cố."
      },
      {
        "question": "Xem trước bằng đơn hàng thử",
        "answer": "Nhập ID đơn hàng WooCommerce trong trình sửa mẫu. Nội dung hiện tại, kể cả thay đổi chưa lưu, được hiển thị với dữ liệu đơn hàng mà không tạo ghi chú hoặc gửi email. Dùng đơn hàng thử hoặc trang staging."
      },
      {
        "question": "Yêu thích cá nhân và mẫu dùng gần đây",
        "answer": "Mỗi người dùng có thể đánh dấu yêu thích cá nhân trong màn hình đơn hàng. Plugin cũng lưu mười mẫu được dùng thành công gần nhất theo người dùng và đưa lên trên. Yêu thích chung vẫn được chia sẻ. Yêu thích cá nhân không ảnh hưởng người dùng khác."
      },
      {
        "question": "Trang chẩn đoán",
        "answer": "Mở <strong>Mailhilfe Order Notes → Chẩn đoán</strong> để xem phiên bản WordPress, PHP, WooCommerce, trạng thái HPOS, email ghi chú khách hàng, ngôn ngữ, số mẫu, bộ nhớ đệm và WP_DEBUG. Cung cấp các giá trị này khi yêu cầu hỗ trợ."
      },
      {
        "question": "Hook và bộ lọc cho nhà phát triển",
        "answer": "Plugin cung cấp hook và bộ lọc cho biến giữ chỗ, giá trị, khóa meta được phép, kết quả mẫu, điều kiện, xem trước, nội dung cuối, hành động trước/sau khi thêm, lịch sử và chẩn đoán. Tên được ghi trong readme.txt. Xác thực, làm sạch và escape dữ liệu tùy chỉnh."
      }
    ]
  },
  "th": {
    "menu": "FAQ",
    "title": "คำถามที่พบบ่อย",
    "intro": "คำตอบสำหรับคำถามทั่วไปเกี่ยวกับ Mailhilfe Order Note Manager for WooCommerce",
    "permission": "คุณไม่มีสิทธิ์จัดการเทมเพลตบันทึก",
    "items": [
      {
        "question": "ปลั๊กอินนี้ใช้ทำอะไร?",
        "answer": "ปลั๊กอินสร้างเทมเพลตบันทึกคำสั่งซื้อ WooCommerce ที่ใช้ซ้ำได้ พนักงานเลือกเทมเพลตในคำสั่งซื้อ ดูตัวอย่าง แล้วเพิ่มเป็นบันทึกภายในหรือบันทึกลูกค้าได้"
      },
      {
        "question": "จัดการเทมเพลตที่ไหน?",
        "answer": "ในเมนูเทมเพลตบันทึกคำสั่งซื้อ สามารถสร้าง แก้ไข ลบ จัดหมวดหมู่ ทำเป็นรายการโปรด และจัดเรียงได้"
      },
      {
        "question": "ตัวยึดตำแหน่งทำงานอย่างไร?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} และ {items} จะถูกแทนด้วยข้อมูลคำสั่งซื้อจริง"
      },
      {
        "question": "จัดรูปแบบข้อความได้ไหม?",
        "answer": "ได้ รองรับย่อหน้า ตัวหนา ตัวเอียง รายการ และลิงก์ HTML ที่ไม่ปลอดภัยจะถูกลบ"
      },
      {
        "question": "บันทึกภายในกับบันทึกลูกค้าต่างกันอย่างไร?",
        "answer": "บันทึกภายในใช้สำหรับทีมร้านค้า บันทึกลูกค้าอาจแสดงให้ลูกค้าเห็นและส่งอีเมล WooCommerce"
      },
      {
        "question": "รองรับ HPOS หรือไม่?",
        "answer": "รองรับ ปลั๊กอินใช้ WooCommerce order API และประกาศความเข้ากันได้กับ HPOS"
      },
      {
        "question": "JSON ทำงานอย่างไร?",
        "answer": "การส่งออกสร้างไฟล์ JSON การนำเข้านำเทมเพลตกลับมาหรือย้ายไปยังร้านอื่น"
      },
      {
        "question": "เพิ่มลิงก์สาธารณะหรือไม่?",
        "answer": "ไม่เพิ่มลิงก์ powered-by หรือลิงก์โฆษณาที่หน้าร้าน"
      },
      {
        "question": "ตรวจสอบตัวอย่าง",
        "answer": "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย Use clear titles and categories."
      },
      {
        "question": "สถานะอีเมลบันทึกลูกค้า",
        "answer": "เมื่อเลือกบันทึกลูกค้า ปลั๊กอินจะแสดงคำเตือนและพยายามบอกว่าอีเมลบันทึกลูกค้าของ WooCommerce เปิดใช้งานอยู่หรือไม่ If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "ภาษาของเทมเพลตและร้านหลายภาษา",
        "answer": "แต่ละเทมเพลตกำหนดภาษาได้ เลือก <strong>ทุกภาษา</strong> สำหรับข้อความทั่วไป หรือเลือกภาษาเฉพาะสำหรับข้อความลูกค้าที่แปลแล้ว For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "ฟิลด์กำหนดเองและเมตาดาต้า",
        "answer": "ตัวแทน <code>{order_meta:meta_key}</code> และ <code>{customer_meta:meta_key}</code> จะแทรกเมตาดาต้าที่เลือก คีย์อ่อนไหวเช่น password, token หรือ secret จะถูกบล็อก Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "บันทึกภายในและบันทึกลูกค้า",
        "answer": "วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย วิธีใช้นี้อธิบายขั้นตอนทั้งหมด: การสร้างเทมเพลตแบบจัดรูปแบบ การใช้ตัวยึดตำแหน่ง การเพิ่มบันทึกในคำสั่งซื้อ WooCommerce การนำเข้า/ส่งออก JSON สิทธิ์ HPOS และการใช้งานอย่างปลอดภัย <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "หน้าการตั้งค่า",
        "answer": "เปิด <strong>Mailhilfe Order Notes → การตั้งค่า</strong> เพื่อกำหนดพฤติกรรมเริ่มต้นของปลั๊กอิน คุณสามารถเลือกชนิดบันทึกเริ่มต้น อนุญาต HTML ควบคุมคำเตือนบันทึกลูกค้า ตัวนับการใช้งาน และการนำเข้า JSON เพื่อความปลอดภัยในการใช้งานประจำวัน ควรใช้บันทึกภายในเป็นค่าเริ่มต้น Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "ตัวอย่างก่อนนำเข้า",
        "answer": "การนำเข้า JSON จะแสดงตัวอย่างเทมเพลตที่จะสร้าง อัปเดต หรือข้ามก่อนนำไปใช้ Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "ทำซ้ำและรุ่นแก้ไข",
        "answer": "การทำซ้ำจะสร้างสำเนาแบบร่าง รุ่นแก้ไขของ WordPress ช่วยเปรียบเทียบและกู้คืนเวอร์ชันเก่า Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "สิทธิ์",
        "answer": "หน้า <strong>สิทธิ์</strong> กำหนดว่าบทบาทใดจัดการเทมเพลตและบทบาทใดใช้เทมเพลตในคำสั่งซื้อ Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "ขั้นตอนที่แนะนำ",
        "answer": "เลือกเทมเพลต ตรวจตัวอย่างที่แทนค่าแล้ว แก้ไขถ้าจำเป็น ตรวจชนิดบันทึก แล้วเพิ่มบันทึก For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "ใช้ปลั๊กอินในร้าน staging ได้หรือไม่?",
        "answer": "ได้ ใช้การนำเข้า/ส่งออก JSON และคำสั่งซื้อทดสอบเพื่อตรวจสอบเทมเพลต เงื่อนไข ตัวยึด และอีเมลก่อนใช้งานจริง"
      },
      {
        "question": "ข้อมูลใดถูกลบเมื่อถอนการติดตั้ง?",
        "answer": "เทมเพลต หมวดหมู่ การตั้งค่า ตารางประวัติ รายการโปรดส่วนตัว และเทมเพลตล่าสุดจะถูกลบ บันทึก WooCommerce ที่มีอยู่ยังอยู่ในประวัติคำสั่งซื้อ"
      },
      {
        "question": "ควรรายงานปัญหาอย่างไร?",
        "answer": "ระบุเวอร์ชัน WordPress, WooCommerce, PHP และปลั๊กอิน สถานะ HPOS ภาษา ขั้นตอน และข้อผิดพลาด พร้อมค่าการวินิจฉัยโดยไม่ใส่ข้อมูลส่วนบุคคลของลูกค้า"
      },
      {
        "question": "เงื่อนไขของเทมเพลต",
        "answer": "เงื่อนไขกำหนดว่าเทมเพลตพร้อมใช้กับคำสั่งซื้อหรือไม่ สามารถจำกัดตามสถานะคำสั่งซื้อ วิธีชำระเงิน วิธีจัดส่ง ประเทศที่ออกใบแจ้งหนี้ และยอดขั้นต่ำหรือสูงสุด เงื่อนไขที่กรอกทั้งหมดต้องตรงกัน ปล่อยช่องว่างหากไม่ต้องการใช้เป็นข้อจำกัด"
      },
      {
        "question": "บันทึกการประมวลผลอีเมล",
        "answer": "สำหรับบันทึกลูกค้า ปลั๊กอินจะบันทึกเมื่อ WooCommerce รายงานว่าอีเมลถูกประมวลผลและบันทึกข้อผิดพลาดทางเทคนิคของ wp_mail สถานะ “ประมวลผลแล้ว” หมายถึงส่งต่อให้ระบบอีเมล ไม่ได้ยืนยันการส่งถึงหรือการอ่าน ดูเหตุการณ์สำเร็จและล้มเหลวในหน้าประวัติ"
      },
      {
        "question": "ประวัติส่วนกลาง",
        "answer": "เปิด <strong>Mailhilfe Order Notes → ประวัติ</strong> เพื่อดูการสร้างบันทึก การใช้เทมเพลต การประมวลผลและความล้มเหลวของอีเมล หากมีจะแสดงคำสั่งซื้อ เทมเพลต ผู้ใช้ ผู้รับ ประเภทเหตุการณ์ และเวลา ใช้สำหรับการสนับสนุน การตรวจสอบ และแก้ปัญหา"
      },
      {
        "question": "ตัวอย่างด้วยคำสั่งซื้อทดสอบ",
        "answer": "ป้อน ID คำสั่งซื้อ WooCommerce ในตัวแก้ไขเทมเพลต เนื้อหาปัจจุบันรวมถึงการเปลี่ยนแปลงที่ยังไม่บันทึกจะแสดงด้วยข้อมูลคำสั่งซื้อ โดยไม่สร้างบันทึกหรือส่งอีเมล ใช้คำสั่งซื้อทดสอบหรือเว็บไซต์ staging"
      },
      {
        "question": "รายการโปรดส่วนตัวและเทมเพลตล่าสุด",
        "answer": "ผู้ใช้แต่ละคนสามารถกำหนดรายการโปรดส่วนตัวในหน้าคำสั่งซื้อ ปลั๊กอินยังบันทึกเทมเพลตสิบรายการล่าสุดที่ใช้สำเร็จต่อผู้ใช้และแสดงไว้ด้านบน รายการโปรดส่วนกลางยังคงใช้ร่วมกัน รายการโปรดส่วนตัวไม่กระทบผู้ใช้อื่น"
      },
      {
        "question": "หน้าวินิจฉัย",
        "answer": "เปิด <strong>Mailhilfe Order Notes → วินิจฉัย</strong> เพื่อดูเวอร์ชัน WordPress, PHP และ WooCommerce, สถานะ HPOS, อีเมลบันทึกลูกค้า, ภาษา, จำนวนเทมเพลต, แคช และ WP_DEBUG ส่งข้อมูลเหล่านี้เมื่อขอความช่วยเหลือ"
      },
      {
        "question": "ฮุกและตัวกรองสำหรับนักพัฒนา",
        "answer": "มีฮุกและตัวกรองสำหรับตัวยึดตำแหน่ง ค่า คีย์เมตาที่อนุญาต ผลลัพธ์เทมเพลต เงื่อนไข ตัวอย่าง เนื้อหาสุดท้าย การทำงานก่อน/หลังเพิ่ม ประวัติ และวินิจฉัย ชื่อระบุใน readme.txt ตรวจสอบ ทำความสะอาด และ escape ข้อมูลที่กำหนดเอง"
      }
    ]
  },
  "uk": {
    "menu": "FAQ",
    "title": "Поширені запитання",
    "intro": "Відповіді на часті запитання про Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "У вас немає прав для керування шаблонами нотаток.",
    "items": [
      {
        "question": "Для чого потрібен плагін?",
        "answer": "Плагін створює багаторазові шаблони нотаток замовлень WooCommerce. Працівник вибирає шаблон у замовленні, перевіряє попередній перегляд і додає внутрішню нотатку або нотатку для клієнта."
      },
      {
        "question": "Де керувати шаблонами?",
        "answer": "У меню шаблонів нотаток можна створювати, редагувати, видаляти, категоризувати, позначати обрані та сортувати шаблони."
      },
      {
        "question": "Як працюють заповнювачі?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} і {items} замінюються реальними даними замовлення."
      },
      {
        "question": "Чи можна форматувати текст?",
        "answer": "Так. Підтримуються абзаци, жирний і курсивний текст, списки та посилання. Небезпечний HTML видаляється."
      },
      {
        "question": "Внутрішня нотатка чи нотатка клієнта?",
        "answer": "Внутрішні нотатки призначені для команди магазину. Нотатки клієнта можуть бути видимі клієнту та надсилати листи WooCommerce."
      },
      {
        "question": "Чи сумісний HPOS?",
        "answer": "Так. Плагін використовує API замовлень WooCommerce і декларує сумісність HPOS."
      },
      {
        "question": "Як працює JSON?",
        "answer": "Експорт створює JSON-файл, імпорт відновлює шаблони або переносить їх до іншого магазину."
      },
      {
        "question": "Чи додаються публічні посилання?",
        "answer": "Ні. Плагін не додає powered-by або рекламні посилання на вітрину."
      },
      {
        "question": "Перевірка попереднього перегляду",
        "answer": "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу. Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу. Use clear titles and categories."
      },
      {
        "question": "Стан e-mail нотатки клієнту",
        "answer": "Коли вибрано нотатку клієнту, плагін показує попередження і намагається визначити, чи активний e-mail WooCommerce для нотаток клієнту. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Мова шаблону та багатомовні магазини",
        "answer": "Кожному шаблону можна призначити мову. Виберіть <strong>Усі мови</strong> для універсального тексту або конкретну мову для перекладених повідомлень клієнту. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Власні поля і метадані",
        "answer": "Заповнювачі <code>{order_meta:meta_key}</code> і <code>{customer_meta:meta_key}</code> вставляють вибрані метадані. Чутливі ключі, такі як password, token або secret, блокуються. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Внутрішні та клієнтські нотатки",
        "answer": "Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу. Ця довідка описує повний процес: створення форматованих шаблонів, використання заповнювачів, додавання нотаток у замовлення WooCommerce, імпорт/експорт JSON, права, HPOS і безпечну роботу. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Сторінка налаштувань",
        "answer": "Відкрийте <strong>Mailhilfe Order Notes → Налаштування</strong>, щоб визначити стандартну поведінку плагіна. Можна вибрати тип нотатки, дозволити HTML, керувати попередженнями для нотаток клієнту, лічильниками використання та імпортом JSON. Для безпечної щоденної роботи використовуйте внутрішні нотатки за замовчуванням. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Попередній перегляд імпорту",
        "answer": "Імпорт JSON спочатку показує, які шаблони буде створено, оновлено або пропущено. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Дублювання та редакції",
        "answer": "Дублювання створює копію як чернетку. Редакції WordPress допомагають порівнювати та відновлювати старі версії. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Дозволи",
        "answer": "Сторінка <strong>Дозволи</strong> визначає, які ролі керують шаблонами, а які використовують їх у замовленнях. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Рекомендований порядок",
        "answer": "Виберіть шаблон, перевірте попередній перегляд, відредагуйте за потреби, підтвердьте тип нотатки й додайте її. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Чи можна використовувати плагін у staging-магазині?",
        "answer": "Так. Використовуйте JSON-імпорт/експорт і тестове замовлення, щоб перевірити шаблони, умови, заповнювачі й пошту до робочого сайту."
      },
      {
        "question": "Які дані видаляються під час деінсталяції?",
        "answer": "Видаляються шаблони, категорії, налаштування, таблиця історії, особисте обране й нещодавні шаблони. Наявні нотатки WooCommerce залишаються в історії замовлень."
      },
      {
        "question": "Як повідомити про проблему?",
        "answer": "Вкажіть версії WordPress, WooCommerce, PHP і плагіна, HPOS, мову, точні кроки й помилку. Додайте діагностику без персональних даних клієнтів."
      },
      {
        "question": "Умови шаблонів",
        "answer": "Умови визначають, чи доступний шаблон для певного замовлення. Можна обмежити за статусом, способом оплати, способом доставки, країною виставлення рахунку та мінімальною або максимальною сумою. Усі задані умови мають збігатися. Залиште поле порожнім, якщо воно не повинно обмежувати шаблон."
      },
      {
        "question": "Журнал обробки електронної пошти",
        "answer": "Для клієнтських нотаток плагін записує повідомлення WooCommerce про обробку листа та технічні помилки wp_mail. «Оброблено» означає передачу поштовій системі, але не підтверджує остаточну доставку або прочитання. Переглядайте оброблені й невдалі події на сторінці історії."
      },
      {
        "question": "Центральна історія",
        "answer": "Відкрийте <strong>Mailhilfe Order Notes → Історія</strong>, щоб переглянути створення нотаток, використання шаблонів, обробку та помилки листів. За наявності показуються замовлення, шаблон, користувач, отримувач, тип події та час. Використовуйте для підтримки, аудиту й діагностики."
      },
      {
        "question": "Попередній перегляд із тестовим замовленням",
        "answer": "У редакторі введіть ID замовлення WooCommerce. Поточний вміст, включно з незбереженими змінами, буде показано з даними замовлення без створення нотатки або надсилання листа. Використовуйте тестове замовлення або staging-сайт."
      },
      {
        "question": "Особисте обране та нещодавні шаблони",
        "answer": "Кожен користувач може позначати особисте обране в замовленні. Плагін також зберігає десять останніх успішно використаних шаблонів і піднімає їх вище. Глобальне обране залишається спільним. Особисте обране не впливає на інших користувачів."
      },
      {
        "question": "Сторінка діагностики",
        "answer": "Відкрийте <strong>Mailhilfe Order Notes → Діагностика</strong>, щоб побачити версії WordPress, PHP і WooCommerce, статус HPOS, лист клієнтської нотатки, мову, кількість шаблонів, кеш і WP_DEBUG. Додавайте ці дані до запиту підтримки."
      },
      {
        "question": "Хуки й фільтри для розробників",
        "answer": "Плагін має хуки й фільтри для заповнювачів, значень, дозволених мета-ключів, результатів шаблонів, умов, перегляду, фінального вмісту, дій до/після додавання, історії та діагностики. Назви описані в readme.txt. Перевіряйте, очищуйте й екрануйте власні дані."
      }
    ]
  },
  "sv_SE": {
    "menu": "FAQ",
    "title": "Vanliga frågor",
    "intro": "Svar på vanliga frågor om Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Du har inte behörighet att hantera notismallar.",
    "items": [
      {
        "question": "Vad används tillägget till?",
        "answer": "Tillägget skapar återanvändbara WooCommerce-mallar för ordernotiser. Personal kan välja en mall i en order, granska förhandsvisningen och lägga till den som intern notis eller kundnotis."
      },
      {
        "question": "Var hanteras mallarna?",
        "answer": "I menyn för ordernotismallar kan du skapa, redigera, ta bort, kategorisera, favoritmarkera och sortera mallar."
      },
      {
        "question": "Hur fungerar platshållare?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} och {items} ersätts med riktiga orderdata."
      },
      {
        "question": "Kan texten formateras?",
        "answer": "Ja. Stycken, fetstil, kursiv stil, listor och länkar stöds. Osäker HTML tas bort."
      },
      {
        "question": "Intern notis eller kundnotis?",
        "answer": "Interna notiser är för butiksteamet. Kundnotiser kan visas för kunden och skicka WooCommerce-e-post."
      },
      {
        "question": "Är det HPOS-kompatibelt?",
        "answer": "Ja. Tillägget använder WooCommerce order-API:er och deklarerar HPOS-kompatibilitet."
      },
      {
        "question": "Hur fungerar JSON?",
        "answer": "Export skapar en JSON-fil, import återställer mallar eller flyttar dem till en annan butik."
      },
      {
        "question": "Läggs offentliga länkar till?",
        "answer": "Nej. Tillägget lägger inte till powered-by- eller reklam­länkar i butiken."
      },
      {
        "question": "Kontrollera förhandsvisning",
        "answer": "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning. Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning. Use clear titles and categories."
      },
      {
        "question": "E-poststatus för kundnotis",
        "answer": "När en kundnotis väljs visar pluginet en varning och försöker visa om WooCommerce-e-post för kundnotiser är aktiv. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Mallens språk och flerspråkiga butiker",
        "answer": "Varje mall kan ha ett språk. Välj <strong>Alla språk</strong> för generell text eller ett specifikt språk för översatta kundmeddelanden. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Egna fält och metadata",
        "answer": "Platshållarna <code>{order_meta:meta_key}</code> och <code>{customer_meta:meta_key}</code> infogar vald metadata. Känsliga nycklar som password, token eller secret blockeras. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Interna notiser och kundnotiser",
        "answer": "Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning. Den här hjälpen beskriver hela arbetsflödet: skapa formaterade mallar, använda platshållare, lägga till notiser i WooCommerce-order, importera/exportera JSON, behörigheter, HPOS och säker användning. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Inställningssida",
        "answer": "Öppna <strong>Mailhilfe Order Notes → Inställningar</strong> för att ange pluginets standardbeteende. Du kan välja standardtyp för notis, tillåta HTML, styra varningar för kundnotiser, användningsräknare och JSON-import. Använd interna notiser som standard för säkrare dagligt arbete. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Importförhandsvisning",
        "answer": "JSON-import visar först vilka mallar som skapas, uppdateras eller hoppas över. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Duplicera och revideringar",
        "answer": "Duplicering skapar en kopia som utkast. WordPress-revideringar hjälper dig jämföra och återställa äldre versioner. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Behörigheter",
        "answer": "Sidan <strong>Behörigheter</strong> anger vilka roller som hanterar mallar och vilka som använder dem i ordrar. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Rekommenderat arbetsflöde",
        "answer": "Välj en mall, granska förhandsvisningen, redigera vid behov, kontrollera notistypen och lägg till notisen. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Kan jag använda tillägget i en stagingbutik?",
        "answer": "Ja. Använd JSON-import/export och en testorder för att kontrollera mallar, villkor, platshållare och e-post före produktion."
      },
      {
        "question": "Vilka data tas bort vid avinstallation?",
        "answer": "Mallar, kategorier, inställningar, historiktabell, personliga favoriter och senaste mallar tas bort. Befintliga WooCommerce-orderanteckningar finns kvar i orderhistoriken."
      },
      {
        "question": "Hur rapporterar jag ett problem?",
        "answer": "Ange versioner av WordPress, WooCommerce, PHP och tillägget, HPOS, språk, exakta steg och fel. Bifoga diagnostik utan kunders personuppgifter."
      },
      {
        "question": "Villkor för mallar",
        "answer": "Villkor avgör om en mall är tillgänglig för en order. Du kan begränsa efter orderstatus, betalningsmetod, leveransmetod, faktureringsland och minsta eller högsta orderbelopp. Alla angivna villkor måste stämma. Lämna ett fält tomt om det inte ska begränsa mallen."
      },
      {
        "question": "Logg för e-postbearbetning",
        "answer": "För kundnotiser registrerar tillägget när WooCommerce rapporterar att e-post har bearbetats samt tekniska wp_mail-fel. ”Bearbetad” betyder överlämnad till e-postsystemet, inte slutlig leverans eller läsning. Se sidan Historik för bearbetade och misslyckade händelser."
      },
      {
        "question": "Central historik",
        "answer": "Öppna <strong>Mailhilfe Order Notes → Historik</strong> för skapade notiser, mallanvändning, e-postbearbetning och fel. Om tillgängligt visas order, mall, användare, mottagare, händelsetyp och tid. Använd historiken för support, granskning och felsökning."
      },
      {
        "question": "Förhandsvisning med testorder",
        "answer": "Ange ett WooCommerce-order-ID i mallredigeraren. Aktuellt innehåll, även osparade ändringar, visas med orderdata utan att skapa en notis eller skicka e-post. Använd en testorder eller stagingmiljö."
      },
      {
        "question": "Personliga favoriter och nyligen använda mallar",
        "answer": "Varje användare kan markera personliga favoriter på ordersidan. Tillägget sparar också de tio senast framgångsrikt använda mallarna och placerar dem högre. Globala favoriter är fortsatt gemensamma. Personliga favoriter påverkar inte andra användare."
      },
      {
        "question": "Diagnossida",
        "answer": "Öppna <strong>Mailhilfe Order Notes → Diagnos</strong> för WordPress-, PHP- och WooCommerce-versioner, HPOS, kundnotis-e-post, språk, antal mallar, cache och WP_DEBUG. Ange dessa uppgifter vid supportförfrågningar."
      },
      {
        "question": "Hooks och filter för utvecklare",
        "answer": "Tillägget har hooks och filter för platshållare, värden, tillåtna metanycklar, mallresultat, villkor, förhandsvisning, slutligt innehåll, åtgärder före/efter tillägg, historik och diagnos. Namnen dokumenteras i readme.txt. Validera, sanera och escape:a egna data."
      }
    ]
  },
  "da_DK": {
    "menu": "FAQ",
    "title": "Ofte stillede spørgsmål",
    "intro": "Svar på almindelige spørgsmål om Mailhilfe Order Note Manager for WooCommerce.",
    "permission": "Du har ikke tilladelse til at administrere noteskabeloner.",
    "items": [
      {
        "question": "Hvad bruges pluginet til?",
        "answer": "Pluginet opretter genanvendelige WooCommerce-skabeloner til ordrenoter. Medarbejdere kan vælge en skabelon i en ordre, se forhåndsvisning og tilføje den som intern note eller kundenote."
      },
      {
        "question": "Hvor administreres skabeloner?",
        "answer": "I menuen for ordrenoteskabeloner kan du oprette, redigere, slette, kategorisere, markere som favorit og sortere skabeloner."
      },
      {
        "question": "Hvordan virker pladsholdere?",
        "answer": "{order_number}, {customer}, {billing_email}, {order_total} og {items} erstattes med rigtige ordredata."
      },
      {
        "question": "Kan tekst formateres?",
        "answer": "Ja. Afsnit, fed, kursiv, lister og links understøttes. Usikker HTML fjernes."
      },
      {
        "question": "Intern note eller kundenote?",
        "answer": "Interne noter er til butiksteamet. Kundenoter kan være synlige for kunden og sende WooCommerce-mails."
      },
      {
        "question": "Er det HPOS-kompatibelt?",
        "answer": "Ja. Pluginet bruger WooCommerce ordre-API’er og erklærer HPOS-kompatibilitet."
      },
      {
        "question": "Hvordan fungerer JSON?",
        "answer": "Eksport opretter en JSON-fil, import gendanner skabeloner eller flytter dem til en anden butik."
      },
      {
        "question": "Tilføjes offentlige links?",
        "answer": "Nej. Pluginet tilføjer ikke powered-by- eller reklamelinks i frontend."
      },
      {
        "question": "Kontrollér forhåndsvisning",
        "answer": "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug. Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug. Use clear titles and categories."
      },
      {
        "question": "E-mailstatus for kundenote",
        "answer": "Når en kundenote vælges, viser pluginet en advarsel og forsøger at vise, om WooCommerce-e-mailen for kundenoter er aktiv. If that email is active, WooCommerce may notify the customer. The plugin can also add an internal protocol note when a customer notification is created. Always check the editable preview."
      },
      {
        "question": "Skabelonsprog og flersprogede butikker",
        "answer": "Hver skabelon kan have et sprog. Vælg <strong>Alle sprog</strong> til generelle tekster eller et bestemt sprog til oversatte kundebeskeder. For multilingual shops, create separate customer-facing templates for each important shop language and keep internal templates language-neutral when possible. Use “All languages” for staff-only notes."
      },
      {
        "question": "Brugerdefinerede felter og metadata",
        "answer": "Pladsholderne <code>{order_meta:meta_key}</code> og <code>{customer_meta:meta_key}</code> indsætter valgte metadata. Følsomme nøgler som password, token eller secret blokeres. Use meta placeholders carefully because they can expose data from other plugins. Check every customer note preview before saving. Example: <code>{order_meta:_tracking_number}</code>."
      },
      {
        "question": "Interne noter og kundenoter",
        "answer": "Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug. Denne hjælp forklarer hele arbejdsgangen: opret formaterede skabeloner, brug pladsholdere, tilføj noter i WooCommerce-ordrer, importer/eksporter JSON, tilladelser, HPOS og sikker brug. <strong>Internal note</strong> / <strong>Customer note</strong>"
      },
      {
        "question": "Indstillingsside",
        "answer": "Åbn <strong>Mailhilfe Order Notes → Indstillinger</strong> for at angive pluginets standardadfærd. Du kan vælge standardnotetype, tillade HTML, styre advarsler for kundenoter, brugstællere og JSON-import. Brug interne noter som standard for et mere sikkert dagligt workflow. Review these settings after installing the plugin or changing staff roles."
      },
      {
        "question": "Importforhåndsvisning",
        "answer": "JSON-import viser først, hvilke skabeloner der oprettes, opdateres eller springes over. Confirm the import only after checking the preview and create an export backup before importing larger template sets. New templates are listed separately from updates."
      },
      {
        "question": "Duplikering og revisioner",
        "answer": "Duplikering opretter en kopi som kladde. WordPress-revisioner hjælper med at sammenligne og gendanne tidligere versioner. Use duplicate templates for similar shipping, payment or support messages and keep titles easy to distinguish. The duplicate starts as a draft."
      },
      {
        "question": "Rettigheder",
        "answer": "Siden <strong>Rettigheder</strong> bestemmer, hvilke roller der administrerer skabeloner, og hvilke der bruger dem i ordrer. Give import/export and management rights only to trusted users. Users who only work in orders usually need only the permission to use templates. Manage templates: create, edit, delete, import and export."
      },
      {
        "question": "Anbefalet arbejdsgang",
        "answer": "Vælg en skabelon, kontroller forhåndsvisningen, rediger om nødvendigt, bekræft notetypen og tilføj noten. For new templates, test placeholders and formatting before using them in real customer communication. Internal notes are for staff information."
      },
      {
        "question": "Kan pluginet bruges i en stagingbutik?",
        "answer": "Ja. Brug JSON-import/eksport og en testordre til at kontrollere skabeloner, betingelser, pladsholdere og e-mail før produktion."
      },
      {
        "question": "Hvilke data fjernes ved afinstallation?",
        "answer": "Skabeloner, kategorier, indstillinger, historiktabel, personlige favoritter og seneste skabeloner fjernes. Eksisterende WooCommerce-ordrenoter forbliver i ordrehistorikken."
      },
      {
        "question": "Hvordan rapporterer jeg et problem?",
        "answer": "Angiv WordPress-, WooCommerce-, PHP- og pluginversion, HPOS, sprog, præcise trin og fejl. Medtag diagnoseværdier uden kunders persondata."
      },
      {
        "question": "Betingelser for skabeloner",
        "answer": "Betingelser afgør, om en skabelon er tilgængelig for en ordre. Den kan begrænses efter ordrestatus, betalingsmetode, leveringsmetode, faktureringsland og minimum eller maksimum total. Alle udfyldte betingelser skal passe. Lad et felt være tomt, hvis det ikke skal begrænse skabelonen."
      },
      {
        "question": "Log over e-mailbehandling",
        "answer": "For kundenoter registrerer pluginet, når WooCommerce melder e-mailen behandlet, samt tekniske wp_mail-fejl. “Behandlet” bekræfter overførsel til mailsystemet, ikke endelig levering eller læsning. Se siden Historik for behandlede og mislykkede hændelser."
      },
      {
        "question": "Central historik",
        "answer": "Åbn <strong>Mailhilfe Order Notes → Historik</strong> for oprettede noter, skabelonbrug, e-mailbehandling og fejl. Når de findes, vises ordre, skabelon, bruger, modtager, hændelsestype og tidspunkt. Brug historikken til support, kontrol og fejlfinding."
      },
      {
        "question": "Forhåndsvisning med testordre",
        "answer": "Indtast et WooCommerce-ordre-ID i skabeloneditoren. Det aktuelle indhold, også ikke-gemte ændringer, vises med ordredata uden at oprette en note eller sende e-mail. Brug en testordre eller staging-side."
      },
      {
        "question": "Personlige favoritter og senest brugte skabeloner",
        "answer": "Hver bruger kan markere personlige favoritter på ordresiden. Pluginet gemmer også de ti senest anvendte skabeloner pr. bruger og viser dem højere. Globale favoritter er fortsat fælles. Personlige favoritter påvirker ikke andre brugere."
      },
      {
        "question": "Diagnoseside",
        "answer": "Åbn <strong>Mailhilfe Order Notes → Diagnose</strong> for WordPress-, PHP- og WooCommerce-versioner, HPOS, kundenote-e-mail, sprog, antal skabeloner, cache og WP_DEBUG. Medtag disse oplysninger ved support."
      },
      {
        "question": "Hooks og filtre for udviklere",
        "answer": "Pluginet tilbyder hooks og filtre for pladsholdere, værdier, tilladte metanøgler, skabelonresultater, betingelser, forhåndsvisning, endeligt indhold, handlinger før/efter tilføjelse, historik og diagnose. Navnene er dokumenteret i readme.txt. Validér, rens og escape egne data."
      }
    ]
  }
}
JSON;

		$sets = json_decode( $json, true );
		return is_array( $sets ) ? $sets : array();
	}
}
