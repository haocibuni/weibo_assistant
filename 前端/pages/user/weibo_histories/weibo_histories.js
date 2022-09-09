import * as echarts from '../../../ec-canvas/echarts';
var Chart = null;
const app = getApp();
const util = require('../../../utils/util.js'); 
Page({
  data: {
    ec: {
      onInit: function (canvas, width, height) {
        chart = echarts.init(canvas, null, {
          width: width,
          height: height
        });
        canvas.setChart(chart);
        return chart;
      },
      lazyLoad: true // 延迟加载
    },
    recorddate:[],
    recordvalue:[]
  },
  onLoad: function (options) {
    let that = this
    wx.request({
      url: app.globalData.global + 'index/index/pgrecord',
      header: { //请求头
        "Content-Type": "applciation/json",
        'session': app.globalData.session
      },
      data: {
        'openId': app.globalData.openId,
      },
      method: "GET", //get为默认方法/POST
      success: function (res) {
        console.log(res.data); //res.data相当于ajax里面的data,为后台返回的数据 
        if (res.data.data.record.length!=0){
          for (let i = 0; i < res.data.data.record.length; i++) {
            if (i > 6) { break; }
            let temp1 = 'recorddate[' + i + ']'
            let temp2 = 'recordvalue[' + i + ']'
            that.setData({
              [temp1]: util.formatDate(res.data.data.record[i].date * 1000),
              [temp2]: res.data.data.record[i].ave_value
            })
          }
          that.setData({
            recorddate: that.data.recorddate.reverse(),
            recordvalue: that.data.recordvalue.reverse()
          })

        }
        
        that.echartsComponnet = that.selectComponent('#mychart');
        //如果是第一次绘制
        if (!Chart) {
          that.init_echarts(); //初始化图表
        } else {
          that.setOption(Chart); //更新数据
        }
      },
      fail: function (err) {
      }, //请求失败
      complete: function () { } //请求完成后执行的函数
    });
  },
  onReady() {
  },
  //初始化图表
  init_echarts: function () {
    this.echartsComponnet.init((canvas, width, height) => {
      // 初始化图表
      const Chart = echarts.init(canvas, null, {
        width: width,
        height: height
      });
      this.setOption(Chart)
      // 注意这里一定要返回 chart 实例，否则会影响事件处理等
      return Chart;
    });
  },
  setOption: function (Chart) {
    Chart.clear();  // 清除
    Chart.setOption(this.getOption());  //获取新数据
  },
  // 图表配置项
  getOption() {
    var self = this;
    var option = {
      // title: {//标题
      //   text: '主页情感均值波动图',
      //   left: 'center'
      // },
      renderAsImage: true, //支持渲染为图片模式
      color: ["#FFC34F", "#FF6D60", "#44B2FB"],//图例图标颜色
      // legend: {
      //   show: true,
      //   itemGap: 25,//每个图例间的间隔
      //   top: 30,
      //   x: 30,//水平安放位置,离容器左侧的距离  'left'
      //   z: 100,
      //   textStyle: {
      //     color: '#383838'
      //   },
      //   data: [//图例具体内容
      //     {
      //       name: '财运',//图例名字
      //       textStyle: {//图例文本样式
      //         fontSize: 13,
      //         color: '#383838'
      //       },
      //       icon: 'roundRect'//图例项的 icon，可以是图片
      //     },
      //     {
      //       name: '感情',
      //       textStyle: {
      //         fontSize: 13,
      //         color: '#383838'
      //       },
      //       icon: 'roundRect'
      //     },
      //     {
      //       name: '事业',
      //       textStyle: {
      //         fontSize: 13,
      //         color: '#383838'
      //       },
      //       icon: 'roundRect'
      //     }
      //   ]
      // },
      grid: {//网格
        left: 0,
        top: 100,
        containLabel: true,//grid 区域是否包含坐标轴的刻度标签
      },
      xAxis: {//横坐标
        type: 'category',
        name: '日期',//横坐标名称
        nameTextStyle: {//在name值存在下，设置name的样式
          color: '#5D5D5D',
          fontStyle: 'normal',
          fontSize: 10
        },
        nameLocation: 'end',
        splitLine: {//坐标轴在 grid 区域中的分隔线。
          show: true,
          lineStyle: {
            type: 'dashed'
          }
        },
        boundaryGap: false,//1.true 数据点在2个刻度直接  2.fals 数据点在分割线上，即刻度值上

        data: self.data.recorddate,
        
        axisLabel: {
          textStyle: {
            fontSize: 10,
            color: '#5D5D5D'
          }
        }
      },
      yAxis: {//纵坐标
        type: 'value',
        position: 'left',
        name: '情感均值',//纵坐标名称
        nameTextStyle: {//在name值存在下，设置name的样式
          color: '#5D5D5D',
          fontSize: 10,
          fontStyle: 'normal'
        },
        splitNumber: 5,//坐标轴的分割段数
        splitLine: {//坐标轴在 grid 区域中的分隔线。
          show: true,
          lineStyle: {
            type: 'dashed'
          }
        },
        axisLabel: {//坐标轴刻度标签
          formatter: function (value) {
            var xLable = [];
            if (value == 20) {
              xLable.push('很差');
            }
            if (value == 40) {
              xLable.push('差');
            }
            if (value == 60) {
              xLable.push('中等');
            }
            if (value == 80) {
              xLable.push('好');
            }
            if (value == 100) {
              xLable.push('很好');
            }
            return xLable
          },
          textStyle: {
            fontSize: 10,
            color: '#5D5D5D',
          }
        },
        min: 0,
        max: 100,
      },
      series: [{
        name: '情感均值',
        type: 'line',
        data: self.data.recordvalue,
        symbol: 'none',
        itemStyle: {
          normal: {
            lineStyle: {
              color: '#FFC34F'
            }
          }
        }
      }],
    }
    return option;
  },
});
