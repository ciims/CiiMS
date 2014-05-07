<?php

use Codeception\Util\Stub;

/**
 * Verifies that the UserRoles mapping is working correctly
 */
class RolesTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeDev
    */
    protected $codeDev;

    /**
     * @var UserRoles
     */
    protected $model;

    protected function _before()
    {
        $this->model = UserRoles::model();
        return parent::_before();
    }

    protected function _after()
    {
    }

    public function testManage() 
    {
        $this->assertTrue($this->model->hasPermission('manage', 9));
        $this->assertFalse($this->model->hasPermission('manage', 8));
        $this->assertFalse($this->model->hasPermission('manage', 7));
        $this->assertFalse($this->model->hasPermission('manage', 5));
        $this->assertFalse($this->model->hasPermission('manage', 3));
        $this->assertFalse($this->model->hasPermission('manage', 2));
        $this->assertFalse($this->model->hasPermission('manage', 1));
        $this->assertFalse($this->model->hasPermission('manage', NULL));
    }

    public function testPublishOther() 
    {
        $this->assertTrue($this->model->hasPermission('publishOther', 9));
        $this->assertTrue($this->model->hasPermission('publishOther', 8));
        $this->assertFalse($this->model->hasPermission('publishOther', 7));
        $this->assertFalse($this->model->hasPermission('publishOther', 5));
        $this->assertFalse($this->model->hasPermission('publishOther', 3));
        $this->assertFalse($this->model->hasPermission('publishOther', 2));
        $this->assertFalse($this->model->hasPermission('publishOther', 1));
        $this->assertFalse($this->model->hasPermission('publishOther', NULL));
    }

    public function testPublish() 
    {
        $this->assertTrue($this->model->hasPermission('publish', 9));
        $this->assertTrue($this->model->hasPermission('publish', 8));
        $this->assertTrue($this->model->hasPermission('publish', 7));
        $this->assertFalse($this->model->hasPermission('publish', 5));
        $this->assertFalse($this->model->hasPermission('publish', 3));
        $this->assertFalse($this->model->hasPermission('publish', 2));
        $this->assertFalse($this->model->hasPermission('publish', 1));
        $this->assertFalse($this->model->hasPermission('publish', NULL));
    }

    public function testDelete()
    {
        $this->assertTrue($this->model->hasPermission('delete', 9));
        $this->assertTrue($this->model->hasPermission('delete', 8));
        $this->assertTrue($this->model->hasPermission('delete', 7));
        $this->assertFalse($this->model->hasPermission('delete', 5));
        $this->assertFalse($this->model->hasPermission('delete', 3));
        $this->assertFalse($this->model->hasPermission('delete', 2));
        $this->assertFalse($this->model->hasPermission('delete', 1));
        $this->assertFalse($this->model->hasPermission('delete', NULL));
    }

    public function testModify()
    {
        return $this->testUpdate();
    }

    public function testUpdate()
    {
        $this->assertTrue($this->model->hasPermission('update', 9));
        $this->assertTrue($this->model->hasPermission('update', 8));
        $this->assertTrue($this->model->hasPermission('update', 7));
        $this->assertTrue($this->model->hasPermission('update', 5));
        $this->assertFalse($this->model->hasPermission('update', 3));
        $this->assertFalse($this->model->hasPermission('update', 2));
        $this->assertFalse($this->model->hasPermission('update', 1));
        $this->assertFalse($this->model->hasPermission('update', NULL));
    }

    public function testCreate()
    {
        $this->assertTrue($this->model->hasPermission('create', 9));
        $this->assertTrue($this->model->hasPermission('create', 8));
        $this->assertTrue($this->model->hasPermission('create', 7));
        $this->assertTrue($this->model->hasPermission('create', 5));
        $this->assertFalse($this->model->hasPermission('create', 3));
        $this->assertFalse($this->model->hasPermission('create', 2));
        $this->assertFalse($this->model->hasPermission('create', 1));
        $this->assertFalse($this->model->hasPermission('create', NULL));
    }

    public function testComment()
    {
        $this->assertTrue($this->model->hasPermission('comment', 9));
        $this->assertTrue($this->model->hasPermission('comment', 8));
        $this->assertTrue($this->model->hasPermission('comment', 7));
        $this->assertTrue($this->model->hasPermission('comment', 5));
        $this->assertFalse($this->model->hasPermission('comment', 3));
        $this->assertFalse($this->model->hasPermission('comment', 2));
        $this->assertTrue($this->model->hasPermission('comment', 1));
        $this->assertTrue($this->model->hasPermission('comment', NULL));
    }

    public function testRead()
    {
        $this->assertTrue($this->model->hasPermission('read', 9));
        $this->assertTrue($this->model->hasPermission('read', 8));
        $this->assertTrue($this->model->hasPermission('read', 7));
        $this->assertTrue($this->model->hasPermission('read', 5));
        $this->assertFalse($this->model->hasPermission('read', 3));
        $this->assertFalse($this->model->hasPermission('read', 2));
        $this->assertTrue($this->model->hasPermission('read', 1));
        $this->assertTrue($this->model->hasPermission('read', NULL));
    }

}