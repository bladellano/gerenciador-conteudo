
<?php

use Source\PageAdmin;
use Source\Model\User;
use CoffeeCode\Uploader\Image;
use Source\Model\Photo;
use Source\Model\PhotoAlbum;

$app->get('/admin/albums', function () {

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
            'href' => '/admin/albums?' . http_build_query([
                'page' => $x + 1,
                'search' => $search
            ]),
            'text' => $x + 1
        ]);
    }

    $page = new PageAdmin();
    $page->setTpl("albums", array(
        "albums" => $pagination['data'],
        "search" => $search,
        "pages" => $pages
    ));
});

//Change cover
$app->get('/admin/albums/change-cover/:id_photo/:id_album', function ($id_photo, $id_album) {
    User::verifyLogin();
    $album = new PhotoAlbum();
    $album->get((int) $id_album);
    $album->setData(['id_photos_cover' => $id_photo]);
    $album->save();
    header("Location:/admin/show-album/{$id_album}");
    exit;
});

//Create new name album
$app->post('/admin/albums/create-name', function () {

    User::verifyLogin();
    $album = new PhotoAlbum();

    $exist = $album->verifyNameAlbum($_POST['album']);

    if (count($exist) != 0)
        die(json_encode(['success' => false, 'msg' => '&bull; Nome já existente na base dados!']));

    $album->setData($_POST);
    $album->save();

    $allAlbums = $album->listAll();
    die(json_encode(['success' => true, 'msg' => 'Registrado com sucesso!', 'data' => $allAlbums]));
});

//Create album with photos

$app->post('/admin/albums/create', function () {

    User::verifyLogin();

    $upload  = new Image("storage/images", "albums");
    $photo = new Photo();

    $data = filter_var_array($_POST, FILTER_SANITIZE_STRING);

    $files = $_FILES['images'];

    /**
     * Normalization
     */
    for ($i = 0; $i < count($files["type"]); $i++) {
        foreach (array_keys($files) as $keys) {
            $newFiles[$i][$keys] = $files[$keys][$i];
        }
    }

    $_SESSION['recoversPost'] = $_POST;

    if (in_array("", $data)) {
        Photo::setError('Preencha todos os campos.');
        header("Location: /admin/albums/create");
        exit;
    }

    foreach ($newFiles as $file) {
        if (!empty($file["image"]) || !in_array($file["type"], $upload::isAllowed())) {
            Photo::setError('Selecione uma imagem válida.');
            header("Location: /admin/albums/create");
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
    header("Location:/admin/albums");
    exit;
});

//Form

$app->get("/admin/albums/create", function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $albums = (new PhotoAlbum())->listAll();
    $page->setTpl("albums-create", [
        'msgError' => Photo::getError(),
        'albums' => $albums
    ]);
});

//Delete
$app->get("/admin/albums/:id/delete", function ($id) {
    User::verifyLogin();
    $album = new PhotoAlbum();
    $album->get((int) $id);

    $aPhotos = (new Photo())->getPhotosFromAlbum($id);

    foreach ($aPhotos as $photo) {

        $oPhoto = new Photo();
        $oPhoto->get((int) $photo['id']);

        if (file_exists($photo['image'])) {
            unlink($photo['image']);
            unlink($photo['image_thumb']);
        }
        $oPhoto->delete();
    }
    $album->delete();
    header("Location: /admin/albums");
    exit;
});


//Show all photos
$app->get("/admin/show-album/:id", function ($id) {

    User::verifyLogin();

    $oAlbum = new PhotoAlbum();
    $page = new PageAdmin();

    $allPhotos = $oAlbum->getPhotos((int) $id);
    $oAlbum->get((int) $id);

    $page->setTpl("show-albums", [
        "photos" => $allPhotos,
        "album" => $oAlbum->getValues(),
        'msgError' => PhotoAlbum::getError()
    ]);
});

//Edit
$app->get("/admin/albums/:id", function ($id) {

    User::verifyLogin();

    $album = new PhotoAlbum();

    $album->get((int) $id);
    $page = new PageAdmin();
    $page->setTpl("albums-update", [
        "album" => $album->getValues(),
        'msgError' => PhotoAlbum::getError()
    ]);
});

//Update
$app->post("/admin/albums/:id", function ($id) {

    User::verifyLogin();
    $album = new PhotoAlbum();
    $album->get((int) $id);

    $album->setData($_POST);
    $album->save();
    header("Location: /admin/albums");
    exit;
});
