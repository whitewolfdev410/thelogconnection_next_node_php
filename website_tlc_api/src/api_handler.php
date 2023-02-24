<?php

if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
	// If someone tries to browse directly to this PHP file, send 404 and exit. It can only included
	// as part of our API.
	header("Location: /404.html", TRUE, 404);
	echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
	die;
}

//GLOBAL CONSTANTS
define("ENVIRONMENT", "local"); // "local or prod"
define("CSV_BASE_URL", "./data/");
// we'll move the DB credentials into an INI file in the next lesson and create an app setup class that 
// defines all constants from an app_config database table.

if (ENVIRONMENT == "prod") {
	define("ASSETS_BASE_URL", "http://thelogconnection.com/_assets/");
	define("CONST_DB_HOST", "localhost");  // update with the location of your MySQL host.
	define("CONST_DB_PORT", 3306);
	define("CONST_DB_USERNAME", "charls");
	define("CONST_DB_PASSWORD", "TLCzgmfx10aCRN");
	define("CONST_DB_SCHEMA", "thelogco_2021");
	define("CONST_DB_USER_ID", "TLC");
} else {
	define("ASSETS_BASE_URL", "http://localhost:3000/_assets/");
	define("CONST_DB_HOST", "localhost");  // update with the location of your MySQL host.
	define("CONST_DB_PORT", 8889);
	define("CONST_DB_USERNAME", "root");
	define("CONST_DB_PASSWORD", "root");
	define("CONST_DB_SCHEMA", "thelogco_2021");
	define("CONST_DB_USER_ID", "TLC");
}


class API_Handler
{

	private $function_map;

	//--------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{
		$this->loadFunctionMap();
	}

	//----------------------------------------------------------------------------------------------------------------------
	public function execCommand($varFunctionName, $varFunctionParams)
	{

		// get the actual function name (if necessary) and the class it belongs to.
		$returnArray = $this->getCommand($varFunctionName);

		// if we don't get a function back, then raise the error
		if ($returnArray['success'] == FALSE) {
			return $returnArray;
		}
		$class = $returnArray['dataArray']['class'];
		$functionName = $returnArray['dataArray']['function_name'];

		// Execute User Profile Commands
		$cObjectClass = new $class();
		$returnArray = $cObjectClass->$functionName($varFunctionParams);
		return $returnArray;
	}

	//----------------------------------------------------------------------------------------------------------------------
	private function getCommand($varFunctionName)
	{

		// get the actual function name and the class it belongs to.
		if (isset($this->function_map[$varFunctionName])) {
			$dataArray['class'] = $this->function_map[$varFunctionName]['class'];
			$dataArray['function_name'] = $this->function_map[$varFunctionName]['function_name'];
			$returnArray = App_Response::getResponse('200');
			$returnArray['dataArray'] = $dataArray;
		} else {
			$returnArray = App_Response::getResponse('405');
		}

		return $returnArray;
	}

	//----------------------------------------------------------------------------------------------------
	private function getToken($varParams)
	{

		// api key is required
		if (!isset($varParams['api_key']) || empty($varParams['api_key'])) {
			$returnArray = App_Response::getResponse('400');
			return $returnArray;
		}

		$apiKey = $varParams['api_key'];

		// get the api key object
		$cApp_API_Key = new App_API_Key;
		$res = $cApp_API_Key->getRecordByAPIKey($apiKey);

		// if anything looks sketchy, bail.
		if ($res['response'] !== '200') {
			return $res;
		}

		$apiSecretKey = $res['dataArray'][0]['api_secret_key'];

		$payloadArray = array();
		$payloadArray['apiKey'] = $apiKey;
		$token = JWT::encode($payloadArray, $apiSecretKey);

		$returnArray = App_Response::getResponse('200');
		$returnArray['dataArray'] = array("token" => $token);

		return $returnArray;
	}

	//----------------------------------------------------------------------------------------------------------------------
	private function loadFunctionMap()
	{

		// load up all public facing functions
		$this->function_map = [
			//Security
			'getToken' => ['class' => 'API_Handler', 'function_name' => 'getToken'],
			//Home Plans 
			'SavePriceQuote' => ['class' => 'Price_Quote', 'function_name' => 'SavePriceQuote'],
			'CaptureUserActivity' => ['class' => 'Price_Quote', 'function_name' => 'CaptureUserActivity'],
			'CalculatePrice' => ['class' => 'Price_Quote', 'function_name' => 'CalculatePrice'],
			'GetStockPlanQuantities' => ['class' => 'PriceQuote_Services', 'function_name' => 'GetStockPlanQuantities'],
			'GetLogHomePlans' => ['class' => 'HomePlan_Services', 'function_name' => 'GetLogHomePlans'],
			'GetFilteredHomePlans' => ['class' => 'HomePlan_Services', 'function_name' => 'GetFilteredHomePlans'],
			'GetPlanImagesData' => ['class' => 'HomePlan_Services', 'function_name' => 'GetPlanImagesData'],
			'SearchHomePlan' => ['class' => 'HomePlan_Services', 'function_name' => 'SearchHomePlan'],
			//Study Set
			'SaveStudySetOrderDetails' => ['class' => 'Order_Study_Set', 'function_name' => 'SaveStudySetOrderDetails'],
			//Plan Book	
			'SavePlanBookOrder' => ['class' => 'Order_Plan_Book', 'function_name' => 'SavePlanBookOrder'],
			//Projects
			'GetProjectDetails' => ['class' => 'Project_Services', 'function_name' => 'GetProjectDetails'],
			'GetProjectList' => ['class' => 'Project_Services', 'function_name' => 'GetProjectList'],
			//Gallery
			'GetGalleryList' => ['class' => 'Gallery_Services', 'function_name' => 'GetGalleryList'],
			//Building Stlyes
			'GetConstructionDetails' => ['class' => 'Building_Styles_Services', 'function_name' => 'GetConstructionDetails'],
			//ContactUs 
			'SendMessage' => ['class' => 'ContactUs', 'function_name' => 'SendMessage'],
			//Newsletter
			'GetNewsletter' => ['class' => 'Newsletter_Services', 'function_name' => 'GetNewsletter'],
			'GetNewsletterByTemplate' => ['class' => 'Newsletter_Services', 'function_name' => 'GetNewsletterByTemplate'],
			'SubscribeToNewsletter' => ['class' => 'Newsletter', 'function_name' => 'SubscribeToNewsletter'],
			'SubscribeToNewsletterGeneric' => ['class' => 'Newsletter', 'function_name' => 'SubscribeToNewsletterGeneric']
		];
	}

	//--------------------------------------------------------------------------------------------------------------------
	public function validateRequest($varAPIKey = NULL, $varToken = NULL)
	{

		// this function requires and API key and token parameters
		// if (!$varAPIKey || !$varToken) {
		// 	$returnArray = App_Response::getResponse('403');
		// 	$returnArray['responseDescription'] .= " Missing API key or token.";
		// 	return $returnArray;
		// }

		// get the api key object
		// $cApp_API_Key = new App_API_Key;
		// $res = $cApp_API_Key->getRecordByAPIKey($varAPIKey);
		// unset($cApp_API_Key);

		// // if anything looks sketchy, bail.
		// if ($res['response'] !== '200') {
		// 	return $res;
		// }

		// // get the client API secret key.
		// $apiSecretKey = $res['dataArray'][0]['api_secret_key'];

		// // decode the token
		// try {
		// 	$payload = JWT::decode($varToken, $apiSecretKey, array('HS256'));
		// }
		// catch(Exception $e) {
		// 	$returnArray = App_Response::getResponse('403');
		// 	$returnArray['responseDescription'] .= " ".$e->getMessage();
		// 	return $returnArray;
		// }

		// // get items out of the payload
		// $apiKey = $payload->apiKey;
		// if (isset($payload->exp)) {$expire = $payload->exp;} else {$expire = 0;}

		// // if api keys don't match, kick'em out
		// if ($apiKey !== $varAPIKey) {
		// 	$returnArray = App_Response::getResponse('403');
		// 	$returnArray['responseDescription'] .= " Invalid API Key.";
		// 	return $returnArray;
		// }

		// // if token is expired, kick'em out
		// $currentTime = time();
		// if (($expire !== 0) && ($expire < $currentTime)) {
		// 	$returnArray = App_Response::getResponse('403');
		// 	$returnArray['responseDescription'] .= " Token has expired.";
		// 	return $returnArray;
		// }

		$returnArray = App_Response::getResponse('200');
		return $returnArray;
	}
} // end of class