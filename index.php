<?php

require __DIR__.'/vendor/autoload.php';

use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();
$app->register(new FormServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new TwigServiceProvider(), [
    'twig.path'     => __DIR__,
]);
$app['debug'] = true;

$app->match('/', function (Application $app, Request $r) {
    $form = $app['form.factory']->createBuilder()
        ->add('testing', 'repeated', [
            'type'              => 'text',
            'required'          => true,
        ])
        ->add('submit', 'submit')
        ->getForm();

    $form->handleRequest($r);
    var_dump($form['testing']['first']->getNormData());
        var_dump($form['testing']['second']->getNormData());die;
    if ($form->isValid()) {
        $val = $form['testing']->getData();
        if (null === $val) {
            return 'Value is null, repated values did not match but the form was valid?';
        } else {
            return sprintf('Value is "%s"', $val);
        }
    }

    return $app['twig']->render('index.html.twig', [
        'form'  => $form->createView(),
    ]);
})->method('GET|POST');

$app->run();
