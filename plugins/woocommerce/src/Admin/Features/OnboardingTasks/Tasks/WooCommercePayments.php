<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;
use Automattic\WooCommerce\Admin\PluginsHelper;

/**
 * WooCommercePayments Task
 */
class WooCommercePayments extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woocommerce-payments';
	}

	/**
	 * Parent ID.
	 *
	 * @return string
	 */
	public function get_parent_id() {
		return 'setup';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Get paid with WooCommerce Payments', 'woocommerce' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			"You're only one step away from getting paid. Verify your business details to start managing transactions with WooCommerce Payments.",
			'woocommerce'
		);
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return __( '2 minutes', 'woocommerce' );
	}

	/**
	 * Action label.
	 *
	 * @return string
	 */
	public function get_action_label() {
		return __( 'Finish setup', 'woocommerce' );
	}

	/**
	 * Additional info.
	 *
	 * @return string
	 */
	public function get_additional_info() {
		return __(
			'By setting up, you are agreeing to the <a href="https://wordpress.com/tos/" target="_blank">Terms of Service</a>',
			'woocommerce'
		);
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return self::is_connected();
	}

	/**
	 * Task visibility.
	 *
	 * @return bool
	 */
	public function can_view() {
		return self::is_requested() &&
			self::is_installed() &&
			self::is_supported() &&
			( $this->get_parent_id() !== 'setup_two_column' || ! self::is_connected() );
	}

	/**
	 * Check if the plugin was requested during onboarding.
	 *
	 * @return bool
	 */
	public static function is_requested() {
		$profiler_data       = get_option( OnboardingProfile::DATA_OPTION, array() );
		$product_types       = isset( $profiler_data['product_types'] ) ? $profiler_data['product_types'] : array();
		$business_extensions = isset( $profiler_data['business_extensions'] ) ? $profiler_data['business_extensions'] : array();

		$subscriptions_and_us = in_array( 'subscriptions', $product_types, true ) && 'US' === WC()->countries->get_base_country();
		return in_array( 'woocommerce-payments', $business_extensions, true ) || $subscriptions_and_us;
	}

	/**
	 * Check if the plugin is installed.
	 *
	 * @return bool
	 */
	public static function is_installed() {
		$installed_plugins = PluginsHelper::get_installed_plugin_slugs();
		return in_array( 'woocommerce-payments', $installed_plugins, true );
	}

	/**
	 * Check if WooCommerce Payments is connected.
	 *
	 * @return bool
	 */
	public static function is_connected() {
		if ( class_exists( '\WC_Payments' ) ) {
			$wc_payments_gateway = \WC_Payments::get_gateway();
			return method_exists( $wc_payments_gateway, 'is_connected' )
				? $wc_payments_gateway->is_connected()
				: false;
		}

		return false;
	}

	/**
	 * Check if the store is in a supported country.
	 *
	 * @return bool
	 */
	public static function is_supported() {
		return in_array(
			WC()->countries->get_base_country(),
			array(
				'US',
				'PR',
				'AU',
				'CA',
				'DE',
				'ES',
				'FR',
				'GB',
				'IE',
				'IT',
				'NZ',
			),
			true
		);
	}
}
