# Documentation

## Usage of the API

A [Postman Collection](Battle_Ship_postman_collection.json) is available which showcases the workflow described below.

### Set up and prepare ships

POST /battles

    {
        "width": 8,
        "height": 8
    }

Check model properties and validation constraints in the [Api Doc](/api/doc). 

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

Computer Opponent is marked with type: 1, Regular user type: 0.

Check the api doc for a complete list of model types and parameters.

(This config theoretically also allows two regular players to play against each other.)

### Shoot

POST /battles/:battleId/players/:playerId/shots

Regular user needs to send coordinates

    {
      "x": 3,
      "y": "E"
    }
    
Computer type of player need only a post request with "empty" body. 

    {
    }
    
This way a calculated shot will be fired and the same response will be delivered.

### Status

GET /battles/:battleId

Get the status of the battle. This should be called after/before shooting, 
to check if the battle has already been won for example. 


### Get the shots of a player

GET /api/v1/battles/{battleId}/players/{playerId}/shots

This lists all the shots that have been fired by a user.
