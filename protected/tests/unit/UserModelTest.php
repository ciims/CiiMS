<?php


class UserModelTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeDev
     */
    protected $dev;

    public function testCreate()
    {
        $model = new Users;
        $model->attributes = array(
            'email' => 'example@ciims.io',
            'password' => 'example_password',
            'username' => 'example_user',
            'user_role' => '9',
            'status' => '1',
        );

        // Verify that we can save a new record
        $this->assertTrue($model->save());

        // Verify that bcrypt password validation passes
        $this->assertTrue(password_verify('example_password', $model->password));
    }

    public function testUpdate()
    {
        $model = Users::model()->findByPk(1);

        // Verify the model isn't null
        $this->assertTrue($model !== NULL);

        $model->password = 'example_password2';

         // Verify that we can save a new record
        $this->assertTrue($model->save());

        // Verify that bcrypt password validation passes
        $this->assertTrue(password_verify('example_password2', $model->password));
    }

    public function testDelete()
    {
        $model = Users::model()->findByPk(1);

        // Verify the model isn't null
        $this->assertTrue($model !== NULL);

        $this->assertTrue($model->delete());
    }
}