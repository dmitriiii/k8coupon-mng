<?php
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

		$ch = curl_init("https://sms.ru/sms/send");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
	    "api_id" => "B3421EE9-59F2-27AF-FA18-2CE9AC127B0F",
	    "to" => $this->phone, // До 100 штук до раз
	    "msg" => 'Dein persönlicher Coupon, einlösbar bei vpntester.de/ppt/ ist: ' . $this->coupon, // Если приходят крякозябры, то уберите iconv и оставьте только "Привет!",
	   	"from" => "vpntester",
	   	// "test" => 1,
	    /*
	    // Если вы хотите отправлять разные тексты на разные номера, воспользуйтесь этим кодом. В этом случае to и msg нужно убрать.
	    "multi" => array( // до 100 штук за раз
	        "79677100226"=> iconv("windows-1251", "utf-8", "Привет 1"), // Если приходят крякозябры, то уберите iconv и оставьте только "Привет!",
	        "74993221627"=> iconv("windows-1251", "utf-8", "Привет 2")
	    ),
	    */
	    "json" => 1 // Для получения более развернутого ответа от сервера
		)));
		$body = curl_exec($ch);
		curl_close($ch);
		return json_decode($body);

	}
}