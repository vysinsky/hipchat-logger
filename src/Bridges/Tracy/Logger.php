<?php

namespace Vysinsky\HipChat\Bridges\Tracy;

use Exception;
use Psr\Log\LogLevel;
use Tracy;
use Vysinsky;


class Logger extends Tracy\Logger
{

	/** @var Vysinsky\HipChat\Logger */
	private $logger;

	/** @var callable|null */
	private $linkToLogFileFactory;


	/**
	 * @param  string $apiToken
	 * @param  string $room
	 */
	public function __construct($apiToken, $room, $filters = [], $linkToLogFileFactory = NULL)
	{
		$reflection = new \ReflectionClass('Tracy\\Logger');
		if ($reflection->getConstructor()) {
			parent::__construct(Tracy\Debugger::$logDirectory, Tracy\Debugger::$email, Tracy\Debugger::getBlueScreen());
		} else {
			$this->directory = Tracy\Debugger::$logDirectory;
			$this->email = Tracy\Debugger::$email;
		}
		$this->linkToLogFileFactory = $linkToLogFileFactory;
		$this->logger = new Vysinsky\HipChat\Logger($apiToken, $room, $filters);
	}


	public function setLinkToLogFileFactory(callable $factory)
	{
		$this->linkToLogFileFactory = $factory;
	}


	public function log($value, $priority = self::INFO)
	{
		$logPath = parent::log($value, $priority);

		if (!$logPath) { // old version of tracy
			if (isset($value[3])) {
				$logFile = trim(substr($value[3], 3));
				$logPath = $this->directory . '/' . $logFile;
			}
		}

		$message = ucfirst($priority . ': ');
		if ($value instanceof Exception) {
			$message .= $value->getMessage();
			$priority = LogLevel::CRITICAL;
		} else {
			if (is_array($value) && isset($value[1])) {
				$message .= $value[1];
			} else {
				$message .= (string) $value;
			}
		}

		if ($this->linkToLogFileFactory && is_callable($this->linkToLogFileFactory)) {
			$linkToLogFile = call_user_func_array($this->linkToLogFileFactory, [$this, $logPath]);
			if ($linkToLogFile) {
				$protocol = 'http://';
				if (isset($_SERVER['HTTPS'])) {
					$protocol = 'https://';
				}

				$message .= ' <a href="' . $protocol . $this->resolveHost() . $linkToLogFile . '">(Open log file)</a>';
			}
		}

		if (isset($value[2])) {
			$message .= ' [' . trim(str_replace('@', NULL, $value[2])) . ']';
		} else {
			$message .= ' [' . $this->resolveHost() . ']';
		}

		$this->logger->log($priority, $message);

		return $logPath;
	}


	private function resolveHost()
	{
		if (isset($_SERVER['HTTP_HOST'])) {
			return $_SERVER['HTTP_HOST'];
		} else {
			return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : NULL;
		}
	}

}
