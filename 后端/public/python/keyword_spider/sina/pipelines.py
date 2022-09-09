# -*- coding: utf-8 -*-
import pymysql
from sina.items import ContentItem
import re
import requests
import json

class MySQLPipeline(object):
    keyword = '无'
    user_id = 0
    def __init__(self):
        self.conn = pymysql.connect(host='81.70.202.142', port=3306, user='weibo', passwd='72Nb7frw53zMX6nk',
                                    db='weibo', charset='utf8', use_unicode=True)
        self.cursor = self.conn.cursor()

    def process_item(self, item, spider):
        if isinstance(item, ContentItem):
            # token = '24.7ebb9c50f44683dc0103c0c96a0fff1a.2592000.1623141067.282335-17678483'
            # baiduurl = 'https://aip.baidubce.com/rpc/2.0/nlp/v1/sentiment_classify?charset=UTF-8&access_token={}'.format(
            #     token)
            # data = {
            #     'text': item['search_content']
            # }
            # data = json.dumps(data)
            # if 'items' in res.json():
            #     t = res.json()['items'][0]['positive_prob']
            # else:
            #     t = 0.5
            # 请求lstm模型api
            modelurl = 'http://0.0.0.0:5000/predict'
            data = json.dumps({"text": item['search_content']})
            res = requests.post(modelurl, data=data)
            t = res.json()['result']
            self.cursor.execute('''insert into weibo_keyword(
                                user_id, keyword,
                                weibo_name,content,like_num,
                                forward_num,conment_num,
                                keyword_value, search_time) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s) ''', (
                                 item['user_id'], item['keyword'],
                                 item['name'], item['search_content'],item['like_num'],
                                 item['forwarding_num'],item['comment_num'],
                                 t,item['search_time']))
        self.conn.commit()
        return item
