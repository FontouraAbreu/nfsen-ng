<?php

class NfDump {
    private $cfg = array(
        'env' => array(),
        'options' => array(),
        'filter' => array()
    );
    private $clean = array();
    private $d;
    public static $_instance;

    function __construct() {
        $this->d = \Debug::getInstance();
        $this->clean = $this->cfg;
        $this->reset();
    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Sets an option's value
     * @param $option
     * @param $value
     */
    public function setOption($option, $value) {
        switch($option) {
            case '-M':
                $this->cfg['option'][$option] = $this->cfg['env']['profiles-data'] . DIRECTORY_SEPARATOR . $this->cfg['env']['profile'] . DIRECTORY_SEPARATOR . $value;
                break;
            default:
                $this->cfg['option'][$option] = $value;
                break;
        }
    }

    /**
     * Sets a filter's value
     * @param $filter
     * @param $value
     */
    public function setFilter($filter, $value) {
        $this->cfg['filter'][$filter] = $value;
    }

    /**
     * Executes the nfdump command, tries to throw an exception based on the return code
     * @return array
     * @throws Exception
     */
    public function execute() {
        $output = array();
        $return = "";
        $command = $this->cfg['env']['bin'] . " " . $this->flatten($this->cfg['option']) . $this->flatten($this->cfg['filter']);
        exec($command, $output, $return);

        switch($return) {
            case 127: throw new Exception("Failed to start process. Is nfdump installed?"); break;
            case 255: throw new Exception("Initialization failed."); break;
            case 254: throw new Exception("Error in filter syntax."); break;
            case 250: throw new Exception("Internal error."); break;
        }
        return $output;
    }

    /**
     * Concatenates key and value of supplied array
     * @param $array
     * @return bool|string
     */
    private function flatten($array) {
        if(!is_array($array)) return false;
        $output = "";

        foreach($array as $key => $value) {
            $output .= $key . " " . $value . " ";
        }
        return $output;
    }

    /**
     * Reset config
     */
    public function reset() {
        $this->clean['env'] = array(
            'bin' => \Config::$cfg['nfdump']['binary'],
            'profiles-data' => \Config::$cfg['nfdump']['profiles-data'],
            'profile' => \Config::$cfg['nfdump']['profile'],
        );
        $this->cfg = $this->clean;
    }
}