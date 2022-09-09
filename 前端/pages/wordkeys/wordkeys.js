// pages/wordkeys/wordkeys.js
import * as echarts from '../../ec-canvas/echarts';
var Chart = null;
const app = getApp();
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
    currentTab: 0,
    search: false,
    searchArray: [],
    searchTxt: "",
    active: [],
    negative: [],
    imageUrl: ""
  },
  onLoad: function () {
    var windowWidth = wx.getSystemInfoSync().windowWidth;
    var windowHeight = wx.getSystemInfoSync().windowHeight;
    //rpx与px单位之间的换算 : 750/windowWidth = 屏幕的高度（rpx）/windowHeight
    var swiper_height = (750 / windowWidth) * windowHeight - 170;
    this.setData({
      swiper_height: swiper_height
    })
  },
  //滑动切换
  swiperTab: function (e) {
    var that = this;
    that.setData({
      currentTab: e.detail.current
    });
  },
  //点击切换
  clickTab: function (e) {
    var that = this;
    if (this.data.currentTab === e.target.dataset.current) {
      return false;
    } else {
      that.setData({
        currentTab: e.target.dataset.current
      })
    }
  },
  input_txt(e) { //输入框输入事件
    this.setData({
      searchTxt: e.detail.value.trim()
    })
  },
  btn_search: function () { //搜索确认事件
    if (this.data.searchTxt == "") {
      wx.showToast({
        title: '关键字为空',
        duration: 1000
      })
      return;
    }
    this.getData();
    // this.buildHistory(this.data.searchTxt) //调用历史记录事件
  },

  getData: function () {
    this.showLoading('正在分析请稍后');
    let that = this;
    // 获得时间戳
    var timestamp = Date.parse(new Date());
    timestamp = timestamp / 1000;
    // console.log("当前时间戳为：" + timestamp);

    wx.request({
      url: app.globalData.global + 'index/index/index',
      header: { //请求头
        "Content-Type": "applciation/json",
        'session': app.globalData.session
      },
      data: {
        "keyword": that.data.searchTxt,
        'openId': app.globalData.openId,
        'timestamp': timestamp
      },
      method: "GET", //get为默认方法/POST
      success: function (res) {
        that.hideLoading();
        console.log(res.data); //res.data相当于ajax里面的data,为后台返回的数据
        let imageUrl = "https://haocibuni.zhangfanglue.com/python/wordCloud/img/" + app.globalData.openId + timestamp + ".png";
        that.setData({
          active: res.data.data.up,
          negative: res.data.data.down,
          activeCount: res.data.data.up_count,
          negativeCount: res.data.data.down_count,
          neutralCount: res.data.data.neutral_count,
          count: res.data.data.up_count + res.data.data.down_count + res.data.data.neutral_count,
          emotion: res.data.data.ave,
          search: true,
          imageUrl: imageUrl
        })
        that.echartsComponnet = that.selectComponent('#mychart');
        //如果是第一次绘制
        if (!Chart) {
          that.init_echarts(); //初始化图表
        } else {
          that.setOption(Chart); //更新数据
          let imageUrl = "https://haocibuni.zhangfanglue.com/python/wordCloud/img/" + app.globalData.openId + timestamp + ".png";
          that.setData({
            imageUrl: imageUrl
          })
        }
      },
      fail: function (err) {
        that.hideLoading();
      }, //请求失败
      complete: function () { } //请求完成后执行的函数
    })
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
    Chart.clear(); // 清除
    Chart.setOption(this.getOption()); //获取新数据
  },
  // 图表配置项
  getOption() {
    var self = this;
    var option = {
      backgroundColor: "#ffffff",
      color: ["#37A2DA", "#32C5E9", "#67E0E3"],
      series: [{
        label: {
          normal: {
            fontSize: 10
          }
        },
        type: 'pie',
        center: ['50%', '50%'],
        radius: [0, '70%'],
        data: [{
          value: self.data.negativeCount,
          name: '消极实例 ' + Math.round(self.data.negativeCount / self.data.count * 1000) / 10 + '%'
        }, {
          value: self.data.activeCount,
            name: '积极实例 ' + Math.round(self.data.activeCount / self.data.count * 1000) / 10 + '%'
        }, {
          value: self.data.neutralCount,
            name: '中立实例 ' + Math.round(self.data.neutralCount / self.data.count * 1000) / 10 + '%'
        },],
        itemStyle: {
          emphasis: {
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowColor: 'rgba(0, 2, 2, 0.3)'
          }
        }
      }]
    }
    return option;
  },
  /**
   * 展示加载
   */
  showLoading: function (message) {
    if (wx.showLoading) {
      // 基础库 1.1.0 微信6.5.6版本开始支持，低版本需做兼容处理
      wx.showLoading({
        title: message,
        mask: true
      });
    } else {
      // 低版本采用Toast兼容处理并将时间设为20秒以免自动消失
      wx.showToast({
        title: message,
        icon: 'loading',
        mask: true,
        duration: 20000
      });
    }
  },
  /**
   * 隐藏加载
   */
  hideLoading: function () {
    if (wx.hideLoading) {
      // 基础库 1.1.0 微信6.5.6版本开始支持，低版本需做兼容处理
      wx.hideLoading();
    } else {
      wx.hideToast();
    }
  },
  //建立搜索记录
  // buildHistory: function (e) {
  //   if (wx.getStorageSync("history").length > 0 && wx.getStorageSync("history").length < 8) {//小于指定数量之内
  //     let index = wx.getStorageSync("history").indexOf(e)
  //     if (index < 0) {//数据不存在时直接追加
  //       searchArray = wx.getStorageSync("history").concat(e)
  //       wx.setStorageSync("history", searchArray)
  //     } else {//数据已存在时调到头部
  //       searchArray = wx.getStorageSync("history")
  //       searchArray.splice(index, 1)
  //       searchArray = searchArray.concat(e);
  //       wx.setStorageSync("history", searchArray)
  //     }
  //   } else if (wx.getStorageSync("history").length >= 8) {//大于指定数量
  //     let index1 = wx.getStorageSync("history").indexOf(e)
  //     if (index1 > -1) {//数据已存在时掉到头部
  //       searchArray = wx.getStorageSync("history")
  //       searchArray.splice(index1, 1)
  //       searchArray = searchArray.concat(e);
  //       wx.setStorageSync("history", searchArray)
  //       return;
  //     }
  //     //数据不存在时删除第一个后追加
  //     searchArray = wx.getStorageSync("history")
  //     searchArray.splice(0, 1)
  //     searchArray = searchArray.concat(e);
  //     wx.setStorageSync("history", searchArray)
  //   } else {//无数据时候直接追加
  //     searchArray = searchArray.concat(e)
  //     wx.setStorageSync("history", searchArray)
  //   }
  // },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  },
})