# Cronphp
Takes two arguments: a crontab-string and a unix-timestamp.<br>
If the crontab-string matches the timestamp it will return "true", otherwise "false".<br>
Syntax and behaviour follows the classic implementation on linux (vixie-cron).

## INSTALLATION
include the class in your php-code 
```php 
include_once "class_Cronphp.php";
```

## USAGE

```php
<?php
include_once "class_Cronphp.php";
$example_cronstring = "*/4 16-17 * * 5";
$example_timestamp = 1651247570;
echo "\n".date("Y-m-d H:i:s", $example_timestamp)."\n";
////////////////////
echo (Cronphp::getmatch($example_cronstring, $example_timestamp)) ? ("match") : ("no match");
////////////////////
echo "\n";
?>
```

### 
### 
### 


