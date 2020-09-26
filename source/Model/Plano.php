<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Plano extends Model
{
    const ERROR = 'PlanoError';

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM planos ORDER BY id DESC");
    }

    /**
     * Insere o plano na base de dados.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();
                
        $results = $sql->select(
            "CALL sp_planos_save(:id, :plan, :description, :qtd_people, :travel, :percentage)",
            [
            ":id" => $this->getid(),
            ":plan" => $this->getplan(),
            ":description" => $this->getdescription(),
            ":qtd_people" => $this->getqtd_people(),
            ":travel" => ($this->gettravel() == 1) ? 1 : 0,
            ":percentage" => $this->getpercentage()
            ]
        );

        $this->setData($results[0]);
    }


    public static function setError($msg)
    {
        $_SESSION[Plano::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Plano::ERROR]) && $_SESSION[Plano::ERROR]) ? $_SESSION[Plano::ERROR] : '';
        Plano::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[Plano::ERROR] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM planos WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM planos WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM planos 
            ORDER BY id
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
            FROM planos 
            WHERE plan LIKE :search 
            ORDER BY plan
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
}//Fim Classe
