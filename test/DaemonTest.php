<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 21/11/13
 * Time: 15:55
 */

class DaemonTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \ebussola\job\Daemon
     */
    private $daemon;

    /**
     * @var \ebussola\job\Schedule
     */
    private $schedule;

    public function setUp() {
        $job_data = new CommandDataStub();
        $this->schedule = new \ebussola\job\Schedule($job_data);
        $this->daemon = new \ebussola\job\Daemon($this->schedule);
    }

    public function testStartDueJobs_NoJobs() {
        $this->daemon->startDueJobs(new DateTime('01:10:00'));

        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(1)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(2)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(3)));
    }

    public function testStartDueJobs_1Job() {
        $this->daemon->startDueJobs(new DateTime('01:00:00'));

        $this->assertTrue($this->schedule->isRunning($this->schedule->getJob(1)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(2)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(3)));

        while ($this->schedule->isRunning($this->schedule->getJob(1))) {
            sleep(1);
        }

        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(1)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(2)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(3)));
    }

    public function testStartDueJobs_3Jobs() {
        $this->daemon->startDueJobs(new DateTime('00:00:00'));

        $this->assertTrue($this->schedule->isRunning($this->schedule->getJob(1)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(2)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(3)));
        $this->assertTrue($this->schedule->isRunning($this->schedule->getJob(4)));

        while ($this->schedule->isRunning($this->schedule->getJob(1))) {
            sleep(1);
        }
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(1)));
        $this->assertTrue($this->schedule->isRunning($this->schedule->getJob(2)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(3)));


        while ($this->schedule->isRunning($this->schedule->getJob(4))) {
            sleep(1);
        }
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(4)));


        while ($this->schedule->isRunning($this->schedule->getJob(2))) {
            sleep(1);
        }
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(1)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(2)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(3)));
        $this->assertFalse($this->schedule->isRunning($this->schedule->getJob(4)));

    }

}