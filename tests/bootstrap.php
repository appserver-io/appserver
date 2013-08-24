<?php

// set the new include path
set_include_path(get_include_path(). PATH_SEPARATOR . __DIR__);

require 'TechDivision/ApplicationServer/SplClassLoader.php';

$classLoader = new TechDivision\ApplicationServer\SplClassLoader();
$classLoader->register();

// require '${dir.www}/${instance.base.dir}/bootstrap.php';