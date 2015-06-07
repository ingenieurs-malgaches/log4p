
log4p
============

Simple Logging API for PHP developpers

============

## Basics

When using log4p, the first thing you have to do is to include the library. Then you have to build the logger by using a set of methods. It is obvious that not all of them is required.
In the following example, the logger writes logging messages in a file called 'myAppLog.log'.
    
```php 

$log =  log4p::builder()->filePath('myAppLog.log')->build();

$log->info('Initializing my app ...');  // will write : Initializing my app ...
$log->error('A problem occured ...');   // will write : A problem occured ...
$log->fatal('Server is down ! ');       // will write : Server is down ! 

```

By executing the code above, you will get the following result in your log file :

```

[INFO] 2015-06-08 0:18:10 :  Initializing my app ... 
[ERROR] 2015-06-08 0:18:10 :  A problem occured ... 
[FATAL] 2015-06-08 0:18:10 :  Server is down !

```
## Mask

Sometimes, using logger API can cause performance problems. So that you have to mask some levels. 
Remember that you have the following relations between log4p levels (this is not specific to log4p) : 
    ```DEBUG < INFO < WARN < ERROR < FATAL```
You can mask levels by using the 'level' method like this :

```php

$log =  log4p::builder()->filePath('myAppLog.log')
            ->level(log4p::ERROR) 
        ->build();

$log->debug('Doing something not important');   // will write nothing 
$log->info('Initializing my app ...');          // will write nothing
$log->error('A problem occured ...');           // will write : A problem occured ...
$log->fatal('Server is down ! ');               // will write : Server is down ! 

```

By executing the code above, you will get the following result in your log file :

```

[ERROR] 2015-06-08 0:14:11 :  A problem occured ... 
[FATAL] 2015-06-08 0:14:11 :  Server is down !  

```
## Limit

A part from that, by using log4p, you can also set a limit of log files. By default, log4p set 
```1024 bytes``` as limit but you can change this value by using the ```'maxFileSize'``` method as shown in 
the following example :

```php

$log =  log4p::builder()->filePath('myAppLog.log')
            ->maxFileSize(200)
        ->build();
        
```

Thank you for visiting my repositories. you can also download some simple applications from [my website](http://nabil.zz.mu).
Enjoy !





