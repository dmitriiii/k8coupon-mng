<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    K8coupon_Mng
 * @subpackage K8coupon_Mng/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    K8coupon_Mng
 * @subpackage K8coupon_Mng/includes
 * @author     Your Name <email@example.com>
 */
class K8coupon_Mng {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      K8coupon_Mng_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $k8coupon_mng    The string used to uniquely identify this plugin.
	 */
	protected $k8coupon_mng;
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'K8COUPON_MNG_VERSION' ) ) {
			$this->version = K8COUPON_MNG_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->k8coupon_mng = 'k8coupon-mng';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - K8coupon_Mng_Loader. Orchestrates the hooks of the plugin.
	 * - K8coupon_Mng_i18n. Defines internationalization functionality.
	 * - K8coupon_Mng_Admin. Defines all hooks for the admin area.
	 * - K8coupon_Mng_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-k8coupon-mng-loader.php';
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-k8coupon-mng-i18n.php';

		#require SMS sending feature
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/smsru_php/sms.ru.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-k8coupon-mng-admin.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-k8coupon-mng-public.php';
		## MY CUSTOM CODE
		#helping class
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-k8coupon-mng-my-help.php';
		#add Sms Sending Support
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-k8coupon-mng-my-sms.php';
		# Add custom code with shortcodes
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-k8coupon-mng-my-short.php';
		#add custom routes
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-k8coupon-mng-my-route.php';
		#Add Ajax Support
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-k8coupon-mng-my-ajax.php';
		$this->loader = new K8coupon_Mng_Loader();
	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the K8coupon_Mng_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new K8coupon_Mng_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new K8coupon_Mng_Admin( $this->get_k8coupon_mng(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		#My custom admin hooks
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'backend' );
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new K8coupon_Mng_Public( $this->get_k8coupon_mng(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		#My custom hooks
		$this->loader->add_action( 'wp_footer', $plugin_public, 'k8_actions_wp_footer', 100 );
		// $this->loader->add_action( 'wp_head', $plugin_public, 'k8_actions_wp_head' );
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_k8coupon_mng() {
		return $this->k8coupon_mng;
	}
	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    K8coupon_Mng_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
