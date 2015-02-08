<?php

namespace Gregwar\Planning;

class Planning
{
    protected $spans = array();
    protected $startDate = null;

    public function __construct($startDate = null)
    {
        $this->startDate = $startDate ?: new \DateTime;
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

        usort($this->spans, function($a, $b) {
            return $a->getStart()->getTimestamp() - $b->getStart()->getTimestamp();
        });
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
     *
     * @return An array of TimeSpan (emptys)
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
     * Adds a timespan
     */
    public function addSpan(TimeSpan $span)
    {
        $this->spans[] = $span;
    }

    /**
     * Allocates spans to the planning
     *
     * @param $duration the duration required
     * @param $contiguous if you want it to be contiguous
     * @return an array with all spans
     */
    public function allocate($duration, $contiguous = false, $data = null)
    {
        $save = serialize($this->spans);
        $spans = array();
        $toDelete = array();

        foreach ($this->spans as $index => $span) {
            if ($span instanceof EmptyTimeSpan) {
                if (!$contiguous && $span->duration() < $duration) {
                    // Taking a whole span
                    $toDelete[] = $index;
                    $duration -= $span->duration();
                    $spans[] = new TimeSpan($span->getStart(), $span->getEnd(), $data);
                } else {
                    if (!$contiguous || $span->duration() >= $duration) {
                        // Breaking a span in parts
                        list($start, $end) = $span->reduce($duration);
                        $spans[] = new TimeSpan($start, $end, $data);
                        if ($span->duration() <= 0) {
                            $toDelete[] = $index;
                        }
                        $duration = 0;
                        break;
                    }
                }
            }
        }

        if ($duration > 0) {
            $this->spans = unserialize($save);
            throw new \Exception('Unable to allocate required span');
        }

        // Removing useless spans
        foreach ($toDelete as $index) {
            unset($this->spans[$index]);
        }

        // Adding spans to the planning
        foreach ($spans as $span) {
            $this->spans[] = $span;
        }

        if ($contiguous) {
            return $spans[0];
        } else {
            return $spans;
        }
    }

    /**
     * Get the spans
     *
     * @return an array of TimeSpan
     */
    public function getSpans()
    {
        return $this->spans;
    }

    /**
     * Get all the spans that are between a given interval
     */
    public function getSpansBetween(\DateTime $start, \DateTime $end)
    {
        $matches = array();
        foreach ($this->spans as $span) {
            if ($span->isBetween($start, $end)) {
                $matches[] = $span;
            }
        }

        return $matches;
    }
    
    /**
     * Get all the spans that are between a given interval
     */
    public function getSpansInside(TimeSpan $span)
    {
        return $this->getSpansBetween($span->getStart(), $span->getEnd());
    }

    public function dump()
    {
        foreach ($this->spans as $span) echo $span."\n";
    }
}
