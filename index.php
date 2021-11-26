<!-- On ouvre la balise php -->
<?php

require 'DIC.php';
require 'Bar.php';
require 'Foo.php';

// On appelle la classe DIC
$app = new DIC();

// On instancie une classe
var_dump($app->get('Foo'));
