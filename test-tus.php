<?php

require 'vendor/autoload.php';

use TusPhp\Tus\Client;

$client = new Client('https://video.bunnycdn.com/tusupload');
echo "Client initialized successfully!";
