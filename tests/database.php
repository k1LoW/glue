<?php
class DATABASE_CONFIG {
    public $default = array(
                            'driver' => 'mysql',
                            'persistent' => false,
                            'host' => '0.0.0.0',
                            'login' => 'root',
                            'password' => '',
                            'database' => 'cakephp_test',
                            'prefix' => ''
                            );
    public $test = array(
                         'driver' => 'mysql',
                         'persistent' => false,
                         'host' => '0.0.0.0',
                         'login' => 'root',
                         'password' => '',
                         'database' => 'cakephp_test',
                         'prefix' => '',
                         'encoding' => 'utf8'
                         );
    public function __construct() {
        $db = (!empty($_SERVER['DB']) ? $_SERVER['DB'] : 'mysql';
        if ($db === 'pgsql') {
            $this->default = $this->test = array(
                                                 'driver' => 'postgresql',
                                                 'persistent' => false,
                                                 'host' => '127.0.0.1',
                                                 'login' => 'postgres',
                                                 'password' => '',
                                                 'database' => 'cakephp_test',
                                                 'prefix' => '',
                                                 'encoding' => 'utf8'
                                                 );
        }
    }
}
