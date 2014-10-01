<?php

class m141001_145619_init extends CDbMigration
{

	/**
	 * For data insertions, this is the current time.
	 * @var integer
	 */
	private $_moment = 0;

	public function safeUp()
	{
		$this->_moment = time();

		// Try to get the table names, if we get something back, do not run this migration
        try {
            $test = Yii::app()->db->schema->getTables();
            if (count($test) <= 1)
                throw new Exception('CiiMS doesn\'t exist. Applying base migration');
            return true;
        } catch (Exception $e) { 
          // Yii will throw an exception if Yii::app()->db->schema is undefined (which is should be if we're doing this for the first time)
          // This SHOULD throw an error if we're running this for the first time - it's the only way to check if we have a valid db or not.
        }

        // Otherwise, run the install migration
        $this->createUserTables();

        $this->createCategories();

        $this->createContent();

        $this->createConfiguration();
	}

	public function safeDown()
	{
		echo "m141001_145619_init does not support migration down.\n";
		return false;
	}

	/**
	 * Creates the tables, indexes, and relations for users
	 */
	private function createUserTables()
	{
		$this->createTable('users', array(
        	'id' 		=> 'pk',
        	'email' 	=> 'string NOT NULL',
        	'password' 	=> 'string NOT NULL',
        	'username' => 'string NOT NULL',
        	'user_role' => 'integer DEFAULT 1',
        	'status' 	=> 'integer DEFAULT 1',
        	'created' 	=> 'integer',
        	'updated' 	=> 'integer'
        ));

        $this->createTable('user_roles', array(
        	'id' 		=> 'pk',
        	'name' 		=> 'string NOT NULL',
        	'created' 	=> 'integer',
        	'updated' 	=> 'integer'
        ));

        $this->createTable('user_metadata', array(
        	'user_id' 	  => 'pk',
        	'key'		  => 'string NOT NULL',
        	'value' 	  => 'text NOT NULL',
        	'entity_type' => 'integer',
        	'created' 	  => 'integer',
        	'updated' 	  => 'integer'
        ));

        // Create the necessary indexes on the columns
        $this->createIndex('user_email', 'users', 'email', true);
		$this->createIndex('user_username', 'users', 'username', true);
        $this->createIndex('user_metadata', 'user_metadata', 'user_id, key', true);

        // Setup the foreign key constraints
        $this->addForeignKey('user_roles_relation_fk', 'user_metadata', 'user_id', 'users', 'id', 'CASCADE', 'NO ACTION');

        // Insert data into the tables
        $this->insert('user_roles', array(
        	'id'      => 1,
        	'name' 	  => 'User',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));

        $this->insert('user_roles', array(
        	'id'      => 2,
        	'name'    => 'Pending',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));

        $this->insert('user_roles', array(
        	'id'     => 3,
        	'name'    => 'Suspended',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));

        $this->insert('user_roles', array(
        	'id'      => 5,
        	'name'    => 'Collaborator',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));

        $this->insert('user_roles', array(
        	'id'      => 6,
        	'name'    => 'Author',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));

        $this->insert('user_roles', array(
        	'id'      => 7,
        	'name'    => 'User',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));

        $this->insert('user_roles', array(
        	'id'      => 8,
        	'name'    => 'Publisher',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));

        $this->insert('user_roles', array(
        	'id'      => 9,
        	'name'    => 'Administrator',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));
	}

	/**
	 * Create the categories and relations
	 */
	private function createCategories()
	{
		// Categories
		$this->createTable('categories', array(
			'id' 		=> 'pk',
			'parent_id' => 'integer DEFAULT 1',
        	'name' 		=> 'string NOT NULL',
			'slug' 	    => 'string NOT NULL',
        	'created' 	=> 'integer',
        	'updated' 	=> 'integer'
		));

		$this->createTable('categories_metadata', array(
			'category_id' => 'integer ',
			'key'		  => 'string NOT NULL',
        	'value' 	  => 'text NOT NULL',
        	'created' 	  => 'integer',
        	'updated' 	  => 'integer'
		));

		// Insert the first record into the categories table
		$this->insert('categories', array(
        	'id'      => 1,
        	'name'    => 'Uncategorized',
        	'slug'    => 'uncategorized',
        	'created' => $this->_moment,
        	'updated' => $this->_moment
        ));       	

       	$this->addForeignKey('categories_parents_fk', 'categories', 'parent_id', 'categories', 'id', 'CASCADE', 'NO ACTION');
		$this->addForeignKey('categories_metadata_fk', 'categories_metadata', 'category_id', 'categories', 'id', 'CASCADE', 'NO ACTION');
	}

	/**
	 * Creates the content, comemnts, and necessary relations
	 */
	private function createContent()
	{
		$this->createTable('content', array(
			'id'		  => 'integer',
			'vid' 		  => 'integer DEFAULT 1',
			'title'		  => 'string  NOT NULL',
			'content'     => 'text NOT NULL',
			'excerpt'     => 'text NOT NULL',
			'slug'        => 'string NOT NULL',
			'category_id' => 'integer DEFAULT 1',
			'author_id'   => 'integer DEFAULT 1',
			'type_id'     => 'integer DEFAULT 2',
			'commentable' => 'integer DEFAULT 1',
			'password' 	  => 'string DEFAULT NULL',
			'status'      => 'integer DEFAULT 0',
			'like_count'  => 'integer DEFAULT 0',
			'published'	  => 'integer',
			'created'     => 'integer',
        	'updated' 	  => 'integer'
		));

		$this->createTable('content_types', array(
			'id'          => 'pk',
			'name'		  => 'string NOT NULL',
			'created'     => 'integer',
        	'updated' 	  => 'integer'
		));

		$this->createTable('content_metadata', array(
			'content_id'  => 'integer',
			'key'		  => 'string NOT NULL',
        	'value' 	  => 'text NOT NULL',
        	'created' 	  => 'integer',
        	'updated' 	  => 'integer'
		));

		$this->createTable('comments', array(
			'id'		  => 'pk',
			'content_id'  => 'integer',
			'author_id'     => 'integer',
			'comment'     => 'integer',
			'created'     => 'integer',
        	'updated' 	  => 'integer'
		));

		$this->insert('content_types', array(
			'id'          => 1,
			'name'        => 'Static Page',
        	'created'     => $this->_moment,
        	'updated'     => $this->_moment
		));

		$this->insert('content_types', array(
			'id'          => 2,
			'name'    	  => 'Blog Post',
        	'created'     => $this->_moment,
        	'updated'     => $this->_moment
		));

		$this->addPrimaryKey('content_composite', 'content', 'id, vid');
		
		$this->createIndex('content', 'content', 'slug', true);
		$this->createIndex('content_author', 'content', 'author_id', true);
		$this->createIndex('content_category', 'content', 'category_id', true);
		$this->createIndex('content_type', 'content', 'type_id', true);
		$this->createIndex('comment_content', 'comments', 'content_id', true);
		$this->createIndex('comment_author', 'comments', 'author_id', true);

		$this->addForeignKey('content_category_fk', 'content', 'category_id', 'categories', 'id', 'CASCADE', 'NO ACTION');
		$this->addForeignKey('content_author_fk', 'content', 'author_id', 'users', 'id', 'CASCADE', 'NO ACTION');
		$this->addForeignKey('content_type_fk', 'content', 'type_id', 'content_types', 'id', 'CASCADE', 'NO ACTION');

		$this->addForeignKey('comments_content_id_fk', 'comments', 'content_id', 'content', 'id', 'CASCADE', 'NO ACTION');
		$this->addForeignKey('comments_author_fk', 'comments', 'author_id', 'users', 'id', 'CASCADE', 'NO ACTION');
	}

	/**
	 * Creates the configuration and events tables
	 */
	private function createConfiguration()
	{
		$this->createTable('configuration', array(
			'key'		  => 'string',
        	'value' 	  => 'text NOT NULL',
        	'created' 	  => 'integer',
        	'updated' 	  => 'integer'
		));

		$this->createTable('events', array(
			'id'		  => 'pk',
			'event'       => 'string NOT NULL',
			'event_data'  => 'text DEFAULT NULL',
			'uri'         => 'string DEFAULT NULL',
			'content_id'  => 'integer DEFAULT NULL',
			'created' 	  => 'integer'
		));

		$this->addPrimaryKey('configuration_pk', 'configuration', 'key');
	}
}