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
     * @var JobPool
     */
    private $job_pool;

    /**
     * @var JobData
     */
    private $job_data;

    /**
     * @param JobData $job_data
     */
    public function __construct(JobData $job_data) {
        $this->runner_pool = new RunnerPool();
        $this->job_pool = new JobPool();
        $this->job_data = $job_data;
    }

    /**
     * Runs a single command
     *
     * @param Job $job
     */
    public function run(Job $job) {
        $runner = $this->getJobRunner($job);

        if ($this->hasDependency($job)) {
            $cmd_dep = $this->getDependency($job);

            if ($cmd_dep->exit_code == self::EXIT_CODE_NORMAL && $this->isValid($cmd_dep)) {
                $job->status_code = 1;
                $this->execute($job, $runner);

            } else if ($this->isRunning($cmd_dep)) {
                $job->status_code = 4;
                $this->execute($job, $runner);

            } else {
                $job->status_code = 2;

            }
        } else {
            $job->status_code = 1;
            $this->execute($job, $runner);
        }
    }

    /**
     * @param int $job_id
     *
     * @return Job
     */
    public function getJob($job_id) {
        if (!$this->job_pool->has($job_id)) {
            $job = $this->job_data->find($job_id);
            $this->job_pool->add($job);
        }

        $job = $this->job_pool->get($job_id);

        return $job;
    }

    /**
     * @param Job $job
     *
     * @return bool
     */
    public function isRunning(Job $job) {
        $parent_running = false;
        if ($job->parent_id != null) {
            $parent_job = $this->getDependency($job);
            $parent_running = $this->isRunning($parent_job);
        }

        $runner = $this->getJobRunner($job);
        return ($runner->isRunning($job) || $parent_running);
    }

    public function startDaemon() {
        // @todo IMPLEMENT THIS
    }

    /**
     * @param Job $job
     *
     * @return bool
     */
    private function isValid(Job $job) {
        return ($job->status_code == 0) && ($job->expires_on > time());
    }

    /**
     * Just run the job
     *
     * @param Job   $job
     * @param JobRunner $runner
     */
    private function execute(Job $job, JobRunner $runner) {
        $runner->runIt($job);
    }

    /**
     * @param Job $job
     *
     * @return bool
     */
    private function hasDependency(Job $job) {
        return $job->parent_id != null;
    }

    /**
     * @param Job $job
     *
     * @return Job
     */
    private function getDependency(Job $job) {
        return $this->getJob($job->parent_id);
    }

    /**
     * @param Job $job
     *
     * @return JobRunner
     */
    private function getJobRunner(Job $job) {
        if (!$this->runner_pool->has($job->runner_class)) {
            $runner = new $job->runner_class();
            $this->runner_pool->add($runner);
        }
        $runner = $this->runner_pool->get($job->runner_class);

        return $runner;
    }

}