<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($container){
    $settings = $container->get('settings')['renderer'];
    // return new Slim\Views\PhpRenderer($settings['template_path']);

     $view = new \Slim\Views\Twig($settings['template_path'], [
         'debug' => true
     ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));

    return $view;
};
