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
     * @param Command $cmd
     *
     * @return mixed
     */
    public function runIt(Command $cmd);

    /**
     * @param Command $cmd
     *
     * @return bool
     */
    public function isRunning(Command $cmd);

} 