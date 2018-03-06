<?php
    namespace Pico\Models;

    use Pico\Dbcore;

    class CLIModel
    {
        public static function createTables()
        {
            $catSql = 'CREATE TABLE categories (
                        id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
                        name varchar(255) NOT NULL
                    );';
            $articleSql = 'CREATE TABLE articles (
                            id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
                            catid int NOT NULL,
                            title varchar(255) NOT NULL,
                            body text(5000) NOT NULL
                        );';
            $db = Dbcore::getInstance();
            $exec = $db->prepare($catSql.$articleSql, []);
            if($exec) return true;

            return false;
        }
    }
