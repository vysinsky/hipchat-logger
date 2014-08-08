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

	/** @var callable|null */
	private $linkToLogFileFactory;


	/**
	 * @param  string $apiToken
	 * @param  string $room
	 */
	public function __construct($apiToken, $room)
	{
		parent::__construct(Tracy\Debugger::$logDirectory, Tracy\Debugger::$email, Tracy\Debugger::getBlueScreen());
		$this->logger = new Vysinsky\HipChat\Logger($apiToken, $room);
	}


	public function setLinkToLogFileFactory(callable $factory)
	{
		$this->linkToLogFileFactory = $factory;
	}


	public function extractLogPath($path)
	{
		return str_replace($_SERVER['DOCUMENT_ROOT'], NULL, $path);
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

		if ($this->linkToLogFileFactory && is_callable($this->linkToLogFileFactory)) {
			$linkToLogFile = $this->linkToLogFileFactory($this, $logPath);
			if ($linkToLogFile) {
				$protocol = 'http://';
				if (isset($_SERVER['HTTPS'])) {
					$protocol = 'https://';
				}

				$message .= ' <a href="' . $protocol . $_SERVER['HTTP_HOST'] . $linkToLogFile . '">(Open log file)</a>';
			}
		}

		$message .= ' [' . $_SERVER['HTTP_HOST'] . ']';

		$this->logger->log($priority, $message);

		return $logPath;
	}

}
