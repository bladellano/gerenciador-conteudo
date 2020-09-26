<?php

use Source\PageAdmin;
use Source\Model\Plano;
use Source\Model\User;

//List All
$app->get('/admin/planos', function () {

    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search']: "";    
    $page = (isset($_GET['page'])) ? (int)$_GET['page']: 1;
    if($search != ''){
        $pagination = Plano::getPageSearch(trim($search),$page);
    } else {
        $pagination = Plano::getPage($page);
    }   

    $pages = [];

    for ($x=0; $x <  $pagination['pages'] ; $x++) { 
        array_push($pages, [
            'href'=>'/admin/planos?'.http_build_query([
                'page'=>$x+1,
                'search'=>$search
            ]),
            'text'=>$x+1
        ]);
    }

    $page = new PageAdmin();

    $page->setTpl("planos",array(
        "planos" => $pagination['data'],
        "search" => $search,
        "pages"=> $pages
    ));   

});

$app->get("/admin/planos/create", function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("planos-create",['msgError'=>Plano::getError()]);
});

//Create
$app->post("/admin/planos/create", function () {

    User::verifyLogin();

    $data = filter_var_array($_POST,FILTER_SANITIZE_STRING);

    if(in_array("",$data)){
        Plano::setError('Preencha todos os campos.');        
        header("Location: /admin/planos/create");
        exit;
    }   

    $plano = new Plano();    
    $plano->setData($data);  
    $plano->save();
    header("Location:/admin/planos");
    exit;
});

//Delete
$app->get("/admin/planos/:id/delete", function ($id) {

    User::verifyLogin();
    $plano = new Plano();
    $plano->get((int) $id);
    $plano->delete();
    header("Location: /admin/planos");
    exit;
});

//Edit
$app->get("/admin/planos/:id", function ($id) {

    User::verifyLogin();
    $plano = new Plano();
    $plano->get((int) $id);
    $page = new PageAdmin();
    $page->setTpl("planos-update", [
        "plano" => $plano->getValues(),
    ]);

});

//Update
$app->post("/admin/planos/:id", function ($id) {
    User::verifyLogin();
    $plano = new Plano();
    $plano->get((int) $id);

    $_POST['travel'] = isset($_POST['travel']) ? 1 : 0;

    $plano->setData($_POST);
    $plano->save();
    header("Location: /admin/planos");
    exit;
});
 

