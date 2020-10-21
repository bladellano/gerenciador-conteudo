<?php

use Source\PageAdmin;
use Source\Model\User;
use Source\Model\Article;
use Source\Model\Convenio;
use Source\Model\Plano;

$app->get('/admin', function () {

    User::verifyLogin();

    $articles = count(Article::listAll());
    $convenios = count(Convenio::listAll());
    $planos = count(Plano::listAll());

    $page = new PageAdmin();
    $page->setTpl("index",[
        "qtdArticles" => $articles,
        "qtdConvenios" => $convenios,
        "qtdPlanos" => $planos,
    ]);
});

/**
 * Login
 */

$app->get('/admin/login', function () {
    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);
    $page->setTpl("login", [
        "msgError" => User::getError()
    ]);
});

$app->post('/admin/login', function () {

    try {
        User::login($_POST["login"], $_POST["password"]);
    } catch (\Exception $e) {

        User::setError($e->getMessage());
        header("Location: /admin/login");
        exit;
    }
    header("Location: /admin");
    exit;
});

$app->get('/admin/logout', function () {
    User::logout();
    header("Location: /admin/login");
    exit;
});


/**
 * Recupera a senha do usuário
 */
$app->get("/admin/forgot", function () {
    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("forgot");
});

$app->post("/admin/forgot", function () {

    $user = User::getForgot($_POST["email"]);
    header("Location:/admin/forgot/sent");
    exit;
});

$app->get("/admin/forgot/sent", function () {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("forgot-sent");
});

$app->get("/admin/forgot/reset", function () {

    $user = User::validForgotDecrypt($_GET['code']);

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("forgot-reset", array(
        "name" => $user["desperson"],
        "code" => $_GET["code"],
    ));
});

$app->post("/admin/forgot/reset", function () {

    $userForgot = User::validForgotDecrypt($_POST["code"]);

    User::setForgotUsed($userForgot["idrecovery"]);

    $user = new User();

    $user->get((int) $userForgot["iduser"]);

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT, [
        "cost" => 12,
    ]);

    $user->setPassword($password);

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("forgot-reset-success");
});
