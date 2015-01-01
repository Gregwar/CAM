<?php

namespace Gregwar\Planning;

abstract class Task
{
    protected $name;
    protected $duration;

    public function __construct($name, $duration)
    {
        $this->name = $name;
        $this->duration = $duration;
    }

    public function __toString()
    {
        return 'Task '.$this->name.' duration: '.$this->duration;
    }
}
