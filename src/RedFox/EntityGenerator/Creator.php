<?php namespace RedFox\EntityGenerator;

use Application\Config;
use CaseHelper\CaseHelperFactory;
use Eternity\ServiceManager\ServiceContainer;
use Minime\Annotations\Reader;
use RedFox\Database\PDOConnection\AbstractPDOConnection;
use RedFox\Entity\Relation\BackReference;
use Eternity\ServiceManager\Service;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Creator {

	use Service;

	/** @var EntityGeneratorConfigInterface  */
	protected $config;

	public function __construct(EntityGeneratorConfigInterface $config) {
		$this->config = $config;
	}


	public function execute(InputInterface $input, OutputInterface $output, Application $application) {

		$name = ucfirst($input->getArgument('name'));
		$recreate = (bool)$input->getOption('recreate');

		$this->output = new SymfonyStyle($input, $output);
		$this->output->writeln('::: '.$name.' :::');

		$root = getenv('ROOT');
		$entityDirectory = $root . '/'. Config::entity_generator()::path() . $name;
		$entityHelperDirectory = $entityDirectory . '/Helpers';
		$templateDirectory = __DIR__ . '/templates';
		$templateHelperDirectory = $templateDirectory . '/Helpers';

		if ($recreate) {
			$this->output->write('Removing existing entity files at ' . $entityDirectory . ' ... ');

			if (file_exists($entityHelperDirectory)) {
				$files = array_diff(scandir($entityHelperDirectory), ['.', '..']);
				foreach ($files as $file) unlink("$entityHelperDirectory/$file");
				rmdir($entityHelperDirectory);
			}
			if (file_exists($entityDirectory)) {
				$files = array_diff(scandir($entityDirectory), ['.', '..']);
				foreach ($files as $file) unlink("$entityDirectory/$file");
				rmdir($entityDirectory);
			}
			$this->output->writeln('DONE');
		}

		if (!is_dir($entityDirectory)) mkdir($entityDirectory);
		if (!is_dir($entityHelperDirectory)) mkdir($entityHelperDirectory);

		if (file_exists($entityHelperDirectory . '/source.php')) {
			list ($database, $table) = include($entityHelperDirectory . '/source.php');
		} else {
			$table = $input->getArgument('table');
			$table = is_null($table) ? CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_CAMEL_CASE)->toSnakeCase($name) : $table;
			$databaseId = $input->getArgument('database');
			$databaseId = is_null($databaseId) ? $this->config::default_database() : $databaseId;
			$database = $this->config::databases()[$databaseId];
		}


		$dictionary = [
			'name'     => $name,
			'database' => $database,
			'table'    => $table,
		];

		$this->translateFile($entityDirectory . '/' . $name . '.php', $templateDirectory . '/entity.template.php', $dictionary);
		$this->translateFile($entityDirectory . '/' . $name . 'Repository.php', $templateDirectory . '/repository.template.php', $dictionary);
		$this->translateFile($entityDirectory . '/' . $name . 'Model.php', $templateDirectory . '/model.template.php', $dictionary);
		$this->translateFile($entityHelperDirectory . '/source.php', $templateHelperDirectory . '/source.template.php', $dictionary);
		$this->translateFile($entityHelperDirectory . '/RepositoryTrait.php', $templateHelperDirectory . '/RepositoryTrait.template.php', $dictionary, true);
		$this->translateFile($entityHelperDirectory . '/EntityTrait.php', $templateHelperDirectory . '/EntityTrait.template.php', $dictionary);
		$this->translateFile($entityHelperDirectory . '/Finder.php', $templateHelperDirectory . '/Finder.template.php', $dictionary, true);
		$this->translateFile($entityHelperDirectory . '/fields.php', $templateHelperDirectory . '/fields.template.php', $dictionary);

		$this->updateFields($database, $table, $entityHelperDirectory . '/fields.php');

		$this->createModelTrait($database, $table, $name,
			$templateHelperDirectory . '/ModelTrait.template.php',
			$entityHelperDirectory . '/ModelTrait.php');

		$this->createEntityInterface($database, $table, $name,
			$templateHelperDirectory . '/EntityInterface.template.php',
			$entityHelperDirectory . '/EntityInterface.php');

		$this->createEntityTrait($database, $table, $name,
			$templateHelperDirectory . '/EntityTrait.template.php',
			$entityHelperDirectory . '/EntityTrait.php');

		$this->output->writeln('');

	}

	protected function createEntityTrait($database, $table, $name, $source, $destination) {
		$fields = '';
		$class = "\\Entity\\" . $name . "\\" . $name;
		/** @var \RedFox\Entity\Model $model */
		$model = $class::model();

		$generatedLines = [];

		$fields = $model->getFields();
		foreach ($fields as $field) {
			$fieldObj = $model->getField($field);
			$generatedLines[] = ' * @property' . ($fieldObj->readonly() ? '-read' : '') . ' ' . $this->getFieldDataType($fieldObj) . ' $' . $field;
		}

		$relations = $model->getRelations();
		foreach ($relations as $relation) {
			$relationObj = $model->getRelation($relation);
			$generatedLines[] = ' * @property-read' . ' ' . $relationObj->getRelatedClass() . ' $' . $relation;
			if ($relationObj instanceof BackReference) {
				$generatedLines[] = ' * @method' . ' ' . $relationObj->getRelatedClass() . ' ' . $relation . '($order=null, $limit=null, $offset=null)';
			}
		}

		$attahcmentGroups = $model->getAttachmentGroups();
		foreach ($attahcmentGroups as $attahcmentGroup) {
			$generatedLines[] = ' * @property-read \\RedFox\\Entity\\Attachment\\AttachmentManager $' . $attahcmentGroup;
		}

		$fields = join("\n", $generatedLines);

		$dictionary = [
			'name'     => $name,
			'database' => $database,
			'table'    => $table,
			'fields'   => $fields,
		];
		$this->translateFile($destination, $source, $dictionary, true);
	}

	protected function createEntityInterface($database, $table, $name, $source, $destination) {
		$constants = '';
		/** @var AbstractPDOConnection $PDOConnection */
		$PDOConnection = ServiceContainer::get($database);
		$access = $PDOConnection->createSmartAccess();

		foreach ($access->getFieldData($table) as $db_field) {
			$label = $db_field['Field'];
			$options = $access->getEnumValues($table, $db_field['Field']);
			foreach ($options as $option) {
				$constant = str_replace(' ', '_', strtoupper($label . '_' . $option));
				$constants .= "\tconst $constant = '$option';\n";
			}
		}
		$dictionary = [
			'name'      => $name,
			'database'  => $database,
			'table'     => $table,
			'constants' => $constants,
		];
		$this->translateFile($destination, $source, $dictionary, true);
	}

	protected function createModelTrait($database, $table, $name, $source, $destination) {
		$fields = '';
		/** @var AbstractPDOConnection $PDOConnection */
		$PDOConnection = ServiceContainer::get($database);
		$access = $PDOConnection->createSmartAccess();

		foreach ($access->getFieldData($table) as $db_field) {
			$type = $this->selectRedfoxField($db_field, $db_field['Field']);
			$label = $db_field['Field'];
			$fields .= ' * px: @property-read ' . $type . ' $' . $label . "\n";
		}

		$dictionary = [
			'name'     => $name,
			'database' => $database,
			'table'    => $table,
			'fields'   => $fields,
		];
		$this->translateFile($destination, $source, $dictionary, true);
	}

	protected function translateFile($destination, $source, $dictionary, $force = false) {
		if (!file_exists($destination) || $force) {
			$this->output->write('Creating file: ' . $destination . ' ... ');
			$output = file_get_contents($source);
			foreach ($dictionary as $key => $value) {
				$output = str_replace('{{' . $key . '}}', $value, $output);
			}
			file_put_contents($destination, $output);
			$this->output->writeln('DONE');
		}
	}

	protected function updateFields($database, $table, $destination) {
		$this->output->write('Updating file: ' . $destination . ' ... ');

		/** @var AbstractPDOConnection $PDOConnection */
		$PDOConnection = ServiceContainer::get($database);
		$access = $PDOConnection->createSmartAccess();

		$fields = include($destination);
		$modifiers = [];
		foreach ($fields as $field => $rest) {
			unset($fields[$field]);
			$fieldname = trim($field, '@!');
			$modifiers[$fieldname] = '';
			if (strpos($field, '@') !== false) $modifiers[$fieldname] .= '@';
			if (strpos($field, '!') !== false) $modifiers[$fieldname] .= '!';
			$fields[$fieldname] = $rest;
		}

		$encoder = new \Riimu\Kit\PHPEncoder\PHPEncoder();

		$output = '<?php return [' . "\n";
		foreach ($access->getFieldData($table) as $db_field) {
			$label = (array_key_exists($db_field['Field'], $modifiers) ? $modifiers[$db_field['Field']] : '') . $db_field['Field'];

			if (strpos($label, '!') !== false) {
				$output .= "\t'$label' => [" . '\\' . $fields[$db_field['Field']][0] . '::class' . (array_key_exists(1,$fields[$db_field['Field']]) ? ', ' . $encoder->encode($fields[$db_field['Field']][1], ['array.inline' => true]) : '') . "],\n";
			} else {
				$type = $this->selectRedfoxField($db_field, $db_field['Field']) . '::class';
				$output .= "\t'$label' => [$type";
				$options = $access->getEnumValues($table, $db_field['Field']);
				if (count($options)) $output .= ', ' . $encoder->encode($options, ['array.inline' => true]);
				$output .= "],\n";
			}
		}
		$output .= "];";

		file_put_contents($destination, $output);

		$this->output->writeln('DONE.');
	}


	protected function selectRedfoxField($db_field, $fieldName) {
		$dbtype = $db_field['Type'];
		if ($db_field['Comment'] == 'password') return '\RedFox\Entity\Fields\PasswordField';
		if ($db_field['Comment'] == 'json') return '\RedFox\Entity\Fields\JsonStringField';

		if ($dbtype == 'tinyint(1)') return '\RedFox\Entity\Fields\BoolField';
		if ($dbtype == 'date') return '\RedFox\Entity\Fields\DateField';
		if ($dbtype == 'datetime') return '\RedFox\Entity\Fields\DateTimeField';
		if ($dbtype == 'float') return '\RedFox\Entity\Fields\FloatField';

		if (strpos($dbtype, 'int(11) unsigned') === 0 && (substr($fieldName, -2) == 'Id' || $fieldName == 'id')) return '\RedFox\Entity\Fields\IdField';
		if (strpos($dbtype, 'int') === 0) return '\RedFox\Entity\Fields\IntegerField';
		if (strpos($dbtype, 'tinyint') === 0) return '\RedFox\Entity\Fields\IntegerField';
		if (strpos($dbtype, 'smallint') === 0) return '\RedFox\Entity\Fields\IntegerField';
		if (strpos($dbtype, 'mediumint') === 0) return '\RedFox\Entity\Fields\IntegerField';
		if (strpos($dbtype, 'bigint') === 0) return '\RedFox\Entity\Fields\IntegerField';

		if (strpos($dbtype, 'varchar') === 0) return '\RedFox\Entity\Fields\StringField';
		if (strpos($dbtype, 'char') === 0) return '\RedFox\Entity\Fields\StringField';
		if (strpos($dbtype, 'text') === 0) return '\RedFox\Entity\Fields\StringField';
		if (strpos($dbtype, 'text') === 0) return '\RedFox\Entity\Fields\StringField';
		if (strpos($dbtype, 'tinytext') === 0) return '\RedFox\Entity\Fields\StringField';
		if (strpos($dbtype, 'mediumtext') === 0) return '\RedFox\Entity\Fields\StringField';
		if (strpos($dbtype, 'longtext') === 0) return '\RedFox\Entity\Fields\StringField';

		if (strpos($dbtype, 'set') === 0) return '\RedFox\Entity\Fields\SetField';
		if (strpos($dbtype, 'enum') === 0) return '\RedFox\Entity\Fields\EnumField';

		return '\RedFox\Entity\Fields\UnsupportedField';
	}

	protected function getFieldDataType($fieldObj){
		$annotationReader = ServiceContainer::get(Reader::class);
		$annotations = $annotationReader->getClassAnnotations(get_class($fieldObj));
		return $annotations->get('datatype');
	}


}