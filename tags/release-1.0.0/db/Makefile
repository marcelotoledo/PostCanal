DB = "./db.php"
BT = "./base_translation.php"

default:
	@echo ""
	@echo "options are: schema|data|translation"
	@echo ""
schema:
	./db.php schema.mysql
	sleep 2s
translation:
	$(BT)
	$(DB) base_translation.mysql
data: translation
	$(DB) user_profile.mysql
	$(DB) user_profile_invitation.mysql
