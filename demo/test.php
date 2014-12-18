<?php

include('../autoload.php');

use Gregwar\CAM\Planifier;
use Gregwar\CAM\Planning;
use Gregwar\CAM\Task;

$planning = new Planning();
$planning->setPrecision(15*60);
$planning->initialize(15);

var_dump($planning->allocate(1873, true, 'Tache 1'));

$planning->clean();

$planning->dump();
