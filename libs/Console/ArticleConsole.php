<?php
    namespace Pico\Console;

    use Pico\Models\Category;
    use Pico\Models\Article;

    class ArticleConsole
    {
        public function __construct()
        {
            $this->setSubCommands();
            $this->article = new Article;
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
                echo "Incorrect chain of commands. Example is article:create.\n";
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
                    $this->createArticle();
                break;

                case 'list':
                    $this->listArticles();
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

        private function createArticle()
        {
            $categoryfound = false;
            // We loop until a correct category is inputed
            while(!$categoryfound) {
                $catid = readline("Choose Category > ");

                if(!$this->category->getCategoryById($catid)) {
                    echo "Category does not exist.\n";
                } else {
                    $categoryfound = true;
                }
            }

            $title = '';
            while($title == '') {
                $title = readline("Enter Title > ");
                if($title == '') echo "You must enter title.\n";
            }

            $body = '';
            while($body == '') {
                $body = readline("Enter content > ");
                if($body == '') echo "You must enter content.\n";
            }

            $data = (object)[
                'title' => $title,
                'catid' => $catid,
                'body' => $body,
            ];

            //$category = new Category;
            //$name = $this->otherArgs[2];
            $article = $this->article->create($data);

            if($article) {
                echo("Article Created successfully. \n");
            }
            return 2;
        }


        private function listArticles()
        {
            $articles = $this->article->list();

            if($articles) {
                echo "ID | Title\n";
                foreach ($articles as $key => $value) {
                    $article = (object) $value;
                    echo "$article->id  | $article->title\n";
                }
            }
            return 2;
        }
    }
