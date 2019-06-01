<?php
class Home extends Controller
{
    // Mehtod: index()
    public function index()
    {
        echo "home";
    }

    public function addserie()
    {
        $error = "";
        $success = "";
        $index = $this->heading("addserie");

        $publishers = $this->model('Publisher')->getPublishers();
        foreach ($publishers as $publisher) {
            $publisherList = new tpl("addserie/publisherList");
            foreach ($publisher as $key => $value) {
                $publisherList->assign($key, $value);
            }
            $publisherRow[] = $publisherList;
        }
        $publisherContents = tpl::merge($publisherRow);
        $index->assign("publisher", $publisherContents);

        $authors = $this->model('Author')->getAuthors();
        foreach ($authors as $author) {
            $authorList = new tpl("addserie/authorList");
            foreach ($author as $key => $value) {
                $authorList->assign($key, $value);
            }
            $authorRow[] = $authorList;
        }
        $authorContents = tpl::merge($authorRow);
        $index->assign("author", $authorContents);

        $series = $this->model('Series')->series();
        foreach ($series as $serie) {
            $serieList = new tpl("addserie/serieList");
            foreach ($serie as $key => $value) {
                $serieList->assign($key, $value);
            }
            $serieRow[] = $serieList;
        }
        $serieContents = tpl::merge($serieRow);
        $index->assign("serie", $serieContents);

        $placeholder = $this->getRoot() . 'img/manga/Placeholder.png';

        if (isset($_POST["addSerie"])) {
            $serie = filter_var($_POST['serieName'], FILTER_SANITIZE_STRING);
            $author = filter_var($_POST['selectAuthor'], FILTER_SANITIZE_STRING);
            $publisher = filter_var($_POST['selectPublisher'], FILTER_SANITIZE_STRING);
            $start = filter_var($_POST['serieStart'], FILTER_SANITIZE_STRING);
            $end = filter_var($_POST['serieEnd'], FILTER_SANITIZE_STRING);
            $volumes = filter_var($_POST['serieVolumes'], FILTER_SANITIZE_STRING);
            $descr = filter_var($_POST['seriesDesc'], FILTER_SANITIZE_STRING);
            $checkSeries = $this->model('Series')->getSerie($serie);
            if ($checkSeries) {
                $error = $serie . " bereits vorhanden";
            } else {
                $this->model('Series')->addNewSeries($serie, $start, $end, $volumes, $descr, $author, $publisher);
                $folder = $this->getRoot() . 'img/manga/' . $serie;
                mkdir($folder);
                fopen($this->getRoot() . 'img/manga/' . $serie . '/' . $serie . '.jpg', "w");
                copy($placeholder, $this->getRoot() . 'img/manga/' . $serie . '/' . $serie . '.jpg');
                for ($i = 1; $i <= $volumes; $i++) {
                    fopen($this->getRoot() . 'img/manga/' . $serie . '/' . $serie . ' - ' . sprintf("%02d", $i) . '.jpg', "w");
                    copy($placeholder, $this->getRoot() . 'img/manga/' . $serie . '/' . $serie . ' - ' . sprintf("%02d", $i) . '.jpg');
                }
                $success = $serie . " wurde erfolgreich hinzugefügt";
                header("Refresh:3");
            }
        }

        if (isset($_POST["addVolume"])) {
            $seriesID = filter_var($_POST['selectSerie'], FILTER_SANITIZE_STRING);
            $volumeISBN = filter_var($_POST['volumeISBN'], FILTER_SANITIZE_STRING);
            $volumePages = filter_var($_POST['volumePages'], FILTER_SANITIZE_STRING);
            $volumeDate = filter_var($_POST['volumeDate'], FILTER_SANITIZE_STRING);
            $volumeVolume = sprintf("%02d", filter_var($_POST['volumeVolume'], FILTER_SANITIZE_STRING));
            $volumeISBN = str_replace("-", "", $volumeISBN);
            $seriesTitle = $this->model('Series')->getSerieByID($seriesID);
            $volumeTitle = $seriesTitle['seriesTitle'] . ' - ' . $volumeVolume;
            $checkVolume = $this->model('Volume')->getVolume($volumeTitle);
            if ($checkVolume) {
                $error = $volumeTitle . " bereits vorhanden";
            } else {
                $this->model('Volume')->addNewVolume($volumeISBN, $volumeTitle, $volumePages, $volumeDate, $volumeVolume, $seriesID);
                $success = $volumeTitle . " wurde erfolgreich hinzugefügt";
                header("Refresh:3");
            }
        }

        if (isset($_POST["addPublisher"])) {
            $publisherName = filter_var($_POST['publisherName'], FILTER_SANITIZE_STRING);
            $checkPublisher = $this->model('Publisher')->searchPublisher($publisherName);
            if ($checkPublisher) {
                $error = $publisherName . " bereits vorhanden";
            } else {
                $this->model('Publisher')->addPublisher($publisherName);
                $success = $publisherName . " wurde erfolgreich hinzugefügt";
                header("Refresh:3");
            }
        }

        if (isset($_POST["addAuthor"])) {
            $authorName = filter_var($_POST['authorName'], FILTER_SANITIZE_STRING);
            $checkAutor = $this->model('Author')->searchAuthor($authorName);
            if ($checkAutor) {
                $error = $authorName . " bereits vorhanden";
            } else {
                $this->model('Author')->addAuthor($authorName);
                $success = $authorName . " wurde erfolgreich hinzugefügt";
                header("Refresh:3");
            }
        }

        $index->assign("error", $error);
        $index->assign("success", $success);
        $index->assign("root", $this->getRoot());
        $this->setView();
    }
}
?>