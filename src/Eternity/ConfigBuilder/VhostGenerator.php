<?php namespace Eternity\ConfigBuilder;

class VhostGenerator {

	public function generate() {
		$project_ini = env('PROJECT-INI');
		$template = file_get_contents($project_ini['VIRTUALHOST']['template']);
		$template = str_replace('{{domain}}', env('DOMAIN'), $template);
		$template = str_replace('{{root}}', env('ROOT'), $template);
		file_put_contents($project_ini['VIRTUALHOST']['output'], $template);
	}
	
}