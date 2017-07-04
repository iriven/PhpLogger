<?php
/**
 * Created by PhpStorm.
 * User: sjhc1170
 * Date: 28/06/2017
 * Time: 14:53
 */

namespace IrivenPHPEvents;


use IrivenPHPEvents\Interfaces\LoggerInterface;
use IrivenPHPEvents\Core\LogLevel;
use IrivenPHPEvents\Core\Utils\LogMessageBuilderTrait;

class Logger implements LoggerInterface
{
    use LogMessageBuilderTrait;
    /**
     * File name and path of log file.
     * @var string
     */
    private $file;
    /**
     * Log channel--namespace for log lines.
     * Used to identify and correlate groups of similar log lines.
     * @var string
     */
    private $channel;
    /**
     * Lowest log level to log.
     * @var int
     */
    private $level;
    /**
     * Whether to log to standard out.
     * @var bool
     */
    private $stdout;
    /**
     * Current timezone.
     * @var string
     */
    private $timezone;
    /**
     * Special minimum log level which will not log any log levels.
     */
    const LOG_LEVEL_NONE = 'none';

    /**
     * PhpLogger constructor.
     * @param array $Setup
     */
    public function __construct(array $Setup=[])
    {
        $params          = $this->setup($Setup);
        $this->file      = $params['file'];
        $this->channel   = $params['channel'];
        $this->stdout    = $params['stdout'];
        $this->timezone  = $params['timezone'];
        $this->level     = $params['level'];
    }

    /**
     * Log a debug message.
     * Fine-grained informational events that are most useful to debug an application.
     *
     * @param string $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     * @return $this
     */
    public function debug($message = '', array $data = null)
    {
        if ($this->isAllowed(LogLevel::DEBUG))
            $this->log(LogLevel::DEBUG, $message, $data);
        return $this;
    }

    /**
     * Log an info message.
     * Interesting events and informational messages that highlight the progress of the application at coarse-grained level.
     *
     * @param string $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     * @return $this
     */
    public function info($message = '', array $data = null)
    {
        if ($this->isAllowed(LogLevel::INFO))
            $this->log(LogLevel::INFO, $message, $data);
        return $this;
    }

    /**
     * Log an notice message.
     * Normal but significant events.
     *
     * @param string $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     * @return $this
     */
    public function notice($message = '', array $data = null)
    {
        if ($this->isAllowed(LogLevel::NOTICE))
            $this->log(LogLevel::NOTICE, $message, $data);
        return $this;
    }

    /**
     * Log a warning message.
     * Exceptional occurrences that are not errors--undesirable things that are not necessarily wrong.
     * Potentially harmful situations which still allow the application to continue running.
     *
     * @param string $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     * @return $this
     */
    public function warning($message = '', array $data = null)
    {
        if ($this->isAllowed(LogLevel::WARNING))
            $this->log(LogLevel::WARNING, $message, $data);
        return $this;
    }

    /**
     * Log an error message.
     * Error events that might still allow the application to continue running.
     * Runtime errors that do not require immediate action but should typically be logged and monitored.
     *
     * @param string $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     * @return $this
     */
    public function error($message = '', array $data = null)
    {
        if ($this->isAllowed(LogLevel::ERROR))
            $this->log(LogLevel::ERROR, $message, $data);
        return $this;
    }

    /**
     * Log a critical condition.
     * Application components being unavailable, unexpected exceptions, etc.
     *
     * @param string $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     * @return $this
     */
    public function critical($message = '', array $data = null)
    {
        if ($this->isAllowed(LogLevel::CRITICAL))
            $this->log(LogLevel::CRITICAL, $message, $data);
        return $this;
    }

    /**
     * Log an alert.
     * This should trigger an email or SMS alert and wake you up.
     * Example: Entire site down, database unavailable, etc.
     *
     * @param string $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     * @return $this
     */
    public function alert($message = '', array $data = null)
    {
        if ($this->isAllowed(LogLevel::ALERT))
            $this->log(LogLevel::ALERT, $message, $data);
        return $this;
    }

    /**
     * Log an emergency.
     * System is unsable.
     * This should trigger an email or SMS alert and wake you up.
     *
     * @param string $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     * @return $this
     */
    public function emergency($message = '', array $data = null)
    {
        if ($this->isAllowed(LogLevel::EMERGENCY))
            $this->log(LogLevel::EMERGENCY, $message, $data);
        return $this;
    }

    /**
     * Log a message.
     * Generic log routine that all severity levels use to log an event.
     *
     * @param string $level the log priority.
     * @param string $message Content of log event.
     * @param array  $data Potentially multidimensional associative array of support data that goes with the log event.
     * @throws \Exception when log file cannot be opened for writing.
     * @return $this
     */
    public function log($level, $message = '', array $data = null)
    {
        try {
            $pid                    = getmypid();
            list($exception, $data) = $this->handleException($data);
            $data                   = $data ? json_encode($data, \JSON_UNESCAPED_SLASHES) : '{}';
            $logContent             = $this->formatMessage($level, $pid, $message, $data, $exception);
            file_put_contents($this->file, $logContent, FILE_APPEND | LOCK_EX);
            if ($this->stdout) print($logContent);
        } catch (\Throwable $e) {
            throw new \RuntimeException("Could not open log file {$this->file} for writing to SimpleLog channel {$this->channel}!", 0, $e);
        }
        return $this;
    }
}
