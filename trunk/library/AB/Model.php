<?php

/* AUTOBLOG MODEL CLASS */


abstract class AB_Model
{
    protected $data = array();

    protected $table_name;
    protected $primary_key;


    public function __get ($name)
    {
        $value = null;

        if(array_key_exists($name, $this->data))
        {
            $value = $this->data[$name];
        }

        return $value;
    }

    public function __set ($name, $value)
    {
        $this->data[$name] = $value;
    }

    protected function _find (
        $conditions=array(), $order=array(), $limit=0, $offset=0)
    {
        $prepared = array();

        $columns = array_keys($conditions);

        foreach($columns as $column)
        {
            $prepared[] = $column . " = :" . $column;
        }

        $sql = "SELECT * FROM " . $this->table_name;

        if(count($conditions) > 0)
        {
            $sql.= " WHERE " . implode(" AND ", $prepared);
        }

        if(count($order) > 0)
        {
            $sql.= " ORDER BY " . implode(", ", $order);
        }

        if(($limit = intval($limit)) > 0)
        {
            $sql.= " LIMIT " . $limit;

            if(($offset = intval($offset)) > 0)
            {
                $sql.= ", " . $offset;
            }
        }

        return $this->_selectModel($sql, $conditions);
    }

    public static function find (/* VOID */)
    {
        /* TODO get_class($this) can be replaced by get_called_class() in php >= 5.3 */
    }

    public function _selectModel ($sql, $data=array())
    {
        $statement = self::_statement($sql, $data);

        $statement->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function selectModel (/* VOID */)
    {
        /* TODO get_class($this) can be replaced by get_called_class() in php >= 5.3 */
    }

    public function save()
    {
        $connection = self::getConnection();

        if($this->isNew())
        {
            $columns = array_keys($this->data);

            $sql = "INSERT INTO " . $this->table_name .
                   "(" . implode(", ", $columns) . ") VALUES " .
                   "(:" . implode(", :", $columns) . ")";

            $this->data[$this->primary_key] = self::insert($sql, $this->data);
        }
        else
        {
            $arguments = array();

            foreach($data as $key => $value)
            {
                if($key != $this->primary_key)
                {
                    $arguments = $key . " = :" . $key;
                }
            }

            $sql = "UPDATE " . $this->table_name . 
                   "   SET " . implode(", ", $arguments) . 
                   " WHERE " . $this->primary_key . " = :" . $this->primary_key;
            
            self::execute($sql, $this->data);
        }
    }

    public function delete()
    {
        $sql = "DELETE FROM " . $this->table_name . 
               "      WHERE " . $this->primary_key . " = :" . $this->primary_key;

        return self::execute($sql, 
            array($this->primary_key => $this->data[$this->primary_key]));
    }

    public function isNew()
    {
        return is_null($this->data[$this->primary_key]);
    }

    public static function _statement($sql, $data=array())
    {
        $statement = self::getConnection()->prepare($sql);

        foreach($data as $column => $value)
        {
            $statement->bindParam(':' . $column, $value);
        }

        return $statement;
    }

    public static function execute($sql, $data=array())
    {
        return self::_statement($sql, $data)->execute();
    }

    public static function insert($sql, $data=array())
    {
        self::execute($sql, $data);

        return self::getConnection()->lastInsertId();
    }

    public static function select($sql, $data=array())
    {
        $statement = self::_statement($sql, $data);

        $statement->setFetchMode(PDO::FETCH_OBJ);

        return $statement->fetchAll();
    }

    public static function selectRow($sql, $data=array())
    {
        return current(self::select($sql, $data));
    }

    public static function getConnection()
    {
        $registry = AB_Registry::singleton();
        
        if($registry->connection == null)
        {
            try
            {
                $registry->connection = new PDO
                (
                    $registry->database_driver . ":host=" . 
                    $registry->database_host . ";dbname=" . 
                    $registry->database_db, 
                    $registry->database_username,
                    $registry->database_password
                );
            }
            catch(PDOException $exception)
            {
                $message = "database connection failed; PDOException: " . $exception;
                throw new Exception($message);
            }
        }

        return $registry->connection;
    }
}
