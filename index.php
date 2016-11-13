<?php

require "vendor/autoload.php";


$obiba = new \ObibaMicaClient\ObibaMicaDocuments();

echo $obiba->getCollections('getStudies');