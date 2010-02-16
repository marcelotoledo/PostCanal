DB = "./load.sh"
BT = "./base_translation.php"
CV = "./current-version"

default:
	@echo ""
	@echo "options are: schema|data|translation|patch"
	@echo ""
schema:
	$(DB) schema.mysql
	@echo "release-1-0-0" > $(CV)
	@sleep 2s
data:
	$(DB) data.mysql
translation:
	$(BT)
	$(DB) base_translation.mysql
patch-release-1-1-0:
	$(DB) schema-patch-1257698089.mysql
	$(DB) schema-patch-1258375512.mysql
	$(DB) schema-patch-1258725971.mysql
	$(DB) schema-patch-1258966932.mysql
	$(DB) schema-patch-1263065745.mysql
	@echo "release-1-1-0" > $(CV)
patch-release-1-1-0-1:
	$(DB) schema-patch-1264085609.mysql
	@echo "release-1-1-0-1" > $(CV)
patch-release-1-2-0:
	$(DB) schema-patch-1266321757.mysql
	@echo "release-1-2-0" > $(CV)
patch: patch-release-1-2-0
