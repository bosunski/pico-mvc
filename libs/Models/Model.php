<?php
    namespace Pico\Models;

    use Pico\Dbcore;

    class Model
    {
        protected $db;

        public function __construct()
        {
            $this->db = Dbcore::getInstance();
        }
    }
