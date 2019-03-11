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
			->setAliases(['config'])
			->setDescription('Builds config file');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$style = new SymfonyStyle($input, $output);
		$conf =env('PROJECT-INI')['CONFIG'];

		$cb = new ConfigBuilder($conf['path']);
		$cb->build($conf['output'], $conf['namespace'], $conf['class']);
		$style->success('Done');
	}

}
