<?php
class GluePostFixture extends CakeTestFixture {
    var $name = 'GluePost';

    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
        'glue_user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20),
        'glue_header_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 20),
        'title' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'body' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
        'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
    );

    var $records = array(
                         array(
                               'glue_user_id' => 1,
                               'glue_header_id' => 1,
                               'title' => 'Title',
                               'body' => 'Glue.Glue Test',
                               'created' => '2011-08-23 17:44:58',
                               'modified' => '2011-08-23 12:05:02'
                               ),
                         array(
                               'glue_user_id' => 1,
                               'glue_header_id' => 1,
                               'title' => 'Title2',
                               'body' => 'Glue.Glue Test2',
                               'created' => '2011-08-25 17:44:58',
                               'modified' => '2011-08-23 12:05:02'
                               ),
                         array(
                               'glue_user_id' => 401,
                               'glue_header_id' => 401,
                               'title' => 'No Glue',
                               'body' => 'Glue.Glue Test',
                               'created' => '2011-08-23 17:44:58',
                               'modified' => '2011-08-23 12:05:02'
                               ),
                         );
}