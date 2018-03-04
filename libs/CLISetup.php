<?php
    namespace Pico;

    use Pico\Models\CLIModel;

    class CLISetup {
        private $dbsettings;
        private $configFile = 'mvc.conf.php';

        public function runCommand($args)
        {
            if(file_exists($this->configFile)) {
                echo "A configuration file already exists. Please delete first.\n";
                return 2;
            }

            echo "Welcome to the Pico Setup Console.\n";
            echo "Lets Get you started on the fly.\n";



            $dbsettings['host'] = readline("MySql Host > ");
            $dbsettings['user'] = readline("MySql User > ");
            $dbsettings['password'] = readline("MySql User Password > ");
            $dbsettings['dbname'] = readline("MySql Database > ");
            $this->dbsettings = (object) $dbsettings;

            echo "Testing Connection...\n";
            sleep(2);

            if(!$this->testConnection()) {
                echo "Ops! Cannot connect to mysql with the things you input. Please be sure that they are correct.\n";
                return 2;
            }

            echo "Connection successful. \nWriting configuration file.\n\n";
            $sample = file_get_contents('sample.conf');
            $sample = str_replace('@dbname', $this->dbsettings->dbname, $sample);
            $sample = str_replace('@user', $this->dbsettings->user, $sample);
            $sample = str_replace('@password', $this->dbsettings->password, $sample);
            $sample = str_replace('@host', $this->dbsettings->host, $sample);

            if (file_put_contents($this->configFile, $sample)) {
                $this->createTables();
            }

            return 2;
        }

        private function createTables()
        {
            sleep(1);
            echo("Creating tables\n");

            $tables = CLIModel::createTables();
            if ($tables) {
                echo("Done Setting Up! Viola!!\n");
                return 2;
            }

            echo("Tables were unable to be created\n");

            return 2;
        }


        private function testConnection()
        {
            $dsn = 'mysql:host='.$this->dbsettings->host.';dbname='.$this->dbsettings->dbname;
            $con = null;
            try {
                $con = new \PDO($dsn, $this->dbsettings->user, $this->dbsettings->password);
            } catch(\PDOException $e) {

            }
            if(!$con) {
                echo('Cannot connect');
                return false;
            } else {
                return true;
            }
        }
    }
