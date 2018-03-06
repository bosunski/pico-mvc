<?php
    namespace Pico\Models;

    use Pico\Dbcore;

    class Category extends Model
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function create($name)
        {
            if($this->getCategory($name)) {
                echo "Category $name already exists.\n";
                return false;
            }

            $sql = "INSERT INTO categories VALUES (NUll, ?)";
            //require('mvc.conf.php');
            $exec = $this->db->prepare($sql, [$name]);
            if($exec) return true;

            return false;
        }

        public function getCategory($name)
        {
            $sql = "SELECT * FROM categories WHERE name = ? LIMIT 1";
            $exec = $this->db->get_single_result($sql, [$name]);
            if($exec) return $exec;
            return false;
        }

        public function getCategoryById($id)
        {
            $sql = "SELECT * FROM categories WHERE id = ? LIMIT 1";
            $exec = $this->db->get_single_result($sql, [$id]);
            if($exec) return $exec;
            return false;
        }

        public function list()
        {
            $sql = "SELECT * FROM categories";
            $exec = $this->db->get_result($sql, []);
            if($exec) return $exec;
            return false;
        }
    }
