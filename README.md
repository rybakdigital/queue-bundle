# Queue Bundle

Simple queue bundle for Symfony 4 application

[![Build Status](https://travis-ci.org/rybakdigital/queue-bundle.svg?branch=master)](https://travis-ci.org/rybakdigital/queue-bundle)
[![CircleCI](https://circleci.com/gh/rybakdigital/queue-bundle/tree/master.svg?style=svg)](https://circleci.com/gh/rybakdigital/queue-bundle/tree/master)

## Usage
Add as a requirement via composer:
```
composer require rybakdigital/queue-bundle
```

Use console command to execute next task from the queue:
```
rybakdigital:queue:worker:do
```

## Examples
|-----------------------------------------------------------------------------------------
|  id  | queue    | callable          | method      | options       | data           | ...
|-----------------------------------------------------------------------------------------
|  1   | main     | my.service        | process     | {"a":"b"}     | {"name":"foo"} |
|  2   | my_queue | App\Class\Name    | generate    | {"foo":"bar"} | {"name":"foo"} |
|  3   | main     | my.mailer.service | sendMessage | {"foo":"bar"} | {"name":"foo"} |

Execute task from named queue:
```
rybakdigital:queue:worker:do my_queue
```
Will execute next available task from queue thats labeled as my_queue (task 2).
It will call `generate` of `App\Class\Name` class and pass 2 arrays: options and data.
