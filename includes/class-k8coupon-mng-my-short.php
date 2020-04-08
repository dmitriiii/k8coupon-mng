<?php
class K8coupon_Mng_My_Short
{
	public function __construct(){
		#Show table with taxonomies Data
		add_shortcode( 'k8coupon_mng_form', array( $this, 'k8coupon_mng_form') );
	}
	public function k8coupon_mng_form($atts, $content, $tag){

		wp_enqueue_script( 'k8-intUtils-js' );
		wp_enqueue_script( 'k8-intlTelInput-js' );
		wp_enqueue_script( 'k8coupon-mng-public-js' );
		wp_localize_script( 'k8coupon-mng-public-js', 'k8All', array(
	    'ajaxurl' => admin_url( 'admin-ajax.php' ),
	  ));
		wp_enqueue_style( 'k8-intlTelInput-css' );
		$a = shortcode_atts( array(
			'vpnid' => 6,
		), $atts );
		ob_start();
		?>
			<form class="k8-form__coupon" action="k8coupon_mng_send" method="post">
				<?php wp_nonce_field('k8_coupon_nonce', 'signonsecurity'); ?>
				<label>Telefonnummer</label>
			  <div class="k8-inp__wrr">
					<input name="k8_phone" pattern="^[0-9]+$" id="k8_phon" type="text" class="k8-inp" data-k8phn required data-imp minlength="5" maxlength="15">
					<input type="hidden" name="k8phn-valid" data-k8phn-valid value='0'>
					<input type="hidden" name="country" data-k8phn-country>
				</div>
				<p class="status"></p>
				<div class="alc">
					<p style="text-align: center;">
						<button class="k8_bec" type="submit">Einen Coupon erhalten!</button>
					</p>
				</div>
				<input type="hidden" name="k8_vpnid" value="<?php echo $a['vpnid']; ?>">
			</form>
		<?php
	  $html = ob_get_clean();
	  return $html;
	}
}
new K8coupon_Mng_My_Short;