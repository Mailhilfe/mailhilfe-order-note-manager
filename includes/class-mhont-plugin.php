<?php
/**
 * Main plugin bootstrap.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
final class MHONT_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var MHONT_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Whether plugin dependencies have been loaded.
	 *
	 * @var bool
	 */
	private static $dependencies_loaded = false;

	/** @var bool */
	private static $order_components_loaded = false;

	/**
	 * Loads plugin classes only when an admin, AJAX or activation request needs them.
	 *
	 * The plugin has no public-facing output, so avoiding these files on normal
	 * frontend requests reduces PHP parsing and memory usage.
	 *
	 * @return void
	 */
	private static function load_dependencies() {
		if ( self::$dependencies_loaded ) {
			return;
		}

		$files = array(
			'class-mhont-capabilities.php',
			'class-mhont-settings.php',
			'class-mhont-post-types.php',
			'class-mhont-template-cache.php',
			'class-mhont-history.php',
		);


		foreach ( $files as $file ) {
			require_once MHONT_PATH . 'includes/' . $file;
		}

		self::$dependencies_loaded = true;
	}

	/**
	 * Returns singleton instance.
	 *
	 * @return MHONT_Plugin
	 */
	public static function instance() {
		self::load_dependencies();

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_translations' ), 0 );
		add_action( 'init', array( 'MHONT_Post_Types', 'register' ) );
		add_action( 'admin_init', array( 'MHONT_Capabilities', 'maybe_add_caps' ) );

		MHONT_Settings::hooks();
		MHONT_Post_Types::hooks();
		MHONT_Template_Cache::hooks();
		MHONT_History::hooks();

		if ( wp_doing_ajax() ) {
			self::load_order_components();
		} else {
			add_action( 'current_screen', array( $this, 'load_screen_components' ), 1 );
		}
		if ( ! wp_doing_ajax() ) {
			add_action( 'admin_menu', array( $this, 'register_lazy_admin_pages' ) );
			add_action( 'admin_post_mhont_export_json', array( $this, 'handle_export_json' ) );
			add_action( 'admin_post_mhont_import_json', array( $this, 'handle_import_json' ) );
			add_action( 'admin_post_mhont_confirm_import_json', array( $this, 'handle_confirm_import_json' ) );
			add_action( 'admin_post_mhont_install_demo_templates', array( $this, 'handle_install_demo_templates' ) );
		}
	}

	/**
	 * Loads order and placeholder classes only on screens that use them.
	 *
	 * @return void
	 */
	private static function load_order_components() {
		if ( self::$order_components_loaded ) {
			return;
		}

		require_once MHONT_PATH . 'includes/class-mhont-placeholders.php';
		require_once MHONT_PATH . 'includes/class-mhont-order-ui.php';
		MHONT_Order_UI::hooks();
		self::$order_components_loaded = true;
	}

	/**
	 * Loads screen-specific classes after WordPress has resolved the admin screen.
	 *
	 * @param WP_Screen $screen Current screen.
	 * @return void
	 */
	public function load_screen_components( $screen ) {
		if ( ! $screen || ! is_object( $screen ) ) {
			return;
		}

		$screen_id   = isset( $screen->id ) ? (string) $screen->id : '';
		$post_type   = isset( $screen->post_type ) ? (string) $screen->post_type : '';
		$is_order    = false !== strpos( $screen_id, 'shop_order' ) || false !== strpos( $screen_id, 'shop-order' ) || false !== strpos( $screen_id, 'wc-orders' );
		$is_template = MHONT_Post_Types::POST_TYPE === $post_type;

		if ( $is_order || $is_template ) {
			self::load_order_components();
		}

		if ( 'plugins' === $screen_id ) {
			self::load_optional_admin_class( 'class-mhont-admin.php', 'MHONT_Admin' );
			MHONT_Admin::hooks();
		}
	}

	/**
	 * Loads one optional admin class on demand.
	 *
	 * @param string $file  File name inside includes.
	 * @param string $class Expected class name.
	 * @return void
	 */
	private static function load_optional_admin_class( $file, $class ) {
		if ( ! class_exists( $class, false ) ) {
			require_once MHONT_PATH . 'includes/' . $file;
		}
	}

	/**
	 * Registers lightweight submenu entries whose large page classes are lazy-loaded.
	 *
	 * @return void
	 */
	public function register_lazy_admin_pages() {
		$parent = 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE;

		add_submenu_page(
			$parent,
			__( 'Mailhilfe Order Note Manager Import/Export', 'mailhilfe-order-note-manager' ),
			__( 'Template Import/Export', 'mailhilfe-order-note-manager' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-import-export',
			array( $this, 'render_import_export_page' )
		);
		add_submenu_page(
			$parent,
			__( 'Help', 'mailhilfe-order-note-manager' ),
			__( 'Help', 'mailhilfe-order-note-manager' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-help',
			array( $this, 'render_help_page' )
		);
		add_submenu_page(
			$parent,
			__( 'FAQ', 'mailhilfe-order-note-manager' ),
			__( 'FAQ', 'mailhilfe-order-note-manager' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-faq',
			array( $this, 'render_faq_page' )
		);
		add_submenu_page(
			$parent,
			__( 'Order note history', 'mailhilfe-order-note-manager' ),
			__( 'History', 'mailhilfe-order-note-manager' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-history',
			array( $this, 'render_history_page' )
		);
		add_submenu_page(
			$parent,
			__( 'Diagnostics', 'mailhilfe-order-note-manager' ),
			__( 'Diagnostics', 'mailhilfe-order-note-manager' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-diagnostics',
			array( $this, 'render_diagnostics_page' )
		);
	}

	/** @return void */
	public function render_import_export_page() {
		self::load_optional_admin_class( 'class-mhont-import-export.php', 'MHONT_Import_Export' );
		MHONT_Import_Export::render_page();
	}

	/** @return void */
	public function render_help_page() {
		self::load_optional_admin_class( 'class-mhont-help.php', 'MHONT_Help' );
		MHONT_Help::render_page();
	}

	/** @return void */
	public function render_faq_page() {
		self::load_optional_admin_class( 'class-mhont-faq.php', 'MHONT_FAQ' );
		MHONT_FAQ::render_page();
	}


	/** @return void */
	public function render_history_page() {
		MHONT_History::render_page();
	}

	/** @return void */
	public function render_diagnostics_page() {
		self::load_optional_admin_class( 'class-mhont-diagnostics.php', 'MHONT_Diagnostics' );
		MHONT_Diagnostics::render_page();
	}

	/** @return void */
	public function handle_export_json() {
		self::load_optional_admin_class( 'class-mhont-import-export.php', 'MHONT_Import_Export' );
		MHONT_Import_Export::export_json();
	}

	/** @return void */
	public function handle_import_json() {
		self::load_optional_admin_class( 'class-mhont-import-export.php', 'MHONT_Import_Export' );
		MHONT_Import_Export::import_json();
	}

	/** @return void */
	public function handle_confirm_import_json() {
		self::load_optional_admin_class( 'class-mhont-import-export.php', 'MHONT_Import_Export' );
		MHONT_Import_Export::confirm_import_json();
	}

	/** @return void */
	public function handle_install_demo_templates() {
		self::load_optional_admin_class( 'class-mhont-import-export.php', 'MHONT_Import_Export' );
		MHONT_Import_Export::install_demo_templates_action();
	}

	/**
	 * Loads packaged translations for direct ZIP installations.
	 *
	 * WordPress.org language packs are loaded automatically when available.
	 * This fallback keeps bundled translations active in local/manual installs
	 * without using the discouraged plugin textdomain loader.
	 *
	 * @return void
	 */
	public function load_translations() {
		global $l10n;

		if ( isset( $l10n[ MHONT_TEXT_DOMAIN ] ) && ! $l10n[ MHONT_TEXT_DOMAIN ] instanceof NOOP_Translations ) {
			return;
		}

		$locales = array();

		$detected_locales = array( determine_locale(), get_user_locale(), get_locale() );
		foreach ( $detected_locales as $detected_locale ) {
			if ( is_string( $detected_locale ) && '' !== $detected_locale ) {
				$locales[] = $detected_locale;
			}
		}

		$locales = array_merge( $locales, self::get_locale_fallbacks( $locales ) );

		foreach ( array_unique( array_filter( $locales ) ) as $current_locale ) {
			$mofile = sprintf( '%1$s-%2$s.mo', MHONT_TEXT_DOMAIN, $current_locale );

			$global_mofile = trailingslashit( WP_LANG_DIR ) . 'plugins/' . $mofile;
			if ( file_exists( $global_mofile ) && load_textdomain( MHONT_TEXT_DOMAIN, $global_mofile ) ) {
				return;
			}

			$bundled_mofile = MHONT_PATH . 'languages/' . $mofile;
			if ( file_exists( $bundled_mofile ) && load_textdomain( MHONT_TEXT_DOMAIN, $bundled_mofile ) ) {
				return;
			}
		}
	}

	/**
	 * Returns fallbacks for the bundled German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese and Czech translation files.
	 *
	 * This covers German variants such as de_AT and de_CH and Spanish variants such as es_MX or es_AR and French variants such as fr_CA or fr_BE, Italian variants such as it_CH, Hindi variants such as hi, Russian variants such as ru_UA or ru_KZ, Portuguese variants such as pt_PT, and Simplified Chinese variants such as zh_SG or zh_Hans when no exact bundled catalog is available. Traditional Chinese locales are not mapped to the Simplified Chinese catalog. Japanese locales use the bundled `ja` catalog. Persian locales use the bundled `fa_IR` catalog. Vietnamese locales use the bundled `vi` catalog. Czech locales use the bundled `cs_CZ` catalog.
	 *
	 * @param array $locales Detected locale values.
	 * @return array
	 */
	private static function get_locale_fallbacks( $locales ) {
		$fallbacks = array();

		foreach ( $locales as $locale ) {
			if ( ! is_string( $locale ) || '' === $locale ) {
				continue;
			}

			$normalized = str_replace( '-', '_', $locale );
			$language   = strtolower( strtok( $normalized, '_' ) );
			if ( 'de' === $language ) {
				$fallbacks[] = 'de_DE';
			}
			if ( 'es' === $language ) {
				$fallbacks[] = 'es_ES';
			}
			if ( 'fr' === $language ) {
				$fallbacks[] = 'fr_FR';
			}
			if ( 'it' === $language ) {
				$fallbacks[] = 'it_IT';
			}
			if ( 'hi' === $language ) {
				$fallbacks[] = 'hi_IN';
			}
			if ( 'zh' === $language && in_array( strtolower( $normalized ), array( 'zh', 'zh_cn', 'zh_sg', 'zh_hans' ), true ) ) {
				$fallbacks[] = 'zh_CN';
			}
			if ( 'ja' === $language ) {
				$fallbacks[] = 'ja';
			}
			if ( 'nl' === $language ) {
				$fallbacks[] = 'nl_NL';
			}
			if ( 'pl' === $language ) {
				$fallbacks[] = 'pl_PL';
			}
			if ( 'tr' === $language ) {
				$fallbacks[] = 'tr_TR';
			}
			if ( 'fa' === $language ) {
				$fallbacks[] = 'fa_IR';
			}
			if ( 'vi' === $language ) {
				$fallbacks[] = 'vi';
			}
			if ( 'cs' === $language ) {
				$fallbacks[] = 'cs_CZ';
			}
			if ( 'ru' === $language ) {
				$fallbacks[] = 'ru_RU';
			}
			if ( 'pt' === $language ) {
				$fallbacks[] = 'pt_BR';
			}
		}

		return $fallbacks;
	}

	/**
	 * Runs on activation.
	 *
	 * @return void
	 */
	public static function activate() {
		self::load_dependencies();
		MHONT_Post_Types::register();
		MHONT_Capabilities::add_caps();
		MHONT_History::install();
		if ( MHONT_Settings::enabled( 'install_demo_on_activation' ) ) {
			self::load_optional_admin_class( 'class-mhont-import-export.php', 'MHONT_Import_Export' );
			MHONT_Import_Export::maybe_install_demo_templates();
		}
		flush_rewrite_rules();
	}

	/**
	 * Runs on deactivation.
	 *
	 * @return void
	 */
	public static function deactivate() {
		self::load_dependencies();
		flush_rewrite_rules();
	}
}
