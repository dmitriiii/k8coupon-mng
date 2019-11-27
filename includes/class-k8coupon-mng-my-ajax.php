<?php
class K8coupon_Mng_My_Ajax
{
  function __construct()
  {
    //Send Phone Number
    add_action('wp_ajax_nopriv_k8coupon_mng_send', array( $this, 'k8coupon_mng_send' ));
    add_action('wp_ajax_k8coupon_mng_send', array( $this, 'k8coupon_mng_send' ));
  }
  public function final( $arrr ){
    echo json_encode( $arrr );
    exit();
  }
  #Send Phone Number
  public function k8coupon_mng_send(){
    $arrr = array();
    extract( $_POST );
    write_log(get_defined_vars());
    die();
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
          $k8_phone = K8coupon_Mng_My_Help::getNumz($item['value']) . $k8_phone;
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
    #Checking for existing errors
    if( isset( $arrr['error'] ) && count( $arrr['error'] ) > 0 ){
      $this->final($arrr);
    }
    // write_log(get_defined_vars());
    global $wpdb;
    $k8_client = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."k8_client` WHERE `phone` = %s", $k8_phone));
    #If number already received Coupon
    if( !empty( $k8_client ) ){
      $arrr['error'][] = 'Du hast bereits einen Coupon erhalten.Diese Coupons sind nur zum Testen gedacht und daher können wir Dir keinen weiteren senden.';
      $this->final($arrr);
    }
    #Selecting Random UnUsed Coupon
    $coup_rand = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."k8_coupon` WHERE `is_taken`=%d ORDER BY RAND() LIMIT 1", 0));
    #No Unused Coupons left
    if( !isset( $coup_rand ) || empty( $coup_rand ) ){
      $arrr['error'][] = 'Keine Gutscheine mehr übrig. Bitte kontaktieren Sie uns!';
      $this->final($arrr);
    }

    #Sending SMS to new client with coupon number
    $my_sms = new K8coupon_Mng_My_Sms(
      array(
        'coupon' => $coup_rand->code,
        'phone' => $k8_phone
      )
    );
    $ress = $my_sms->send();

    // write_log( $ress );

    // die();

    #Check if sending error or not
    if( $ress->status == 'ERROR' ){
      $arrr['error'][] = 'Fehler beim Senden der SMS. Bitte kontaktieren Sie uns, um das Problem zu lösen';
      $this->final($arrr);
    }
    #Phone number Doesnt Exist at all!
    foreach ($ress->sms as $phone => $data) {
      if ($data->status !== "OK") {
        $arrr['error'][] = 'Fehler beim Senden der SMS. Telefonnummer, die Sie eingegeben haben - existiert nicht';
        $this->final($arrr);
      }
    }


    #inserting phone number to DB
    $wpdb->insert( $wpdb->prefix . 'k8_client',
      array(
        'phone' => $k8_phone,
      ),
      array(
        '%s',
      )
    );
    $latest_client_id = $wpdb->insert_id;
    #Updating coupon table with data
    $wpdb->update(
      $wpdb->prefix.'k8_coupon',
      array(
        'client_id' => $latest_client_id,  // string
        'is_taken' => 1, // integer (number)
        'reg_date' => current_time( 'mysql' )
      ),
      array( 'id' => $coup_rand->id ),
      array(
        '%s', // value1
        '%d',  // value2
        '%s'
      ),
      array( '%d' )
    );

    $arrr['html'] = 'ok!';
    $this->final($arrr);
  }
}
new K8coupon_Mng_My_Ajax;