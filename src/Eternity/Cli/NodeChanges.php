<?php namespace Eternity\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NodeChanges extends Command {

	protected function configure() {
		$this->setName('nodechanges')->setAliases(['nc'])->setDescription('Looking for changes in node_modules')
			->addArgument('pattern', InputArgument::OPTIONAL, "searchpattern", "phlex-*")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$style = new SymfonyStyle($input, $output);

		$style->title('Looking for changes');
		$folders = glob(getenv('ROOT') . '/node_modules/'.$input->getArgument('pattern'));
		foreach ($folders as $folder) {
			if (is_dir($folder)) {
				chdir($folder);
				echo exec("publish-diff --filter='lib/**'", $output);
				if(trim(join('',$output))){
					$style->warning(basename($folder));
				}else{
					$style->success(basename($folder));
				}
				$output = '';
			}
		}

	}

}
