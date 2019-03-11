<?php namespace Eternity\Cli;

use Eternity\ConfigBuilder\EnvBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BuildEnv extends Command {

	protected function configure() {
		$this
			->setName('env');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$style = new SymfonyStyle($input, $output);

		$envgen = new EnvBuilder();
		$envgen->build('WEB');
		$envgen->build('CLI');

		$style->success('Done');
	}

}
