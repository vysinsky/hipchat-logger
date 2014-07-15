<?php

namespace Vysinsky\HipChat\Bridges\Tracy;

use Exception;
use Psr\Log\LogLevel;
use Tracy;
use Vysinsky;


class Logger extends Tracy\Logger
{

	/**
	 * @var Vysinsky\HipChat\Logger
	 */
	private $logger;


	/**
	 * @param  string $apiToken
	 * @param  string $room
	 */
	public function __construct($apiToken, $room)
	{
		parent::__construct(Tracy\Debugger::$logDirectory, Tracy\Debugger::$email, Tracy\Debugger::getBlueScreen());
		$this->logger = new Vysinsky\HipChat\Logger($apiToken, $room);
	}


	function log($value, $priority = self::INFO)
	{
		$logPath = parent::log($value, $priority);

		$message = ucfirst($priority . ': ');
		if ($value instanceof Exception) {
			$message .= $value->getMessage();
			$priority = LogLevel::CRITICAL;
		} else {
			$message .= (string) $value;
		}

		$message .= ' (' . $_SERVER['HTTP_HOST'] . ')';

		$this->logger->log($priority, $message);

		return $logPath;
	}

}
