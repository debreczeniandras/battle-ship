# Documentation

## Tools, libraries, components, features used for this project

* Symfony 5.1
* FosRestBundle 3.0
* Symfony ParamConverter (FrameWorkExtraBundle)
* Symfony Workflow Component
* Symfony Serializer Component
* Symfony Forms Component
* NelmioApiDocBundle  3.6
* redis for persistance
* Postman
* docker-compose
* ansible

## Usage of the API

### Set up and prepare ships

POST /battles

    {
        "width": 8,
        "height": 8
    }

### Set up players

PUT /battles/:battleId

     [
       {
         "id": "A",
         "type": 0,
         "grid": {
           "ships": [
             {
               "id": "carrier",
               "start": {
                 "x": 2,
                 "y": "B"
               },
               "end": {
                 "x": 2,
                 "y": "F"
               }
             }
           ]
         }
       },
       {
         "id": "B",
         "type": 1
       }
     ] 

Computer Opponent is marked with type: 1, Regular user type: 0

### Shoot

POST /battles/:battleId/players/:playerId/shots

Regular user needs to send coordinates

    {
      "x": 3,
      "y": "E"
    }
    
Computer user only a post request

    {
    }


## Api Documentation

Accessible via the Nelmio Api Doc:

{host}/api/doc 

    