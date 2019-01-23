<?php namespace Eternity\Cli;

use Eternity\ConfigBuilder\ConfigBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;


class BuildConfig extends Command {
	protected function configure() {
		$this
			->setName('build-config')
			->setAliases(['bc'])
			->setDescription('Builds config file');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$style = new SymfonyStyle($input, $output);

		$config = include getenv('ROOT').'/config/config-builder.php';
		$cb = new ConfigBuilder($config);
		$cb->build(getenv('ROOT').'/Application');
		$style->success('Done');
	}

}
