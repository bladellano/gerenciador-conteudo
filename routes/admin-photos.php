
<?php

use Source\PageAdmin;
use Source\Model\User;
use CoffeeCode\Uploader\Image;
use Source\Model\Photo;
use Source\Model\PhotoAlbum;

$app->get('/admin/fotos_x', function () {
    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '') {
        $pagination = Photo::getPageSearch(trim($search), $page);
    } else {
        $pagination = Photo::getPage($page);
    }

    $pages = [];

    for ($x = 0; $x <  $pagination['pages']; $x++) {
        array_push($pages, [
            'href' => '/admin/fotos_?' . http_build_query([
                'page' => $x + 1,
                'search' => $search
            ]),
            'text' => $x + 1
        ]);
    }

    $page = new PageAdmin();
    $page->setTpl("photos_", array(
        "photos" => $pagination['data'],
        "search" => $search,
        "pages" => $pages
    ));
});

$app->get('/admin/fotos', function () {
    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '') {
        $pagination = PhotoAlbum::getPageSearch(trim($search), $page);
    } else {
        $pagination = PhotoAlbum::getPage($page);
    }

    $pages = [];

    for ($x = 0; $x <  $pagination['pages']; $x++) {
        array_push($pages, [
            'href' => '/admin/fotos?' . http_build_query([
                'page' => $x + 1,
                'search' => $search
            ]),
            'text' => $x + 1
        ]);
    }

    $page = new PageAdmin();
    $page->setTpl("photos", array(
        "albums" => $pagination['data'],
        "search" => $search,
        "pages" => $pages
    ));
});

$app->post('/admin/fotos/create', function () {

    User::verifyLogin();

    $upload  = new Image("storage", "photos");
    $photo = new Photo();

    $data = filter_var_array($_POST, FILTER_SANITIZE_STRING);

    $files = $_FILES['images'];

    /* Normalização */
    for ($i = 0; $i < count($files["type"]); $i++) {
        foreach (array_keys($files) as $keys) {
            $newFiles[$i][$keys] = $files[$keys][$i];
        }
    }

    $_SESSION['recoversPost'] = $_POST;

    if (in_array("", $data)) {
        Photo::setError('Preencha todos os campos.');
        header("Location: /admin/fotos/create");
        exit;
    }

    foreach ($newFiles as $file) {
        if (!empty($file["image"]) || !in_array($file["type"], $upload::isAllowed())) {
            Photo::setError('Selecione uma imagem válida.');
            header("Location: /admin/fotos/create");
            exit;
        } else {
            $image_thumb = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 250);
            $image = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 1020);

            $data['image'] = $image;
            $data['image_thumb'] = $image_thumb;
            $photo->setData($data);
            $photo->save();
        }
    }
    unset($_SESSION['recoversPost']);
    header("Location:/admin/fotos");
    exit;
});

$app->get("/admin/fotos/create", function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $albums = (new PhotoAlbum())->listAll();
    $page->setTpl("photos-create", [
        'msgError' => Photo::getError(),
        'albums' => $albums
    ]);
});

//Delete
$app->get("/admin/photos/:id/delete", function ($id) {

    User::verifyLogin();
    $photo = new Photo();
    $photo->get((int) $id);
    $idAlbum = $photo->getid_photos_albums();

    if (file_exists($photo->getimage())) {
        unlink($photo->getimage());
        unlink($photo->getimage_thumb());
    }

    $photo->delete();
    header("Location: /admin/show-album/{$idAlbum}");
    exit;
});


//Show all photos
$app->get("/admin/fotos_/:id", function ($id) {

    User::verifyLogin();

    $oAlbum = new PhotoAlbum();
    $page = new PageAdmin();

    $allPhotos = $oAlbum->getPhotos((int) $id);
    $oAlbum->get((int) $id);
    
    $page->setTpl("show-photos", [
        "photos" => $allPhotos,
        "album" => $oAlbum->getValues(),
        'msgError' => PhotoAlbum::getError()
        ]);
});

//Edit
$app->get("/admin/fotos/:id", function ($id) {

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
$app->post("/admin/fotos/:id", function ($id) {

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
            header("Location: /admin/fotos/{$id}");
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
    header("Location: /admin/fotos");
    exit;
});
