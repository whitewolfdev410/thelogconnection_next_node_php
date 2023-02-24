<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
    // if accessing this class directly through URL, send 404 and exit
    // this section of code will only work if you have a 404.html file in your root document folder.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

define("GALLERY_CSV_PATH", CSV_BASE_URL . "gallery/");
define("GALLERY_ASSETS_IMG_PATH", ASSETS_BASE_URL . "gallery/");

class Gallery_Services extends Data_Reader
{
    public function __construct()
    {
    }

    public function GetGalleryList($filter)
    {

        $result = array();
        $base_url_img = '';
        $base_url_thumbnail = '';

        try {
            switch ($filter) {
                case 'construction':
                    $base_url_img = GALLERY_ASSETS_IMG_PATH . "construction/";
                    $base_url_thumbnail = GALLERY_ASSETS_IMG_PATH . "construction/thumbs/";
                    break;
                case 'interior':
                    $base_url_img = GALLERY_ASSETS_IMG_PATH . "interior/";
                    $base_url_thumbnail = GALLERY_ASSETS_IMG_PATH . "interior/thumbs/";
                    break;
                case 'exterior':
                    $base_url_img = GALLERY_ASSETS_IMG_PATH . "exterior/";
                    $base_url_thumbnail = GALLERY_ASSETS_IMG_PATH . "exterior/thumbs/";
                    break;
                case 'finish':
                    $base_url_img = GALLERY_ASSETS_IMG_PATH . "finish/";
                    $base_url_thumbnail = GALLERY_ASSETS_IMG_PATH . "finish/thumbs/";
                    break;
            }
            $path =  GALLERY_CSV_PATH . "gallery-" . $filter . ".csv";
            $data = $this->ReadCSV($path);

            foreach ($data as $value) {
                $value["imageUrl"] = $base_url_img . $value["FileName"] . "." . $value["FileExtension"];
                $value["thumbnailUrl"] = $base_url_thumbnail . $value["FileName"] . "." . $value["FileExtension"];
                array_push($result, $value);
            };
        } catch (Exception $e) {
            return $result;
        } finally {
            return $result;
        }
    }
}
