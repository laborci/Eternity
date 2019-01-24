<?php namespace RedFox\Cli;

use RedFox\EntityGenerator\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateEntities extends Command {

	protected function configure() {
		$this
			->setName('update-entities')
			->setAliases(['update'])
			->setDescription('Updates model from database table');
	}

	protected function execute(InputInterface $input, OutputInterface $output) { Updater::Service()->execute($input, $output, $this->getApplication()); }

}
