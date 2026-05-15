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
			// Register on every plausible hook so the handles exist whenever Elementor tries to enqueue them.
			add_action( 'init', array( __CLASS__, 'register_assets' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
			add_action( 'elementor/frontend/after_register_styles', array( __CLASS__, 'register_assets' ) );
			add_action( 'elementor/frontend/after_register_scripts', array( __CLASS__, 'register_assets' ) );
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
			$plugin_dir = dirname( __FILE__, 2 ); // .../prdctfltr/includes
			$plugin_root = dirname( $plugin_dir );  // .../prdctfltr
			$plugin_url = plugin_dir_url( $plugin_root . '/prdctfltr.php' );

			$css_path = $plugin_root . '/includes/css/xwoo-filter.css';
			$js_path  = $plugin_root . '/includes/js/xwoo-filter.js';

			// Use filemtime so the version string changes whenever the asset
			// changes — bypasses browser/CDN caching of older builds.
			$css_ver = file_exists( $css_path ) ? filemtime( $css_path ) : '1.0.0';
			$js_ver  = file_exists( $js_path )  ? filemtime( $js_path )  : '1.0.0';

			wp_register_style(
				'xwoo-filter',
				$plugin_url . 'includes/css/xwoo-filter.css',
				array(),
				$css_ver
			);

			wp_register_script(
				'xwoo-filter',
				$plugin_url . 'includes/js/xwoo-filter.js',
				array(),
				$js_ver,
				true
			);
		}
	}

	XforWC_Elementor_Loader::init();

endif;
