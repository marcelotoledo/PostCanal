Hello Marcelo,

About your point: "the clear method is for clearing the data of this instantiated class so you can reuse the connection". The connection can be reused, because PeterBrowser supports cookie. You can always reuse the connection as long as the cookie does not expire(if you do nothing in a long time, the cookie may expire). But I guess you just want the title and content attributes to be cleared, so I modified the clear(). If this is not what you want, tell me, I can modify it in minutes. Of course I can tell you how to modify it, it's very easy. You are also an advanced user of python, LOL.

I did a simple test and the result is OK. I notice a thing about LiveJournal.com: if two adjacent posts are of the same title and content or post time, your post will not successful. This makes sense, from the perspective of livejournal.com. So don't treat this as a bug of the code.

For now I just submit the code to you and close the task. If you have any concern or issue aobut the code, just tell me, I can modify it easily. Maybe now I should go ahead with other services, such as wordpress. And in the future maybe I will have the chance to merge the codes for multiple services into one.

Best regards,

Peter
