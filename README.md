# PHP SimProf
Simple profiler for PHP scripts
Ver 0.1 - (c) 2017, Davide Del Papa, Public Domain
(*Originally based on an answer from [StackOverflow](http://stackoverflow.com/questions/21133/simplest-way-to-profile-a-php-script#answer-29022400)*)

## Simple Usage

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

## More Complex Use Case

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
### Note on process.py

The following is the list the ```process.py``` works with:
```
'simprof.php',
'sp_manual',
'sp_set_param',
'sp_flag',
'sp_start',
'sp_end',
'sp_prepare_report',
'sp_print_report'
```

Whenever ```process.py``` finds any of these keyword in a line **it will delete the whole line!**
You are warned!

## Manual Use

Naturally, not all programs are so simple as to be possible to use it with the ```profile.php```

Consider the following script (which accepts $_REQUEST):

#### manual_test1.php
```php
<?php
$res = $_REQUEST['x'] + $_REQUEST['y'];
echo "<h1>Result: $res</h1>";
sleep(2);
?>
```
In order to test it from the command line, we run:

```shell
php -B "\$_REQUEST = array('x' => '12', 'y' => '3');" -F 'manual_test1.php'
```
**Notes**:

- press ```ENTER``` to "pulse" a request to the script (otherwise there will be no output); ```Ctrl+C``` when you are done plying with our fabulous script!
- Whenever possible, use a *server* :-)

In order to profile the script we need to add manually both the flags and the commands that calls the ```simprof.php```, plus you need to call the ```sp_manual()``` function

#### manual_test1.php.profiler
```php
<?php
include_once 'simprof.php';
sp_manual();
sp_flag('start; calculate sum');
$res = $_REQUEST['x'] + $_REQUEST['y'];
sp_flag('display result');
echo "<h1>Result: $res</h1>";
sp_flag('sleep a little');
sleep(2);
sp_flag('end');
sp_prepare_report();
sp_print_report();
?>
```

Now we run the usual test (pressing the usual ```Enter```:

```shell
php -B "\$_REQUEST = array('x' => '12', 'y' => '3');" -F 'test/manual_test1.php.profiler'
```

#### Output
```
<h1>Result: 15</h1>
<html>
<body>
<h1>Simple PHP Profiler</h1>
<hr>
<b>File:</b> simprof.php
<h4>Results:</h4>
<table>
<caption><em>Results of 1 run -- 2017 Jun 23, 08:44:07pm</em></caption>
<tr><th rowspan='2'></th><th colspan = '2'>Total Time</th></tr>
<tr><th>Milliseconds</th><th>Seconds</th></tr>
<tr><th>start; calculate sum</th><td>0.053167</td><td>0.000053</td></tr>
<tr><th>display result</th><td>0.198841</td><td>0.000199</td></tr>
<tr><th>sleep a little</th><td>2000.303984</td><td>2.000304</td></tr>
<tr><th>end</th><td colspan = '2'><center>(last mark)</center></td></tr>
<tr><th>TOTAl TIME</th><th>2000.555992</th><th>2.000556</th></tr>
</table>
</body>
</html>
```

As you can see, (at least for now) the profiler's report is concatenated at the end of our script's output

#### Notes

- You can have in a project manual scripts mixed in other scripts, and the automatic ```profile.php``` will skip their execution.
- ```process.py``` is well aware of the manual usage (check the key word list!) and knows (a little) how to clean a manually profiled script.

## The sp_set_param() command

The previous example showed the difficulty of running automated profiling on a script which needs $_REQUEST, $_GET, or $_POST parameters.
We saw how using the manual mode we can profile each individual script manually.

However, there is no need to run manually a script if the only difficulty we have is to set HTTP request parameters.
The command ```sp_set_param($parameters)``` comes handy in this case.

An example:
#### param_test1.php.profiler
```php
<?php
sp_set_param(array('x' => '5', 'y' => '2'));
sp_start();
$res = $_GET['x'] + $_POST['y'];
sp_flag();
echo "<h1>Result: $res</h1>";
sp_flag();
sleep(1);
sp_end();
?>
```
#### Note

The output will be discarded in the 
## Available Commands

```php
sp_flag('message');
```

Sets a flag for the profiler with a commenting message. The message is useful to describe what the next section will do, until the following ```sp_flag(string)``` is issued (or an end);
**Note**: in the new version of *simprof* the *message* is optional.

```php
sp_start();
```

Starting message for the profiler; alternatively just use ```sp_flag(string)``` with an appropriate starting message.

```php
sp_end();
```

Ending message for the profiler; alternatively just use ```sp_flag(string)``` with an appropriate ending message.

#### Caution
Whatever the last flag (```sp_flag(string)``` or ```sp_end()```) the profiler does not check the actual timings until the end of the script. *Commands present after the last flag will not be profiled*.

```php
sp_set_param($parameters[, mode]);
```

Sets HTTP request parameters ($_REQUEST, $_GET, or $_POST ).
*mode* can be one of the following:

- ```$_SP_ALL``` (default): sets the three of them ($_REQUEST, $_GET, or $_POST ) with the parameters' array. It occupies memory, but it allows for mixing $_REQUEST, $_GET, and $_POST parameters.
- ```$_SP_REQUEST```: Sets $_REQUEST with the parameters' array.
- ```$_SP_GET```: Sets $_GET with the parameters' array.
- ```$_SP_POST```: Sets $_POST with the parameters' array.


```php
sp_manual();
```

Starts the manual mode. FOR MANUAL USE.

```php
sp_prepare_report();
```

(to be used with ```sp_print_report()```)
Prepares the report, at the end of all flags. FOR MANUAL USE.

```php
sp_print_report();
```

(to be used with ```sp_prepare_report())```)
Prints out the actual report (using ```echo```). FOR MANUAL USE.

## Road-map

For now this is a simple utility I use for my own scripts. 
Since it is based on a public answer on StackOverflow I don't deem it a good idea to give it a license, and all.
I have thought about the following useful functionalities, which I'll implement maybe some day:

- a configuration file
- flags to skip certain scripts(not only manual) or parts of a script
- flags for comments about the flags, to be removed along with the flag themselves
- possibility of multiple runs of the same scripts, with different configurations (to be handled by the aforementioned config file)
- maybe some diagrams to show more visually the interrupts flow