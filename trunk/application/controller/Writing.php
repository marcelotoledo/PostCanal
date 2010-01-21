<?php

/**
 * Writing controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 */

class C_Writing extends B_Controller
{
    /**
     * Configure controller
     */
    public function configure($action_name)
    {
        $this->hasTranslation(false);
    }

    /**
     * Before action
     */
    public function before()
    {
        $this->authorize();
        $this->response()->setXML(true);
    }

    /**
     * create / update writings
     */
    public function A_save()
    {
        $blog = $this->request()->blog;
        $user = $this->session()->user_profile_id;

        $writing         = $this->session()->writing;
        $writing_title   = $this->request()->writing_title;
        $writing_content = $this->request()->writing_content;

        $writing_obj = null;

        if($writing) $writing_obj = UserBlogWriting::findWriting($blog, $user, $writing);

        if(is_object($writing_obj)==false)
        {
            $writing_obj = new UserBlogWriting();
            if(is_object(($blog_obj = UserBlog::getByUserAndHash($user, $blog)))) 
                $writing_obj->user_blog_id = $blog_obj->user_blog_id;
        }

        $writing_obj->writing_title   = $writing_title;
        $writing_obj->writing_content = $writing_content;
        $writing_obj->save();
    }
}
