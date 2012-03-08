<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;

/*

$container->setDefinition(
    'pwx_deploy.example',
    new Definition(
        'Pwx\DeployBundle\Example',
        array(
            new Reference('service_id'),
            "plain_value",
            new Parameter('parameter_name'),
        )
    )
);

*/