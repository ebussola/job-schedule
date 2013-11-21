<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 17:54
 */

namespace ebussola\job;

/**
 * Interface Command
 * @package ebussola\job
 *
 * @property int    $id
 * @property int    $status_code
 * @property int    $exit_code
 * @property int    $expires_on
 * @property int    $parent_id
 * @property string $runner_class
 * @property string $schedule
 * @property string $command
 */
interface Job {

} 