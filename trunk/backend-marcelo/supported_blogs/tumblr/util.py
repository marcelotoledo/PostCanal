'''utility module, contains LoggerFactory and PeterBrowser, the former is to produce a
logger, the latter is used to mock a web browser'''

import logging
import urllib2

class LoggerFactory:
    '''This factory class is for producing loggers'''
    formatter = logging.Formatter('%(asctime)s %(levelname)s %(message)s')

    @staticmethod
    def getLogger(name):
        '''generate and return a logger instance'''
        logger=logging.getLogger(name)
        if not logger.handlers:
            handler=logging.FileHandler(name+'.log')
            handler.setFormatter(LoggerFactory.formatter)
            logger.addHandler(handler)
            logger.setLevel(logging.INFO)
        return logger


class PeterBrowser:
    '''this class can mock a browser's behavior'''
    userAgent='Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9) Gecko/2008061015 Firefox/3.0'
    logger=LoggerFactory.getLogger('peterBrowser')

    def __init__(self):
        '''initialize and enable cookie'''
        self.opener=urllib2.build_opener(urllib2.HTTPCookieProcessor)

    def getUrl(self,url,data=None):
        '''fetch the url, default using GET, if data is not None, using POST'''
        request = urllib2.Request(url,data)
        request.add_header('User-Agent',PeterBrowser.userAgent)
        success=False
        counter=0
        while not success:
            counter+=1
            if counter>6: #connection retry 6 times
                PeterBrowser.logger.error('connection failed @ '+url)
                return ''
            try:
                socket=self.opener.open(request)
                success=True
            except:
                pass
        success=False
        counter=0
        while not success:
            counter+=1
            if counter>16: #read retry 16 times
                PeterBrowser.logger.error('network read error @ '+url)
                return ''
            try:
                data=socket.read()
                success=True
            except:
                pass
        socket.close()
        return data

peterBrowser=PeterBrowser()
