<?php

class Publisher extends DB
{
    private $db, $sql;

    public function __construct()
    {
        $this->db = new DB();
        $this->sql = "";
    }

    function getPublishers()
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM publisher
               ORDER BY publisherName ASC
                    ';
        $this->db->query($this->sql);
        $this->db->close();
        return $this->db->results();
    }

    function searchPublisher($publisherName)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM publisher
                       WHERE publisherName = :publisherName
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':publisherName', $publisherName);
        $this->db->close();
        return $this->db->result();
    }

    function addPublisher($publisherName)
    {
        $this->db->connect();
        $this->sql = 'INSERT INTO publisher (publisherName)
                      VALUES (:publisherName)';
        $this->db->query($this->sql);
        $this->db->bind(':publisherName', $publisherName);
        $this->db->execute();
        $this->db->close();
    }

}