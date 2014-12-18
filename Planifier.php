<?php

namespace Gregwar\CAM;

class Planifier
{
    protected $tasks = array();
    protected $planning;

    public function __construct()
    {
        $this->planning = new Planning;
    }

    /**
     * Add a task
     *
     * @param Task the task
     */
    public function addTask(Task $task)
    {
        $this->tasks = $task;
    }

    /**
     * Removes a task
     *
     * @param Task the task
     */
    public function removeTask(Task $task)
    {
        $taskIndex = null;
        foreach ($this->tasks as $index => $t) {
            if ($task == $task) {
                $taskIndex = $index;
                break;
            }
        }
        if ($taskIndex !== null) {
            unset($this->tasks[$taskIndex]);
        }
    }

    /**
     * Gets the planning
     *
     * @return Planning the current planning this planifier is working on
     */
    public function getPlanning()
    {
        return $this->planning;
    }

    /**
     * Run the planifier
     *
     * @return Planning a planning containing everything
     */
    public function run()
    {
        while ($this->tasks) {
            $task = array_pop($this->tasks);
            $task->place($this->planning);
        }

        return $this->getPlanning();
    }
}
