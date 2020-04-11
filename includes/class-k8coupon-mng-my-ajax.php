<?php
class K8coupon_Mng_My_Ajax
{
	function __construct()
	{
		//Send Phone Number
		add_action('wp_ajax_nopriv_k8coupon_mng_send', array( $this, 'k8coupon_mng_send' ));
		add_action('wp_ajax_k8coupon_mng_send', array( $this, 'k8coupon_mng_send' ));

		#live update send status
		add_action('wp_ajax_nopriv_k8coupon_mng_live_auth', array( $this, 'k8coupon_mng_live_auth' ));
		add_action('wp_ajax_k8coupon_mng_live_auth', array( $this, 'k8coupon_mng_live_auth' ));
	}
	public function final( $arrr ){
		echo json_encode( $arrr );
		exit();
	}
	#live update wheather user called us
	public function k8coupon_mng_live_auth(){
		$arrr = array();
		extract( $_POST );
		// write_log(get_defined_vars());
		global $wpdb;
		$k8_client = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."k8_client` WHERE `callcheck_id`=%s", $callcheck_id));
		#Phone Auth passed
		if( !empty($k8_client) && $k8_client->callcheck_status == 401 ){
			
			$k8_coupon = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."k8_coupon` WHERE `client_id`=%d", $k8_client->id));

			$arrr['phn_approved'] = 1;
			$arrr['redirect_url'] = get_field('pp','option');
			$arrr['code'] = $k8_coupon->code;
			# Redirect for hide.me VPN
			if( $k8_client->vpn_id == 15 ){
				$arrr['redirect_url'] = get_field('hm','option') .$k8_coupon->code.'/';
			}
			#Phone number authorization successfully passed modal
			$arrr['txt1'] = get_field('u_n_t_4','option');
		}
		// write_log($k8_client);
		$this->final($arrr);
	}

	#Send Phone Number
	public function k8coupon_mng_send(){
		$arrr = array();
		$enabled_countries = [ '+43','+49','+41','+7' ];
		$k8_country = '';
		extract( $_POST );
		// write_log(get_defined_vars());
		// die();
		# checking for direct form submission
		if( !isset( $action ) || $action !== 'k8coupon_mng_send' || !isset( $datta ) || !is_array( $datta ) || count( $datta ) <= 0 ){
			$arrr['error'][] = 'Hackers sind unerwünscht. nutze das Webformular!';
		}
		foreach ($datta as $item) {
			switch ( $item['name'] ) {
				case 'signonsecurity':
					$signonsecurity = $item['value'];
					break;
				case 'k8_phone':
					$k8_phone = K8coupon_Mng_My_Help::getNumz($item['value']);
					break;
				case 'k8phn-valid':
					$k8phn_valid = K8coupon_Mng_My_Help::getNumz($item['value']);
					break;
				case 'country':
					$k8_country = $item['value'];
					$k8_phone = K8coupon_Mng_My_Help::getNumz($item['value']) . $k8_phone;
					break;
				case 'k8_vpnid':
					$k8_vpnid = K8coupon_Mng_My_Help::getNumz($item['value']);
					break;
				default:
					break;
			}
		}
		#Checking nonce
		if( !isset( $signonsecurity ) || !wp_verify_nonce( $signonsecurity, 'k8_coupon_nonce' ) ){
			$arrr['error'][] = 'Hacker sind hier nicht erwünscht, daher machen wir hier auch nicht weiter.';
		}
		#Checking phone for validity
		if( !isset( $k8phn_valid ) || $k8phn_valid != 1 || !isset( $k8_phone ) ){
			$arrr['error'][] = 'Bitte gibt eine gültige Telefonnummer an!';
		}
		#Checking if phone number greater than 15 MSISDN
		if( strlen( $k8_phone ) > 17 ){
			$arrr['error'][] = 'max. 17-stellige internationale MSISDN';
		}
		#Checking for allowed country code
		if( !in_array($k8_country, $enabled_countries) ){
			$arrr['error'][] = 'Dein Land ist nicht erlaubt';
		}
		#Checking for existing errors
		if( isset( $arrr['error'] ) && count( $arrr['error'] ) > 0 ){
			$this->final($arrr);
		}
		global $wpdb;
		$k8_client = $wpdb->get_row($wpdb->prepare("SELECT `id` FROM `".$wpdb->prefix."k8_client` WHERE `phone`=%s AND `is_used`=%d", $k8_phone, 1));
		#If number is used and already received Coupon
		if( !empty($k8_client) ){
			$arrr['error'][] = get_field('u_n_t_1',"option");
			$this->final($arrr);
		}

		#setting up phone authorization
		$bbody = file_get_contents("https://sms.ru/callcheck/add?api_id=B3421EE9-59F2-27AF-FA18-2CE9AC127B0F&phone=".$k8_phone."&json=1");
		$jjson = json_decode($bbody);
		// write_log($jjson); // Для дебага

		// $k8_client = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."k8_client WHERE phone=%s AND is_used IS NULL", $k8_phone));
		$k8_client = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."k8_client` WHERE `phone`=".$k8_phone." AND `is_used` IS NULL");
		// write_log($k8_client);
		// $date1 = ;
		// $d1 =	strtotime(current_time('mysql') .'+5 minutes');

		// write_log( $d1 );
		#Update
		if( is_array($k8_client) && count($k8_client) > 0 ){
			foreach ($k8_client as $k8_cli) {

				#If user tried with no luck more than 3 times
				if( $k8_cli->atempts >= 3 ){
					$arrr['error'][] = get_field('u_n_t_2',"option");
					$this->final($arrr);
				}
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE `".$wpdb->prefix."k8_client` SET `callcheck_id`=%s, `phn_auth`=%s, `callcheck_date`=%s, `vpn_id`=%d, `atempts`=`atempts`+1  WHERE `id`=%d",
						$jjson->check_id,
						$jjson->call_phone,
						current_time('mysql'),
						$k8_vpnid,
						$k8_cli->id
					)
				);
			}
		}
		// Insert
		else{
			$wpdb->insert(
				$wpdb->prefix . 'k8_client',
				array(
					'phone' => $k8_phone,
					'callcheck_id' => $jjson->check_id,
					'phn_auth' => $jjson->call_phone,
					'callcheck_date' => current_time( 'mysql' ),
					'vpn_id' => $k8_vpnid
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%d'
				)
			);
		}

		#Call to our number, that listed below, so we can be sure  that you are a real person with real phone number
		$arrr['html'] = "ok";
		$arrr['html_1'] = get_field('u_n_t_3',"option");
		$arrr['callcheck_id'] = $jjson->check_id;
		$this->final($arrr);

	}
}
new K8coupon_Mng_My_Ajax;