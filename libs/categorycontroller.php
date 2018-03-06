<?php
class CategoryController extends Controller
{
    public static $class = __CLASS__;

    public function __construct()
    {
        parent::__construct();
    }

    public function index($url = null)
    {
        $this->_view->_props['title'] = 'Categories';

        // Load the view
        //$this->_view->craft('home');

        echo "Home Page";
    }
}
