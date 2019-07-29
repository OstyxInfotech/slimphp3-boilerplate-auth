<?php

$config=require __DIR__ . '/../config/database.php' ;

$capsule=new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($config);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db']=function($container) use ($capsule){
  return $capsule;
};