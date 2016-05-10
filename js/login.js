/**
 * 全局导出js,此文件名是login.js,实际上内容不止只有登录，还有其它文件的内容，所以这个js文件，改为全局js文件
 */

 //消息提醒
	function alertChecked(key,value){
	    if(!value)
			{value="flase"}
		$.ajax({
			type: 'get',
			url: 'index.php?r=sysmanage/alertsManage',
			data: {
				alertkey: key,
				alertdisplay:value
			},
			beforeSend: function(){$('#loading').show()},
			success: function(data){
				$('#loading').hide();
				alert(data);
			}
		});
	}
	
    function flow_h(){
     $('.flow').height($('.alertsMenu').height()-24)
    }
	 
	function has_massage(){
	 var a="";
	 $('.alertsMenu li:not(".chilun")').each(function(){
	   if($(this).css("display")=="block")
	   {a="has"}
	 })
	 {$('.massage').removeClass("has").addClass(a);}
	}
	
$(function(){
  //right_left
  var lefts=0;
  $('.to_left').click(function(){
     lefts=$('.left').width();
     $('.left').animate({left:-lefts},200,function(){
	 var win_width=$(window).width();
     $('.nano .head_in,.nano .table_content').width(win_width-246+lefts)
	 });
	 $('.to_right').show();
	 $('.main').animate({left:200-lefts},200);
  })
  
  $('.to_right').click(function(){
     lefts=0;
	 //$('.left').show();
     $('.left').animate({left:0},200);
	 $('.to_right').hide();
	 $('.main').animate({left:200},200);
	 var win_width=$(window).width();
     $('.nano .head_in,.nano .table_content').width(win_width-246+lefts);
  })
  
  
  
  //加title
  $('.show_div span').each(function(){
   $(this).attr('title',$(this).text())
  })
  //消息提醒
  has_massage();
  flow_h();
  $(".help>a").hover(function(){
    $('.alertsListDiv').removeClass("has_hover")
    $('.alertsMenu').hide()
  })
  $(".alertsListDiv").hover(
    function(){
	 $(this).addClass("has_hover")
	 $(this).children(".alertsMenu").show()}
   )
   $(document).live("click",function(e){
	   var target = $(e.target);
	   if(target.closest(".alertsListDiv").length == 0){
	   $(".alertsMenu").hide();
	   $('.alertsListDiv').removeClass("has_hover")
	 }
   })
  $(".chilun").click(function(){
   if($(this).children('a').text()=="提醒设置"){
   $.ajax({
		type : 'post',
		url : 'index.php?r=sysmanage/alertsManage',
		data : {setalert:'setalert'},
		success:function(data){	
			data = eval('('+data+')');
			for(var i in data){
				if(data[i]=="0")
					checked = false;
				else
					checked = true;
				$('#'+i).children('input').attr("checked",checked);
			}
		}
   })
    
    $(".alertsMenu li,.alertsMenu li input").show()
	$(".alertsMenu li a span").hide()
	$(".alertsListDiv .flow").show()
	$(".alertsMenu>li:not('.chilun')").addClass('opation')
	flow_h();
    $(this).children('a').text("返回")
   }
   else if($(this).children('a').text()=="返回"){
   $.ajax({
		type : 'post',
		url : 'index.php?r=sysmanage/alertsManage',
		data : {alertreturn:'alertreturn'},
		success:function(data){
			data = eval('('+data+')');
			for(var i in data){
				if(data[i]=="0")
					checked = "none";
				else
					checked = "block";
				$('#'+i).css("display",checked);
				$(".alertsMenu li input").hide()
				has_massage();
			}
		}
   })
    $(".alertsMenu li").each(function(){
	 var lis=$(this).children("input")
	 if(lis.length>0&&lis.attr("checked")!=="checked")
	 {$(this).hide()}
	})
	$(".alertsMenu>li:not('.chilun')").removeClass('opation')
	$(this).children('a').removeClass('opation').text("提醒设置")
	$(".alertsListDiv .flow").hide()
	$(".alertsMenu li a span").show()
	$(".alertsMenu li input").hide()
   }
  })
   
  //星星
  $('.logo').append("<div class='xingxing'></div>")
  var nums=$(".xingxing>div").length
  for(var i=1;nums<i;nums++)
  {$('.xingxing').append("<div class="+'xing'+nums+"></div>")}
  $(".xingxing>div").show()
  setTimeout(start,3000);
	function start(){
	   var time=Math.round(Math.random()*(1));
	   var name=".xingxing .xing"+time
		$(name).fadeTo(1000,0);
		$(name).fadeTo(1000,1);
		setTimeout(start,4000);
    }
	 
  $('.exprot_box').appendTo('.table_search ul')
  $(".show_menu p").click(function(){
    $(this).parent("div").parent("li").addClass("show_active")	 
 	                     .siblings("li").removeClass("show_active");
	$(this).addClass("show_p").siblings("p").removeClass("show_p");
	$(".click_menu").removeClass("span_active");
	$(this).parent("div").siblings("span").addClass("span_active");
  });  
  	//邦定,系统管理页面中的编辑，弹出div
	$('.config').live('mouseout', function() {
		$(this).children('.down').hide();
	}).live('mouseover', function() {
		$(this).children('.down').show();
	});
  //权限
  $('.createRloeDiv .first li .first span').click(function(){
     
     $(this).parent('div').parent('li').siblings('li').children('.that').hide();
     $(this).parent('div').next().slideToggle();
	 
  });
  $('.listss span').click(function(){
   var $val=$(this).text();
   $(this).parent('.listss').parents('.lists').children('input').val($val);
   $(this).parent('.listss').hide();

  });
  $('.lists .xiala').click(function(){
   $(this).next('.listss').slideToggle();
   $(this).parent('.lists').parent('li').siblings('li').find('.listss').hide();
   $(this).parent('.lists').parent('li').parent('ul').parent('li').siblings('li').find('.listss').hide();
  });
  //导航刷新

   $('.main_table').after("<div id='loader_container' style='display:none;'><div id='loader'><div><img src=\"images/loading1.gif\" />数据计算中...</div></div></div>");
   $('.table_menu:not(".home .table_menu")').append("<div id='loader_container1' style='display:none;'><div><div><img src=\"images/loading2.gif\" />数据计算中...</div></div></div>");
   $("#yw2").children("li").children("a").children("p").children("span").click(function(){
	//	window.setTimeout(rotate, 1000);
		window.setTimeout(aaa1,100);
		function aaa1(){
		//$(".main_table").hide();
		$("#loader_container").show(); 
		}
   });
   $("#submit,.table_menu li a").not('input.notload,.not_flow li a').click(function(){
	window.setTimeout(aaa,5);
		function aaa(){
		 $("#loader_container1").show(); 
		}
   });
   $(".not_pic .table_menu li a").click(function(){
       window.setTimeout(aaa,10);
		function aaa(){
		 $("#loader_container1").hide(); 
		}
	
 });

   $("#yw3").children("li").children("ul").children("li").click(function(){
		window.setTimeout(aaa,100);
		function aaa(){
		 $//(".main_table").hide();
		 //$(".table_content").hide();
	 	 $("#loader_container").show(); 
	    }
   });
   
   $(".content").ajaxComplete(function(event,request, settings){ 
       tbodys();
	   heights();
   })
   
   function tbodys(){
    var nums=$('.nano .head_in').length
    if(nums>0)
	{}
	else{
    var a=$('.nano .table_content').attr("class")
	if(a=="table_content duboule")
	{$('.nano .table_content').before("<table class='duboule head_in'></table>");}
	else if(a=="table_content terminal")
	{$('.nano .table_content').before("<table class='terminal head_in'></table>");}
	else if(a=="table_content urssi")
	{$('.nano .table_content').before("<table class='urssi head_in'></table>");}
	else if(a=="table_content mrssi")
	{$('.nano .table_content').before("<table class='mrssi head_in'></table>");}
    else
	{$('.nano .table_content').before("<table class='head_in'></table>");}
    $('.nano .table_content thead').appendTo('.head_in');
    $('.table_content').wrap("<div class='auto_scroll'></div>");
	$('.nano .pager').appendTo('.auto_scroll')
	}
   }
   tbodys()
  
  $('.items').parent('div').wrap('<div class="sec_scroll"></div>')
  //表格插入
	$('.main_table .duboule #static-information-grid_c0').css("display","none");
	$(".main_table #static-information-grid .duboule thead tr").before("<tr ><th rowspan='2'>IMSI</th><th colspan='6'>系统信息</th><th colspan='4'>终端网络信息</th><th rowspan='2'>业务统计</th></tr>");	
	$(".main_table #static-information-grid .terminal thead tr").before("<tr ><th colspan='6'>终端信息</th><th colspan='4'>终端业务信息</th></tr>");	
	$(".main_table #static-information-grid .urssi thead tr").before("<tr ><th colspan='4'>终端信息</th><th colspan='9'>终端信号信息(RSSI/RSRP单位：dbm)</th><th rowspan='2'>近期业务</th></tr>");	
	$(".main_table #static-information-grid .mrssi thead tr").before("<tr ><th colspan='2'>终端信息</th><th colspan='9'>终端信号信息(RSSI/RSRP单位：dbm)</th></tr>");	
	$('#cell_identity_change_record-grid_c6').css("display","none");
	$('#cell_identity_change_record-grid_c7').css("display","none");
	$('.main_table .duboule #static-information-grid_c9').css("display","none");
	$('.main_table .urssi #static-information-grid_c13').css("display","none");
	$("#cell_identity_change_record-grid .duboule thead tr").before("<tr ><th colspan='3'>Cell A</th><th colspan='3'>Cell B</th><th rowspan='2'>乒乓切换次数</th><th rowspan='2'>操作</th></tr>");
	
  //class
  
  
  //高度计算
  /* $(".content").ajaxComplete(function(event,request, settings){ 
		heights();  
  }); */
  
  heights();
  $(window).resize(function() {
    heights();
  });
 
   
  function heights(){
   var win_width=$(window).width();
   $('.nano .head_in,.nano .table_content').width(win_width-246+lefts);
   var win_height=$(window).height();
   var h1=$('.table_search').height()||0;
   var h2=$('.table_order').height()||0;
   var h3=$('.errorSummary').height()||0;
   var h4=$('.head_in').height()||0;
   var nano=win_height-h1-h2-h4-203;
   $('.main1').not('.home .main1').height(win_height-158);
   $('.help_all').height(win_height-197);
   $('.nano .sec_scroll').height(win_height-h1-h2-200);
   $('.home_p1 .nano .sec_scroll').height(win_height-h3-293);
   $('.nano .form').height(win_height-h1-h2-200)
   $('.biao').height(win_height-h1-h2-198);
   $('.biao1').height(win_height-h1-h2-202);
   $('.biao2').height(win_height-h1-h2-202);
   $('.nano .auto_scroll').height(nano);
   $('.home_p2 .nano').height(win_height-h3-202);
   $('.home_p1 .nano').height(win_height-h3-298);
   $('.home_p').height(win_height-220);
   $('.home_p2').height(win_height-200);
   $('.sec_menu').height(win_height-165);
   $('.maps .nano').height(win_height-h1-h2-210);
   $('.index').height(win_height-145)
  }
  
  //小区数据业务分析
  $("#content .tb:eq(0)").show();
  $(".table_menu li").click(function(){
    var $num=$(this).index();
	$("#content .tb:eq("+$num+")").fadeIn(300)
	                                .siblings(".tb").hide();
  });
  
  $(".UserViewPage,.iframe_autoheight").load(function(){
    var mainheight = $(this).contents().find("body").height();
    $(this).height(mainheight);
  }); 
  //
   $('.sec_menu > li:last').css('border-bottom','none');
   
  //gis切换
   $('.gis_ping span').click(function(){
     $(this).addClass('select')
	        .siblings('span').removeClass('select');
   });

  //
  $("tbody tr:odd").addClass("even");
  $("tbody tr:even").addClass("odd");
  
  //
  $('.netflow_view_tag span:first').addClass('select');
  $('.netflow_view_tag span').click(function(){
    $(this).addClass('select').siblings('span').removeClass('select');
  });
  
  //表单变色
  $('.form_modify :text').live('blur',function(){
        
         var $notnull=$(this).val();
		 var $val=$.trim($notnull)
	     if($val=='')
		 {$(this).css({'background':'#f5f5f5','border':'1px solid #ddd'});}
		 else
		 {
		   if($val==this.defaultValue)
         {$(this).css({'background':'#f5f5f5','border':'1px solid #ddd'});}
           else
         {$(this).css({'background':'url(images/li_select.png) no-repeat right 3px #ECFBD4','border':'1px solid #666'});}
		 }
		 
	
		 
   }); 
   
   //排序
   $('.table_order .order li').live('click',function(){
     var vals=$(this).children('span').text();
	 $('thead tr th a').each(function(){
	   if($(this).text()==vals)
	   {alert(123);$(this).click()}
	 });
   });
   
   
   
   $('.table_order .order li span').attr('class','');
   $('.table_order .order li').click(function(){
     $(this).siblings('li').children('span').attr('class','');
   });
   $('.grid-view table thead tr th a').live('click',function(){
      $('.table_order .order li').children('span').attr('class','');
      $('.table_order .order li').removeClass('select');
      var name=$(this).text();
	  var cls=$(this).attr('class');
	  $('.table_order .order li span').each(function(){
	    if($(this).text()==name)
	    { 
		  $(this).parent('li').addClass('select');
		
		  if(cls==undefined){$(this).attr('class','asc');}
		  
		  else if(cls=="asc"){$(this).attr('class','desc');}
		  
		  else if(cls=="desc"){$(this).attr('class','asc');}
		}
	  });
   });
  //ie8 position:bug
  /* $('.order li').click(function(){
	 var bro=$.browser;
     var binfo="";
     if(bro.version&&bro.version==8) 
	 {
	   var $bg=$(this).children('span').css('background-position-y');
	   if($bg==('3px'))
	   {
	    $(this).siblings("li").children("span").css("background-position-y","3px");
	    $(this).children("span").css("background-position-y","-37px")
	   }
	  
	   else if($bg==('-37px'))
       {
	    $(this).children("span").css("background-position-y","-57px");
	    $(this).siblings("li").children("span").css("background-position-y","3px") 
	   }
	 
	   else if($bg==('-57px'))
       {
	    $(this).siblings("li").children("span").css("background-position-y","3px");
	    $(this).children("span").css("background-position-y","-37px")
	   }
	 }
     else{
	  var $bg=$(this).children('span').css('background-position');
	  if($bg==('100% 3px'))
	  {
	   $(this).siblings("li").children("span").css("background-position","right 3px");
	   $(this).children("span").css("background-position","right -37px")
	  }
	  
	  else if($bg==('100% -37px'))
      {
	    $(this).children("span").css("background-position","right -57px");
	    $(this).siblings("li").children("span").css("background-position","right 3px") 
	  }
	 
	  else if($bg==('100% -57px'))
      {
	  $(this).siblings("li").children("span").css("background-position","right 3px");
	   $(this).children("span").css("background-position","right -37px")
	  }
	 }
  })
  
  */
   
  //input 值
  $(".brand .show_div span").click(function(){
	  var $span=$(this).text();
	  $(this).parent(".show_div").siblings(".sele_div").children("input").val($span);
  });
  
  //导航
  
   $('#allcheckbtn').live('click',function() {
		var checked=this.checked;
		$("input[name='checkboxs\[\]']").each(function() {this.checked=checked;});
	});
	$("input[name='checkboxs\[\]']").live('click', function() {
		$('#allcheckbtn').prop('checked', $("input[name='checkboxs\[\]']").length==$("input[name='checkboxs\[\]']:checked").length);
	});
  
});




//通用

$(function(){
  $(".table_order .order li").click(function(){
    $(this).addClass("select")
	       .siblings("li").removeClass("select");
  });
  
  
  $(".table_menu li a").click(function(){
   $(this).parent('li').addClass("select")
          .siblings("li").removeClass("select");
  });



	//批量
   $("table .checks .option").live('click',function(){
     $(this).next(".allselect").slideToggle(0);
   }); 
});



	function change1(id)
	{		
			add=document.createElement('div');
			add.id='showl';
			document.getElementById('content').appendChild(add);
			pieurl="index.php?r="+id+"/PieHighCharts";
			$.ajax({
			type : "GET",
			url : encodeURI(pieurl,"gb2312"),
			success : function(data){
			$("#showl").html(data);
			$('#mydialog').dialog({autoOpen: false,width: 900,modal: true});
			$('#mydialog').dialog("open");				
			}			
			});
	}

	$(document).ready(function(){
    var bro=$.browser;
    var binfo="";
    if(bro.msie) {binfo="IE"+bro.version;}
    if(bro.mozilla) {binfo="Mozilla Firefox "+bro.version;}
    if(bro.safari) {binfo="Apple Safari "+bro.version;}
    if(bro.opera) {binfo="Opera "+bro.version;}
    
	
    if($.browser.msie&&($.browser.version == "6.0")){
	 $('.header').append("<div class='page123'></div>");
	 $(".page123").html("<div class='pages'></div>"+"<p>您的浏览器内核("+binfo+")过旧，为解决显示错误，请用：</p>"+"IE8，IE9，" + "<a href='http://firefox.com.cn/' target='_new'>火狐浏览器</a>，"+"<a href='http://www.google.cn/intl/zh-CN/chrome/browser/' target='_new'>google浏览器</a>");
	   $(".page123 .pages").click(function(){
        $(this).parent("div").remove();
      });
    }
    });
/**
 * 全局导出js
 * 
 * 判断手机要导出的数量是否大于0,提示用户
 * 如果上一次导出还没有完成,则告诉用户,让用户等待
 * 
 * 将传入的,time_div,中显示用时*秒,一直滚动显示它
 * ajax,get向后台请求.
 * 
 * 调用方式:
 * onclick="global_exprot_excel('/mqs_view/index.php?r=complain/ExprotDat','exprot_note',25)"
 * 
 * @return:
 * <iframe src='index.php?r=site/download&fn="文件名.xls"' style='display:none'></iframe>
 * 
 * @param String url 导出的地址
 * @param String time 显示正在导出的div的id
 * @param int count 可以导出的数量
 * @param Boot isEmptyExprot 空数据是否可以导出,true:可以导出,false:不可以导出
 */
var current_exprot = false;//用全局变量来显示,现在有没有数据在导出
function global_exprot_excel(url,time_div,count,isEmptyExprot){
	if(isEmptyExprot && count == 0){
		alert("没有可导出的数据");
		return;
	}
	if(current_exprot){
		//alert("系统正在导出,请稍候...");//,用户不可以再导出
		return;
	}
	current_exprot = true;

	var j=0;
	var img_box = $("#"+time_div);
	var eptime = setInterval(function(){
		img_box.html("用时:"+Math.floor(j/100)+"."+j%100+"秒");
		j++;
	},10);
	img_box.show();
	
	$.ajax({
		type : "GET",
		url : url,
		success : function(v){
			clearInterval(eptime);
			img_box.hide();
			img_box.html(v);
			var timeer = setInterval(function(){
				clearInterval(timeer);
				current_exprot = false;
			},1000);
		} 
	});
}