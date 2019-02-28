<?php namespace RedFox\EntityGenerator;

use Application\Config;
use Eternity\ServiceManager\Service;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Updater {

	use Service;

	public function execute(InputInterface $input, OutputInterface $output, Application $application) {
		$style = new SymfonyStyle($input, $output);

		$style->title('Updating all entites');
		$folders = glob(getenv('ROOT') . '/'.Config::entity_generator()::path().'*');
		foreach ($folders as $folder) {
			if (is_dir($folder)) {
				$name = basename($folder);
				$command = $application->find('create-entity');
				$updateInput = new ArrayInput(['command' => 'create-entity', 'name' => $name, '--update']);
				$command->run($updateInput, $output);
			}
		}
	}

}