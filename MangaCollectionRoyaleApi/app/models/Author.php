<?php

class Author extends DB
{
    private $db, $sql;

    public function __construct()
    {
        $this->db = new DB();
        $this->sql = "";
    }

    function getAuthor($seriesID)
    {
        $this->db->connect();
        $this->sql = 'SELECT * FROM author
                    WHERE seriesID = :seriesID';
        $this->db->query($this->sql);
        $this->db->bind(':seriesID', $seriesID);
        $this->db->close();
        return $this->db->results();
    }

    function getAuthors()
    {
        $this->db->connect();
        $this->sql = 'SELECT * FROM author
                      ORDER BY authorName ASC ';
        $this->db->query($this->sql);
        $this->db->close();
        return $this->db->results();
    }

    function searchAuthor($authorName)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM author
                       WHERE authorName = :authorName
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':authorName', $authorName);
        $this->db->close();
        return $this->db->result();
    }

    function addAuthor($authorName)
    {
        $this->db->connect();
        $this->sql = 'INSERT INTO author (authorName)
                      VALUES (:authorName)';
        $this->db->query($this->sql);
        $this->db->bind(':authorName', $authorName);
        $this->db->execute();
        $this->db->close();
    }

}
