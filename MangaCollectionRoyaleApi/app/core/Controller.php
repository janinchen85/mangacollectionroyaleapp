<?php

class Controller
{
    private $timezone;
    private $root, $modelDir, $viewDir;
    private $index, $method;
    private $secret;
    private $access;

    function __construct()
    {
        session_start();
        require_once '../dev/engine/engine.php';
        require_once '../dev/database/pdo.php';
        $this->timezone = date_default_timezone_set('Europe/Berlin');
        $this->root = '../public/';
        $this->modelDir = '../app/models/';
        $this->viewDir = '../app/views/';
        $this->index = "home";
        $this->method = "index";
        $this->secret = "-GcS5Wap1";
        $this->access = 'false';
    }

    public function secret()
    {
        return $this->secret;
    }

    // Method: model()
    // Parameter: $model
    public function model($model)
    {
        $modelFile = $this->modelDir . $model . '.php';
        // cheack if the file with this name exist
        if (file_exists($modelFile)) {
            // if the file exist and is not already included, include this file
            require_once $modelFile; 
            // create new Object of the model class
            return new $model();
        } else {
            echo "Could not find this model: " . $modelFile;
        }
    }
    // Method: view()
    // Parameter: $view
    // This method is currently not needed
    public function view($view)
    {
        // save filepath in the variable $viewFile
        $viewFile = $this->viewDir . $view . '.php';
        // cheack if the file with this name exist
        if (file_exists($viewFile)) {
            // if the file exist and is not already included, include this file
            require_once $viewFile;
        } else {
            echo "Could not find this view: " . $viewFile;
        }
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    // Method: index()
    // Parameter: $index, $method, $navbar
    public function heading($index = "", $method = "", $navbar = "")
    {
        // create a new template object for navbar
        $this->nav = new tpl('style/' . $navbar . '');
            // if the parameter variable $method is not empty rewrite the value of $this->method
            // with the value of the parameter variable $method
        if (!empty($method)) {
            $this->method = $method;
        }
            // if the parameter variable $index is not empty rewrite the value of $this->index
            // with the value of the parameter variable $index
        if (!empty($index)) {
            $this->index = $index;
        }
        // create a new tpl Object, so it can be user for the view
        $this->index = new tpl($this->index . '/' . $this->method);
        //$this->index->assign('header', $this->header->replace());
        //$this->index->assign('footer', $this->footer->replace());
        $this->index->assign('root', $this->getRoot());
        return $this->index;
    }

    public function setTitle($title)
    {
        $this->index->assign('title', $title);
    }

    public function setView()
    {
        $this->index->replace();
        $this->index->show();
    }

    public function substrwords($text, $maxchar, $end = '...')
    {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);
            $output = '';
            $i = 0;
            while (1) {
                $length = strlen($output) + strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } else {
            $output = $text;
        }
        return $output;
    }

}

?>