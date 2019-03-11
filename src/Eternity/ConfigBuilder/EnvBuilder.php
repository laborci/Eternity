<?php namespace Eternity\ConfigBuilder;

class EnvBuilder{

	/** @var array */
	protected $project_ini;

	public function __construct(){
		$this->project_ini = env('PROJECT-INI');
	}

	public function build($context){
		foreach ($this->project_ini['INI']['load'] as $inifile){
			$ini = parse_ini_file($inifile, true, INI_SCANNER_RAW);

			foreach ($ini as $key => $value){
				if (is_array($value)){
					$topkey = null;
					$ctx = substr($key, -4);
					if ($ctx === '@CLI' || $ctx === '@WEB'){
						if (
							($context === 'CLI' && $ctx === '@CLI') ||
							($context === 'WEB' && $ctx === '@WEB')
						)
							$topkey = substr($key, 0, -4);
					}else{
						$topkey = $key;
					}

					if (!is_null($topkey)){
						foreach ($value as $subkey => $subvalue){
							$ini[$topkey . '.' . $subkey] = $subvalue;
						}
					}

					unset($ini[$key]);
				}
			}

			foreach ($ini as $key => $value){
				$ctx = substr($key, -4);
				if ($ctx === '@CLI' || $ctx === '@WEB'){
					$key = substr($key, 0, -4);
					if (
						($context === 'CLI' && $ctx === '@CLI') ||
						($context === 'WEB' && $ctx === '@WEB')
					)
						$values[$key] = $value;
				}else{
					$values[$key] = $value;
				}
			}
		}

		do{
			$found = false;
			foreach ($values as $key => $value){
				$re = '/\$\{(.*?)\}/m';
				preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);
				foreach ($matches as $match){
					$found = true;
					$replace = '';
					if (array_key_exists($match[1], $values))
						$replace = $values[$match[1]];
					if (getenv($match[1]))
						$replace = getenv($match[1]);
					$values[$key] = str_replace($match[0], $replace, $values[$key]);
				}
			}
		}while ($found === true);

		foreach ($values as $key => $value){
			if (substr($key, 0, 1) === '.')
				unset($values[$key]);
		}

		file_put_contents(str_replace('{{CONTEXT}}', $context, $this->project_ini['ENV']['env']), "<?php return " . var_export($values, true) . ';');
	}

}