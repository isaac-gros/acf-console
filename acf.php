#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Console\Command\CreateBasicFieldCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new CreateBasicFieldCommand());
$application->run();