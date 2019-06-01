<?php

class User extends DB
{
    private $db, $sql;

    public function __construct()
    {
        $this->db = new DB();
        $this->sql = "";
    }

    public function getUser($userName)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM user
                       WHERE userName = :userName
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userName', $userName);
        $this->db->close();
        return $this->db->result();
    }

    public function getEmail($email)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM user
                       WHERE userEmail = :email
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':email', $email);
        $this->db->close();
        return $this->db->result();
    }

    public function setLoggedInOut($userID, $api, $userIsLoggedIn)
    {
        $this->db->connect();
        $this->sql = 'UPDATE user 
                      SET userIsLoggedIn = :userIsLoggedIn,
                          userAPIkey = :api
                      WHERE userID = :userID
                      ';
        $this->db->query($this->sql);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':api', $api);
        $this->db->bind(':userIsLoggedIn', $userIsLoggedIn);
        $this->db->execute();
        $this->db->close();
    }

    public function addUserRegisterCache($userCacheName, $userCacheEmail, $userCachePassword, $userCacheError)
    {
        $this->db->connect();
        $this->sql = 'INSERT INTO user_register_cache (userCacheName, userCacheEmail, userCachePassword, userCacheError)
                      VALUES (:userCacheName, :userCacheEmail, :userCachePassword, :userCacheError)';
        $this->db->query($this->sql);
        $this->db->bind(':userCacheName', $userCacheName);
        $this->db->bind(':userCacheEmail', $userCacheEmail);
        $this->db->bind(':userCachePassword', $userCachePassword);
        $this->db->bind(':userCacheError', $userCacheError);
        $this->db->execute();
        $this->db->close();
    }

    public function addUser($userName, $userEmail, $userPassword)
    {
        $this->db->connect();
        $this->sql = 'INSERT INTO user (userName, userEmail, userPW)
                      VALUES (:userName, :userEmail, :userPassword)';
        $this->db->query($this->sql);
        $this->db->bind(':userName', $userName);
        $this->db->bind(':userEmail', $userEmail);
        $this->db->bind(':userPassword', $userPassword);
        $this->db->execute();
        $this->db->close();
    }

    public function deleteUserCache($userName)
    {
        $this->db->connect();
        $this->sql = 'DELETE FROM user_register_cache 
                      WHERE userCacheName = :userName ';
        $this->db->query($this->sql);
        $this->db->bind(':userName', $userName);
        $this->db->execute();
        $this->db->close();
    }

    public function getUserRegisterCache($userName)
    {
        $this->db->connect();
        $this->sql =
            '  SELECT * FROM user_register_cache
                       WHERE userCacheName = :userName
                    ';
        $this->db->query($this->sql);
        $this->db->bind(':userName', $userName);
        $this->db->close();
        return $this->db->result();
    }

}