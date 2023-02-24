<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
    // if accessing this class directly through URL, send 404 and exit
    // this section of code will only work if you have a 404.html file in your root document folder.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

define("PQ_CSV_PATH", CSV_BASE_URL . "home-plans/");

class PriceQuote_Services extends Data_Reader
{
    public function __construct() {}


    public function GetStockPlanQuantities()
    {
        try {
            $file = PQ_CSV_PATH . "stockplan-quantities.csv";
            $responseArray = [];
            $qDescription = array();
            $csvFile = fopen($file, "r");
            if (!$csvFile) {
                exit($file . " NOT FOUND. SCRIPT ABORTED.<p>");
                return;
            }
            //--  FIRST ROW CONTAINS COLUMN NAMES: Plan_Name, Display_Name, Log_Shell_Price, AWB_Price...
            $line = fgetcsv($csvFile, 1000, ",");
            $heads = $line;      //-- MUST NOT SKIP BLANK / EMPTY CELLS????.....

            //--  SECOND ROW CONTAINS COLUMN DESCRIPTIONS: 'Plan Name', 'Display Name', 'Log Shell Price', 'All Weather Barrier'...
            $second = fgetcsv($csvFile, 1000, ",");           //-- = NUMBERED ARRAY: [0],[1],[2]
            for ($n = 0; $n < count($heads); $n++) {
                $qDescription[$heads[$n]] = $second[$n];       //-- EG: $qDescription['AWB_Price'] = 'All Weather Barrier'
            }

            //--  REMAINING ROWS CONTAIN STOCK PLAN QUANTITIES:
            $quantities = array();
            while ($line = fgetcsv($csvFile, 1000, ",")) {
                $name = $line[0];                                          //-- filename FOR THIS ROW; WILL BE USED AS INDEX FOR ARRAY ENTRY
                for ($n = 0; $n < count($line); $n++) {
                    $quantities[$name][$heads[$n]] = $line[$n];    //-- EG: $quantities['Westbury']['Roof_SF'] = 6678
                }    //-- END FOR ($n=0....
            }    //-- END WHILE ($line = fgetcsv....
            $searchChars = array("$", ",");                            //-- NOW STRIP THESE CHARACTERS OUT OR THEY WILL SCREW UP CALCULATIONS
            foreach ($quantities as $q) {                           //-- EG: $quantities['Westbury']
                $name = $q['Plan_Name'];
                foreach ($q as $qKey => $qValue) {                   //-- EG: 'Plan_Name'=>'Westbury','Display_Name'=>'Westbury','Log_Shell_Price'=>156789,..
                    if (is_string($qValue)) {
                        $x = str_replace($searchChars, "", $qValue);
                        $quantities[$name][$qKey] = $x;
                    }                               //-- END if (is_string($qValue)...
                }                                   //-- END foreach ($q as $qKey =...
            }                                       //-- END foreach ($quantities as $q )...
            fclose($csvFile);
            $result = array();
            $result["description"] = $qDescription;
            $result["quantities"] = $quantities;
            $responseArray = $result;
        } catch (Exception $e) {
            $responseArray = App_Response::getResponse('500');
            $responseArray['message'] = $e->getMessage();
        } finally {
            return $responseArray;
        }
    }

    public function GetPriceQuoteMatrix()
    {
        try {
            $filename = PQ_CSV_PATH . "pricequote-matrix.csv";
            $responseArray = [];
            $formMatrix = array();
            $component = array();
            $csvFile = fopen($filename, "r");
            if (!$csvFile) {
                exit($filename . " NOT FOUND. SCRIPT ABORTED.<p>");
                return FALSE;
            }

            //-- READ LINES FROM CSV FILE:
            $line = fgetcsv($csvFile, 1000, ",");
            $flag = (bool) $line;
            $compIndex = array();  //-- INDEX USED BY BOTH $formMatrix AND $component TO INTERACT FOR EACH FORM FIELD
            $field = "";
            $value = "";
            while ($flag) {
                $first = substr($line[0], 0, 1);
                switch ($first) {          //-- ORDER SHOULD BE: #field, :material, :quantity, :factor, client choices...
                    case ' ':                                        //-- ==COMMENT, SO SKIP THIS LINE
                    case '*':                                //-- ==COMMENT 	   	 
                    case NULL:                            //-- ==COMMENT 	   	 
                        break;
                    case '#':                                    //-- = START OF NEW FIELD
                        $temp = array_shift($line);        //-- $line NOW CONTAINS COMPONENT NAMES
                        $field = substr($temp, 1);            //-- STORE NAME OF FIELD (REMOVES THE '#')
                        $compIndex = $this->Purge($line);        //-- FILL $compIndex ARRAY FOR THIS FIELD
                        break;
                    case ':':                              //-- = 'material', 'quantity', or 'factor' FOR THIS FIELD; GO INTO $component ARRAY.
                        $temp = array_shift($line);        //-- $line ARRAY NOW CONTAINS ONLY THE DATA FOR THIS LINE   
                        $head = substr($temp, 1);            //-- 'material', 'quantity', or 'factor'
                        $good = $this->Purge($line);
                        $pos = strpos($head, "factor");
                        if ($pos >= 0) {                    //-- CONVERT 'factor' VALUES TO FLOATS
                            for ($n = 0; $n < count($good); $n++) {
                                //$good[$n] = (float) $good[$n];  //-- THIS DESTROYS EVERYTHING:  WHY ????????????????????????????????
                            }   //-- END FOR $n=0...
                        }   //-- END if ($pos >= 0)...   
                        for ($n = 0; $n < count($good); $n++) {
                            $component[$field][$compIndex[$n]][$head] = $good[$n];  //-- EG $component['Roofing]['vapor_barrier']['quantity'] = 'Roof_SF'
                        }   //-- END FOR $n=0...
                        break;
                    default:                                    //-- = MATRIX ROWS FOR EACH FORM OPTION; GO INTO $formMatrix ARRAY.
                        $temp = array_shift($line);        //-- $line NOW CONTAINS ONLY VALUES, BUT SOME MAY BE EMPTY
                        $value = $temp;                    //-- VALUE NAME FOR THIS ROW, EG: 'CS'
                        for ($n = 0; $n < count($line); $n++) {
                            if (isset($compIndex[$n]) and ($compIndex[$n])) {     //-- ONLY STORE VALUE IF $compIndex[n] ALREADY SET UP
                                $x = (float) $line[$n];
                                $formMatrix[$field][$value][$compIndex[$n]] = $x;  //-- EG $formMatrix['Roofing]['Asphalt']['vapor_barrier'] = 1.0
                            }   //-- END IF (isset($compIndex[$n]...
                        }   //-- END FOR $n=0...
                }        //-- END switch ($first)...
                $line = fgetcsv($csvFile, 1000, ",");
                $flag = (bool) $line;
            }                //-- END WHILE $flag...
            fclose($csvFile);
            //return TRUE;
            $result = array();
            $result["component"] = $component;
            $result["formMatrix"] = $formMatrix;
            $responseArray = $result;
        } catch (Exception $e) {
            $responseArray = App_Response::getResponse('500');
            $responseArray['message'] = $e->getMessage();
        } finally {
            return $responseArray;
        }
    }

    public function GetShellsExtra($planName)
    {
        $result = array();
        $path = PQ_CSV_PATH . $planName . "/shell_extras.csv";
        $result = $this->GetCSVlines($path);
        return $result;
    }

    public function GetMaterialsExtra($planName)
    {
        $result = array();
        $path = PQ_CSV_PATH . $planName . "/material_extras.csv";
        $result = $this->GetCSVlines($path);
        return $result;
    }

    public function GetMaterialsRate()
    {
        $csvFileName = PQ_CSV_PATH . "material-rates.csv";
        $csvFile = fopen($csvFileName, "r");
        if (!$csvFile) {
            exit($csvFileName . " NOT FOUND. SCRIPT ABORTED.<p>");
            return FALSE;
        }
        //-- READ LINES FROM CSV FILE:
        //-- FIRST LINE CONTAINS NAMES: description, material, rate, units, rate2; ONLY material AND rate WILL BE USED.
        $line = fgetcsv($csvFile, 1000, ",");
        $heads = $line;  //-- EG: $heads[0]='description', $heads[1]='material', $heads[2]='rate', $heads[3]='units', $heads[4]='rate2'
        $materials = array();
        while (($line = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
            $item = array();
            for ($n = 0; $n < count($line); $n++) {
                $item[$heads[$n]] = $line[$n];
                //-- EG $item['description'] = $line[0], $item['material'] = $line[1], $item['rate'] = $line[2]
            }    //-- END for ($n=0;...
            $materials[$item['material']] = $item['rate'];       //-- EG $materials['stud_2x4'] = 0.40
        }    //-- END WHILE $line...
        fclose($csvFile);
        return ($materials);
    }
}
