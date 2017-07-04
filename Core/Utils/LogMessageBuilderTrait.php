<?php
/**
 * Created by PhpStorm.
 * User: sjhc1170
 * Date: 29/06/2017
 * Time: 08:50
 */

namespace Events\Core\Utils;
use Events\Core\LogLevel;

trait LogMessageBuilderTrait
{
    use LogSetupManagerTrait;
    /**
     * Log fields separated by tabs to form a TSV (CSV with tabs).
     */
    private $TAB = "\t";
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
     * Log level hierachy
     */
    private $LEVELS    = [
        self::LOG_LEVEL_NONE => -1,
        LogLevel::DEBUG      => 0,
        LogLevel::INFO       => 1,
        LogLevel::NOTICE     => 2,
        LogLevel::WARNING    => 3,
        LogLevel::ERROR      => 4,
        LogLevel::CRITICAL   => 5,
        LogLevel::ALERT      => 6,
        LogLevel::EMERGENCY  => 7,
    ];
    /**
     * Format log message
     *
     * @param  string $level
     * @param  int    $pid
     * @param  string $message
     * @param  string $data
     * @param  string $exception
     * @return string
     */
    private function formatMessage($level, $pid, $message, $data, $exception)
    {
        return  '['.$this->getDate()->format('Y-m-d H:i:s.u').']' . $this->TAB .
                '['.$level.']'                                           . $this->TAB .
                '['.$this->channel.']'                                   . $this->TAB .
                '[pid:'.$pid.']'                                         . $this->TAB .
            str_replace(\PHP_EOL, '   ', trim($message))  . $this->TAB .
            str_replace(\PHP_EOL, '   ', $data)           . $this->TAB .
            str_replace(\PHP_EOL, '   ', $exception) . \PHP_EOL;
    }
    /**
     * Handle an exception in the data context array.
     * If an exception is included in the data context array, extract it.
     *
     * @param  array  $data
     *
     * @return array  [exception, data (without exception)]
     */
    private function handleException(array $data = null)
    {
        if (isset($data['exception']) && $data['exception'] instanceof \Throwable) {
            $exception      = $data['exception'];
            $exception_data = $this->buildExceptionData($exception);
            unset($data['exception']);
        } else {
            $exception_data = '{}';
        }
        return [$exception_data, $data];
    }
    /**
     * Build the exception log data.
     *
     * @param  \Throwable $e
     *
     * @return string JSON {message, code, file, line, trace}
     */
    private function buildExceptionData(\Throwable $e)
    {
        return json_encode(
            [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTrace()
            ],
            \JSON_UNESCAPED_SLASHES
        );
    }

}
