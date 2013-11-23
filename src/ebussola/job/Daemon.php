<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 21/11/13
 * Time: 15:10
 */

namespace ebussola\job;


use Cron\CronExpression;

class Daemon {

    /**
     * @var Schedule
     */
    private $schedule;

    /**
     * @var Job[]
     */
    private $jobs;

    /**
     * @param Schedule $schedule
     */
    public function __construct(Schedule $schedule) {
        $this->schedule = $schedule;
        $this->jobs = $this->schedule->getAllJobs();
    }

    /**
     * Start Due Jobs
     */
    public function startDueJobs($current_time = null) {
        foreach ($this->jobs as $job) {
            $schedule = CronExpression::factory($job->schedule);
            if ($schedule->isDue($current_time)) {
                $this->schedule->run($job);
            }
        }
    }

    public function start() {
        while (true) {

            $this->startDueJobs();
            sleep(1);

            while (date('s') != 00) {
                $this->refreshJobs();
                sleep(1);
            }
        }
    }

    private function refreshJobs() {
        foreach ($this->jobs as $job) {
            $this->schedule->isRunning($job);
        }
    }

}