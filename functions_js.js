
/* Show/Hide comments apo to menu twn comments */
function toggle_comment_show(comment) {
var total = $("#total_comments").val();
for(var i = 0; i <= total; i++){
	if(i==comment){
		$("#ann_"+i).removeClass().addClass('ann_show');
	}else{
		$("#ann_"+i).removeClass().addClass('ann_hidden');
	}
} 
}

/* Show next or previous 5 comments sto menu twn comments */
function prevornext_five_comments(multiplier) {
var pixels = multiplier*14;
$("#ann_list_ul").css('margin-top', '-'+pixels+'px'); 
}

/* Give the user's info in pop up for admin */
function getuserinfo(aem){
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: "aem="+aem+"&get_user_info=1",
		dataType: "html",
		success: function(html){
			html = html+"<br /><br /><center><button type='button' onclick='closeuserinfopop()'>Close Popup</button></center>";
			$("#userinfopopupdiv").html(html);   
			$("#userinfopopupdiv").css('display', 'block');
		}
	}); 
}

/* Klinei to popup me tis plirofories tou xristi */
function closeuserinfopop(){
	$("#userinfopopupdiv").css('display', 'none');
	$("#userinfopopupdiv").html('');
}


/* Show mail form to all users in schedule for admin */
function showmailtoallform(sch_id,title){

	html = "<center>Send mail to all users in "+title+"<br/><form action='functions.php' method='post' id='sendspammail' class='inlineform'><input type='hidden' name='sch_id' value='"+sch_id+"'>";
	html = html+"<br/><textarea name='message' form='sendspammail'>Enter message here.</textarea>";
	html = html+"<br/><input  type='submit' name='spam_ann_mail' value='Send'></form><button type='button' onclick='closespammailform("+sch_id+")'>Close Popup</button></center>";
	
	$("#formspammail_"+sch_id).html(html);
	$("#formspammail_"+sch_id).css('display', 'block');
}

/* Klinei to popup me tis plirofories tou xristi gia mail*/
function closespammailform(sch_id){
	$("#formspammail_"+sch_id).css('display', 'none');
	$("#formspammail_"+sch_id).html('');
}


/* Show sms form to all users in schedule for admin */
function showsmstoallform(sch_id,title){

	html = "<center>Send mail to all users in "+title+"<br/><form action='functions.php' method='post' id='sendspammail' class='inlineform'><input type='hidden' name='sch_id' value='"+sch_id+"'>";
	html = html+"<br/><textarea name='message' id='message_sms_"+sch_id+"' form='sendspammail'>Enter message here.</textarea>";
	html = html+"<br/></form><button type='button' onclick='sendsmstoall("+sch_id+")'>Send SMS</button><button type='button' onclick='closespamsmsform("+sch_id+")'>Close Popup</button></center>";
	
	$("#formspamsms_"+sch_id).html(html);
	$("#formspamsms_"+sch_id).css('display', 'block');
}

/* Send sms all users in schedule for admin */
function sendsmstoall(sch_id){

	ids = $("#sch_ids_"+sch_id).val();
	ids = ids.split(" ");
	message = $("#message_sms_"+sch_id).val();
	success_counter = 0;
	form_html = $("#formspamsms_"+sch_id).html();
	
	$.each(ids, function(index, item) {
		// get the phone number
		$.ajax({
			type: "GET",
			url: "functions.php",
			data: "id="+item+"&get_user_phone=1",
			dataType: "html",
			success: function(html){
			
				if(html != "-1"){
					//send the sms
					$.ajax({
						type: "POST",
						url: "https://vlsi.gr/sms/webservice/process.php",
						data: "message="+message+"&mobilenr="+html+"&authcode=2002415",
						dataType: "html",
						success: function(html){
						}
					}); 
					success_counter=success_counter+1;
					$("#formspamsms_"+sch_id).html(form_html+"<br/>Message was successfully send to "+success_counter+" students");
				}
				
			}
		});	
	}); 

}

/* Emfanizei to popup kai enimerwnei gia tin poria apostolis tou ics e-mail ston admin */
function showicspopup(id,title){
	html = "<center>Collecting data and sending the ICalc e-mail for the schedule \""+title+"\"<br/> . . . <br/></center>";
	$("#popup_modal").html(html);
	$("#popup_modal").show();
	
	$.ajax({
		type: "POST",
		url: "functions.php",
		data: "sch_id="+id+"&ics_admin=1",
		dataType: "html",
		success: function(html){
			$("#popup_modal").html("<center>E-mail send successfully.</center>"+html);
			$("#popup_modal").delay(2000).fadeOut();
		}
	});
}

/* Emfanizei to popup kai enimerwnei gia tin poria apostolis tou ics e-mail ston user */
function showicspopup_stud(sch_info){
	html = "<center>Collecting data and sending the ICalc e-mail for the schedule.<br/>Please make sure your e-mail can accept icalc events.<br/></center>";
	$("#popup_modal").html(html);
	$("#popup_modal").show();
	
	$.ajax({
		type: "POST",
		url: "functions.php",
		data: "sch_info="+sch_info+"&ics_stud=1",
		dataType: "html",
		success: function(html){
			$("#popup_modal").html("<center>E-mail send successfully.</center>");
			$("#popup_modal").delay(2000).fadeOut();
		}
	});
}

/* Klinei to popup me tis plirofories tou xristi gia sms*/
function closespamsmsform(sch_id){
	$("#formspamsms_"+sch_id).css('display', 'none');
	$("#formspamsms_"+sch_id).html('');
}

/* Emfanizei to popup gia edit public announcement. */
function showpopupeditpublcann(ann_id){
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: "id="+ann_id+"&givetopopupeditpublicann=1",
		dataType: "html",
		success: function(html){
			$("#editanndiv_"+ann_id).html(html);   
			$("#editanndiv_"+ann_id).removeClass("hidden").addClass("editpublicannpopup");
		}
	});
}


/* Eksafanizei to popup gia edit public announcement. */
function hidepopupeditpublcann(ann_id){
	$("#editanndiv_"+ann_id).removeClass("editpublicannpopup").addClass("hidden");
}


/* Emfanizei to preview sto popup gia edit public announcement. */
function publicannpreview(ann_id){
	html=$("#textareapublicann_"+ann_id).val();
	$("#public_announcement"+ann_id).html(html);
}
