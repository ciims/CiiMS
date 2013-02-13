<?php

class m120101_000000_base extends CDbMigration
{
    public function safeUp()
    {
        // Try to get the table names, if we get something back, do not run this migration
        try {
            $test = Yii::app()->db->schema->getTables();
            if (count($test) <= 1)
                throw new Exception('CiiMS doesn\'t exist. Applying base migration');
            return true;
        } catch (Exception $e) {}
        // Otherwise, run the install migration
        
        // Categories
        $this->execute("CREATE TABLE IF NOT EXISTS `categories` (
                      `id` int(15) NOT NULL AUTO_INCREMENT,
                      `parent_id` int(11) NOT NULL,
                      `name` varchar(150) NOT NULL,
                      `slug` varchar(150) NOT NULL,
                      `created` datetime NOT NULL,
                      `updated` datetime NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `parent_id` (`parent_id`)
                    ) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
                    
        // CategoriesMetadata
        $this->execute("CREATE TABLE IF NOT EXISTS `categories_metadata` (
                  `category_id` int(11) NOT NULL,
                  `key` varchar(50) NOT NULL,
                  `value` varchar(50) NOT NULL,
                  `created` datetime NOT NULL,
                  `updated` datetime NOT NULL,
                  UNIQUE KEY `category_id_2` (`category_id`,`key`),
                  KEY `category_id` (`category_id`)
                ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
                
        // Comments
        $this->execute("CREATE TABLE IF NOT EXISTS `comments` (
                  `id` int(15) NOT NULL AUTO_INCREMENT,
                  `content_id` int(15) NOT NULL,
                  `user_id` int(15) NOT NULL,
                  `parent_id` int(15) NOT NULL,
                  `comment` text NOT NULL,
                  `approved` int(15) NOT NULL,
                  `created` datetime NOT NULL,
                  `updated` datetime NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `content_id` (`content_id`),
                  KEY `user_id` (`user_id`),
                  KEY `parent_id` (`parent_id`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
                
        // CommentMetadata
        $this->execute("CREATE TABLE IF NOT EXISTS `comment_metadata` (
                  `comment_id` int(15) NOT NULL,
                  `key` varchar(50) NOT NULL,
                  `value` varchar(50) NOT NULL,
                  `created` datetime NOT NULL,
                  `updated` datetime NOT NULL,
                  UNIQUE KEY `comment_id_2` (`comment_id`,`key`),
                  KEY `comment_id` (`comment_id`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
                
        // Configuration
        $this->execute("CREATE TABLE IF NOT EXISTS `configuration` (
                  `key` varchar(64) NOT NULL,
                  `value` varchar(255) NOT NULL,
                  `created` datetime NOT NULL,
                  `updated` datetime NOT NULL,
                  PRIMARY KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci");
                
        // Content
        $this->execute("CREATE TABLE IF NOT EXISTS `content` (
                  `id` int(15) NOT NULL AUTO_INCREMENT,
                  `vid` int(15) NOT NULL,
                  `author_id` int(15) NOT NULL,
                  `title` varchar(150) NOT NULL,
                  `content` text NOT NULL,
                  `extract` mediumtext NOT NULL,
                  `status` int(11) NOT NULL,
                  `commentable` int(15) NOT NULL,
                  `parent_id` int(15) NOT NULL,
                  `category_id` int(15) NOT NULL,
                  `type_id` int(15) NOT NULL,
                  `password` varchar(150) NOT NULL,
                  `comment_count` int(15) NOT NULL DEFAULT '0',
                  `slug` varchar(150) NOT NULL,
                  `created` datetime NOT NULL,
                  `updated` datetime NOT NULL,
                  PRIMARY KEY (`id`,`vid`),
                  KEY `author_id` (`author_id`),
                  KEY `parent_id` (`parent_id`),
                  KEY `category_id` (`category_id`),
                  KEY `slug` (`slug`)
                ) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
        
        // ContentMetadata
        $this->execute("CREATE TABLE IF NOT EXISTS `content_metadata` (
              `content_id` int(15) NOT NULL,
              `key` varchar(50) NOT NULL,
              `value` text NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL,
              UNIQUE KEY `content_id_2` (`content_id`,`key`),
              KEY `content_id` (`content_id`)
            ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        
        // Groups
        $this->execute("CREATE TABLE IF NOT EXISTS `groups` (
              `id` int(15) NOT NULL AUTO_INCREMENT,
              `name` varchar(150) NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci; AUTO_INCREMENT=1 ;");
        
        // PublicTags
        $this->execute("CREATE TABLE IF NOT EXISTS `tags` (
              `id` int(15) NOT NULL AUTO_INCREMENT,
              `user_id` int(15) NOT NULL,
              `tag` varchar(64) NOT NULL,
              `approved` int(15) NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
        
        // Users
        $this->execute("CREATE TABLE IF NOT EXISTS `users` (
              `id` int(15) NOT NULL AUTO_INCREMENT,
              `email` varchar(255) NOT NULL,
              `password` varchar(64) NOT NULL,
              `firstName` varchar(255) NOT NULL,
              `lastName` varchar(255) NOT NULL,
              `displayName` varchar(255) NOT NULL,
              `user_role` int(15) NOT NULL,
              `status` int(15) NOT NULL,
              `activation_key` varchar(64) NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `email` (`email`),
              KEY `user_role` (`user_role`)
            ) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
        
        // user_groups
        $this->execute("CREATE TABLE IF NOT EXISTS `user_groups` (
              `id` int(15) NOT NULL AUTO_INCREMENT,
              `group_id` int(15) NOT NULL,
              `user_id` int(15) NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
        
        // UserMetadata
        $this->execute("CREATE TABLE IF NOT EXISTS `user_metadata` (
              `user_id` int(15) NOT NULL,
              `key` varchar(50) NOT NULL,
              `value` varchar(50) NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL,
              UNIQUE KEY `user_id_2` (`user_id`,`key`),
              KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci");
        
        // UserRoles
        $this->execute("CREATE TABLE IF NOT EXISTS `user_roles` (
              `id` int(15) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
            
        
        // Alter Commands
        $this->execute("ALTER TABLE `categories` ADD FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        $this->execute("ALTER TABLE `categories_metadata` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
        $this->execute("ALTER TABLE `comments` ADD  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION, ADD FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
        $this->execute("ALTER TABLE `comment_metadata` ADD FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
        $this->execute("ALTER TABLE `tags` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        $this->execute("ALTER TABLE `users` ADD FOREIGN KEY (`user_role`) REFERENCES `user_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        $this->execute("ALTER TABLE `user_metadata` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
        
        $this->execute("ALTER TABLE  `content` ADD FOREIGN KEY (  `author_id` ) REFERENCES  `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;");
        $this->execute("ALTER TABLE  `content` ADD FOREIGN KEY (  `category_id` ) REFERENCES  `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;");
        $this->execute("ALTER TABLE `content_metadata` ADD FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
        
        
        // Inserts
        $this->execute("INSERT INTO `user_roles` (`id`, `name`, `created`, `updated`) VALUES
            (1, 'User', NOW(),NOW()),
            (2, 'Pending', NOW(), NOW()),
            (3, 'Suspended', NOW(), NOW()),
            (4, 'Moderator', NOW(), NOW()),
            (5, 'Administrator', NOW(), NOW());");
        $this->execute("INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `created`, `updated`) VALUES (1, 1, 'Uncategorized', 'uncategorized', NOW(), NOW());");
        
        return true;
    }
    
    public function safeDown()
    {
        echo "Base installer does not support migrating down\n";
        return false;
    }
}