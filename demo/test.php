<?php

include('../autoload.php');

use Gregwar\CAM\Planifier;
use Gregwar\CAM\Planning;
use Gregwar\CAM\Task;

$planning = new Planning;
$planning->initialize(3);
$planning->dump();
exit();

$working = $planning->getTimeSpans(10, false, 'Test');

$planning->clean();
$planning->dump();

/*
$planner = new Planifier;
$planner
    ->addTask(new Task(
*/
