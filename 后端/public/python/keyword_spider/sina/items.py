# -*- coding: utf-8 -*-
from scrapy import Item, Field

class ContentItem(Item):
    user_id = Field()
    keyword = Field()
    search_time = Field()
    search_content = Field() # 关键字微博内容
    name = Field() # 发布内容者
    like_num = Field() #微博点赞数
    forwarding_num = Field() # 微博转发数
    comment_num = Field() # 微博评论数


