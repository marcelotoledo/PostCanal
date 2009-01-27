<?php

/**
 * Model
 * 
 * @category    Autoblog
 * @package     AB
 */
abstract class AB_Model
{
    /**
     * Model data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name;

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name;

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name;


    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get ($name)
    {
        $value = null;

        if(array_key_exists($name, $this->data))
        {
            $value = $this->data[$name];
        }

        return $value;
    }

    /**
     * Set overloading
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function __set ($name, $value)
    {
        /* fix boolean */
        if(is_bool($value)) $value = ($value == true) ? 1 : 0;

        $this->data[$name] = $value;
    }

    /**
     * Save model
     *
     * @param   string      Sequence name
     * 
     * @return  boolean
     */
    public function _save($sequence)
    {
        $connection = self::getConnection();
        $saved = false;

        if($this->isNew())
        {
            $columns = array_keys($this->data);

            $sql = "INSERT INTO " . $this->getTableName() . " " . 
                   "(" . implode(", ", $columns) . ") VALUES " .
                   "(?" . str_repeat(", ?", count($columns) - 1) . ")";

            $id = self::_insert($sql, array_values($this->data), $sequence);
            $this->setPrimaryKey($id);
            $saved = ($id > 0);
        }
        else
        {
            $values = array();

            foreach($this->data as $name => $value)
            {
                if($name != $this->getPrimaryKeyName())
                {
                    $arguments[] = $name . " = ?";
                    $values[] = $value;
                }
            }

            $sql = "UPDATE " . $this->getTableName() . 
                   "   SET " . implode(", ", $arguments) . 
                   " WHERE " . $this->getPrimaryKeyName() . " = ?";
            
            array_push($values, $this->getPrimaryKey());

            $affected = self::execute($sql, $values);
            $saved = ($affected > 0);
        }

        return $saved;
    }

    abstract protected function save();

    /**
     * Delete model
     *
     * @return  boolean
     */
    public function delete()
    {
        $sql = "DELETE FROM " . $this->getTableName() . 
               "      WHERE " . $this->getPrimaryKeyName() . " = ?";

        return (self::execute($sql, array($this->getPrimaryKey())) > 0);
    }

    /**
     * Check if model is new
     *
     * @return  boolean
     */
    public function isNew()
    {
        return is_null($this->getPrimaryKey());
    }

    /**
     * Set model primary key
     *
     * @param   mixed   $value
     * @return  void
     */
    public function setPrimaryKey($value)
    {
        $this->data[$this->getPrimaryKeyName()] = $value;
    }

    /**
     * Get primary key value
     *
     * @return  array
     */
    public function getPrimaryKey()
    {
        return array_key_exists($this->getPrimaryKeyName(), $this->data) ? 
            $this->data[$this->getPrimaryKeyName()] : null;
    }

    /**
     * Find models with an encapsulated SELECT command
     *
     * @param   array   $conditions WHERE parameters
     * @param   array   $order      ORDER parameters
     * @param   integer $limit      LIMIT parameter
     * @param   integer $offset     OFFSET parameter
     * @param   string  $table      Table name
     * @param   string  $model      Model name
     * @return  array
     */
    protected static function _find ($conditions=array(), 
                                     $order=array(), 
                                     $limit=0, 
                                     $offset=0,
                                     $table,
                                     $model)
    {
        $prepared = array();

        $columns = array_keys($conditions);

        foreach($columns as $column)
        {
            $prepared[] = $column . " = ?";
        }

        $sql = "SELECT * FROM " . $table;

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

        return self::_selectModel($sql, array_values($conditions), $model);
    }

    abstract protected static function find($conditions=array(), 
                                            $order=array(), 
                                            $limit=0, 
                                            $offset=0);

    /**
     * Get models with SQL
     *
     * @param   string  $sql        SQL query
     * @param   array   $data       values array
     * @param   string  $model      Model class name
     * @return  array
     */
    protected static function _selectModel ($sql, $data=array(), $model)
    {
        $statement = null;

        if(count($data) > 0)
        {
            try
            {
                $statement = self::getConnection()->prepare($sql);
                $statement->setFetchMode(PDO::FETCH_CLASS, $model);
                $statement->execute($data);
            }
            catch(PDOException $exception)
            {
                $message = $exception->getMessage() . "; ";
                $message.= $exception->getTraceAsString();
                throw new Exception($message);
            }
        }
        else
        {
            try
            {
                $statement = self::getConnection()->query($sql, 
                                                          PDO::FETCH_CLASS, 
                                                          $model);
            }
            catch(PDOException $exception)
            {
                $message = $exception->getMessage() . "; ";
                $message.= $exception->getTraceAsString();
                throw new Exception($message);
            }
        }
 
        return $statement->fetchAll();
    }

    abstract protected static function selectModel ($sql, $data=array());

    /**
     * Execute a SQL query and returns affected rows
     *
     * @param   string  $sql    SQL query
     * @param   array   $data   values array
     * @return  integer
     */
    public static function execute($sql, $data=array())
    {
        $affected = 0;

        if(count($data) > 0)
        {
            try
            {
                $statement = self::getConnection()->prepare($sql);
                $statement->execute($data);
                $affected = $statement->rowCount();
            }
            catch(PDOException $exception)
            {
                $message = $exception->getMessage() . "; ";
                $message.= $exception->getTraceAsString();
                throw new Exception($message);
            }
        }
        else
        {
            try
            {
                $affected = (int) self::getConnection()->exec($sql);
            }
            catch(PDOException $exception)
            {
                $message = $exception->getMessage() . "; ";
                $message.= $exception->getTraceAsString();
                throw new Exception($message);
            }
        }

        return $affected;
    }

    /**
     * Execute a SQL insert query and returns last insert id
     *
     * @param   string  $sql            SQL query
     * @param   array   $data           values array
     * @param   array   $sequence       Sequence name
     * @return  integer
     */
    protected static function _insert($sql, $data=array(), $sequence)
    {
        $id = null;

        if(self::execute($sql, $data) > 0)
        {
            $id = self::getConnection()->lastInsertId($sequence);
        }

        return $id;
    }

    abstract protected static function insert($sql, $data=array());

    /**
     * Execute a SQL select query and returns array of objects
     *
     * @param   string  $sql    SQL query
     * @param   array   $data   values array
     * @return  array
     */
    public static function select($sql, $data=array())
    {
        $statement = null;

        if(count($data) > 0)
        {
            try
            {
                $statement = self::getConnection()->prepare($sql);
                $statement->setFetchMode(PDO::FETCH_OBJ);
                $statement->execute($data);
            }
            catch(PDOException $exception)
            {
                $message = $exception->getMessage() . "; ";
                $message.= $exception->getTraceAsString();
                throw new Exception($message);
            }
        }
        else
        {
            try
            {
                $statement = self::getConnection()->query($sql, PDO::FETCH_OBJ);
            }
            catch(PDOException $exception)
            {
                $message = $exception->getMessage() . "; ";
                $message.= $exception->getTraceAsString();
                throw new Exception($message);
            }
        }

        return $statement->fetchAll();
    }

    /**
     * Execute a SQL select query and returns a single row as object
     *
     * @param   string  $sql    SQL query
     * @param   array   $data   values array
     * @return  object
     */
    public static function selectRow($sql, $data=array())
    {
        return current(self::select($sql, $data));
    }

    abstract protected function getTableName();
    abstract protected function getSequenceName();
    abstract protected function getPrimaryKeyName();

    /**
     * Get database connection
     *
     * @return  PDO
     */
    public static function getConnection()
    {
        $registry = AB_Registry::singleton();
        
        if(empty($registry->database->connection))
        {
            try
            {
                $registry->database->connection = new PDO
                (
                    $registry->database->driver . ":host=" . 
                    $registry->database->host . ";dbname=" . 
                    $registry->database->db, 
                    $registry->database->username,
                    $registry->database->password
                );

                $registry->database->connection->setAttribute(
                    PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $exception)
            {
                $message = "database connection failed; ";
                $message.= $exception->getMessage() . "; ";
                $message.= $exception->getTraceAsString();
                throw new Exception($message);
            }
        }

        return $registry->database->connection;
    }
}
