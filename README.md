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

For Nette there is compiler extension. Just add it to your extensions list in neon configuration:

```
hipChatLogger: Vysinsky\HipChat\Bridges\Tracy\DI\Extension
```

And add some configuration:

```
hipChatLogger:
    accessToken: yourAccessToken
    roomName: test
    filters:
        - [LoggerFilter, filterAccess]
    linkFactory: [MyLinkFactory::createLink] # Set link factory
```

## Other features

### Filters

You can now easilly filter messages and decide, whether message should be sent. Filters are simple callbacks which get $level, $message and $context as parameter. Filter return boolean $shouldSend. As soon as any filter returns FALSE execution is stopped.

Example (we don't want to log 404s):

```php
class LoggerFilter 
{
    function filterAccess($level, $message, $context)
    {
        return $level !== 'access';
    }
}
```

### Log file link factory

You can set callback factory to Logger, which will create link to log file and send it in message if link is available.

You can set it with calling `setLinkToLogFileFactory` (only in Vysinsky\HipChat\Bridges\Tracy):

```php
$logger->setLinkToLogFileFactory(function(Vysinsky\HipChat\Bridges\Tracy $logger, $logPath){
	return $logger->extractLogPath($logPath);
});
```
