
<?php

use Source\PageAdmin;
use Source\Model\User;
use CoffeeCode\Uploader\Image;
use Source\Model\Article;
use Source\Model\ArticleCategory;

$app->get('/admin/artigos', function () {
    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '') {
        $pagination = Article::getPageSearch(trim($search), $page);
    } else {
        $pagination = Article::getPage($page);
    }

    $pages = [];

    for ($x = 0; $x <  $pagination['pages']; $x++) {
        array_push($pages, [
            'href' => '/admin/artigos?' . http_build_query([
                'page' => $x + 1,
                'search' => $search
            ]),
            'text' => $x + 1
        ]);
    }

    $page = new PageAdmin();

    $page->setTpl("articles", array(
        "articles" => $pagination['data'],
        "search" => $search,
        "pages" => $pages
    ));
});

$app->post('/admin/artigos/create', function () {

    User::verifyLogin();
//PROBLEMAS EM DELETAR ARTIGO
//PROBLEMAS EM DELETAR ARTIGO
//PROBLEMAS EM DELETAR ARTIGO
//PROBLEMAS EM DELETAR ARTIGO
//PROBLEMAS EM DELETAR ARTIGO
//PROBLEMAS EM DELETAR ARTIGO
//PROBLEMAS EM DELETAR ARTIGO
    $upload  = new Image("storage/images", "articles");
    $article = new Article();

    $data = filter_var_array($_POST, FILTER_SANITIZE_STRING);

    $files = $_FILES;

    $_SESSION['recoversPost'] = $_POST;

    if (in_array("", $data)) {
        Article::setError('Preencha todos os campos.');
        header("Location: /admin/artigos/create");
        exit;
    }

    if (!empty($files['image']) && $files['image']['error'] == 0) {

        $file = $files["image"];
        if (!empty($file["image"]) || !in_array($file["type"], $upload::isAllowed())) {
            Article::setError('Selecione uma imagem válida.');
            header("Location: /admin/artigos/create");
            exit;
        } else {

            $image_thumb = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 100);
            $image = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 600);

            $data['image'] = $image;
            $data['image_thumb'] = $image_thumb;
        }
    }

    $article->setData($data);
    $article->save();
    unset($_SESSION['recoversPost']);
    header("Location:/admin/artigos");
    exit;
});

$app->get("/admin/artigos/create", function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $categories = (new ArticleCategory())->listAll();
    $page->setTpl("articles-create", [
        'msgError' => Article::getError(),
        'categories' => $categories
    ]);
});

//Delete
$app->get("/admin/artigos/:id/delete", function ($id) {

    User::verifyLogin();
    $article = new Article();
    $article->get((int) $id);

    if (file_exists($article->getimage())) {
        unlink($article->getimage());
        unlink($article->getimage_thumb());
    }

    $article->delete();
    header("Location: /admin/artigos");
    exit;
});

//Edit
$app->get("/admin/artigos/:id", function ($id) {

    User::verifyLogin();

    $article = new Article();

    $categories = (new ArticleCategory())->listAll();

    $article->get((int) $id);
    $page = new PageAdmin();
    $page->setTpl("articles-update", [
        "article" => $article->getValues(),
        'msgError' => Article::getError(),
        'categories' => $categories
    ]);
});

//Update
$app->post("/admin/artigos/:id", function ($id) {

    User::verifyLogin();

    $article = new Article();
    $upload  = new Image("storage", "images");

    $article->get((int) $id);

    $files = $_FILES;

    if ($files['image']['error'] == 0) {
        /*Primeiro apaga imagem anterior*/
        if (file_exists($article->getimage())) {
            unlink($article->getimage());
            unlink($article->getimage_thumb());
        }

        /*Trata a nova imagem*/
        $file = $files["image"];

        if (!empty($file["image"]) || !in_array($file["type"], $upload::isAllowed())) {
            Article::setError('Selecione uma imagem válida.');
            header("Location: /admin/artigos/{$id}");
            exit;
        } else {

            $_POST['image'] = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 100);
            $_POST['image_thumb'] = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 600);
        }
    }

    if (!isset($_POST['spotlight']) || !isset($_POST['show_author'])) {
        $_POST['spotlight'] = $_POST['spotlight'];
        $_POST['show_author'] = $_POST['show_author'];
    }

    $article->setData($_POST);
    $article->save();
    header("Location: /admin/artigos");
    exit;
});
