
// function registry_filter(){
// 	// console.log($("li").find("[data-order-id=16]").text());
// 	$(".s-order-status-pending").filter("[data-order-id=15]").remove();
// }

function registrylogin(){
	var form = $("#registrylogin").serialize();
	var succ = true;
	$.ajax({
		type : "POST",
		url : "/registrylogin/",
		dataType: 'jsonp',
		data: form,
		success: function(msg){
			
		}
	}).complete(function(){
		if(succ){
			location.reload()	
		}
	});
}

function check_date(form){
	var day = $(form).find("#date_day").val();
	var month = $(form).find("#date_mounth").val();
	var year = $(form).find("#date_year").val();
	if(day == 0 || month == 0 || year == 0) return true;
	return false;
}
function createregistry(){
	var form = $("#create_registry_form");
	
	if($(form).find("input[name=reg_event_name]").val() == ""){
		$(".error").html("Not filled the name of the registry");
		return;
	}
	if($(form).find("select[name=reg_event_type]").val() == 0){
		$(".error").html("Not the type of event");
		return;
	}
	if(check_date(form)){
		$(".error").html("Not the selected date events");
		return;
	}
	if($(form).find("input[name=reg_user_firstname]").val() == ""){
		$(".error").html("Not filled the name of the registrant");
		return;
	}
	if($(form).find("input[name=reg_user_lastname]").val() == ""){
		$(".error").html("Not populated last name of the registrant");
		return;
	}
	if($(form).find("input[name=reg_user_address]").val() == ""){
		$(".error").html("Not filled address of the registrant");
		return;
	}
	if($(form).find("input[name=reg_user_city]").val() == ""){
		$(".error").html("Not filled city registrant");
		return;
	}
	if($(form).find("select[name=reg_user_state]").val() == 0){
		$(".error").html("Not the selected state");
		return;
	}
	if(check_zip($(form).find("input[name=reg_user_zip]").val())){
		$(".error").html("Not the right zip");
		return;
	}
	var phone = $(form).find("input[name=reg_user_phone]").val();
	if($(phone) == ""){
		$(".error").html("Not a true phone of the registrant");
		return;
	}
	var co_registrant = ($(form).find("input[name=enable_registrant_co]").prop('checked'));
	if(co_registrant){
		if($(form).find("input[name=reg_user_firstname_co]").val() == ""){
			$(".error").html("Not filled in the name of CO-registrant");
			return;
		}
		if($(form).find("input[name=reg_user_lastname_co]").val() == ""){
			$(".error").html("Not filled in the name of CO-registrant");
			return;
		}
		if($(form).find("input[name=reg_user_address_co]").val() == ""){
			$(".error").html("Address is not filled in CO registrant");
			return;
		}
		if($(form).find("input[name=reg_user_city_co]").val() == ""){
			$(".error").html("Not filled city CO registrant");
			return;
		}
		if($(form).find("select[name=reg_user_state_co]").val() == 0){
			$(".error").html("Not selected state CO registrant");
			return;
		}
		if(check_zip($(form).find("input[name=reg_user_zip_co]").val())){
			$(".error").html("Not the right CO zip");
			return;
		}
		var phone = $(form).find("input[name=reg_user_phone_co]").val();
		if($(phone) == ""){
			$(".error").html("Not the right CO registrant phone");
			return;
		}
	}
	var shipping_registrant_before = $(form).find("input[name=before_event]").filter(":checked").val();
	if(shipping_registrant_before == 3){
		if($(form).find("input[name=reg_user_firstname_before]").val() == ""){
			$(".error").html("Not filled the name of the Recipient");
			return;
		}
		if($(form).find("input[name=reg_user_lastname_before]").val() == ""){
			$(".error").html("Not filled the name of the recipient");
			return;
		}
		if($(form).find("input[name=reg_user_address_before]").val() == ""){
			$(".error").html("Not filled shipping address");
			return;
		}
		if($(form).find("input[name=reg_user_city_before]").val() == ""){
			$(".error").html("Not filled shipping city");
			return;
		}
		if($(form).find("select[name=reg_user_state_before]").val() == 0){
			$(".error").html("Not the selected shipping state");
			return;
		}
		if(check_zip($(form).find("input[name=reg_user_zip_before]").val())){
			$(".error").html("Not the right recipient`s zip");
			return;
		}
		var phone = $(form).find("input[name=reg_user_phone_before]").val();
		if($(phone) == ""){
			$(".error").html("Not the right recipient's phone");
			return;
		}
	}
	$(".error").html("");
	var new_form = $(form).serialize();
	var succ = false;
	$.ajax({
		type : "POST",
		url : "/registrycreatereg/",
		dataType: 'json',
		data: new_form,
		success: function(msg){
			if(msg[1] == 1){
				document.location.replace("/registrymanage");
			}
			else{
				$(".error").html("Error");
			}
		}
	}).complete(function(){
			console.log("Error");
	});
}



function check_zip(zip){
	if(zip != ""){
		if(isFinite(zip)){
			return false;
		}
	}
	return true;
}
function registryfind_function(){
	var	lastname = $("#lastname").val();
	var	firstname = $("#firstname").val();
	if(lastname == "" || firstname == ""){
		if(lastname == ""){
			$(".warning_registry").text("Lastname is not entered");
		}
		else{
			$(".warning_registry").text("Firstname is not entered");
		}
	}
	else{
		$(".warning_registry").text("");
		var form = $("#registryfind_form").serialize();
		$.ajax({
			type : "POST",
			url : "/registryfind/",
			dataType: 'json',
			data: form,
			success: function(msg){
				$("#registry_result").html(msg[1]);
			}

		});
	}
}
function add_uniq_product(element){
	var form = $(element).prev().serialize();
	$.ajax({
		type : "POST",
		url : "/registryaddprd/",
		dataType: 'json',
		data: form,
		success: function(msg){
			$('#exampleModal').find("#exampleModalInfo").html(msg[1]);
		}
	});
}

$(document).ready(function() {
	$(".date-select").on("change", function(event){
		if($(this).attr("name") == "date_mounth"){
			if($(this).val() == 2){
				if($("#date_day").val() > 28) $("#date_day").val("0");  
				$.each($("#date_day").find("option"), function(){
					if($(this).val() > 28) $(this).css("display", "none");
				});
			}
			else if($(this).val() == 4 || $(this).val() == 6 || $(this).val() == 9 || $(this).val() == 9){
				if($("#date_day").val() > 30) $("#date_day").val("0");  
				$.each($("#date_day").find("option"), function(){
					if($(this).val() > 30) $(this).css("display", "none");
					else $(this).css("display", "block");
				});
			}
			else{
				$.each($("#date_day").find("option"), function(){
					$(this).css("display", "block");
				});
			}
		}
	});




	$(".rp_open_details").live("click", function(event){
		var	div = $(this).next();
		if($(div).css("display") == "none"){
			$(div).slideDown(300);
		} 
		else{
			$(div).slideUp(300);
		} 
	});
	$(".del_registry").on("click", function(event){
		if(confirm("Are you sure you want to delete the registry?")){
			var reg_id = $(this).closest(".register_action").find(".registr").val();
			$.ajax({
				type : "POST",
				url : "/registrydelete/",
				dataType: 'json',
				data: {reg_id : reg_id},
				success: function(msg){
					if(msg[1] == 1){
						location.reload();
					}
					else{
						$(".error").text(msg[1]);
					}
				}
			});
		}
	});
	$(".del_registry_product").on("click", function(event){
		var sku_id = $(this).parent().prev().find(".sku_id").val();
		var reg_id = $(this).parent().prev().find(".registr").val();
		$.ajax({
			type : "POST",
			url : "/registryproductdelete/",
			dataType: 'json',
			data: {reg_id : reg_id, sku_id : sku_id},
			success: function(msg){
				if(msg[1] == 1){
					location.reload();
				}
				else{
					$(".error").text(msg[1]);
				}
			}
		});
	});
	$(".registry_product_cange").on("click", function(event){
		var sku_id = $(this).attr("data_sku");
		var reg_id = $(this).attr("data_reg");
		var	new_count = $(this).prev().val();
		console.log(sku_id);
		$.ajax({
			type : "POST",
			url : "/registryproductchange/",
			dataType: 'json',
			data: {reg_id : reg_id, sku_id : sku_id, new_count : new_count},
			success: function(msg){
				if(msg[1] == 1){
					location.reload();
				}
				else{
					$(".error").text(msg[1]);
				}
			}
		});
	});
	$(".input_shipping").on("change", function(event){
		if($(this).val() == 3){
			$("#form_shipping").slideDown(300);
		}
		else if($(this).val() == 2){
			if(!$("#enable_registrant_co").prop("checked")){
				$("#enable_registrant_co").trigger("click");
			}
			$("#form_registrant_co").slideDown(300);
		}
		else{
			$("#form_shipping").slideUp(300);
		}
	});
	$("#enable_registrant_co").on("click", function(event){
		if($(this).prop("checked")){
			$("#form_registrant_co").slideDown(300);
		}
		else{
			$("#form_registrant_co").slideUp(300);
		}
	});
	$("#addtoregistrysku").on("click", function(event){
		var product_id = $(this).attr("data_prd");
		var options = $("div.options").find("select");
		var options_id = [];  
		var options_value = [];
		$(options).each(function(indx, element){
			options_id.push($(element).attr("data-feature-id"));
			var option = $(element).find("option");
			$(option).each(function(ind, elem){
				if($(elem).prop("selected")){
					options_value.push($(elem).attr("value"));
				}
			});
		});
		count = $("div").find(".add2cart").find("[name=quantity]").val();
		$.ajax({
			type : "POST",
			url : "/registryadd/",
			dataType: 'json',
			data: {option_ids : options_id, option_values : options_value, product_id : product_id, count : count},
			success: function(msg){
				$('#exampleModal').find("#exampleModalInfo").html(msg[1]);
				$('#exampleModal').arcticmodal();
				if(msg[2] == 1){
					$('#exampleModal').find(".rigistry_link").trigger("click");
				}
			}
		});
	});
	$(".buy_registry_product").on("click", function(event){
		var reg_count = parseInt($(this).attr("data_cnt"));
		var form = $(this).prev(".addtocart");
		var quantity = parseInt($(form).find("input[name=quantity]").val());
		if(quantity > reg_count){
			$(".error").html("Products in the registry is not enough");
		}
		else{
			$("#dialog").find(".cart").html('This product has been added to <a href="/cart/"><strong>your shopping cart</strong></a>');
			$("#dialog").find(".cart").css("top", "35%");
			$("#dialog").find(".cart").css("bottom", "50%");
			$("#dialog").css("display", "block");
			var send_cart = function(){
			  $(form).trigger("submit");
			};
			setTimeout(send_cart, 1000);
			
		}
	});
	$(".rp_status_c").on("click", function(){
		var status = $(this).attr("data_status");
		var block = $(this).closest(".register_action");
		var register_id = $(block).find(".registr").val();
		$.ajax({
			type : "POST",
			url : "/registrychangestatus/",
			dataType: 'json',
			data: {status : status, register_id : register_id},
			success: function(msg){
				if(msg[1] == 1){
					location.reload();
				}
				else{
					$(block).find(".error").html(msg[1]);
				}
			}
		});

	});
	// $(".click_inner_link").on("click", function(event){
	// 	$(this).
	// });
});

// Admin

function rp_delete(register_id){
	if(confirm("Delete thid RP?")){
		$.ajax({
			type : "POST",
			url : "?plugin=registrypage&action=rpdelete",
			dataType: 'json',
			data: {register_id : register_id},
			success: function(msg){
				location.reload();
			}
		});
	}
}

function rp_not_active(register_id, status){
	$.ajax({
		type : "POST",
		url : "?plugin=registrypage&action=rpstatus",
		dataType: 'json',
		data: {register_id : register_id, status : status},
		success: function(msg){
			selectRp(register_id);
		}
	});
}

function selectRp(register_id){
	$.ajax({
		type : "POST",
		url : "?plugin=registrypage",
		dataType: 'html',
		data: {register_id : register_id},
		success: function(msg){
			$("#rp_content").html($(msg).find("#rp_content").html());
			$(".rp_open").removeClass("rp_selected");
			$(".rp_open").filter("[data_rp="+register_id+"]").addClass("rp_selected");
		}
	});
}

function filter_rp(){
	var form = $("#rp_filter_form").serialize();
	$.ajax({
		type : "POST",
		url : "?plugin=registrypage",
		dataType: 'html',
		data: form,
		success: function(msg){
			$("#s-content").html($(msg).find("#s-content").html());
		}
	});
}

