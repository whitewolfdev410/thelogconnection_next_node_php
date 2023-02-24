<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
    // if accessing this class directly through URL, send 404 and exit
    // this section of code will only work if you have a 404.html file in your root document folder.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

define("BS_ASSETS_CONSTRUCTION_IMG_PATH", ASSETS_BASE_URL . "building-styles/construction-details/");
define("BS_ASSETS_CONSTRUCTION_THUMBS_PATH", ASSETS_BASE_URL . "building-styles/construction-details/thumbs/");
define("BS_CSV_PATH", CSV_BASE_URL . "building-styles/");

class Building_Styles_Services extends Data_Reader
{

    public function __construct() { }

    public function GetConstructionDetails($param)
    {
        $result = array();
        $path = BS_CSV_PATH . "construction-details.csv";
        if (empty($param)) {
            $result = $this->ReadCSV($path);
        } else {
            $data = $this->ReadCSV($path);
            $filter = $param;
            foreach ($data as $value) {
                if ($value[$filter] == 'x') {
                    $value["imageUrl"] = BS_ASSETS_CONSTRUCTION_IMG_PATH . $value["fileName"] . "." . $value["fileExtension"]; 
                    $value["thumbnailUrl"] = BS_ASSETS_CONSTRUCTION_THUMBS_PATH . $value["fileName"] . "." . $value["fileExtension"]; 
                    array_push($result, $value);
                }
            };
        }
        return $result;
    }
}
