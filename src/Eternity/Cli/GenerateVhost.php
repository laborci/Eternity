<?php namespace Eternity\Cli;

use Eternity\ConfigBuilder\VhostGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;


class GenerateVhost extends Command {
	protected function configure() {
		$this
			->setName('generate-vhost')
			->setAliases(['vhost'])
			->setDescription('Generates vhost config file from the template');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$style = new SymfonyStyle($input, $output);

		$vg = new VhostGenerator();
		$vg->generate();
		$style->success('Done');
	}

}
