<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
	// if accessing this class directly through URL, send 404 and exit
	// this section of code will only work if you have a 404.html file in your root document folder.
	header("Location: /404.html", TRUE, 404);
	echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
	die;
}

include './src/php_mailer/PHPMailer.php';
include './src/php_mailer/SMTP.php';
include './src/utility_classes/Logger.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Order_Study_Set extends Data_Access
{

	public function __construct()
	{
		// attempt database connection
		$res = $this->dbConnect();

		// if we get anything but a good response ...
		if ($res['response'] != '200') {
			die;
		}
	}

	public function SaveStudySetOrderDetails()
	{
		try {
			$formData = (array) json_decode(file_get_contents('php://input'), TRUE);
			$params = array();
			//print_r($formData);
			#AY = ABOUT YOU
			$params["first_name"] = $formData['AY_FirstName'];
			$params["last_name"] =  $formData['AY_LastName'];
			$params["email_address"] = $formData['AY_EmailAddress'];
			$params["phone"] = $formData['AY_Phone'];

			#SA = SHIPPING ADDRESS
			$params["sa_street"] = array_key_exists('SA_Street', $formData) ? $formData['SA_Street'] : '';
			$params["sa_apt_no"] = array_key_exists('SA_AptNo', $formData) ? $formData['SA_AptNo'] : '';
			$params["sa_city"] = array_key_exists('SA_City', $formData) ? $formData['SA_City'] : '';
			$params["sa_country"] = array_key_exists('SA_Country', $formData) ? $formData['SA_Country'] : '';
			$params["sa_state"] = array_key_exists('SA_State', $formData) ? $formData['SA_State'] : '';
			$params["sa_postal_code"] = array_key_exists('SA_PostalCd', $formData) ? $formData['SA_PostalCd'] : '';

			// #PI = PROJECT INFORMATION
			$params["pi_country"] = array_key_exists('PI_Country', $formData) ? $formData['PI_Country'] : '';
			$params["pi_state"] = array_key_exists('PI_State', $formData) ? $formData['PI_State'] : '';
			$params["pi_build_date"] = array_key_exists('PI_BuildDate', $formData) ? $formData['PI_BuildDate'] : '';
			$params["pi_turn_key_budget"] = array_key_exists('PI_TurnKeyBudget', $formData) ? $formData['PI_TurnKeyBudget'] : '';
			$params['pi_has_purchased_land'] = array_key_exists('PI_HasPurchasedLand', $formData) && $formData['PI_HasPurchasedLand'] === true ?  1 : 0;
			$params['pi_has_blueprint'] = array_key_exists('PI_HasBlueprint', $formData) && $formData['PI_HasBlueprint'] === true ?  1 : 0;

			// #OP = ORDER PLAN
			$params["op_home_plan_code_1"] = array_key_exists('OP_HomePlanCd1', $formData) ? $formData['OP_HomePlanCd1'] : '';
			$params["op_home_plan_code_2"] = array_key_exists('OP_HomePlanCd2', $formData) ? $formData['OP_HomePlanCd2'] : '';
			$params["op_home_plan_code_3"] = array_key_exists('OP_HomePlanCd3', $formData) ? $formData['OP_HomePlanCd3'] : '';
			$params["op_price_1"] = array_key_exists('OP_Price1', $formData) ? $formData['OP_Price1'] : '';
			$params["op_price_2"] = array_key_exists('OP_Price2', $formData) ? $formData['OP_Price2'] : '';
			$params["op_price_3"] = array_key_exists('OP_Price3', $formData) ? $formData['OP_Price3'] : '';
			$params["op_price_total"] = array_key_exists('OP_PriceTotal', $formData) ? $formData['OP_PriceTotal'] : '';

			// #CC = CREDIT CARD
			$params["cc_type"] = array_key_exists('CC_Type', $formData) ? $formData['CC_Type'] : '';
			$params["cc_holder_name"] = array_key_exists('CC_HolderNm', $formData) ? $formData['CC_HolderNm'] : '';
			$params["cc_number"] = array_key_exists('CC_Nbr', $formData) ? $formData['CC_Nbr'] : '';
			$params["cc_expiration"] = array_key_exists('CC_Expiration', $formData) ? $formData['CC_Expiration'] : '';
			$params["cc_code"] = array_key_exists('CC_Last3Code', $formData) ? $formData['CC_Last3Code'] : '';

			// #ACK = Acknowledgement
			$params['ack_copyright'] = array_key_exists('ACK_Copyright', $formData) && $formData['ACK_Copyright'] === true ?  1 : 0;
			$params['ack_no_refund'] = array_key_exists('ACK_NoRefund', $formData) && $formData['ACK_NoRefund'] === true ?  1 : 0;

			#for audit logs
			$params["delete_flag"] = 0;
			$params["created_by"] = CONST_DB_USER_ID;
			$params["created_dttm"] = gmdate("Y/m/d H:i:s");
			$params["updated_by"] = CONST_DB_USER_ID;
			$params["updated_dttm"] = gmdate("Y/m/d H:i:s");
			$params["client_ip"] = $_SERVER['REMOTE_ADDR'];

			//print_r($params);
			$columns = '';
			$values = array();
			$parameters = str_repeat('?,', count($params) - 1) . '?';
			$types = str_repeat('s', count($params));
			foreach ($params as $key => $value) {
				$columns = $columns . $key . ",";
				array_push($values, $value);
			};
			$columns = rtrim($columns, ",");
			// print_r($values);

			$script = "INSERT INTO order_study_set ($columns) VALUES ($parameters)";
			if (isset($GLOBALS['dbConnection']->errno) && ($GLOBALS['dbConnection']->errno != 0)) {
				$responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Internal server error. MySQL error: ' . $GLOBALS['dbConnection']->errno . ' ' . $GLOBALS['dbConnection']->error;
			} else {
				//echo ($script);
				$stmt = $GLOBALS['dbConnection']->prepare($script);
				if ($stmt) {
					$stmt->bind_param($types, ...$values);
					$stmt->execute();
					if ($stmt->affected_rows > 0) {
						$responseArray = App_Response::getResponse('200');
						$responseArray['message'] = "Study Quote succesfully saved";
					} else {
						throw new Exception('No record inserted');
					}
				} else {
					$responseArray = App_Response::getResponse('400');
				}
			}

			/*
			|--------------------------------------------------------------------------
			| Send study set details to 'order_study@thelogconnection.com'
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
			$mail->Username   = "order_study@thelogconnection.com";
			$mail->Password   = "!\$tudY*2022";

			/*
			|--------------------------------------------------------------------------
			| Sender details
			|--------------------------------------------------------------------------
			|
			*/

			$mail->SetFrom('order_study@thelogconnection.com', 'Study set');
			$mail->AddReplyTo('order_study@thelogconnection.com', 'Study set');

			/*
			|--------------------------------------------------------------------------
			| Email content
			|--------------------------------------------------------------------------
			|
			*/

			$mail->IsHTML(true);

			$message = '<html><body>';
			// Client details
			$message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
			$message .= "<tr style='background: #eee;'><td><strong>First Name:</strong> </td><td>" . strip_tags($formData['AY_FirstName']) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Last Name:</strong> </td><td>" . strip_tags($formData['AY_LastName']) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Email:</strong> </td><td>" . strip_tags($formData['AY_EmailAddress']) . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Phone:</strong> </td><td>" . strip_tags($formData['AY_Phone']) . "</td></tr>";
			// Shipping address
			$message .= "<tr style='background: #eee;'><td><strong>Street:</strong> </td><td>" . (array_key_exists('SA_Street', $formData) ? $formData['SA_Street'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Apartment no.:</strong> </td><td>" . (array_key_exists('SA_AptNo', $formData) ? $formData['SA_AptNo'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>City:</strong> </td><td>" . (array_key_exists('SA_City', $formData) ? $formData['SA_City'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Country:</strong> </td><td>" . (array_key_exists('SA_Country', $formData) ? $formData['SA_Country'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>State:</strong> </td><td>" . (array_key_exists('SA_State', $formData) ? $formData['SA_State'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Postal code:</strong> </td><td>" . (array_key_exists('SA_PostalCd', $formData) ? $formData['SA_PostalCd'] : '') . "</td></tr>";
			// Project details
			$message .= "<tr style='background: #eee;'><td><strong>Project country:</strong> </td><td>" . (array_key_exists('PI_Country', $formData) ? $formData['PI_Country'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Project state:</strong> </td><td>" . (array_key_exists('PI_State', $formData) ? $formData['PI_State'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Project build date:</strong> </td><td>" . (array_key_exists('PI_BuildDate', $formData) ? $formData['PI_BuildDate'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Project trun key budget:</strong> </td><td>" . (array_key_exists('PI_TurnKeyBudget', $formData) ? $formData['PI_TurnKeyBudget'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Has purchased land?:</strong> </td><td>" . (array_key_exists('PI_HasPurchasedLand', $formData) && $formData['PI_HasPurchasedLand'] == 1 ? 'Yes' : 'No') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Has blueprint?:</strong> </td><td>" . (array_key_exists('PI_HasBlueprint', $formData) && $formData['PI_HasBlueprint'] == 1 ? 'Yes' : 'No') . "</td></tr>";
			// Order details
			$message .= "<tr style='background: #eee;'><td><strong>Home plan code 1:</strong> </td><td>" . (array_key_exists('OP_HomePlanCd1', $formData) ? $formData['OP_HomePlanCd1'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Home plan code 2:</strong> </td><td>" . (array_key_exists('OP_HomePlanCd2', $formData) ? $formData['OP_HomePlanCd2'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Home plan code 3:</strong> </td><td>" . (array_key_exists('OP_HomePlanCd3', $formData) ? $formData['OP_HomePlanCd3'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Home plan 1 price:</strong> </td><td>" . (array_key_exists('OP_Price1', $formData) ? $formData['OP_Price1'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Home plan 2 price:</strong> </td><td>" . (array_key_exists('OP_Price2', $formData) ? $formData['OP_Price2'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Home plan 3 price:</strong> </td><td>" . (array_key_exists('OP_Price3', $formData) ? $formData['OP_Price3'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Total price:</strong> </td><td>" . (array_key_exists('OP_PriceTotal', $formData) ? $formData['OP_PriceTotal'] : '') . "</td></tr>";
			// Credit card details
			$message .= "<tr style='background: #eee;'><td><strong>Credit card number:</strong> </td><td>" . (array_key_exists('CC_Type', $formData) ? $formData['CC_Type'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Cardholder's name:</strong> </td><td>" . (array_key_exists('CC_HolderNm', $formData) ? $formData['CC_HolderNm'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Credit card number:</strong> </td><td>" . (array_key_exists('CC_Nbr', $formData) ? $formData['CC_Nbr'] : '') . "</td></tr>";
			$message .= "<tr style='background: #eee;'><td><strong>Expiry date:</strong> </td><td>" . (array_key_exists('CC_Expiration', $formData) ? $formData['CC_Expiration'] : '') . "</td></tr>";
			// $message .= "<tr style='background: #eee;'><td><strong>Code:</strong> </td><td>" . (array_key_exists('CC_Last3Code', $formData) ? $formData['CC_Last3Code'] : '') . "</td></tr>";
			$message .= "</table>";
			$message .= "</body></html>";

			/*
			|--------------------------------------------------------------------------
			| Receiver configuration
			|--------------------------------------------------------------------------
			|
			*/

			$mail->AddAddress("order_study@thelogconnection.com", "Order study set");
			$mail->Subject = "[New study set order from " . $formData['AY_EmailAddress'] . "] - Details";
			$content = $message;

			/*
			|--------------------------------------------------------------------------
			| Send email
			|--------------------------------------------------------------------------
			|
			*/

			$mail->MsgHTML($content);
			if ($mail->Send()) {
				$responseArray = App_Response::getResponse('200');
				$responseArray['message'] = "Study set saved successfully.";
			} else {
				$responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Failed to send email.';
				return $responseArray;
			}
		} catch (Exception $e) {
			$responseArray = App_Response::getResponse('500');
			$responseArray['message'] = $e->getMessage();
			return $responseArray;
		}
	}


	// private function sendEmailToDealer($params) {

	// 	  //-- SEND EMAIL OF ORDER:    
	// 	  $mailSubject = "STUDY SET ORDER--NAME: " . $params['AY_FirstName'] . " " . $params['AY_LastName'] . 
	// 	  "--PLAN: " . $params[];
	// 	  if ($planName2 != "" ) $mailSubject .=  " --AND: " . $planName2;
	// 	  $mailSubject .= $couponNote;
	// 	  //--FUNCTION TO USE: SortFormItems ($sourceArray,$itemArray,$type)
	// 	  $eHtml = "<html>\n";
	// 	  $eHtml .= "<head><title>" . $mailSubject . "</title></head>";
	// 	  $eHtml .= "<body bgcolor='#FFFFFF'>";
	// 	  $eHtml .= "<b>$mailSubject</b><BR>\n";
	// 	  $eHtml .= "<HR width=90% size=1>\n" .
	// 			 "<TABLE border=0 cellpadding=2>";
	// 	  $items = array('Plan_Code_1','Order_Price_1','Plan_Code_2','Order_Price_2','Order_Price_Total');
	// 	  $eHtml .= SortFormItems ($formData,$items,'html');
	// 	  $eHtml .= "<TR><TD colspan=2><HR>CONTACT INFO<HR></TD></TR>\n";
	// 	  $items = array('FirstName','LastName','StreetAddress1','StreetAddress2','City','State_Name',
	// 		  'ZipCode','Country','Phone_Daytime','Phone_Other','FAX','Email');
	// 	  $eHtml .= SortFormItems ($formData,$items,'html');
	// 	  $eHtml .= "<TR><TD colspan=2><HR>CREDIT CARD INFO<HR></TD></TR>\n";
	// 	  $items = array('Ordering_CardType','Ordering_CardHolderName','Ordering_CardNumber','Ordering_CardExpiration');
	// 	  $eHtml .= SortFormItems ($formData,$items,'html');
	// 	  $eHtml .= "<TR><TD colspan=2><HR>SHIPPING INFO<HR></TD></TR>\n";
	// 	  $items = array('Shipping_Method');
	// 	  $eHtml .= SortFormItems ($formData,$items,'html');
	// 	  $eHtml .= "</TABLE>\n";
	// 	  $eHtml .= "</body></html>";
	// 	  $headers = "From: order_study@thelogconnection.com\n";
	// 	  $headers .= "Reply-to: " . $formData['Email'] . "\n";
	// 	  $headers .= "MIME-Version: 1.0\n";
	// 	  $headers .= "Content-Type: text/html; charset=ISO-8859-1\n";

	// 	mail($mailTo, $mailSubject, $eHtml, $headers);

	// 	$IP_Address = $_SERVER['REMOTE_ADDR'];
	// 	$keyString = 'Date_Ordered,IP_Address';
	// 	$valueString = "'" . date("F j, Y, g:i a T") . "','" . $IP_Address . "'";
	// 	unset ($formData['Ordering_CardNumber']); //-- DON'T SAVE THIS FOR SECURITY REASONS
	// 	unset ($formData['Ordering_CardExpiration']); //-- DON'T SAVE THIS FOR SECURITY REASONS		  
	// }

}
