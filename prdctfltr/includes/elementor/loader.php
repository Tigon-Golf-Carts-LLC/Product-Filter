<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'XforWC_Elementor_Loader' ) ) :

	final class XforWC_Elementor_Loader {

		public static function init() {
			// Elementor calls this once it's ready. If Elementor isn't installed, this hook never fires.
			add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
			add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_category' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
			add_action( 'elementor/editor/after_enqueue_styles', array( __CLASS__, 'register_assets' ) );
		}

		public static function register_category( $elements_manager ) {
			$elements_manager->add_category(
				'woocommerce-elements',
				array(
					'title' => esc_html__( 'WooCommerce', 'prdctfltr' ),
					'icon'  => 'eicon-woocommerce',
				)
			);
		}

		public static function register_widgets( $widgets_manager ) {
			require_once __DIR__ . '/class-xwoo-filter-widget.php';
			$widgets_manager->register( new \XWoo_Filter_Widget() );
		}

		public static function register_assets() {
			$plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) . '/prdctfltr.php' );
			$version    = defined( 'XforWC_Product_Filters::$version' ) ? \XforWC_Product_Filters::$version : '1.0.0';

			wp_register_style(
				'xwoo-filter',
				$plugin_url . 'includes/css/xwoo-filter.css',
				array(),
				$version
			);

			wp_register_script(
				'xwoo-filter',
				$plugin_url . 'includes/js/xwoo-filter.js',
				array(),
				$version,
				true
			);
		}
	}

	XforWC_Elementor_Loader::init();

endif;
