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
          
## Implementation details:

- No external libraries are used for this REST API. 
Everything is intentionally coded from scratch 
(as a demo project to explicitly demonstrate REST API application design) 
- In terms of workflow the following interaction is used: to get the job done for any 
given request usually something like this is happening: Controller uses Service 
(which uses Service) which uses Repository which uses Entity. This way we have a good 
thin controller along with practices like Separation of Concerns, Single responsibility 
principle etc.
- App\EventSubscriber\ExceptionSubscriber is used to process all Symfony-thrown exceptions 
and turn them into nice REST-API compatible JSON response (instead of HTML error pages 
shown by default in case of exception like 404 (Not Found) or 500 (Internal Server Error))
- App\Service\ResponseErrorDecoratorService is a simple helper to prepare error responses 
and to make this process consistent along the framework. It is used every time error 
response (such as status 400 or 404) is returned.
- HTTP status codes and REST API url structure is implemented in a way similar to 
described here (feel free to reshape it how you wish): 
https://blog.mwaysolutions.com/2014/06/05/10-best-practices-for-better-restful-api/
- No authentication (like JWT) is used. Application is NOT secured) 
- All application code is in /src folder
- All tests are located in /tests folder
- In most cases the following test-case naming convention is used: MethodUnderTest____Scenario____Behavior()
     
## Usage/testing:

    First of all, start your MySQL server and PHP server. Here is example of how to start local PHP server on Windows 10:
    C:\Users\admin\PhpProjects\symfony_restapi>php -S 127.0.0.1:8000 -t public
    * After that http://localhost:8000 should be up and running
    
    * If you use docker, make sure PHP and MySQL (with required database) containers are up and running

You can simply look at and run PHPUnit tests (look at tests folder where all test files are located) 
to execute all possible REST API endpoints. (To run all tests execute this command from project's root folder: 
"php bin/phpunit"), but if you want, you can also use tools like POSTMAN to manually access REST API endpoints. 
Here is how to test all currently available API endpoints:
    
We can use POSTMAN to access all endpoints:

    * Here is a table of possible operations:
    
    --------------------------- --------  -------------------- 
     Action                      Method    Path                
    --------------------------- --------  --------------------  
     Create listing              POST      /api/listings       
     Get listing                 GET       /api/listings/{id}  
     Get listings (filtered)     GET       /api/listings       
     Update listing              PUT       /api/listings/{id}  
     Delete listing              DELETE    /api/listings/{id}
    --------------------------- --------  --------------------     
    
    * First of all, clear DB and install some sample data using SQL queries provided above.
        
    - Here is how to access REST API endpoint to create listing:
    
    method: POST
    url: http://localhost:8000/api/listings
    Body (select raw) and add this line: 
    
    {"section_id":1,"title":"Test listing 1","zip_code":"10115","city_id":1,"description":"Test listing 1 description Test listing 1 description","period_id":1,"user_id":"test1@restapier.com"}        
    
    Response should look similar to this:
    
    {
        "data": {
            "id": 326,
            "section_id": 1,
            "title": "Test listing 1",
            "zip_code": "10115",
            "city_id": 1,
            "description": "Test listing 1 description Test listing 1 description",
            "publication_date": "2018-09-10 14:29:33",
            "expiration_date": "2018-09-13 14:29:33",
            "user_id": "test1@restapier.com"
        }
    }        
    
    - Update attributes of a listing. Let's say we want to change `city` and `title` of some particular listing:
    
    method: PUT
    url: http://localhost:8000/api/listings/{id} (where {id} is id of existing listing you want to modify, for example http://localhost:8000/api/listings/326)
    Body (select raw) and add this line: 
    {"title": "New title 1", "city_id": 2}        	
    
    Response should look similar to this:
    
    {
        "data": {
            "id": 326,
            "section_id": 1,
            "title": "New title 1",
            "zip_code": "10115",
            "city_id": 2,
            "description": "Test listing 1 description Test listing 1 description",
            "publication_date": "2018-09-10 14:29:33",
            "expiration_date": "2018-09-13 14:29:33",
            "user_id": "test1@restapier.com"
        }
    }
                	
    - Get listings. Let's say we want to get listings for some particular section and city. You can do this using
      filter:                                                       
    
    method: GET
    url: http://localhost:8000/api/listings?section_id=1&city_id=1&days_back=30&excluded_user_id=1 
        (where 
            - section_id is id of a category you want to filter by
            - city_id is id of a city to filter by
            - days_back is used to get listings published up to 30 days ago
            - excluded_user_id if listing belongs to given excluded_user_id, it will be filtered out
            * all filter keys are optional (you can use none, one or all of them if needed)
            )
    Body: none (this is a GET request, so we pass params via query string)     
    
    Response should look similar to this:
    
    {
        "data": {
            "listings": [
                {
                    "id": 326,
                    "section_id": 1,
                    "title": "New title 1",
                    "zip_code": "10115",
                    "city_id": 2,
                    "description": "Test listing 1 description Test listing 1 description",
                    "publication_date": "2018-09-10 14:29:33",
                    "expiration_date": "2018-09-13 14:29:33",
                    "user_id": "test1@restapier.com"
                }
            ]
        }
    }
    
    - Delete listing:
    
    method: DELETE
    url: http://localhost:8000/api/listings/{id} (where {id} is id of existing listing you want to delete, for example http://localhost:8000/api/listings/326)	       
    
    Response HTTP status should be 204 (endpoint is successfully executed, but there is nothing to return)
    
    * Errors are also taken into account (see PHPUnit tests on which errors are addressed) and usually if there was
      an error during your request, special JSON response will be return. Here are examples:
    
    You will see this in case item is deleted already or in case of inexisting endpoint:  
    {
        "error": {
            "code": 404,
            "message": "Not Found"
        }
    }    
    
    Here is response in case you tried to filter by city_id=XXX:      
    {
        "error": {
            "code": 400,
            "message": "Unexpected city_id"
        }
    }    
    
    Here is a response in case you are trying to use inexisting section id to create new listing:    
    {
        "error": {
            "code": 400,
            "message": "Unable to find section by given section_id"
        }
    }    
    
    * There are many other errors addressed, but JSON result you get back is consistent and looks 
      like in examples just described.   

## To improve this REST API you can implement:
- pagination
- customize App\EventSubscriber to also support debug mode during development (to debug status 500 etc.) 
 (currently you need to manually go to processException() and just use "return;" on the first line of this method's body to avoid exception "prettyfying")
- SSL (https connection)
- there are many strings returned from services in case of various errors (see try/catch cases in ListingService.php for example). It will be probably better to convert these to exceptions instead.