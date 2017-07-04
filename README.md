# Iriven Php Logger
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XDCFPNTKUC4TU)

Iriven Php Logger: a powerful logging system developed according to PSR-3 standards.
Logging is undoubtedly the discreet hero of the world of computer security.
With [Iriven Php Logger](https://github.com/iriven/PhpLogger) you have the possibility to keep a complete record of your application users's activities .
It contributes to compliance with current security policies and regulations.


### ( Documentation En Cours )

## Features

 * Power and Simplicity
 * PSR-3 logger interface
 * Multiple log level severities
 * Log channels
 * Process ID logging
 * Custom log messages
 * Custom contextual data
 * Exception logging

## Usage:

### Installation And Initialisation

To utilize PhpLogger, first import and require Logger.php file in your project.

##### Installation
```php
require_once 'Logger.php';
```


##### Setup (example)

```php
$Config = [
            'channel'       => 'Tracking',
            'level'         => 'debug', // log severity (values: debug, info, notice, warning, error, critical, alert, emergency)
            'filename'      => 'messages',
            'directory'     => __DIR__.DIRECTORY_SEPARATOR.'PhpLogger',
            'stdout'        => false,           // display logs on screen or not (values: false, true)
            'rotate'        => true, 		//allow log rotation
            'granularity'   => 'month', 	//logs rotate frequency (values: day, week, month, year)
            'timezone'      => 'Europe/Paris',
            'type'          => 'Syslog'
        ];
```
##### Initialisation

```php
$logger = new \IrivenPHPEvents\Logger($Config);
$logger->info('Event Handler',[
                                                'Ip'=>$_SERVER['REMOTE_ADDR'],
                                                'HttpMethod'=>$_SERVER['REQUEST_METHOD'],
                                                'Url'=>$_SERVER['REQUEST_URI'],
                                                'Referer'=>$_SERVER['HTTP_REFERER']?:null
                                            ]);
```
That's it! Your application is logging!


### Log Output
Log lines have the following format:
```
YYYY-mm-dd HH:ii:ss.uuuuuu  [loglevel]  [channel]  [pid:##]  Log message content  {"Optional":"JSON Contextual Support Data"}  {"Optional":"Exception Data"}
```

Log lines are easily readable and parsable. Log lines are always on a single line. Fields are tab separated.

### Log Levels

PhpLogger has eight log level severities based on [PSR Log Levels](http://www.php-fig.org/psr/psr-3/#psrlogloglevel).

```php
$logger->debug('Detailed information about the application run.');
$logger->info('Informational messages about the application run.');
$logger->notice('Normal but significant events.');
$logger->warning('Information that something potentially bad has occured.');
$logger->error('Runtime error that should be monitored.');
$logger->critical('A service is unavailable or unresponsive.');
$logger->alert('The entire site is down.');
$logger->emergency('The Web site is on fire.');
```

By default all log levels are logged. The minimum log level can be changed in two ways:
 * Optional constructor parameter
 * Setter method at any time

```php
// Setter method (Only warning and above are logged)
$logger->setLogLevel(LogLevel::WARNING);
```

## Authors

* **Alfred TCHONDJO** - *Project Initiator* - [iriven France](https://www.facebook.com/Tchalf)

## License

This project is licensed under the GNU General Public License V3 - see the [LICENSE](LICENSE) file for details

## Donation

If this project help you reduce time to develop, you can give me a cup of coffee :)

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XDCFPNTKUC4TU)

## Disclaimer

If you use this library in your project please add a backlink to this page by this code.

```html

<a href="https://github.com/iriven/PhpLogger" target="_blank">This Project Uses Alfred's TCHONDJO  PhpLogger Library.</a>
```
## Issues Repport
Repport issues [Here](https://github.com/iriven/PhpLogger/issues)
