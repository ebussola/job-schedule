<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 18:07
 */

namespace ebussola\job;


interface JobRunner {

    /**
     * @param Job $cmd
     *
     * @return mixed
     */
    public function runIt(Job $cmd);

    /**
     * @param Job $cmd
     *
     * @return bool
     */
    public function isRunning(Job $job);

    /**
     * @param Job $job
     *
     * @return bool
     */
    public function isWaiting(Job $job);

} 