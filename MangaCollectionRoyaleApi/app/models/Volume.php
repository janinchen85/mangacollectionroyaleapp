<?php

class Volume extends DB
{
    private $db, $sql;

    public function __construct()
    {
        $this->db = new DB();
        $this->sql = "";
    }

    function addNewVolume($volumeISBN, $volumeTitle, $volumePages, $volumeDate, $volumeVolume, $seriesID)
    {
        $this->db->connect();
        $this->sql = 'INSERT INTO volume (volumeISBN, volumeTitle, volumePages, volumeDate, volumeVolume, seriesID)
                      VALUES (:volumeISBN, :volumeTitle, :volumePages, :volumeDate, :volumeVolume, :seriesID)';
        $this->db->query($this->sql);
        $this->db->bind(':volumeISBN', $volumeISBN);
        $this->db->bind(':volumeTitle', $volumeTitle);
        $this->db->bind(':volumePages', $volumePages);
        $this->db->bind(':volumeDate', $volumeDate);
        $this->db->bind(':volumeVolume', $volumeVolume);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->execute();
        $this->db->close();
    }

    function getVolumeByISBN($isbn)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM volume v
                        JOIN author a
                        JOIN publisher p
                        JOIN series s
                        WHERE s.seriesID = v.seriesID
                        AND a.authorID = s.authorID
                        AND p.publisherID = s.publisherID
                        AND volumeISBN = :volumeISBN
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':volumeISBN', $isbn);
        $this->db->close();
        return $this->db->result();
    }

    function getVolumes($seriesID, $volumeID)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM volume
                       WHERE seriesID = :seriesID
                       AND volumeID != :volumeID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->bind(':volumeID', $volumeID);
        $this->db->close();
        return $this->db->results();
    }

    function getVolume($volumeTitle)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM volume
                       WHERE volumeTitle = :volumeTitle
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':volumeTitle', $volumeTitle);
        $this->db->close();
        return $this->db->result();
    }

    function getVolumesOfSerie($seriesID)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM volume
                       WHERE seriesID = :seriesID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->close();
        return $this->db->results();
    }

    function getUserVolumeStats($userID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_volume
                        WHERE userID = :userID 
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    function getUserVolume($userID, $volumeID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_volume
                        WHERE userID = :userID 
                        AND volumeID = :volumeID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':volumeID', $volumeID);
        $this->db->close();
        return $this->db->results();
    }


    function countVolumes($seriesID)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM volume
                       WHERE seriesID = :seriesID
                        AND volumeDate <= CURDATE()
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':seriesID', $seriesID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    function getUserVolumeRead($userID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_volume
                        WHERE userID = :userID 
                        AND isRead = 1
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    function getUserVolumeUnread($userID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_volume
                        WHERE userID = :userID 
                        AND isRead = 0
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    function checkVolume($userID, $volumeID)
    {
        $this->db->connect();
        $this->sql =
            '   SELECT * FROM user_has_volume
                        WHERE userID = :userID 
                        AND volumeID = :volumeID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':volumeID', $volumeID);
        $this->db->close();
        return $this->db->result();
    }

    function setVolumeStatus($userID, $volumeID, $isRead)
    {
        $this->db->connect();
        $this->sql = 'UPDATE user_has_volume 
                      SET isRead = :isRead
                      WHERE userID = :userID
                      AND volumeID = :volumeID
                      ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':volumeID', $volumeID);
        $this->db->bind(':isRead', $isRead);
        $this->db->execute();
        $this->db->close();
    }

    public function addVolume($userID, $volumeID)
    {
        $this->db->connect();
        $this->sql = 'INSERT INTO user_has_volume (userID, volumeID)
                      VALUES (:userID, :volumeID)';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':volumeID', $volumeID);
        $this->db->execute();
        $this->db->close();
    }

    public function deleteVolume($userID, $volumeID)
    {
        $this->db->connect();
        $this->sql = 'DELETE FROM user_has_volume 
                      WHERE userID = :userID 
                      AND volumeID = :volumeID';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':volumeID', $volumeID);
        $this->db->execute();
        $this->db->close();
    }



    public function owned($userID, $seriesID)
    {
        $this->db->connect();
        $this->sql =
            '           SELECT * FROM volume v 
                        JOIN user_has_volume u 
                        WHERE v.volumeID = u.volumeID
                        AND u.userID = :userID 
                        AND v.seriesID = :seriesID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':seriesID', $seriesID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    public function getUnread($userID, $seriesID)
    {
        $this->db->connect();
        $this->sql =
            '           SELECT * FROM volume v 
                        JOIN user_has_volume u 
                        WHERE v.volumeID = u.volumeID
                        AND isRead = 0
                        AND u.userID = :userID 
                        AND v.seriesID = :seriesID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':seriesID', $seriesID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    public function getRead($userID, $seriesID)
    {
        $this->db->connect();
        $this->sql =
            '           SELECT * FROM volume v 
                        JOIN user_has_volume u 
                        WHERE v.volumeID = u.volumeID
                        AND isRead = 1
                        AND u.userID = :userID 
                        AND v.seriesID = :seriesID
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':seriesID', $seriesID);
        $rows = $this->db->rowCount($this->db->results());
        $this->db->close();
        return $rows;
    }

    public function nextVolume($seriesID)
    {
        $this->db->connect();
        $this->sql =
            '           SELECT * FROM volume 
                        WHERE seriesID = :seriesID 
                        AND volumeDate >= CURDATE() 
                        LIMIT 1 
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->close();
        return $this->db->result();
    }


}