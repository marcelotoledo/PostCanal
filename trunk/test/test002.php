<?php

require "../application/console.php";

$sql = "SELECT hash, item_title, publish_status FROM model_user_blog_queue_item 
        WHERE user_blog_id = 1 
        AND publish_status IN ('new','waiting','failed')
        ORDER BY updated_at ASC, created_at DESC";

print_r(B_Model::select($sql));
