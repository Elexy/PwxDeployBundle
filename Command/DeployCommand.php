<?php

namespace PwX\DeployBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends ContainerAwareCommand
{
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Console\Command.Command::configure()
   */
  protected function configure()
  {
    $this->setName('pjx:deploy')
         ->setDescription('Deploy the application to the cloud')
         ->addArgument('go', InputArgument::OPTIONAL, 'don\'t simulate, just do it!');
  }

  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Console\Command.Command::execute()
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
//    $intTime = time();
//    $output->writeln("Starting deploy");

    // get production db credentials

    // check migration status

    // connect to db

    // run all nescesary migration sqls


  }
}
