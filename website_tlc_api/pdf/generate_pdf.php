<?php
error_reporting(0);
set_time_limit(20);

/*
|--------------------------------------------------------------------------
| Header settings
|--------------------------------------------------------------------------
|
*/

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');

/*
|--------------------------------------------------------------------------
| Libraries
|--------------------------------------------------------------------------
|
*/

require('lib/fpdf.php');
define('FPDF_FONTPATH', 'lib/font/');
require('lib/fpdi.php');
require('lib/fpdi_protection.php');

class MYPDF extends FPDI_Protection
{
    /*
    |--------------------------------------------------------------------------
    | Add a horizontal line
    |--------------------------------------------------------------------------
    |
    */

    function Rule($size)
    {
        global $leftMargin;
        $length = 500;
        if ($size == 3) $length = 560;
        if ($size == 2) $length = 510;
        if ($size == 1) $length = 400;
        $this->Ln(4);
        $y = $this->GetY();
        $this->line($leftMargin, $y, $length, $y);
        $this->Ln(4);
    }

    /*
    |--------------------------------------------------------------------------
    | Extract template to be used for PDF.
    |--------------------------------------------------------------------------
    |
    | Template contains home plan images, description and floor plan images
    |
    */

    function Header()
    {
        global $importedPageCount;
        $scriptPageNo = $this->PageNo() - $importedPageCount;
        if ($scriptPageNo > 0) {
            $pdfHeaderFile = "_Pricequote_header.pdf";
            $this->setSourceFile($pdfHeaderFile);
            $tplidh = ($this->ImportPage(1));
            $this->useTemplate($tplidh);
        }
    }

    function FooterDISABLED()
    {
        $str = 'All materials � Copyright 1990-2021 by The Log Connection';
        $this->SetY(-35);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, $str, 0, 0, 'C');
    }

    function WriteHeading($str)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0);
        $this->Ln(8);
        $this->write(10, $str);
        $this->Ln(12);
    }

    function WriteSection($section)
    {
        $this->SetFont('times', '', 10);
        $this->SetTextColor(0);
        $theArray = (array) $section;
        foreach ($theArray as $item) {
            $this->write(9, $item);
            $this->Ln(10);
        }
    }

    function WriteSubtotal($caption, $cost)
    {
        global $formData;
        $this->Rule(2);
        $this->SetFont('Arial', 'B', 11);
        $this->write(12, '   ' . $caption);
        $text = '$' . number_format($cost, 0) . ' ' . $formData['Dollar_Preference'] . '            ';
        $this->cell(0, 12, $text, 0, 0, 'R');
        $this->Ln(16);
    }

    function WriteTotal($caption, $cost)
    {
        global $formData;
        $this->Rule(3);
        $this->Ln(6);
        $this->SetFont('Arial', 'B', 14);
        $this->write(12, $caption);
        $text = '$' . number_format($cost, 0) . ' ' . $formData['Dollar_Preference'];
        $this->cell(0, 12, $text, 0, 0, 'R');
        $this->Ln(16);
        $this->Rule(3);
    }

    function WriteBulletList($blt_array)
    {
        $blt_array = (array) $blt_array;
        $w = 500;
        $spacer = 2;
        $bullet = chr(149);
        $blt_width = 8;
        $h = 9;
        $align = 'L';
        $bak_x = $this->x;
        $this->SetFont('times', '', 10);
        $this->SetTextColor(0);
        for ($i = 0; $i < count($blt_array); $i++) {
            $this->SetX($bak_x);
            $this->Cell($blt_width, $h, $bullet, 0, '');
            $this->MultiCell(($w - $blt_width), $h, $blt_array[$i], 0, 1, $align);
            if ($i != (count($blt_array) - 1)) $this->Ln($spacer);
        }
        $this->x = $bak_x;
    }
}

/*
|--------------------------------------------------------------------------
| User input
|--------------------------------------------------------------------------
|
*/

$post = (array) json_decode(file_get_contents('php://input'), TRUE);
ob_start();
$formData["Country_Select"] = $post["Country_Select"];
$formData["State_Province"] = $post["State_Province"];
$formData["Building_Location"] = $post["Building_Location"];
$formData["Address"] = $post["Address"];
$formData["City"] = $post["City"];
$formData["Zip"] = $post["Zip"];
$formData["City"] = $post["City"];
$formData["Building_City"] = $post["Building_City"];
$formData["Building_Other_Location"] = $post["Building_Other_Location"];
$formData["Other"] = $post["Other"];
$formData["Dollar_Preference"] = $post["Dollar_Preference"];
$formData["Log_Style"] = $post["Log_Style"];
$formData["Plan_Name"] = $post["Plan_Name"];
$formData['Log_Type'] = $post['Log_Type'];
$formData['Notch'] = $post['Notch'];
$formData['Log_Stair'] = $post['Log_Stair'];
$formData['Stair_Railing']  = $post['Stair_Railing'];
$formData['Guard_Railing'] = $post['Guard_Railing'];
$formData['Deck_Railing'] = $post['Deck_Railing'];
$formData['Roofing'] = $post['Roofing'];
$formData['Gables'] = $post['Gables'];
$formData['Floor'] = $post['Floor'];
$formData['Deck'] = $post['Deck'];
$formData['Windows'] = $post['Windows'];
$formData['Windows_Extra'] = $post['Windows_Extra'];
$formData['Doors'] = $post['Doors'];
$formData['Doors_Extra'] = $post['Doors_Extra'];
$formData['Walls'] = $post['Walls'];
$formData['First_Name'] = $post['First_Name'];
$formData['Last_Name'] = $post['Last_Name'];
$formData['EMail_Address'] = $post['EMail_Address'];
$formMatrix = $post['formMatrix'];
$breakdown = $post['breakdown'];
$shellPrice = $post['shellPrice'];
$materialsPrice = $post['materialsPrice'];
$totalPrice = $post['totalPrice'];
$planDisplayName = $post["Plan_Name"];
$houseFileName = $post["Plan_Name"];

/*
|--------------------------------------------------------------------------
| PDF configuration
|--------------------------------------------------------------------------
|
*/

$leftMargin = 50;
$topMargin = 80;
$rightMargin = 35;
$bottomMargin = 40;

/*
|--------------------------------------------------------------------------
| Remove empty items from an array
|--------------------------------------------------------------------------
|
*/

function Purge($badArray)
{
    $goodArray = array();
    $m = 0;
    for ($n = 0; $n < count($badArray); $n++) {
        if (($badArray[$n]) and ($badArray[$n] != " ")) {
            $goodArray[$m] = $badArray[$n];
            $m++;
        }
    }
    return $goodArray;
}

/*
|--------------------------------------------------------------------------
| Extract each item from breakdown list
|--------------------------------------------------------------------------
|
*/

$breakdowntx = array();
foreach ($breakdown as $key => $val) {
    $removes = array("\n", "<i>", "</i>", "\r\n", "\r", "\t");
    $str = str_replace($removes, " ", $val);
    $str = str_replace("<li>", "<li>", $str);
    $str = str_replace("<li><li>", "<li>", $str);
    $str = html_entity_decode($str);
    $strarray = explode("<li>", $str);
    $strarray = Purge($strarray);
    $breakdowntx[$key] = ($strarray);
}

/*
|--------------------------------------------------------------------------
| PDF details
|--------------------------------------------------------------------------
|
*/

$title = "The " . $planDisplayName . " Price Quote--The Log Connection";
$subject = "Price Quote for the " . $planDisplayName . ", a log home from from The Log Connection";

/*
|--------------------------------------------------------------------------
| Initialize PDF
|--------------------------------------------------------------------------
|
*/

$pdf = new MYPDF('P', 'pt', 'Letter');
$pdf->SetTitle($title);
$pdf->SetSubject($subject);
$pdf->SetAutoPageBreak(true, $bottomMargin);
$pdf->SetLeftMargin($leftMargin);
$pdf->SetTopMargin($topMargin);
$pdf->SetRightMargin($rightMargin);

/*
|--------------------------------------------------------------------------
| Defines an alias for the total number of pages.
|--------------------------------------------------------------------------
|
*/

$numPages = $pdf->AliasNbPages();

/*
|--------------------------------------------------------------------------
| Set template for the PDF
|--------------------------------------------------------------------------
|
| Templates contain house images, descriptions and floor plan images
|
*/

$pdfImportFile = "./_pdfs/" . $houseFileName . ".pdf";
if (file_exists($pdfImportFile)) {
    $importedPageCount = $pdf->setSourceFile($pdfImportFile);
    for ($n = 1; $n <= $importedPageCount; $n++) {
        $pdf->addPage();
        $tplidx = $pdf->ImportPage($n);
        $pdf->useTemplate($tplidx);
    }
} else {
    $pdf->write(12, "FILE: $pdfImportFile NOT FOUND.");
}
$pdf->addPage();

/*
|--------------------------------------------------------------------------
| Append dealer information to the PDF
|--------------------------------------------------------------------------
|
| Here 'The log connection' is the dealer.
|
*/

$Dealer_Name = 'The Log Connection';
$Dealer_Phone = '1-888-207-0210';
$Dealer_EMail = 'info@logconnection.com';

$pdf->SetFont('arial', 'B', 10);
$pdf->SetTextColor(120, 120, 120);
$pdf->write(12, 'YOUR LOG CONNECTION CONTACT: ');
$pdf->Ln(12);

$pdf->SetTextColor(0);
$pdf->SetX(96);
$pdf->write(12, $Dealer_Name);
$pdf->Ln(12);
$pdf->SetX(96);
$pdf->write(12, 'Phone: ');
$pdf->write(12, $Dealer_Phone);
$pdf->Ln(12);

$pdf->SetX(96);
$pdf->write(12, 'E-Mail: ');
$pdf->write(12, $Dealer_EMail);
$pdf->Ln(12);

/*
|--------------------------------------------------------------------------
| Append quotation details to the PDF
|--------------------------------------------------------------------------
|
*/

$pdf->SetTextColor(128, 0, 0);
$pdf->SetDrawColor(128, 0, 0);
$pdf->SetFont('times', 'B', 24);
$text = "Quote for:   The " . strtoupper($planDisplayName);
$pdf->Cell(0, 32, $text, 1, 0, 'C', 0);
$pdf->Ln(36);

/*
|--------------------------------------------------------------------------
| Client's name
|--------------------------------------------------------------------------
|
*/

$pdf->SetFont('arial', 'B', 10);
$pdf->SetTextColor(120, 120, 120);
$pdf->write(12, "PREPARED FOR: ");
$pdf->Ln(12);

$pdf->SetX(96);
$pdf->SetTextColor(0);
$pdf->SetFont('times', '', 10);
$text = $formData['First_Name'] . " " . $formData['Last_Name'] . ', ' . $formData['Address'] . ', ' . $formData['City'] . ', ' . $formData['State_Province'] . $formData['Other'] . ' ' . $formData['Zip'];
$pdf->write(12, $text);
$pdf->Ln(12);

/*
|--------------------------------------------------------------------------
| Client's email
|--------------------------------------------------------------------------
|
*/

$pdf->SetX(96);
$pdf->SetFont('arial', '', 10);
$pdf->write(12, "E-Mail:   ");
$pdf->SetFont('times', '', 10);
$pdf->write(12, $formData['EMail_Address']);
$pdf->Ln(14);

/*
|--------------------------------------------------------------------------
| Date of quotation
|--------------------------------------------------------------------------
|
*/

$pdf->SetFont('arial', 'B', 10);
$pdf->SetTextColor(120, 120, 120);
$pdf->write(12, "PREPARED ON: ");
$pdf->SetFont('arial', '', 10);
$pdf->SetTextColor(0);
$pdf->write(12, date("l, F j, Y, g:i a T"));
$pdf->Ln(12);
$pdf->Rule(3);


/*
|--------------------------------------------------------------------------
| List of items included in the quotation
|--------------------------------------------------------------------------
|
*/

$pdf->Ln(15);
$pdf->SetFont('Times', 'B', 16);
$pdf->SetTextColor(256);
$pdf->SetFillColor(128, 0, 0);
$pdf->Cell(0, 25, 'Details of Shell', 0, 0, 'C', 1);
$pdf->Ln(18);

$pdf->SetFillColor(255, 255, 255);
$pdf->WriteHeading('Shell Wall System');
$pdf->WriteBulletList($breakdowntx['Shell_Wall']);

$pdf->WriteHeading('Shell Roof System');
$pdf->WriteBulletList($breakdowntx['Shell_Roof']);

$pdf->WriteHeading('Shell Upper Floor System');
$pdf->WriteBulletList($breakdowntx['Shell_Floor']);

$pdf->WriteHeading('Steel Requirements Supplied and Installed');
$pdf->WriteBulletList($breakdowntx['Shell_Steel']);

$pdf->WriteHeading('Shell Pre Delivery');
$pdf->WriteBulletList($breakdowntx['Shell_Pre_Delivery']);

$pdf->WriteHeading('Delivery Advisor');
$pdf->WriteBulletList($breakdowntx['Shell_Delivery_Advisor']);

if ($shell_extras) {
    $pdf->WriteHeading('Shell Extras');
    $pdf->WriteBulletList($breakdowntx['Shell_Extras']);
}

/*
|--------------------------------------------------------------------------
| Total price
|--------------------------------------------------------------------------
|
*/

$pdf->WriteTotal('Total Price of Shell:', $shellPrice);
$pdf->Ln(14);
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(128, 0, 0);
$pdf->SetDrawColor(128, 0, 0);
$pdf->SetFillColor(239, 237, 231);
$text = '$' . number_format($totalPrice, 0) . ' ' . $formData['Dollar_Preference'];
$pdf->Cell(0, 30, "Total Price (Log Shell and Materials):       $text", 1, 1, 'C', 1);
$pdf->Ln(10);

/*
|--------------------------------------------------------------------------
| Terms and conditions
|--------------------------------------------------------------------------
|
*/

$notlist = array(
    'All quotes subject to verification by our sales staff',
    'Transportation of log package and building material package is not included in total price',
    'Installation of items in Recommended Material Package is not included in total price',
);
$pdf->WriteBulletList($notlist);

/*
|--------------------------------------------------------------------------
| Download PDF file
|--------------------------------------------------------------------------
|
*/

ob_end_clean();
$pdf->Output(($houseFileName . '_Price_Quote.pdf'), 'D');
ob_end_flush();
