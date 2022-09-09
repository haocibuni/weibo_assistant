// pages/weibopage/weibopage.js"3206164850"
import * as echarts from '../../ec-canvas/echarts';
var Chart = null;
const app = getApp();
Page({
  data: {
    ec: {
      onInit: function(canvas, width, height) {
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
    url: '',
    binding_txt: "",
    bind: false,
    analyse: false,
    active: [],
    negative: []
  },
  onLoad: function() {
    var windowWidth = wx.getSystemInfoSync().windowWidth;
    var windowHeight = wx.getSystemInfoSync().windowHeight;
    //rpx与px单位之间的换算 : 750/windowWidth = 屏幕的高度（rpx）/windowHeight
    var swiper_height = (750 / windowWidth) * windowHeight - 170;
    this.setData({
      swiper_height: swiper_height
    })
    // 判断该用户是否绑定微博主页 如果绑定则返回主页地址
    let that = this;
    wx.request({
      url: app.globalData.global + 'index/index/isbind',
      header: { //请求头
        "Content-Type": "applciation/json",
        'session': app.globalData.session
      },
      data: {
        'openId': app.globalData.openId
      },
      method: "GET",
      success: function(res) {
        console.log(res.data);
        that.setData({
          bind: res.data.data.bind_flag,
          url: 'https://weibo.cn/' + res.data.data.weibo_page
        })
      },
      fail: function(err) {},
      complete: function() {}
    })

  },
  //滑动切换
  swiperTab: function(e) {
    var that = this;
    that.setData({
      currentTab: e.detail.current
    });
  },
  //点击切换
  clickTab: function(e) {
    var that = this;
    if (this.data.currentTab === e.target.dataset.current) {
      return false;
    } else {
      that.setData({
        currentTab: e.target.dataset.current
      })
    }
  },
  // 输入微博主页地址
  binding_txt(e) {
    this.setData({
      binding_txt: e.detail.value.trim()
    })
  },
  //绑定微博主页
  btn_binding: function() {
    console.log(this.data.binding_txt)
    if (this.data.binding_txt == "") {
      wx.showToast({
        title: '微博标识为空',
        duration: 1000
      })
      return;
    }
    this.getDate();
  },
  // let that = this;
  getDate: function() {
    let that = this;
    wx.request({
      url: app.globalData.global + 'index/index/binding',
      header: {
        "Content-Type": "applciation/json",
        'session': app.globalData.session
      },
      data: {
        'openId': app.globalData.openId,
        'weibopage_id': that.data.binding_txt
      },
      method: "GET",
      success: function(res) {
        console.log(res.data);
        if (res.data.data.isbind == true) {
          that.setData({
            bind: true,
            url: 'https://weibo.cn/' + that.data.binding_txt
          })
        }
      },
      fail: function(err) {},
      complete: function() {}
    })

  },
  //解绑微博主页
  btn_unbinding: function() {
    let that = this;
    wx.request({
      url: app.globalData.global + 'index/index/unbinding',
      header: {
        "Content-Type": "applciation/json",
        'session': app.globalData.session
      },
      data: {
        'openId': app.globalData.openId,
      },
      method: "GET",
      success: function(res) {
        console.log(res.data);
        if (res.data.data.isbind == false) {
          that.setData({
            bind: false,
            analyse: false,
            url: ''
          })
        }
      },
      fail: function(err) {},
      complete: function() {}
    })
  },
  //分析微博主页评论
  btn_analyse: function() { //搜索确认事件
    this.showLoading('正在分析请稍后~');
    let that = this;
    wx.request({
      url: app.globalData.global + 'index/index/analyse',
      header: { //请求头
        "Content-Type": "applciation/json",
        'session': app.globalData.session
      },
      data: {
        'openId': app.globalData.openId,
      },
      method: "GET", //get为默认方法/POST
      success: function(res) {
        that.hideLoading();
        console.log(res.data); //res.data相当于ajax里面的data,为后台返回的数据
        that.setData({
          active: res.data.data.up,
          negative: res.data.data.down,
          activeCount: res.data.data.up_count,
          negativeCount: res.data.data.down_count,
          neutralCount: res.data.data.neutral_count,
          count: res.data.data.up_count + res.data.data.down_count + res.data.data.neutral_count,
          emotion: res.data.data.ave,
          page_information: res.data.data.page_information,
          analyse: true
        })
        that.echartsComponnet = that.selectComponent('#mychart');
        //如果是第一次绘制
        if (!Chart) {
          that.init_echarts(); //初始化图表
        } else {
          that.setOption(Chart); //更新数据
        }
      },
      fail: function(err) {
        that.hideLoading();

      }, //请求失败
      complete: function() {} //请求完成后执行的函数
    })
  },
  //初始化图表
  init_echarts: function() {
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
  setOption: function(Chart) {
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
          name: '消极评论 ' + Math.round(self.data.negativeCount / self.data.count*1000) / 10  + '%'
        }, {
          value: self.data.activeCount,
            name: '积极评论 ' + Math.round(self.data.activeCount / self.data.count*1000) / 10 + '%'
        }, {
          value: self.data.neutralCount,
            name: '中立评论 ' + Math.round(self.data.neutralCount / self.data.count * 1000) / 10+ '%'
        }, ],
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
  showLoading: function(message) {
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
  hideLoading: function() {
    if (wx.hideLoading) {
      // 基础库 1.1.0 微信6.5.6版本开始支持，低版本需做兼容处理
      wx.hideLoading();
    } else {
      wx.hideToast();
    }
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {

  },
  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },
  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },
  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  },

})