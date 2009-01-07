<?php

/* AUTOBLOG MODEL CLASS */


abstract class AB_Model
{
    protected $data = array();

    protected static $table_name;
    protected static $primary_key;


    public function __get($name)
    {
        $value = null;

        if(array_key_exists($name, $this->data))
        {
            $value = $this->data[$name];
        }

        return $value;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function save()
    {
        if($this->isNew())
        {
            $data_keys = array_keys($this->data);
            $data_values = array_values($this->data);

            $sql = "INSERT INTO " . self::$table_name . 
                   "(" . implode(", ", $data_keys) .   ") VALUES " .
                   "(" . implode(", ", $data_values) . ")";

            $this->data[self::$primary_key] = self::insert($sql);
        }
        else
        {
            $arguments = array();

            foreach($data as $key => $value)
            {
                if($key != self::$primary_key)
                {
                    $arguments = $key . " = " . $value;
                }
            }

            $id = $this->data[self::$primary_key];

            $sql = "UPDATE " . self::$table_name . 
                   "   SET " . implode(", ", $arguments) . 
                   " WHERE " . self::$primary_key . " = " . $id;
            
            self::execute($sql);
        }
    }

    public function delete()
    {
        $id = $this->data[self::$primary_key];

        $sql = "DELETE FROM " . self::$table_name . 
               "      WHERE " . self::$primary_key . " = " . $id;
    }

    public function isNew()
    {
        isset($this->data[self::$primary_key]);
    }

    public static function execute($sql)
    {
        return self::getConnection()->exec($sql);
    }

    public static function insert($sql)
    {
        self::execute($sql);
        return self::getConnection()->lastInsertId();
    }

    public static function select($sql)
    {
        $statement = self::getConnection()->query($sql);
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public static function selectRow($sql)
    {
        $result = self::select($sql);
        return $result ? current($result) : array();
    }

    public static function selectColumn($sql)
    {
        $result = self::selectOne($sql);
        return $result ? current($result) : null;
    }

    public static function selectModel($sql)
    {
        $statement = self::getConnection()->query($sql);
        return $statement->fetchAll(PDO::FETCH_CLASS, self);
    }

    public static function selectModelWhere($parameters)
    {
        $arguments = array();

        foreach($parameters as $key => $value)
        {
            $arguments[] = $key . " = " . $value;
        }

        $sql = "SELECT * FROM " . self::$table_name . 
               "        WHERE " . implode(" AND ", $arguments);

        $results = self::selectModel($sql);

        return count($results) == 1 ? current($results) : $results;
    }

    public static function getConnection()
    {
        $registry = AB_Registry::singleton();
        
        if(is_null($registry->connection) == true)
        {
            $registry->connection = new PDO
            (
                $registry->database_driver . ":dbname=" . 
                $registry->database_db . ";host=" . 
                $registry->database_host, 
                $registry->database_username,
                $registry->database_password
            );
        }

        return $registry->connection;
    }
}
