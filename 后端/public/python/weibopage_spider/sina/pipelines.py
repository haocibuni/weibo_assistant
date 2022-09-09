
# -*- coding: utf-8 -*-
import pymysql
from sina.items import   InformationItem, CommentItem
import requests
import json

class MySQLPipeline(object):
    def __init__(self):
        self.conn = pymysql.connect(host='81.70.202.142', port=3306, user='weibo', passwd='72Nb7frw53zMX6nk',
                                    db='weibo', charset='utf8', use_unicode=True)
        self.cursor = self.conn.cursor()
    def process_item(self, item, spider):
        """ 判断item的类型，并作相应的处理，再入数据库 """

        if isinstance(item, InformationItem):
            self.cursor.execute('''replace into weibo_page_information(
                          weibopage_id,nick_name,
                          gender,city,
                          follows_num,fans_num, 
                          tweets_num )  VALUES (%s,%s,%s,%s,%s,%s,%s)''', (item['_id'], item['nick_name'],
                                                                           1, 2,
                                                                           item['follows_num'], item['fans_num'],
                                                                           item['tweets_num']))

        elif isinstance(item, CommentItem):
            # token = '24.7ebb9c50f44683dc0103c0c96a0fff1a.2592000.1623141067.282335-17678483'
            # url = 'https://aip.baidubce.com/rpc/2.0/nlp/v1/sentiment_classify?charset=UTF-8&access_token={}'.format(
            #     token)
            # data = {
            #     'text': item['content']
            # }
            # data = json.dumps(data)
            # res = requests.post(url, data=data)
            # if 'items' in res.json():
            #     t = res.json()['items'][0]['positive_prob']
            # else:
            #     t = 0.5
            modelurl = 'http://0.0.0.0:5000/predict'
            data = json.dumps({"text": item['content']})
            res = requests.post(modelurl, data=data)
            t = res.json()['result']
            self.cursor.execute('''insert into weibo_page_comment(weibopage_id,
                                            user_id,date,
                                            weibopage,comment_name,
                                            comment,page_value) VALUES (%s,%s,%s,%s,%s,%s,%s)''',(
                                 item['weibopage_id'], item['user_id'],
                                 item['date'],item['weibo_url'],
                                 item['comment_user_id'],item['content'],
                                 t ))

        self.conn.commit()
        return item
