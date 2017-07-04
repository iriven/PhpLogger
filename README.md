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
            'filename'      => 'messages',
            'extension'     => '.log',
            'channel'       => 'Tracking',
            'level'         => 'debug',
            'directory'     => __DIR__.DIRECTORY_SEPARATOR.'PhpLogger',
            'stdout'        => false,
            'rotate'        => true, 		//allow log rotation
            'granularity'   => 'month', 	//logs rotate frequency (values: day, week, month, year)
            'timezone'      => 'Europe/Paris',
            'type'          => 'Syslog'
        ];
```
##### Initialisation

```php
$Event = new \IrivenPHPEvents\Logger($Config);
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
