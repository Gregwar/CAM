<?php

include('../autoload.php');

use Gregwar\CAM\Planifier;
use Gregwar\CAM\Planning;
use Gregwar\CAM\Task;

$planning = new Planning;
$planning->initialize(15);

$working = $planning->getTimeSpans(3*60*60, true, 'Tache 1');

$planning->clean();
$planning->dump();

/*
$planner = new Planifier;
$planner
    ->addTask(new Task(
*/
