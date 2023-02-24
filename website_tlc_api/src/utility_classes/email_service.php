


<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
    // if accessing this class directly through URL, send 404 and exit
    // this section of code will only work if you have a 404.html file in your root document folder.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

define("COMMON_CSV_PATH", CSV_BASE_URL . "common/");

class Email_Services extends Data_Reader
{
    public function __construct() { }

    public function SendEmail_ContactTLC($message, $from)
	{
		try {
            $to = 'loghomes@thelogconnection.com';
            $subject = '[TLC] - Contact Form';
            $headers = "From: " . strip_tags($from) . "\r\n";
            $headers .= "Reply-To: ". strip_tags($from) . "\r\n";
            // $headers .= "CC: test@example.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			mail($to, $subject, $message, $headers);
		} catch (Exception $e) {
			throw $e;
		}
	}

}


