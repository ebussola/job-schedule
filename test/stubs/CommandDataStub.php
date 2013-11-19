<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 19:06
 */

class CommandDataStub implements \ebussola\job\CommandData {

    /**
     * @param int $command_id
     *
     * @return \ebussola\job\Command
     */
    public function find($command_id) {
        switch ($command_id) {
            case 1 :
                $cmd = new \ebussola\job\command\Command();
                $cmd->id = 1;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = 'JobRunnerStub';
                break;

            case 2 :
                $cmd = new \ebussola\job\command\Command();
                $cmd->id = 2;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = 'JobRunnerStub';
                $cmd->parent_id = 1;
                break;

            case 3 :
                $cmd = new \ebussola\job\command\Command();
                $cmd->id = 3;
                $cmd->expires_on = strtotime('+1 hour');
                $cmd->runner_class = 'JobRunnerStub';
                $cmd->parent_id = 2;
                break;
        }

        if (isset($cmd)) {
            return $cmd;
        }

        return null;
    }

}