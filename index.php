<?php

session_start();

require_once "vendor/autoload.php";

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);          
               
require_once("functions.php");
require_once("site.php");
require_once("admin.php");

require_once("routes/admin-convenios.php");
require_once("routes/admin-planos.php");
require_once("routes/admin-articles.php");
require_once("routes/admin-articles-categories.php");
require_once("routes/admin-pages.php");
require_once("routes/admin-photos.php");
require_once("routes/admin-albums.php");
require_once("routes/admin-samples.php");
require_once("routes/admin-banners.php");

require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");
require_once("admin-orders.php");

$app->run();
