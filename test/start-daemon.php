<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 25/12/13
 * Time: 20:20
 */

require __DIR__ . '/bootstrap.php';

$logger = new LoggerStub();

$schedule = new \ebussola\job\Schedule(new CommandDataStub(), $logger);
$daemon = new \ebussola\job\Daemon($schedule);

$logger->info('Daemon Started');
$daemon->start();