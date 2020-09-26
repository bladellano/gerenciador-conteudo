
<?php

use Source\PageAdmin;
use Source\Model\User;
use CoffeeCode\Uploader\Image;
use Source\Model\Banner;

$app->get('/admin/banners', function () {

    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '') {
        $pagination = Banner::getPageSearch(trim($search), $page);
    } else {
        $pagination = Banner::getPage($page);
    }
    $pages = [];

    for ($x = 0; $x <  $pagination['pages']; $x++) {
        array_push($pages, [
            'href' => '/admin/banners?' . http_build_query([
                'page' => $x + 1,
                'search' => $search
            ]),
            'text' => $x + 1
        ]);
    }

    $page = new PageAdmin();

    $page->setTpl("banners", array(
        "banners" => $pagination['data'],
        "search" => $search,
        "pages" => $pages
    ));
});

$app->post('/admin/banners/create', function () {

    User::verifyLogin();
    $upload  = new Image("storage/images", "banners");
    $banner = new Banner();
    $data = $_POST;
    $files = $_FILES;

    $_SESSION['recoversPost'] = $_POST;

    if (in_array("", $data)) {
        Banner::setError('Preencha todos os campos.');
        header("Location: /admin/banners/create");
        exit;
    }

    if (!empty($files['image']) && $files['image']['error'] == 0) {

        $file = $files["image"];
        if (!empty($file["image"]) || !in_array($file["type"], $upload::isAllowed())) {
            Banner::setError('Selecione uma imagem válida.');
            header("Location: /admin/banners/create");
            exit;
        } else {

            $image_thumb = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 350);
            $image = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 1920);

            $data['image'] = $image;
            $data['image_thumb'] = $image_thumb;
        }
    }

    $banner->setData($data);
    $banner->save();
    unset($_SESSION['recoversPost']);
    header("Location:/admin/banners");
    exit;
});

$app->get("/admin/banners/create", function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("banners-create", [
        'msgError' => Banner::getError(),
    ]);
});

//Delete
$app->get("/admin/banners/:id/delete", function ($id) {

    User::verifyLogin();
    $banner = new Banner();
    $banner->get((int) $id);

    if (file_exists($banner->getimage())) {
        unlink($banner->getimage());
        unlink($banner->getimage_thumb());
    }
    $banner->delete();
    header("Location: /admin/banners");
    exit;
});

//Edit
$app->get("/admin/banners/:id", function ($id) {

    User::verifyLogin();

    $banner = new Banner();
    $banner->get((int) $id);
    $page = new PageAdmin();
    
    $page->setTpl("banners-update", [
        "banner" => $banner->getValues(),
        'msgError' => Banner::getError()
    ]);
});

//Update
$app->post("/admin/banners/:id", function ($id) {

    User::verifyLogin();
    $banner = new Banner();
    $upload  = new Image("storage", "images");
    $banner->get((int) $id);

    $files = $_FILES;

    if ($files['image']['error'] == 0) {
        /*Primeiro apaga imagem anterior*/
        if (file_exists($banner->getimage())) {
            unlink($banner->getimage());
            unlink($banner->getimage_thumb());
        }
        /*Trata a nova imagem*/
        $file = $files["image"];

        if (!empty($file["image"]) || !in_array($file["type"], $upload::isAllowed())) {
            Banner::setError('Selecione uma imagem válida.');
            header("Location: /admin/banners/{$id}");
            exit;
        } else {
            $_POST['image'] = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 1920);
            $_POST['image_thumb'] = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 350);
        }
    }
    $banner->setData($_POST);
    $banner->save();
    header("Location: /admin/banners");
    exit;
});
