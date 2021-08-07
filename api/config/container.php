<?php

declare(strict_types=1);

use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../config/dependencies.php');

return $builder->build();
