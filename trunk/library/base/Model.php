<?php

/**
 * Base Model
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 *
 * Use the following convention
 * 
 * getBySomething       obtain a single record and returns as an model class object
 * findBySomething      obtain zero or more records as array of objects
 * 
 * insertSomething, updateSomething, foobarSomething, etc
 */

if(class_exists('PDO')==false)
{
    echo "<pre>";
    echo "class PDO not found\n";
    echo "</pre>";
    exit(1);
}

abstract class B_Model
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
     * Table structure
     *
     * @var array
     */
    protected static $table_structure;

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
                $value = is_integer($value) ? 
                    date("Y-m-d H:i:s", $value) :
                    date("Y-m-d H:i:s", strtotime($value));
            }
            else
            {
                settype($value, $type);
            }
        }

        $this->data[$name] = $value;
    }

    /**
     * Populate model data
     *
     * @param   array   $data
     */
    public function populate($data)
    {
        $structure = $this->getTableStructure();

        foreach($data as $name => $value)
        {
            if(array_key_exists($name, $structure))
            {
                $this->{$name} = $value;
            }
        }
    }

    /**
     * Dump model data
     *
     * @param   array   $keys
     * @return  array
     */
    public function dump($keys=array())
    {
        if(count($keys) == 0)
        {
            $keys = array_keys($this->getTableStructure());
        }

        $dump = array();

        foreach($keys as $name)
        {
            $dump[$name] = $this->{$name};
        }

        return $dump;
    }

    /**
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        $connection = self::connection();
        $saved = false;

        $this->sanitize();

        /* ignore non-column data and split name=>value */

        $structure = $this->getTableStructure();
        $scolumns = array_keys($structure);
        $dcolumns = array_keys($this->data);

        $ndata = array_diff($dcolumns, $scolumns);
        $datak = array();
        $datav = array();
        $datac = 0;

        foreach($this->data as $name => $value)
        {
            if(in_array($name, $ndata)==false)
            {
                $datak[] = $name;
                $datav[] = $value;
                $datac++;
            }
        }

        if($this->isNew())
        {
            $sql = "INSERT INTO " . $this->getTableName() . " " . 
                   "(" . implode(", ", $datak) . ") VALUES " .
                   "(?" . str_repeat(", ?", $datac - 1) . ")";

            $id = self::insert_($sql, $datav, $this->getSequenceName());

            $this->setPrimaryKey($id);
            $saved = ($id > 0);
        }
        else
        {
            $sql = "UPDATE " . $this->getTableName() .
                   " SET " . implode(" = ?, ", $datak) . " = ?" .
                   " WHERE " . $this->getPrimaryKeyName() . " = ?";
            
            array_push($datav, $this->getPrimaryKey());

            $affected = self::execute($sql, $datav);
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
     * @throws  B_Exception
     * @return  void
     */
    protected function sanitize()
    {
        $structure = $this->getTableStructure();
        $columns = array_keys($structure);

        /* auto set created_at */

        $current_time = time();

        if($this->isNew() == true && in_array('created_at', $columns))
        {
            $this->created_at = $current_time;
        }

        /* auto set updated_at */

        if(in_array('updated_at', $columns))
        {
            $this->updated_at = $current_time;
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
                    throw new B_Exception($_m, E_WARNING, $_d);
                }
            }
        }
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
                $statement = self::connection()->prepare($sql);
                $statement->execute($data);
                $affected = $statement->rowCount();
            }
            catch(PDOException $exception)
            {
                $_m = "execute sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_ERROR, $exception, $_d);
            }
        }
        else
        {
            try
            {
                $affected = (int) self::connection()->exec($sql);
            }
            catch(PDOException $exception)
            {
                $_m = "execute sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_ERROR, $exception, $_d);
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
    protected static function insert_ ($sql, $data=array(), $sequence=null)
    {
        $id = null;

        if(self::execute($sql, $data) > 0)
        {
            $connection = self::connection();
            $id = ($sequence ? 
                $connection->lastInsertId($sequence) : 
                $connection->lastInsertId());
        }

        return $id;
    }

    protected static function insert ($sql, $data=array())
    {
        // abstract
    }

    /**
     * Execute a SQL select query and returns array of (assoc, obj, class, etc.)
     *
     * @param   string  $sql    SQL query
     * @param   array   $data   values array
     * @param   integer $method @see http://br.php.net/manual/en/pdo.constants.php
     * @param   string  $model  Model class name
     * 
     * @return  array
     */
    public static function select($sql, 
                                  $data=array(), 
                                  $method=PDO::FETCH_OBJ, 
                                  $model=null)
    {
        $statement = null;

        if(count($data) > 0)
        {
            try
            {
                $statement = self::connection()->prepare($sql);
                $statement->setFetchMode($method, $model);
                $statement->execute($data);
            }
            catch(PDOException $exception)
            {
                $_m = "select sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_ERROR, $exception, $_d);
            }
        }
        else
        {
            try
            {
                $statement = self::connection()->query($sql, $method, $model);
            }
            catch(PDOException $exception)
            {
                $_m = "select sql (" . $sql . ") failed";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_ERROR, $exception, $_d);
            }
        }

        return $statement->fetchAll();
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
    public static function connection($database='default')
    {
        if(($db = B_Registry::get('database')->{$database}()) == null)
        {
            $_m = "database (" . $database . ") does not exists in registry";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_ERROR, $_d);
        }
        
        if($db->connection == null) self::setupConnection($db);

        return $db->connection;
    }

    /**
     * Transaction
     */
    public static function transaction()
    {
        self::execute("START TRANSACTION");
    }

    /**
     * Commit
     */
    public static function commit()
    {
        self::execute("COMMIT");
    }

    /**
     * Rollback
     */
    public static function rollback()
    {
        self::execute("ROLLBACK");
    }

    /**
     * Set up connection
     *
     * @param   B_Registry     $db
     * @return  void
     */
    private static function setupConnection($db)
    {
        try
        {
            $uri = $db->driver . ":host=" . $db->host . ";dbname=" . $db->db;
            $db->connection = new PDO ($uri, $db->username, $db->password);
            $db->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $exception)
        {
            echo "<pre>";
            echo "database connection failed;\n" . $exception->getMessage();
            echo "</pre>";
            exit(1);
        }
    }
}
