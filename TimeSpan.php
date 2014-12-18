<?php

namespace Gregwar\CAM;

class TimeSpan
{
    protected $start, $end;
    protected $data;

    public function __construct(\DateTime $start, \DateTime $end, $data = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->data = $data;
    }

    /**
     * Sets the timespan data
     *
     * @param $data the data of the span
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Gets the timespan data
     *
     * @return $data the data of the span
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Gets the start of the span
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Gets the end of the span
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * The duration of this timespan, in second
     *
     * @return a duration in seconds
     */
    public function duration()
    {
        return $this->end->getTimestamp()-$this->start->getTimestamp();
    }

    /**
     * Reduces this timspan by $duration
     *
     * @param the duration that should be reduced
     * @return an array containing the start and the end that was reduced
     */
    public function reduce($duration)
    {
        $start = $this->start;
        $end = new \DateTime;
        $end->setTimestamp($this->start->getTimestamp()+$duration);
        $this->start = $end;

        return array($start, $end);
    }

    public function __toString()
    {
        return 'TimeSpan from '.$this->start->format('d/m/Y H:i:s').' to '.
            $this->end->format('d/m/Y H:i:s').' ('.$this->data.')';
    }
}
