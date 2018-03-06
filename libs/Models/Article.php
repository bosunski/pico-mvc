<?php
    namespace Pico\Models;

    use Pico\Dbcore;

    class Article extends Model
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function create($data)
        {

            $sql = "INSERT INTO articles VALUES (NUll, ?, ?, ?)";
            //require('mvc.conf.php');
            $exec = $this->db->prepare($sql, [$data->catid, $data->title, $data->body]);
            if($exec) return true;

            return false;
        }

        public function getArticle($id)
        {
            $sql = "SELECT * FROM articles WHERE id = ? LIMIT 1";
            $exec = $this->db->get_single_result($sql, [$id]);
            if($exec) return $exec;
            return false;
        }

        public function list()
        {
            $sql = "SELECT * FROM articles";
            $exec = $this->db->get_result($sql, []);
            if($exec) return $exec;
            return false;
        }
    }
