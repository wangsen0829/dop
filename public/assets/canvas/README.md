# layui-h5-canvas网页签字效果支持PC与手机

#### 介绍
h5 canvas 签字效果 支持电脑与手机(有笔锋效果)

![电脑效果](https://images.gitee.com/uploads/images/2021/0904/225049_009ae466_415712.png "未命名1630766843.png")
![手机效果](https://images.gitee.com/uploads/images/2021/0904/225121_5da81b51_415712.jpeg "微信图片_20210904225056.jpg")

#### 使用方法见 
 参加 index.html


```
     layui.use(['sign'], function () {

            var $ = layui.$;
            var sign = layui.sign;

            //渲染
            var ins1 = sign.render({
                elem: '#canvas'  //绑定元素
            });

            //清除  
            $('#clear_btn').on('click', function (e) {
                console.log(ins1);
                ins1.clear();
                
            });
            //保存画图
            $('#save_btn').on('click', function (e) {
                var image = ins1.getimg();
                console.log(image);
            });

        });
```
代码收集于网络 未找到出处 本人简单的封装为layui扩展


