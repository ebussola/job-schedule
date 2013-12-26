<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 25/12/13
 * Time: 20:23
 */

class ExternalControllerCommandTest extends PHPUnit_Framework_TestCase {

    public function testGetLoadedJobsSocket() {
        $zmq_context = new \ZMQContext();
        $zmq_socket = $zmq_context->getSocket(\ZMQ::SOCKET_REQ);
        $zmq_socket->connect('ipc:///tmp/ebussola-job-schedule.ipc');

        $response = unserialize($zmq_socket->send('get loaded jobs')->recv());
        $this->assertCount(5, $response);
    }

    public function testListJobs() {
        $cli = new \Symfony\Component\Console\Application();
        $cli->addCommands(array(
            new \ebussola\job\console\ListJobsCommand()
        ));

        $cmd_tester = new \Symfony\Component\Console\Tester\CommandTester($cli->find('jobschedule:list'));
        $cmd_tester->execute(array(
            'command' => 'jobschedule:list'
        ));
        $output = $cmd_tester->getDisplay();

        $this->assertContains('[1]', $output);
        $this->assertContains('[2]', $output);
        $this->assertContains('[3]', $output);
        $this->assertContains('[4]', $output);
        $this->assertContains('[5]', $output);
        $this->assertNotContains('[6]', $output);
    }

    public function testRefreshJobs() {
        $cli = new \Symfony\Component\Console\Application();
        $cli->addCommands(array(
            new \ebussola\job\console\RefreshJobsCommand(),
            new \ebussola\job\console\ListJobsCommand()
        ));

        $refresh_tester = new \Symfony\Component\Console\Tester\CommandTester($cli->find('jobschedule:refresh'));
        $list_tester = new \Symfony\Component\Console\Tester\CommandTester($cli->find('jobschedule:list'));


        // stage 1
        $refresh_tester->execute(array('command' => 'jobschedule:refresh'));
        $output = $refresh_tester->getDisplay();
        $this->assertContains('Jobs Reloaded', $output);

        $list_tester->execute(array(
            'command' => 'jobschedule:list'
        ));
        $output = $list_tester->getDisplay();
        $this->assertContains('[1]', $output);
        $this->assertContains('[2]', $output);
        $this->assertContains('[3]', $output);
        $this->assertContains('[4]', $output);
        $this->assertContains('[5]', $output);
        $this->assertContains('[6]', $output);


        // stage 2
        $refresh_tester->execute(array('command' => 'jobschedule:refresh'));
        $output = $refresh_tester->getDisplay();
        $this->assertContains('Jobs Reloaded', $output);

        $list_tester->execute(array(
            'command' => 'jobschedule:list'
        ));
        $output = $list_tester->getDisplay();
        $this->assertNotContains('[1]', $output);
        $this->assertNotContains('[2]', $output);
        $this->assertContains('[3]', $output);
        $this->assertContains('[4]', $output);
        $this->assertContains('[5]', $output);
        $this->assertContains('[6]', $output);
    }

}
 