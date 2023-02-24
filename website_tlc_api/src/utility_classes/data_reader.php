<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
    // if accessing this class directly through URL, send 404 and exit
    // this section of code will only work if you have a 404.html file in your root document folder.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

class Data_Reader
{

    public function __construct() {}

    protected function GetCSVArray($csvFileName)
    {
        $csvFile = fopen($csvFileName, "r");
        if (!$csvFile) {
            exit($csvFileName . " NOT FOUND. SCRIPT ABORTED.<p>");
            return FALSE;
        }
        //-- READ LINES FROM CSV FILE:
        //-- FIRST LINE CONTAINS NAMES.
        $line = fgetcsv($csvFile, 1000, ",");
        $heads = $line;  //-- EG: $heads[0]='name', $heads[1]='display_name', $heads[2]='company'
        $theArray = array();
        while ($line = fgetcsv($csvFile, 1000, ",")) {
            $item = array();
            for ($n = 1; $n < count($line); $n++) {
                if (isset($line[0])) $theArray[$line[0]][$heads[$n]] = $line[$n]; //-- EG $dealers['Greg_Thompson']['phone'] = '(318) 792-3533'
            }    //-- END for ($n=0;...
        }    //-- END WHILE $line...
        fclose($csvFile);
        return ($theArray);
    }

    protected function GetCSVlines($csvFileName)
    {
        $lines = array();
        if (file_exists($csvFileName)) {
            $csvFile = fopen($csvFileName, "r");
        } else {
            return ($lines);
        }
        //-- READ LINES FROM CSV FILE:
        //-- EG, EACH LINE CONTAINS AN ITEM TO BE LISTED IN THE BREAKDOWN.
        $n = 0;
        while ($line = fgetcsv($csvFile, 1000, ",")) {
            $lines[$n] = $line[0];
            $n++;
        }    //-- END WHILE $line...
        fclose($csvFile);
        return ($lines);
    }

    protected function ReadCSV($csvfile)
    {
        $delimiter = ',';
        if (!file_exists($csvfile) || !is_readable($csvfile))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($csvfile, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    protected function Purge($badArray)
    {
        $goodArray = array();
        $m = 0;
        for ($n = 0; $n < count($badArray); $n++) {
            if (($badArray[$n]) and ($badArray[$n] != " ")) {
                $goodArray[$m] = $badArray[$n];
                $m++;
            }   //-- END IF $badArray[$n]...
        }   //-- END FOR $n=0...
        return $goodArray;
    }

    protected function GetFilteredArray($params, $data)
    {
        $filtredArray = [];
        foreach ($params as $key => $value) {
            foreach ($data as $index => $item) {
                if (array_key_exists($key, $item) && in_array($value, $params)) {
                    if ($item[$key] == $value) {
                        $filtredArray[$index] = $item;
                    } else {
                        continue;
                    }
                }
            }
        }
        return $filtredArray;
    }
}
