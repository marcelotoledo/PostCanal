<?php

/**
 * Model
 * 
 * @category    Autoblog
 * @package     AB
 */
class AB_Model
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
    protected $table_name;

    /**
     * Sequence name
     *
     * @var string
     */
    protected $sequence_name = null;

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primary_key;


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
     * Find models with an encapsulated SELECT command
     *
     * @param   array   $conditions WHERE parameters
     * @param   array   $order      ORDER parameters
     * @param   integer $limit      LIMIT parameter
     * @param   integer $offset     OFFSET parameter
     * @return  array
     */
    protected function _find ($conditions=array(), 
                              $order=array(), 
                              $limit=0, 
                              $offset=0)
    {
        $prepared = array();

        $columns = array_keys($conditions);

        foreach($columns as $column)
        {
            $prepared[] = $column . " = ?";
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

        return $this->_selectModel($sql, array_values($conditions));
    }

    public static function find (/* void */)
    {
        /* TODO get_class($this) can be replaced by 
         * get_called_class() in php >= 5.3 */
    }

    /**
     * Get models with SQL
     *
     * @param   string  $sql    SQL query
     * @param   array   $data   values array
     * @return  array
     */
    protected function _selectModel ($sql, $data=array())
    {
        $statement = null;

        if(count($data) > 0)
        {
            try
            {
                $statement = self::getConnection()->prepare($sql);
                $statement->setFetchMode(PDO::FETCH_CLASS, get_class($this));
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
                                                          get_class($this));
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

    public static function selectModel (/* void */)
    {
        /* TODO get_class($this) can be replaced by 
         * get_called_class() in php >= 5.3 */
    }

    /**
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        $connection = self::getConnection();
        $saved = false;

        if($this->isNew())
        {
            $columns = array_keys($this->data);

            $sql = "INSERT INTO " . $this->table_name .
                   "(" . implode(", ", $columns) . ") VALUES " .
                   "(?" . str_repeat(", ?", count($columns) - 1) . ")";

            $id = $this->_insert($sql, array_values($this->data));
            $this->setPrimaryKey($id);
            $saved = ($id > 0);
        }
        else
        {
            $values = array();

            foreach($this->data as $key => $value)
            {
                if($key != $this->primary_key)
                {
                    $arguments[] = $key . " = ?";
                    $values[] = $value;
                }
            }

            $sql = "UPDATE " . $this->table_name . 
                   "   SET " . implode(", ", $arguments) . 
                   " WHERE " . $this->primary_key . " = ?";
            
            array_push($values, $this->getPrimaryKey());

            $affected = self::execute($sql, $values);
            $saved = ($affected > 0);
        }

        return $saved;
    }

    /**
     * Delete model
     *
     * @return  boolean
     */
    public function delete()
    {
        $sql = "DELETE FROM " . $this->table_name . 
               "      WHERE " . $this->primary_key . " = ?";

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
        $k = $this->primary_key;
        $this->data[$k] = $value;
    }

    /**
     * Get primary key value
     *
     * @return  array
     */
    public function getPrimaryKey()
    {
        $s = $this->primary_key;
        return array_key_exists($s, $this->data) ? $this->data[$s] : null;
    }


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
     * @param   string  $sql        SQL query
     * @param   array   $data       values array
     * @return  integer
     */
    protected function _insert($sql, $data=array())
    {
        $id = null;

        if(self::execute($sql, $data) > 0)
        {
            $id = self::getConnection()->lastInsertId($this->sequence_name);
        }

        return $id;
    }

    public static function insert (/* void */)
    {
        /* TODO get_class($this) can be replaced by 
         * get_called_class() in php >= 5.3 */
    }

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
