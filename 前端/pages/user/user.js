// person/person.js

const app = getApp();
Page({
  data: {
    personPhoto: "../../icons/wechat.png"

  },
  //个人头像的获得
  getPhoto() {
    this.setData({
      personPhoto: app.globalData.personphoto
    });
  },


  chooseKeywordHistories() {
    wx.navigateTo({
      url: "keywords_ histories/keywords_histories"
    })
  },

  chooseWeiboHistories() {
    wx.navigateTo({
      url: "weibo_histories/weibo_histories"
    })
  },


  chooseSetUp() {
    wx.navigateTo({
      url: "set_up/set_up"
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {

    var that = this;
    that.getPhoto();
    
    //
    // if(that.data.personphoto== ""){
    //   wx.navigateTo({
    //     url: "../getinfo/getinfo"
    //   })
    // }

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

  }
})