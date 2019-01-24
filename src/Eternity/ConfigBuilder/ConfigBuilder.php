<?php namespace Eternity\ConfigBuilder;


class ConfigBuilder {

	protected $values = [];
	protected $sets = [];
	protected $envloaders = [];

	public function __construct($config) {
		foreach ($config['values'] as $key=>$value) $this->addValue($key, $value);
		foreach ($config['sets'] as $key=>$value) $this->addSet($key, $value);
	}


	protected function addValue($name, $value) { $this->values[str_replace('-', '_', $name)] = $value; }
	protected function addSet($name, array $values) {
		$set = [];
		foreach ($values as $key => $value) $set[str_replace('-', '_', $key)] = $value;
		$this->sets[str_replace('-', '_', $name)] = $set;
	}

	public function build($path) {
		ob_start();

		echo "<?php namespace Application;" . "\n\n";
		echo "class Config{" . "\n";
		foreach ($this->values as $key => $value) {
			//echo "\tconst $key = " . var_export($value, true) . ";" . "\n";
			echo "\tstatic public function $key(){ return " . var_export($value, true) . ";}" . "\n";
		}
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
			echo "class cfg_$key ".(array_key_exists('interface', $set) ? "implements \\". $set['interface'] : '')."{" . "\n";
			if(array_key_exists('value', $set)) foreach ($set['value'] as $datakey => $data) {
				echo "\tstatic public function ".str_replace('-', '_', $datakey)."(){ return " . var_export($data, true) . "; }" . "\n";
			}
			if(array_key_exists('env', $set)) foreach ($set['env'] as $datakey=>$envkey) {
				if(!is_string($datakey)) $datakey = strtolower(str_replace('-', '_', $envkey));
				else{

				}
				echo "\tstatic public function " . $datakey . "(){ return getenv('" . strtoupper($envkey) . "'); }" . "\n";
			}
			echo "}" . "\n";
		}

		$output = ob_get_clean();
		file_put_contents($path.'/Config.php', $output);
	}
}