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
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $username = filter_var($request->username, FILTER_SANITIZE_STRING);
            $password = filter_var($request->password, FILTER_SANITIZE_STRING);
            $apikey = filter_var($request->apikey, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        $user = $this->model('User')->getUser($username);
        if (password_verify($password, $user['userPW'])) {
            $api = $this->generateAPI();
            $this->model('User')->setLoggedInOut($user['userID'], $apikey, 1);
        }
    }

    public function logout()
    {
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $username = $request->username;
        }
        $user = $this->model('User')->getUser($username);
        $apikey = "";
        $this->model('User')->setLoggedInOut($user['userID'], $apikey, 0);
    }

    public function info()
    {
        $items_arr = array();
        $items_arr['user'] = array();
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
                "userIsLoggedIn" => $this->user['userIsLoggedIn'],
            );
            array_push($items_arr['user'], $user_item);
        }
        echo json_encode($items_arr);
    }

    public function register()
    {
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
