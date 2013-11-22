<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 22/11/13
 * Time: 10:33
 */

class LoggerStub implements \Psr\Log\LoggerInterface {
    use \Psr\Log\LoggerTrait;

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array()) {
        echo "\n[{$level}] " . $message;
    }

}