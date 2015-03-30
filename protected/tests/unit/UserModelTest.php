<?php


class UserModelTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeDev
     */
    protected $dev;

    public function testUserCreate()
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

    public function testUserUpdate()
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

    public function testUserEmailChange()
    {
        $newEmail = 'example2@ciims.io';

        $model = Users::model()->findByPk(1);
        $profileForm = new ProfileForm;

        $this->assertTrue($model !== NULL);
        $profileForm->load($user->id, true);
        $profileForm->email = $newEmail;

        // Verify that the profile form saves
        $this->assertTrue($profileForm->save());

        // Verify that the base user model didn't change
        $model = Users::model()->findByPk(1);
        $this->assertTrue($model->email == 'example.ciims.io');

        $newEmailModel = UserMetadata::model()->findByAttributes(array(
                              'user_id' => $user->id,
                              'key' => 'newEmailAddress'
                          ));

        // Verify that the new email is stored in the database
        $this->assertTrue($newEmailModel !== NULL);
        $this->assertTrue($newEmailModel->value == $newEmail);

        $key = UserMetadata::model()->findByAttributes(array(
                                        'user_id' => $user->id,
                                        'key' => 'newEmailAddressChangeKey'
                                    ));

        $this->assertTrue($key !== NULL);

        $emailChangeForm = new EmailChangeForm;
        $emailChangeForm->setUser(Users::model()->findByPk(1));
        $emailChangeForm->verificationKey = $key->value;
        $emailChangeForm->password = 'example_password';

        // Verify that the verification key works
        $this->assertTrue($emailChangeForm->validateVerificationKey());

        // Veirfy that the email address changes
        $this->assertTrue($emailChangeForm->validate());
        $this->assertTrue($emailChangeForm->save());

        // Verify that the email has changed for the model now
        $model = Users::model()->findByPk(1);

        $this->assertTrue($model->email == $newEmail);
    }

    public function testUserDelete()
    {
        $model = Users::model()->findByPk(1);

        // Verify the model isn't null
        $this->assertTrue($model !== NULL);

        $this->assertTrue($model->delete());
    }
}