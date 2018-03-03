<?php
    include('mvc.conf.php');

    include('inc/functions.php');

    Session::start();

    $app = new App;
    $app->run();
