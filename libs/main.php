<?php
class Main extends Controller
{
    public static $class = __CLASS__;

    public function __construct()
    {
        parent::__construct();
    }

    public function index($url = null)
    {
        $this->_view->_props['title'] = 'Joint Hands | Equipping People';

        // Load the view
        //$this->_view->craft('home');

        echo "Home Page";
    }
}
