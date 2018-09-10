# Symfony 4 REST API example/boilerplate/demo (no authentication)

This is a boilerplate implementation of Symfony 4 REST API (without authentication). 
It is created with best REST API practices in mind (except authentification). 
REST API interaction more or less follows guidline/summary provided by this excellent 
article: https://blog.mwaysolutions.com/2014/06/05/10-best-practices-for-better-restful-api/

Regarding project itself. Several ideas were in mind, like thin-controller and TDD approach. SOLID principles, speaking names and other good design 
practices were also kept in mind (thankfully Symfony itself is a good primer of this). 
Most business logic is moved from controllers to corresponding services, 
which in turn use other services and Doctrine repositories to execute various DB queries.

That said, there is always room for improvement, so use it as a starting point and modify
according to your requirements. P.S. if you are looking for JWT token based REST API, 
please look at my other Symfony 4 REST API JWT based project located here (which is very
similar to the current one): https://github.com/vgrankin/symfony_4_jwt_restapi_demo


## What this REST API is doing?

This is a simple listing managing app, which is implemented as REST API which creates various 
endpoints to allow CRUD operations on listings. This is a simple project which is used to demonstrate 
how to create and structure REST API services using Symfony 4. 
See "Usage/testing" section.

## Technical details / Requirements:
- Current project is built using Symfony 4.1 framework
- It is based on microservice/API symfony project (symfony/skeleton)
	- https://symfony.com/download
- PHPUnit is used for tests	
	* Note: it is better to run symfony's built-in PHPUnit, not the global one you have on your system, 
			  because different versions of PHPUnit expect different syntax. Tests for this project 
			  were built using preinstalled PHPUnit which comes with Symfony (located in bin folder). 
			  You can run all tests by running this command from project directory: 
			  ./bin/phpunit (php bin/phpunit on Windows). 
			  * Read more here: https://symfony.com/doc/current/testing.html			 
- PHP 7.2.9 is used so you will need something similar available on your system (there are many options to install it: Docker/XAMPP/standalone version etc.)
- MariaDB (MySQL) is required (10.1.31-MariaDB was used during development)
- Guzzle composer package is used to test REST API endpoints


## Installation:
	
    - git clone https://github.com/vgrankin/symfony_4_customer_service_rest_api
    
    - go to project directory and run: composer install
    
    * at this point make sure MySQL is installed and is running	
    - open .env filde in project directory (copy .env.dist and create .env file out of it (in same location) if not exists)
    
    - configure DATATABSE_URL
        - This is example of how my .env config entry looks: DATABASE_URL=mysql://root:@127.0.0.1:3306/customer_service # user "root", no db pass
    * more infos:
        - https://symfony.com/doc/current/configuration.html#the-env-file-environment-variables
        - https://symfony.com/doc/current/doctrine.html#configuring-the-database
        - https://symfony.com/doc/current/configuration/environments.html
        
    - go to project directory and run following commands to create database using Doctrine:
        - php bin/console doctrine:database:create (to create database called `customer_service`, it will figure out db name based on your DATABASE_URL config)		
        - php bin/console doctrine:schema:update --force (executes queries to create/update all Entities in the database in accordance to latest code)
        
        * example of command execution on Windows machine: C:\Users\admin\PhpProjects\symfony_restapi>php bin/console doctrine:database:create
        * you can preview SQL queries Doctrine will run (without actually executing queries). To do so, run: php bin/console doctrine:schema:update --dump-sql
        * if you need to start from scratch, you can drop database like this: php bin/console doctrine:database:drop --force
        * Run php bin/console list doctrine to see a full list of commands available.
        
    - In order to run PHPUnit tests yourself, you will need to create local version of phpunit.xml:
        - for that, just copy phpunit.xml.dist and rename it to phpunit.xml
        - then add record to phpunit.xml which will tell Symfony which database server (and DB) you want to use specifically for tests:
            * add it right below where it says: "<!-- define your env variables for the test env here -->"
            <env name="DATABASE_URL" value="mysql://root:@127.0.0.1:3306/customer_service" /><!-- this is how my config looks like -->
            * read more here: https://symfony.com/doc/4.0/testing/database.html
    - If you want to try this API without manually inserting new records, here are some example records to start with:
    
        DELETE FROM `listing`;
        
        DELETE FROM `city`;
        INSERT INTO `city` (`id`, `name`) VALUES (1, 'Berlin');
        INSERT INTO `city` (`id`, `name`) VALUES (2, 'Porta Westfalica');
        INSERT INTO `city` (`id`, `name`) VALUES (3, 'Lommatzsch');
        INSERT INTO `city` (`id`, `name`) VALUES (4, 'Hamburg');
        INSERT INTO `city` (`id`, `name`) VALUES (5, 'Bülzig');
        INSERT INTO `city` (`id`, `name`) VALUES (6, 'Diesbar-Seußlitz');
        
        DELETE FROM `period`;
        INSERT INTO `period` (`id`, `name`, `date_addon`) VALUES (1, 'Plus 3 days', 'P3D');
        INSERT INTO `period` (`id`, `name`, `date_addon`) VALUES (2, 'Plus 20 days', 'P40D');
        INSERT INTO `period` (`id`, `name`, `date_addon`) VALUES (3, 'Plus 60 days', 'P60D');
        
        DELETE FROM `section`;
        INSERT INTO `section` (`id`, `name`) VALUES (1, 'Sonstige Umzugsleistungen');
        INSERT INTO `section` (`id`, `name`) VALUES (2, 'Abtransport, Entsorgung und Entrümpelung');
        INSERT INTO `section` (`id`, `name`) VALUES (3, 'Fensterreinigung');
        INSERT INTO `section` (`id`, `name`) VALUES (4, 'Holzdielen schleifen');
        INSERT INTO `section` (`id`, `name`) VALUES (5, 'Kellersanierung');
        
        DELETE FROM `user`;
        INSERT INTO `user` (`id`, `password`) VALUES ('test1@restapier.com', 
            '$2y$10$dK0QHbmFiBaOKDx0sjNFAemqBhSjdjifTg6HZE3P6mQ9hIbAPraey');
        INSERT INTO `user` (`id`, `password`) VALUES ('test2@restapier.com', 
            '$2y$10$dK0QHbmFiBaOKDx0sjNFAemqBhSjdjifTg6HZE3P6mQ9hIbAPraey');
    
        * These records are required in order to create listing using REST API. This is because
          listing consists of several fields, including id of the city where listing is published,
          period which will be used to decided when listing will expire (in the examples above - 
          in 3 days, in 40 days and in 60 days from publishing date (P3D, P40D and P60D are 
          PHP date interval formats. More information here: 
          http://www.php.net/manual/de/dateinterval.format.php).
          

     
