BLOTOMATE_DEFAULT_CONFIG_PATH = "../config/environment.xml"

class BlotomateConfig:
    def __init__(self, config_path):
        from xml.dom import minidom
        config_path = config_path
        if(config_path == ""):
            config_path = BLOTOMATE_DEFAULT_CONFIG_PATH
        self.xmldoc = minidom.parse(config_path)
    def get(self, path):
        tag = self.xmldoc.firstChild
        for folder in path:
            tag = tag.getElementsByTagName(folder)[0]
        return tag.firstChild.data
