<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
    // if accessing this class directly through URL, send 404 and exit
    // this section of code will only work if you have a 404.html file in your root document folder.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

define("HP_CSV_PATH", CSV_BASE_URL . "home-plans/");
define("HP_GALLERY_CSV_PATH", CSV_BASE_URL . "home-plans/gallery/");
define("ASSETS_HP_FLOOR_PLANS_PATH", ASSETS_BASE_URL . "home-plans/_plans/");
define("ASSETS_HP_PATH", ASSETS_BASE_URL . "home-plans/");

class HomePlan_Services extends Data_Reader
{
    public function __construct()
    {
    }

    public function GetLogHomePlans($planCode = null)
    {

        $tempResult = array();

        $file = HP_CSV_PATH . "home-plans.csv";
        $data = $this->ReadCSV($file);

        if (empty($planCode) || $planCode === "all") {
            $tempResult = $data;
        } else {
            $temp = array_filter($data, function ($obj) use ($planCode) {
                if (isset($obj['planCode'])) {
                    if ($obj['planCode'] == $planCode && $obj['public'] == 'x') {
                        return true;
                    }
                }
                return false;
            });
            $tempResult = array_values($temp);
        }

        $result = array();
        foreach ($tempResult as $value) {
            $imagesPath = ASSETS_HP_PATH . $value['planCode'] . "/";
            $thumbnailPath = ASSETS_HP_PATH . $value['planCode'] . "/thumbs/";

            $value["thumbnail"] = $thumbnailPath . $value["fileName"] . "." . $value["fileNameExt"];
            $value["imageUrl"] = $imagesPath . $value["fileName"] . "." . $value["fileNameExt"];

            $floorPlanImgArr = array();
            if (isset($value["floorPlanCount"])) {
                for ($i = 0; $i < $value["floorPlanCount"]; $i++) {
                    $num =  $i + 1;
                    $fpImgPath = ASSETS_HP_FLOOR_PLANS_PATH . $value["fileName"] . "_f" . $num . ".gif";
                    array_push($floorPlanImgArr, $fpImgPath);
                }
            }
            if (isset($value["sf0"]) && $value["sf0"] != 0) {
                $fpImgPath = ASSETS_HP_FLOOR_PLANS_PATH . $value["fileName"] . "_f0" . ".gif";
                array_push($floorPlanImgArr, $fpImgPath);
            }
            $value["floorPlans"] = $floorPlanImgArr;
            array_push($result, $value);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        |
        | sf represents size
        | name represents name
        |
        */

        $paramsString = $_SERVER['QUERY_STRING'];

        parse_str($paramsString, $paramsArray);

        $sortBy = 'name';
        if (isset($paramsArray['sort_by']) && ($paramsArray['sort_by'] == 'name' || $paramsArray['sort_by'] == 'size')) {
            if ($paramsArray['sort_by'] == 'name') $sortBy = 'name';
            if ($paramsArray['sort_by'] == 'size') $sortBy = 'sf';
        }

        $sortDirection = 'asc';
        if (isset($paramsArray['sort_direction']) && ($paramsArray['sort_direction'] == 'asc' || $paramsArray['sort_direction'] == 'desc')) {
            $sortDirection = $paramsArray['sort_direction'];
        }

        $sortColumn = array_column($result, $sortBy);
        array_multisort($sortColumn, $sortDirection == 'asc' ? SORT_ASC : SORT_DESC, $result);

        return $result;
    }

    public function GetFilteredHomePlans($filter = null)
    {
        $tempResult = array();
        $file = HP_CSV_PATH . "home-plans.csv";
        $data = $this->ReadCSV($file);
        if (empty($filter) || $filter === "all") {
            echo ($filter);
            $tempResult = $data;
        } else {
            $temp = array_filter($data, function ($obj) use ($filter) {
                if (isset($obj[$filter])) {
                    if ($obj[$filter] == 'x' && $obj['public'] == 'x') {
                        return true;
                    }
                }
                return false;
            });
            $tempResult = array_values($temp);
        }

        $result = array();
        foreach ($tempResult as $value) {
            $imagesPath = ASSETS_HP_PATH . $value['planCode'] . "/";
            $thumbnailPath = ASSETS_HP_PATH . $value['planCode'] . "/thumbs/";

            $value["thumbnail"] = $thumbnailPath . $value["fileName"] . "." . $value["fileNameExt"];
            $value["imageUrl"] = $imagesPath . $value["fileName"] . "." . $value["fileNameExt"];

            $floorPlanImgArr = array();
            if (isset($value["floorPlanCount"])) {
                for ($i = 0; $i < $value["floorPlanCount"]; $i++) {
                    $num =  $i + 1;
                    $fpImgPath = ASSETS_HP_FLOOR_PLANS_PATH . $value["fileName"] . "_f" . $num . ".gif";
                    array_push($floorPlanImgArr, $fpImgPath);
                }
            }
            if (isset($value["sf0"]) && $value["sf0"] != 0) {
                $fpImgPath = ASSETS_HP_FLOOR_PLANS_PATH . $value["fileName"] . "_f0" . ".gif";
                array_push($floorPlanImgArr, $fpImgPath);
            }
            $value["floorPlans"] = $floorPlanImgArr;
            array_push($result, $value);
        }

        return $result;
    }

    public function SearchHomePlan()
    {
        try {
            $searchParams = (array)json_decode(file_get_contents('php://input'), TRUE);
            $matchingCodes = array();

            $planArr = $this->GetLogHomePlans();
            foreach ($planArr as $thisPlanCode => $thisPlan) {
                $matchFlag = TRUE;
                if ($searchParams['sfMax'] && ($thisPlan['sf'] > $searchParams['sfMax'])) {
                    $matchFlag = FALSE;
                }
                if ($searchParams['sfMin'] && ($thisPlan['sf'] < $searchParams['sfMin'])) {
                    $matchFlag = FALSE;
                }
                if ($searchParams['width'] && ($thisPlan['Width'] > $searchParams['width'])) {
                    $matchFlag = FALSE;
                }
                if ($searchParams['depth'] && ($thisPlan['Depth'] > $searchParams['depth'])) {
                    $matchFlag = FALSE;
                }
                if ($searchParams['bedsMin'] && ($thisPlan['Beds'] < $searchParams['bedsMin'])) {
                    $matchFlag = FALSE;
                }
                if ($searchParams['bedsMax'] && ($thisPlan['Beds'] > $searchParams['bedsMax'])) {
                    $matchFlag = FALSE;
                }
                if ($searchParams['bathsMin'] && ($thisPlan['Baths'] < $searchParams['bathsMin'])) {
                    $matchFlag = FALSE;
                }
                if ($searchParams['bathsMax'] && ($thisPlan['Baths'] > $searchParams['bathsMax'])) {
                    $matchFlag = FALSE;
                }
                if ($matchFlag) {
                    array_push($matchingCodes, $thisPlan);
                }
            }

            $responseArray = App_Response::getResponse('200');
            $responseArray = $matchingCodes;
            return $responseArray;
        } catch (Exception $e) {
            $responseArray = App_Response::getResponse('500');
            $responseArray['message'] = $e->getMessage();
            return $responseArray;
        }
    }

    public function GetPlanImagesData($planCode)
    {
        try {

            $responseArray = [];
            $filename_default = HP_GALLERY_CSV_PATH . $planCode . ".csv";
            $filename_ext = HP_GALLERY_CSV_PATH . $planCode . "-ext.csv";
            $filename_int = HP_GALLERY_CSV_PATH . $planCode . "-int.csv";
            $default = array();
            $int = array();
            $ext = array();

            $default = $this->ReadImagesDataCSV($filename_default, $planCode);
            $int = $this->ReadImagesDataCSV($filename_int, $planCode);
            $ext = $this->ReadImagesDataCSV($filename_ext, $planCode);

            if (!is_array($default)) {
                $default = [];
            }
            if (!is_array($int)) {
                $int = [];
            }
            if (!is_array($ext)) {
                $ext = [];
            }

            $responseArray = App_Response::getResponse('200');
            $responseArray = array_merge($default, $ext, $int);
            return $responseArray;
        } catch (Exception $e) {
            $responseArray = App_Response::getResponse('500');
            $responseArray['message'] = $e->getMessage();
            return $responseArray;
        }
    }

    private function ReadImagesDataCSV($filename, $planCode)
    {
        try {

            $IMG_BASE_URL = ASSETS_HP_PATH . $planCode . "/";
            $picArray = array();
            if (file_exists($filename)) {
                $f = fopen($filename, "r");
                $i = 0;
                while (($line = fgetcsv($f, 1000, ",")) !== FALSE) {
                    if ((count($line) > 1) &&  ($line[0] != "")) { // IE NOT A BLANK LINE
                        $picArray[$i]['filebasename'] = $line[0];
                        $picArray[$i]['extension'] = $line[1];
                        $picArray[$i]['filename'] = $line[0] . "." . $line[1];
                        $picArray[$i]['imageUrl'] = $IMG_BASE_URL . $picArray[$i]['filename'];
                        //$picArray[$i]['caption'] = $line[2];
                        if (isset($line[2])) {
                            $picArray[$i]['caption'] = htmlspecialchars($line[2], ENT_QUOTES);
                        } else {
                            $picArray[$i]['caption'] = "";
                        }  //-- ENCODE QUOTE MARKS ETC FOR SAFETY
                    }
                    $i++;
                }     //-- END WHILE $LINE..
                fclose($f);
                return $picArray;
            } else {
                return $picArray;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
