<?php

include('../autoload.php');

use Gregwar\CAM\Planifier;
use Gregwar\CAM\Planning;
use Gregwar\CAM\Task;

$planning = new Planning();
$planning->setPrecision(15*60);
$planning->initialize(15);

$planning->allocate(1873, true, 'Task 1');
$planning->allocate(2034, false, 'Task 2');

$planning->clean();

$planning->dump();
