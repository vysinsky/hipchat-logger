HipChat Logger
==============

[PSR-3](http://www.php-fig.org/psr/psr-3/) compatible logger class which will send you notification to your [HipChat](https://www.hipchat.com) room.

## Screenshot (taken with Nette Framework's Tracy bridge)
![Screenshot](screenshot.png)

## Installation

Via `composer require vysinsky/hipchat-logger:@dev`

## Configuration

Class `Vysinsky\HipChat\Logger` has some static properties you can use to configure:

- notifyLevels - Array of levels for which notification (HipChat's popup) is enabled
- colors - Basic colors setup based on log level. In case of unknown key yellow color will be used

## Available bridges

### Nette Framework ([Tracy](http://tracy.nette.org/en/))

Bridge class: `Vysinsky\HipChat\Bridges\Tracy\Logger`

Usage (in bootstrap.php):

```php
$configurator->enableDebugger(__DIR__ . '/../log'); // put it after this line
Debugger::setLogger(new Logger('<Your room API token>', '<Your room name>'));
```
That's all now on production environment you will get notice to HipChat's room.

### Log file link factory

You can set callback factory to Logger, which will create link to log file and send it in message if link is available.

You can set it with calling `setLinkToLogFileFactory` (only in Tracy\Bridges\Logger):

```php
$logger->setLinkToLogFileFactory(function(Tracy\Bridges\Logger $logger, $logPath){
	return $logger->extractLogPath($logPath);
});
```
