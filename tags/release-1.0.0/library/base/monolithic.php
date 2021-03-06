<?php

/**
 * Base Bootstrap
 *
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Bootstrap
{
    /**
     * Default controller name
     *
     * @var string
     */
    public $default_controller = "index";

    /**
     * Default action name
     *
     * @var string
     */
    public $default_action = "index";

    /**
     * Translation loading list
     *
     * @var array
     */
    public $translation_load = array();


    /**
     * Run bootstrap
     */
    public function run()
    {
        $has_error = false;
        $message = ((string) null); 

        /* initialize request and response */

        $request = new B_Request();
        $response = new B_Response();
        B_Registry::set('request/object',  $request);
        B_Registry::set('response/object', $response);

        /* check controller */

        $controller_name = $request->getController();

        if(strlen($controller_name) == 0)
        {
            $controller_name = $this->default_controller;
        }

        if(($controller = self::factory($controller_name)) == null)
        {
            $has_error = true;
            $message = "controller (" . $controller_name . ") not found";
            $response->setStatus(B_Response::STATUS_NOT_FOUND);
        }
        else
        {
            /* check action */

            $action_name = $request->getAction();

            if(strlen($action_name) == 0)
            {
                $action_name = $this->default_action;
            }

            if($controller->check($action_name) == false)
            {
                $has_error = true;
                $message = "action (" . $action_name . ") not found";
                $response->setStatus(B_Response::STATUS_NOT_FOUND);
            }
            else
            {
                /* initialize view */

                $view = new B_View();
                $layout = strtolower($controller_name);
                $view->setLayout($layout);
                $template = ucfirst($controller_name) . "/" . $action_name;
                $view->setTemplate($template);
                $controller->view = $view;

                /* initialize session */

                $session_name = B_Registry::get('session/name');
                $session = null;

                try
                {
                    $session = new B_Session($session_name);
                }
                catch(Exception $e)
                {
                    $message = $e->getMessage();
                    $response->setStatus(B_Response::STATUS_ERROR);
                    $has_error = true;
                    $session = null;
                }

                B_Registry::set('session/object', $session);

                /* initialize translation */

                $culture = $session ? $session->getCulture() : 'en_US';
                $translation = new B_Translation($culture);
                B_Registry::set('translation/object', $translation);

                /* translation load */

                $this->translation_load[] = 'application';
                $this->translation_load[] = $controller_name;
                $this->translation_load[] = $controller_name . "/" . $action_name;

                try
                {
                    $translation->load($this->translation_load);
                }
                catch(Exception $e)
                {
                    $message = $e->getMessage();
                    $response->setStatus(B_Response::STATUS_ERROR);
                    $has_error = true;
                    $session = null;
                }
            }

            if($has_error==false)
            {
                /* run action */

                try
                {
                    $controller->before();
                    $controller->run($action_name);
                    $controller->after();
                }
                catch(B_Exception $exception)
                {
                    /* set error */

                    $exception->controller = $controller_name;
                    $exception->action = $action_name;
                    $has_error = true;
                    $message = ((string) $exception);

                    /* set response status */

                    if($exception->getCode() == E_ERROR)
                    {
                        $response->setStatus(B_Response::STATUS_ERROR);
                    }

                    /* log exception */

                    $exception->writeLog();
                }
                catch(Exception $exception)
                {
                    /* set error */

                    $has_error = true;

                    $message = "message: " . $exception->getMessage() . "; " .
                               "code: "    . $exception->getCode() . "; " .
                               "file: "    . $exception->getFile() . "; " .
                               "line: "    . $exception->getLine() . "; " .
                               "trace: "   . $exception->getTraceAsString();

                    /* unexpected exceptions are fatal errors */

                    $response->setStatus(B_Response::STATUS_ERROR);
         
                    /* log exception */

                    $_d = array ('method' => __METHOD__, 
                                 'controller' => $controller_name, 
                                 'action' => $action_name);
                    B_Log::write($message, E_ERROR, $_d);
                }
            }
        }

        /* error reporting */

        if($has_error)
        {
            if(error_reporting() > 0)
            {
                $response->setBody($message);
            }
            else
            {
                if($response->isXML()==false)
                {
                    $status = $response->getStatus();
                    $response->setBody(self::error($status));
                }
            }
        }

        /* send response */

        $response->send();
    }

    /**
     * Controller factory
     *
     * @param   string          $name   Controller name
     *
     * @return  B_Controller
     */
    protected static function factory($name)
    {
        $controller = null;
        $class_name = 'C_' . ucfirst($name);

        if(class_exists($class_name))
        {
            $controller = new $class_name();
        }

        return $controller;
    }

    /**
     * Error response body
     *
     * @param   integer $status
     */
    public static function error($status)
    {
        $path = BASE_PATH . "/public/" . $status . ".html";
        $s = "<h1>error " . $status . "</h2>";

        if(file_exists($path))
        {
            $f = fopen($path, "r");
            $s = fread($f, filesize($path));
            fclose($f);
        }

        return $s;
    }
}



/**
 * Base Controller
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Controller
{
    /**
     * View
     *
     * @var B_View
     */
    public $view;

 
    /**
     * Access to data
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __call ($name, $arguments)
    {
        if($name == "view") return $this->view;
        else                return B_Registry::get($name . '/object');
    }

    /**
     * Before action
     */
    public function before()
    {
    }

    /**
     * After action
     */
    public function after()
    {
    }

    /**
     * Check action
     * 
     * @param   string      $name   Action name
     * @return  boolean
     */
    public function check($name)
    {
        return method_exists($this, ('A_' . $name));
    }

    /**
     * Run controller action
     * 
     * @param   string      $name
     * @return  void
     */
    public function run($name)
    {
        $this->{('A_' . $name)}();

        /* unset layout and template for xml response */

        if($this->response()->isXML() == true)
        {
            $this->view()->setLayout(null);
            $this->view()->setTemplate(null);
        }

        /* render only for non redirect request */

        if($this->response()->isRedirect() == false)
        {
            ob_start();
            $this->view()->render();
            $this->response()->setBody(ob_get_clean());
        }
    }

    /**
     * Session authorize
     *
     * @return  boolean
     */
    protected function authorize($redirect=null)
    {
        if(($active = $this->session()->getActive()) == false)
        {
            if($redirect == null)
            {
                $redirect = BASE_URL;
            }

            $this->response()->setRedirect($redirect, B_Response::STATUS_UNAUTHORIZED);
            $_m = "session unauthorized";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_NOTICE, $_d);
        }

        return $active;
    }
}

/**
 * Base Exception
 *
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Exception extends Exception
{
    /**
     * Data
     *
     * @var array
     */
    protected $data;


    /**
     * Exception constructor
     *
     * @param   string  $message
     * @param   integer $code
     * @param   array   $data
     * @return void
     */
    public function __construct($message, $code=E_NOTICE, $data=array())
    {
        /* force use of predefined codes */

        if(!in_array($code, array(E_NOTICE, E_WARNING, E_ERROR)))
        {
            $code = E_ERROR;
        }

        $this->data = $data;

        parent::__construct($message, $code);
    }

    /**
     * To string
     *
     * return string
     */
    public function __toString()
    {
        $message = null;
        $priority = null;

        switch($this->getCode())
        {
            case E_ERROR:   $priority = "ERROR";   break;
            case E_WARNING: $priority = "WARNING"; break;
            case E_NOTICE:  $priority = "NOTICE";  break;
            default:             $priority = "ERROR";
        }

        $message = $priority . ": " . $this->getMessage();

        foreach($this->data as $name => $value)
        {
            $message.= ";\n" . $name . ": " . $value;
        }
            
        $message.= ";\nfile: " . $this->getFile();
        $message.= ";\nline: " . $this->getLine();

        if($priority == "ERROR")
        {
            $message.= ";\ntrace:\n" . $this->getTraceAsString();
        }

        return $message;
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        $value = null;

        if(is_array($this->data))
        {
            if(array_key_exists($name, $this->data))
            {
                $value = $this->data[$name];
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
    public function __set($name, $value)
    {
        if(is_array($this->data) == false)
        {
            $this->data = array();
        }

        $this->data[$name] = $value;
    }

    /**
     * Write to log
     *
     * @return  void
     */
    public function writeLog()
    {
        B_Log::write($this->getMessage(), $this->getCode(), $this->data);
    }

    /**
     * Forward exception
     *
     * @param   string          $message
     * @param   integer         $code
     * @param   Exception       $exception
     * @param   array           $data
     * @return  void
     */
    public static function forward($message, $code, $exception=null, $data=array())
    {
        if(is_object($exception))
        {
            if(get_class($exception) == __CLASS__)
            {
                $message.= ";\n" . $exception->getMessage();

                /* E_ERROR < E_WARNING < E_NOTICE */

                if($exception->getCode() < $code)
                {
                    $code = $exception->getCode();
                }

                $data = array_merge($exception->data, $data);
            }
            else
            {
                $message.= ";\nexception: " . chop($exception->getMessage());
            }
        }

        throw new B_Exception ($message, $code, $data);
    }
}

/**
 * Base Loader
 *
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Loader
{
    /**
     * Autoload register
     *
     * @return  void
     */
    public static function register ()
    {
        spl_autoload_register (array('B_Loader', 'autoload'));
    }

    /**
     * Class loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function autoload ($name)
    {
        if(class_exists($name) == false)
        {
            if    (strpos($name, "L_") === 0)    self::library($name);
            elseif(strpos($name, "C_") === 0)    self::controller($name);
            elseif(strpos($name, "H_") === 0)    self::helper($name);
            elseif(strpos($name, "Zend_") === 0) self::zend($name);
            else                                 self::model($name);
        }
    }

    /**
     * Application Library loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function library($name)
    {
        $path = APPLICATION_PATH . "/library/" . substr($name, 2) . ".php";

        if(class_exists($name) == false)
        {
            if(file_exists($path))
            {
                include $path;
            }
        }
    }

    /**
     * Controller loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function controller($name)
    {
        $path = APPLICATION_PATH . "/controller/" . substr($name, 2) . ".php";

        if(file_exists($path))
        {
            include $path;
        }
    }

    /**
     * Helper loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function helper($name)
    {
        $path = APPLICATION_PATH . "/view/helper/" . substr($name, 2) . ".php";

        if(file_exists($path))
        {
            include $path;
        }
    }

    /**
     * Model loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function model($name)
    {
        $path = APPLICATION_PATH . "/model/" . $name . ".php";

        if(file_exists($path))
        {
            include $path;
        }
    }

    /**
     * Zend loader
     *
     * @param   string  $name   Class name
     * @throw   B_Exception
     * @return  void
     */
    public static function zend($name)
    {
        if(class_exists("Zend_Loader") == false)
        {
            if(file_exists($zend = LIBRARY_PATH . "/Zend/Loader.php"))
            {
                include $zend;

            }
        }

        if(class_exists("Zend_Loader"))
        {
            Zend_Loader::loadClass($name);
        }
        else
        {
            echo "<pre>";
            echo "class Zend_Loader not found\n";
            echo "</pre>";
            exit(1);
        }
    }
}

/**
 * Base Log
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Log
{
    /**
     * Log table name
     *
     * @var string
     */
    private static $table_name = 'base_log';


    /**
     * Write log
     *
     * @param   string  $message    Log message
     * @param   integer $priority   Priority
     * @param   array   $data       Data (data_* columns)
     * @return  void
     */
    public static function write ($message,
                                  $priority=E_NOTICE,
                                  $data=array())
    {
        $columns = array('message', 'priority', 'created_at');
        $values = array($message, $priority, date('Y-m-d H:i:s'));

        /* set extra data */

        foreach($data as $name => $value)
        {
            $columns[] = "data_" . $name;
            $values[] = $value;
        }

        B_Model::execute("INSERT INTO " . self::$table_name . " " .
                          "(" . implode(", ", $columns) . ") VALUES " .
                          "(?" . str_repeat(", ?", count($columns) - 1) . ")",
                          $values);
    }
}

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
        if(($db = B_Registry::get('database/' . $database))==null)
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
        $uri = $db->driver . ":host=" . $db->host . ";dbname=" . $db->db;
        $db->connection = new PDO ($uri, $db->username, $db->password);
        $db->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}

/**
 * Base Registry
 *
 * Generic storage class to manage global data
 *
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Registry
{
    /**
     * Singleton instance
     * 
     * @var B_Registry
     */
    private static $instance;


    private function __construct() { }
    private function __clone() { }

    /**
     * Singleton constructor
     */
    protected static function singleton()
    {
        if(is_null(self::$instance) == true)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set overloading
     */
    private function __set ($k, $v)
    {
        $this->{$k} = $v;
    }

    /**
     * Get overloading
     */
    public function __get ($k)
    {
        return isset($this->{$k}) ? $this->{$k} : null;
    }

    /**
     * Loader
     *
     * @param   string  $filename
     * @param   string  $type
     */
    public static function load($filename=null, $type='xml')
    {
        if(strlen($filename) > 0 && file_exists($filename))
        {
            switch (strtolower($type))
            {
                case 'xml' : 
                    $xml = simplexml_load_file($filename); 

                    if(is_object($xml)) 
                        if(count($xml) > 0) 
                            self::fromXML($xml->children(), self::singleton());
                break;
            }
        }
    }

    /**
     * Static setter
     *
     * @param   string  $path
     * @param   mixed   $value
     */
    public static function set($path, $value)
    {
        $r = self::singleton();
        $a = explode('/', $path);
        $j = array_pop($a);

        foreach($a as $i)
        {
            if(strlen($i)==0) throw new B_Exception('invalid path', E_WARNING);
            if(!isset($r->{$i})) $r->{$i} = new self();
            $r = $r->{$i};
        }

        $r->{$j} = $value;
    }

    /**
     * Static getter
     *
     * @param   string  $path
     * @return  mixed
     */
    public static function get($path)
    {
        $r = self::singleton();
        $a = explode('/', $path);

        foreach($a as $i)
        {
            if(strlen($i)==0) throw new B_Exception('invalid path', E_WARNING);
            $r = $r->{$i};
        }

        return $r;
    }

    /**
     * Load data from XML
     *
     * @param   SimpleXMLElement    $xml
     * @param   object              $obj
     * @return  void
     */
    protected static function fromXML($xml, $obj)
    {
        foreach($xml as $k => $v)
        {
            if(count($v) > 0) 
            {
                $obj->{$k} = new self();
                self::fromXML($v, $obj->{$k});
            }
            else
            {
                $obj->{$k} = ((string) $v);
            }
        }
    }
}

/**
 * Base Request
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Request
{
    /**
     * Method constants
     */
    const METHOD_GET  = "GET";
    const METHOD_POST = "POST";


    /**
     * Request path
     *
     * @var string
     */
    private $path = "/";

    /**
     * Request method
     *
     * @var string
     */
    private $method;

    /**
     * Controller name
     *
     * @var string
     */
    private $arguments = array();


    /**
     * Request constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  string
     */
    public function __get($name)
    {
        $value = null;

        if(array_key_exists($name, $_REQUEST))
        {
            $value = $_REQUEST[$name];
        }

        return $value;
    }

    public function __set($name, $value) { } /* request is read-only */
    
    /**
     * Request initializer
     *
     * @return void
     */
    private function initialize()
    {
        /* initialize */

        $this->path = self::pathFromServer();
        $this->method = self::methodFromServer();

        $arguments = explode("/", trim($this->path, "/"));
        $total = count($arguments);

        /* filter arguments */

        for($i=0;$i<$total;$i++)
        {
            $arguments[$i] = strtolower($arguments[$i]);
            $arguments[$i] = preg_replace("/[^a-z0-9]/", "", $arguments[$i]);
        }

        $this->arguments = $arguments;
    }

    /**
     * Request path
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Request method
     *
     * @return  string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get request argument
     *
     * @parameter   integer     $i
     * @return      array
     */
    public function getArgument($i)
    {
        return $this->arguments[$i];
    }

    /**
     * Request controller
     *
     * @return  array
     */
    public function getController()
    {
        return array_key_exists(0, $this->arguments) ? $this->arguments[0] : "index";
    }

    /**
     * Request action
     *
     * @return  array
     */
    public function getAction()
    {
        return array_key_exists(1, $this->arguments) ? $this->arguments[1] : "index";
    }

    /**
     * URL for controller / action
     *
     * @param   string  $controller
     * @param   string  $action
     * @param   array   $parameters
     * @param   array   $base           Base URL (when <> BASE_URL)
     * @return  string
     */
    public static function url ($controller=null, $action=null, 
                                $parameters=array(), $base=null)
    {
        $url = $base ? $base : BASE_URL;

        if(strlen($controller) > 0)
        {
            $url .= "/" . $controller;

            if(strlen($action) > 0)
            {
                $url .= "/" . $action;
            }
        }

        if(count($parameters) > 0)
        {
            $url .= "?";
            $url_parameters = array();

            foreach($parameters as $name => $value)
            {
                $url_parameters[] = $name . "=" . urlencode($value);
            }

            $url .= implode("&", $url_parameters);
        }

        return $url;
    }

    /**
     * Path from server (tested only with Apache web server)
     *
     * @throws  B_Exception
     * @return  string
     */
    public static function pathFromServer()
    {
        $request_uri = ((string)$_SERVER['REQUEST_URI']);

        if(strlen($request_uri) == 0)
        {
            $_m = "request uri is empty";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_ERROR, $_d);
        }

        $path = $request_uri;

        $script_name = ((string)$_SERVER['SCRIPT_NAME']);
 
        if(strlen($script_name) > 0)
        {
            if(strpos($path, $script_name) === 0)
            {
                $path = str_replace($script_name, "", $path);
            }
        }

        $script_dir = str_replace("/index.php", "", $script_name);

        if(strlen($script_dir) > 0)
        {
            if(strpos($path, $script_dir) === 0)
            {
                $path = str_replace($script_dir, "", $path);
            }
        }

        $query_string = ((string)$_SERVER['QUERY_STRING']);

        if(strlen($query_string) > 0)
        {
            if(strpos($path, $query_string) > 0)
            {
                $path = str_replace("?" . $query_string, "", $path);
            }
        }

        return $path;
    }

    /**
     * Method from server (tested only with Apache web server)
     *
     * @throws  B_Exception
     * @return  string
     */
    public static function methodFromServer()
    {
        $request_method = ((string)$_SERVER['REQUEST_METHOD']);

        if(strlen($request_method) == 0)
        {
            $_m  = "request method is empty";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_ERROR, $_d);
        }

        return $request_method;
    }
}

/**
 * Base Response
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Response
{
    /**
     * Response status codes
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     */
    const STATUS_OK           = 200;
    const STATUS_REDIRECT     = 302;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_NOT_FOUND    = 404;
    const STATUS_ERROR        = 500;

    /**
     * Response headers
     *
     * @var array
     */
    private $headers = array();

    /**
     * Response status code
     *
     * @var integer
     */
    private $status = self::STATUS_OK;

    /**
     * Allow response redirect
     *
     * @var boolean
     */
    private $allow_redirect = true;

    /**
     * Response redirect
     *
     * @var boolean
     */
    private $is_redirect = false;

    /**
     * Response XML
     *
     * @var boolean
     */
    private $is_xml = false;

    /**
     * Response body
     *
     * @var string
     */
    private $body = "";

    
    /**
     * Response constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->setContentType();
    }

    /**
     * Add item to header list
     *
     * @param   string  $label  Item label
     * @param   string  $value  Header content
     * @return  void
     */
    public function setHeader($label, $value)
    {
        $this->headers[$label] = $value;
    }

    /**
     * Remove item from header list
     *
     * @param   string  $label  Item label
     * @return  void
     */
    public function unsetHeader($label)
    {
        if(array_key_exists($label, $this->headers)) 
            unset($this->headers[$label]);
    }

    /**
     * Set response status code
     *
     * @param   integer $status Status code
     * @return  void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get response status code
     *
     * @return  integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Allow redirect
     */
    public function allowRedirect($allow=true)
    {
        $this->allow_redirect = $allow;
    }

    /**
     * Set redirect
     *
     * @return  void
     */
    public function setRedirect($url, $status=self::STATUS_REDIRECT)
    {
        $this->setHeader('Location', $url);
        $this->status = $status;
        $this->is_redirect = true;
    }

    /**
     * Is redirect ?
     * 
     * @return  boolean
     */
    public function isRedirect()
    {
        return $this->is_redirect;
    }

    /**
     * Set XML
     *
     * @param   boolean     $is_xml
     * @return  void
     */
    public function setXML($is_xml)
    {
        $this->is_xml = $is_xml;
        $this->allow_redirect = $is_xml ^ true;
        $this->setContentType($is_xml ? 'text/xml' : 'text/html');
    }

    /**
     * Is XML
     *
     * @return  boolean
     */
    public function isXML()
    {
        return $this->is_xml;
    }

    /**
     * Set content type
     *
     * @return  void
     */
    public function setContentType($type='text/html', $charset='utf-8')
    {
        $this->setHeader('Content-Type', $type . "; charset=" . $charset);
    }

    /**
     * Set response body
     *
     * @param   string  $body
     * @return  void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get response body
     *
     * @return  string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Send response [headers] and body
     *
     * @return  void
     */
    public function send()
    {
        if($this->status == self::STATUS_REDIRECT && 
           $this->allow_redirect === false)
        {
            unsetHeader('Location');
            $this->setBody(((string) null));
            $this->status = self::STATUS_ERROR;
        }

        $this->sendHeaders();
        $this->sendBody();
    }

    /**
     * Send response headers
     *
     * @return  void
     */
    private function sendHeaders()
    {
        $headers_file = null;
        $headers_line = null;
        $headers_sent = headers_sent($headers_file, $headers_line);

        if($headers_sent == false)
        {
            header('HTTP/1.1 ' . $this->status);

            if($this->status == self::STATUS_OK)
            {
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
            }

            foreach($this->headers as $name => $header)
            {
                header($name . ": " . $header, true);
            }
        }
    }

    /**
     * Send response body
     *
     * @return  void
     */
    private function sendBody()
    {
        echo $this->body;
    }
}

/**
 * Base Session
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Session
{
    /**
     * Session name
     *
     * @var string
     */
    private $session_name = null;

    /**
     * Session table name
     *
     * @var string
     */
    private static $table_name = 'base_session';

    /**
     * Session constructor
     *
     * @param   string  $name
     * @return  void
     */
    public function __construct($name='default')
    {
        session_start();
        $this->session_name = $name;
    }

    /**
     * As of PHP 5.0.5 the write  and close  handlers are called after object
     * destruction and therefore cannot use objects or throw exceptions. The object
     * destructors can however use sessions.
     * 
     * It is possible to call session_write_close() from the destructor to
     * solve this chicken and egg problem. 
     *
     * @see http://br2.php.net/manual/en/function.session-set-save-handler.php
     */
    public function __destruct() { session_write_close(); }

    /**
     * Get overloading (session attribute)
     *
     * @param   string  $name
     * @return  mixed
     */
    protected function __get($name)
    {
        $value = null;

        if(array_key_exists($this->session_name, $_SESSION))
        {
            if(array_key_exists($name, $_SESSION[$this->session_name]))
            {
                $value = $_SESSION[$this->session_name][$name];
            }
        }

        return $value;
    }

    /**
     * Set overloading (session attribute)
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    protected function __set($name, $value)
    {
        if(is_array($_SESSION[$this->session_name]))
        {
            $_SESSION[$this->session_name][$name] = $value;
        }
    }

    /**
     * Open the session
     *
     * @return bool
     */
    public static function open ()
    {
        return true;
    }

    /**
     * Close the session
     *
     * @return bool
     */
    public static function close ()
    {
        return true;
    }

    /**
     * Read the session
     *
     * @param   string  $id
     * @return  string
     */
    public static function read ($id)
    {
        $result = current(B_Model::select("SELECT session_data " . 
                                          "FROM " . self::$table_name . " " .
                                          "WHERE id = ?", array($id)));

        return is_object($result) ? $result->session_data : '';
    }

    /**
     * Write the session
     *
     * @param   string      $id
     * @param   string      $data
     * @return  boolean
     */
    public static function write($id, $data)
    {
        $result = current(B_Model::select("SELECT COUNT(*) AS total " . 
                                          "FROM " . self::$table_name . " " .
                                          "WHERE id = ?", array($id)));

        return B_Model::execute
        (
            ($result->total > 0) ? 
                "UPDATE " . self::$table_name . " SET " .
                "session_expires = ?, session_data = ? " .
                "WHERE id = ?" :
                "INSERT INTO " . self::$table_name . " VALUES (?, ?, ?, 0)",
            ($result->total > 0) ?
                array(time(), $data, $id) :
                array($id, time(), $data)
        );
    }

    /**
     * Destroy the session
     *
     * @param   string  $id
     * @return  boolean
     */
    public static function destroy ($id) 
    {
        return B_Model::execute("DELETE FROM " . self::$table_name . " WHERE id = ?",
                                 array($id));
    }

    /**
     * Garbage Collector
     *
     * @param   integer     $life 
     * @return  boolean
     */
    public static function gc ($max) 
    {
        $exp = intval(B_Registry::get('session/expiration'));
        if($exp==0) $exp = 3600;

        return B_Model::execute("DELETE FROM " . self::$table_name . " " .
                                 "WHERE (session_expires < ? AND active = 0) " .
                                 "OR (session_expires < ? AND active = 1)",
                                 array((time() - $max), (time() - $exp)));
    }

    /**
     * Session handler register
     *
     * @return  void
     */
    public static function register ()
    {
        ini_set('session.save_handler', 'user');

        session_set_save_handler(array('B_Session', 'open'),
                                 array('B_Session', 'close'),
                                 array('B_Session', 'read'),
                                 array('B_Session', 'write'),
                                 array('B_Session', 'destroy'),
                                 array('B_Session', 'gc')
        );
    }

    /**
     * Switch session activity
     *
     * @param   boolean     $active
     * @return  boolean
     */
    public function setActive($active=true)
    {
        if($active == true)
        {
            $_SESSION[$this->session_name] = array();
        }
        else
        {
            unset($_SESSION[$this->session_name]);
        }

        return B_Model::execute("UPDATE " . self::$table_name . " " .
                                 "SET active = ? WHERE id = ?",
                                 array($active, session_id()));
    }

    /**
     * Get session activity
     *
     * @return  boolean
     */
    public function getActive()
    {
        $result = current(B_Model::select("SELECT COUNT(*) AS total " . 
                                          "FROM " . self::$table_name . " " .
                                          "WHERE id = ? AND active = 1", 
                                          array(session_id())));

        return ($result->total > 0);
    }

    /**
     * Set culture 
     *
     * @param   string  $culture
     */
    public function setCulture($culture)
    {
        $this->__set('B_Session_Culture', $culture);
    }

    /**
     * Get culture 
     */
    public function getCulture()
    {
        $c = $this->__get('B_Session_Culture');
        return (strlen($c) > 0) ? $c : 'en_US';
    }

    /**
     * Set timezone
     *
     * @param   string  $timezone
     */
    public function setTimezone($timezone)
    {
        $this->__set('B_Session_Timezone', $timezone);
    }

    /**
     * Get timezone
     */
    public function getTimezone()
    {
        $t = $this->__get('B_Session_Timezone');
        return (strlen($t) > 0) ? $t : 'UTC';
    }
}

/**
 * Base Translation
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Translation
{
    /**
     * Culture
     * 
     * @var string
     */
    private $culture;

    /**
     * Data
     * 
     * @var array
     */
    private $data = array();

    /**
     * Translation table name
     *
     * @var string
     */
    private static $table_name = 'base_translation';


    /**
     * Translation constructor
     *
     * @param   string      $controller
     * @param   string      $action
     * @param   string      $culture
     * @return  void
     */
    public function __construct($culture='us_EN')
    {
        $this->culture = $culture;
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        $value = null;

        if(is_array($this->data))
        {
            if(array_key_exists($name, $this->data))
            {
                $value = $this->data[$name];
            }
        }

        if($value == null)
        {
            $value = str_replace('_', ' ', $name);
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
    public function __set($name, $value)
    {
        if(is_array($this->data) == false)
        {
            $this->data = array();
        }

        $this->data[$name] = $value;
    }

    /**
     * Load translation data
     *
     * @param   mixed   $template   Template name or array
     * @return  void
     */
    public function load($template)
    {
        $sql = "SELECT name, value FROM " . self::$table_name . " WHERE (" .
               substr(str_repeat("template = ? OR ", count($template)), 0, -4) .
               ") AND culture = ?";

        $arg = is_array($template) ? $template : array($template);
        $arg[] = $this->culture;

        foreach(B_Model::select($sql, $arg) as $_t)
        {
            $this->data[$_t->name] = $_t->value;
        }
    }
}

/**
 * Base View
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_View
{
    /**
     * View data
     *
     * @var mixed
     */
    private $data = array();

    /**
     * View layout
     *
     * @var string
     */
    private $layout;

    /**
     * View template
     *
     * @var string
     */
    private $template;


    /**
     * Access to registry data
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __call($name, $arguments)
    {
        return B_Registry::get($name . '/object');
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        $result = null;

        if(array_key_exists($name, $this->data))
        {
            $result = $this->data[$name];
        }

        return $result;
    }

    /**
     * Set overloading
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * To (string) XML
     *
     * @return  string
     */
    public function __toXML()
    {
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->startElement("data");
        self::__xml_recursive($this->data, $xml);
        $xml->endElement();
        return $xml->outputMemory();
    }

    /**
     * Deep recursion in array to write xml
     * Auxiliar method for __toXML
     */
    private static function __xml_recursive($a, &$xml)
    {
        foreach($a as $k => $v)
        {
            $element = is_integer($k) ? "item" : $k;

            if(is_object($v)) $v = ((array) $v);

            if(is_array($v))
            {
                $xml->startElement($element);
                if(is_integer($k)) $xml->writeAttribute("key", $k);
                self::__xml_recursive($v, $xml);
                $xml->endElement();
            }
            elseif(is_bool($v))
            {
                $xml->writeElement($element, ($v == true) ? "true" : "false");
            }
            else
            {
                $xml->writeElement($element, $v);
            }
        }
    }

    /**
     * Get layout
     *
     * @return  string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set layout
     *
     * @param   string  $layout
     * @return  void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Get template
     *
     * @return  string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set template
     *
     * @param   string  $template
     * @return  void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * View render
     *
     * @throws  B_Exception
     * @return  void
     */
    public function render()
    {
        /* render layout */

        if(strlen($this->layout) > 0)
        {
            if(B_Registry::get('view')->compression=='true' && 
               strlen($this->template) > 0)
            {
                $this->includeCache();
            }
            else
            {
                $this->includeLayout();
            }
        }

        /* render view data as xml */

        else
        {
            echo $this->__toXML();
        }
    }

    /**
     * include cache file
     */
    public function includeCache()
    {
        $path = APPLICATION_PATH . '/view/cache/' . $this->layout . '-' .
                strtolower(str_replace('/', '-', $this->template)) . '.php';

        if(file_exists($path))
        {
            include $path;
        }
        else
        {
            $_m = 'cache not found in path (' . $path . ')';
            throw new B_Exception($_m, E_ERROR);
        }
    }

    /**
     * include layout file
     *
     * @param   string  $name
     */
    public function includeLayout($name=null)
    {
        if($name==null) $name = $this->layout . '.php';
        if(file_exists(($path = APPLICATION_PATH . '/view/layout/' . $name))) include $path;
    }

    /**
     * include template file
     *
     * @param   string  $type
     */
    public function includeTemplate($type='php')
    {
        if(file_exists(($path = APPLICATION_PATH . '/view/template/' . $this->template  . '.' . $type))) include $path;
    }
}
