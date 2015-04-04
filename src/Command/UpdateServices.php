<?php
namespace Kpacha\Config\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kpacha\Config\Configuration;
use Kpacha\Config\ServiceManagerBuilder;

class UpdateServices extends Command
{
    protected function configure()
    {
        $this->setName('config:update-services')
                ->setDescription('Update the service config file')
                ->addArgument('dir', InputArgument::REQUIRED, 'config dir');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!($configDir = $input->getArgument('dir'))) {
            throw new \InvalidArgumentException("Specify a config dir");
        }
        
        $start = microtime(true);
        
        $output->writeln("Loading the config files");
        $builder = new ServiceManagerBuilder();
        $serviceManager = $builder->build(new Configuration($configDir, true));
        
        $output->writeln("Regenerating the solved services config file");
        $serviceManager->refreshConfigWithSolvedServices();
        $totalTime = microtime(true) - $start;
        $output->writeln("Solved services config file generated in {$totalTime} µs");
    }
}
