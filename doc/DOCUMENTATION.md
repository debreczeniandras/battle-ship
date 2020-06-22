# Documentation

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

Computer Opponent is marked with type: 1, Regular user type: 0.

Check the api doc for a complete list of model types and parameters.

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

### Status

GET /battles/:battleId

Get the status of the battle. This should be called after/before shooting, 
to check if the battle has already been won for example. 


### Get the shots of a player

GET /api/v1/battles/{battleId}/players/{playerId}/shots

This lists all the shots that have been fired by a user.
