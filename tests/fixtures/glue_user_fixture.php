<?php
class GlueUserFixture extends CakeTestFixture {
    var $name = 'GlueUser';

    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
        'username' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'password' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
        'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
    );

    var $records = array(
                         array(
                               'id' => 1,
                               'username' => 'k1LoW',
                               'password' => 'password',
                               'created' => '2011-08-23 17:44:58',
                               'modified' => '2011-08-23 12:05:02'
                               ),
                         );
}