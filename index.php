<?php
    include('mvc.conf.php');

    require('libs/functions.php');

    $app = new App;

    Session::start();
    $app->run();
