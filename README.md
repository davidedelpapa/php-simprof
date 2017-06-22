# PHP SimProf
Simple profiler for PHP scripts
Ver 0.1 - (c) 2017, Davide Del Papa, Public Domain
(*Originally based on an answer from [StackOverflow](http://stackoverflow.com/questions/21133/simplest-way-to-profile-a-php-script#answer-29022400)*)

## Usage

An example is worth more than a thousand words:

#### test1.php.profiler
```php
<?php
sp_start();
sleep(2);
sp_flag('middle1: action 1');
sleep(1);
sp_flag('middle1: action 2');
sleep(3);
sp_end();
?>
```

### Run the profiler

From the shell 
```shell
php -f profile.php > output.html
```

And visualize the contents of ```output.html```

Alternatively, in a testing server, just point the browser to the file ```profile.php```
#### Output (output.html)
```
Simple PHP Profiler

File: test1.php.profiler
Results:

Results of 1 run -- 2017 Jun 22, 10:07:07pm
Total Time
Milliseconds	    Seconds
Profiler::Start     2000.307083	2.000307
middle1: action 1	1000.312090	1.000312
middle1: action 2	3000.240803	3.000241
Profiler::End	
(last mark)
TOTAl TIME	6000.859976	6.000860
```
## Available Commands

```php
sp_start();
```

Starting message for the profiler; alternatively just use ```sp_flag(string)``` with an appropriate starting message.


```php
sp_flag('message');
```

Sets a flag for the profiler with a commenting message. The message is useful to describe what the next section will do, until the following ```sp_flag(string)``` is issued (or an end);


```php
sp_end();
```

Ending message for the profiler; alternatively just use ```sp_flag(string)``` with an appropriate ending message.

#### Caution
Whatever the last flag (```sp_flag(string)``` or ```sp_end()```) the profiler does not check the actual timings until the ned of the script.

## More Complex Usage

Another example:

#### Dir structure
```
.
├── profile.php
├── simprof.php
├── test1.php
├── test2.profiler
└── tests
    └── test3.php.profiler
```

As you can see your scripts can have normal ```.php``` extension, or ```.profiler```, or ```.php.profiler```; it works inside the current dir or its subdirectories.

**Recommended behavior**:

- Put the PHP SimProf files inside the root dir, and all PHP scripts in sub-directories
- For additional clarity call all scripts ```.php.profiler```

#### test1.php
```php
<?php
sp_flag('start');
sleep(1);
sp_flag('middle');
sleep(4);
sp_flag('end');
?>
```

#### test2.profiler
```php
<?php
sp_flag('start');
sleep(3);
sp_flag('middle');
sleep(2);
sp_flag('end');
```

#### test3.php.profiler
```php
<?php
sp_flag('start');
sleep(1);
sp_flag('middle1');
sleep(1);
sp_flag('middle2');
sleep(1);
sp_flag('end');
?>
```

#### Output (output.html)
```
Simple PHP Profiler

File: test1.php
Results:

Results of 1 run -- 2017 Jun 22, 09:35:57pm
Total Time
Milliseconds	Seconds
start	1000.277996	1.000278
middle	4000.146866	4.000147
end	
(last mark)
TOTAl TIME	5000.424862	5.000425
File: test2.profiler
Results:

Results of 1 run -- 2017 Jun 22, 09:36:02pm
Total Time
Milliseconds	Seconds
start	3000.408173	3.000408
middle	2000.092030	2.000092
end	
(last mark)
TOTAl TIME	5000.500202	5.000500
File: test3.php.profiler
Results:

Results of 1 run -- 2017 Jun 22, 09:36:05pm
Total Time
Milliseconds	Seconds
start	1000.219107	1.000219
middle1	1000.108004	1.000108
middle1	1000.205994	1.000206
end	
(last mark)
TOTAl TIME	3000.533104	3.000533
```

## Cleaning the files

You may use the process.py utility to clean the code of your script from the ```sp_flag()``` calls

#### Usage
```shell
.\process.py input-file output-file
```

Example:
```shell
./process.py test/test1.php.profiler test/test1.php
```
## Road-map

For now this is a simple utility I use for my own scripts. 
Since it is based on a public answer on StackOverflow I don't deem it a good idea to give it a license, and all.
I have thought about the following useful functionalities, which I'll implement maybe some day:

- a configuration file
- flags to skip certain scripts
- flags for comments about the flags, to be removed along with the flag themselves
- possibility of multiple runs of the same scripts, with different configurations (to be handled by the aforementioned config file)
- maybe some diagrams to show more visually the interrupts flow