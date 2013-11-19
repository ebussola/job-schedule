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
        $this->schedule = new \ebussola\job\Schedule($command_data);
    }

    public function testGetCommand() {
        $cmd = $this->schedule->getCommand(1);

        $this->assertInstanceOf('\ebussola\job\Command', $cmd);
        $this->assertEquals(1, $cmd->id);

        // testing pool
        $cmd->status_code = 1;
        $cmd2 = $this->schedule->getCommand(1);

        $this->assertInstanceOf('\ebussola\job\Command', $cmd2);
        $this->assertEquals(1, $cmd2->status_code);
        $this->assertEquals($cmd, $cmd2);
    }

    public function testIsRunning() {
        $cmd = $this->schedule->getCommand(1);
        $this->schedule->run($cmd);

        $this->assertTrue($this->schedule->isRunning($cmd));

        sleep(6);

        $this->assertFalse($this->schedule->isRunning($cmd));
    }

    public function testRun() {
        $cmd = $this->schedule->getCommand(1);
        $this->schedule->run($cmd);

        while ($this->schedule->isRunning($cmd)) {
            sleep(1);
        }

        $this->assertEquals(0, $cmd->exit_code);
        $this->assertEquals(0, $cmd->status_code);
    }

    public function testHierarchyError() {
        $dep_cmd = $this->schedule->getCommand(1);
        $dep_cmd->status_code = 3;

        $cmd = $this->schedule->getCommand(2);
        $this->schedule->run($cmd);

        $this->assertEquals(2, $cmd->status_code);
    }

    public function testHierarchyRun_1Parent() {
        $cmd1 = $this->schedule->getCommand(1);
        $this->schedule->run($cmd1);

        $cmd2 = $this->schedule->getCommand(2);
        $this->schedule->run($cmd2);

        $this->assertEquals(1, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(4, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        while ($this->schedule->isRunning($cmd1)) {
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
        $cmd1 = $this->schedule->getCommand(1);
        $this->schedule->run($cmd1);

        $cmd2 = $this->schedule->getCommand(2);
        $this->schedule->run($cmd2);

        $cmd3 = $this->schedule->getCommand(3);
        $this->schedule->run($cmd3);

        $this->assertEquals(1, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(4, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        $this->assertEquals(4, $cmd3->status_code);
        $this->assertEquals(0, $cmd3->exit_code);

        while ($this->schedule->isRunning($cmd1)) {
            sleep(1);
        }

        $this->assertEquals(0, $cmd1->status_code);
        $this->assertEquals(0, $cmd1->exit_code);

        $this->assertEquals(1, $cmd2->status_code);
        $this->assertEquals(0, $cmd2->exit_code);

        $this->assertEquals(4, $cmd3->status_code);
        $this->assertEquals(0, $cmd3->exit_code);

        while ($this->schedule->isRunning($cmd2)) {
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

}