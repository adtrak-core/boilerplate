var tvc_helper = {
	tvc_alert:function(msg_type=null, msg_subject=null, msg, auto_close=false, tvc_time=7000){
		document.getElementById('tvc_msg_title').innerHTML ="";
		document.getElementById('tvc_msg_content').innerHTML ="";
		document.getElementById('tvc_msg_icon').innerHTML ="";

		if(msg != ""){
			let tvc_popup_box = document.getElementById('tvc_popup_box');
			tvc_popup_box.classList.remove("tvc_popup_box_close");
			tvc_popup_box.classList.add("tvc_popup_box");

	  	//tvc_popup_box.style.display = "block";
	  	document.getElementById('tvc_msg_title').innerHTML =this.tvc_subject_title(msg_type, msg_subject);
			document.getElementById('tvc_msg_content').innerHTML =msg;
			if(msg_type=="success"){
				document.getElementById('tvc_msg_icon').innerHTML ='<i class="fas fa-check-circle fa-3x tvc-success"></i>';
			}else{
				document.getElementById('tvc_msg_icon').innerHTML ='<i class="fas fa-exclamation-circle fa-3x"></i>';
			}
			if(auto_close == true){
				setTimeout(function(){  //tvc_popup_box.style.display = "none";				
					tvc_popup_box.classList.add("tvc_popup_box_close");
					tvc_popup_box.classList.remove("tvc_popup_box");				
				}
				, tvc_time);
			}
		}
	},
	tvc_subject_title:function(msg_type=null, msg_subject=null){
		if(msg_subject == null || msg_subject ==""){
			if(msg_type=="success" ){
				return '<span class="tvc-success">Success!!</span>';
			}else{
				return '<span class="tvc-error">Oops!</span>';
			}
		}else{
			if(msg_type=="success" ){
				return '<span class="tvc-success">'+msg_subject+'</span>';
			}else{
				return '<span>'+msg_subject+'</span>';
			}
		}		
	},
	tvc_close_msg:function(){
		let tvc_popup_box = document.getElementById('tvc_popup_box');
		tvc_popup_box.classList.add("tvc_popup_box_close");
		//tvc_popup_box.style.display = "none";
	},
	loaderSection:function(isShow) {
	  if (isShow){
	    $('#feed-spinner').show();
	  }else{
	    $('#feed-spinner').hide();
	  }
	}
};