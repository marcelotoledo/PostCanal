default:
	@echo
	@echo "options are: schema|data|translation"
	@echo
schema:
	./load.php schema.mysql
translation:
	php ./base_translation.php
	./load.php base_translation.mysql
data: translation
	./load.php user_profile.mysql
	./load.php blog_type_wordpress.mysql
