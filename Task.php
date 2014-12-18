<?php

namespace Gregwar\CAM;

abstract class Task
{
    protected $name;
    protected $duration;

    public function __construct($name, $duration)
    {
        $this->name = $name;
        $this->duration = $duration;
    }

    public function place(Planifier $planifier)
    {
        $planning = $planifier->getPlanning();
    }

    public function __toString()
    {
        return 'Task '.$this->name.' duration: '.$this->getDuration;
    }
}
