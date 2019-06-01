<?php

class Series extends DB
{
    private $db, $sql;

    public function __construct()
    {
        $this->db = new DB();
        $this->sql = "";
    }

    function addNewSeries($serie, $start, $end, $volumes, $isCanceled, $descr, $authorID, $publisherID)
    {
        $this->db->connect();
        $this->sql = 'INSERT INTO series (seriesTitle, seriesStart, seriesEnd, seriesVolumes, isCanceled, seriesDesc, authorID, publisherID)
                      VALUES (:serie, :startDate, :endDate, :volumes, :isCanceled, :descr, :authorID, :publisherID)';
        $this->db->query($this->sql);
        $this->db->bind(':serie', $serie);
        $this->db->bind(':startDate', $start);
        $this->db->bind(':endDate', $end);
        $this->db->bind(':volumes', $volumes);
        $this->db->bind(':isCanceled', $isCanceled);
        $this->db->bind(':descr', $descr);
        $this->db->bind(':authorID', $authorID);
        $this->db->bind(':publisherID', $publisherID);
        $this->db->execute();
        $this->db->close();
    }

    function getSeries($seriesID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM series s
                        JOIN author a
                        JOIN publisher p
                        WHERE a.authorID = s.authorID
                        AND p.publisherID = s.publisherID
                        AND seriesID = :seriesID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->close();
        return $this->db->result();
    }

    function getSerie($series)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM series 
                        WHERE seriesTitle = :seriesTitle
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':seriesTitle', $series);
        $this->db->close();
        return $this->db->result();
    }

    function getSerieByID($seriesID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM series 
                        WHERE seriesID = :seriesID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->close();
        return $this->db->result();
    }

    function series()
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM series 
                        ORDER BY seriesTitle ASC
                    ';
        $this->db->query($this->sql);
        $this->db->close();
        return $this->db->results();
    }


    function checkSeries($userID, $seriesID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_serie 
                        WHERE seriesID = :seriesID
                        AND userID = :userID 
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->close();
        return $this->db->results();
    }

    function getUserSeries($userID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_serie u 
                        JOIN series s 
                        JOIN author a
                        WHERE s.seriesID = u.seriesID
                        AND s.authorID = a.authorID
                        AND userID = :userID 
                        ORDER BY seriesTitle ASC
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->close();
        return $this->db->results();
    }

    function getUserSeriesStats($userID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_serie u 
                        JOIN series s 
                        WHERE s.seriesID = u.seriesID
                        AND userID = :userID 
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    function getUserSeriesComplete($userID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_serie u 
                        JOIN series s 
                        WHERE u.volumeCount = s.seriesVolumes
                        AND userID = :userID 
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    public function addSeries($userID, $seriesID)
    {
        $this->db->connect();
        $this->sql = 'INSERT INTO user_has_serie (userID, seriesID, volumeCount)
                      VALUES (:userID, :seriesID, 1)';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->execute();
        $this->db->close();
    }

    public function delSeries($userID, $seriesID)
    {
        $this->db->connect();
        $this->sql = 'DELETE FROM user_has_serie
                      WHERE userID = :userID 
                      AND seriesID = :seriesID';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->execute();
    }

    public function checkVolumeCount($userID, $seriesID)
    {
        $this->db->connect();
        $this->sql =
            '          SELECT * FROM user_has_volume u
                        JOIN volume v
                        ON v.volumeID = u.volumeID
                        WHERE u.userID = :userID
                        AND seriesID = :seriesID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':seriesID', $seriesID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    public function updateVolumeCount($userID, $seriesID, $volumeCount)
    {
        $this->db->connect();
        $this->sql = 'UPDATE user_has_serie 
                      SET volumeCount = :volumeCount
                      WHERE userID = :userID
                      AND seriesID = :seriesID
                      ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->bind(':volumeCount', $volumeCount);
        $this->db->execute();
        $this->db->close();
    }
}