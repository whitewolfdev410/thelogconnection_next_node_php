<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6149302486')) {
    // if accessing this class directly through URL, send 404 and exit
    // this section of code will only work if you have a 404.html file in your root document folder.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

define("ASSETS_PROJECTS_PATH", ASSETS_BASE_URL . "projects/");
define("ASSETS_PROJECTS_PLANS_PATH", ASSETS_BASE_URL . "projects/_plans/");
define("ASSETS_PROJECTS_THUMBS_PATH", ASSETS_BASE_URL . "projects/_thumbs/");
define("PROJECTS_CSV_PATH", CSV_BASE_URL . "projects/");

class Project_Services extends Data_Reader
{

    public function __construct()
    {
    }

    public function GetProjectList($param)
    {
        $tempResult = array();
        $path = PROJECTS_CSV_PATH . "_projects.csv";
        $data = $this->ReadCSV($path);
        if (empty($param)) {
            $tempResult = $data;
        } else {
            $temp = array_filter($data, function ($obj) use ($param) {
                if (isset($obj['Status'])) {
                    if ($obj['Status'] == $param) {
                        return true;
                    }
                }
                return false;
            });
            $tempResult = array_values($temp);
        }

        $result = array();
        foreach ($tempResult as $value) {
            $value["Thumbnail"] = ASSETS_PROJECTS_THUMBS_PATH . $value["FileName"] . "." . $value["FileExtension"];
            array_push($result, $value);
        }

        return $result;
    }

    public function GetProjectDetails($projectCode)
    {
        try {
            $DETAILS_BASE_URL = ASSETS_PROJECTS_PATH . $projectCode . "/";
            $DETAILS_THUMBS_BASE_URL = ASSETS_PROJECTS_PATH . $projectCode . "/thumbs/";
            $csvFile = PROJECTS_CSV_PATH . $projectCode . ".csv";

            $data = $this->ReadCSV($csvFile);

            $project = array();
            $elevations = array(
                "label" => "",
                "images" => array()
            );
            $plans = array(
                "label" => "",
                "images" => array()
            );
            //PHOTOS
            $photos = array();
            $photosSectionCount = -1;
            $photosSectionObj = array(
                "sectionName" => "",
                "sectionItems" => array()
            );
            $photosSubSectionCount = -1;
            $photosSubSectionObj =  array(
                "subSectionName" => "",
                "subSectionItems" => array()
            );
            //VIDEOS
            $videos = array();
            $videosSectionCount = -1;
            $videosSectionObj = array(
                "sectionName" => "",
                "sectionItems" => array()
            );
            $videosSubSectionCount = -1;
            $videosSubSectionObj =  array(
                "subSectionName" => "",
                "subSectionItems" => array()
            );

            $thisType = "";
            foreach ($data as $value) {
                // $value['text'] = $value['text'];
                $thisText = $value['text'];
                $thisTitle = $value['title'];
                $thisFileName = $value['fileName'];
                $thisFileExtension = $value['extension'];
                if (substr($thisTitle, 0, 1) == "#") {
                    $thisType = substr($value['title'], 1);
                }

                switch ($thisType) {
                    case 'project':
                        $project = array(
                            "code" => $value['fileName'],
                            "label" => $thisText
                        );
                        break;
                    case 'elevations':
                        if ($thisTitle != "") {
                            $elevations["label"] = $thisText;
                        } else if ($thisTitle == "") {
                            $elImgObj = array(
                                "imageUrl" => ASSETS_PROJECTS_PLANS_PATH . $thisFileName . "." . $thisFileExtension,
                                "label" => $thisText
                            );
                            array_push($elevations["images"], $elImgObj);
                        }
                        break;
                    case 'plans':
                        if ($thisTitle != "") {
                            $plans["label"] = $thisText;
                        } else if ($thisTitle == "") {
                            $plansImgObj = array(
                                "imageUrl" => ASSETS_PROJECTS_PLANS_PATH . $thisFileName . "." . $thisFileExtension,
                                "label" =>  $thisText
                            );
                            array_push($plans["images"], $plansImgObj);
                        }
                        break;
                    case 'photos':
                        if ($thisTitle != "" && substr($thisTitle, 0, 1) == "#") {
                            $photosSectionObj["sectionName"] = $thisText;
                            $photosSectionObj["sectionItems"] = [];
                            $photosSubSectionCount = -1;
                            $photosSectionCount++;
                        } else if (substr($thisTitle, 0, 1) != "#") {
                            if ($thisTitle != "") {
                                $photosSubSectionObj["subSectionName"] = $thisTitle;
                                $photosSubSectionObj["subSectionItems"] = [];
                                $photosSubSectionCount++;
                            }
                            $photosImgObj = array(
                                "imageUrl" =>  $DETAILS_BASE_URL . $thisFileName . "." . $thisFileExtension,
                                "thumbnailUrl" => $DETAILS_THUMBS_BASE_URL . $thisFileName . "." . $thisFileExtension,
                                "label" =>  $thisText,
                                "type" => "image"
                            );

                            array_push($photosSubSectionObj["subSectionItems"], $photosImgObj);

                            if ($photosSubSectionObj["subSectionName"] && !empty($photosSubSectionObj["subSectionItems"]) && $photosSubSectionCount > -1) {
                                $photosSectionObj["sectionItems"][$photosSubSectionCount] = $photosSubSectionObj;
                            }
                        }
                        break;
                    case 'videos':
                        if ($thisTitle != "" && substr($thisTitle, 0, 1) == "#") {
                            $videosSectionObj["sectionName"] = $thisText;
                            $videosSectionObj["sectionItems"] = [];
                            $videosSubSectionCount = -1;
                            $videosSectionCount++;
                        } else if (substr($thisTitle, 0, 1) != "#") {
                            if ($thisTitle != "") {
                                $videosSubSectionObj["subSectionName"] = $thisTitle;
                                $videosSubSectionObj["subSectionItems"] = [];
                                $videosSubSectionCount++;
                            }
                            $videosImgObj = array(
                                "videoUrl" =>  $DETAILS_BASE_URL . $thisFileName . "." . $thisFileExtension,
                                "thumbnailUrl" => $DETAILS_THUMBS_BASE_URL . $thisFileName . "." . $thisFileExtension,
                                "label" =>  $thisText,
                                "type" => "video"
                            );

                            array_push($videosSubSectionObj["subSectionItems"], $videosImgObj);

                            if ($videosSubSectionObj["subSectionName"] && !empty($videosSubSectionObj["subSectionItems"]) && $videosSubSectionCount > -1) {
                                $videosSectionObj["sectionItems"][$videosSubSectionCount] = $videosSubSectionObj;
                            }
                        }
                        break;
                }

                if ($photosSectionObj["sectionName"] && !empty($photosSectionObj["sectionItems"]) && $photosSectionCount > -1) {
                    $photos[$photosSectionCount] = $photosSectionObj;
                }

                if ($videosSectionObj["sectionName"] && !empty($videosSectionObj["sectionItems"]) && $videosSectionCount > -1) {
                    $videos[$videosSectionCount] = $videosSectionObj;
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Next project's code
        |--------------------------------------------------------------------------
        |
        */

            // Get current project's status
            $projects = $this->GetProjectList('');
            $status = null;
            foreach ($projects as $p) {
                if ($p['ProjectCode'] == $project['code']) {
                    $status = $p['Status'];
                }
            }

            // Get next and previous project's code
            $nextProjectCode = null;
            $previousProjectCode = null;
            $firstProjectCode = null;
            $projects = $this->GetProjectList($status);
            foreach ($projects as $key => $p) {
                if ($key == 0) {
                    $firstProjectCode = $p['ProjectCode'];
                }
                if ($p['ProjectCode'] == $project['code']) {
                    // If next project exists
                    if (isset($projects[$key + 1])) {
                        $nextProjectCode = $projects[$key + 1]['ProjectCode'];
                    }
                    // If previous project exists
                    if (isset($projects[$key - 1])) {
                        $previousProjectCode = $projects[$key - 1]['ProjectCode'];
                    }

                    break;
                }
            }

            $project['previous_project_code'] = $previousProjectCode;
            $project['next_project_code'] = $nextProjectCode;
            $project['first_project_code'] = $firstProjectCode;
            $project['status'] = $status;

            $responseArray = array(
                "project" => $project,
                "elevations" => $elevations,
                "plans" => $plans,
                "photos" => $photos,
                "videos" => $videos,
            );

            return $responseArray;
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
