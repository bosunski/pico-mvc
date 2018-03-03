<?php
  class Craft {
    private static $instance;
    private $craft_suffix = '.craft.php';
    private $view_dir = '/views/def_view/';
    private $title;
    public $_props;
    private $_outsource;

    private function __construct() {
      $this->_outsource = new OutSource;
    }

    // Loads Website configurations
    public function load_options() {
      $res = $this->_outsource->load_options();
      $this->level_payments = array(1000, 2000, 5000, 20000, 100000);
      foreach ($res as $key => $options) {
        foreach ($options as $k => $value) {
          $this->_options[$options['name']] = $value;
        }
      }
    }

    /* Creates a Singleton instance of this class */
    public static function gI() {
      if(!isset(self::$instance)) {
        $cls = __CLASS__;
        self::$instance = new $cls;
      }
      return self::$instance;
    }

    /* Loads a particular View */
    public function craft($file, $cmd = null) {
      $this->load_options();
      // Checking if the site is on
      if($this->_options['site_status'] == 'offline') {
        // If its a mandatory request
        $this->_cY = date('Y', time());
        if($cmd == 'verbose') {
          $file = ABSPATH . $this->view_dir . $file . $this->craft_suffix;
          if(file_exists($file)) {
            if(isset($this->_props))
              $this->set_prop($this->_props);
            require($file);
          }
        } else {
          $file = ABSPATH . $this->view_dir . 'maintenance' . $this->craft_suffix;
          require($file);
        }
      } else {
        $this->_cY = date('Y', time());
        $file = ABSPATH . $this->view_dir . $file . $this->craft_suffix;
        if(file_exists($file)) {
          if(isset($this->_props))
            $this->set_prop($this->_props);
          require($file);
        }
      }
      // Exiting so that only one view will be rendred 
      exit;
    }

    //Loads the Header
    private function getHomeHeader() {
      require(ABSPATH . $this->view_dir . 'header.craft.php');
    } 

    private function getDashHeader() {
      require(ABSPATH . $this->view_dir . 'dashHead.craft.php');
      //$this->craft('dashHead');
    }   

    //Loads the Header
    private function getHomeFooter() {
      require(ABSPATH . $this->view_dir . 'footer.craft.php');
      //$this->craft('footer');
    }

    /*  Loads the Model thats describes specific parts of a page */
    public static function getModel($model) {
      include(ABSPATH . $this->view_dir . 'air_default.php');
      if(array_key_exists($model, $air_model)) {
        return $air_model[$model];
      }
      return ' ';
    }

    /* Sets the page properties */
    public function set_prop(array $props) {
      foreach ($props as $key => $value) {
        $this->$key = $value;
      }
    }

    public function get($key) {
      return (isset($this->{$key}) && $this->{$key} != null) ? $this->{$key} : '';
    }



  }
?>
