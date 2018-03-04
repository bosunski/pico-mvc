<?php

    namespace Pico\Console;

    use Pico\CLISetup;

    class Console {

        protected $nl = "\n";

        public function __construct()
        {
            $this->setArgs();
            $this->loadCommands();
        }

        public function run()
        {
            $base = explode(':', $this->argv[1])[0];

            if (array_key_exists(explode(':', $this->argv[1])[0], $this->commands)) {
                $obj = $this->commands[$base];
                return $obj->runCommand($this->argv);
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
                'category'    =>    $this->make(CategoryConsole::class)
            ];
        }


        private function make($class)
        {
            // We can change Logic to load dependencies better than this
            return new $class;
        }
    }
