<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
	// if accessing this class directly through URL, send 404 and exit
	// this section of code will only work if you have a 404.html file in your root document folder.
	header("Location: /404.html", TRUE, 404);
	echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
	die;
}
error_reporting(0);
include '../data_services/common.php';
include '../data_services/price_quote.php';

include './src/php_mailer/PHPMailer.php';
include './src/php_mailer/SMTP.php';
include './src/utility_classes/Logger.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//----------------------------------------------------------------------------------------------------------------------
class Price_Quote extends Data_Access
{
	//----------------------------------------------------------------------------------------------------
	public function __construct()
	{
		// attempt database connection
		$res = $this->dbConnect();
		// if we get anything but a good response ...
		if ($res['response'] != '200') {
			die;
		}
	}

	//----------------------------------------------------------------------------------------------------
	public function SavePriceQuote()
	{
		try {
			$formData = (array) json_decode(file_get_contents('php://input'), TRUE);
			$params = array();

			//required information
			$params["first_name"]  = $formData['first_name'];
			$params["last_name"]  = $formData['last_name'];
			$params["email_address"]  = $formData['email_address'];
			$params["phone"] = $formData['phone'];
			$params["build_place_opt"] = $formData['build_place_opt'];
			$params["build_place"]  = $formData['build_place'];
			$params['is_save_cookie'] = array_key_exists('is_save_cookie', $formData) && $formData['is_save_cookie'] === true ?  1 : 0;
			$params['currency'] = $formData['currency'];

			//optional contact information
			$params["plan_build_city"]  = array_key_exists('plan_build_city', $formData) ? $formData['plan_build_city'] : '';
			$params['plan_build_country'] = array_key_exists('plan_build_country', $formData) ? $formData['plan_build_country'] : '';
			$params["plan_build_state"]  = array_key_exists('plan_build_state', $formData) ? $formData['plan_build_state'] : '';
			$params["plan_build_date"]  = array_key_exists('plan_build_date', $formData) && strtotime($formData['plan_build_date']) ? date("d-m-Y", strtotime($formData['plan_build_date'])) : '';
			$params['turn_key_budget'] = array_key_exists('turn_key_budget', $formData) ? $formData['turn_key_budget'] : '';
			$params["has_purchased_land"]  = array_key_exists('has_purchased_land', $formData) && $formData['has_purchased_land'] === true ?  1 : 0;
			$params['has_blueprint'] = array_key_exists('has_blueprint', $formData) && $formData['has_blueprint'] ? 1 : 0;
			$params["address_street"]  = array_key_exists('address_street', $formData) ? $formData['address_street'] : '';
			$params['address_apt_no'] = array_key_exists('address_apt_no', $formData) ? $formData['address_apt_no'] : '';
			$params["address_city"]  = array_key_exists('address_city', $formData) ? $formData['address_city'] : '';
			$params['address_country'] = array_key_exists('address_country', $formData) ? $formData['address_country'] : '';
			$params['address_state'] = array_key_exists('address_state', $formData) ? $formData['address_state'] : '';
			$params['address_postal_code'] = array_key_exists('address_postal_code', $formData) ? $formData['address_postal_code'] : '';

			#for audit logs
			$params["delete_flag"] = 0;
			$params["created_by"] = CONST_DB_USER_ID;
			$params["created_dttm"] = gmdate("Y/m/d H:i:s");
			$params["updated_by"] = CONST_DB_USER_ID;
			$params["updated_dttm"] = gmdate("Y/m/d H:i:s");
			$params["client_ip"] = $_SERVER['REMOTE_ADDR'];

			//homeplan
			$params["plan_code"] = array_key_exists('plan_code', $formData) ? $formData['plan_code'] : '';
			$params["plan_name"] = array_key_exists('plan_name', $formData) ? $formData['plan_name'] : '';
			$params["log_style"] = array_key_exists('log_style', $formData) ? $formData['log_style'] : '';
			$params["all_weather_barrier"] = array_key_exists('all_weather_barrier', $formData) ? $formData['all_weather_barrier'] : '';
			$params["log_type"] = array_key_exists('log_type', $formData) ? $formData['log_type'] : '';
			$params["notch"] = array_key_exists('notch', $formData) ? $formData['notch'] : '';
			$params["roofing"] = array_key_exists('roofing', $formData) ? $formData['roofing'] : '';
			$params["tg_ceiling"] = array_key_exists('tg_ceiling', $formData) ? $formData['tg_ceiling'] : '';
			$params["deck"] = array_key_exists('deck', $formData) ? $formData['deck'] : '';
			$params["gables"] = array_key_exists('gables', $formData) ? $formData['gables'] : '';
			$params["floor"] = array_key_exists('floor', $formData) ? $formData['floor'] : '';
			$params["walls"] = array_key_exists('walls', $formData) ? $formData['walls'] : '';
			$params["windows"] = array_key_exists('windows', $formData) ? $formData['windows'] : '';
			$params["windows_extra"] = array_key_exists('windows_extra', $formData) ? $formData['windows_extra'] : '';
			$params["doors"] = array_key_exists('doors', $formData) ? $formData['doors'] : '';
			$params["doors_extra"] = array_key_exists('doors_extra', $formData) ? $formData['doors_extra'] : '';
			$params["log_stair"] = array_key_exists('log_stair', $formData) ? $formData['log_stair'] : '';
			$params["stair_railing"] = array_key_exists('stair_railing', $formData) ? $formData['stair_railing'] : '';
			$params["guard_railing"] = array_key_exists('guard_railing', $formData) ? $formData['guard_railing'] : '';
			$params["deck_railing"] = array_key_exists('deck_railing', $formData) ? $formData['deck_railing'] : '';
			$params["order_type"]  = array_key_exists('order_type', $formData) ? $formData['order_type'] : '';
			$params["package"] = array_key_exists('package', $formData) ? $formData['package'] : '';
			//Pricing
			$params["shell_price"] = array_key_exists('shell_price', $formData) ? $formData['shell_price'] : '';
			$params["total_price"] = array_key_exists('total_price', $formData) ? $formData['total_price'] : '';
			// Additional details
			$params["additional_details"] = array_key_exists('additional_details', $formData) ? $formData['additional_details'] : '';

			/*
			|--------------------------------------------------------------------------
			| Send price quote details to user and info@thelogconnection.com
			|--------------------------------------------------------------------------
			|
			*/

			/*
            |--------------------------------------------------------------------------
            | Initialize mailer
            |--------------------------------------------------------------------------
            |
            */

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Mailer = "smtp";

			/*
            |--------------------------------------------------------------------------
            | Email server credentials
            |--------------------------------------------------------------------------
            |
            */

			$mail->SMTPDebug  = 1;
			$mail->SMTPAuth   = TRUE;
			$mail->SMTPSecure = "ssl";
			$mail->Port       = 465;
			$mail->Host       = "mail.server266.com";
			$mail->Username   = "pricequote@thelogconnection.com";
			$mail->Password   = "pR!c3quote*2022";

			/*
            |--------------------------------------------------------------------------
            | Sender details
            |--------------------------------------------------------------------------
            |
            */

			$mail->SetFrom('pricequote@thelogconnection.com', 'Price quote');
			$mail->AddReplyTo('pricequote@thelogconnection.com', 'Price quote');

			/*
			|--------------------------------------------------------------------------
			| Selected options
			|--------------------------------------------------------------------------
			|
			*/

			$selectedOptions = '';

			// Log style
			switch ($params["log_style"]) {
				case 'Stacked':
					$buildingStyle = 'Stacked log walls';
					break;
				case 'PB':
					$buildingStyle = 'Round Log Post and Beam';
					break;
				case 'Timber':
					$buildingStyle = 'Timber Frame';
					break;
				case 'Fusion':
					$buildingStyle = 'Fusion Style';
					break;
				default:
					$buildingStyle = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Building style:</strong> </td><td style='width: 400px'>" . $buildingStyle . "</td></tr>";

			// Log species
			switch ($params["log_type"]) {
				case 'Fir':
					$woodSpepcies = 'Douglas Fir';
					break;
				case 'Cedar':
					$woodSpepcies = 'Western Red Cedar';
					break;
				case 'Spruce':
					$woodSpepcies = 'Englemann Spruce';
					break;
				default:
					$woodSpepcies = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Wood species:</strong> </td><td style='width: 400px'>" . $woodSpepcies . "</td></tr>";

			// Notching system
			switch ($params["notch"]) {
				case 'Saddle':
					$notchingSystem = 'Scandavanian Saddle Notch';
					break;
				case 'Chinked':
					$notchingSystem = 'Chinked Style';
					break;
				case 'Dovetail_Full':
					$notchingSystem = 'Full-Scribe Dovetail';
					break;
				case 'Dovetail_Chinked':
					$notchingSystem = 'Chinked Dovetail';
					break;
				default:
					$notchingSystem = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Notching system:</strong> </td><td style='width: 400px'>" . $notchingSystem . "</td></tr>";

			// All weather barrier system
			switch ($params["all_weather_barrier"]) {
				case 'AWB':
					$allWeatherBarrierSystem = 'Yes, include';
					break;
				case 'CS':
					$allWeatherBarrierSystem = 'Do not include';
					break;
				default:
					$allWeatherBarrierSystem = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>All weather barrier system:</strong> </td><td style='width: 400px'>" . $allWeatherBarrierSystem . "</td></tr>";

			// Interior Log Staircase
			switch ($params["log_stair"]) {
				case 'Log_Stair':
					$interiorLogStaircase = 'Hand crafted log railing for main stair';
					break;
				case 'CS':
					$interiorLogStaircase = 'Do not include';
					break;
				default:
					$interiorLogStaircase = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Interior Log Staircase:</strong> </td><td style='width: 400px'>" . $interiorLogStaircase . "</td></tr>";

			// Interior Guard railing
			switch ($params["guard_railing"]) {
				case 'All':
					$interiorGuardRailing = 'Complete, with railing and newel posts';
					break;
				case 'Newels':
					$interiorGuardRailing = 'Newel posts only (no railing)';
					break;
				case 'CS':
					$interiorGuardRailing = 'Do not include';
					break;
				default:
					$interiorGuardRailing = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Interior Guard Railing:</strong> </td><td style='width: 400px'>" . $interiorGuardRailing . "</td></tr>";

			// Exterior deck railing
			switch ($params["deck_railing"]) {
				case 'All':
					$exteriorGuardRailing = 'Complete log deck railing';
					break;
				case 'Newels':
					$exteriorGuardRailing = 'Newel posts only for deck (no railing)';
					break;
				case 'CS':
					$exteriorGuardRailing = 'Do not include';
					break;
				default:
					$exteriorGuardRailing = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Exterior deck railing:</strong> </td><td style='width: 400px'>" . $exteriorGuardRailing . "</td></tr>";

			/*
            |--------------------------------------------------------------------------
            | Email content
            |--------------------------------------------------------------------------
            |
            */

			$mail->IsHTML(true);

			$message = '<html><body>';
			// Home plan image and description
			$message .= '<table>';
			$message .= "<tr'><td style='width: 200px'><h3 style='font-size: 24px;'>Personal information</h3></td></tr>";
			$message .= "</table>";

			// Personal information
			$message .= '<table rules="all" style="background-color: rgb(220, 220, 220);" cellpadding="10">';
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>First Name:</strong> </td><td style='width: 400px'>" . strip_tags($params["first_name"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Last name:</strong> </td><td style='width: 400px'>" . strip_tags($params["last_name"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Email address:</strong> </td><td style='width: 400px'>" . strip_tags($params["email_address"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Phone:</strong> </td><td style='width: 400px'>" . strip_tags($params["phone"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Street:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_street"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Apartment no.:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_apt_no"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>City:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_city"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Country:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_country"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>State:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_state"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Postal code:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_postal_code"]) . "</td></tr>";
			$message .= "</table>";

			$message .= '<table>';
			$message .= "<tr'><td style='width: 200px'><h3 style='font-size: 24px;'>Project information</h3></td></tr>";
			$message .= "</table>";

			// Optional contact information
			$message .= '<table rules="all" style="background-color: rgb(220, 220, 220);" cellpadding="10">';
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build place (OPT.):</strong> </td><td style='width: 400px'>" . strip_tags($params["build_place_opt"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build place:</strong> </td><td style='width: 400px'>" . strip_tags($params["build_place"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Currency:</strong> </td><td style='width: 400px'>" . strip_tags($params["currency"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build city:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_build_city"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build country:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_build_country"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build state:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_build_state"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build date:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_build_date"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Turn key budget:</strong> </td><td style='width: 400px'>" . strip_tags($params["turn_key_budget"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Has purchased land?:</strong> </td><td style='width: 400px'>" . ($params["has_purchased_land"] == '0' ? 'No' : 'Yes') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Has blue print?:</strong> </td><td style='width: 400px'>" . ($params["has_blueprint"] == '0' ? 'No' : 'Yes') . "</td></tr>";

			// Selected options
			$message .= $selectedOptions;

			// Home plan details
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Home plan code:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_code"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Home plan:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_name"]) . "</td></tr>";
			// Price
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Shell price:</strong> </td><td style='width: 400px'>" . strip_tags($params["currency"]) . ' ' . strip_tags(number_format($params["shell_price"], 2)) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Total price:</strong> </td><td style='width: 400px'>" . strip_tags($params["currency"]) . ' ' . strip_tags(number_format($params["total_price"], 2)) . "</td></tr>";
			// Additional details
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Additional details:</strong> </td><td style='width: 400px'>" . strip_tags($params["additional_details"]) . "</td></tr>";

			$message .= "</table>";
			$message .= "</body></html>";

			/*
            |--------------------------------------------------------------------------
            | Receiver configuration (potential client)
            |--------------------------------------------------------------------------
            |
            */

			$mail->AddAddress($params["email_address"], $params["first_name"]);
			$mail->Subject = "[Price quotation] - " . $params["plan_code"];
			$content = $message;

			/*
            |--------------------------------------------------------------------------
            | Send email to potential client
            |--------------------------------------------------------------------------
            |
            */

			$mail->MsgHTML($content);
			if (!$mail->Send()) {
				$responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Failed to send email.';
				return $responseArray;
			}

			/*
            |--------------------------------------------------------------------------
            | Receiver configuration (thelogconnection)
            |--------------------------------------------------------------------------
            |
            */

			$mail->clearAllRecipients();
			$mail->AddAddress('info@thelogconnection.com', 'info@thelogconnection.com');
			$mail->Subject = "[Price quotation] - " . $params["plan_code"];
			$content = $message;

			/*
            |--------------------------------------------------------------------------
            | Visitor information
            |--------------------------------------------------------------------------
            |
            */

			$message .= '<table>';
			$message .= "<tr'><td style='width: 200px'><h3 style='font-size: 24px;'>Visitor information</h3></td></tr>";
			$message .= "</table>";

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			$message .= '<table rules="all" style="background-color: rgb(220, 220, 220);" cellpadding="10">';
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>IP Address:</strong> </td><td style='width: 400px'>" . $ip . "</td></tr>";
			$message .= "</table>";

			$content = $message;
			$mail->MsgHTML($content);
			if (!$mail->Send()) {
				$responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Failed to send email.';
				return $responseArray;
			}

			// print_r($params);
			$columns = '';
			$values = array();
			$parameters = str_repeat('?,', count($params) - 1) . '?';
			$types = str_repeat('s', count($params));
			foreach ($params as $key => $value) {
				$columns = $columns . $key . ",";
				array_push($values, $value);
			};
			$columns = rtrim($columns, ",");

			$script = "INSERT INTO price_quote ($columns) VALUES ($parameters)";
			if (isset($GLOBALS['dbConnection']->errno) && ($GLOBALS['dbConnection']->errno != 0)) {
				$responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Internal server error. MySQL error: ' . $GLOBALS['dbConnection']->errno . ' ' . $GLOBALS['dbConnection']->error;
			} else {
				$stmt = $GLOBALS['dbConnection']->prepare($script);
				if ($stmt) {
					$stmt->bind_param($types, ...$values);
					$stmt->execute();
					if ($stmt->affected_rows > 0) {
						$responseArray = App_Response::getResponse('200');
						$responseArray['message'] = "Price Quote succesfully saved";
						$this->SendEmailToDealer($params);
					} else {
						throw new Exception('No record inserted');
					}
				} else {
					$responseArray = App_Response::getResponse('400');
				}
			}
			return $responseArray;
		} catch (Exception $e) {
			$responseArray = App_Response::getResponse('500');
			$responseArray['message'] = $e->getMessage();
			return $responseArray;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Capture user activity in price quote form
	|--------------------------------------------------------------------------
	|
	*/

	public function CaptureUserActivity()
	{
		try {
			$formData = (array) json_decode(file_get_contents('php://input'), TRUE);
			$params = array();

			//required information
			$params["first_name"]  = $formData['first_name'];
			$params["last_name"]  = $formData['last_name'];
			$params["email_address"]  = $formData['email_address'];
			$params["phone"] = $formData['phone'];
			$params["build_place_opt"] = $formData['build_place_opt'];
			$params["build_place"]  = $formData['build_place'];
			$params['is_save_cookie'] = array_key_exists('is_save_cookie', $formData) && $formData['is_save_cookie'] === true ?  1 : 0;
			$params['currency'] = $formData['currency'];

			//optional contact information
			$params["plan_build_city"]  = array_key_exists('plan_build_city', $formData) ? $formData['plan_build_city'] : '';
			$params['plan_build_country'] = array_key_exists('plan_build_country', $formData) ? $formData['plan_build_country'] : '';
			$params["plan_build_state"]  = array_key_exists('plan_build_state', $formData) ? $formData['plan_build_state'] : '';
			$params["plan_build_date"]  = array_key_exists('plan_build_date', $formData) && strtotime($formData['plan_build_date']) ? date("d-m-Y", strtotime($formData['plan_build_date'])) : '';
			$params['turn_key_budget'] = array_key_exists('turn_key_budget', $formData) ? $formData['turn_key_budget'] : '';
			$params["has_purchased_land"]  = array_key_exists('has_purchased_land', $formData) && $formData['has_purchased_land'] === true ?  1 : 0;
			$params['has_blueprint'] = array_key_exists('has_blueprint', $formData) && $formData['has_blueprint'] ? 1 : 0;
			$params["address_street"]  = array_key_exists('address_street', $formData) ? $formData['address_street'] : '';
			$params['address_apt_no'] = array_key_exists('address_apt_no', $formData) ? $formData['address_apt_no'] : '';
			$params["address_city"]  = array_key_exists('address_city', $formData) ? $formData['address_city'] : '';
			$params['address_country'] = array_key_exists('address_country', $formData) ? $formData['address_country'] : '';
			$params['address_state'] = array_key_exists('address_state', $formData) ? $formData['address_state'] : '';
			$params['address_postal_code'] = array_key_exists('address_postal_code', $formData) ? $formData['address_postal_code'] : '';

			#for audit logs
			$params["delete_flag"] = 0;
			$params["created_by"] = CONST_DB_USER_ID;
			$params["created_dttm"] = gmdate("Y/m/d H:i:s");
			$params["updated_by"] = CONST_DB_USER_ID;
			$params["updated_dttm"] = gmdate("Y/m/d H:i:s");
			$params["client_ip"] = $_SERVER['REMOTE_ADDR'];

			//homeplan
			$params["plan_code"] = array_key_exists('plan_code', $formData) ? $formData['plan_code'] : '';
			$params["plan_name"] = array_key_exists('plan_name', $formData) ? $formData['plan_name'] : '';
			$params["log_style"] = array_key_exists('log_style', $formData) ? $formData['log_style'] : '';
			$params["all_weather_barrier"] = array_key_exists('all_weather_barrier', $formData) ? $formData['all_weather_barrier'] : '';
			$params["log_type"] = array_key_exists('log_type', $formData) ? $formData['log_type'] : '';
			$params["notch"] = array_key_exists('notch', $formData) ? $formData['notch'] : '';
			$params["roofing"] = array_key_exists('roofing', $formData) ? $formData['roofing'] : '';
			$params["tg_ceiling"] = array_key_exists('tg_ceiling', $formData) ? $formData['tg_ceiling'] : '';
			$params["deck"] = array_key_exists('deck', $formData) ? $formData['deck'] : '';
			$params["gables"] = array_key_exists('gables', $formData) ? $formData['gables'] : '';
			$params["floor"] = array_key_exists('floor', $formData) ? $formData['floor'] : '';
			$params["walls"] = array_key_exists('walls', $formData) ? $formData['walls'] : '';
			$params["windows"] = array_key_exists('windows', $formData) ? $formData['windows'] : '';
			$params["windows_extra"] = array_key_exists('windows_extra', $formData) ? $formData['windows_extra'] : '';
			$params["doors"] = array_key_exists('doors', $formData) ? $formData['doors'] : '';
			$params["doors_extra"] = array_key_exists('doors_extra', $formData) ? $formData['doors_extra'] : '';
			$params["log_stair"] = array_key_exists('log_stair', $formData) ? $formData['log_stair'] : '';
			$params["stair_railing"] = array_key_exists('stair_railing', $formData) ? $formData['stair_railing'] : '';
			$params["guard_railing"] = array_key_exists('guard_railing', $formData) ? $formData['guard_railing'] : '';
			$params["deck_railing"] = array_key_exists('deck_railing', $formData) ? $formData['deck_railing'] : '';
			$params["order_type"]  = array_key_exists('order_type', $formData) ? $formData['order_type'] : '';
			$params["package"] = array_key_exists('package', $formData) ? $formData['package'] : '';
			//Pricing
			$params["shell_price"] = array_key_exists('shell_price', $formData) ? $formData['shell_price'] : '';
			$params["total_price"] = array_key_exists('total_price', $formData) ? $formData['total_price'] : '';
			// Additional details
			$params["additional_details"] = array_key_exists('additional_details', $formData) ? $formData['additional_details'] : '';

			/*
            |--------------------------------------------------------------------------
            | Initialize mailer
            |--------------------------------------------------------------------------
            |
            */

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Mailer = "smtp";

			/*
            |--------------------------------------------------------------------------
            | Email server credentials
            |--------------------------------------------------------------------------
            |
            */

			$mail->SMTPDebug  = 1;
			$mail->SMTPAuth   = TRUE;
			$mail->SMTPSecure = "ssl";
			$mail->Port       = 465;
			$mail->Host       = "mail.server266.com";
			$mail->Username   = "pricequote@thelogconnection.com";
			$mail->Password   = "pR!c3quote*2022";

			/*
            |--------------------------------------------------------------------------
            | Sender details
            |--------------------------------------------------------------------------
            |
            */

			$mail->SetFrom('pricequote@thelogconnection.com', 'Price quote');
			$mail->AddReplyTo('pricequote@thelogconnection.com', 'Price quote');

			/*
			|--------------------------------------------------------------------------
			| Selected options
			|--------------------------------------------------------------------------
			|
			*/

			$selectedOptions = '';

			// Log style
			switch ($params["log_style"]) {
				case 'Stacked':
					$buildingStyle = 'Stacked log walls';
					break;
				case 'PB':
					$buildingStyle = 'Round Log Post and Beam';
					break;
				case 'Timber':
					$buildingStyle = 'Timber Frame';
					break;
				case 'Fusion':
					$buildingStyle = 'Fusion Style';
					break;
				default:
					$buildingStyle = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Building style:</strong> </td><td style='width: 400px'>" . $buildingStyle . "</td></tr>";

			// Log species
			switch ($params["log_type"]) {
				case 'Fir':
					$woodSpepcies = 'Douglas Fir';
					break;
				case 'Cedar':
					$woodSpepcies = 'Western Red Cedar';
					break;
				case 'Spruce':
					$woodSpepcies = 'Englemann Spruce';
					break;
				default:
					$woodSpepcies = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Wood species:</strong> </td><td style='width: 400px'>" . $woodSpepcies . "</td></tr>";

			// Notching system
			switch ($params["notch"]) {
				case 'Saddle':
					$notchingSystem = 'Scandavanian Saddle Notch';
					break;
				case 'Chinked':
					$notchingSystem = 'Chinked Style';
					break;
				case 'Dovetail_Full':
					$notchingSystem = 'Full-Scribe Dovetail';
					break;
				case 'Dovetail_Chinked':
					$notchingSystem = 'Chinked Dovetail';
					break;
				default:
					$notchingSystem = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Notching system:</strong> </td><td style='width: 400px'>" . $notchingSystem . "</td></tr>";

			// All weather barrier system
			switch ($params["all_weather_barrier"]) {
				case 'AWB':
					$allWeatherBarrierSystem = 'Yes, include';
					break;
				case 'CS':
					$allWeatherBarrierSystem = 'Do not include';
					break;
				default:
					$allWeatherBarrierSystem = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>All weather barrier system:</strong> </td><td style='width: 400px'>" . $allWeatherBarrierSystem . "</td></tr>";

			// Interior Log Staircase
			switch ($params["log_stair"]) {
				case 'Log_Stair':
					$interiorLogStaircase = 'Hand crafted log railing for main stair';
					break;
				case 'CS':
					$interiorLogStaircase = 'Do not include';
					break;
				default:
					$interiorLogStaircase = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Interior Log Staircase:</strong> </td><td style='width: 400px'>" . $interiorLogStaircase . "</td></tr>";

			// Interior Guard railing
			switch ($params["guard_railing"]) {
				case 'All':
					$interiorGuardRailing = 'Complete, with railing and newel posts';
					break;
				case 'Newels':
					$interiorGuardRailing = 'Newel posts only (no railing)';
					break;
				case 'CS':
					$interiorGuardRailing = 'Do not include';
					break;
				default:
					$interiorGuardRailing = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Interior Guard Railing:</strong> </td><td style='width: 400px'>" . $interiorGuardRailing . "</td></tr>";

			// Exterior deck railing
			switch ($params["deck_railing"]) {
				case 'All':
					$exteriorGuardRailing = 'Complete log deck railing';
					break;
				case 'Newels':
					$exteriorGuardRailing = 'Newel posts only for deck (no railing)';
					break;
				case 'CS':
					$exteriorGuardRailing = 'Do not include';
					break;
				default:
					$exteriorGuardRailing = '';
			}
			$selectedOptions .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Exterior deck railing:</strong> </td><td style='width: 400px'>" . $exteriorGuardRailing . "</td></tr>";

			/*
            |--------------------------------------------------------------------------
            | Email content
            |--------------------------------------------------------------------------
            |
            */

			$mail->IsHTML(true);

			$message = '<html><body>';
			// Home plan image and description
			$message .= '<table>';
			$message .= "<tr'><td style='width: 200px'><h3 style='font-size: 24px;'>Personal information</h3></td></tr>";
			$message .= "</table>";

			// Personal information
			$message .= '<table rules="all" style="background-color: rgb(220, 220, 220);" cellpadding="10">';
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>First Name:</strong> </td><td style='width: 400px'>" . strip_tags($params["first_name"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Last name:</strong> </td><td style='width: 400px'>" . strip_tags($params["last_name"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Email address:</strong> </td><td style='width: 400px'>" . strip_tags($params["email_address"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Phone:</strong> </td><td style='width: 400px'>" . strip_tags($params["phone"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Street:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_street"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Apartment no.:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_apt_no"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>City:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_city"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Country:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_country"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>State:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_state"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Postal code:</strong> </td><td style='width: 400px'>" . strip_tags($params["address_postal_code"]) . "</td></tr>";
			$message .= "</table>";

			$message .= '<table>';
			$message .= "<tr'><td style='width: 200px'><h3 style='font-size: 24px;'>Project information</h3></td></tr>";
			$message .= "</table>";

			// Optional contact information
			$message .= '<table rules="all" style="background-color: rgb(220, 220, 220);" cellpadding="10">';
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build place (OPT.):</strong> </td><td style='width: 400px'>" . strip_tags($params["build_place_opt"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build place:</strong> </td><td style='width: 400px'>" . strip_tags($params["build_place"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Currency:</strong> </td><td style='width: 400px'>" . strip_tags($params["currency"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build city:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_build_city"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build country:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_build_country"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build state:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_build_state"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Build date:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_build_date"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Turn key budget:</strong> </td><td style='width: 400px'>" . strip_tags($params["turn_key_budget"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Has purchased land?:</strong> </td><td style='width: 400px'>" . ($params["has_purchased_land"] == '0' ? 'No' : 'Yes') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Has blue print?:</strong> </td><td style='width: 400px'>" . ($params["has_blueprint"] == '0' ? 'No' : 'Yes') . "</td></tr>";

			// Selected options
			$message .= $selectedOptions;
			
			// Home plan details
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Home plan code:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_code"]) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Home plan:</strong> </td><td style='width: 400px'>" . strip_tags($params["plan_name"]) . "</td></tr>";
			// Price
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Shell price:</strong> </td><td style='width: 400px'>" . strip_tags($params["currency"]) . ' ' . strip_tags(number_format($params["shell_price"], 2)) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Total price:</strong> </td><td style='width: 400px'>" . strip_tags($params["currency"]) . ' ' . strip_tags(number_format($params["total_price"], 2)) . "</td></tr>";
			// Additional details
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>Additional details:</strong> </td><td style='width: 400px'>" . strip_tags($params["additional_details"]) . "</td></tr>";

			$message .= "</table>";

			/*
            |--------------------------------------------------------------------------
            | Visitor information
            |--------------------------------------------------------------------------
            |
            */

			$message .= '<table>';
			$message .= "<tr'><td style='width: 200px'><h3 style='font-size: 24px;'>Visitor information</h3></td></tr>";
			$message .= "</table>";

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			$message .= '<table rules="all" style="background-color: rgb(220, 220, 220);" cellpadding="10">';
			$message .= "<tr style='background: #eee;'><td style='width: 200px'><strong>IP Address:</strong> </td><td style='width: 400px'>" . $ip . "</td></tr>";
			$message .= "</table>";

			$message .= '</body></html>';

			/*
            |--------------------------------------------------------------------------
            | Receiver configuration (thelogconnection)
            |--------------------------------------------------------------------------
            |
            */

			$mail->AddAddress('info@thelogconnection.com', 'info@thelogconnection.com');
			$mail->Subject = "[Price quotation] - " . $params["plan_code"];

			$mail->MsgHTML($message);
			if (!$mail->Send()) {
				$responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Failed to send email.';
				return $responseArray;
			}

			$responseArray = App_Response::getResponse('200');
			$responseArray['message'] = "Price Quote succesfully saved";
			return $responseArray;
		} catch (Exception $e) {
			$responseArray = App_Response::getResponse('500');
			$responseArray['message'] = $e->getMessage();
			return $responseArray;
		}
	}

	public function CalculatePrice()
	{
		try {
			$responseArray = array();
			$formData = (array) json_decode(file_get_contents('php://input'), TRUE);
			$formData["Country_Select"] = "Other";

			$common_data = new Common_Services();
			$pq_data = new PriceQuote_Services();

			$materials = $pq_data->GetMaterialsRate();
			$dealers = $common_data->GetDealers();
			$stateprovince = $common_data->GetStateProvince();
			$stateprovince = $this->AddRates($stateprovince);

			$quantities = array();
			$quantitiesAndDesc = $pq_data->GetStockPlanQuantities();
			$quantities = $quantitiesAndDesc["quantities"];

			$formMatrix = array();
			$component = array();
			$formMatrixAndComponent = array();
			$formMatrixAndComponent = $pq_data->GetPriceQuoteMatrix();
			$formMatrix = $formMatrixAndComponent["formMatrix"];
			$component = $formMatrixAndComponent["component"];

			if ($formData['Country_Select'] != 'Other') {
				$spCode = $formData['State_Province'];
			} else {
				$spCode = "Other";
			}
			if (isset($formData['dtoken']) && isset($dealers[$formData['dtoken']])) {
				$thisDealer = $formData['dtoken']; //--dtoken IS VALID
			} else {  //--dtoken IS ABSENT OR INVALID:
				$thisDealer = $stateprovince[$spCode]['dealer'];
				$formData['dtoken'] = "";
			}
			$formData['dealer'] = $thisDealer;
			$Dealer_Code = $formData["dealer"];
			if (!isset($dealers[$Dealer_Code])) {
				$Dealer_Code = "Admin";
			}

			$Dealer_Name = $dealers[$Dealer_Code]['display_name'];
			$Dealer_Address = $dealers[$Dealer_Code]['address'] . $dealers[$Dealer_Code]['address2'];
			$Dealer_EMail = $dealers[$Dealer_Code]['email'];
			$Dealer_Phone = $dealers[$Dealer_Code]['phone'];

			if (!isset($formData['TG_Ceiling'])) {
				$formData['TG_Ceiling'] = "No";
			}
			$spName = $stateprovince[$spCode]['name'];
			$bldgCode = $formData['Building_Location'];
			if (isset($stateprovince[$bldgCode]['name'])) {
				$bldgLocationName = $stateprovince[$bldgCode]['name'];
			} else {
				$bldgLocationName = "";
			}

			$formData['Full_Address'] =  $formData['Address'] . "\n" .  $formData['City'] . "\n" . $spName . $formData['Other'] . "\n" . $formData['Zip'];
			$formData['Html_Address'] =  $formData['Address'] . "<br>" .  $formData['City'] . ", " . $spName . $formData['Other'] . "<BR>" . $formData['Zip'];
			$formData['Building_Full_Address'] = $formData['Building_City'] . ", " .  $bldgLocationName . ", " . $formData['Building_Other_Location'];

			//-- MULTIPLY factorS ACCORDING TO CLIENT'S LOCATION (WILL MODIFY $component[x]['factor']):
			$factor = $stateprovince[$spCode]['rate'];
			$component = $this->MultiplyFactor($component, $factor);
			$location = $stateprovince[$spCode]['name'];

			//-- MULTIPLY factorS ACCORDING TO DOLLAR PREFERENCE (WILL MODIFY $component[x]['factor']):
			if ($formData['Dollar_Preference'] == 'CDN') {
				$factor = $materials['dollar_Cdn'];
				$component = $this->MultiplyFactor($component, $factor);
			}

			//-- CORRECT $component ARRAY FOR EXTERIOR WALL CALCULATION: 
			//--  IF STACKED STYLE WAS CHOSEN, USE Ext_Wall_2_SF FOR EACH $component['Gables'][*]['quantity']
			//--  EG CHANGE $component['Gables']['sheathing']['quantity'] FROM 'Ext_Wall_SF' TO 'Ext_Wall_2_SF'
			if ($formData['Log_Style'] == 'Stacked') {
				foreach ($component['Gables'] as $key => $val) {
					if ($val['quantity'] == 'Ext_Wall_SF') {
						$component['Gables'][$key]['quantity'] = 'Ext_Wall_2_SF';
					}
				}
			}

			$planName = $formData['Plan_Name'];
			$planDisplayName = $quantities[$planName]['Display_Name'];
			$shell_extras = $pq_data->GetShellsExtra($planName);
			$material_extras = $pq_data->GetMaterialsExtra($planName);

			$calculatedArr = array();
			$calculatedArr = $this->CalcPrices($planName, $formMatrix, $component, $materials, $quantities);
			$component = $calculatedArr['component'];
			$formMatrix = $calculatedArr['formMatrix'];
			$materials = $calculatedArr['materials'];
			$quantities = $calculatedArr['quantities'];

			$breakdown = array();
			$breakdown = $this->CreateBreakdown($formData, $quantities, $planName, $formMatrix, $shell_extras, $material_extras);
			$modifiedFormMatrix = array();
			$modifiedFormMatrix = $this->AddFormResults($formData, $formMatrix);
			$responseArray['breakDown'] = $breakdown;
			$responseArray['formMatrix'] = $modifiedFormMatrix;

			$shellPrice = 0;
			$materialsPrice = 0;
			$totalPrice = 0;
			// print_r($modifiedFormMatrix['Log_Style']['price']);
			$shellPrice = $modifiedFormMatrix['Package']['price'] +
				$modifiedFormMatrix['Log_Style']['price'] +
				$modifiedFormMatrix['Log_Type']['price'] +
				$modifiedFormMatrix['Notch']['price'] +
				$modifiedFormMatrix['AWB']['price'] +
				$modifiedFormMatrix['Log_Stair']['price'] +
				$modifiedFormMatrix['Stair_Railing']['price'] +
				$modifiedFormMatrix['Guard_Railing']['price'] +
				$modifiedFormMatrix['Deck_Railing']['price'];
			// $materialsPrice = $modifiedFormMatrix['Roofing']['price'] + 
			// 		$modifiedFormMatrix['TG_Ceiling']['price'] + 
			// 		$modifiedFormMatrix['Walls']['price'] +
			// 		$modifiedFormMatrix['Deck']['price'] + 
			// 		$modifiedFormMatrix['Gables']['price'] +  
			// 		$modifiedFormMatrix['Floor']['price'] +  
			// 		$modifiedFormMatrix['Windows']['price'] +
			// 		$modifiedFormMatrix['Doors']['price'] + 
			// 		$modifiedFormMatrix['Windows_Extra']['price'] +  
			// 		$modifiedFormMatrix['Doors_Extra']['price'];

			$totalPrice = $shellPrice + $materialsPrice;
			$responseArray['shellPrice'] = round($shellPrice, 0);
			$responseArray['materialsPrice'] = round($materialsPrice, 0);
			$responseArray['totalPrice'] = round($totalPrice, 0);
			return $responseArray;
		} catch (Exception $e) {
			$responseArray = App_Response::getResponse('500');
			$responseArray['message'] = $e->getMessage();
			return $responseArray;
		}
	}

	private function CreateBreakdown($formData, $quantities, $planName, $formMatrix, $shell_extras, $material_extras)
	{

		$breakdown = array();
		$breakdown['Blueprints'] = "
		<li>4 Exterior elevations
		<li>1 Foundation plan
		<li>1 Lower floor plan
		<li>1 Main floor plan
		<li>1 Upper floor plan
		<li>2 Cross sections
		<li>Construction details
		<li>Electrical plans";

		$logs = array();
		$logs['Spruce'] = 'Engelmann Spruce';
		$logs['Pine'] = 'Lodgepole Pine';
		$logs['Fir'] =  'Douglas Fir';
		$logs['Cedar'] =  'Western Red Cedar';
		$theLogs = $logs[$formData['Log_Type']];
		$breakdown['Log_Type'] = $theLogs;
		$spiral = $quantities[$planName]['Spiral_Stair'];
		$numSpiralExt = substr_count($spiral, 'ext');
		$numSpiralInt = substr_count($spiral, 'int');
		$numSpirals = 0 + $numSpiralExt + $numSpiralInt;
		if ($numSpiralExt > 0) {
			$spiralExt = "<li>Exterior spiral stair (note: railing for spiral stair not included in log shell)
		";
		} else {
			$spiralExt = "";
		}
		if ($numSpiralInt > 0) {
			$spiralInt = "<li>Interior spiral stair (note: railing for spiral stair not included in log shell)
		";
		} else {
			$spiralInt = "";
		}
		$corner = array();
		$corner['Saddle'] = "Hand hewn Scandinavian saddle notch Log Walls (Standard height = 10'-6&quot;)";
		$corner['Dovetail_Full'] = "Hand hewn dovetailed corner notch Log Walls (Standard height = 10'-6&quot;)";
		$corner['Dovetail_Chinked'] = "Hand hewn dovetailed chinked-style corner notch Log Walls (Standard height = 10'-6&quot;)";
		$corner['Chinked'] = "Hand hewn chinked-style round-notch Log Walls (Standard height = 10'-6&quot;)";
		//$theCorner = $corner[$formData['Log_Style']];
		$theCorner = $corner[$formData['Notch']];


		// ....................................................................................................................
		// .................... SHELL WALLS  ..................................................................................
		// ....................................................................................................................
		if ($formData['Log_Style'] == 'Stacked') {  // ...... STACKED LOG WALLS 
			$breakdown['Shell_Wall'] = "
		<li>$theCorner
		<li>Log walls constructed of $theLogs 
		<li>Average wall log diameter 14&quot; +/-									
		<li>Anchorage system installed to secure home to foundation									
		<li>Pre-drill, include and install 3/4&quot; diameter through-bolts (Includes timber washers and nuts.)									
		<li>Window and door openings pre-cut with bevels to outside and sills sloped									
		<li>Window and door spline cuts (keyway) to accept supplied 2&quot;x2&quot;x1/4&quot; angle iron
		<li>All pinning and bolting around openings and panel walls as required
		<li>Drilling for electrical wire passages									
		<li>Pre-cut for electrical boxes, switches, plugs and lights									
		<li>Interior partition slot cuts for interior framed walls									
		<li>Flatten log for installation of switch and plug face plates									
		<li>Posts flattened to accept finish-framing materials									
		<li>Flattening and slabbing for installation of framing materials and second floor gable ends									
		<li>Flat end trim on posts to provide direct bearing surface and even load transfer									
		<li>Tuck cuts for interior finish framing materials									
		<li>Sanding of knots, logging scars and exposed chain saw cuts									
		<li>Logs treated with protective anti-mildew/ fungicide deterrent									
		<li>All log work conforms with the International Log Builders Association standards
		";
			if ($numSpirals > 0) {
				$breakdown['Shell_Wall'] .= "<li>Spiral stair NOT INCLUDED in log shell price
		";
			}
		}   //... END $formData['Log_Style'] == 'Stacked'


		if ($formData['Log_Style'] == 'PB') {  // ....... POST AND BEAM LOG WALLS
			$breakdown['Shell_Wall'] = "
		<li>Round log post and beam construction
		<li>Log posts are constructed of $theLogs
		<li>Average wall log diameter 13&quot;									
		<li>Wall pitch height as per plans (approx 9'-6&quot;+-)
		<li>Posts flattened to accept finish-framing materials
		<li>Flattening and slabbing for installation of framing materials and second floor gable ends
		<li>Flat end trim on posts to provide direct bearing surface and even load transfer
		<li>Tuck cuts for interior finish framing materials
		<li>Sanding of knots, logging scars and exposed chain saw cuts
		<li>Logs treated with protective anti-mildew/ fungicide deterrent
		<li>Log roof system, ridge beams, purlins
		<li>Flattening and ledging for subsequent framing of second floor framing and gable ends
		<li>Sanding of all exposed chain saw cuts
		<li>All log work conforms with the International Log Builders Association standards
		";
			// . $spiralExt . $spiralInt;
		}   //... END if $postbeam ELSE.


		if ($formData['Log_Style'] == 'Fusion') {
			$breakdown['Shell_Wall'] = "$theCorner
		<li>Log walls constructed of $theLogs 
		<li>Average wall log diameter 13&quot;									
		<li>Anchorage system installed to secure home to foundation									
		<li>Drilling for electrical wire passages									
		<li>Pre-cut for electrical boxes									
		<li>Interior partition slot cuts for interior framed walls									
		<li>Flatten log for installation of plug face plates									
		<li>Posts flattened to accept finish framing materials									
		<li>Flattening and slabbing for installation of framing materials and second floor gable ends									
		<li>Flat end trim on posts to provide direct bearing surface and even load transfer									
		<li>Tuck cuts for interior finish framing materials									
		<li>Sanding of knots, logging scars and exposed chain saw cuts									
		<li>Logs treated with protective anti-mildew/ fungicide deterrent									
		<li>All log work conforms with the International Log Builders Association standards
		" . $spiralExt . $spiralInt;
		}

		if ($formData['Log_Style'] == 'Timber') {
			$breakdown['Shell_Wall'] = "<li>Squared timber construction
		<li>All posts, beams, and braces are constructed of Douglas Fir
		<li>Wall height as per plans (approx 9'-6&quot; +-)
		<li>Timber roof system, ridge beams, purlins
		" . $spiralExt . $spiralInt;
		}


		// ....................................................................................................................
		// .....................................  SHELL ROOF AND UPPER FLOOR ..................................................
		// ....................................................................................................................

		if (($formData['Log_Style'] == 'Stacked') || ($formData['Log_Style'] == 'Fusion')) {
			$breakdown['Shell_Roof'] = "<li>Douglas Fir round log ridge beams									
		<li>Douglas Fir round log purlins									
		<li>Hand scribed round log trusses									
		<li>Log truss bolting hardware									
		<li>Log support posts									
		<li>Flattening and slabbing of roof beams for installation of framing materials									
		<li>All roof connections include permanent positioning and alignment device									
		";
			$breakdown['Shell_Floor'] = "<li>Douglas Fir round log second floor joists									
		<li>Flattening and slabbing for installation of framing materials									
		<li>Douglas Fir round log second floor double log beams									
		<li>Tuck cuts for interior finish framing materials									
		";
		}

		if ($formData['Log_Style'] == 'PB') {  // ....... POST AND BEAM LOG WALLS
			$breakdown['Shell_Roof'] = "<li>Douglas Fir round log ridge beams									
		<li>Douglas Fir round log purlins									
		<li>Hand scribed round log trusses									
		<li>Log truss bolting hardware									
		<li>Log support posts									
		<li>Flattening and slabbing of roof beams for installation of framing materials									
		<li>All roof connections include permanent positioning and alignment device									
		";
			$breakdown['Shell_Floor'] = "<li>Douglas Fir round log second floor joists									
		<li>Flattening and slabbing for installation of framing materials									
		<li>Douglas Fir round log second floor double log beams									
		<li>Tuck cuts for interior finish framing materials									
		";
		}

		if ($formData['Log_Style'] == 'Timber') {    //-- FOR TIMBER: 
			$breakdown['Shell_Roof'] = "<li>Douglas Fir timber ridge beams									
		<li>Douglas Fir timber roof purlins									
		<li>Timber log trusses									
		<li>Truss bolting hardware									
		<li>Timber support posts									
		";
			$breakdown['Shell_Floor'] = "<li>Douglas Fir timber second floor joists									
		<li>Douglas Fir timber second floor double log beams									
		";
		}

		// ....................................................................................................................
		// .....................................  STAIR AND RAILINGS ..................................................
		// ....................................................................................................................
		$breakdown['Stair_and_Railings'] = "";
		if ($formData['Log_Stair'] == 'Log_Stair') {
			$breakdown['Stair_and_Railings'] .= "<li>One interior log staircase								
		";
		} else {
			$breakdown['Stair_and_Railings'] .= "<li>Staircase NOT included (customer will supply)							
		";
		}

		if ($formData['Stair_Railing'] == 'Stair_Railing') {
			$breakdown['Stair_and_Railings'] .= "<li>Log railings for one interior staircase								
		";
		} else {
			$breakdown['Stair_and_Railings'] .= "<li>Railing for staircase NOT included (customer will supply)							
		";
		}

		if ($formData['Guard_Railing'] == 'Newels') {
			$breakdown['Stair_and_Railings'] .= "<li>Newel posts ONLY for railings around openings to below as shown on plan
		<li>Log railings and pickets NOT included
		";
		}
		if ($formData['Guard_Railing'] == 'All') {
			$breakdown['Stair_and_Railings'] .= "<li>Log railings around interior openings to below as shown on plan
		<li>Log Newel posts for interior railings								
		";
		}
		if ($formData['Guard_Railing'] == 'CS') {
			$breakdown['Stair_and_Railings'] .= "<li>Railings around interior openings to below NOT included (customer will supply)								
		";
		}

		if ($formData['Deck_Railing'] == 'Newels') {
			$breakdown['Stair_and_Railings'] .= "<li>Newel posts ONLY for railings around exterior decks as shown on plan
		<li>Log railings and pickets NOT included
		";
		}
		if ($formData['Deck_Railing'] == 'All') {
			$breakdown['Stair_and_Railings'] .= "<li>Log railings around exterior decks as shown on plan
		<li>Log Newel posts for exterior deck railings								
		<li>NOTE: railings for exterior stairways NOT included								
		";
		}
		if ($formData['Deck_Railing'] == 'CS') {
			$breakdown['Stair_and_Railings'] .= "<li>Railings around exterior decks NOT included (customer will supply)								
		";
		}

		// ....................................................................................................................
		// .....................................     OTHER STUFF     ..........................................................
		// ....................................................................................................................

		// ..........   Steel Requirements Supplied and Installed:</span>
		$breakdown['Shell_Steel'] = "";
		if (($formData['Log_Style'] == 'Stacked') || ($formData['Log_Style'] == 'Fusion')) {
			$breakdown['Shell_Steel'] .= "<li>3/4&quot; diameter through bolts with timber washers and nuts									
			<li>1&quot; diameter screw jacks with minimum 1/2&quot; thick plates									
			<li>2&quot;x2&quot;x1/4&quot; pre-cut and drilled window and door angle iron									
			<li>Bottom round anchorage (Rawl anchors)									
		";
		}
		$breakdown['Shell_Steel'] .= "<li>All lagging bolts and drift pins									
		<li>Truss bolts and hardware									
		<li>Post bottom brackets									
		";

		// ..........   Pre Delivery:</span><br>
		$breakdown['Shell_Pre_Delivery'] = "<li>Full visual inspection of all logs and timbers during tagging and numbering.									
		<li>Final spot sanding and cleanup of logs before shipping.									
		<li>Apply non-hazardous wood treatment to prepare for staining, maintain color and deter mold and fungus growth.									
		";

		// ..........   Delivery Advisor:</span>
		$breakdown['Shell_Delivery_Advisor'] = "<li>Our pricing includes on site technical assistance for the entire re-assembly of the shell package.									
		<li>On-site for the duration of shell package re-assembly.									
		<li>Advisors will assist in the erection of the shell package and offer on site technical information.
		";
		if (($formData['Log_Style'] == 'Stacked') || ($formData['Log_Style'] == 'Fusion')) {
			$breakdown['Shell_Delivery_Advisor'] .= "
		<li>Includes pre-erection review, placements of bottom round, anchorage to foundation, stacking of log walls and installation of log roof members.									
		";
		}
		$breakdown['Shell_Delivery_Advisor'] .= "
		<li>After the erection of the shell, our advisors will finish the on site detail work and consult with your general contractor to ensure a full understanding of the framing and finishing details required for a log home.									
		";


		// ..........   Extras:
		$text = "";
		if ($shell_extras) {
			foreach ($shell_extras as $item) {
				$text .=  "<li>$item
				";
			}	 	 //-- END FOREACH
			$breakdown['Shell_Extras'] = $text;
		}	 	 //-- END IF


		// MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM 
		// MMMMMMMMMMMMMMMMMMMMMMMMM   ... MATERIAL PACKAGE OPTIONS ..  MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
		// MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM 

		// ..........   Roof Finishing System:</span><br>
		$thisChoice = $formData['Roofing'];				  //-- CLIENT'S CHOICE FOR Roofing, EG 'Cedar'
		$thisOption = $formMatrix['Roofing'][$thisChoice];		  //-- =ARRAY: $thisOption['joists']=0,['strapping']=564,...
		$roofTotal = $formMatrix['Roofing']['price'] + $formMatrix['TG_Ceiling']['price'];
		$text = "";

		if ($roofTotal) {			//-- ROOF OR CEILING OR BOTH
			if ($thisOption['asphalt']) $text .= '<li>Asphalt Shingles';
			if ($thisOption['cedar']) $text .= '<li>Cedar Shake';
			if ($thisOption['metal']) $text .= '<li>Colored Metal Roofing' .
				'<li>Associated metal flashing, trims, fasteners, and vents';
			if ($thisOption['joists']) $text .= '<li>2x12 Dimensional Roof Rafters @24� o.c.';
			if ($thisOption['sheathing']) $text .= '<li>1/2� OSB Sheathing';
			if ($thisOption['strapping']) $text .= '<li>2x4 Strapping @ 18� o.c.';
			if ($thisOption['bldg_paper']) $text .= '<li>15 # Felt Paper';
			if ($thisOption['vapor_barrier']) $text .= '<li>Poly Vapor Barrier';
			if ($thisOption['insulation']) $text .= '<li>R-38x24 Batt Insulation';
			if ($thisOption['fascia']) $text .= '<li>Triple 1x8 Pine Fascia';
			$breakdown['Roofing'] = $text;
		}

		if ($quantities[$planName]['Roof_Porch_SF']) {	 	  //-- IF PORCH QUANTITY NOT 0
			$text = "";
			if ($thisOption['asphalt']) $text .= '<li>Asphalt Shingles';
			if ($thisOption['cedar']) $text .= '<li>Cedar Shake';
			if ($thisOption['metal']) $text .= '<li>Colored Metal Roofing';
			if ($thisOption['joists']) $text .= '<li>2x Dimensional Roof Rafters @24� o.c.';
			if ($thisOption['sheathing']) $text .= '<li>1/2� OSB Sheathing';
			if ($thisOption['strapping']) $text .= '<li>2x4 Strapping @ 18� o.c.';
			if ($thisOption['bldg_paper']) $text .= '<li>15 # Felt Paper';
			if ($thisOption['fascia']) $text .= '<li>Triple 1x8 Pine Fascia';
			$breakdown['Roof_Porch'] = $text;
		}	 	  		  				//-- END if ($quantities[$planName]['Roof_Porch_SF']....



		// ..........   T + G CEILING:
		//$tgceilingTotal = $formMatrix['TG_Ceiling']['price'];
		$text = "";
		if ($formData['TG_Ceiling'] == 'Yes') {
			$text .= '<li>1x6 Interior Pine Ceiling';
			if ($quantities[$planName]['Roof_Porch_SF']) $text .= '<li>1x6 Porch Pine Ceiling';
			$breakdown['TG_Ceiling'] = $text;
		}


		// ..........   Gable and Dormer System:</span><br>
		$thisChoice = $formData['Gables'];
		$thisOption = $formMatrix['Gables'][$thisChoice];
		$text = "";
		if ($thisChoice != 'CS') {
			if ($thisOption['cedar']) $text .= '<li>Hand Split Cedar Shakes';
			if ($thisOption['tg_siding']) $text .= '<li>1x6 T+G Exterior Pine Siding';
			if ($thisOption['log_siding']) $text .= '<li>Half Log Siding';
			if ($thisOption['sheathing']) $text .= '<li>3/8� OSB Sheathing';
			if ($thisOption['bldg_paper']) $text .= '<li>15 # Felt Paper';
			if ($thisOption['wall_studs']) $text .= '<li>2x6 Exterior Stud Walls @ 16� o.c.';
			if ($thisOption['vapor_barrier']) $text .= '<li>Poly Vapor Barrier';
			if ($thisOption['insulation']) $text .= '<li>R-20x16 Batt Insulation';
			$breakdown['Gables'] = $text;
		}			//-- END if ($thisChoice != 'CS')


		// ..........   UPPER FLOOR ....................................... -->
		// ..........   THIS SECTION ONLY IF 'Floor2_SF' IS NON-ZERO -->

		if ($quantities[$planName]['Floor2_SF']) {	 	  	//-- IF UPPER FLOOR AREA NOT 0

			// ..........   Upper Floor System:</span><br>
			$thisChoice = $formData['Floor'];
			$thisOption = $formMatrix['Floor'][$thisChoice];
			$text = "";
			if ($thisChoice != 'CS') {
				if ($thisOption['sheathing']) {
					$text .= '<li>3/4" T+G Floor sheathing';
				}
				if ($thisOption['joists_2x8']) {
					$text .= '<li>2x8 Dimensional Floor Joists @16" o.c.' .
						'<li>2x8 Solid Blocking where required' .
						'<li>2x8 Ledger Boards' .
						'<li>2x8 Dimensional Rim Joist';
					if ($thisOption['beams']) {
						$text .= '<li>2 x 8 Floor Beam Material';
					}  	 					  //-- END IF $thisOption['beams'])
				}  	 					  //-- END IF ($thisOption['joists_2x8'])
				if ($thisOption['joists_2x10']) {
					$text .= '<li>2x10 Dimensional Floor Joists @16" o.c.' .
						'<li>2x10 Solid Blocking where required' .
						'<li>2x10 Ledger Boards' .
						'<li>2x10 Dimensional Rim Joist';
					if ($thisOption['beams']) $text .= '<li>2x10 Floor Beam Material';
				}  	 					  //-- END IF ($thisOption['joists_2x10'])
				if ($thisOption['decking']) $text .= '<li>1x6 Pine Ceiling';

				$breakdown['Floor'] = $text;
			}			//-- END IF ($thisChoice != 'CS')
		}			//-- END IF UPPER FLOOR AREA NOT 0

		// ...................................... DECK ............................................. -->

		// ........ Exterior Deck Framing:</span><br>
		$thisChoice = $formData['Deck'];
		$thisOption = $formMatrix['Deck'][$thisChoice];
		$text = "";
		if ($thisChoice != 'CS') {
			if ($thisOption['decking']) $text .= '<li>1" x 5" Pressure Treated Finish Decking';
			if ($thisOption['joists_2x10']) {
				$text .= '<li>2x10 Pressure Treated Joists @ 16" o.c.';
				$text .= '<li>2x10 Pressure Treated Perimeter Rim Joist';
				$text .= '<li>2x10 Pressure Treated Ledger Board';
			}
			if ($thisOption['joists_2x8']) {
				$text .= '<li>2x8 Pressure Treated Joists @ 16" o.c.';
				$text .= '<li>2x8 Pressure Treated Perimeter Rim Joist';
				$text .= '<li>2x8 Pressure Treated Ledger Board';
			}
			if ($thisOption['beams']) {
				$text .= '<li>2x10 Pressure Treated Beams';
			}
			//if ($thisOption['posts']) $text .= '<li>6x6 Pressure Treated Beam Support Posts';
			if ($thisOption['deck_railing']) $text .= '<li>Exterior Log Deck Railing';

			$breakdown['Deck'] = $text;
		}			//-- END if ($thisChoice != 'CS') 

		// ...................................... INTERIOR WALLS ........................................ -->
		//..Interior Wall Framing:</span><br>
		$thisChoice = $formData['Walls'];
		$thisOption = $formMatrix['Walls'][$thisChoice];
		$text = "";

		if ($thisChoice != 'CS') {
			if ($thisOption['int_2x4_walls']) $text .= '<li>2x4 Interior Stud Walls @ 16" o.c.';
			if ($thisOption['int_2x6_walls']) $text .= '<li>2x6 Interior Stud Walls @ 16" o.c.';
			$breakdown['Walls'] = $text;
		}			//-- END if ($thisChoice != 'CS') 

		// ...................................... WINDOWS ............................................. -->
		//..Window Package for Main and Upper Floors:</span><br>
		$thisChoice = $formData['Windows'];
		$thisOption = $formMatrix['Windows'][$thisChoice];
		$text = "";

		if ($thisChoice != 'CS') {
			if ($thisOption['wood']) $text .= '<li>Wood Windows';
			if ($thisOption['vinyl']) $text .= '<li>Vinyl Windows (Color)';
			//if ($thisOption['aluminum']) $text .= '<li>Aluminum Windows';
			$text .= '<li>Opening Units Include Bug Screens';
			$text .= '<li>Cardinal Low-E Squared';
			if ($thisOption['vinyl']) $text .= '<li>Breathers';
			$text .= '<li>' . $quantities[$planName]['Windows_Count'] . ' Total Windows and Fixed Glass Included';
			$text .= '<li>NOTE: Window mullions may not be exactly as shown';
			$breakdown['Windows'] = $text;
		}			//-- END if ($thisChoice != 'CS') 

		// ...................................... WINDOWS EXTRA ............................................. -->
		// THIS SECTION ONLY IF 'Windows_Extra_Count' IS NON-ZERO -->
		if ($quantities[$planName]['Windows_Extra_Count']) {

			//.. Windows For Basement:</span><br>
			$thisChoice = $formData['Windows_Extra'];
			$thisOption = $formMatrix['Windows_Extra'][$thisChoice];
			$text = "";

			if ($thisChoice != 'CS') {
				if ($thisOption['wood']) $text .= '<li>Wood Windows';
				if ($thisOption['vinyl']) $text .= '<li>Vinyl Windows (Color)';
				$text .= '<li>Includes Handles and Locks';
				$text .= '<li>Opening Units Include Bug Screens';
				$text .= '<li>Cardinal Low-E Squared';
				if ($thisOption['vinyl']) $text .= '<li>Breathers';
				$text .= '<li>' . $quantities[$planName]['Windows_Extra_Count'] . ' Total Windows and Fixed Glass Included';
				$breakdown['Windows_Extra'] = $text;
			}			//-- END if ($thisChoice != 'CS') 

		} 			//-- END if ($quantities[$planName]['Windows_Extra_Count'])

		// ...................................... DOORS ............................................. -->
		//..Exterior Door Package for Main and Upper Floors:</span><br>
		$thisChoice = $formData['Doors'];
		$thisOption = $formMatrix['Doors'][$thisChoice];
		$text = "";
		if ($thisChoice != 'CS') {
			if ($thisOption['wood']) $text .= '<li>Wood Doors';
			if ($thisOption['metal']) $text .= '<li>Insulated Exterior Steel Doors';
			// $text .= '<li>Includes Handles and Locks';
			$text .= '<li>Cardinal Low-E Squared';
			$text .= '<li>' . $quantities[$planName]['Doors_Count'] . ' Total Doors Included';
			$breakdown['Doors'] = $text;
		}			//-- END if ($thisChoice != 'CS')

		// ...................................... DOORS EXTRA............................................. -->
		// THIS SECTION ONLY IF 'Doors_Extra_Count' IS NON-ZERO -->
		if ($quantities[$planName]['Doors_Extra_Count']) {
			//..Exterior Doors For Basement:</span><br>
			$thisChoice = $formData['Doors_Extra'];
			$thisOption = $formMatrix['Doors_Extra'][$thisChoice];
			$text = "";
			if ($thisChoice != 'CS') {
				if ($thisOption['wood']) $text .= '<li>Wood Doors';
				if ($thisOption['metal']) $text .= '<li>Insulated Exterior Steel Doors';
				// $text .= '<li>Includes Handles and Locks';
				$text .= '<li>Cardinal Low-E Squared';
				$text .= '<li>' . $quantities[$planName]['Doors_Extra_Count'] . ' Total Doors Included';
				$breakdown['Doors_Extra'] = $text;
			}			//-- END if
		}

		// ...................................... MATERIAL EXTRAS............................................. -->
		$text = "";
		if ($material_extras) {
			foreach ($material_extras as $item) {
				$text .= "<li>$item";
			}
			$breakdown['Material_Extras'] = $text;
		}

		return $breakdown;
	}

	private function SendEmailToDealer($formData)
	{
		try {

			$planDisplayName = $formData['Plan_Name'];
			$clientIP = $_SERVER['REMOTE_ADDR'];
			$shellPrice = '';

			$phoneCode = '*NP--';
			if (strlen($formData['Home_Phone']) > 1) $phoneCode = '';
			if (strlen($formData['Work_Phone']) > 1) $phoneCode = '';
			if (strlen($formData['Comments']) > 1) $phoneCode = '*COMMENTS--';
			if (strlen($formData['MakeAnOffer']) > 1) $phoneCode .= 'MAKE AN OFFER--';
			$dtokenCode = false;
			if (strlen($formData['dtoken']) >= 1) $dtokenCode = true;
			$searchChars = array('//', '\\');
			$formData['Last_Name'] = str_replace($searchChars, '', $formData['Last_Name']);

			$mailSubject = "QUOTE-" . $phoneCode . "-NAME: " . $formData['First_Name'] . " " . $formData['Last_Name'] . "--PLAN: " . $planDisplayName;
			if ($dtokenCode)  $mailSubject .= ("--REFERRAL: " . $formData['dtoken']);

			//--FUNCTION TO USE: SortFormItems ($sourceArray,$itemArray,$type)
			$eHtml = "<html>\n";
			$eHtml .= "<head><title>QUOTE--" . $phoneCode . "NAME: " . $formData['First_Name'] . " " . $formData['Last_Name'] . "--PLAN: " . $planDisplayName . "</title></head>";
			$eHtml .= "<body bgcolor='#FFFFFF'>";
			$eHtml .= "<b>QUOTE FOR: " . $formData['First_Name'] . " " . $formData['Last_Name'] . "----FOR PLAN NAME: " . $planDisplayName . "<BR>\n" .
				"<HR width=90% size=1>\n";
			if ($dtokenCode) $eHtml .= ('REFERRED FROM: ' . $formData['dtoken']);
			$eHtml .= ("--Forwarded to dealer: " . $formData['dealer']);
			$eHtml .= "</B><TABLE border=0 cellpadding=2>";
			$eHtml .= "<TR><TD colspan=2><HR>CONTACT INFO<HR></TD></TR>\n";
			$items = array(
				'First_Name', 'Last_Name', 'Address', 'City', 'State_Province', 'Other', 'Zip', 'Full_Address',
				'Home_Phone', 'Home_Phone_Time', 'Work_Phone', 'Work_Phone_Time', 'Fax_Number', 'EMail_Address'
			);
			$eHtml .=  $this->SortFormItems($formData, $items, 'html');
			$eHtml .= "<TR><TD>IP Address:</TD><TD>" . $clientIP . "</TD></TR>\n";
			$eHtml .= "<TR><TD colspan=2><HR>SHELL SELECTIONS<HR></TD></TR>\n";
			$items = array('Plan_Name', 'Log_Style', 'Log_Type', 'Notch', 'AWB', 'Log_Stair', 'Stair_Railing', 'Guard_Railing', 'Deck_Railing');
			$eHtml .=  $this->SortFormItems($formData, $items, 'html');

			//$eHtml .= "<TR><TD colspan=2><HR>MATERIALS SELECTIONS<HR></TD></TR>\n";
			//$items = array('Roofing','TG_Ceiling','Deck','Gables','Floor','Walls','Windows','Windows_Extra','Doors','Doors_Extra');
			//$eHtml .= SortFormItems ($formData,$items,'html');

			$eHtml .= "<TR><TD colspan=2><HR>SITE INFO<HR></TD></TR>\n";
			$items = array('Do_You_Own_Site', 'Building_City', 'Building_Location', 'Building_Other_Location', 'Build_Month', 'Build_Year', 'Budget', 'Dollar_Preference');
			$eHtml .=  $this->SortFormItems($formData, $items, 'html');
			$eHtml .= "<TR><TD colspan=2><HR></TD></TR>\n";
			$eHtml .= "<TR><TD>LOG SHELL PRICE:</TD><TD>$" . number_format($shellPrice, 0) . "</TD></TR>\n";
			if (strlen($formData['MakeAnOffer']) > 1) {
				$eHtml .= "<TR><TD>MAKE AN OFFER:</TD><TD>$" . $formData['MakeAnOffer'] . "</TD></TR>\n";
				$eHtml .= "<TR><TD>MAKE AN OFFER PHONE:</TD><TD>" . $formData['MakeAnOfferPhone'] . "</TD></TR>\n";
			}
			//$eHtml .= "<TR><TD>MATERIALS PRICE:</TD><TD>$" . number_format($materialsPrice, 0) . "</TD></TR>\n";
			//$eHtml .= "<TR><TD>TOTAL PRICE:</TD><TD>$" . number_format($totalPrice, 0) . "</TD></TR>\n";
			$eHtml .= "<TR><TD colspan=2><HR></TD></TR>\n";
			$items = array('Comments');
			$eHtml .= $this->SortFormItems($formData, $items, 'html');
			$eHtml .= "</TABLE>\n";
			$eHtml .= "</body></html>";

			$headers = "From: pricequote_forwarder@thelogconnection.com\n";
			$headers .= "Reply-to: " . $formData['EMail_Address'] . "\n";
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
			$mailTo2 = "macrohard.dev@outlook.com"; //for testing only
			mail($mailTo2, $mailSubject, $eHtml, $headers);
		} catch (Exception $e) {
			throw $e;
		}
	}

	private function SortFormItems($sourceArray, $itemArray, $type)
	{
		$str = "";
		$htm = "";
		foreach ($itemArray as $key) {
			if (isset($sourceArray[$key])) {
				$thisValue = $sourceArray[$key];
			} else {
				$thisValue = "";
			}
			$str .= ($key . ': ' . $thisValue . "\n");
			$htm .= ('<TR><TD>' . $key . ':</TD><TD>' . $thisValue . "</TD></TR>\n");
		}
		if ($type == 'string') {
			return $str;
		} else {
			return $htm;
		}
	}


	private function AddRates($stateprovince)
	{
		foreach ($stateprovince as $sIndex => $sValue) {      //-- EG 'AKN' => array('name'=>'Alaska (North)', 'dealer'=>'Jeff_Lipscomb')...
			if (isset($sValue['dealer'])  && $sValue['dealer']) {
				$stateprovince[$sIndex]['rate'] = 1.00;			   //-- IF A DEALER NAME IS GIVEN, THE RATE IS 1.05	
			} else {
				$stateprovince[$sIndex]['dealer'] = "Admin";	   //-- OTHERWISE IT'S admin AND RATE IS 1.00
				$stateprovince[$sIndex]['rate'] = 1.00;
			}
		}
		return ($stateprovince);
	}

	private function MultiplyFactor($component, $newFactor)
	{
		foreach ($component as $fieldKey => $compName) {					 				//-- EG: 'Roofing' => array['joists']['strapping']...['vapor_barrier']..
			//-- EG: THIS FIELD IS 'Roofing':...
			foreach ($compName as $compKey => $compValue) {								//-- EG: 'joists' => array['material'],['quantity'],['factor']
				//-- EG: THIS COMPONENT IS 'joists':...
				$component[$fieldKey][$compKey]['factor'] = $compValue['factor'] * $newFactor;
			}	 										  //-- END foreach ($compName...
		}
		return $component;												  //-- END foreach ($component...
	}	 //-- END FUNCTION


	private function CalcPrices($thisHome, $formMatrix, $component, $materials, $quantities)
	{
		foreach ($formMatrix as $fieldKey => $option) {					 					//-- EG: 'Roofing' => array['Asphalt']['Cedar']['Metal]
			//-- EG: THIS FIELD IS 'Roofing':...
			foreach ($option as $optionKey => $includeComponent) {						//-- EG: 'Asphalt' => array['joists']['strapping']...['vapor_barrier']..
				//-- EG: THIS OPTION IS 'Asphalt':...
				$price = 0;
				foreach ($includeComponent as $includedKey => $zero_one) {
					// print_r($includeComponent );
					// print_r($component[$fieldKey]);			//-- EG: 'vapor_barrier' => 1
					//-- EG: THIS COMPONENT IS 'vapor_barrier':...
					if ($zero_one) {		   //-- IF IS -8, 1, 0.15, .. BUT NOT 0.
						$thisQuantity = $component[$fieldKey][$includedKey]['quantity'];	 		//-- EG: 'Roof_SF'
						$thisMaterial = $component[$fieldKey][$includedKey]['material'];		//-- EG: 'vb'
						$thisFactor = $component[$fieldKey][$includedKey]['factor'];			//-- EG: 1.15
						$thisPrice = $quantities[$thisHome][$thisQuantity] * $materials[$thisMaterial] * $thisFactor * $zero_one;
						$formMatrix[$fieldKey][$optionKey][$includedKey] = $thisPrice;			//-- REPLACE 0 OR 1 BY ACTUAL PRICE FOR THIS PLAN
						$price += $thisPrice;
					}		//-- END if ($zero_one == 1)
					//-- SAVE THE RAW FACTOR; EG $formMatrix['Log_Type']['Cedar']['surfactor']=0.1
					if ($includedKey == 'surcharge') {
						$formMatrix[$fieldKey][$optionKey]['surfactor'] = $zero_one;
					}
				}	 		//-- END foreach ($includeComponent as $includedKey...
				$price = round($price, 2);
				if ($price === NULL) {
					$formMatrix[$fieldKey][$optionKey]['price'] = 0;
				}
				$formMatrix[$fieldKey][$optionKey]['price'] = $price;	//-- EG: $formMatrix['Roofing]['Asphalt']['price']=13047.56
			}	 			//-- END foreach ($option as $optionKey...
		}

		$result = array();
		$result['formMatrix'] = $formMatrix;
		$result['component'] = $component;
		$result['materials'] = $materials;
		$result['quantities'] = $quantities;
		return $result;
		//-- END foreach ($formMatrix as $fieldKey...
	}	 //-- END FUNCTION

	private function AddFormResults($formData, $formMatrix)
	{
		$msg = "";
		foreach ($formMatrix as $fieldKey => $option) {					 					//-- EG: 'Roofing' => array['Asphalt']['Cedar']['Metal]
			if (isset($formData[$fieldKey])) {
				$sel = $formData[$fieldKey]; 											//-- FORM SELECTED OPTION, EG 'Asphalt'
			} else {
				//$msg .= "FORM FIELD $fieldKey NOT FOUND; "; 									//-- DEFAULT IF NO DATA POSTED WITH THIS INDEX
			}

			$fieldPrice = $formMatrix[$fieldKey][$sel]['price'];  //-- PRICE OF SELECTED OPTION EG: $formMatrix['Roofing']['Asphalt']['price']
			$formMatrix[$fieldKey]['price'] = $fieldPrice;
			foreach ($option as $optionKey => $includeComponent) {  //-- EG: 'Asphalt' => array['joists']['strapping']...['vapor_barrier']..
				//-- EG: THIS OPTION IS 'Asphalt':...
				$thisPrice = $formMatrix[$fieldKey][$optionKey]['price'];
				if ($optionKey == $sel) {
					$formMatrix[$fieldKey][$optionKey]['selected'] = "SELECTED";
					$formMatrix[$fieldKey][$optionKey]['priceDiff'] = 0;
					$formMatrix[$fieldKey][$optionKey]['priceDiffText'] = "";
				} else {
					$formMatrix[$fieldKey][$optionKey]['selected'] = " ";
					$thisPriceDiff = $thisPrice - $fieldPrice;
					$formMatrix[$fieldKey][$optionKey]['priceDiff'] = $thisPriceDiff;
					if ($thisPriceDiff > 0) {
						$formMatrix[$fieldKey][$optionKey]['priceDiffText'] = " (...ADD: $" . number_format($thisPriceDiff, 0) . ")";
					}
					if ($thisPriceDiff < 0) {
						$formMatrix[$fieldKey][$optionKey]['priceDiffText'] = " (...DEDUCT: $" . number_format(-$thisPriceDiff, 0) . ")";
					}
				}
			}	 //-- END foreach ($option as $optionKey...

		}	 //-- END foreach ($formMatrix as $fieldKey...
		return $formMatrix;
	}
}
