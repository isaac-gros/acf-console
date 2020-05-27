#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Console\Command\GreetCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new GreetCommand());
$application->run();