 //手机端JS脚本
   $(function(){
		 var h= document.documentElement.clientHeight || document.body.clientHeight;
			$(".page").height(h);
   	  $(".login").css("transform","translateY("+h/3+"px)");
   	 // var _time=3;
   	 // var _timego;
   	 //    _timego=setInterval(function(){
   	 //    		    _time--;
   	 //  	if(_time==2){
   	 //  		$(".bg_m img").attr("src","/Template/mobile/new/Static/images/2@2x.png")
   	 //  	}else if(_time==1){
   	 //  		$(".bg_m img").attr("src","/Template/mobile/new/Static/images/1@2x.png")
   	 //  	}else if(_time==0){
   	 //  		clearInterval(_timego)
   	 //  		$(".bg_m img").attr("src","/Template/mobile/new/Static/images/GO@2x.png")
   	 //  	//	location.href="play.html"
   	 //
   	 //
   	 //  	}
   	 //    },1000)
   	  
   	   
   })
  /* $(".but").click(function(){
   	 location.href="ready.html";
   })*/
  
   var flag=false;
   // $(".go_but").click(function(){
   // 	  $(".star").css("display","none");
   // 	  $(".end").css("display","block");
   // 	  $(".but_text").html("已准备");
   // 	  $(".go_but img").attr("src","/mobile/Static/an.png")
   // 	 // location.href="time.html"
   // 	  flag=true;
   // 	  if(flag==true){
   // 	  	$(".go_but").unbind()
   // 	  }
   // })