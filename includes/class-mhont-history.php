<?php
/**
 * Central usage and email delivery history.
 *
 * @package Mailhilfe_Order_Note_Manager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class MHONT_History {
	const TABLE_SUFFIX = 'mhont_history';
	const OPTION_DB_VERSION = 'mhont_history_db_version';
	const DB_VERSION = '1.0';
	private static $pending_customer_note = array();
	private static $email_hooks_registered = false;
	private static $mail_failure_handled = array();

	public static function hooks() {
		if ( self::DB_VERSION !== get_option( self::OPTION_DB_VERSION ) ) {
			self::install();
		}
		self::email_hooks();
	}

	/**
	 * Registers lightweight mail hooks on every request, including cron.
	 *
	 * @return void
	 */
	public static function email_hooks() {
		if ( self::$email_hooks_registered ) {
			return;
		}

		add_action( 'woocommerce_email_sent', array( __CLASS__, 'log_woocommerce_email' ), 10, 3 );
		add_action( 'wp_mail_failed', array( __CLASS__, 'log_wp_mail_failure' ) );
		self::$email_hooks_registered = true;
	}

	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_SUFFIX;
	}

	/**
	 * Creates or upgrades the history table.
	 *
	 * The database version is stored only after the table can be verified. This
	 * prevents a failed dbDelta() call from permanently suppressing later repair
	 * attempts.
	 *
	 * @return bool Whether the table is available.
	 */
	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();
		$table = self::table_name();
		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			created_at datetime NOT NULL,
			order_id bigint(20) unsigned NOT NULL DEFAULT 0,
			template_id bigint(20) unsigned NOT NULL DEFAULT 0,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			event_type varchar(40) NOT NULL,
			status varchar(30) NOT NULL,
			recipient varchar(190) NOT NULL DEFAULT '',
			details text NULL,
			PRIMARY KEY  (id),
			KEY order_id (order_id),
			KEY template_id (template_id),
			KEY event_type (event_type),
			KEY created_at (created_at)
		) {$charset};";
		dbDelta( $sql );

		$found_table = $wpdb->get_var(
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) )
		);
		if ( $table === $found_table ) {
			update_option( self::OPTION_DB_VERSION, self::DB_VERSION, false );
			return true;
		}

		delete_option( self::OPTION_DB_VERSION );
		return false;
	}

	public static function record( $event_type, $status, $order_id = 0, $template_id = 0, $details = array(), $recipient = '' ) {
		global $wpdb;

		if ( self::DB_VERSION !== get_option( self::OPTION_DB_VERSION ) && ! self::install() ) {
			return 0;
		}
		$details_json = wp_json_encode(
			is_array( $details ) ? $details : array( 'message' => (string) $details ),
			JSON_INVALID_UTF8_SUBSTITUTE
		);
		if ( false === $details_json ) {
			$details_json = '{}';
		}

		$data = array(
			'created_at'  => current_time( 'mysql' ),
			'order_id'    => absint( $order_id ),
			'template_id' => absint( $template_id ),
			'user_id'     => get_current_user_id(),
			'event_type'  => sanitize_key( $event_type ),
			'status'      => sanitize_key( $status ),
			'recipient'   => sanitize_email( $recipient ),
			'details'     => $details_json,
		);
		$inserted = $wpdb->insert( self::table_name(), $data, array( '%s','%d','%d','%d','%s','%s','%s','%s' ) );
		if ( false === $inserted ) {
			// Retry table installation on the next event if the table was removed or
			// could not be written after an otherwise successful activation.
			delete_option( self::OPTION_DB_VERSION );
			return 0;
		}

		$insert_id = (int) $wpdb->insert_id;
		do_action( 'mailhilfe_order_note_history_recorded', $data, $insert_id );
		return $insert_id;
	}

	public static function mark_pending_customer_note( $order_id, $template_id ) {
		$order_id = absint( $order_id );
		if ( $order_id ) {
			unset( self::$mail_failure_handled[ $order_id ] );
		}
		self::$pending_customer_note = array(
			'order_id'    => $order_id,
			'template_id' => absint( $template_id ),
		);
	}

	/**
	 * Clears the request-local customer-note context.
	 *
	 * @return void
	 */
	public static function clear_pending_customer_note() {
		self::$pending_customer_note = array();
	}

	public static function log_woocommerce_email( $sent, $email_id, $email ) {
		if ( 'customer_note' !== (string) $email_id ) {
			return;
		}

		$order_id  = 0;
		$recipient = '';
		if ( is_object( $email ) ) {
			if ( isset( $email->object ) && is_a( $email->object, 'WC_Order' ) ) {
				$order_id = absint( $email->object->get_id() );
			}
			if ( method_exists( $email, 'get_recipient' ) ) {
				$recipient = (string) $email->get_recipient();
			}
		}

		$template_id = 0;
		if ( ! empty( self::$pending_customer_note['order_id'] ) && (int) self::$pending_customer_note['order_id'] === $order_id ) {
			$template_id = absint( self::$pending_customer_note['template_id'] );
		}

		if ( $order_id && isset( self::$mail_failure_handled[ $order_id ] ) ) {
			unset( self::$mail_failure_handled[ $order_id ] );
			if ( ! $sent ) {
				self::clear_pending_customer_note();
				return;
			}
		}

		self::record(
			'email',
			$sent ? 'processed' : 'failed',
			$order_id,
			$template_id,
			array(
				'email_id' => (string) $email_id,
				'meaning'  => 'WooCommerce mail handler result; not a delivery receipt.',
			),
			$recipient
		);

		self::clear_pending_customer_note();
	}

	public static function log_wp_mail_failure( $error ) {
		if ( ! is_wp_error( $error ) || empty( self::$pending_customer_note['order_id'] ) ) {
			return;
		}

		$data      = $error->get_error_data();
		$recipient = '';
		if ( is_array( $data ) && ! empty( $data['to'] ) ) {
			$to        = is_array( $data['to'] ) ? reset( $data['to'] ) : $data['to'];
			$recipient = is_string( $to ) ? $to : '';
		}

		$order_id    = absint( self::$pending_customer_note['order_id'] );
		$template_id = absint( self::$pending_customer_note['template_id'] );

		self::record(
			'mail_error',
			'failed',
			$order_id,
			$template_id,
			array( 'message' => $error->get_error_message() ),
			$recipient
		);

		self::$mail_failure_handled[ $order_id ] = true;
		self::clear_pending_customer_note();
	}

	/**
	 * Get a display label and HPOS-compatible edit URL for an order.
	 *
	 * @param int $order_id Order ID.
	 * @return array{label:string,url:string}
	 */
	private static function get_order_link_data( $order_id ) {
		static $cache = array();

		$order_id = absint( $order_id );
		if ( ! $order_id ) {
			return array( 'label' => '', 'url' => '' );
		}

		if ( isset( $cache[ $order_id ] ) ) {
			return $cache[ $order_id ];
		}

		$data = array(
			'label' => '#' . $order_id,
			'url'   => '',
		);

		if ( function_exists( 'wc_get_order' ) ) {
			try {
				$order = wc_get_order( $order_id );
				if ( $order ) {
					$order_number = (string) $order->get_order_number();
					if ( '' !== $order_number ) {
						$data['label'] = '#' . $order_number;
					}

					if ( ( current_user_can( 'edit_shop_orders', $order_id ) || current_user_can( 'edit_post', $order_id ) ) && method_exists( $order, 'get_edit_order_url' ) ) {
						$data['url'] = (string) $order->get_edit_order_url();
					}
				}
			} catch ( Throwable $throwable ) {
				// Keep the numeric fallback when an order cannot be loaded.
			}
		}

		$cache[ $order_id ] = $data;
		return $data;
	}

	public static function render_page() {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) { wp_die( esc_html__( 'You are not allowed to view the history.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) ); }
		global $wpdb;
		$table = self::table_name();
		$rows  = $wpdb->get_results(
			$wpdb->prepare( 'SELECT * FROM %i ORDER BY id DESC LIMIT %d', $table, 250 )
		);

		if ( $rows ) {
			$user_ids     = array_values( array_unique( array_filter( array_map( 'absint', wp_list_pluck( $rows, 'user_id' ) ) ) ) );
			$template_ids = array_values( array_unique( array_filter( array_map( 'absint', wp_list_pluck( $rows, 'template_id' ) ) ) ) );
			if ( $user_ids ) {
				cache_users( $user_ids );
			}
			if ( $template_ids ) {
				_prime_post_caches( $template_ids, false, false );
			}
		}
		?>
		<div class="wrap"><h1><?php esc_html_e( 'Order note history', 'mailhilfe-order-note-manager' ); ?></h1>
		<p><?php esc_html_e( 'The email status records whether WooCommerce/WordPress processed the send request. It is not proof that the recipient opened or received the message.', 'mailhilfe-order-note-manager' ); ?></p>
		<table class="widefat striped"><thead><tr><th><?php esc_html_e( 'Date', 'mailhilfe-order-note-manager' ); ?></th><th><?php esc_html_e( 'Event', 'mailhilfe-order-note-manager' ); ?></th><th><?php esc_html_e( 'Status', 'mailhilfe-order-note-manager' ); ?></th><th><?php esc_html_e( 'Order', 'mailhilfe-order-note-manager' ); ?></th><th><?php esc_html_e( 'Template', 'mailhilfe-order-note-manager' ); ?></th><th><?php esc_html_e( 'User', 'mailhilfe-order-note-manager' ); ?></th><th><?php esc_html_e( 'Recipient', 'mailhilfe-order-note-manager' ); ?></th></tr></thead><tbody>
		<?php if ( $rows ) : foreach ( $rows as $row ) : $user = $row->user_id ? get_userdata( $row->user_id ) : false; $order_data = self::get_order_link_data( $row->order_id ); ?>
		<tr><td><?php echo esc_html( $row->created_at ); ?></td><td><?php echo esc_html( $row->event_type ); ?></td><td><?php echo esc_html( $row->status ); ?></td><td><?php if ( $order_data['url'] ) : ?><a href="<?php echo esc_url( $order_data['url'] ); ?>"><?php echo esc_html( $order_data['label'] ); ?></a><?php else : echo esc_html( $order_data['label'] ); endif; ?></td><td><?php echo esc_html( $row->template_id ? get_the_title( $row->template_id ) : '' ); ?></td><td><?php echo esc_html( $user ? $user->display_name : '' ); ?></td><td><?php echo esc_html( $row->recipient ); ?></td></tr>
		<?php endforeach; else : ?><tr><td colspan="7"><?php esc_html_e( 'No history entries found.', 'mailhilfe-order-note-manager' ); ?></td></tr><?php endif; ?>
		</tbody></table></div><?php
	}
}
