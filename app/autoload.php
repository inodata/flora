<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require_once __DIR__.'/../vendor/autoload.php';

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';
}

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
