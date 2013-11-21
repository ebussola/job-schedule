<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 19:11
 */

class JobRunnerStub implements \ebussola\job\JobRunner {

    private $running = [];

    private $running_until = [];

    private $waiting = [];

    /**
     * @param \ebussola\job\Job $cmd
     *
     * @return mixed
     */
    public function runIt(\ebussola\job\Job $cmd) {
        switch ($cmd->status_code) {
            case 1 :
            case 3 :
                $this->running[] = $cmd;
                $this->running_until[] = strtotime('+' . rand(1, 5) . ' seconds');
                break;

            case 4 :
                $this->waiting[] = $cmd;
                break;
        }
    }

    /**
     * @param \ebussola\job\Job $cmd
     *
     * @return bool
     */
    public function isRunning(\ebussola\job\Job $cmd) {
        $this->refreshJobs($cmd);

        return (in_array($cmd, $this->running));
    }

    /**
     * @param \ebussola\job\Job $job
     *
     * @return bool
     */
    public function isWaiting(\ebussola\job\Job $job) {
        $this->refreshJobs($job);

        return (in_array($job, $this->waiting));
    }

    /**
     * @param \ebussola\job\Job $cmd
     */
    private function refreshJobs(\ebussola\job\Job $cmd) {
        if (in_array($cmd, $this->running)) {
            $key = array_search($cmd, $this->running);
            if (time() > $this->running_until[$key]) {
                unset($this->running[$key]);
                unset($this->running_until[$key]);
                $cmd->status_code = 0;

                foreach ($this->waiting as $w_key => $cmd_waiting) {
                    if ($cmd_waiting->parent_id == $cmd->id) {
                        unset($this->waiting[$w_key]);
                        $cmd_waiting->status_code = 1;
                        $this->runIt($cmd_waiting);
                    }
                }
            }
        }
    }

}