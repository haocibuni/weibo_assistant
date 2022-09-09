# -*- coding: utf-8 -*-
from scrapy import Item, Field

class TweetsItem(Item):
    """ 微博信息 """
    weibo_url = Field()  # 微博URL
class InformationItem(Item):
    """ 个人信息 """
    _id = Field()  # 微博id
    nick_name = Field()  # 昵称
    gender = Field()  # 性别
    city = Field()  # 所在城市
    tweets_num = Field()  # 微博数
    follows_num = Field()  # 关注数
    fans_num = Field()  # 粉丝数

class CommentItem(Item):
    """
    微博评论信息
    """
    weibopage_id = Field() #微博主体id
    user_id = Field() #用户id
    date = Field() # 分析时间
    comment_user_id = Field()  # 评论用户的id
    content = Field()  # 评论的内容
    weibo_url = Field()  # 评论的微博的url

