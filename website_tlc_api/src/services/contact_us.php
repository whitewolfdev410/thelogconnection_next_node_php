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
use Logger;

class ContactUs extends Email_Services
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function SendMessage()
    {

        try {

            /*
            |--------------------------------------------------------------------------
            | User input
            |--------------------------------------------------------------------------
            |
            */

            $formData = (array) json_decode(file_get_contents('php://input'), TRUE);
            $firstName = $formData["FirstName"];
            $lastName = $formData["LastName"];
            $emailAdd = $formData["EmailAddress"];
            $phone = $formData["Phone"];
            $bestCallTime = $formData["BestCallTime"];
            $msg = $formData["Message"];

            /*
            |--------------------------------------------------------------------------
            | Store contact us details in database
            |--------------------------------------------------------------------------
            |
            */

            $dbConnection = new mysqli(CONST_DB_HOST, CONST_DB_USERNAME, CONST_DB_PASSWORD, CONST_DB_SCHEMA, CONST_DB_PORT);
            $query = "INSERT INTO contact_us (`first_name`, `last_name`, `email`, `phone`, `best_time_to_call`, `message`) VALUES ('" . $firstName . "', '" . $lastName . "', '" . $emailAdd . "', '" . $phone . "', '" . $bestCallTime . "', '" . $msg . "')";
            $dbConnection->query($query);

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
            $mail->Username   = "contactus@thelogconnection.com";
            $mail->Password   = "tlc(0nta@ctu$!";

            /*
            |--------------------------------------------------------------------------
            | Sender details
            |--------------------------------------------------------------------------
            |
            */

            $mail->SetFrom('contactus@thelogconnection.com', 'Contact us');
            $mail->AddReplyTo('contactus@thelogconnection.com', 'Contact us');

            /*
            |--------------------------------------------------------------------------
            | Email content
            |--------------------------------------------------------------------------
            |
            */

            $mail->IsHTML(true);

            $message = '<html><body>';
            $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
            $message .= "<tr style='background: #eee;'><td><strong>First Name:</strong> </td><td>" . strip_tags($firstName) . "</td></tr>";
            $message .= "<tr style='background: #eee;'><td><strong>Last Name:</strong> </td><td>" . strip_tags($lastName) . "</td></tr>";
            $message .= "<tr style='background: #eee;'><td><strong>Email:</strong> </td><td>" . strip_tags($emailAdd) . "</td></tr>";
            $message .= "<tr style='background: #eee;'><td><strong>Phone:</strong> </td><td>" . strip_tags($phone) . "</td></tr>";
            $message .= "<tr style='background: #eee;'><td><strong>Best Call Time:</strong> </td><td>" . strip_tags($bestCallTime) . "</td></tr>";
            $message .= "<tr style='background: #eee;'><td><strong>Message:</strong> </td><td>" . $msg . "</td></tr>";
            $message .= "</table>";
            $message .= "</body></html>";

            /*
            |--------------------------------------------------------------------------
            | Receiver configuration
            |--------------------------------------------------------------------------
            |
            */

            $mail->AddAddress("loghomes@thelogconnection.com", "Log homes");
            $mail->Subject = "[TLC] - Contact Form";
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
                $responseArray['message'] = "Success";
                return $responseArray;
            } else {
                $responseArray = App_Response::getResponse('500');
				$responseArray['message'] = 'Failed to send email.';
				return $responseArray;
            }

            /*
            |--------------------------------------------------------------------------
            | Success response
            |--------------------------------------------------------------------------
            |
            */

            $responseArray = App_Response::getResponse('200');
            $responseArray['message'] = "Success";
            return $responseArray;
        } catch (Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | Failure response
            |--------------------------------------------------------------------------
            |
            */

            $responseArray = App_Response::getResponse('500');
            $responseArray['message'] = $e->getMessage();
            return $responseArray;
        }
    }
}
