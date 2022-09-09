# -*- coding: utf-8 -*-

BOT_NAME = 'sina'

SPIDER_MODULES = ['sina.spiders']
NEWSPIDER_MODULE = 'sina.spiders'

ROBOTSTXT_OBEY = False

#  定义请求头
DEFAULT_REQUEST_HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:61.0) Gecko/20100101 Firefox/61.0',
    'Cookie': "_T_WM=6e9d2a91353e68e32fac5e697c7a4a9b; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9W5lxZkfRv9D7SG.7bAfVFDv5NHD95QES0zf1he0ehnfWs4Dqcj1BXH2xLSWxLS2-XxWxJ2t; SCF=AmXp13HXbCUXzP216Sjbo6hv-EKz4K-oc_ojo3tIzO-tYF54UXYEeGyuAL18vMbVMduFpqQAiRf-DaAtpIxAcRc.; SUB=_2A25y80H_DeRhGeRJ6VcZ8y3MwzmIHXVuHG-3rDV6PUJbktAKLRbnkW1NUk6ZTHEYTNqde9sIxZ8UPjEaLxSXBMot"}
# 当前是单账号，所以下面的 CONCURRENT_REQUESTS 和 DOWNLOAD_DELAY 请不要修改
CONCURRENT_REQUESTS = 100
# 每3秒左右来一个request
DOWNLOAD_DELAY = 0.1

# 下载器中间件组件
DOWNLOADER_MIDDLEWARES = {
    'weibo.middlewares.UserAgentMiddleware': None,
    'scrapy.downloadermiddlewares.cookies.CookiesMiddleware': None,
    'scrapy.downloadermiddlewares.redirect.RedirectMiddleware': None
}

ITEM_PIPELINES = {
   'sina.pipelines.MySQLPipeline': 300,
}


