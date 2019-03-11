<?php namespace Eternity\ConfigBuilder;


class ConfigBuilder {

	protected $sets = [];

	public function __construct($configurations) {

		$files = glob($configurations.'/*.php');
		foreach ($files as $file){
			if(is_file($file)){
				/** @var \Eternity\ConfigBuilder\ConfigSegment $set */
				$set = include $file;
				$name = $set->config;
				if(is_null($name))$name = pathinfo($file)['filename'];
				$this->addSet($name, $set);
			}
		}
	}


	protected function addSet($name, $values) {
		$set = [];
		foreach ($values as $key => $value) $set[str_replace('-', '_', $key)] = $value;
		$this->sets[str_replace('-', '_', $name)] = $set;
	}

	public function build($outputfile, $namespace, $class) {
		ob_start();

		echo "<?php namespace $namespace;" . "\n\n";
		echo "class $class{" . "\n";

		foreach ($this->sets as $key => $value) {
			echo "\n";
			echo "\tstatic protected \$cfg_$key;" . "\n";
			echo "\tstatic function $key():cfg_$key{" . "\n";
			echo "\t\tif(!self::\$cfg_$key) self::\$cfg_$key = new cfg_$key();" . "\n";
			echo "\t\treturn self::\$cfg_$key;" . "\n";
			echo "\t}" . "\n";
		}
		echo "}" . "\n";


		foreach ($this->sets as $key => $set) {
			echo "\n";
			echo "class cfg_$key ".(array_key_exists('interface', $set) && $set['interface'] ? "implements \\". $set['interface'] : '')."{" . "\n";
			if(array_key_exists('value', $set)) foreach ($set['value'] as $datakey => $data) {
				echo "\tstatic public function ".str_replace('-', '_', $datakey)."(){ return " . var_export($data, true) . "; }" . "\n";
			}
			if(array_key_exists('env', $set)) foreach ($set['env'] as $datakey=>$envkey) {
				if(!is_string($datakey)) $datakey = strtolower(str_replace('-', '_', $envkey));
				else{

				}
				echo "\tstatic public function " . $datakey . "(){ return env('" . strtoupper($envkey) . "'); }" . "\n";
			}
			echo "}" . "\n";
		}

		$output = ob_get_clean();
		file_put_contents($outputfile, $output);
	}
}