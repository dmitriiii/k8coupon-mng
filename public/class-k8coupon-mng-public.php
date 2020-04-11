<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    K8coupon_Mng
 * @subpackage K8coupon_Mng/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    K8coupon_Mng
 * @subpackage K8coupon_Mng/public
 * @author     Your Name <email@example.com>
 */
class K8coupon_Mng_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $k8coupon_mng    The ID of this plugin.
	 */
	private $k8coupon_mng;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $k8coupon_mng       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $k8coupon_mng, $version ) {
		$this->k8coupon_mng = $k8coupon_mng;
		$this->version = $version;
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in K8coupon_Mng_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The K8coupon_Mng_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_style( 'k8-intlTelInput-css', plugin_dir_url( __FILE__ ) . 'css/intlTelInput.min.css', array(), false, 'all' );
		wp_register_style( 'k8coupon-mng-public-css', plugin_dir_url( __FILE__ ) . 'css/k8coupon-mng-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'k8coupon-mng-public-css' );
	}
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in K8coupon_Mng_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The K8coupon_Mng_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_script( 'k8-intUtils-js', plugin_dir_url( __FILE__ ) . 'js/utils.js', array('jquery'), 1.1, false );
		wp_register_script( 'k8-intlTelInput-js', plugin_dir_url( __FILE__ ) . 'js/intlTelInput-jquery.min.js', array('jquery'), 1.1, false );
		wp_register_script( 'k8coupon-mng-public-js', plugin_dir_url( __FILE__ ) . 'js/k8coupon-mng-public.js', array('jquery'), rand(1,1000), false );
	}
	#Adding custom Hooks
	public function k8_actions_wp_footer(){ ?>
		<div class="k8-prld">
			<div class="k8-prld__inn">
				<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
			</div>
		</div>
		<div class="modd" id="modd__err">
			<div class="modd__content">
				<div class="modd__clz">&times;</div>
				<div class="modd__title" style="color: red;">
					<!-- Fehler bei <br> der Übermittlung. -->
				</div>
				<div class="modd__txt" style="color: red;"></div>
		  </div>
		</div>
		<div class="modd" id="modd__succ">
			<div class="modd__content">
				<div class="modd__clz">&times;</div>
				<div class="modd__title" style="color: green;">
					Erfolgreich.
				</div>
				<div class="modd__txt" style="color: green;">
					<p>
						Coupon wurde erfolgreich gesendet.
					</p>
					<p>
						Nimm Dein Telefon zu Hand.
					</p>
				</div>
		  </div>
		</div>
	<?php
	}
}
