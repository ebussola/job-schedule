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
     * @var \ZMQSocket
     */
    private $zmq_socket;

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
        $this->initSocket();

        while (true) {

            $this->startDueJobs();
            sleep(1);

            while (date('s') != 00) {
                $this->checkForExternalCommand();
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

    private function initSocket() {
        $zmq_context = new \ZMQContext();
        $this->zmq_socket = $zmq_context->getSocket(\ZMQ::SOCKET_REP);
        $this->zmq_socket->bind('ipc:///tmp/ebussola-job-schedule.ipc');
        chmod('/tmp/ebussola-job-schedule.ipc', 0777);
    }

    private function checkForExternalCommand() {
        $cmd = $this->zmq_socket->recv(\ZMQ::MODE_NOBLOCK);
        if ($cmd != null) {
            switch ($cmd) {
                case 'refresh jobs' :
                    $this->jobs = $this->schedule->getAllJobs();
                    $this->zmq_socket->send(1, \ZMQ::MODE_NOBLOCK);
                    break;

                case 'get loaded jobs' :
                    $data = array();
                    foreach ($this->jobs as $job) {
                        $data[] = (array) $job;
                    }
                    $this->zmq_socket->send(serialize($data), \ZMQ::MODE_NOBLOCK);
                    break;
            }
        }
    }

}