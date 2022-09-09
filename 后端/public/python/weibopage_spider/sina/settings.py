# -*- coding: utf-8 -*-

BOT_NAME = 'sina'
SPIDER_MODULES = ['sina.spiders']
NEWSPIDER_MODULE = 'sina.spiders'
HTTPERROR_ALLOWED_CODES = [302]
ROBOTSTXT_OBEY = False
# 请将Cookie替换成你自己的Cookie
DEFAULT_REQUEST_HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:61.0) Gecko/20100101 Firefox/61.0',
    'Cookie': "_T_WM=4369dda4464e5dca4967646faa886b60; SUB=_2A25NhIf8DeRhGeRJ6VcZ8y3MwzmIHXVuhim0rDV6PUJbktAKLWzWkW1NUk6ZTAP6tV0DXRvZmpaueW-4F1-n7eqN; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9W5lxZkfRv9D7SG.7bAfVFDv5NHD95QES0zf1he0ehnfWs4Dqcj1BXH2xLSWxLS2-XxWxJ2t; SSOLoginState=1619064748"}
# 当前是单账号，所以下面的 CONCURRENT_REQUESTS 和 DOWNLOAD_DELAY 请不要修改

DOWNLOAD_DELAY = 0
CONCURRENT_REQUESTS = 1

DOWNLOADER_MIDDLEWARES = {
    'weibo.middlewares.UserAgentMiddleware': None,
    'scrapy.downloadermiddlewares.cookies.CookiesMiddleware': None,
    'scrapy.downloadermiddlewares.redirect.RedirectMiddleware': None
}

ITEM_PIPELINES = {
   'sina.pipelines.MySQLPipeline': 300,
}