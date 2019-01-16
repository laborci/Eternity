<?php namespace RedFox\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateEntities extends Command {

	protected function configure() {
		$this->setName('update-entities')->setAliases(['update'])->setDescription('Updates model from database table');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$style = new SymfonyStyle($input, $output);

		$style->title('Updating all entites');
		$folders = glob(getenv('ROOT') . '/Entity/*');
		foreach ($folders as $folder) {
			if (is_dir($folder)) {
				$name = basename($folder);
				$command = $this->getApplication()->find('create-entity');
				$updateInput = new ArrayInput(['command' => 'create-entity', 'name' => $name, '--update']);
				$command->run($updateInput, $output);
			}
		}

	}

}
