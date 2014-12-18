<?php

namespace Gregwar\CAM;

class Planning
{
    protected $timeSpans = array();
    protected $startDate = null;
    protected $precision;

    public function __construct($startDate = null, $precision = 1)
    {
        $this->startDate = $startDate ?: new \DateTime;
        $this->precision = $precision;
    }

    /**
     * Sets the starting date
     *
     * @param $startDate the starting date of the planning
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Sets the plannin precision
     *
     * @param $precision the precision in seconds
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Create empty time spans to fill
     *
     * @param $days days to fill
     */
    public function initialize($days = 30)
    {
        $base = $this->startDate->format('d-m-Y');
        $startTs = $this->startDate->getTimestamp();

        for ($day=0; $day<$days; $day++) {
            // Gets spans for a day
            $spans = $this->spansForDay(new \DateTime($base.'+'.$day.'days 00:00'));

            // Truncating spans of the past
            $toDelete = array();
            foreach ($spans as $index => $span) {
                $start = $span->getStart()->getTimestamp();
                $end = $span->getEnd()->getTimestamp();
                if ($end <= $startTs) {
                    $toDelete[] = $index;
                } else {
                    if ($start <= $startTs) {
                        $span->reduce(abs($startTs-$start));
                    }
                }
            }
            foreach ($toDelete as $index) {
                unset($spans[$index]);
            }

            // Adding spans
            foreach ($spans as $span) {
                $this->spans[] = $span;
            }
        }
    }

    /**
     * Cleans all the empty time span
     */
    public function clean()
    {
        $toDelete = array();
        foreach ($this->spans as $index => $span) {
            if ($span instanceof EmptyTimeSpan) {
                $toDelete[] = $index;
            }
        }
        foreach ($toDelete as $index) {
            unset($this->spans[$index]);
        }
    }

    /**
     * Working hours for a day
     *
     * @param DateTime the day
     * @return The work hours for this day
     */
    protected function workHours(\DateTime $day)
    {
        // We work all days except saturday and sunday
        if ($day->format('N')<6) {
            // Default working hours
            return array(
                array('08:00', '12:30'),
                array('14:00', '19:30'),
            );
        } else {
            return array();
        }
    }

    /**
     * This creates spans for a given day, the default logics here fills
     * the week day
     */
    protected function spansForDay(\DateTime $day)
    {
        $spans = array();

        $hours = $this->workHours($day);
        $date = $day->format('d-m-Y');
        $h = function($hour) use ($date) {
            return new \DateTime($date.' '.$hour);
        };

        foreach ($hours as $startEnd) {
            $start = new \DateTime($date.' '.$startEnd[0]);
            $end = new \DateTime($date.' '.$startEnd[1]);

            $spans[] = new EmptyTimeSpan($start, $end);
        }

        return $spans;
    }

    /**
     * Align a value to the precision
     */
    protected function align($value)
    {
        return ceil($value/$this->precision)*$this->precision;
    }

    /**
     * Gets a new timespan
     *
     * @param $duration the duration required
     * @param $contiguous if you want it to be contiguous
     * @return an array with all spans
     */
    public function getTimeSpans($duration, $contiguous = false, $data = null)
    {
        $spans = array();
        $toDelete = array();
        $duration = $this->align($duration);

        foreach ($this->spans as $index => $span) {
            if (!$contiguous && $span->duration() < $duration) {
                // Taking a whole span
                $toDelete[] = $index;
                $duration -= $span->duration();
                $duration = $this->align($duration);
                $spans[] = new TimeSpan($span->getStart(), $span->getEnd(), $data);
            } else {
                if (!$contiguous || $span->duration() >= $duration) {
                    // Breaking a span in parts
                    list($start, $end) = $span->reduce($duration);
                    $spans[] = new TimeSpan($start, $end, $data);
                    if ($span->duration() <= 0) {
                        $toDelete[] = $index;
                    }
                    break;
                }
            }
        }

        // Removing useless spans
        foreach ($toDelete as $index) {
            unset($this->spans[$index]);
        }

        // Adding spans to the planning
        foreach ($spans as $span) {
            $this->spans[] = $span;
        }

        return $spans;
    }

    public function dump()
    {
        foreach ($this->spans as $span) echo $span."\n";
    }
}
