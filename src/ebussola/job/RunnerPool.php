<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 18:14
 */

namespace ebussola\job;


use ebussola\common\pool\inmemory\PoolAbstract;

class RunnerPool extends PoolAbstract {

    /**
     * @param $object
     *
     * @return int | string
     */
    protected function makeId($object) {
        return get_class($object);
    }

}