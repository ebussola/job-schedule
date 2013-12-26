<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 19:06
 */

class CommandDataStub implements \ebussola\job\JobData {

    private $get_all_times_executed = 0;

    /**
     * @param int $command_id
     *
     * @return \ebussola\job\Job
     */
    public function find($command_id) {
        switch ($command_id) {
            case 1 :
                $cmd = new \ebussola\job\job\Job();
                $cmd->id = 1;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = 'JobRunnerStub';
                $cmd->schedule = '@hourly';
                $cmd->command = '?';
                break;

            case 2 :
                $cmd = new \ebussola\job\job\Job();
                $cmd->id = 2;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = 'JobRunnerStub';
                $cmd->parent_id = 1;
                $cmd->schedule = '@daily';
                $cmd->command = '?';
                break;

            case 3 :
                $cmd = new \ebussola\job\job\Job();
                $cmd->id = 3;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = 'JobRunnerStub';
                $cmd->parent_id = 2;
                $cmd->schedule = '0 8 * * *';
                $cmd->command = '?';
                break;

            case 4 :
                $cmd = new \ebussola\job\job\Job();
                $cmd->id = 4;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = 'JobRunnerStub';
                $cmd->schedule = '@daily';
                $cmd->command = '?';
                break;

            case 5 :
                $cmd = new \ebussola\job\job\Job();
                $cmd->id = 5;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = '\\JobRunnerStub';
                $cmd->schedule = '@daily';
                $cmd->command = '?';
                break;

            case 6 :
                $cmd = new \ebussola\job\job\Job();
                $cmd->id = 6;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = '\\JobRunnerStub';
                $cmd->schedule = '@daily';
                $cmd->command = '?';
                break;
        }

        if (isset($cmd)) {
            return $cmd;
        }

        return null;
    }

    /**
     * @return \ebussola\job\Job[]
     */
    public function getAll() {
        $jobs = array();
        $jobs[] = $this->find(1);
        $jobs[] = $this->find(2);
        $jobs[] = $this->find(3);
        $jobs[] = $this->find(4);
        $jobs[] = $this->find(5);

        switch ($this->get_all_times_executed) {
            case 1 :
                $jobs[] = $this->find(6);
                break;
            case 2 :
                $jobs[] = $this->find(6);
                unset($jobs[0]);
                unset($jobs[1]);
                break;
        }

        $this->get_all_times_executed++;

        return $jobs;
    }
}