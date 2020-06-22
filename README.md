# Battle Ship Test

## Tools, components used for this project

* Symfony 5.1
* FosRestBundle 3.0
* PhpUnit 7.5
* Symfony ParamConverter (FrameWorkExtraBundle)
* Symfony Workflow Component
* Symfony Serializer Component
* Symfony Forms Component
* Symfony Validation Component
* NelmioApiDocBundle  3.6
* redis for persistence
* Postman
* docker-compose
* ansible

## Documentation

* [Read the docs here](doc/DOCUMENTATION.md)
* [Check how to install here](doc/INSTALLATION.md)
* [Postman Collection here](doc/Battle_Ship_postman_collection.json)


## Links

* [Nelmio Api Doc](http://localhost:60280/api/doc) for full documentation of the models and parameter types 
* [Swagger json export](http://localhost:60280/api/doc.json) 
* [Redis Explorer](http://localhost:60236) to access the persisted battles in redis

(Please replace the port numbers in case you picked different values.) 

## Tests

Run the test suite with (docker-compose) phpunit:

    docker-compose run app bin/phpunit --testdox

