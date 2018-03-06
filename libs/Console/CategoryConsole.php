<?php
    namespace Pico\Console;

    use Pico\Models\Category;

    class CategoryConsole
    {
        public function __construct()
        {
            $this->setSubCommands();
            $this->category = new Category;
        }

        public function runCommand($args)
        {
            $this->otherArgs = $args;
            $subcommand = $this->getSubCommand($args[1]);
            unset($args[1]);
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
            unset($this->otherArgs[1]);
            switch ($sub) {
                case 'create':
                    $this->createCategory();
                break;

                case 'list':
                    $this->listCategories();
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
                'list'
            ];
        }

        private function listSubCommands()
        {
            foreach ($this->subcommands as $key => $value) {
                echo "=> $value\n";
            }
        }

        private function createCategory()
        {
            if(empty($this->otherArgs)) {
                echo("Please Provide the name of the Category you want to create.\n");
                return 2;
            }

            //$category = new Category;
            $name = $this->otherArgs[2];
            $category = $this->category->create($name);

            if($category) {
                echo("Category $name Created successfully. \n");
            }
            return 2;
        }


        private function listCategories()
        {
            $categories = $this->category->list();

            if($categories) {
                echo "ID | Name\n";
                foreach ($categories as $key => $value) {
                    $category = (object) $value;
                    echo "$category->id  | $category->name\n";
                }
            }
            return 2;
        }
    }
