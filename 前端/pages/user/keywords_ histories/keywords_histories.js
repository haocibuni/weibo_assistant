// pages/user/keywords_ histories/keywords_histories.js
const app = getApp();
const util = require('../../../utils/util.js'); 
Page({
  /**
   * 页面的初始数据
   */
  data: {
    //标签云
    labArr: [],
    // 自定义自己喜欢的颜色
    colorArr: ["#EE2C2C", "#ff7070", "#EEC900", "#4876FF", "#ff6100",
      "#7DC67D", "#E17572", "#7898AA", "#C35CFF", "#33BCBA", "#C28F5C",
      "#FF8533", "#6E6E6E", "#428BCA", "#5cb85c", "#FF674F", "#E9967A",
      "#66CDAA", "#00CED1", "#9F79EE", "#CD3333", "#FFC125", "#32CD32",
      "#00BFFF", "#68A2D5", "#FF69B4", "#DB7093", "#CD3278", "#607B8B"],
    // 存储随机颜色
    randomColorArr: []
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let that = this,
      //判断执行
      labLen = 100,
      colorArr = that.data.colorArr,
      colorLen = colorArr.length,
      randomColorArr = [];
    do {
      let random = colorArr[Math.floor(Math.random() * colorLen)];
      randomColorArr.push(random);
      labLen--;
    } while (labLen > 0)
    that.setData({
      randomColorArr: randomColorArr
    });
    wx.request({
        url: app.globalData.global + 'index/index/kwrecord',
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
          // that.setData({
          //   labArr: res.data.data.record,
          // })
          for (let i = 0; i < res.data.data.record.length; i++) {
            if (i > 10) { break; }
            let temp1 = 'labArr[' + i + ']'
            let temp2 = 'labArr[' + i + '].date'
            that.setData({
              [temp1]: res.data.data.record[i],
              [temp2]: util.formatDate(res.data.data.record[i].date * 1000)
            })
          }
        },
        fail: function (err) {
        }, //请求失败
        complete: function () { } //请求完成后执行的函数
    });
    
   
    

   
  },



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

  }
})
