<?php
class GluePostHasOneFixture extends CakeTestFixture {
    var $name = 'GluePostHasOne';

    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
        'glue_post_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20),
        'comment' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
        'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
    );

    var $records = array(
                         array(
                               'id' => 1,
                               'glue_post_id' => 1,
                               'comment' => 'HasOne Comment',
                               'created' => '2011-08-23 17:44:58',
                               'modified' => '2011-08-23 12:05:02'
                               ),
                         array(
                               'id' => 2,
                               'glue_post_id' => 2,
                               'comment' => 'HasOne Comment',
                               'created' => '2011-08-23 17:44:58',
                               'modified' => '2011-08-23 12:05:02'
                               ),
                         );
}