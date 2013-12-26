<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 24/12/13
 * Time: 20:57
 */

namespace ebussola\job\console;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshJobsCommand extends Command {

    protected function configure() {
        $this
            ->setName('jobschedule:refresh')
            ->setDescription('Refresh loaded Jobs');
    }

    public function run(InputInterface $input, OutputInterface $output) {
        $zmq_context = new \ZMQContext();
        $zmq_socket = $zmq_context->getSocket(\ZMQ::SOCKET_REQ);
        $zmq_socket->connect('ipc:///tmp/ebussola-job-schedule.ipc');

        $response = $zmq_socket->send('refresh jobs')->recv();
        if ($response == null) {
            $output->writeln('Daemon is not responding');
        } else {
            if ($response == 1) {
                $output->writeln('Jobs Reloaded!');
            } else {
                $output->writeln('Error');
            }
        }
    }

} 