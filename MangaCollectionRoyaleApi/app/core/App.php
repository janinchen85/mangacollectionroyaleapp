<?php
// Create Class
class App{
    // set variables
    protected $controller;
    protected $method; 
    protected $params = []; 
    // Constructor
    public function __construct(){
        // call method parseURL() to get a string
        $url = $this->parseURL();
        // isset = Check if the variable exists and if it is not NULL
        // !is_numeric = check if the variable is not a number 
        if(isset($url[0]) && !is_numeric($url[0])){
            // set the first value of the array to the controller variable
            $this->controller = $url[0];
            // delete the first variable from the array
            // just for cleaning up memory
            // Benefits if the array is very long
            unset($url[0]);
        } else {
            $this->controller = "home";
        }
        if(isset($url[1]) && !is_numeric($url[1])){
            // set the sencond value of the array to the method variable
            $this->method = $url[1];
            unset($url[1]);
        } else {
            $this->method = "index";
        }
        // set controller path
        $controller = '../app/controllers/'.$this->controller.'.php';
        // check if there is a file with this controller name
        if(file_exists($controller)){
            // if there is this controller file and
            // If this file has not been included yet, include it
            require_once $controller;
            // create a new object of this controller class
            $this->controller = new $this->controller;
            // Check if the method exists within this class
            if(method_exists($this->controller,$this->method)){
                // set the rest values of the url array as parameters
                $this->params = $url ? array_values($url) : [];
                // callback with an array of parameters
                call_user_func_array([$this->controller, $this->method], $this->params);
            } else {
                // if this method doesn't exist return an error
                header("HTTP/1.0 404 Not Found");
                die();
            }
        } else {
            // if the controller doesn't exsist, return an error
            echo "Sorry, this page does not exist: ".$this->controller;
        }  
    }
    // Method: parseURL()
    public function parseURL(){
        if(isset($_GET['url'])){
            // explode = Search a string for the delimiter "/" and then split the string.
            // rtrim = remove white space (or other characters) from the end of a string
            // in this case: remove "/" at the end of thie url
            // FILTER_SANITIZE_URL = remove all illegal URL characters from a string
            // Example: $url= /public/home/
            return $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)); 
            // return = array("public","home")
        }
    }
}
?>