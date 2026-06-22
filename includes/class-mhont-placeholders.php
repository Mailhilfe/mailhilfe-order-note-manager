<?php
/**
 * Placeholder replacement.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replaces placeholders in templates.
 */
final class MHONT_Placeholders {

	/**
	 * Returns all available placeholders with human-readable labels.
	 *
	 * @return array<string,string>
	 */
	public static function get_definitions() {
		static $definitions = null;
		if ( null !== $definitions ) {
			$definitions = apply_filters( 'mailhilfe_order_note_placeholders', $definitions );
		return is_array( $definitions ) ? $definitions : array();
		}

		$definitions = array(
			'{order_id}'            => __( 'Order ID', 'mailhilfe-order-note-manager' ),
			'{order_number}'        => __( 'Order number', 'mailhilfe-order-note-manager' ),
			'{order_status}'        => __( 'Order status', 'mailhilfe-order-note-manager' ),
			'{order_date}'          => __( 'Order date', 'mailhilfe-order-note-manager' ),
			'{order_time}'          => __( 'Order time', 'mailhilfe-order-note-manager' ),
			'{date}'                => __( 'Order date and time', 'mailhilfe-order-note-manager' ),
			'{paid_date}'           => __( 'Paid date', 'mailhilfe-order-note-manager' ),
			'{completed_date}'      => __( 'Completed date', 'mailhilfe-order-note-manager' ),
			'{customer}'            => __( 'Customer name', 'mailhilfe-order-note-manager' ),
			'{customer_id}'         => __( 'Customer ID', 'mailhilfe-order-note-manager' ),
			'{customer_first_name}' => __( 'Customer first name', 'mailhilfe-order-note-manager' ),
			'{customer_last_name}'  => __( 'Customer last name', 'mailhilfe-order-note-manager' ),
			'{customer_note}'       => __( 'Customer order note', 'mailhilfe-order-note-manager' ),
			'{billing_email}'       => __( 'Billing email', 'mailhilfe-order-note-manager' ),
			'{billing_phone}'       => __( 'Billing phone', 'mailhilfe-order-note-manager' ),
			'{billing_company}'     => __( 'Billing company', 'mailhilfe-order-note-manager' ),
			'{billing_address}'     => __( 'Billing address', 'mailhilfe-order-note-manager' ),
			'{billing_city}'        => __( 'Billing city', 'mailhilfe-order-note-manager' ),
			'{billing_postcode}'    => __( 'Billing postcode', 'mailhilfe-order-note-manager' ),
			'{billing_country}'     => __( 'Billing country', 'mailhilfe-order-note-manager' ),
			'{shipping_first_name}' => __( 'Shipping first name', 'mailhilfe-order-note-manager' ),
			'{shipping_last_name}'  => __( 'Shipping last name', 'mailhilfe-order-note-manager' ),
			'{shipping_company}'    => __( 'Shipping company', 'mailhilfe-order-note-manager' ),
			'{shipping_address}'    => __( 'Shipping address', 'mailhilfe-order-note-manager' ),
			'{shipping_city}'       => __( 'Shipping city', 'mailhilfe-order-note-manager' ),
			'{shipping_postcode}'   => __( 'Shipping postcode', 'mailhilfe-order-note-manager' ),
			'{shipping_country}'    => __( 'Shipping country', 'mailhilfe-order-note-manager' ),
			'{payment_method}'      => __( 'Payment method', 'mailhilfe-order-note-manager' ),
			'{payment_method_id}'   => __( 'Payment method ID', 'mailhilfe-order-note-manager' ),
			'{shipping_method}'     => __( 'Shipping method', 'mailhilfe-order-note-manager' ),
			'{order_total}'         => __( 'Order total', 'mailhilfe-order-note-manager' ),
			'{order_subtotal}'      => __( 'Order subtotal', 'mailhilfe-order-note-manager' ),
			'{shipping_total}'      => __( 'Shipping total', 'mailhilfe-order-note-manager' ),
			'{discount_total}'      => __( 'Discount total', 'mailhilfe-order-note-manager' ),
			'{tax_total}'           => __( 'Tax total', 'mailhilfe-order-note-manager' ),
			'{currency}'            => __( 'Currency', 'mailhilfe-order-note-manager' ),
			'{currency_symbol}'     => __( 'Currency symbol', 'mailhilfe-order-note-manager' ),
			'{item_count}'          => __( 'Item count', 'mailhilfe-order-note-manager' ),
			'{items}'               => __( 'Ordered items', 'mailhilfe-order-note-manager' ),
			'{site_name}'           => __( 'Site name', 'mailhilfe-order-note-manager' ),
			'{admin_email}'         => __( 'Admin email', 'mailhilfe-order-note-manager' ),
			'{current_date}'        => __( 'Current date', 'mailhilfe-order-note-manager' ),
			'{current_time}'        => __( 'Current time', 'mailhilfe-order-note-manager' ),
			'{current_user}'        => __( 'Current admin user', 'mailhilfe-order-note-manager' ),
			'{order_meta:meta_key}'  => __( 'Order custom field by meta key', 'mailhilfe-order-note-manager' ),
			'{customer_meta:meta_key}' => __( 'Customer custom field by meta key', 'mailhilfe-order-note-manager' ),
		);

		return $definitions;
	}

	/**
	 * Replaces placeholders with order values.
	 *
	 * @param string   $text  Template text.
	 * @param WC_Order $order WooCommerce order.
	 * @return string
	 */
	public static function replace( $text, $order ) {
		if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
			return $text;
		}

		$values = self::get_values( $order );

		/**
		 * Filters placeholder values before replacement.
		 *
		 * @param array    $values Placeholder map.
		 * @param WC_Order $order  WooCommerce order.
		 */
		$values = apply_filters( 'mhont_placeholder_values', $values, $order );
		$values = apply_filters( 'mailhilfe_order_note_placeholder_values', $values, $order );

		$text = strtr( (string) $text, array_map( array( __CLASS__, 'clean_value' ), $values ) );

		$text = preg_replace_callback(
			'/\{order_meta:([A-Za-z0-9_\-:.]+)\}/',
			static function ( $matches ) use ( $order ) {
				$key = self::sanitize_custom_meta_key( $matches[1] );
				if ( '' === $key || self::is_blocked_meta_key( $key ) ) {
					return '';
				}
				return self::clean_value( $order->get_meta( $key, true ) );
			},
			$text
		);

		$text = preg_replace_callback(
			'/\{customer_meta:([A-Za-z0-9_\-:.]+)\}/',
			static function ( $matches ) use ( $order ) {
				$customer_id = absint( $order->get_customer_id() );
				$key         = self::sanitize_custom_meta_key( $matches[1] );
				if ( ! $customer_id || '' === $key || self::is_blocked_meta_key( $key ) ) {
					return '';
				}
				return self::clean_value( get_user_meta( $customer_id, $key, true ) );
			},
			$text
		);

		return $text;
	}

	/**
	 * Returns placeholder values for an order.
	 *
	 * @param WC_Order $order WooCommerce order.
	 * @return array<string,string>
	 */
	private static function get_values( $order ) {
		$date_created   = $order->get_date_created();
		$date_paid      = $order->get_date_paid();
		$date_completed = $order->get_date_completed();
		$customer       = trim( $order->get_formatted_billing_full_name() );
		$current_user   = wp_get_current_user();
		$date_format    = (string) get_option( 'date_format' );
		$time_format    = (string) get_option( 'time_format' );

		if ( '' === $customer ) {
			$customer = $order->get_billing_email();
		}

		return array(
			'{order_id}'            => $order->get_id(),
			'{order_number}'        => $order->get_order_number(),
			'{order_status}'        => wc_get_order_status_name( $order->get_status() ),
			'{order_date}'          => self::format_datetime( $date_created, 'date' ),
			'{order_time}'          => self::format_datetime( $date_created, 'time' ),
			'{date}'                => self::format_datetime( $date_created, 'datetime' ),
			'{paid_date}'           => self::format_datetime( $date_paid, 'date' ),
			'{completed_date}'      => self::format_datetime( $date_completed, 'date' ),
			'{customer}'            => $customer,
			'{customer_id}'         => $order->get_customer_id(),
			'{customer_first_name}' => $order->get_billing_first_name(),
			'{customer_last_name}'  => $order->get_billing_last_name(),
			'{customer_note}'       => $order->get_customer_note(),
			'{billing_email}'       => $order->get_billing_email(),
			'{billing_phone}'       => $order->get_billing_phone(),
			'{billing_company}'     => $order->get_billing_company(),
			'{billing_address}'     => self::get_address( $order, 'billing' ),
			'{billing_city}'        => $order->get_billing_city(),
			'{billing_postcode}'    => $order->get_billing_postcode(),
			'{billing_country}'     => $order->get_billing_country(),
			'{shipping_first_name}' => $order->get_shipping_first_name(),
			'{shipping_last_name}'  => $order->get_shipping_last_name(),
			'{shipping_company}'    => $order->get_shipping_company(),
			'{shipping_address}'    => self::get_address( $order, 'shipping' ),
			'{shipping_city}'       => $order->get_shipping_city(),
			'{shipping_postcode}'   => $order->get_shipping_postcode(),
			'{shipping_country}'    => $order->get_shipping_country(),
			'{payment_method}'      => $order->get_payment_method_title(),
			'{payment_method_id}'   => $order->get_payment_method(),
			'{shipping_method}'     => self::get_shipping_method_names( $order ),
			'{order_total}'         => self::format_price( $order->get_total(), $order ),
			'{order_subtotal}'      => self::format_price( $order->get_subtotal(), $order ),
			'{shipping_total}'      => self::format_price( $order->get_shipping_total(), $order ),
			'{discount_total}'      => self::format_price( $order->get_discount_total(), $order ),
			'{tax_total}'           => self::format_price( $order->get_total_tax(), $order ),
			'{currency}'            => $order->get_currency(),
			'{currency_symbol}'     => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol( $order->get_currency() ) : $order->get_currency(),
			'{item_count}'          => $order->get_item_count(),
			'{items}'               => self::get_order_items( $order ),
			'{site_name}'           => get_bloginfo( 'name' ),
			'{admin_email}'         => get_bloginfo( 'admin_email' ),
			'{current_date}'        => date_i18n( $date_format ),
			'{current_time}'        => date_i18n( $time_format ),
			'{current_user}'        => $current_user && $current_user->exists() ? $current_user->display_name : '',
		);
	}

	/**
	 * Formats a WooCommerce date object.
	 *
	 * @param WC_DateTime|false|null $date Date value.
	 * @param string                 $type Format type.
	 * @return string
	 */
	private static function format_datetime( $date, $type ) {
		if ( ! $date ) {
			return '';
		}

		if ( 'date' === $type ) {
			return wc_format_datetime( $date, get_option( 'date_format' ) );
		}

		if ( 'time' === $type ) {
			return wc_format_datetime( $date, get_option( 'time_format' ) );
		}

		return wc_format_datetime( $date );
	}

	/**
	 * Formats a price as plain text.
	 *
	 * @param string|float $amount Amount.
	 * @param WC_Order     $order  WooCommerce order.
	 * @return string
	 */
	private static function format_price( $amount, $order ) {
		if ( function_exists( 'wc_price' ) ) {
			return html_entity_decode( wp_strip_all_tags( wc_price( (float) $amount, array( 'currency' => $order->get_currency() ) ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );
		}

		return (string) $amount;
	}

	/**
	 * Returns a comma-separated address.
	 *
	 * @param WC_Order $order WooCommerce order.
	 * @param string   $type  Address type.
	 * @return string
	 */
	private static function get_address( $order, $type ) {
		$prefix = 'shipping' === $type ? 'shipping' : 'billing';
		$parts  = array();
		$fields = array( 'company', 'address_1', 'address_2', 'postcode', 'city', 'state', 'country' );

		foreach ( $fields as $field ) {
			$method = 'get_' . $prefix . '_' . $field;
			if ( is_callable( array( $order, $method ) ) ) {
				$parts[] = $order->{$method}();
			}
		}

		return implode( ', ', array_filter( array_map( 'trim', $parts ) ) );
	}

	/**
	 * Returns shipping method names.
	 *
	 * @param WC_Order $order WooCommerce order.
	 * @return string
	 */
	private static function get_shipping_method_names( $order ) {
		$shipping_names = array();

		foreach ( $order->get_shipping_methods() as $shipping_method ) {
			if ( is_object( $shipping_method ) && method_exists( $shipping_method, 'get_name' ) ) {
				$shipping_names[] = $shipping_method->get_name();
			}
		}

		return implode( ', ', array_filter( $shipping_names ) );
	}

	/**
	 * Returns ordered item names with quantities.
	 *
	 * @param WC_Order $order WooCommerce order.
	 * @return string
	 */
	private static function get_order_items( $order ) {
		$items = array();

		foreach ( $order->get_items( 'line_item' ) as $item ) {
			if ( is_object( $item ) && method_exists( $item, 'get_name' ) && method_exists( $item, 'get_quantity' ) ) {
				$items[] = sprintf(
					/* translators: 1: item name, 2: item quantity. */
					__( '%1$s × %2$s', 'mailhilfe-order-note-manager' ),
					$item->get_name(),
					$item->get_quantity()
				);
			}
		}

		return implode( ', ', array_filter( $items ) );
	}



	/**
	 * Validates a custom-field key used in a placeholder.
	 *
	 * WordPress meta keys may legitimately contain dots or colons. Using
	 * sanitize_key() here would silently change such keys and retrieve the wrong
	 * value, so only the explicitly supported character set is accepted.
	 *
	 * @param mixed $key Raw meta key.
	 * @return string
	 */
	private static function sanitize_custom_meta_key( $key ) {
		if ( ! is_string( $key ) ) {
			return '';
		}

		$key = trim( $key );
		if ( '' === $key || strlen( $key ) > 191 || ! preg_match( '/\A[A-Za-z0-9_\-:.]+\z/', $key ) ) {
			return '';
		}

		return $key;
	}

	/**
	 * Blocks sensitive custom field keys from generic placeholder output.
	 *
	 * @param string $key Meta key.
	 * @return bool
	 */
	private static function is_blocked_meta_key( $key ) {
		$allowed = apply_filters( 'mailhilfe_order_note_allowed_meta_keys', array(), $key );
		if ( is_array( $allowed ) && in_array( $key, $allowed, true ) ) { return false; }
		$blocked_fragments = array( 'password', 'pass', 'token', 'secret', 'key', 'session', 'nonce', 'auth', 'hash' );
		foreach ( $blocked_fragments as $fragment ) {
			if ( false !== strpos( strtolower( $key ), $fragment ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Cleans a placeholder value for safe note insertion.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private static function clean_value( $value ) {
		if ( is_scalar( $value ) || null === $value ) {
			return wp_strip_all_tags( (string) $value );
		}

		if ( is_array( $value ) ) {
			$clean_values = array();
			foreach ( $value as $item ) {
				if ( is_scalar( $item ) || null === $item ) {
					$clean_values[] = wp_strip_all_tags( (string) $item );
				}
			}
			return implode( ', ', array_filter( $clean_values, 'strlen' ) );
		}

		return '';
	}
}
