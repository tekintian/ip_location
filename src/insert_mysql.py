# coding:utf8
'''
    主要目的： 将 ipdata.txt 中的数据导入到我们的数据库中
    author ; zhyh2010 create in 2016.05.30
'''

#from __future__ import print_function
from mysql import connector

class Ipdata:
    '''
    将 ipdata.txt 中的数据导入到我们的数据库中

    主要分为两个模块， 数据库操作， 文件操作
    '''
    def __init__(self):
        self.DROP_TABLE = 'drop table if exists ipdata2;'
        self.CREATE_TABLE = ' create table ipdata2( \
                            id bigint(20) not null auto_increment primary key, \
                            startip varchar(45) default null, \
                            endip varchar(45) default null, \
                            country varchar(45) default null, \
                            local varchar(300) default null \
                        ) charset = utf8;'
        self.sql_model = 'insert into `ipdata2` (`startip`, `endip`, `country`, `local`) values(%s, %s, %s, %s)'
        self.values = []
        self.filename = "IPData.txt"
        self.filecode = "gb2312"

    def LoadData(self):
        # load data from ipdata.txt
        fd = open(self.filename, "r")
        data = fd.readlines()
        for line in data:
            line = line.decode(self.filecode, "ignore")
            items = line.split('\t')
            items_new = []
            for item in items:
                items_new.append(item)
            value = tuple(items_new)
            #print(line, *value)
            self.values.append(value)
        self.values = self.values[1:]

    def InsertIntoDB(self):
        # connect to the db and insert data
        self.LoadData()
        db = connector.Connect(host = "127.0.0.1",
                               user="root",
                               passwd="zhyh2010",
                               charset="utf8",
                               database="pythontest")
        cursor = db.cursor()
        print cursor.execute(self.DROP_TABLE)
        print cursor.execute(self.CREATE_TABLE)
        print cursor.executemany(self.sql_model, self.values)
        db.commit()
        db.close()

if __name__ == "__main__":
    ipdata = Ipdata()
    ipdata.InsertIntoDB()