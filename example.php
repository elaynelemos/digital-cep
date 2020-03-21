<?php

require_once "vendor/autoload.php";

use Lay\DigitalCep\Search;

$search = new Search;

$result = $search->getAddressFromZipcode('01001000');

print_r($result);