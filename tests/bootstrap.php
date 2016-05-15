<?php

use duncan3dc\Sql\Driver\Mysql\Server;
use duncan3dc\Sql\Factory;

require __DIR__ . "/../vendor/autoload.php";

$server = new Server("kessel", "songbook-test", "E8XJzk1W6H%SpI^^8HNj61KZNF@uA#7kGJ6SpGyr&GA7zjNJyP7%3RHeX0ITdK!7jlhU^zW28domc!oKxml4XQlkeGdAYQRiYW@");
$server->setDatabase("songbook");
Factory::addServer("songbook", $server);
