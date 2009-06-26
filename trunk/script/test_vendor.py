import sys, os

base_path = os.path.abspath("../")
sys_path = base_path + "/backend"
sys.path.append(sys_path)

# ==============================================================================

from vendor import feedparser

url = "http://www.bovespa.com.br/rss/"

try:
    parsed = feedparser.parse(url)
except:
    pass
print parsed
