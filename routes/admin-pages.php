
<?php

use Source\PageAdmin;
use Source\Model\User;
use Source\Model\Page;

$app->get('/admin/paginas', function () {
    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '') {
        $pagination = Page::getPageSearch(trim($search), $page);
    } else {
        $pagination = Page::getPage($page);
    }

    $pages = [];

    for ($x = 0; $x <  $pagination['pages']; $x++) {
        array_push($pages, [
            'href' => '/admin/paginas?' . http_build_query([
                'page' => $x + 1,
                'search' => $search
            ]),
            'text' => $x + 1
        ]);
    }

    $page = new PageAdmin();

    $page->setTpl("pages", array(
        "p" => $pagination['data'],
        "search" => $search,
        "pages" => $pages
    ));
});

$app->post('/admin/paginas/create', function () {

    User::verifyLogin();
    $p = new Page();
    #$data = filter_var_array($_POST, FILTER_SANITIZE_STRING);
    $data = $_POST;

    $_SESSION['recoversPost'] = $_POST;
    
    if (in_array("", $data)) {
        Page::setError('Preencha todos os campos.');
        header("Location: /admin/paginas/create");
        exit;
    }

    $p->setData($data);
    $p->save();
    unset($_SESSION['recoversPost']);
    header("Location:/admin/paginas");
    exit;
});

$app->get("/admin/paginas/create", function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("pages-create", ['msgError' => Page::getError()]);
});

/**
 * Delete one page
 */
$app->get("/admin/paginas/:id/delete", function ($id) {

    User::verifyLogin();
    $p = new Page();
    $p->get((int) $id);
    $p->delete();
    header("Location: /admin/paginas");
    exit;
});

/**
 * Edit
 */
$app->get("/admin/paginas/:id", function ($id) {

    User::verifyLogin();

    $p = new Page();

    $p->get((int) $id);
    $page = new PageAdmin();
    $page->setTpl("pages-update", [
        "p" => $p->getValues(),
        'msgError' => Page::getError()
    ]);
});

/**
 * Update
 */
$app->post("/admin/paginas/:id", function ($id) {

    User::verifyLogin();
    $p = new Page();
    $p->get((int) $id);

    $p->setData($_POST);
    $p->save();
    header("Location: /admin/paginas");
    exit;
});
