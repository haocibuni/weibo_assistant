// pages/user/set_up/set_up.js
const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    max:100,
    min:0,
    success:false,
    kwDepth: 0,
    pgDepth: 0,
  },
  changeSlider1:function(e){
    this.setData({ kwDepth: e.detail.value })
  },
  changeSlider2:function(e){
    this.setData({ pgDepth: e.detail.value })
  },
  handleSubmit: function(){
    let that = this;
    wx.request({
      url: app.globalData.global + 'index/index/setSetup',
      header: { //请求头
        "Content-Type": "applciation/json",
        'session': app.globalData.session
      },
      data: {
        'openId': app.globalData.openId,
        'kwDepth': that.data.kwDepth,
        'pgDepth': that.data.pgDepth
      },
      method: "GET", //get为默认方法/POST
      success: function (res) {
        console.log(res.data); //res.data相当于ajax里面的data,为后台返回的数据
        that.setData({
          success: res.data.data.success
        })
        
      },
      fail: function (err) {}, //请求失败
      complete: function () { } //请求完成后执行的函数
    })
  },
  handleBackTap: function(){
    wx.navigateBack({
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let that = this;
    wx.request({
      url: app.globalData.global + 'index/index/getSetup',
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
        that.setData({
          kwDepth: res.data.data.kwDepth,
          pgDepth: res.data.data.pgDepth,
          success: false
        })

      },
      fail: function (err) { }, //请求失败
      complete: function () { } //请求完成后执行的函数
    })

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