#!/usr/bin/env python
# encoding: utf-8
import re
from lxml import etree
from scrapy import Spider
from scrapy.crawler import CrawlerProcess
from scrapy.selector import Selector
from scrapy.http import Request
from scrapy.utils.project import get_project_settings
from sina.items import  InformationItem, CommentItem, TweetsItem
from sina.spiders.utils import time_fix
import time


class WeiboSpider(Spider):
    name = "weibo_spider"
    base_url = "https://weibo.cn"
    current_commment = 0
    def __init__(self, weibopageid=None, userid=None, pgdepth=1, date=None ,*args, **kwargs):
        super(WeiboSpider, self).__init__(*args, **kwargs)
        self.weibopage_id = weibopageid
        self.user_id = userid
        self.pg_depth = int(pgdepth)
        self.date = date
        self.max_comment = 10*int(pgdepth)
    def start_requests(self):

        yield Request(url="https://weibo.cn/%s" % self.weibopage_id, callback=self.second_requests,  dont_filter=True)

    def second_requests(self, response):
        "抓取关注分析微博数"
        selector = Selector(response)
        text = response.text
        information_item = InformationItem()
        information_item['_id'] = self.weibopage_id
        tweets_num = re.findall('微博\[(\d+)\]', text)
        if tweets_num:
            information_item['tweets_num'] = int(tweets_num[0])
        follows_num = re.findall('关注\[(\d+)\]', text)
        if follows_num:
            information_item['follows_num'] = int(follows_num[0])
        fans_num = re.findall('粉丝\[(\d+)\]', text)
        if fans_num:
            information_item['fans_num'] = int(fans_num[0])
        information_item['nick_name'] = selector.xpath('//span[@class="ctt"]/text()').extract_first()
        yield information_item
        for i in range(0, 100):
            yield Request(url="https://weibo.cn/" + self.weibopage_id + "?page=1", callback=self.parse_tweet,dont_filter=True)
    def parse_tweet(self, response):
        "爬取主页微博"

        tree_node = etree.HTML(response.body)
        tweet_nodes = tree_node.xpath('//div[@class="c" and @id]')
        for tweet_node in tweet_nodes:
            tweet_comment_num = tweet_node.xpath('.//a[contains(text(),"评论[")]/text()')[0]
            if(int(re.findall("\d+", tweet_comment_num)[0])!=0 ):
                tweet_comment_url = tweet_node.xpath('.//a[contains(text(),"评论[")]/@href')[0]
                yield Request(url=tweet_comment_url, callback=self.parse_comment, meta={'weibo_url': tweet_comment_url},dont_filter=True)
    def parse_comment(self, response):

        selector = Selector(response)
        comment_nodes = selector.xpath('//div[@class="c" and contains(@id,"C_")]')
        for comment_node in comment_nodes:
            self.current_commment = self.current_commment + 1
            if (self.current_commment >= self.max_comment ):
                # logging.info("计数超过10，停止爬虫")
                self.crawler.engine.close_spider(self, '计数超过爬取深度，停止爬虫!')
            pass
            comment_user_url = comment_node.xpath('.//a[contains(@href,"/u/")]')
            comment_name = comment_user_url.xpath('string(.)').extract_first()
            if not comment_user_url:
                continue
            comment_item = CommentItem()
            comment_item['weibopage_id'] = self.weibopage_id
            comment_item['user_id'] = self.user_id
            comment_item['date'] = self.date
            comment_item['weibo_url'] = response.meta['weibo_url']
            comment_item['comment_user_id'] = comment_name
            comment_item['content'] = comment_node.xpath('.//span[@class="ctt"]').xpath('string(.)').extract_first()
            yield comment_item



if __name__ == "__main__":
    process = CrawlerProcess(get_project_settings())
    process.crawl('weibo_spider')
    process.start()
