<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
	// if accessing this class directly through URL, send 404 and exit
	// this section of code will only work if you have a 404.html file in your root document folder.
	header("Location: /404.html", TRUE, 404);
	echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
	die;
}


class Order_Plan_Book extends Data_Access
{

	public function __construct()
	{
		// attempt database connection
		$res = $this->dbConnect();

		// if we get anything but a good response ...
		if ($res['response'] != '200') {
			echo "Houston? We have a problem.";
			die;
		}
	}

    public function SavePlanBookOrder()
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

			$script = "INSERT INTO order_plan_book ($columns) VALUES ($parameters)";
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
						$responseArray['message'] = "Order succesfully saved";
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