<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Banner extends Model
{
    const ERROR = 'BannerError';

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM banners ORDER BY id DESC");
    }

    /**
     * Insere o banner na base de dados.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();

        $results = $sql->select(
            "CALL sp_banners_save(:id,:title,:description,:slug,:image,:image_thumb,:align_text,:author,:resume,:idperson)",
            [
                ":id" => $this->getid(),
                ":title" => $this->gettitle(),
                ":description" => $this->getdescription(),
                ":slug" => $this->getslug(),
                ":image" => $this->getimage(),
                ":image_thumb" => $this->getimage_thumb(),
                ":align_text" => $this->getalign_text(),
                ":author" => $this->getauthor(),
                ":resume" => $this->getresume(),
                ":idperson" => $this->getidperson()
            ]
        );
        $this->setData($results[0]);
    }


    public static function setError($msg)
    {
        $_SESSION[Banner::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Banner::ERROR]) && $_SESSION[Banner::ERROR]) ? $_SESSION[Banner::ERROR] : '';
        Banner::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[Banner::ERROR] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM banners WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM banners WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM banners 
            ORDER BY id DESC
            LIMIT $start, $itensPerPage;
        "
        );

        $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            'data' => $results,
            'total' => (int) $resultTotal[0]['nrtotal'],
            'pages' => ceil($resultTotal[0]['nrtotal'] / $itensPerPage),
        ];
    }
    public static function getPageSearch($search, $page = 1, $itensPerPage = 3)
    {

        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM banners 
            WHERE title LIKE :search 
            ORDER BY title
            LIMIT $start, $itensPerPage;
        ",
            [
                ':search' => '%' . $search . '%'
            ]
        );

        $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            'data' => $results,
            'total' => (int) $resultTotal[0]['nrtotal'],
            'pages' => ceil($resultTotal[0]['nrtotal'] / $itensPerPage),
        ];
    }
}//End Classe
