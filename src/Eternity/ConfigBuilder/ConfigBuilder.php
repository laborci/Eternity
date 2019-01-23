<?php namespace Eternity\ConfigBuilder;


class ConfigBuilder {

	protected $values = [];
	protected $sets = [];
	protected $envloaders = [];

	public function __construct($config) {
		foreach ($config['values'] as $key=>$value) $this->addValue($key, $value);
		foreach ($config['sets'] as $key=>$value) $this->addSet($key, $value);
		foreach ($config['envloaders'] as $key=>$value) $this->addEnvLoader($key, $value);
	}


	protected function addValue($name, $value) { $this->values[str_replace('-', '_', $name)] = $value; }
	protected function addSet($name, array $values) {
		$set = [];
		foreach ($values as $key => $value) $set[str_replace('-', '_', $key)] = $value;
		$this->sets[str_replace('-', '_', $name)] = $set;
	}
	protected function addEnvLoader($name, $fields) {
		$this->envloaders[str_replace('-', '_', $name)] = $fields;
	}

	public function build($path) {
		ob_start();

		echo "<?php namespace Application;" . "\n\n";
		echo "class Config{" . "\n";
		foreach ($this->values as $key => $value) {
			echo "\tconst $key = " . var_export($value, true) . ";" . "\n";
		}
		foreach ($this->sets as $key => $value) {
			echo "\n";
			echo "\tstatic protected \$cfg_$key;" . "\n";
			echo "\tstatic function $key():cfg_$key{" . "\n";
			echo "\t\tif(!self::\$cfg_$key) self::\$cfg_$key = new cfg_$key();" . "\n";
			echo "\t\treturn self::\$cfg_$key;" . "\n";
			echo "\t}" . "\n";
		}
		foreach ($this->envloaders as $key => $value) {
			echo "\n";
			echo "\tstatic protected \$cfg_$key;" . "\n";
			echo "\tstatic function $key():cfg_$key{" . "\n";
			echo "\t\tif(!self::\$cfg_$key) self::\$cfg_$key = new cfg_$key();" . "\n";
			echo "\t\treturn self::\$cfg_$key;" . "\n";
			echo "\t}" . "\n";
		}
		echo "}" . "\n";


		foreach ($this->sets as $key => $value) {
			echo "\n";
			echo "class cfg_$key{" . "\n";
			foreach ($value as $subkey => $subvalue) {
				echo "\tconst $subkey = " . var_export($subvalue, true) . ";" . "\n";
			}
			echo "}" . "\n";
		}

		foreach ($this->envloaders as $key => $value) {
			echo "\n";
			echo "class cfg_$key{" . "\n";
			foreach ($value as $subkey) {
				echo "\tstatic function " . strtolower(str_replace('-', '_', $subkey)) . "(){return getenv('" . strtoupper($subkey) . "');}" . "\n";
			}
			echo "}" . "\n";
		}

		$output = ob_get_clean();
		file_put_contents($path.'/Config.php', $output);
	}
}