<?php

class Database  
{
    private $DB_HOST = DB_HOST;
    private $DB_USER = DB_USER;
    private $DB_PASS = DB_PASSWORD;
    private $DB_NAME = DB_NAME;
    
    private $dbh;
    private $stmt;
    private $error;


    public function __construct()
    {
        $dsn = 'mysql:host=' . $this->DB_HOST.';dbname='.$this->DB_NAME;
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->DB_USER, $this->DB_PASS, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }

    }

    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind($param, $value, $type = NULL)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                     $type = PDO::PARAM_STR;
                    break;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute()
    {
        try {
            $this->stmt->execute();
            return true;
        } catch (\Throwable $th) {
            var_dump($th);
            return false;
        }
    }

    public function resultSet($type = PDO::FETCH_OBJ)
    {
        $this->execute();
        return $this->stmt->fetchAll($type);
    }

    public function single($type = PDO::FETCH_OBJ)
    {
        $this->execute();
        return $this->stmt->fetch($type);
    }

    public function rowCount()
    {
        $this->execute();
        return $this->stmt->rowCount();
    }

}
