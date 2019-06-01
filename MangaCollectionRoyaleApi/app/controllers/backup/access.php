<?php
class Access extends Controller
{
    public $user, $userName;
    public $username, $email, $password;

    public function index()
    {
        die("no access!");
    }

    public function user($mode = "", $userName = "")
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");    // cache for 1 day
        }
        $this->userName = $userName;
        if ($mode == "login") {
            $this->login();
        } else if ($mode == "info") {
            $this->info();
        } else if ($mode == "logout") {
            $this->logout();
        } else if ($mode == "register") {
            $this->register();
        } else if ($mode == "response") {
            $this->response();
        }
    }

    public function login()
    {
        $items_arr = array();
        $items_arr['user'] = array();
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");    // cache for 1 day
        }
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $username = filter_var($request->username, FILTER_SANITIZE_STRING);
            $password = filter_var($request->password, FILTER_SANITIZE_STRING);
        }
        if ($this->userName == $username) {
            $this->user = $this->model('User')->getUser($username);
            if (password_verify($password, $this->user['userPW'])) {
                $api = $this->generateAPI();
                $this->model('User')->setLoggedInOut($this->user['userID'], $api, 1);
            } else {
                die("no access!");
            }
        } else {
            die("no access!");
        }
    }

    public function logout()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");    // cache for 1 day
        }
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $username = $request->username;
        }
        if ($this->userName == $username) {
            $this->user = $this->model('User')->getUser($username);
            $api = "";
            $this->model('User')->setLoggedInOut($this->user['userID'], $api, 0);
        } else {
            die("no access!");
        }
    }

    public function info()
    {
        $items_arr = array();
        $items_arr['user'] = array();
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");    // cache for 1 day
        }
        $this->user = $this->model('User')->getUser($this->userName);
        if ($this->user['userIsLoggedIn'] == 0) {
            $user_item = array(
                "userID" => "",
                "userName" => "",
                "userAPIkey" => "",
                "userIsLoggedIn" => $this->user['userIsLoggedIn'],
            );
            array_push($items_arr['user'], $user_item);
        } else {
            $user_item = array(
                "userID" => $this->user['userID'],
                "userName" => $this->user['userName'],
                "userAPIkey" => $this->user['userAPIkey'],
                "userIsLoggedIn" => $this->user['userIsLoggedIn'],
            );
            array_push($items_arr['user'], $user_item);
        }
        echo json_encode($items_arr);
    }

    public function register()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");    // cache for 1 day
        }
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $username = filter_var($request->username, FILTER_SANITIZE_STRING);
            $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);
            $password = filter_var($request->password, FILTER_SANITIZE_STRING);
            $checkUser = $this->model('User')->getUser($username);
            $checkEmail = $this->model('User')->getEmail($email);
            if ($checkUser) {
                if ($checkEmail) {
                    $error = 1; // User and Email
                } else {
                    $error = 2; // User
                }
            } else if ($checkEmail) {
                $error = 3;  // Email
            } else {
                $error = 0; // Success
            }
            if ($error != 0) {
                $this->model('User')->addUserRegisterCache($username, $email, $password, $error);
            } else {
                $this->model('User')->addUser($username, $email, $password);
                $this->model('User')->deleteUserCache($username);
            }
        }
    }

    private function response()
    {
        $items_arr = array();
        $items_arr['response'] = array();
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");    // cache for 1 day
        }
        $checkUser = $this->model('User')->getUserRegisterCache($this->userName);
        if (!$checkUser) {
            $res_item = array(
                'Response' => "Success"
            );
            array_push($items_arr['response'], $res_item);
        } else if ($checkUser['userCacheError'] == 1) {
            $res_item = array(
                'Response' => "UserEmailError"
            );
            array_push($items_arr['response'], $res_item);
        } else if ($checkUser['userCacheError'] == 2) {
            $res_item = array(
                'Response' => "UserError"
            );
            array_push($items_arr['response'], $res_item);
        } else if ($checkUser['userCacheError'] == 3) {
            $res_item = array(
                'Response' => "EmailError"
            );
            array_push($items_arr['response'], $res_item);
        }
        echo json_encode($items_arr);
        $this->model('User')->deleteUserCache($this->userName);
    }

    private function generateAPI()
    {
        $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $block = "";

        for ($i = 0; $i < 32; $i++) {
            $block .= $string[rand(0, strlen($string) - 1)];
        }
        return chunk_split($block, 6, '-') . $this->secret();
    }
}
