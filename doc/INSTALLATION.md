# Bootstrap Application with ansible (linux)

## Install Ansible and pip

    sudo apt install ansible python3-pip

## Execute the playbook

    ansible-playbook -K setup.yml -v

You'll be prompted for your root password - which is relevant for apache modules and config changes.

Please select afterwards two available ports for the following services:

* _port_web_ is to reach the docker webserver from outside
* _port_redis_ is to reach the redis explorer from outside
 

Sensible defaults are already provided, which you may accept by clicking Enter.
 
## URLS

| Url | Description |
|---- | --- | 
| http://api.battle.local | Frontend API |


## Ansible on MAC

Some of the playbooks are completely ignored on other systems than Linux.

That means the playbook can be executed without elevated privileges:
  
    ansible-playbook setup.yml -v

You may need to manually configure the following afterwards
 
* create a host entry `api.battle.local` in your hosts file, that should resolve to 127.0.0.1
* to have the best experience, use a local webserver on the host machine to proxy all requests from host to the docker web service on the web port provided above.
(i.e. api.battle.local --> localhost:62080) This helps avoiding using a port number in the urls.

## Without ansible

* copy .env.dist to .env
* fill out the needed ports
* docker-compose up --build -d
* enjoy!

## EXECUTE PHP COMMANDS

Execute composer commands by simply prefixing them like this:
    
    docker-compose run --rm app composer {install|require|update}
    
Execute php commands by simply prefixing them like this:
    
    docker-compose run --rm app {command|bin/phpunit}

