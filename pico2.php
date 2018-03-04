<?php
# Create arrays to hold our command history and list of valid commands.
$history = array();
$validCommands = array();
# Define some valid commands (I imagine we're programming a command-line
# interface to killer robot here...you know, typical day-to-day stuff)
$validCommands[] = 'kill';
$validCommands[] = 'destroy';
$validCommands[] = 'obliterate';
$validCommands[] = 'history';
$validCommands[] = 'byebye';

function tab_complete ($partial) {
  global $validCommands;
  return $validCommands;
};

readline_completion_function('tab_complete');

while (1) {
      $line = readline(date('H:i:s')." Enter command > ");

          readline_add_history($line);

            $history[] = $line;


            switch ($line) {
              case "kill":
                  echo "You don't want to do that.\n";
                  break;
              case "destroy":
                  echo "That really isn't a good idea.\n";
                  break;
              case "obliterate":
                  echo "Well, if we really must.\n";
                  break;
              case "history":
                  $counter = 0;

                  foreach($history as $command) {
                      $counter++;
                      echo("$counter: $command\n");
                    };
                    break;
                case "byebye":
          # If it's time to leave, we want to break from both the switch
          # statement and the while loop, so we break with a level of 2.
                    break 2;
                default :
          # Always remember to give feedback in the case of user error.
                echo("Sorry, command ".$line." was not recognised.\n");
            }
          };
          # If we reached here, outside of the while(1) loop, the user typed byebye.
          echo("Bye bye, come again soon!\n");
