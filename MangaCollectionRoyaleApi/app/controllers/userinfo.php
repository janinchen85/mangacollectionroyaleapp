<?php
class UserInfo extends Controller
{
    public $pageAccess = false;
    public $userName, $isbn, $id;
    public $myhost = "http://needle-sorcery.com/";
    // Mehtod: index()
    public function index()
    {
        die();
    }

    public function user($userName = "", $apikey = "", $mode = "", $id = "")
    {
        $this->userName = $userName;
        $this->id = $id;
        $user = $this->model('User')->getUser($userName);
        if ($user['userIsLoggedIn'] == 1 && $apikey == $user['userAPIkey']) {
            $this->pageAccess = true;
            if ($mode == "home") {
                $this->home();
            } else if ($mode == "serieStats") {
                $this->serieStats();
            } else if ($mode == "scanBook") {
                $this->scanBook();
            } else if ($mode == "setReadStatus") {
                $this->setReadStatus();
            } else if ($mode == "addVolume") {
                $this->addVolume();
            } else if ($mode == "delVolume") {
                $this->delVolume();
            } else if ($mode == "showSeries") {
                $this->showSeries();
            }
        } else {
            $this->pageAccess = false;
            echo "no access!";
        }
    }

    public function home()
    {
        $items_arr = array();
        $items_arr['userinfo'] = array();
        $items_arr['userstats'] = array();
        $items_arr['userseries'] = array();
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            $user = $this->model('User')->getUser($this->userName);
            $user_item = array(
                'ID' => $user["userID"],
                'Name' => $user["userName"]
            );
            $userID = $user["userID"];
            array_push($items_arr['userinfo'], $user_item);
            $series = $this->model('Series')->getUserSeriesStats($userID);
            $volumes = $this->model('Volume')->getUserVolumeStats($userID);
            $read = $this->model('Volume')->getUserVolumeRead($userID);
            $unread = $this->model('Volume')->getUserVolumeUnread($userID);
            $userstats = array(
                'Serien' => $series,
                'Manga' => $volumes,
                'Gelesen' => $read,
                'Ungelesen' => $unread,
            );
            array_push($items_arr['userstats'], $userstats);
            $results = $this->model('Series')->getUserSeries($userID);
            foreach ($results as $result) {
                $cover = $this->myhost . "manga/public/img/manga/" . $result["seriesTitle"] . "/" . $result["seriesTitle"] . ".jpg";
                $string = $this->substrwords($result["seriesTitle"], 12);
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

    public function serieStats()
    {
        $items_arr = array();
        $items_arr['serieStats'] = array();
        $items_arr['seriesInfos'] = array();
        $items_arr['seriesVolumes'] = array();
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            $user = $this->model('User')->getUser($this->userName);
            $serie = $this->model('Series')->getSeries($this->id);
            $owned = $this->model('Volume')->owned($user['userID'], $this->id);
            $notOwned = $serie['seriesVolumes'] - $owned;
            $unreadVolumes = $this->model('Volume')->getUnread($user['userID'], $this->id);
            $readVolumes = $this->model('Volume')->getRead($user['userID'], $this->id);
            $seriestats_item = array(
                'Manga' => $owned,
                'Missing' => $notOwned,
                'Read' => $readVolumes,
                'Unread' => $unreadVolumes
            );
            array_push($items_arr['serieStats'], $seriestats_item);
            $series = $this->model('Series')->getSeries($this->id);
            if (!($series["seriesStart"]) || ($series["seriesStart"] == "0000-00-00")) {
                $start = "unbekannt";
            } else {
                $start = strtotime($series["seriesStart"]);
                $start = date("d.m.Y", $start);
            }
            if (!($series["seriesEnd"]) || ($series["seriesEnd"] == "0000-00-00")) {
                $end = "laufend";
            } else {
                $end = strtotime($series["seriesEnd"]);
                $end = date("d.m.Y", $end);
            }
            if ($series["isCanceled"] == 1) {
                $canceled = "yes";
            } else {
                $canceled = "no";
            }
            $nextVol = $this->model('Volume')->nextVolume($this->id);
            $nextVolume = $nextVol['volumeVolume'];
            $nextDate = $nextVol['volumeDate'];
            $volDate = strtotime($nextDate);
            $volDate = date("d.m.Y", $volDate);

            $volumes = $this->model('Volume')->countVolumes($this->id);
            $cover = $this->myhost . "manga/public/img/manga/" . $series['seriesTitle'] . "/" . $series['seriesTitle'] . ".jpg";
            $series_item = array(
                'ID' => $series["seriesID"],
                'Title' => $series["seriesTitle"],
                'Autor' => $series["authorName"],
                'Text' => $series["seriesDesc"],
                'Publisher' => $series["publisherName"],
                'VolumesAll' => $series["seriesVolumes"],
                'Volumes' => $volumes,
                'Start' => "$start",
                'End' => $end,
                'Canceled' => $canceled,
                'NextVol' => $nextVolume,
                'NextDate' => $volDate,
                'Cover' => $cover
            );
            array_push($items_arr['seriesInfos'], $series_item);
            $results = $this->model('Volume')->getVolumesOfSerie($this->id);
            foreach ($results as $result) {
                $volume = $this->model('Volume')->getUserVolume($user['userID'], $result["volumeID"]);
                if ($volume) {
                    $owned = "owned";
                } else {
                    $owned = "not-owned";
                }
                $cover = $this->myhost . "manga/public/img/manga/" . $series["seriesTitle"] . "/" . $result["volumeTitle"] . ".jpg";
                $volumes_item = array(
                    'ID' => $result["volumeID"],
                    'Series' => $series["seriesTitle"],
                    'Title' => $result["volumeTitle"],
                    'Volume' => $result["volumeVolume"],
                    'Besitz' => $owned,
                    'Cover' => $cover
                );
                array_push($items_arr['seriesVolumes'], $volumes_item);
            }
            echo json_encode($items_arr);
        } else {
            echo "no access";
            die();
        }
    }

    public function scanBook()
    {
        $items_arr = array();
        $items_arr['bookInfo'] = array();
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            $user = $this->model('User')->getUser($this->userName);
            $book = $this->model('Volume')->getVolumeByISBN($this->id);
            $userCheck = $this->model('Volume')->checkVolume($user['userID'], $book["volumeID"]);
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
            $book_item = array(
                "Title" => $book['volumeTitle'],
                'Besitz' => $owned,
                'Status' => $read
            );
            array_push($items_arr['bookInfo'], $book_item);
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
            $postdata = file_get_contents("php://input");
            if (isset($postdata)) {
                $request = json_decode($postdata);
                $username = $request->username;
                $isbn = $request->ISBN;
                $status = 1;
                $user = $this->model('User')->getUser($username);
                $book = $this->model('Volume')->getVolumeByISBN($isbn);
                $this->model('Volume')->setVolumeStatus($user['userID'], $book["volumeID"], $status);
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
            $postdata = file_get_contents("php://input");
            if (isset($postdata)) {
                $request = json_decode($postdata);
                $username = $request->username;
                $isbn = $request->ISBN;
                $user = $this->model('User')->getUser($username);
                $book = $this->model('Volume')->getVolumeByISBN($isbn);
                $checkVolume = $this->model('Volume')->checkVolume($user['userID'], $book["volumeID"]);
                if (!$checkVolume) {
                    $this->model('Volume')->addVolume($user['userID'], $book["volumeID"]);
                    $volumeCount = $this->model('Series')->checkVolumeCount($user['userID'], $book["seriesID"]);
                    $this->model('Series')->updateVolumeCount($user['userID'], $book["seriesID"], $volumeCount);
                }
                $checkSeries = $this->model('Series')->checkSeries($user['userID'], $book["seriesID"]);
                if (!$checkSeries) {
                    $this->model('Series')->addSeries($user['userID'], $book["seriesID"]);
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
            $postdata = file_get_contents("php://input");
            if (isset($postdata)) {
                $request = json_decode($postdata);
                $username = $request->username;
                $isbn = $request->ISBN;
                $user = $this->model('User')->getUser($username);
                $book = $this->model('Volume')->getVolumeByISBN($isbn);
                $this->model('Volume')->deleteVolume($user['userID'], $book["volumeID"]);
                $volumeCount = $this->model('Series')->checkVolumeCount($user['userID'], $book["seriesID"]);
                $this->model('Series')->updateVolumeCount($user['userID'], $book["seriesID"], $volumeCount);
                if ($volumeCount == 0) {
                    $this->model('Series')->delSeries($user['userID'], $book["seriesID"]);
                }
            }
        } else {
            echo "no access";
            die();
        }
    }

    function showSeries()
    {
        $items_arr = array();
        $items_arr['series'] = array();
        $items_arr['volumes'] = array();
        if ($this->pageAccess == false) {
            echo "no access";
            die();
        } elseif ($this->pageAccess == true) {
            $user = $this->model('User')->getUser($this->userName);
            $book = $this->model('Volume')->getVolumeByISBN($this->id);
            $series = $this->model('Series')->getSeries($book["seriesID"]);
            if (!($series["seriesEnd"]) || ($series["seriesEnd"] == "0000-00-00")) {
                $end = "laufend";
            } else {
                $end = strtotime($series["seriesEnd"]);
                $end = date("d.m.Y", $end);
            }
            $start = strtotime($series["seriesStart"]);
            $start = date("d.m.Y", $start);
            $cover = $this->myhost . "manga/public/img/manga/" . $series["seriesTitle"] . "/" . $series["seriesTitle"] . ".jpg";
            $series_item = array(
                'ID' => $series["seriesID"],
                'Title' => $series["seriesTitle"],
                'Author' => $series["authorName"],
                'Publisher' => $series["publisherName"],
                'Start' => $start,
                'End' => $end,
                'Volumes' => $series["seriesVolumes"],
                'Summary' => $series["seriesDesc"],
                'Cover' => $cover
            );
            array_push($items_arr['series'], $series_item);
            $results = $this->model('Volume')->getVolumesOfSerie($series["seriesID"]);
            foreach ($results as $result) {
                $userCheck = $this->model('Volume')->checkVolume($user['userID'], $result["volumeID"]);
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
                $cover = $this->myhost . "manga/public/img/manga/" . $series["seriesTitle"] . "/" . $result["volumeTitle"] . ".jpg";
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
}