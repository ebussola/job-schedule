<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 18:43
 */

namespace ebussola\job;


interface JobData {

    /**
     * @param int $command_id
     *
     * @return Job
     */
    public function find($command_id);

    /**
     * @return Job[]
     */
    public function getAll();

} 