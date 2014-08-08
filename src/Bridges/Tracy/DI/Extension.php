<?php

namespace Vysinsky\HipChat\Bridges\Tracy\DI;

use Nette;
use Nette\DI\CompilerExtension;


class Extension extends CompilerExtension
{

	private $defaults = [
		'enabled' => TRUE,
		'filters' => [],
		'linkFactory' => NULL,
	];



	public function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
		$config = $this->getConfig($this->defaults);

		Nette\Utils\Validators::assertField($config, 'enabled', 'boolean');
		Nette\Utils\Validators::assertField($config, 'accessToken', 'string');
		Nette\Utils\Validators::assertField($config, 'roomName', 'string');
		Nette\Utils\Validators::assertField($config, 'filters', 'array');

		if (!$config['enabled']) {
			return;
		}

		unset($config['enabled']);
		$init = $class->methods['initialize'];
		$init->addBody(Nette\PhpGenerator\Helpers::format('
			$logger = new Vysinsky\HipChat\Bridges\Tracy\Logger(?, ?, ?, ?);
			Tracy\Debugger::setLogger($logger);
		', $config['accessToken'], $config['roomName'], $config['filters'], $config['linkFactory']));
	}

}
