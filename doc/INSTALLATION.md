# Bootstrap Application with ansible

## Prerequisites and steps for linux

Install the following requirements on your system:

(This only needs to be done once.)

* docker
* docker-compose
* docker should run as a non-root user      
* pip & ansible (see below)

### Install pip & ansible

(This only needs to be done once.)

Install these libraries first (linux): 
    
    sudo apt install python-dev python3-dev build-essential python

Install pip and then ansible globally (mind the SUDO)

    curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py
    sudo -H python get-pip.py
    sudo -H pip install ansible
    rm get-pip.py


## Set up Ansible project requirements

(This only needs to be done once.)

    pip install -r etc/ansible/requirements.txt --user

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

The apache playbook is completely ignored on other systems than Linux.

You may need to manually configure the following.
 
* create a host entry `api.battle.local` in your hosts file, that should resolve to 127.0.0.1
* to have the best experience, use a local webserver on the host machine to proxy all requests from host to the docker web service on the web port provided above.
(i.e. api.battle.local --> localhost:62080) This helps avoiding using a port number in the urls.

## EXECUTE COMPOSER COMMANDS

Execute composer commands by simply prefixing them like this:
    
    docker-compose run --rm app composer {install|require|update}
