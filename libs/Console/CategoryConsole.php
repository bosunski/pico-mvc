<?php
    namespace Pico\Console;

    class CategoryConsole
    {
        public function __construct()
        {
            $this->setSubCommands();
        }
        public function runCommand($args)
        {
            $subcommand = $this->getSubCommand($args[1]);
        }

        public function getSubCommand($command)
        {
            $sub = explode(':', $command)[1] ?? null;
            if (!$sub) {
                echo "Incorrect chain of commands. Example is category:create.\n";
                return 2;
            }

            if (!in_array($sub, $this->subcommands)) {
                echo "Can't recognize '$sub' command, Options are:\n";
                $this->listSubCommands();
                return 2;
            }

            $this->processSubcommand($sub);

        }

        private function processSubcommand($sub)
        {
            switch ($sub) {
                case 'create':
                    echo $sub;
                break;

                case 'list':
                break;

                case 'delete':
                break;
            }
        }


        private function setSubCommands()
        {
            $this->subcommands = [
                'create',
                'delete',
            ];
        }

        private function listSubCommands()
        {
            foreach ($this->subcommands as $key => $value) {
                echo "=> $value\n";
            }
        }
    }
