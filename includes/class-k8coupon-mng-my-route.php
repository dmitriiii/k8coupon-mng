<?php
class K8coupon_Mng_My_Route
{
	function __construct()
	{
		add_action( 'rest_api_init', array( $this, 'reg_route' ) );
	}

	public function reg_route(){
		register_rest_route(
			'k8coupon',
			'update',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'coup_upd' )
			)
		);
	}

	public function coup_upd( WP_REST_Request $request ){
		$post_params = $request->get_params();
		write_log( 'Callback Triggered!' );
		global $wpdb;
		// write_log($post_params);

		$api_id = "B3421EE9-59F2-27AF-FA18-2CE9AC127B0F"; // Ваш личный api_id - доступен на главной странице личного кабинета

		/* Защита от злоумышленников - проверка принятых данных на валидность (мы расчитываем md5 хэш вашего ключа и данных на нашей стороне, чтобы его можно было проверить на вашей стороне) */

		$hash = "";
		foreach ($post_params["data"] as $entry) {
			$hash .= $entry;
		}
		if ($post_params["hash"] == hash("sha256",$api_id.$hash)) {
			// переданные данные верны
		}
		// write_log( get_defined_vars() );

		/* Обработка переданных данных */
		foreach ($post_params["data"] as $entry) {
			$lines = explode("\n",$entry);
			switch ($lines[0]) {
				#SMS delivery
				case "sms_status":
					$sms_id = $lines[1];
					$sms_status = $lines[2];
					$k8_client = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."k8_client` WHERE `sms_id`=%s", $sms_id));
					$wpdb->query( $wpdb->prepare("UPDATE `".$wpdb->prefix."k8_client` SET `sms_status`=%d WHERE `sms_id`=%s", $sms_status, $sms_id) );
					write_log($k8_client);
					#SMS was received
					if( $sms_status == 103 || $sms_status == 102 ){
						$wpdb->query( $wpdb->prepare("UPDATE `".$wpdb->prefix."k8_coupon` SET `is_taken`=%d, `reg_date`=%s WHERE `client_id`=%d", 1, current_time( 'mysql' ), $k8_client->id) );
						$wpdb->query( $wpdb->prepare("UPDATE `".$wpdb->prefix."k8_client` SET `is_used`=%d WHERE `id`=%d", 1, $k8_client->id) );
					}

					// "Изменение статуса. Сообщение: $sms_id. Новый статус: $sms_status";
					// Здесь вы можете уже выполнять любые действия над этими данными.
					break;
				#Phone Authentification
				case "callcheck_status":
					$check_id = $lines[1];
					$check_status = $lines[2];

					$wpdb->query( $wpdb->prepare("UPDATE `".$wpdb->prefix."k8_client` SET `callcheck_status`=%d WHERE `callcheck_id`=%s AND `is_used` IS NULL", $check_status, $check_id) );

					// write_log( $rezz );

					#User uses real phone number!
					if ($check_status == "401") {

						$k8_client = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."k8_client` WHERE `callcheck_id`=%s", $check_id));
						#Selecting Random UnUsed Coupon
						$coup_rand = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."k8_coupon` WHERE `is_taken`=%d AND `vpn_id`=%d ORDER BY RAND() LIMIT 1", 0, $k8_client->vpn_id));

						$smsru = new SMSRU('B3421EE9-59F2-27AF-FA18-2CE9AC127B0F'); // Ваш уникальный программный ключ, который можно получить на главной странице

						$sms_data = new stdClass();
						$sms_data->to = $k8_client->phone;
						$sms_data->text = 'Hello World ' . $coup_rand->code; // Текст сообщения
						$sms_data->from = 'vpntester';
						// $data->from = ''; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
						// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
						// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
						// $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
						// $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
						$sms = $smsru->send_one($sms_data); // Отправка сообщения и возврат данных в переменную
						// if ($sms->status == "OK") { // Запрос выполнен успешно
						//     echo "Сообщение отправлено успешно. ";
						//     echo "ID сообщения: $sms->sms_id. ";
						//     echo "Ваш новый баланс: $sms->balance";
						// } else {
						//     echo "Сообщение не отправлено. ";
						//     echo "Код ошибки: $sms->status_code. ";
						//     echo "Текст ошибки: $sms->status_text.";
						// }

						// $wpdb->query( $wpdb->prepare("UPDATE `".$wpdb->prefix."k8_client` SET `callcheck_status`=%d WHERE `callcheck_id`=%s AND `is_used` IS NULL", $check_status, $check_id) );

						#Message Sent
						if ($sms->status == "OK") {
							$wpdb->query(
								$wpdb->prepare(
									"UPDATE `".$wpdb->prefix."k8_client` SET `sms_id`=%s, `atempts`=%d WHERE `id`=%d",
									$sms->sms_id,
									0,
									$k8_client->id
								)
							);
							$wpdb->query( $wpdb->prepare("UPDATE `".$wpdb->prefix."k8_coupon` SET `client_id`=%d WHERE `id`=%d", $k8_client->id, $coup_rand->id) );
						}

						// write_log($k8_client);
						// write_log($coup_rand);
						// write_log( $sms );

						// write_log('Succ phone authorized!');
						// Авторизация пройдена успешно. Мы получили звонок с номера, который вы нам передавали.
						// Идентификатор авторизации $check_id (вы должны были сохранить его в вашей базе)

					}
					// elseif ($check_status == "402") {
						// Истекло время, отведенное под авторизацию. Мы не получили звонка с номера, который вы нам передавали
						// Идентификатор авторизации $check_id (вы должны были сохранить его в вашей базе)
					// }
					break;
			}
		}
		return 100;/* Важно наличие этого блока, иначе наша система посчитает, что в вашем обработчике сбой */
	}

}
new K8coupon_Mng_My_Route;
