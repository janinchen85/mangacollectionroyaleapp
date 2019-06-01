<?php
class UserInfo extends Controller
{
    public $pageAccess = false;
    public $userName, $userPW, $isLoggedIn;
    public $seriesID, $isbn;
    // Mehtod: index()
    public function index()
    {
        die();
    }

    public function user($apikey = "", $userName = "", $mode = "", $id = "")
    {
        $apiKeyCheck = 'cbaf0b05-d399-4a35-b4d3-d1dfb98a6c22';
        if ($apikey == $apiKeyCheck) {
            $this->pageAccess = true;
            $this->userName = $userName;
            $this->seriesID = $id;
            $this->isbn = $id;
            if ($mode == "series") {
                $this->seriesInfo($this->userName, $this->seriesID);
            } elseif ($mode == "isbn") {
                $this->isbn($this->userName, $this->isbn);
            } elseif ($mode == "showSeries") {
                $this->showSeries($this->userName, $this->isbn);
            } elseif ($mode == "setReadStatus") {
                $this->setReadStatus();
            } elseif ($mode == "handleVolume") {
                $this->handleVolume();
            } elseif ($mode == "addVolume") {
                $this->addVolume();
            } elseif ($mode == "delVolume") {
                $this->delVolume();
            } else {
                $this->access($this->userName);
            }
        } else {
            $this->pageAccess = false;
            $this->access("");
        }

    }

    function showSeries($userName, $isbn)
    {
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");    // cache for 1 day
            }
            $results = $this->model('User')->getUser($userName);
            foreach ($results as $result) {
                $user_item = array(
                    'ID' => $result["userID"],
                    'Name' => $result["userName"]
                );
            }
            $userID = $result["userID"];
            $items_arr = array();
            $items_arr['series'] = array();
            $items_arr['volumes'] = array();
            $results = $this->model('Volume')->getVolumeByISBN($isbn);
            foreach ($results as $result) {
                $seriesID = $result["seriesID"];
            }
            $results = $this->model('Series')->getSeries($result["seriesID"]);
            foreach ($results as $result) {
                if (!($result["seriesEnd"]) || ($result["seriesEnd"] == "0000-00-00")) {
                    $end = "laufend";
                } else {
                    $end = strtotime($result["seriesEnd"]);
                    $end = date("d.m.Y", $end);
                }
                $start = strtotime($result["seriesStart"]);
                $start = date("d.m.Y", $start);
                $cover = "http://needle-sorcery.com/manga/public/img/manga/" . $result["seriesTitle"] . "/" . $result["seriesTitle"] . ".jpg";
                $series_item = array(
                    'ID' => $result["seriesID"],
                    'Title' => $result["seriesTitle"],
                    'Author' => $result["authorName"],
                    'Publisher' => $result["publisherName"],
                    'Group' => $result["targetGroupName"],
                    'GroupDesc' => $result["targetGroupDesc"],
                    'MainGenre' => $result["genreName"],
                    'Start' => $start,
                    'End' => $end,
                    'Volumes' => $result["seriesVolumes"],
                    'Summary' => $result["seriesDesc"],
                    'Cover' => $cover
                );
                array_push($items_arr['series'], $series_item);
            }
            $seriesTitle = $result["seriesTitle"];
            $results = $this->model('Volume')->getVolumesOfSerie($seriesID);
            foreach ($results as $result) {
                $userCheck = $this->model('Volume')->checkVolume($userID, $result["volumeID"]);
                if ($userCheck == "") {
                    $owned = "Nein";
                } else {
                    $owned = "Ja";
                }
                if ($userCheck["isRead"] == 1) {
                    $read = "gelesen";
                } else {
                    $read = "ungelesen";
                }
                $date = strtotime($result["volumeDate"]);
                $date = date("d.m.Y", $date);
                $cover = "http://needle-sorcery.com/manga/public/img/manga/" . $seriesTitle . "/" . $result["volumeTitle"] . ".jpg";
                $volumes_item = array(
                    'ID' => $result["volumeID"],
                    'ISBN' => $result["volumeISBN"],
                    'Volume' => $result["volumeVolume"],
                    'Title' => $result["volumeTitle"],
                    'Pages' => $result["volumePages"],
                    'Date' => $date,
                    'Besitz' => $owned,
                    'Status' => $read,
                    'Cover' => $cover
                );
                array_push($items_arr['volumes'], $volumes_item);
            }
        //array_push($datas['results'],$items_arr);
            echo json_encode($items_arr);
        }
    }

    public function isbn($userName, $isbn)
    {
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");    // cache for 1 day
            }
            $results = $this->model('User')->getUser($userName);
            foreach ($results as $result) {
                $user_item = array(
                    'ID' => $result["userID"],
                    'Name' => $result["userName"]
                );
            }
            $userID = $result["userID"];
            $items_arr = array();
            $items_arr['volume'] = array();
            $items_arr['series'] = array();
            $items_arr['moreVolumes'] = array();
        // Get Book
            $results = $this->model('Volume')->getVolumeByISBN($isbn);
            foreach ($results as $result) {
                $userCheck = $this->model('Volume')->checkVolume($userID, $result["volumeID"]);
                if ($userCheck == "") {
                    $owned = "Nein";
                } else {
                    $owned = "Ja";
                }
                if ($userCheck["isRead"] == 1) {
                    $read = "gelesen";
                } else {
                    $read = "ungelesen";
                }
                $cover = "http://needle-sorcery.com/manga/public/img/manga/" . $result["seriesTitle"] . "/" . $result["volumeTitle"] . ".jpg";
                $date = strtotime($result["volumeDate"]);
                $date = date("d.m.Y", $date);
                $volume_item = array(
                    'ID' => $result["volumeID"],
                    'ISBN' => $result["volumeISBN"],
                    'Volume' => $result["volumeVolume"],
                    'Title' => $result["volumeTitle"],
                    'Author' => $result["authorName"],
                    'Publisher' => $result["publisherName"],
                    'Pages' => $result["volumePages"],
                    'Date' => $date,
                    'Besitz' => $owned,
                    'Status' => $read,
                    'Cover' => $cover
                );
                array_push($items_arr['volume'], $volume_item);
            }
            $volumeID = $result["volumeID"];
            $seriesTitle = $result["seriesTitle"];
        // Get Book Series
            $results = $this->model('Series')->getSeries($result["seriesID"]);
            foreach ($results as $result) {
                if (!($result["seriesEnd"]) || ($result["seriesEnd"] == "0000-00-00")) {
                    $end = "laufend";
                } else {
                    $end = strtotime($result["seriesEnd"]);
                    $end = date("d.m.Y", $end);
                }
                $start = strtotime($result["seriesStart"]);
                $start = date("d.m.Y", $start);
                $cover = "http://needle-sorcery.com/manga/public/img/manga/" . $result["seriesTitle"] . "/" . $result["seriesTitle"] . ".jpg";
                $series_item = array(
                    'ID' => $result["seriesID"],
                    'Title' => $result["seriesTitle"],
                    'Author' => $result["authorName"],
                    'Publisher' => $result["publisherName"],
                    'Group' => $result["targetGroupName"],
                    'GroupDesc' => $result["targetGroupDesc"],
                    'MainGenre' => $result["genreName"],
                    'Start' => $start,
                    'End' => $end,
                    'Volumes' => $result["seriesVolumes"],
                    'Summary' => $result["seriesDesc"],
                    'Cover' => $cover
                );
                array_push($items_arr['series'], $series_item);
            }
        // Get more Volumes
            $results = $this->model('Volume')->getVolumes($result["seriesID"], $volumeID);
            foreach ($results as $result) {
                $userCheck = $this->model('Volume')->checkVolume($userID, $result["volumeID"]);
                if ($userCheck == "") {
                    $owned = "Nein";
                } else {
                    $owned = "Ja";
                }
                if ($userCheck["isRead"] == 1) {
                    $read = "gelesen";
                } else {
                    $read = "ungelesen";
                }
                $date = strtotime($result["volumeDate"]);
                $date = date("d.m.Y", $date);
                $cover = "http://needle-sorcery.com/manga/public/img/manga/" . $seriesTitle . "/" . $result["volumeTitle"] . ".jpg";
                $volumes_item = array(
                    'ID' => $result["volumeID"],
                    'ISBN' => $result["volumeISBN"],
                    'Volume' => $result["volumeVolume"],
                    'Title' => $result["volumeTitle"],
                    'Pages' => $result["volumePages"],
                    'Date' => $date,
                    'Besitz' => $owned,
                    'Status' => $read,
                    'Cover' => $cover
                );
                array_push($items_arr['moreVolumes'], $volumes_item);
            }
        //array_push($datas['results'],$items_arr);
            echo json_encode($items_arr);
        }
    }


    public function seriesInfo($userName, $seriesID)
    {
        $items_arr = array();
        $items_arr['seriestats'] = array();
        $items_arr['seriesinfo'] = array();
        $items_arr['volumes'] = array();
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");    // cache for 1 day
            }
            $results = $this->model('User')->getUser($userName);
            foreach ($results as $result) {
                $user_item = array(
                    'ID' => $result["userID"],
                    'Name' => $result["userName"]
                );
            }
            $userID = $result["userID"];
            $results = $this->model('Series')->getSeries($seriesID);
            $volumes = $this->model('Volume')->countVolumes($seriesID);
            foreach ($results as $result) {
                $start = strtotime($result["seriesStart"]);
                $start = date("d.m.Y", $start);
                $end = strtotime($result["seriesEnd"]);
                $end = date("d.m.Y", $end);
                $nextVols = $this->model('Volume')->nextVolume($seriesID);
                foreach ($nextVols as $nextVol) {
                    $nextVolume = $nextVol['volumeVolume'];
                    $nextDate = $nextVol['volumeDate'];
                }
                $volDate = strtotime($nextDate);
                $volDate = date("d.m.Y", $volDate);
                $cover = "http://needle-sorcery.com/manga/public/img/manga/" . $result['seriesTitle'] . "/" . $result['seriesTitle'] . ".jpg";
                $series_item = array(
                    'ID' => $result["seriesID"],
                    'Title' => $result["seriesTitle"],
                    'Autor' => $result["authorName"],
                    'Text' => $result["seriesDesc"],
                    'Publisher' => $result["publisherName"],
                    'VolumesAll' => $result["seriesVolumes"],
                    'Volumes' => $volumes,
                    'Start' => $start,
                    'End' => $end,
                    'NextVol' => $nextVolume,
                    'NextDate' => $volDate,
                    'Genre' => $result["genreName"],
                    'Cover' => $cover
                );
            }
            $serieVolumes = $result["seriesVolumes"];
            $seriesTitle = $result["seriesTitle"];
            array_push($items_arr['seriesinfo'], $series_item);
            $results = $this->model('Volume')->getVolumesOfSerie($seriesID);
            foreach ($results as $result) {
                $volume = $this->model('Volume')->getUserVolume($userID, $result["volumeID"]);
                if ($volume) {
                    $owned = "owned";
                } else {
                    $owned = "not-owned";
                }
                $cover = "http://needle-sorcery.com/manga/public/img/manga/" . $seriesTitle . "/" . $result["volumeTitle"] . ".jpg";
                $volumes_item = array(
                    'ID' => $result["volumeID"],
                    'Series' => $seriesTitle,
                    'Title' => $result["volumeTitle"],
                    'Volume' => $result["volumeVolume"],
                    'Besitz' => $owned,
                    'Cover' => $cover
                );
                array_push($items_arr['volumes'], $volumes_item);
            }
            $results = $this->model('Series')->checkSeries($userID, $seriesID);
            foreach ($results as $result) {
                $mangacount = $result['volumeCount'];
            }
            $missing = $serieVolumes - $mangacount;
            $read = $this->model('Volume')->getRead($userID);
            $unread = $mangacount - $read;
            $stats_items = array(
                'Manga' => $mangacount,
                'Missing' => $missing,
                'Read' => $read,
                'Unread' => $unread
            );
            array_push($items_arr['seriestats'], $stats_items);

            echo json_encode($items_arr);
        } else {
            echo "no access";
            die();
        }
    }

    public function access($userName = "")
    {
        $items_arr = array();
        $items_arr['userinfo'] = array();
        $items_arr['userstats'] = array();
        $items_arr['userseries'] = array();
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");    // cache for 1 day
            }
            $results = $this->model('User')->getUser($userName);
            foreach ($results as $result) {
                $user_item = array(
                    'ID' => $result["userID"],
                    'Name' => $result["userName"]
                );
            }
            $userID = $result["userID"];
            array_push($items_arr['userinfo'], $user_item);

            $series = $this->model('Series')->getUserSeriesStats($userID);
            $complete = $this->model('Series')->getUserSeriesComplete($userID);
            $incomplete = $series - $complete;
            $volumes = $this->model('Volume')->getUserVolumeStats($userID);
            $read = $this->model('Volume')->getUserVolumeRead($userID);
            $unread = $this->model('Volume')->getUserVolumeUnread($userID);
        // series complete
        // series incomplete
            $userstats = array(
                'Serien' => $series,
                'Vollständig' => $complete,
                'Unvollständig' => $incomplete,
                'Manga' => $volumes,
                'Gelesen' => $read,
                'Ungelesen' => $unread,
            );

            array_push($items_arr['userstats'], $userstats);

            $results = $this->model('Series')->getUserSeries($userID);
            foreach ($results as $result) {
                $cover = "http://needle-sorcery.com/manga/public/img/manga/" . $result["seriesTitle"] . "/" . $result["seriesTitle"] . ".jpg";
                $string = $this->substrwords($result["seriesTitle"], 15);
                $seriesID = $result["seriesID"];
                $checks = $this->model('Series')->checkSeries($userID, $seriesID);
                foreach ($checks as $check) {
                    $volumeCount = $check["volumeCount"];
                }
                $userseries_item = array(
                    "SeriesID" => $seriesID,
                    "Series" => $result["seriesTitle"],
                    "SeriesSh" => $string,
                    "Autor" => $result["authorName"],
                    "Cover" => $cover,
                    "VolumesAll" => $result["seriesVolumes"],
                    "VolumeCount" => $volumeCount
                );
                array_push($items_arr['userseries'], $userseries_item);
            }


            echo json_encode($items_arr);
        } else {
            echo "no access";
            die();
        }

    }

    public function setReadStatus()
    {
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");    // cache for 1 day
            }
            $postdata = file_get_contents("php://input");
            if (isset($postdata)) {
                $request = json_decode($postdata);
                $username = $request->username;
                $isbn = $request->ISBN;
                $status = 1;
                $results = $this->model('User')->getUser($username);
                foreach ($results as $result) {
                    $userID = $result["userID"];
                }
                $results = $this->model('Volume')->getVolumeByISBN($isbn);
                foreach ($results as $result) {
                    $volumeID = $result["volumeID"];
                }
                $this->model('Volume')->setVolumeStatus($userID, $volumeID, $status);
            }
        } else {
            echo "no access";
            die();
        }
    }

    public function handleVolume()
    {
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");    // cache for 1 day
            }
            $postdata = file_get_contents("php://input");
            if (isset($postdata)) {
                $request = json_decode($postdata);
                $username = $request->username;
                $volumeID = $request->volumeID;
                $type = $request->status;
                $results = $this->model('User')->getUser($username);
                foreach ($results as $result) {
                    $userID = $result["userID"];
                }
                if ($type == "delete") {
                    $this->model('Volume')->deleteVolume($userID, $volumeID);
                } elseif ($type == "add") {
                    $check = $this->model('Volume')->checkVolume($userID, $volumeID);
                    if ($check == "") {
                        $this->model('Volume')->addVolume($userID, $volumeID);
                    } else {
                        die();
                    }
                } else {
                    die();
                }
            }
        } else {
            echo "no access";
            die();
        }
    }

    public function addVolume()
    {
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");    // cache for 1 day
            }
            $postdata = file_get_contents("php://input");
            if (isset($postdata)) {
                $request = json_decode($postdata);
                $username = $request->username;
                $isbn = $request->ISBN;
                $results = $this->model('User')->getUser($username);
                foreach ($results as $result) {
                    $userID = $result["userID"];
                }
                $results = $this->model('Volume')->getVolumeByISBN($isbn);
                foreach ($results as $result) {
                    $volumeID = $result["volumeID"];
                    $seriesID = $result["seriesID"];
                }
                $checkVolume = $this->model('Volume')->checkVolume($userID, $volumeID);
                if (!$checkVolume) {
                    $this->model('Volume')->addVolume($userID, $volumeID);
                    $volumeCount = $this->model('Series')->checkVolumeCount($userID, $seriesID);
                    $this->model('Series')->updateVolumeCount($userID, $seriesID, $volumeCount);
                }
                $checkSeries = $this->model('Series')->checkSeries($userID, $seriesID);
                if (!$checkSeries) {
                    $this->model('Series')->addSeries($userID, $seriesID);
                }
            }
        } else {
            echo "no access";
            die();
        }
    }

    public function delVolume()
    {
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");    // cache for 1 day
            }
            $postdata = file_get_contents("php://input");
            if (isset($postdata)) {
                $request = json_decode($postdata);
                $username = $request->username;
                $isbn = $request->ISBN;
                $results = $this->model('User')->getUser($username);
                foreach ($results as $result) {
                    $userID = $result["userID"];
                }
                $results = $this->model('Volume')->getVolumeByISBN($isbn);
                foreach ($results as $result) {
                    $volumeID = $result["volumeID"];
                    $seriesID = $result["seriesID"];
                }
                $this->model('Volume')->deleteVolume($userID, $volumeID);
                $volumeCount = $this->model('Series')->checkVolumeCount($userID, $seriesID);
                $this->model('Series')->updateVolumeCount($userID, $seriesID, $volumeCount);
                if ($volumeCount == 0) {
                    $this->model('Series')->delSeries($userID, $seriesID);
                }

            }
        } else {
            echo "no access";
            die();
        }
    }

}