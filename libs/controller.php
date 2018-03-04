<?php
class Controller
{
    // CLass variables
    protected $error;
    protected $view;
    protected $notfound = '404';
    protected $linkCount = 2;
    protected $notfoundInfo = 'Page Not Found';
    protected $url;
    protected $outsorce;
    protected $fundcore;

    public function __construct()
    {
        $this->error = new ErrorHandler(__CLASS__);
        // Load Theme Engine
        $this->view = Craft::gI();
        // Load Route Register
        $this->registry = Register::gI();
        // Create the Db Instance
        $this->db = Dbcore::getInstance();

        //$this->_outsource = new OutSource;
        //$this->_fundcore = new FundCore($this->_outsource, $this->_view);
    }


    public function index($url)
    {
        if (empty($url)) {
            //$this->_view->craft('main');
        } else {
            $this->_url = $url;
            $this->process_other_link();
        }
    }

    // Handling of Errors due to page requests
    protected function load_error($key, $info)
    {
        $this->_error->craft($key, $info);
    }

    protected function process_other_link()
    {
        $entries = $this->_registry->getRegistry('pages');
        $url = filter_var($this->url, FILTER_SANITIZE_URL);
        $this->_link = explode('/', $this->url[1]);

        if (count($this->link) > $this->linkCount) {
            $this->load_error($this->notfound, $this->notfoundInfo);
        }

        if (!array_key_exists($this->link[0], $entries)) {
            $this->load_error($this->notfound, $this->notfoundInfo);
        }

        if (!method_exists($this, $entries[$this->link[0]])) {
            $this->load_error($this->notfound, $this->notfoundInfo);
        }

        $this->{$entries[$this->link[0]]}();
    }
}
