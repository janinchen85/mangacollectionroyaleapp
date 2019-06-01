<?php
class Manga extends Controller{
    // Mehtod: index()
    public function index(){
        echo "manga";
    }

    public function isbn($isbn = ''){
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $items_arr = array();
        $items_arr['volume'] = array();
        $items_arr['series'] = array();
        $items_arr['moreVolumes'] = array();
        // Get Book
        $results = $this->model('Volume')->getVolumeByISBN($isbn);
        foreach($results as $result){
            $date = strtotime($result["volumeDate"]);
            $date = date("d.m.Y", $date);
            $volume_item = array(
                'ID'        => $result["volumeID"],
                'ISBN'      => $result["volumeISBN"],
                'Volume'    => $result["volumeVolume"],
                'Title'     => $result["volumeTitle"],
                'Author'    => $result["authorName"],
                'Publisher' => $result["publisherName"],
                'Chapters'  => $result["volumeChapters"],
                'Pages'     => $result["volumePages"],
                'Date'      => $date
            );
            array_push($items_arr['volume'],$volume_item);
        }
        $volumeID = $result["volumeID"];
        // Get Book Series
        $results = $this->model('Series')->getSeries($result["seriesID"]);
        foreach($results as $result){
            if(!($result["seriesEnd"]) || ($result["seriesEnd"] == "0000-00-00")){
                $end = "laufend";
            } else {
                $end = strtotime($result["seriesEnd"]);
                $end = date("d.m.Y", $end);
            }
            $start = strtotime($result["seriesStart"]);
            $start = date("d.m.Y", $start);
            $series_item = array(
                'ID'        => $result["seriesID"],
                'Title'     => $result["seriesTitle"],
                'Author'    => $result["authorName"],
                'Publisher' => $result["publisherName"],
                'Group'     => $result["targetGroupName"],
                'GroupDesc' => $result["targetGroupDesc"],
                'MainGenre' => $result["genreName"],
                'Start'     => $start,
                'End'       => $end,
                'Volumes'   => $result["seriesVolumes"],
                'Chapters'  => $result["seriesChapters"],
                'Summary'   => $result["seriesDesc"]
            );
            array_push($items_arr['series'],$series_item);
        }
        // Get more Volumes
        $results = $this->model('Volume')->getVolumes($result["seriesID"],$volumeID);
        foreach($results as $result){
            $date = strtotime($result["volumeDate"]);
            $date = date("d.m.Y", $date);
            $volumes_item = array(
                'ID'        => $result["volumeID"],
                'ISBN'      => $result["volumeISBN"],
                'Volume'    => $result["volumeVolume"],
                'Title'     => $result["volumeTitle"],
                'Chapters'  => $result["volumeChapters"],
                'Pages'     => $result["volumePages"],
                'Date'      => $date
            );
            array_push($items_arr['moreVolumes'],$volumes_item);
        }
        //array_push($datas['results'],$items_arr);
        echo json_encode($items_arr);
    }
}
?>