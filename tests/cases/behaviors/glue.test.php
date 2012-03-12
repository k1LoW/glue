<?php
App::import('Core', 'Model');
App::import('Fixture', 'GluePost');
App::import('Fixture', 'GluePostGlued');
App::import('Fixture', 'GluePostGlued2');
App::import('Fixture', 'GlueUser');

class GlueUser extends CakeTestModel{
    public $name = 'GlueUser';

    public $actsAs = array('Glue.Glue');

    public $hasMany = array(
                            'GluePost' => array(
                                                'className' => 'GluePost',
                                                'foreignKey' => 'glue_user_id',
                                                'dependent' => false,
                                                )
                            );
}

class GluePost extends CakeTestModel{

    public $name = 'GluePost';

    public $actsAs = array('Glue.Glue');

    public $belongsTo = array(
                              'GlueUser' => array(
                                                  'className' => 'GlueUser',
                                                  'foreignKey' => 'glue_user_id',
                                                  )
                              );

    public $hasOne = array(
                           'GluePostHasOne' => array(
                                                     'className' => 'GluePostHasOne',
                                                     'foreignKey' => 'glue_post_id',
                                                     'dependent' => false,
                                                     )
                           );

    public $hasGlued = array(
                             'GluePostGlued' => array('class' => 'GluePostGlued'),
                             'GluePostGlued2' => array('class' => 'GluePostGlued2'),
                             );
}

class GlueTestCase extends CakeTestCase{

    public $fixtures = array('plugin.glue.glue_user',
                             'plugin.glue.glue_post',
                             'plugin.glue.glue_post_has_one',
                             'plugin.glue.glue_post_glued',
                             'plugin.glue.glue_post_glued2');

    function startTest() {
        $this->GlueUser = ClassRegistry::init('GlueUser');
        $this->GluePost = ClassRegistry::init('GluePost');
        $this->GluePostFixture = ClassRegistry::init('GluePostFixture');
    }

    function endTest() {
        unset($this->GlueUser);
        unset($this->GluePost);
        unset($this->GluePostFixture);
    }

    /**
     * testFindGlued
     *
     * en:
     * jpn: GluePost::hasGluedに設定してあるGluePostGluedを強制的にマージする
     *      同名フィールドが存在した場合はGluePostを優先する
     *      hasGluedに設定してあるモデルに同名フィールドが存在した場合は最初に設定してあるほうを優先する
     */
    function testFindGlued(){
        $query = array();
        $query['conditions'] = array('GluePost.id' => 1);
        $result = $this->GluePost->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'glue_user_id' => 1,
                          'title' => 'Title',
                          'body' => 'Glue.Glue Test',
                          'body2' => 'Glued',
                          'body3' => 'Glued2',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02',
                          );

        $this->assertEqual($result['GluePost'], $expected);
    }

    /**
     * testFindHasOne
     *
     * en:
     * jpn: GluePost::hasOneに設定してあるデータは問題なく取得できている
     */
    function testFindHasOne(){
        $query = array();
        $query['conditions'] = array('GluePost.id' => 1);
        $result = $this->GluePost->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'glue_post_id' => 1,
                          'comment' => 'HasOne Comment',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02'
                          );

        $this->assertEqual($result['GluePostHasOne'], $expected);
    }

    /**
     * testFindAllGlued
     *
     * en:
     * jpn: GluePost::hasGluedに設定してあるGluePostGluedを強制的にマージする
     *      また、同名フィールドが存在した場合はGluePostを優先する
     */
    function testFindAllGlued(){
        $result = $this->GluePost->find('all');

        $expected = array(
                          'id' => 1,
                          'glue_user_id' => 1,
                          'title' => 'Title',
                          'body' => 'Glue.Glue Test',
                          'body2' => 'Glued',
                          'body3' => 'Glued2',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02',
                          );
        $this->assertEqual($result[0]['GluePost'], $expected);
    }

    /**
     * testFindNoGlued
     *
     * en:
     * jpn: GluePostGluedがない場合でもkeyは存在する
     */
    function testFindNoGlued(){
        $query = array();
        $query['conditions'] = array('GluePost.id' => 401);
        $result = $this->GluePost->find('first', $query);

        $expected = array(
                          'id' => 401,
                          'glue_user_id' => 401,
                          'title' => 'No Glue',
                          'body' => 'Glue.Glue Test',
                          'body2' => null,
                          'body3' => null,
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02',
                          );

        $this->assertEqual($result['GluePost'], $expected);
    }

    /**
     * testFindField
     *
     * en:
     * jpn: field指定ができる
     */
    function testFindField(){
        $query = array();
        $query['fields'] = array('GluePost.id',
                                 'GluePost.title',
                                 'GluePost.body',
                                 );
        $query['conditions'] = array('GluePost.id' => 1);
        $result = $this->GluePost->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'title' => 'Title',
                          'body' => 'Glue.Glue Test',
                          );

        $this->assertEqual($result['GluePost'], $expected);
    }

    /**
     * testFindGluedField
     *
     * en:
     * jpn: GluePost::hasGluedに設定してあるモデルのfieldを自分のfieldのように指定できる
     */
    function testFindGluedField(){
        $query = array();
        $query['fields'] = array('GluePost.id',
                                 'GluePost.body2',
                                 'GluePost.title',
                                 );
        $query['conditions'] = array('GluePost.id' => 1);
        $result = $this->GluePost->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'title' => 'Title',
                          'body2' => 'Glued',
                          );

        $this->assertEqual($result['GluePost'], $expected);
    }

    /**
     * testSaveGlued
     *
     * en:
     * jpn: GluePost::save()できる
     */
    public function testSaveGlued(){
        $data = array(
                      'glue_user_id' => 1,
                      'title' => 'Glue::save()',
                      'body' => 'Glue.Glue save test',
                      'body2' => 'Glue.Glue save test2',
                      'body3' => 'Glue.Glue save test3',
                      'created' => '2011-08-23 17:44:58',
                      'modified' => '2011-08-23 12:05:02',
                      );

        $this->GluePost->set($data);
        $result = $this->GluePost->save($data);
        $this->assertTrue($result);

        $id = $this->GluePost->getLastInsertId();
        $query = array();
        $query['conditions'] = array('GluePost.id' => $id);
        $result = $this->GluePost->find('first', $query);
        $this->assertIdentical($result['GluePost']['body'], $data['body']);
        $this->assertIdentical($result['GluePost']['body2'], $data['body2']);
        $this->assertIdentical($result['GluePost']['body3'], $data['body3']);
    }

    /**
     * testSaveGluedAndCountupGluedModel
     *
     * en:
     * jpn: 新規GluePost::save()後にGluePostGluedの数は増えている
     */
    public function testSaveGluedAndCountupGluedModel(){
        $before = $this->GluePost->GluePostGlued->find('count');

        $data = array(
                      'glue_user_id' => 1,
                      'title' => 'Glue::save()',
                      'body' => 'Glue.Glue save test',
                      'body2' => 'Glue.Glue save test2',
                      'created' => '2011-08-23 17:44:58',
                      'modified' => '2011-08-23 12:05:02',
                      );

        $this->GluePost->set($data);
        $result = $this->GluePost->save($data);
        $this->assertTrue($result);

        $after = $this->GluePost->GluePostGlued->find('count');
        $this->assertIdentical($after, $before + 1);
    }

    /**
     * testUpdateGlued
     *
     * en:
     * jpn: GluePostのデータを更新できる
     */
    public function testUpdateGlued(){
        $data = array(
                      'id' => 1,
                      'body' => 'Update',
                      'body2' => 'Update2',
                      );

        $this->GluePost->set($data);
        $result = $this->GluePost->save($data);
        $this->assertTrue($result);

        $query = array();
        $query['conditions'] = array('GluePost.id' => 1);
        $result = $this->GluePost->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'glue_user_id' => 1,
                          'title' => 'Title',
                          'body' => 'Update',
                          'body2' => 'Update2',
                          'body3' => 'Glued2',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => date('Y-m-d H:i:s'),
                          );

        $this->assertEqual($result['GluePost'], $expected);
    }

    /**
     * testUpdateGluedAndNoCountupGluedModel
     *
     * en:
     * jpn: GluePost更新後にGluePostGluedの数は増えない
     */
    public function testUpdateGluedAndNoCountupGluedModel(){
        $before = $this->GluePost->GluePostGlued->find('count');
        $data = array(
                      'id' => 1,
                      'body' => 'Update',
                      'body2' => 'Update2',
                      );

        $this->GluePost->set($data);
        $result = $this->GluePost->save($data);
        $this->assertTrue($result);

        $after = $this->GluePost->GluePostGlued->find('count');
        $this->assertIdentical($after, $before);
    }

    /**
     * testParentFindHasManySupport
     *
     * en:
     * jpn: hasManyでもGlueが発動する
     */
    public function testParentFindHasManySupport(){
        $query = array();
        $query['conditions'] = array('GlueUser.id' => 1);
        $result = $this->GlueUser->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'glue_user_id' => 1,
                          'title' => 'Title',
                          'body' => 'Glue.Glue Test',
                          'body2' => 'Glued',
                          'body3' => 'Glued2',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02',
                          );

        $this->assertEqual($result['GluePost'][0], $expected);
    }

    /**
     * testParentFindHasManySupport2
     *
     * en:
     * jpn: hasManyでもGlueが発動する
     */
    public function testParentFindHasManySupport2(){
        $query = array();
        $query['conditions'] = array('GlueUser.id' => 1);
        $result = $this->GlueUser->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'glue_user_id' => 1,
                          'title' => 'Title',
                          'body' => 'Glue.Glue Test',
                          'body2' => 'Glued',
                          'body3' => 'Glued2',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02',
                          );

        $this->assertEqual($result['GluePost'][0], $expected);
    }

    /**
     * testParentFindHasManySupport3
     *
     * en:
     * jpn: hasManyでもGlueが発動する。またrecursiveが深く付与されたデータはそのまま表示される
     */
    public function testParentFindHasManySupport3(){
        $query = array();
        $query['recursive'] = 2;
        $query['conditions'] = array('GlueUser.id' => 1);
        $result = $this->GlueUser->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'glue_user_id' => 1,
                          'title' => 'Title',
                          'body' => 'Glue.Glue Test',
                          'body2' => 'Glued',
                          'body3' => 'Glued2',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02',

                          'GluePostHasOne' => array(
                                                    'id' => 1,
                                                    'glue_post_id' => 1,
                                                    'comment' => 'HasOne Comment',
                                                    'created' => '2011-08-23 17:44:58',
                                                    'modified' => '2011-08-23 12:05:02'
                                                    ),
                          'GlueUser' => array(
                                              'id' => 1,
                                              'username' => 'k1LoW',
                                              'password' => 'password',
                                              'created' => '2011-08-23 17:44:58',
                                              'modified' => '2011-08-23 12:05:02'
                                              ),
                          );

        $this->assertEqual($result['GluePost'][0], $expected);
    }
}
