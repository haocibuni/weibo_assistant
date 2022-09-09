# -*- encoding:utf-8 -*-
import jieba.analyse
import sys
from os import path
from scipy.misc import imread
import matplotlib as mpl
import matplotlib.pyplot as plt
from wordcloud import WordCloud, STOPWORDS, ImageColorGenerator

if __name__ == "__main__":
	
    mpl.rcParams['font.sans-serif'] = ['FangSong']
    # mpl.rcParams['axes.unicode_minus'] = False
    name = sys.argv[1]
    content = open("./txt/"+name+".txt", "rb").read()

    # tags extraction based on TF-IDF algorithm
    tags = jieba.analyse.extract_tags(content, topK=100, withWeight=False)
    text = " ".join(tags)
    text = str(text)
    # read the mask
    d = path.dirname(__file__)
    trump_coloring = imread(path.join(d, "./weibo.png"))
    # 

    wc = WordCloud(font_path='./simsun.ttc',
                   background_color="white", max_words=300, mask=trump_coloring,
                   max_font_size=50, random_state=42)

    # generate word cloud 
    wc.generate(text)

    # generate color from image
    image_colors = ImageColorGenerator(trump_coloring)
    plt.imshow(wc)
    plt.axis("off")
    plt.savefig("./img/"+name+".png", dpi=500, bbox_inches='tight')  # 解决图片不清晰，不完整的问题
    # plt.show()
