<?php

include(__DIR__.'/../autoload.php');

use Gregwar\Planning\Planning;
use Gregwar\Planning\Task;

// Creating a planning, with 15 free days of empty
// span
$planning = new Planning();
$planning->initialize(15);

// Allocating tasks
$planning->allocate(45*60, true, 'Task 1');
$planning->allocate(90*60, false, 'Task 2');

// Cleaning empty spans and dumping
$planning->clean();
$planning->dump();
