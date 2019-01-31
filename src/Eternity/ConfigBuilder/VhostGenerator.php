<?php namespace Eternity\ConfigBuilder;


class VhostGenerator {

	public function generate() {
		$path = getenv('ROOT').'/config/local';
		$template = file_get_contents($path.'/virtualhost.conf.template');
		$template = str_replace('{{domain}}', getenv('DOMAIN'), $template);
		$template = str_replace('{{root}}', getenv('ROOT'), $template);
		file_put_contents($path.'/virtualhost.conf', $template);
	}
}