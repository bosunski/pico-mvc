<?php

    namespace Pico\Console;

    use Pico\CLISetup;

    class Console {

        protected $nl = "\n";

        protected $configFile = 'mvc.conf.php';

        public function __construct()
        {
            $this->setArgs();
            $this->checkEnviroments();
        }

        public function run()
        {
            //exit();
            $this->loadCommands();
            $base = explode(':', $this->argv[1])[0];

            if (array_key_exists(explode(':', $this->argv[1])[0], $this->commands)) {
                $obj = new $this->commands[$base];
                return $obj->runCommand($this->argv);
            }
        }

        private function checkEnviroments()
        {
            if(!file_exists($this->configFile)) {
                if( $this->argv[1] == 'install') return;
                echo "Cannot find configuration file.\n";
                echo "Please Run php pico intsall to create the config file.\n";
                exit();
            }
        }

        private function availableCommands()
        {

        }

        private function setArgs()
        {
            global $argv, $argc;
            $this->argv = $argv;
            $this->argc = $argc;
            unset($this->argv[0]);
            if (empty($this->argv)) {
                echo "Please Provide a command. Type help to view command list\n";
                exit(2);
            }
        }


        private function loadCommands():void
        {
            /**
             * This Logic can be changed but it basically creates a class variable array
             * containing available commands
             */
            $this->commands = [
                'install'     =>    $this->make(CLISetup::class),
                'category'    =>    $this->make(CategoryConsole::class),
                'article'    =>    $this->make(ArticleConsole::class)
            ];
        }


        private function make($class)
        {
            // We can change Logic to load dependencies better than this
            return $class;
        }
    }
