<?php

namespace Vysinsky\HipChat;


use PageBoost\HipChatV2\HipChat;
use PageBoost\HipChatV2\HipChatFactory;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;


/**
 * Class Logger
 *
 * PSR-3 compatible logger. This logger sends notification to your HipChat room
 *
 * @author  Michal Vyšinský <vysinsky@live.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Logger extends AbstractLogger
{

	/**
	 * Array of levels for which notification (HipChat's popup) is enabled
	 * @var array
	 */
	public static $notifyLevels = [
		LogLevel::ERROR,
		LogLevel::CRITICAL
	];

	/**
	 * Basic colors setup. In case of unknown key yellow color will be used
	 * @var array
	 */
	public static $colors = [
		LogLevel::INFO => HipChat::COLOR_GRAY,
		LogLevel::WARNING => HipChat::COLOR_YELLOW,
		LogLevel::ERROR => HipChat::COLOR_RED,
		LogLevel::CRITICAL => HipChat::COLOR_RED,
	];

	/** @var HipChat */
	private $hipChat;

	/** @var string */
	private $room;


	/**
	 * @param  string $apiToken Your HipChat Room's API token
	 * @param  string $room Your room name
	 */
	public function __construct($apiToken, $room)
	{
		$this->hipChat = HipChatFactory::instance();
		$this->hipChat->setAccessToken($apiToken);
		$this->room = $room;
	}


	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function log($level, $message, array $context = array())
	{
		$message = $this->interpolate($message, $context);
		$this->hipChat->room($this->room)->send($message, $this->shouldNotify($level), $this->getColor($level));
	}


	private function interpolate($message, $context)
	{
		$replace = [];
		foreach ($context as $key => $value) {
			$replace['{' . $key . '}'] = $value;
		}
		return strtr($message, $replace);
	}


	private function shouldNotify($level)
	{
		return in_array($level, self::$notifyLevels);
	}


	private function getColor($level)
	{
		if (isset(self::$colors[$level])) {
			return self::$colors[$level];
		}
		return HipChat::COLOR_YELLOW;
	}

}
