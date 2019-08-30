<?php
include "websmscom-php-master/WebSmsCom_Toolkit.inc";
/**
 * Sends SMS
 */
class K8coupon_Mng_My_Sms
{
	public $coupon;
	public $phone;
	function __construct( $data )
	{
		$this->coupon = $data['coupon'];
		$this->phone = $data['phone'];
	}
	public function send(){
		# Modify these values to your needs
		$username             = 'mh@geroy.ooo';
		$pass                 = 'kl9023kwds';
		// OR (optional)
		$accessToken          = '7f206019-acbb-4e8a-a610-e0813074a0a5';
		$gateway_url          = 'https://api.websms.com/';
		$recipientAddressList = array( $this->phone );
		$utf8_message_text    = 'Dein persönliche Coupon, einlösbar bei https://vpntester.de/ppt/ ist: ' . $this->coupon;
		$maxSmsPerMessage     = 1;
		$test                 = false; // true: do not send sms for real, just test interface
		try {
		  // 1.) -- create sms client (once) ------
		  $smsClient = new WebSmsCom_Client($username, $pass, $gateway_url);
		  // 1.) -- Alternatively authenticate over access token
		  // $smsClient = new WebSmsCom_Client($accessToken, '', $gateway_url, WebSmsCom_AuthenticationMode::ACCESS_TOKEN);
		  //$smsClient->setVerbose(true);
		  //$smsClient->setSslVerifyHost(2); // needed if CURLOPT_SSL_VERIFYHOST no longer accepts the value 1
		  // 2.) -- create text message ----------------
		  $message  = new WebSmsCom_TextMessage($recipientAddressList, $utf8_message_text);
		  //$message = binary_sms_sample($recipientAddressList);
		  //$maxSmsPerMessage = null;  //needed if binary messages should be send
		  // 3.) -- send message ------------------
		  $Response = $smsClient->send($message, $maxSmsPerMessage, $test);
		  // show success
		  return array(
		         "Status          : ".$Response->getStatusCode(),
		         "StatusMessage   : ".$Response->getStatusMessage(),
		         "TransferId      : ".$Response->getTransferId(),
		         "ClientMessageId : ".(($Response->getClientMessageId()) ?
		                              $Response->getClientMessageId() : '<NOT SET>'),
		  			);
		// catch everything that's not a successfully sent message
		} catch (WebSmsCom_ParameterValidationException $e) {
		  exit("ParameterValidationException caught: ".$e->getMessage()."\n");
		} catch (WebSmsCom_AuthorizationFailedException $e) {
		  exit("AuthorizationFailedException caught: ".$e->getMessage()."\n");
		} catch (WebSmsCom_ApiException $e) {
		  echo $e; // possibility to handle API status codes $e->getCode()
		  exit("ApiException Exception\n");
		} catch (WebSmsCom_HttpConnectionException $e) {
		  exit("HttpConnectionException caught: ".$e->getMessage()."HTTP Status: ".$e->getCode()."\n");
		} catch (WebSmsCom_UnknownResponseException $e) {
		  exit("UnknownResponseException caught: ".$e->getMessage()."\n");
		} catch (Exception $e) {
		  exit("Exception caught: ".$e->getMessage()."\n");
		}
	}
}