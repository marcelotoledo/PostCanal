class Hello:
    __share_resources = {}

    def __init__(self):
        #implement the borg pattern (we are one)
        self.__dict__ = self.__share_resources

    def myvalue(self, value=None):
        if value:
           self.__myvalue = value
        return self.__myvalue

h = Hello()
h.myvalue("Hello")
print h.myvalue()
h2 = Hello()
print h2.myvalue()
