<?php

/**
 * Model
 * 
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
abstract class AB_Model
{
    /** 
     * column structure constants 
     */
    const STRUCTURE_TYPE     = 'type';
    const STRUCTURE_SIZE     = 'size';
    const STRUCTURE_REQUIRED = 'required';

    /** 
     * column type constants 
     */
    const TYPE_STRING  = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT   = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DATE    = 'date';


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

            $structure = $this->getTableStructure();

            /* set variable type */

            if(array_key_exists($name, $structure))
            {
                $type = $structure[$name][self::STRUCTURE_TYPE];

                if($type == self::TYPE_DATE)
                {
                    $value = strtotime($value);
                }
                else
                {
                    settype($value, $type);
                }
            }
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
        if(is_bool($value)) $value = ($value == true) ? 1 : 0;

        $structure = $this->getTableStructure();

        /* set variable type */

        if(array_key_exists($name, $structure))
        {
            $type = $structure[$name][self::STRUCTURE_TYPE];
            $size = $structure[$name][self::STRUCTURE_SIZE];

            if($type == self::TYPE_BOOLEAN) 
            {
                settype($value, 'integer');
            }
            elseif($type == self::TYPE_DATE)
            {
                if(($time = intval($value)) > 0)
                    $value = date("Y-m-d H:i:s", $time);
            }
            else
            {
                settype($value, $type);
            }
        }

        $this->data[$name] = $value;
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

        $this->sanitize();

        if($this->isNew())
        {
            $columns = array_keys($this->data);

            $sql = "INSERT INTO " . $this->getTableName() . " " . 
                   "(" . implode(", ", $columns) . ") VALUES " .
                   "(?" . str_repeat(", ?", count($columns) - 1) . ")";

            $id = self::_insert($sql, 
                                array_values($this->data), 
                                $this->getSequenceName());

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
                   " SET " . implode(", ", $arguments) .
                   " WHERE " . $this->getPrimaryKeyName() . " = ?";
            
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
        $sql = "DELETE FROM " . $this->getTableName() .
               " WHERE " . $this->getPrimaryKeyName() . " = ?";

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
     * Sanitize model
     *
     * @throws  AB_Exception
     * @return  void
     */
    protected function sanitize()
    {
        $structure = $this->getTableStructure();
        $columns = array_keys($structure);

        /* auto set (if exists) updated_at when model is not new */

        if(!$this->isNew() && in_array('updated_at', $columns))
        {
            $this->updated_at = time();
        }

        /* check data for errors */

        foreach($structure as $column => $settings)
        {
            /* truncate */

            if(($size = $settings[self::STRUCTURE_SIZE]) > 0)
            {
                if(strlen($this->{$column}) > $size)
                {
                    $this->{$column} = substr($this->{$column}, 0, $size);
                }
            }

            /* required */

            if(($required = $settings[self::STRUCTURE_REQUIRED]) == true)
            {
                if(is_null($this->{$column}))
                {
                    $_m= "column (" . $column . ") is required";
                    $_d = array('method' => __METHOD__);
                    throw new AB_Exception($_m, E_USER_WARNING, $_d);
                }
            }
        }
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
     * @throw   AB_Exception
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
                $_m = "select model (" . $model . ") with sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                AB_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
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
                $_m = "select model (" . $model . ") with sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                AB_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
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
                $_m = "execute sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                AB_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
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
                $_m = "execute sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                AB_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
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
                $_m = "select sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                AB_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
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
                $_m = "select sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                AB_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
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
    abstract protected function getTableStructure();
    abstract protected function getSequenceName();
    abstract protected function getPrimaryKeyName();

    /**
     * Get database connection
     *
     * @return  PDO
     */
    public static function getConnection($database='default')
    {
        $registry = AB_Registry::singleton();
        $configuration = $registry->database->{$database};

        if(empty($configuration))
        {
            $_m = "database (" . $database . ") does not exists in registry";
            $_d = array('method' => __METHOD__);
            throw new AB_Exception($_m, E_USER_ERROR, $_d);
        }
        
        $has_connection = false;
        $connection = $registry->connection->{$database};

        if(get_class($connection) == "PDO")
        {
            $has_connection = true;
        }

        if($has_connection == false)
        {
            try
            {
                $connection = new PDO
                (
                    $configuration->driver . ":host=" . 
                    $configuration->host . ";dbname=" . 
                    $configuration->db, 
                    $configuration->username,
                    $configuration->password
                );

                $connection->setAttribute(
                    PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $registry->connection->{$database} = $connection;
            }
            catch(PDOException $exception)
            {
                $_m = "database connection failed";
                $_d = array ('method' => __METHOD__);
                AB_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
            }
        }

        /* set time zone */

        self::setTimeZone($database);

        return $connection;
    }

    /**
     * Set database timezone
     *
     * @return  void
     */
    private static function setTimeZone($database)
    {
        $registry = AB_Registry::singleton();
        $connection = $registry->connection->{$database};
        $configuration = $registry->database->{$database};

        if(!empty($connection) && 
           !empty($configuration->driver) && 
           !empty($configuration->timezone))
        {
            try
            {
                $driver = strtolower($configuration->driver);
                $timezone = $configuration->timezone;

                if($driver == "pgsql")
                {
                    $connection->exec("SET TIME ZONE '" . $timezone . "'");
                }
                elseif($driver == "mysql")
                {
                    $connection->exec("SET time_zone = '" . $timezone . "'");
                }
                else
                {
                    $_m = "no implementation to driver (" . $driver . ")";
                    $_d = array('method' => __METHOD__);
                    throw new AB_Exception($_m, E_USER_ERROR, $_d);
                }
            }
            catch(Exception $exception)
            {
                $_m = "failed to set the timezone";
                $_d = array ('method' => __METHOD__);
                AB_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
            }
        }
    }
}
