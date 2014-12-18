<?php

namespace Gregwar\CAM;

class Planning
{
    protected $timeSpans = array();
    protected $startDate = null;

    public function __construct($startDate = null)
    {
        $this->startDate = $startDate ?: new \DateTime('today 00:00');
    }

    /**
     * Create empty time spans to fill
     *
     * @param $days days to fill
     */
    public function initialize($days = 30)
    {
        for ($day=0; $day<$days; $day++) {
            $spans = $this->spansForDay(new \DateTime('+'.$day.'days 00:00'));
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
     * This creates spans for a given day, the default logics here fills
     * the week day
     */
    protected function spansForDay(\DateTime $day)
    {
        $spans = array();

        $dow = $day->format('N');
        $date = $day->format('d-m-Y');
        $h = function($hour) use ($date) {
            return new \DateTime($date.' '.$hour);
        };
        if ($dow < 6) {
            $spans[] = new EmptyTimeSpan($h('08:00'), $h('12:00'));
            $spans[] = new EmptyTimeSpan($h('14:00'), $h('18:00'));
        }

        return $spans;
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

        foreach ($this->spans as $index => $span) {
            if (!$contiguous && $span->duration() < $duration) {
                // Taking a whole span
                $toDelete[] = $index;
                $duration -= $span->duration();
                $spans[] = new TimeSpan($span->getStart(), $span->getEnd(), $data);
            } else {
                // Breaking a span in parts
                list($start, $end) = $span->reduce($duration);
                $spans[] = new TimeSpan($start, $end, $data);
                if ($span->duration() <= 0) {
                    $toDelete[] = $index;
                }
                break;
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
