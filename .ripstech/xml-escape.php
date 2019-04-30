<?php

require_once __DIR__.'/../vendor/autoload.php';

use League\CommonMark\Util\Xml;

echo Xml::escape($_GET['input']);
