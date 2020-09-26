
<?php

use Source\PageAdmin;
use Source\Model\User;
use Source\Model\ArticleCategory;

$app->get('/admin/artigos-categorias', function () {
    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '') {
        $pagination = ArticleCategory::getPageSearch(trim($search), $page);
    } else {
        $pagination = ArticleCategory::getPage($page);
    }

    $pages = [];

    for ($x = 0; $x <  $pagination['pages']; $x++) {
        array_push($pages, [
            'href' => '/admin/artigos-categorias?' . http_build_query([
                'page' => $x + 1,
                'search' => $search
            ]),
            'text' => $x + 1
        ]);
    }

    $page = new PageAdmin();

    $page->setTpl("articles-categories", array(
        "articles" => $pagination['data'],
        "search" => $search,
        "pages" => $pages
    ));
});

$app->post('/admin/artigos-categorias/create', function () {

    User::verifyLogin();

    $category = new ArticleCategory();

    $data = filter_var_array($_POST, FILTER_SANITIZE_STRING);

    $_SESSION['recoversPost'] = $_POST;

    if (in_array("", $data)) {
        ArticleCategory::setError('Preencha todos os campos.');
        header("Location: /admin/artigos-categorias/create");
        exit;
    }

    $category->setData($data);
    $category->save();
    unset($_SESSION['recoversPost']);
    header("Location:/admin/artigos-categorias");
    exit;
});

$app->get("/admin/artigos-categorias/create", function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("articles-categories-create", ['msgError' => ArticleCategory::getError()]);
});

//Delete
$app->get("/admin/artigos-categorias/:id/delete", function ($id) {
    User::verifyLogin();
    $category = new ArticleCategory();
    $category->get((int) $id);
    $category->delete();
    header("Location: /admin/artigos-categorias");
    exit;
});

//Edit
$app->get("/admin/artigos-categorias/:id", function ($id) {

    User::verifyLogin();

    $category = new ArticleCategory();
    $category->get((int) $id);
    $page = new PageAdmin();
    $page->setTpl("articles-categories-update", [
        "category" => $category->getValues(),
        'msgError' => ArticleCategory::getError()
    ]);
});

//Update
$app->post("/admin/artigos-categorias/:id", function ($id) {

    User::verifyLogin();

    $category = new ArticleCategory();
    $category->get((int) $id);
    $category->setData($_POST);
    $category->save();
    header("Location: /admin/artigos-categorias");
    exit;
});
