<?php
/**
 * Plugin Name:       Elementor Markdown Widget
 * Description:       A custom Elementor widget to parse and display Markdown with LaTeX support.
 * Plugin URI:        https://github.com/raefko/Elementor-markdown-plugin
 * Version:           1.1.0
 * Author:            Raefko
 * Author URI:        https://github.com/raefko/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       elementor-markdown-widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main Elementor Markdown Widget Class
 *
 * @since 1.0.0
 */
final class Elementor_Markdown_Addon {

    /**
     * Plugin Version
     * @since 1.0.0
     * @var string The plugin version.
     */
    const VERSION = '1.1.0';

    /**
     * Minimum Elementor Version
     * @since 1.0.0
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

    /**
     * Instance
     * @since 1.0.0
     * @access private
     * @static
     * @var Elementor_Markdown_Addon The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     * Ensures only one instance of the class is loaded or can be loaded.
     * @since 1.0.0
     * @access public
     * @static
     * @return Elementor_Markdown_Addon An instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     * @since 1.0.0
     * @access public
     */
    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
    }
    
    /**
     * On Plugins Loaded
     * Checks if Elementor has loaded, and performs some compatibility checks.
     * If all checks pass, initializes the plugin.
     * @since 1.0.0
     * @access public
     */
    public function on_plugins_loaded() {
        if ( $this->is_compatible() ) {
            add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
            add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
            add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
        }
    }

    /**
     * Compatibility Checks
     * @since 1.0.0
     * @access public
     * @return bool Whether the plugin is compatible.
     */
    public function is_compatible() {
        // Check if Elementor is installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return false;
        }

        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return false;
        }

        return true;
    }
    
    /**
	 * Add custom widget categories.
	 * @since 1.0.0
	 * @access public
	 */
	function add_elementor_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'custom-widgets',
			[
				'title' => esc_html__( 'Custom Widgets', 'elementor-markdown-widget' ),
				'icon' => 'fa fa-plug',
			]
		);
	}

    /**
     * Enqueue scripts and styles for the frontend.
     * @since 1.1.0
     */
    public function enqueue_scripts() {
        // Enqueue KaTeX CSS
        wp_enqueue_style( 'katex', 'https://cdn.jsdelivr.net/npm/katex@0.16.0/dist/katex.min.css', [], '0.16.0' );

        // Enqueue KaTeX JS and its auto-render extension
        wp_enqueue_script( 'katex', 'https://cdn.jsdelivr.net/npm/katex@0.16.0/dist/katex.min.js', [], '0.16.0', true );
        wp_enqueue_script( 'katex-auto-render', 'https://cdn.jsdelivr.net/npm/katex@0.16.0/dist/contrib/auto-render.min.js', ['katex'], '0.16.0', true );
        
        // Enqueue our custom script
        wp_enqueue_script( 'markdown-widget-frontend', plugin_dir_url( __FILE__ ) . 'assets/js/frontend.js', ['elementor-frontend', 'katex-auto-render'], self::VERSION, true );
    }

    /**
     * Enqueue scripts for the Elementor editor.
     * This ensures our script runs in the editor view for a live preview.
     * @since 1.1.0
     */
    public function enqueue_editor_scripts() {
        $this->enqueue_scripts();
    }


    /**
     * Admin notice for missing Elementor.
     * @since 1.0.0
     */
    public function admin_notice_missing_main_plugin() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        $message = sprintf( esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-markdown-widget' ), '<strong>' . esc_html__( 'Elementor Markdown Widget', 'elementor-markdown-widget' ) . '</strong>', '<strong>' . esc_html__( 'Elementor', 'elementor-markdown-widget' ) . '</strong>' );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * Admin notice for minimum Elementor version.
     * @since 1.0.0
     */
    public function admin_notice_minimum_elementor_version() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        $message = sprintf( esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-markdown-widget' ), '<strong>' . esc_html__( 'Elementor Markdown Widget', 'elementor-markdown-widget' ) . '</strong>', '<strong>' . esc_html__( 'Elementor', 'elementor-markdown-widget' ) . '</strong>', self::MINIMUM_ELEMENTOR_VERSION );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * Register Widgets
     * @since 1.0.0
     */
    public function register_widgets($widgets_manager) {
        require_once( __DIR__ . '/widgets/widget-markdown.php' );
        $widgets_manager->register( new \Elementor_Markdown_Widget() );
    }
}

Elementor_Markdown_Addon::instance();