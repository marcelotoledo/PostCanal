<?php

/**
 * Report controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 */

class C_Report extends B_Controller
{
    /* configure controller */

    public function configure($action_name)
    {
        $this->hasSession(false);
        $this->hasTranslation(false);
    }

    public function before()
    {
        $this->view()->setLayout('admin');

        /* http auth */

        if(!isset($_SERVER['PHP_AUTH_USER']) || 
           !isset($_SERVER['PHP_AUTH_PW']))
        {
            header('WWW-Authenticate: Basic realm="Authorized Only"');
            header('HTTP/1.0 401 Unauthorized');
            echo '<h1>401 Unauthorized</h1>';
            exit(0);
        }
        else
        {
            if($_SERVER['PHP_AUTH_USER'] != 
                B_Registry::get('application/report/username') ||
               $_SERVER['PHP_AUTH_PW'] != 
                B_Registry::get('application/report/password'))
            {
                echo '<h1>401 Unauthorized</h1>';
                B_Log::write(sprintf('attempt to access report denied for user (%s) and password (%s)', $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']), E_WARNING, array('method' => __METHOD__));
                exit(0);
            }
        }
    }

    /**
     * Default action
     *
     * @return void
     */
    public function A_index()
    {
    }

    /**
     * Add
     */
    public function A_add()
    {
        $this->response()->setXML(true);
        $this->view()->added = false;

        if($this->request()->getMethod() == B_Request::METHOD_POST)
        {
            $report = new Report();
            $report->name = $this->request()->name;
            $report->query = $this->request()->query;

            try
            {
                $report->save();
                $this->view()->added = true;
            }
            catch(Exception $e)
            {
            }
        }
    }

    /**
     * List
     */
    public function A_list()
    {
        $this->response()->setXML(true);
        $this->view()->list = Report::findAll();
    }

    /**
     * Edit
     */
    public function A_edit()
    {
        ($this->request()->getMethod() == B_Request::METHOD_POST) ?
            $this->P_edit() :
            $this->G_edit() ;
    }

    private function G_edit()
    {
        $this->response()->setXML(true);

        if(is_object($report = Report::getByPrimaryKey($this->request()->id)))
        {
            $this->view()->report = $report->dump();
        }
    }

    private function P_edit()
    {
        $this->response()->setXML(true);
        $this->view()->updated = false;

        if(is_object($report = Report::getByPrimaryKey($this->request()->id)))
        {
            $report->name = $this->request()->name;
            $report->query = $this->request()->query;
            
            try
            {
                $report->save();
                $this->view()->updated = true;
            }
            catch(Exception $e)
            {
            }
        }
    }

    /**
     * Delete
     */
    public function A_delete()
    {
        $this->response()->setXML(true);
        $this->view()->deleted = false;

        if($this->request()->getMethod() == B_Request::METHOD_POST)
        {
            if(is_object($report = Report::getByPrimaryKey($this->request()->id)))
            {
                $report->enabled = false;
            
                try
                {
                    $report->save();
                    $this->view()->deleted = true;
                }
                catch(Exception $e)
                {
                }
            }
        }
    }

    /**
     * View
     */
    public function A_view()
    {
        $this->view()->result = array();

        if(is_object($report = Report::getByPrimaryKey($this->request()->id)))
        {
            $this->view()->report_name = $report->name;
            $this->view()->result = B_Model::select($report->query, 
                                                    array(), 
                                                    PDO::FETCH_ASSOC);
        }
    }
}
