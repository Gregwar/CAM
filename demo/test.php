<?php

include('../autoload.php');

use Gregwar\CAM\Planifier;
use Gregwar\CAM\Planning;
use Gregwar\CAM\Task;

$planning = new Planning(new \DateTime, 15*60);
$planning->initialize(15);

$working = $planning->getTimeSpans(1873, false, 'Tache 1');

$planning->clean();
$planning->dump();

/*
$planner = new Planifier;
$planner
    ->addTask(new Task(
*/
