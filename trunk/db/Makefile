install: schema sleep data
clean: schema
schema:
	./load.php schema.mysql
sleep:	
	sleep 1s
data:
	./load.php base_translation.mysql
	./load.php user_profile.mysql
	./load.php blog_type_wordpress.mysql
	./load.php aggregator.mysql
