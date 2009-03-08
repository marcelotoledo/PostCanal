install: schema data
clean: schema
schema:
	./configure.php schema.mysql
	sleep 1s
data:
	./configure.php base_translation.mysql
	./configure.php user_profile.mysql
	./configure.php cms_type_wordpress.mysql
	./configure.php aggregator.mysql
