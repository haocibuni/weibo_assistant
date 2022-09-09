#!/usr/bin/env python
# encoding: utf-8
import re
import lxml
from scrapy import Spider
from scrapy.crawler import CrawlerProcess
from scrapy.selector import Selector
from scrapy.http import Request
from scrapy.utils.project import get_project_settings
import urllib
from sina.items import ContentItem
from sina.spiders.utils import time_fix
import time
import json
import requests
class WeiboSpider(Spider):
    # scrapy crawl weibo_spider -a keyword="吴磊" -a userid=2 -a  searchtime=2019
    name = "weibo_spider"
    base_url = "https://weibo.cn"
    def __init__(self, keyword=None, userid=None, kwdepth=None,  searchtime=None ,*args, **kwargs):
        super(WeiboSpider, self).__init__(*args, **kwargs)
        self.key_word = keyword
        self.user_id = userid
        self.kw_depth = int(kwdepth)
        self.search_time = searchtime
    def start_requests(self):
        
        str1 = urllib.parse.quote(self.key_word)
        # 请求十页数据
        for i in range(1, self.kw_depth):
            yield Request(url="https://s.weibo.com/weibo?q=" + str1 + "&page=" + str(i),callback=self.parse_information)
            # yield Request(url="https://weibo.cn/search/mblog?hideSearchFrame=&keyword="+ str1+"&page="+str(i), callback=self.parse_information)

    def parse_information(self, response):
        content= ContentItem()
        selector = Selector(response)
        # 将每页的微博进行划分
        datas = selector.xpath('//div[@class="card"]')
        for data in datas:
            # 将用户id和关键词传入
            content['keyword'] = self.key_word
            content['user_id']=self.user_id
            content['search_time'] = self.search_time
            # 获取文本内容
            content_page = data.xpath('.//p[@class="txt"]')
            # 检测由没有阅读全文:
            all_content_link = content_page.xpath('.//a[text()="展开全文"]')
            if not all_content_link:
                contents = content_page.xpath('string(.)').extract_first()
                contents = contents.replace("\n", "")
                contents = contents.replace("", "")
                contents = contents.replace("#", "")
                content['search_content'] = contents.replace(" ", "")
            # 获取用户名
            namedata = data.xpath('.//a[@class="name"]')
            name = namedata.xpath('string(.)').extract_first()
            name_url = namedata.xpath('@href').extract_first()
            content['name'] = name
            # 获取点赞数
            acts = data.xpath('.//div[@class="card-act"]')
            like_num = acts.xpath('./ul/li[4]/a/em/text()').extract_first()
            if(like_num!=None):
            	content['like_num'] = like_num
            else:
            	content['like_num'] = 0
            # 获取转发数
            forwarding_num = acts.xpath('.//a[contains(text(),"转发")]/text()').extract_first()
            if (re.findall("\d+", forwarding_num)):
                content['forwarding_num'] = re.findall("\d+", forwarding_num)[0]
            else:
                content['forwarding_num'] = 0
            # 获取评论数
            comment_num = acts.xpath('.//a[contains(text(),"评论")]/text()').extract_first()
            if(re.findall("\d+",comment_num)):
                content['comment_num'] = re.findall("\d+", comment_num)[0]
            else:
                content['comment_num'] = 0

            yield content


if __name__ == "__main__":
    process = CrawlerProcess(get_project_settings())
    process.crawl('weibo_spider')
    process.start()
