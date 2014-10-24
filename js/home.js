;(function($){
	var url=window.location.href;
	$("#scan").click(function(e){
		e.preventDefault();
		
		$(".scanning_pause").show();
		var url=window.location.href;
		var name=url;
		if(url.indexOf("?app_name")>0)
			name=url.split("=")[1];
		if(url.indexOf("&")>0){
			name=name.split("&")[0];
		}
		var state="";
		var u="scan.php?app_name="+name;
		$.get(u,  function(data) {
			state=data;	
			console.log(data);
			$(".progress-bar").attr("style","width:100%");
			$(".scanning_pause").hide();
			if(confirm(data))
				document.location.reload();
		});
		$(".progress-bar").animate({width:"100%"},10000);
		
	})
	$(".hideit").hide();
	$(".more").click(function(e){
		e.preventDefault();
		if($(this).text()=="更多"){
			$(this).text("折叠");
			$(".hideit").show();
			alert($(this).parents(".filter-line").children("li")[0].html());
		}else{
			var cs=$(this).next().children();
			$(".hideit").css("display","inline-table").hide();
			$(this).text("更多");
		}
	})
	
})(jQuery);