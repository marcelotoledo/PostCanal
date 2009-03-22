install: schema sleep data
clean: schema
schema:
	./configure.php schema.mysql
sleep:	
	sleep 1s
data:
	./configure.php base_translation.mysql
	./configure.php user_profile.mysql
	./configure.php cms_type_wordpress.mysql
	./configure.php aggregator.mysql
