<?php

use Source\PageAdmin;
use Source\Model\Convenio;
use Source\Model\User;

$app->get('/admin/convenios', function () {

    User::verifyLogin();
    
    $search = (isset($_GET['search'])) ? $_GET['search']: "";    
    $page = (isset($_GET['page'])) ? (int)$_GET['page']: 1;
    if($search != ''){
        $pagination = Convenio::getPageSearch(trim($search),$page);
    } else {
        $pagination = Convenio::getPage($page);
    }   
    $pages = [];

    for ($x=0; $x <  $pagination['pages'] ; $x++) { 
        array_push($pages, [
            'href'=>'/admin/convenios?'.http_build_query([
                'page'=>$x+1,
                'search'=>$search
            ]),
            'text'=>$x+1
        ]);
    }

    $page = new PageAdmin();

    $page->setTpl("convenios",array(
        "convenios" => $pagination['data'],
        "search" => $search,
        "pages"=> $pages
    ));   

});

$app->get("/admin/convenios/create", function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("convenios-create",['msgError'=>Convenio::getError()]);
});

$app->post("/admin/convenios/create", function () {

    User::verifyLogin();

    $data = filter_var_array($_POST,FILTER_SANITIZE_STRING);

    if(in_array("",$data)){
        Convenio::setError('Preencha todos os campos.');        
        header("Location: /admin/convenios/create");
        exit;
    }   

    $convenio = new Convenio();
    $convenio->setData($data);

    $convenio->save();
    header("Location:/admin/convenios");
    exit;
});

$app->get("/admin/convenios/:id/delete", function ($id) {

    User::verifyLogin();
    $convenio = new Convenio();
    $convenio->get((int) $id);
    $convenio->delete();
    header("Location: /admin/convenios");
    exit;
});


$app->get("/admin/convenios/:id", function ($id) {

    User::verifyLogin();
    $convenio = new Convenio();
    $convenio->get((int) $id);
    $page = new PageAdmin();
    $page->setTpl("convenios-update", [
        "convenio" => $convenio->getValues(),
    ]);

});

$app->post("/admin/convenios/:id", function ($id) {

    User::verifyLogin();
    $convenio = new Convenio();
    $convenio->get((int) $id);
    $convenio->setData($_POST);
    $convenio->save();
    header("Location: /admin/convenios");
    exit;
});
 

