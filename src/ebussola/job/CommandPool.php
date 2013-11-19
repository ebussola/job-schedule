<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 18:47
 */

namespace ebussola\job;


use ebussola\common\pool\inmemory\PoolAbstract;

class CommandPool extends PoolAbstract {

    /**
     * @param Command $object
     *
     * @return int | string
     */
    protected function makeId($object) {
        return $object->id;
    }

}