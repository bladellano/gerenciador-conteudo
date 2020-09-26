
<?php

use Source\Model\Article;

//=========================================//
//=====GERANDO REGISTRO PARA ARTIGOS=======//
//=========================================//

$app->get('/samples/articles', function () {

    $faker = Faker\Factory::create();

    for ($i = 0; $i < 10; $i++) {
        $data = [
            'title' => $faker->text,
            'description' => $faker->text,
            'slug' => $faker->slug,
            'image' => $faker->image('tmp', 640, 480),
            'image_thumb' => $faker->image('tmp', 340, 280),
            'keywords' => $faker->name,
            'author' => $faker->firstNameMale,
            'resume' => $faker->text,
            'qtd_access' => rand(1, 100),
            'spotlight' => 1,
            'id_articles_categories' => 5,
            'show_author' => 1,
            'idperson' => 1
        ];

        (new Article())->setData($data)->save($data);
    }
    die("Total de registro(s) inserido:" . $i);
});
