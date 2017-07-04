<?php
/**
 * Created by PhpStorm.
 * User: sjhc1170
 * Date: 29/06/2017
 * Time: 11:06
 */

namespace IrivenPHPEvents\Core\Utils;

use IrivenPHPEvents\Core\LogLevel;

trait LogSetupManagerTrait
{
    private $Setup = [];

    /**
     * @param array $config
     * @return $this
     */
    private function setup($config=[])
    {
        $default = [
            'filename'      => 'messages',
            'extension'     => '.log',
            'channel'       => 'Tracking',
            'level'         => LogLevel::DEBUG,
            'directory'     => (pathinfo(ini_get('error_log'),PATHINFO_DIRNAME)?:__DIR__).DIRECTORY_SEPARATOR.'PhpLogger',
            'stdout'        => false,
            'rotate'        => true,
            'granularity'   => 'month',
            'timezone'      => 'Europe/Paris',
            'type'          => 'Events'
        ];
        $AcceptedOptions = array_diff_key(array_keys($default), ['extension'=>false]);
        $AcceptedGranularities =['day','week','month','year'];
        if(!is_array($config)) $config =[];
        if(count($config) !== count($config, COUNT_RECURSIVE))
            exit('Invalid Data passed to $config in: '.get_class($this));
        $config = array_change_key_case($config,CASE_LOWER);
        foreach (array_keys($config) AS $key):
            if(!in_array($key,$AcceptedOptions,true))
                unset($config[$key]);
        endforeach;
        $Setup = [
            'channel'       => isset($config['channel'])? $config['channel']:null,
            'directory'     => isset($config['directory'])? pathinfo($config['directory'],PATHINFO_DIRNAME):null,
            'filename'      => isset($config['filename'])? pathinfo($config['filename'],PATHINFO_FILENAME):null,
            'granularity'   => (isset($config['granularity']) and in_array($config['granularity'], $AcceptedGranularities ,false))? strtolower($config['granularity']):null,
            'level'         => (isset($config['level']) and in_array($config['level'], array_keys($this->LEVELS),false))? trim(strtolower($config['level'])):null,
            'rotate'        => (isset($config['rotate']) and is_bool($config['rotate']))? $config['rotate']:null,
            'stdout'        => (isset($config['stdout']) and is_bool($config['stdout']))? $config['stdout']:null,
            'timezone'      => (isset($config['timezone']) and in_array($config['timezone'], \DateTimeZone::listIdentifiers()))? $config['timezone']:null,
            'type'          => isset($config['type'])? preg_replace('/\s+/', '', $config['type']):null
        ];
        foreach ($Setup AS $key=>$value):
            if(is_null($value)) unset($Setup[$key]);
            endforeach;
        $Setup =  array_merge($default,$Setup);
        if(!strpos($Setup['directory'],$Setup['type']))
        {
            $this->createHtaccess($Setup['directory']);
            $Setup['directory'] .= DIRECTORY_SEPARATOR . $Setup['type'];
        }
        $logDate = $this->getDate($Setup['timezone']);
        if($Setup['rotate'])
        {
            $SubDirectory = null;
            switch($Setup['granularity'])
            {
                case ($Setup['granularity'] === 'day'):
                    $SubDirectory .= $logDate->format('Ymd');
                    break;
                case ($Setup['granularity'] === 'week'):
                    $SubDirectory .= $logDate->format('Y-m').'_week_'.$logDate->format('W');
                    break;
                case ($Setup['granularity'] === 'year'):
                    $SubDirectory .= $logDate->format('Y');
                    break;
                default:
                $SubDirectory .= $logDate->format('Y-m');
                    break;
            }
            if(!strpos($Setup['directory'],$SubDirectory))
                $Setup['directory'] .= DIRECTORY_SEPARATOR.$SubDirectory;
        }
        if(!is_dir($Setup['directory']))
            mkdir($Setup['directory'],0755,true);
        $Setup['file'] = $Setup['directory'].DIRECTORY_SEPARATOR;
        if($Setup['rotate'])
            $Setup['granularity'] === 'day' OR  $Setup['file'] .= $logDate->format('Ymd').'_';
        $Setup['file'] .= $Setup['filename'].$Setup['extension'];
        $Setup['level'] = $this->LEVELS[$Setup['level']];
        return $this->Setup = $Setup;
    }
    /**
     * @param null $tz
     * @return \DateTime
     */
    private function getDate($tz=null)
    {
        $tz OR $tz = $this->timezone?:'Europe/Paris';
        $date = new \DateTime('now', new \DateTimeZone($tz));
        $date->setTimezone(new \DateTimeZone($tz));
        return $date;
    }
    /**
     * Set the lowest log level to log.
     *
     * @param string $level
     * @return $this
     */
    public function setLevel($level)
    {
        $params = $this->setup(array_merge($this->Setup,['level'=>$level]));
        $this->level = $params['level'];
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type){
        $params = $this->setup(array_merge($this->Setup,['type'=>$type]));
        $this->level = $params['type'];
        return $this;
    }
    /**
     * Set the log channel which identifies the log line.
     *
     * @param string $channel
     * @return $this
     */
    public function setChannel($channel)
    {

        $params = $this->setup(array_merge($this->Setup,['channel'=>$channel]));
        $this->channel = $params['channel'];
        return $this;
    }

    /**
     * Set the standard out option on or off.
     * If set to true, log lines will also be printed to standard out.
     *
     * @param bool $stdout
     * @return $this
     */
    public function setOutput($stdout)
    {
        $params = $this->setup(array_merge($this->Setup,['stdout'=>$stdout]));
        $this->stdout = $params['stdout'];
        return $this;
    }
    /**
     * Set the default timezone
     *
     * @param string $timezone
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $params = $this->setup(array_merge($this->Setup,['timezone'=>$timezone]));
        $this->timezone = $params['timezone'];
        return $this;
    }
    /**
     * Determine if the logger should log at a certain log level.
     *
     * @param  string $level
     * @return bool   True if we log at this level; false otherwise.
     */
    private function isAllowed($level)
    {
        return $this->LEVELS[$level] >= $this->level;
    }
    /**
     * @param string $path
     * @return bool
     */
    private function createHtaccess($path = '')
    {

        $location = rtrim(preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, trim($path)), DIRECTORY_SEPARATOR);
        $location .= DIRECTORY_SEPARATOR.'.htaccess';
        if(!file_exists($location)):
            try
            {
                $content  = '<IfModule mod_authz_core.c>'.PHP_EOL;
                $content .= 'Require local'.PHP_EOL;
                $content .= '</IfModule>'.PHP_EOL;
                $content .= '<IfModule !mod_authz_core.c>'.PHP_EOL;
                $content .= 'order deny, allow'.PHP_EOL;
                $content .= 'deny from all'.PHP_EOL;
                $content .= 'allow from 127.0.0.1'.PHP_EOL;
                $content .= 'allow from ::1'.PHP_EOL;
                $content .= '</IfModule>'.PHP_EOL;
                if(!file_put_contents($location, $content, LOCK_EX))
                    throw new \Exception('Can\'t create .htaccess',97);
                return true;
            }
            catch(\Exception $e){ echo $e->getMessage();}
        endif;
        return false;
    }
}
