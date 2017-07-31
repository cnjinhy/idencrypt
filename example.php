<?php

require dirname(__FILE__) . '/IdEndecrypt.class.php';

$key = 'Test1234';

$id = 123456;
$id_encode = IdEndecrypt::encode($id, $key);
echo $id_encode . PHP_EOL;

$id_decode = IdEndecrypt::decode($id_encode, $key);
echo $id_decode . PHP_EOL;
