// getinfo/getinfo.js
//获取用户信息登陆页面
const app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    //判断小程序的API，回调，参数，组件等是否在当前版本可用。
    canIUse: wx.canIUse('button.open-type.getUserInfo'),
    accept: false,
    appid: "wxc859e116e42ec727",
    secret: "bd9bad7e2e7e8c052ebb595aa0688264"
  },
  //授权处理
  bindGetUserInfo: function (e) {
    let that = this;
    // console.log(that.code)
    // console.log(e.detail.errMsg)
    // console.log(e.detail.userInfo)
    // console.log(e.detail.rawData)
    console.log(e)
    wx.login({
      success: res => {
        that.code = res.code
        console.log(that.code)
        wx.request({
          url: app.globalData.global + 'wxlogin/index/login',
          method: 'POST',
          data: {
            code: that.code,
            rawData: e.detail.rawData,
            signature: e.detail.signature,
            encryptedData: e.detail.encryptedData,
            iv: e.detail.iv
          },
          header: {
            'content-type': 'application/json'
          },
          success: function (res) {
            // that.hideLoading();
            console.log(res.data)
            app.globalData.session = res.data.data.session3rd
            app.globalData.personphoto = res.data.data.avatarUrl
            app.globalData.openId = res.data.data.openId
            wx.reLaunch({
              url: "../wordkeys/wordkeys"
            })
          }
        })
      },
      fail: err => {
        // that.hideLoading();
        console.log(err)
      }
    });

  },


  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

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