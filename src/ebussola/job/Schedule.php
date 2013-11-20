<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 17:15
 */

namespace ebussola\job;

class Schedule {

    const EXIT_CODE_NORMAL = 0;

    /**
     * @var RunnerPool
     */
    private $runner_pool;

    /**
     * @var CommandPool
     */
    private $command_pool;

    /**
     * @var CommandData
     */
    private $command_data;

    /**
     * @param CommandData $command_data
     */
    public function __construct(CommandData $command_data) {
        $this->runner_pool = new RunnerPool();
        $this->command_pool = new CommandPool();
        $this->command_data = $command_data;
    }

    /**
     * Runs a single command
     *
     * @param Command $cmd
     */
    public function run(Command $cmd) {
        $runner = $this->getJobRunner($cmd);

        if ($this->hasDependency($cmd)) {
            $cmd_dep = $this->getDependency($cmd);

            if ($cmd_dep->exit_code == self::EXIT_CODE_NORMAL && $this->isValid($cmd_dep)) {
                $cmd->status_code = 1;
                $this->execute($cmd, $runner);

            } else if ($this->isRunning($cmd_dep)) {
                $cmd->status_code = 4;
                $this->execute($cmd, $runner);

            } else {
                $cmd->status_code = 2;

            }
        } else {
            $cmd->status_code = 1;
            $this->execute($cmd, $runner);
        }
    }

    /**
     * @param int $command_id
     *
     * @return Command
     */
    public function getCommand($command_id) {
        if (!$this->command_pool->has($command_id)) {
            $cmd = $this->command_data->find($command_id);
            $this->command_pool->add($cmd);
        }

        $cmd = $this->command_pool->get($command_id);

        return $cmd;
    }

    /**
     * @param Command $cmd
     *
     * @return bool
     */
    public function isRunning(Command $cmd) {
        $parent_running = false;
        if ($cmd->parent_id != null) {
            $parent_cmd = $this->getDependency($cmd);
            $parent_running = $this->isRunning($parent_cmd);
        }

        $runner = $this->getJobRunner($cmd);
        return ($runner->isRunning($cmd) || $parent_running);
    }

    public function startDaemon() {
        // @todo IMPLEMENT THIS
    }

    /**
     * @param Command $cmd
     *
     * @return bool
     */
    private function isValid(Command $cmd) {
        return ($cmd->status_code == 0) && ($cmd->expires_on > time());
    }

    /**
     * Just run the job
     *
     * @param Command   $cmd
     * @param JobRunner $runner
     */
    private function execute(Command $cmd, JobRunner $runner) {
        $runner->runIt($cmd);
    }

    /**
     * @param Command $cmd
     *
     * @return bool
     */
    private function hasDependency(Command $cmd) {
        return $cmd->parent_id != null;
    }

    /**
     * @param Command $cmd
     *
     * @return Command
     */
    private function getDependency(Command $cmd) {
        return $this->getCommand($cmd->parent_id);
    }

    /**
     * @param Command $cmd
     *
     * @return JobRunner
     */
    private function getJobRunner(Command $cmd) {
        if (!$this->runner_pool->has($cmd->runner_class)) {
            $runner = new $cmd->runner_class();
            $this->runner_pool->add($runner);
        }
        $runner = $this->runner_pool->get($cmd->runner_class);

        return $runner;
    }

}