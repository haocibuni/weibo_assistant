# 微舆情助手
  本系统前端为微信小程序，后端由ThinkPHP框架、Scrapy爬虫和LSTM文本情感分析模型构成，数据库采用MySQL数据库，除上述技术框架外本系统还使用了docker、Echarts可视化库等技术。  本文档主要介绍如何部署该系统，由前端和后端两部分构成。
## 前端
通过微信开发者工具导入代码中的前端文件夹后，将app.js文件的globalData对象中的global字符串替换为后端服务器地址。
## 后端
后端的部署包括：ThinkPHP框架的配置、MySQL数据库的配置、Scrapy爬虫框架的配置、LSTM文本情感分析模型的部署。
* * *
### ThinkPHP框架的配置
php的版本为PHP-71
若需要将小程序进行上线或者发布测试版，请对后端网站申请SSL证书
将后端文件夹上传到服务器后，打开application文件夹中的database.php文件，修改服务器类型、服务器地址、数据库名、用户名、密码、端口等数据库信息
* * *
### MySQL数据库配置
数据库版本为MySQL5.6，在MySQL数据库中导入database文件即可
* * *
### Scrapy爬虫框架的配置
首先安装python环境，python版本为3.6
然后进入```/后端/public/python```路径后，通过``` >>> pip install -r ./requirements.txt```命令 ，安装scrapy爬虫框架所需的包
最后进入```/后端/public/python```路径后，修改keyword_spider和weibo_spider文件夹中pipelines.py中的MySQL数据库配置
* * *
### LSTM文本情感分析模型的部署
LSTM文本情感分析模型以SavedModel格式保存在后端/public/python/lstm/tfserving位置
首先在服务器上安装Docker，推荐使用宝塔面板直接安装
然后执行``` docker run -p 8501:8501 --name="sentiment_analysis_model" --mount type=bind,source=后端路径/public/python/lstm/tfserving,target=/models/sentiment_analysis_model -e MODEL_NAME=sentiment_analysis_model -t tensorflow/serving "&"```命令，将LSTM模型加载到TF_serving上
最后进入```/后端路径/public/python/lstm```路径后,执行```nohup python3 flask_api.py >/dev/null 2>&1 &```启动flask后端接口
## 效果展示
### 授权登录页面
![授权登录.png](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f3975e05.png)
### 关键词舆情分析模块及历史记录的使用
![关键词总体分析展示.jpg](http://nas.wulei.pro:5088/upload/2024/05/08/663b7f3e0ecd4.jpg)
![关键词消极实例展示.jpg](http://nas.wulei.pro:5088/upload/2024/05/08/663b7f3d52f5c.jpg)
![关键词积极实例展示.jpg](http://nas.wulei.pro:5088/upload/2024/05/08/663b7f3c9d231.jpg)
![关键词分析记录.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f3bdf7da.jpg)
![分析深度设置.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f3a60c15.jpg)
### 微博主页评论舆情分析模块及历史记录的使用
![微博主页评论绑定.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f4324a4b.jpg)
![微博主页评论分析.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f440b8f6.jpg)
![微博主页评论总体分析展示.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f45ceedb.jpg)
![微博主页评论分析.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f440b8f6.jpg)
![微博评论积极实例展示.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f3fb5fc9.jpg)
![微博评论消极实例展示.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f40ce75e.jpg)
![微博主页评论分析记录.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f44d7dbe.jpg)
### 用户分析深度设置的使用
![分析深度设置.jpg](https://nas.wulei.pro:5088/upload/2024/05/08/663b7f3a60c15.jpg)
