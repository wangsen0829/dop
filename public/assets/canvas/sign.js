/**

 @Title: sign 签名组件
 @License：MIT

 */

 layui.define('jquery', function (exports) {
    "use strict";
    var $ = layui.jquery
  
      //外部接口
      , sign = {
        config: {}
  
        //设置全局项
        , set: function (options) {
          var that = this;
          that.config = $.extend({}, that.config, options);
          return that;
        }
  
        //事件
        , on: function (events, callback) {
          return layui.onevent.call(this, MOD_NAME, events, callback);
        }
      }
  
      //操作当前实例
      , thisRate = function () {
        var that = this
          , options = that.config;
        return {
          clear: function () {
            that.clear.call(that);
          },
          getimg: function (value) {
            that.getimg.call(that, value);
          },
          watermark: function (value) {
            that.watermark.call(that, value);
          }
          , config: options
        }
      }
  
  
      //字符常量
      , MOD_NAME = 'sign'
  
      //构造器
      , Class = function (options) {
        var that = this;
        that.config = $.extend({}, that.config, sign.config, options);
        that.init();
      };
  
    //默认配置
    Class.prototype.config = {
      width: 500, //画布宽度
      height: 200,//画布高度
      strokeColor: "#000",//画笔颜色  
  
      maxV: 4,
      minV: 0.1,
      maxLineWidth: 5,
      minLineWidth: 1,
      isWatermark: false,
      name: '',
  
      watermark: {
        watermark: new Date().getTime(),
        text: '',
        num: 5,
      }
  
    }
    //默认配置
    Class.prototype.default = Class.prototype.config;
  
    //初始化
    Class.prototype.init = function () {
      console.log('render');
      var that = this
        , options = that.config
  
        , isMouseDown = false//鼠标  
        , lastLoc = { x: 0, y: 0 }  //上一次绘制的的坐标  
        , lastTimestamp = 0  //初始记录事件  
        , lastLineWidth = -1//上一次线条宽度  
  
  
  
        // -------------
        //线条宽度  
        , calcLineWidth = function (t, s) {
          var v = s / t;
          var resultLineWidth;
          if (v <= options.minV) {
            resultLineWidth = options.maxLineWidth;
          } else if (v >= options.maxV) {
            resultLineWidth = options.minLineWidth;
          } else {
            resultLineWidth = options.maxLineWidth - (v - options.minV) / (options.maxV - options.minV) * (options.maxLineWidth - options.minLineWidth);
          }
          if (lastLineWidth == -1) {
            return resultLineWidth;
          } else {
            return lastLineWidth * 2 / 3 + resultLineWidth * 1 / 3;
          }
        }
        //速度 = 路程 / 时间     用来计算书写速度来改变线条粗细  
        , calcDistance = function (loc1, loc2) {
          //返回 数的平方根  
          return Math.sqrt((loc1.x - loc2.x) * (loc1.x - loc2.x) + (loc1.y - loc2.y) * (loc1.y - loc2.y));
        }
        , moveStroke = function (point) {
          //开始绘制直线  
          var curLoc = windowToCanvas(point.x, point.y);
          //路程  
          var s = calcDistance(curLoc, lastLoc);
          //结束时间  
          var curTimestamp = new Date().getTime();
          //时间差  
          var t = curTimestamp - lastTimestamp;
          //绘制线条粗细  
          var lineWidth = calcLineWidth(t, s);
          //绘制
          options.context.beginPath();
          options.context.moveTo(lastLoc.x, lastLoc.y);
          options.context.lineTo(curLoc.x, curLoc.y);
          options.context.strokeStyle = options.strokeColor;
          options.context.lineWidth = lineWidth;
          options.context.lineCap = "round";
          options.context.lineJoin = "round";
          options.context.stroke();
          //给lastLoc赋值维护  
          lastLoc = curLoc;
          //时间更新  
          lastTimestamp = curTimestamp;
          lastLineWidth = lineWidth;
        }
        , endStroke = function () {
          isMouseDown = false;
        }
  
        //获取canvas 坐标 x，y 分别代表相对window内的xy  
        , windowToCanvas = function (x, y) {
          //canvas提供的方法返回canvas 距 他外围包围盒子的距离left,top值  
          var bbox = options.canvas.getBoundingClientRect();
          //返回的就是canvas 内的坐标值  
          return { x: Math.round(x - bbox.left), y: Math.round(y - bbox.top) }
        }
        //封装 事件  
        , beginStroke = function (point) {
  
  
  
          if (that.config.name != '') {
            that.config.name = '';
            that.clear();
          }
          isMouseDown = true;
          //第一次用户画的坐标初始值  
          lastLoc = windowToCanvas(point.x, point.y);
          //获取首次点击鼠标 事件戳  
          lastTimestamp = new Date().getTime();
        }
  
      // -------------
      options.canvas = $(options.elem).get(0);
      options.context = options.canvas.getContext('2d');
      options.ctx = options.context;
      options.canvas.width = options.width;
      options.canvas.height = options.height;
  
      if (that.config.name != '') {
        options.ctx.save();
        options.ctx.font = '150px Arial';//字体大小也会影响的哦。
        options.ctx.fillStyle = "#999999";
        options.ctx.globalAlpha = 0.5;
        options.ctx.textAlign = 'center';
        options.ctx.textBaseline = "middle";
        options.ctx.fillText(that.config.name, options.canvas.width * 0.5, options.canvas.height * 0.5);
  
        options.ctx.restore();
      }
  
  
  
  
      //pc鼠标事件  
      options.canvas.onmousedown = function (e) {
        e.preventDefault();
        beginStroke({ x: e.clientX, y: e.clientY });
      }
      options.canvas.onmouseup = function (e) {
        e.preventDefault();
        endStroke();
      }
      options.canvas.onmouseout = function (e) {
        e.preventDefault();
        endStroke();
      }
  
      options.canvas.onmousemove = function (e) {
        e.preventDefault();
        if (isMouseDown) {
          moveStroke({ x: e.clientX, y: e.clientY });
        }
      }
      //移动端  
      options.canvas.addEventListener("touchstart", function (e) {
        e.preventDefault();
        var touch = e.touches[0]; //限制一根手指触碰屏幕  
  
        beginStroke({ x: touch.pageX, y: touch.pageY });
      });
      options.canvas.addEventListener("touchend", function (e) {
        e.preventDefault();
        endStroke();
      });
      options.canvas.addEventListener("touchmove", function (e) {
        e.preventDefault();
        if (isMouseDown) {
          var touch = e.touches[0];
          moveStroke({ x: touch.pageX, y: touch.pageY });
        }
      });
      that.watermarks();
  
    };
  
  
    //重画
    Class.prototype.clear = function (value) {
      console.log('clear');
      var that = this
      that.config = $.extend({}, that.config);
      // console.log(sign.config);
  
      that.init();
  
  
    };
    //水印
    Class.prototype.watermark = function (value) {
      var that = this
      that.config.isWatermark = true;
  
      that.config.watermark = $.extend({}, that.config.watermark, value);
      that.watermarks()
    }
    Class.prototype.watermarks = function (value) {
      console.log('watermarks');
      var that = this
      var ctx = that.config.context
      var canvas = that.config.canvas
  
  
      if (!that.config.isWatermark) {
        return;
      }
      // that.clear();
  
  
      that.config.watermark = $.extend({}, that.config.watermark, value);
  
      // // that.config = $.extend({}, that.config, sign.config, options);
      var random = function (min, max) {
        return Math.floor(Math.random() * (max - min)) + min;
      }
      ctx.save();
      ctx.font = '22px Arial';//字体大小也会影响的哦。
      ctx.globalAlpha = 0.03;
      ctx.fillStyle = "#000000";
      // ctx.fillStyle = "#0000000d";
      ctx.textAlign = 'center';
      ctx.textBaseline = "middle";
      // ctx.fillStyle = "red";
      var watermark_width = ctx.measureText(that.config.watermark.watermark).width;
      var watermark_height = 10;
      for (let index = 0; index < that.config.watermark.num; index++) {
        ctx.fillText(that.config.watermark.watermark, random(watermark_width / 2, canvas.width - watermark_width / 2), random(watermark_height / 2, canvas.height - watermark_height));
  
      }
      ctx.globalAlpha = 1;
      ctx.font = '12px Arial';//字体大小也会影响的哦。
      ctx.fillStyle = "#999";
      ctx.fillText(that.config.watermark.text + ' ' + that.config.watermark.watermark, canvas.width * 0.5, canvas.height - 12);
      ctx.restore();
    };
  
    //获取图像
    Class.prototype.getimg = function (value) {
      console.log('getimg', value);
      var that = this
      var set = {
        type: 'png'
      }
      set = $.extend({}, set, value);
      var image = new Image();
      image.src = that.config.canvas.toDataURL("image/" + set.type);
      console.log(image);
    };
  
  
    //事件处理
    Class.prototype.events = function () {
      console.log('events');
      var that = this
        , options = that.config;
    };
  
    //核心入口
    sign.init = function (options) {
      console.log('options', options);
      var inst = new Class(options);
      return thisRate.call(inst);
    };
  
    exports(MOD_NAME, sign);
  })