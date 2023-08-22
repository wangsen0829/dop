$(function() {
	//获得画布元素
	var canvas = document.getElementById("signCanvas");
	//获得二维绘图对象
	var ctx = canvas.getContext("2d");
	initCanvas();
	//每次绘画重新开始
	canvas.onmousedown = function(e) {
		var e = e || window.event;
		ctx.moveTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
		document.onmousemove = function(e) {
			var e = e || window.event;
			ctx.lineTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
			ctx.stroke();
		};
		document.onmouseup = function() {
			document.onmousemove = null;
			document.onmouseup = null;
		};
	};
	//画板初始化
	function initCanvas() {
		canvas.width = $(".signNameCanvasBox").width();
		canvas.height = $(".signNameCanvasBox").height();
		//设置线宽
		ctx.lineWidth = '2';
		//线条颜色
		ctx.strokeStyle = 'red';
	}






	//重写签名
	function clearCanvas() {
		initCanvas();
	}
	$(".chongxie").click(function() {
		clearCanvas()
	})
	//生成
	$(".shengcheng").click(function() {
		var reg = canvas.toDataURL("image/png"); //跳转页面手动保存
		console.log(reg);
		//        var reg=canvas.toDataURL("image/png").replace("image/png","image/octet-stream");//直接自动保存下载
		// location.href=reg;
	})
})
