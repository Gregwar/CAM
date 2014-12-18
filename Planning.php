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

        foreach ($this->spans as $span) echo $span."\n";
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

    public function getTimeSpan($duration)
    {

    }
}
