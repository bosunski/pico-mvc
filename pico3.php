<?php
    define("ESC", "\033");
    define("CLEAR", ESC."[cJ");
    define("HOME", ESC."[0;0f]");

    fwrite(STDOUT, "Press Enter to begin, And Enter Again to End");

    fread(STDIN, 1);

    stream_set_blocking(STDIN, 0);

    $rows = intval(`tput lines`);
    $cols = intval('tput cols');

    fwrite(STDOUT, CLEAR.HOME);

    for ($rowcount = 2; $rowcount < $rows; $rowcount++) {
        fwrite(STDOUT, ESC."[$rowcount;1f"."•");
        fwrite(STDOUT, ESC."[$rowcount;${cols}f"."•");
    }

    for ($colcount = 2; $colcount < $cols; $colcount++) {
        fwrite(STDOUT, ESC."[1;${colcount};f"."•");
        fwrite(STDOUT, ESC."[$rows;${colcount}f"."•");
    }

    fwrite(STDOUT, ESC."[1;1f"."•");
    fwrite(STDOUT, ESC."[1;${cols}f"."•");
    fwrite(STDOUT, ESC."[$rows;1f"."•");
    fwrite(STDOUT, ESC."[$rows;${cols}f"."•");

    $p = ["x" => intval($cols/2), "y" => intval($rows/2)];

    // Runs until user provides an input
    while(1) {
        if (fread(STDIN, 1)) {
            break;
        }

        $p['x'] = $p['x'] + rand(-1, 1);
        $p['y'] = $p['y'] + rand(-1, 1);

        if ($p['x'] > ($cols - 1)) { $p['x'] = ($cols - 1); }
        if ($p['y'] > ($rows - 1)) { $p['y'] = ($rows - 1); }

        if ($p['x'] < 2) { $p['x'] = 2; }
        if ($p['y'] < 2) { $p['y'] = 2; }

        $fg_color = rand(30, 37);
        $bg_color = rand(40, 47);

        fwrite(STDOUT, ESC."[${fg_color}m"); # \033[$32m sets green foreground
          fwrite(STDOUT, ESC."[${bg_color}m"); # \033[$42m sets green background

            fwrite(STDOUT, ESC."[${p['y']};${p['x']}f"."•");

              usleep(1000);
    }

    fwrite(STDOUT, ESC."[0m");

    fwrite(STDOUT, CLEAR.HOME);
