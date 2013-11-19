<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 18:43
 */

namespace ebussola\job;


interface CommandData {

    /**
     * @param int $command_id
     *
     * @return Command
     */
    public function find($command_id);

} 