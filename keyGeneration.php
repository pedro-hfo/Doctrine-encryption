<?php
require_once "vendor/autoload.php";
use ParagonIE\Halite\KeyFactory;

$encKey = KeyFactory::generateEncryptionKey();
KeyFactory::save($encKey, 'test_key.key');