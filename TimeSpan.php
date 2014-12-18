<?php

namespace Gregwar\CAM;

class TimeSpan
{
    protected $start, $end;

    public function __construct(\DateTime $start, \DateTime $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function __toString()
    {
        return 'TimeSpan from '.$this->start->format('d/m/Y H:i:s').' to '.
            $this->end->format('d/m/Y H:i:s');
    }
}
