# monitor.py --- pcd monitor

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import sqlite3

DATABASE="/Users/marcelo/Desktop/storage/personal/projects/current/postcanal/trunk/pcd/pcd_monitor.sqlite"

class Monitor:
    def __init__(self, dbFile=None):
        if dbFile == None:
           self.dbFile = DATABASE
        else:
            self.dbFile = dbFile
            
        self.conn   = None
        self.openConnection()
        
    def openConnection(self):
        try:
            self.conn = sqlite3.connect(self.dbFile)
        except:
            return False
        return True

    def close(self):
        self.conn.close()

    # def query(self, query):
    #     try:
    #         res = self.conn.execute(query)
    #     except:
    #         return None
    #     return res

    def setStatus(self, key, value):
        cur = self.conn.cursor()
        cur.execute("select * from pcd_monitor where key='%s'" % key)
        res = cur.fetchone()

        if res != None:
            self.update(key, value)
        else:
            self.insert(key, value)

    def getValue(self, key):
        cur = self.conn.cursor()
        cur.execute("select value from pcd_monitor where key='%s'" % key)
        return cur.fetchone()[0]

    def insert(self, key, value):
        self.conn.execute("insert into pcd_monitor values ('%s', '%s')" % (key, value))
        self.conn.commit()
        
    def update(self, key, value):
        self.conn.execute("update pcd_monitor set value='%s' where key='%s'" % (value, key))
        self.conn.commit()

    def printAll(self):
        for item in self.conn.execute('select key, value from pcd_monitor'):
            print "%20s %s" % (item[0], item[1])

    def delAll(self):
        self.conn.execute("delete from pcd_monitor")
        self.conn.commit()
            

# mon = Monitor()
# #mon.delAll()
# mon.setStatus('nome', 'marcelo')
# mon.printAll()
# print ""
# mon.setStatus('nome', 'toledo')
# mon.printAll()
# print ""
# mon.setStatus('nome', 'dias')
# mon.setStatus('post-thread-size', '110')
# mon.printAll()
