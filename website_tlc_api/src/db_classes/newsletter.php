<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
	// if accessing this class directly through URL, send 404 and exit
	// this section of code will only work if you have a 404.html file in your root document folder.
	header("Location: /404.html", TRUE, 404);
	echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
	die;
}

class Newsletter extends Data_Access
{

	public function __construct()
	{
		// attempt database connection
		$res = $this->dbConnect();

		// if we get anything but a good response ...
		if ($res['response'] != '200') {
			echo "Error";
			die;
		}
	}

	public function SubscribeToNewsletter()
	{
		try {
			$formData = (array) json_decode(file_get_contents('php://input'), TRUE);
			$params = array();

			//var_dump($formData);
			$params["first_name"] = array_key_exists('FirstNm', $formData) ? $formData['FirstNm'] : '';
			$params["last_name"] = array_key_exists('LastNm', $formData) ? $formData['LastNm'] : '';
			$params["email_address"] = array_key_exists('EmailAddress', $formData) ? $formData['EmailAddress'] : '';
			$params["post_and_beam"] = array_key_exists('PostAndBeam', $formData) && $formData['PostAndBeam'] == true ? 1 : 0;
			$params["stacked_log"] = array_key_exists('StackedLog', $formData) && $formData['StackedLog'] == true  ? 1 : 0;
			$params["timber_frame"] = array_key_exists('TimberFrame', $formData)  && $formData['TimberFrame'] == true  ? 1 : 0;

			#for audit logs
			$params["delete_flag"] = 0;
			$params["client_ip"] = $_SERVER['REMOTE_ADDR'];

			if (isset($GLOBALS['dbConnection']->errno) && ($GLOBALS['dbConnection']->errno != 0)) {
				$responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Internal server error. MySQL error: ' . $GLOBALS['dbConnection']->errno . ' ' . $GLOBALS['dbConnection']->error;
			} else {
				$eaddStmt = $GLOBALS['dbConnection']->prepare('SELECT id, email_address FROM newsletter WHERE email_address = (?) AND delete_flag = 0');
				$eaddStmt->bind_param('s', $params["email_address"]);
				$eaddStmt->execute();

				$data = $eaddStmt->get_result();
				$rowCount = $data->num_rows;
				if ($rowCount == 0) {
					$params["created_by"] = CONST_DB_USER_ID;
					$params["created_dttm"] = gmdate("Y/m/d H:i:s");
					$params["updated_by"] = CONST_DB_USER_ID;
					$params["updated_dttm"] = gmdate("Y/m/d H:i:s");

					$columns = '';
					$values = array();
					$parameters = str_repeat('?,', count($params) - 1) . '?';
					$types = str_repeat('s', count($params));
					foreach ($params as $key => $value) {
						$columns = $columns . $key . ",";
						array_push($values, $value);
					};
					$columns = rtrim($columns, ",");

					$script = "INSERT INTO newsletter ($columns) VALUES ($parameters)";
		
					$stmt = $GLOBALS['dbConnection']->prepare($script);
					if ($stmt) {
						$stmt->bind_param($types, ...$values);
						$stmt->execute();
						if ($stmt->affected_rows > 0) {
							$responseArray = App_Response::getResponse('200');
							$responseArray['message'] = "Newsletter subscription completed";
						} else {
							throw new Exception('No record inserted');
						}
					} else {
						$responseArray = App_Response::getResponse('400');
					}
				} else {
					$responseArray = App_Response::getResponse('200');
					$responseArray['message'] = "Already Subscribed";
				}
			}
			return $responseArray;
		} catch (Exception $e) {
			$responseArray = App_Response::getResponse('500');
			$responseArray['message'] = $e->getMessage();
			echo ($e->getMessage());
			return $responseArray;
		}
	}

	public function SubscribeToNewsletterGeneric()
	{
		try {
			$formData = (array) json_decode(file_get_contents('php://input'), TRUE);
			$params = array();

			$params["email_address"] = array_key_exists('email_address', $formData) ? $formData['email_address'] : '';
			#for audit logs
			$params["delete_flag"] = 0;
			$params["client_ip"] = $_SERVER['REMOTE_ADDR'];

			if (isset($GLOBALS['dbConnection']->errno) && ($GLOBALS['dbConnection']->errno != 0)) {
				$responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Internal server error. MySQL error: ' . $GLOBALS['dbConnection']->errno . ' ' . $GLOBALS['dbConnection']->error;
			} else {
				$eaddStmt = $GLOBALS['dbConnection']->prepare('SELECT email_address FROM newsletter_generic WHERE email_address = (?) AND delete_flag = 0');
				$eaddStmt->bind_param('s', $params["email_address"]);
				$eaddStmt->execute();

				$data = $eaddStmt->get_result();
				$rowCount = $data->num_rows;
				if ($rowCount == 0) {
					$params["created_by"] = CONST_DB_USER_ID;
					$params["created_dttm"] = gmdate("Y/m/d H:i:s");
					$params["updated_by"] = CONST_DB_USER_ID;
					$params["updated_dttm"] = gmdate("Y/m/d H:i:s");

					$columns = '';
					$values = array();
					$parameters = str_repeat('?,', count($params) - 1) . '?';
					$types = str_repeat('s', count($params));
					foreach ($params as $key => $value) {
						$columns = $columns . $key . ",";
						array_push($values, $value);
					};
					$columns = rtrim($columns, ",");

					$script = "INSERT INTO newsletter_generic ($columns) VALUES ($parameters)";
		
					$stmt = $GLOBALS['dbConnection']->prepare($script);
					if ($stmt) {
						$stmt->bind_param($types, ...$values);
						$stmt->execute();
						if ($stmt->affected_rows > 0) {
							$responseArray = App_Response::getResponse('200');
							$responseArray['message'] = "Newsletter subscription completed";
						} else {
							throw new Exception('No record inserted');
						}
					} else {
						$responseArray = App_Response::getResponse('400');
					}
				} else {
					$responseArray = App_Response::getResponse('200');
					$responseArray['message'] = "Already Subscribed";
				}
			}
			return $responseArray;
		} catch (Exception $e) {
			$responseArray = App_Response::getResponse('500');
			$responseArray['message'] = $e->getMessage();
			echo ($e->getMessage());
			return $responseArray;
		}
	}

	
}
