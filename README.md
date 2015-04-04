config
================

A simple config module integrated with some service discovery system clients for php

[![Build Status](https://travis-ci.org/kpacha/config.png?branch=master)](https://travis-ci.org/kpacha/config)

This project is an abstraction of the initial versions of [suricate-config](https://github.com/kpacha/suricate-config).

#Requirements

* git
* PHP >=5.3.3

##Optional dependencies (depending on your service discovering flavour)

* [suricate-config](https://github.com/kpacha/suricate-config)
* [consul-config](https://github.com/kpacha/consul-config)

(so check their dependencies!)

#Installation

##Standalone

##As a library (recomended)

Include the `kpacha/sconfig` package in your compose.json with all the dependencies of your project

    "require":{
        "kpacha/config": "~0.1"
    }

###Git installation

Clone the repo

    $ git clone https://github.com/kpacha/config.git

Install the php dependencies

    $ cd config
    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar install

and add one of the recommended dependencies

    $ php composer.phar require kpacha/suricate-config

###Composer installation

Create a project with composer

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar create-project kpacha/config [directory]

and add one of the recommended dependencies

    $ cd [directory]
    $ php composer.phar require kpacha/suricate-config

Remeber to set the [directory] parameter or composer will create the project in your current path.

#Config files

Note that `config` expects to find all your config files in a single dir. Currently, yaml is the only supported format for config files.

Also, you should keep in mind those rules:

* All parsed config files will be cached in a single native php config file called `config_file.php`.
* Every parsed config file will be stored in an array, indexed by its basename.
* The `config` module uses the `Symfony\Component\Config\ConfigCache` class to manage the cached configuration, so it will create a `.meta` file with some info about the cached files.
* The `config` module expects to find a config file called `suricate_services.yml` with some required info (check the [tests/fixtures](https://github.com/kpacha/config/tree/master/tests/fixtures) dir for an example)
* The service manager will create a config file called `services_solved.yml`file. Please, do not play with it.

##services.yml

The required fields are:

* *service-manager*: the class of the service manager to handle the discovering tool (suricate or consul)
* *server*: the url of the suricate server or the consul agent
* *service-names*: the list of services to watch
* *default*: the default configuration to use when the suricate server is not reachable

#Usage

##Config module

Create a `Kpacha\Config\Configuration` object.

    use Kpacha\Config\Configuration;
    $configuration = new Configuration('/path/to/your/config/folder', true);

And you're ready to go! Just ask for your config data whenever you need it.

    $someModuleConfig = $configuration->get('some-module');

    try{
        $configuration->get('unknown-module'); // unknown-module.yml does not exist
    } catch(\Exception $e){
       // do something
    }

##Console

The `config` packages exposes a clean CLI interface so you could add a cron to update the service info querying the service discovering server/agent for the services listed in the `service-name` area of your `services.yml` file with

    $ bin/config c:u /path/to/config/dir

Run the `config` script to trigger any console command.