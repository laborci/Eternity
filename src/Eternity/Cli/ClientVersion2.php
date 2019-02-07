<?php namespace Eternity\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;


class Clientversion extends Command {
	protected function configure() {
		$this
			->setName('clientversion')
			->setAliases(['cv'])
			->setDescription('Creates/Increments var/clientversion');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$style = new SymfonyStyle($input, $output);

		$file = getenv('ROOT').'/var/clientversion';
		if(!file_exists($file))$version = 0;
		else $version = file_get_contents($file);
		file_put_contents($file, ++$version);
		$style->success('Version: ' . $version);
	}

}
