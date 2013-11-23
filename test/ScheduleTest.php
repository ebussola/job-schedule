<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/11/13
 * Time: 19:05
 */

class ScheduleTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \ebussola\job\Schedule
     */
    private $schedule;

    public function setUp() {
        $command_data = new CommandDataStub();
        $this->schedule = new \ebussola\job\Schedule($command_data, new LoggerStub());
    }

    public function testGetJob() {
        $job = $this->schedule->getJob(1);

        $this->assertInstanceOf('\ebussola\job\Job', $job);
        $this->assertEquals(1, $job->id);

        // testing pool
        $job->status_code = 1;
        $job2 = $this->schedule->getJob(1);

        $this->assertInstanceOf('\ebussola\job\Job', $job2);
        $this->assertEquals(1, $job2->status_code);
        $this->assertEquals($job, $job2);
    }

    public function testGetAllJobs() {
        $jobs = $this->schedule->getAllJobs();
        foreach ($jobs as $job) {
            $this->assertInstanceOf('\ebussola\job\Job', $job);
        }
    }

    public function testIsRunning() {
        $job = $this->schedule->getJob(1);
        $job2 = $this->schedule->getJob(2);

        $this->schedule->run($job);

        $this->assertTrue($this->schedule->isRunning($job));
        $this->assertFalse($this->schedule->isRunning($job2));

        sleep(6);

        $this->assertFalse($this->schedule->isRunning($job));
        $this->assertFalse($this->schedule->isRunning($job2));
    }

    public function testIsWaiting() {
        $job = $this->schedule->getJob(1);
        $job2 = $this->schedule->getJob(2);

        $this->schedule->run($job);
        $this->schedule->run($job2);

        $this->assertTrue($this->schedule->isRunning($job));
        $this->assertTrue($this->schedule->isWaiting($job2));

        sleep(6);

        $this->assertFalse($this->schedule->isRunning($job));
        $this->assertFalse($this->schedule->isWaiting($job2));
    }

    public function testRun() {
        $cmd = $this->schedule->getJob(1);
        $this->schedule->run($cmd);

        while ($this->schedule->isRunning($cmd)) {
            sleep(1);
        }

        $this->assertEquals(0, $cmd->exit_code);
        $this->assertEquals(0, $cmd->status_code);
    }

    public function testHierarchyError() {
        $dep_cmd = $this->schedule->getJob(1);
        $dep_cmd->status_code = 3;

        $cmd = $this->schedule->getJob(2);
        $this->schedule->run($cmd);

        $this->assertEquals(2, $cmd->status_code);
    }

    public function testHierarchyRun_1Parent() {
        $cmd1 = $this->schedule->getJob(1);
        $this->schedule->run($cmd1);

        $cmd2 = $this->schedule->getJob(2);
        $this->schedule->run($cmd2);

        $this->assertEquals(1, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(4, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        while ($this->schedule->isRunning($cmd1)) {
            $this->schedule->isRunning($cmd2);
            sleep(1);
        }

        $this->assertEquals(0, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(1, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        while ($this->schedule->isRunning($cmd2)) {
            sleep(1);
        }

        $this->assertEquals(0, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(0, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);
    }

    public function testHierarchyRun_2Parents() {
        $cmd1 = $this->schedule->getJob(1);
        $this->schedule->run($cmd1);

        $cmd2 = $this->schedule->getJob(2);
        $this->schedule->run($cmd2);

        $cmd3 = $this->schedule->getJob(3);
        $this->schedule->run($cmd3);

        $this->assertEquals(1, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(4, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        $this->assertEquals(4, $cmd3->status_code);
        $this->assertEquals(0, $cmd3->exit_code);

        while ($this->schedule->isRunning($cmd1)) {
            $this->schedule->isRunning($cmd2);
            $this->schedule->isRunning($cmd3);
            sleep(1);
        }

        $this->assertEquals(0, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(1, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        $this->assertEquals(4, $cmd3->status_code);
        $this->assertEquals(0, $cmd3->exit_code);

        while ($this->schedule->isRunning($cmd2)) {
            $this->schedule->isRunning($cmd3);
            sleep(1);
        }

        $this->assertEquals(0, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(0, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        $this->assertEquals(1, $cmd3->status_code);
        $this->assertEquals(0, $cmd3->exit_code);

        while ($this->schedule->isRunning($cmd3)) {
            sleep(1);
        }

        $this->assertEquals(0, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(0, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        $this->assertEquals(0, $cmd3->status_code);
        $this->assertEquals(0, $cmd3->exit_code);
    }

    public function testBug_runner_class_starting_with_slash() {
        // bug runner_class starting with \
        $job = $this->schedule->getJob(5);
        $this->schedule->run($job);

        foreach ($this->schedule->getAllJobs() as $job) {
            $this->schedule->run($job);
        }
    }

}