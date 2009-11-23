DB = "./db.php"
BT = "./base_translation.php"
PN = "patch-release-1-1-0"

default:
	@echo ""
	@echo "options are: schema|translation|""$(PN)"
	@echo ""
schema:
	./db.php schema.mysql
	sleep 2s
translation:
	$(BT)
	$(DB) base_translation.mysql
patch:
	$(DB) schema-patch-1257698089.mysql
	$(DB) schema-patch-1258375512.mysql
	$(DB) schema-patch-1258725971.mysql
	$(DB) schema-patch-1258966932.mysql
patch-release-1-1-0: patch
