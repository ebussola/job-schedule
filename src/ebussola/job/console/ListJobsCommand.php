<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 25/12/13
 * Time: 20:31
 */

namespace ebussola\job\console;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListJobsCommand extends Command {

    protected function configure() {
        $this
            ->setName('jobschedule:list')
            ->setDescription('List loaded Jobs');
    }

    public function run(InputInterface $input, OutputInterface $output) {
        $zmq_context = new \ZMQContext();
        $zmq_socket = $zmq_context->getSocket(\ZMQ::SOCKET_REQ);
        $zmq_socket->connect('ipc:///tmp/ebussola-job-schedule.ipc');

        $response = $zmq_socket->send('get loaded jobs')->recv();
        if ($response == null) {
            $output->writeln('Daemon is not responding');
        } else {
            $jobs = unserialize($response);
            foreach ($jobs as $job) {
                $output->writeln('['.$job['id'].'] ' . $job['command'] . ' => status_code:'.$job['status_code']);
            }
        }
    }

} 