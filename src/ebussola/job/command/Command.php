<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 18:39
 */

namespace ebussola\job\command;


class Command implements \ebussola\job\Command {

    /**
     * @var int
     */
    public $id;

    /**
     * Job-Schedule status code
     * @see README.md
     *
     * @var int
     */
    public $status_code;

    /**
     * UNIX exit code
     *
     * @var int
     */
    public $exit_code;

    /**
     * Timestamp
     *
     * @var int
     */
    public $expires_on;

    /**
     * The parent dependency job id
     *
     * @var int
     */
    public $parent_id;

    /**
     * @var string
     */
    public $runner_class;

} 