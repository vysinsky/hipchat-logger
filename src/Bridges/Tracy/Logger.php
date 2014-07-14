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
			$message .= $this->createLinkToLogfile($logPath);
			$priority = LogLevel::CRITICAL;
		} else {
			$message .= (string) $value;
		}

		$this->logger->log($priority, $message);

		return $logPath;
	}


	private function extractLogPath($path)
	{
		return str_replace($_SERVER['DOCUMENT_ROOT'], NULL, $path);
	}


	private function createLinkToLogfile($path)
	{
		$protocol = 'http://';

		if (isset($_SERVER['HTTPS'])) {
			$protocol = 'https://';
		}
		return ' <a href="' . $protocol . $_SERVER['HTTP_HOST'] . $this->extractLogPath($path) . '">(Open log file)</a>';
	}

}
