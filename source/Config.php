<?php

define("URL_BASE","http://sistemamilenium.local.com");

// Email configuration
// define("URL_BASE","https://gerenciador.sociedadenovomilenium.com.br/");
// define("URL_BASE_ADMIN","https://gerenciador.sociedadenovomilenium.com.br/");

// Email configuration
define("MAIL_EMAIL","contato@sociedadenovomilenium.com.br");
define("MAIL_PASSWORD","iL5pb2?6");
define("MAIL_HOST","sociedadenovomilenium.com.br");
define("MAIL_NAME_FROM","Gerenciador de Conteúdo");
define("ASAAS_API_KEY","1acf62867108e51a32a60e5e93ed8de32b8e887d2d5e89b1aad5b38e8dac90a1");

// Database configuration
define("DB_SITE", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "db_ecommerce_editado",
    "username" => "admin",
    "passwd" => "admin",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);
