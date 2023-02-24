<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
    // if accessing this class directly through URL, send 404 and exit
    // this section of code will only work if you have a 404.html file in your root document folder.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

define("NEWSLETTER_CSV_PATH", CSV_BASE_URL . "newsletter/");
define("ASSETS_NEWSLETTER_PATH", ASSETS_BASE_URL . "newsletter/");

class NewsLetter_Services extends Data_Reader
{
    public function __construct(){}

    public function GetNewsletter($filter)
    {
        try {

            $file = NEWSLETTER_CSV_PATH . "newsletter.csv";
            $data = $this->ReadCSV($file);
            $tempResult = array();

            $temp = array_filter($data, function ($obj) use ($filter) {
                if (isset($obj['Status'])) {
                    if ($obj['Status'] == $filter) {
                        return true;
                    }
                }
                return false;
            });
            $tempResult = array_values($temp);

            $result = array();
            foreach ($tempResult as $value) {
                array_push($result, $value);
            }
            $responseArray = App_Response::getResponse('200');
            $responseArray = $result;
            return $responseArray;
        } catch (Exception $e) {
            $responseArray = App_Response::getResponse('500');
            $responseArray['message'] = $e->getMessage();
            return $responseArray;
        }
    }

    public function GetNewsletterByTemplate($newsletterCd)
    {
        try {

            $file = NEWSLETTER_CSV_PATH . "newsletter.csv";
            $data = $this->ReadCSV($file);
            $tempResult = array();

            $temp = array_filter($data, function ($obj) use ($newsletterCd) {
                if (isset($obj['NewsletterCd'])) {
                    if ($obj['NewsletterCd'] == $newsletterCd) {
                        return true;
                    }
                }
                return false;
            });
            $tempResult = array_values($temp);

            $result = array();
            foreach ($tempResult as $value) {
                array_push($result, $value);
            }
            $responseArray = App_Response::getResponse('200');
            $responseArray = $result;
            return $responseArray;
        } catch (Exception $e) {
            $responseArray = App_Response::getResponse('500');
            $responseArray['message'] = $e->getMessage();
            return $responseArray;
        }
    }
}
