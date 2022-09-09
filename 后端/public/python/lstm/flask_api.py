# -*- coding: utf-8 -*-
from flask import Flask, request
import requests
import json
from util import load_yaml_config, texts_to_sequences

app = Flask(__name__)


@app.route('/predict', methods=['POST'])
def check():
    # 默认返回内容
    return_dict = {'return_code': '200', 'result': False}
    # 如果请求值为空
    if request.get_data() is None:
        return_dict['return_code'] = '5004'
        return json.dumps(return_dict, ensure_ascii=False)
    # 加载配置文件
    config = load_yaml_config("config.yml")
    # 获取传入的参数
    get_Data = request.get_data()
    # 传入的参数为bytes类型，需要转化成json
    get_Data = json.loads(get_Data)
    text = get_Data.get('text')
    vocab_path = config["data"]["vocab_path"]
    stopword_path = config["data"]["stopword_path"]
    text = texts_to_sequences(text, vocab_path, stopword_path)
    return_dict['result'] = predict(list(text)[0].tolist())['predictions'][0][0]
    return json.dumps(return_dict, ensure_ascii=False)

def predict(text):
    res = requests.post("http://localhost:8501/v1/models/sentiment_analysis_model:predict",
                        data=json.dumps({"instances": [{"input_x": text, "dropout_keep_prob": 1}],
                                         "signature_name": "my_signature"}))
    return res.json()


if __name__ == "__main__":
    app.run(host='0.0.0.0', debug=True)
