$(document).ready(function() { 
	var isEnteredInModal = false;
	var isCustomerToSelect2 = false;
	
	$(".inodata_customer, " +
	  ".inodata_payment_contact, " +
	  ".inodata_product, " +
	  ".inodata_category_day, " +
	  ".inodata_messages"
	).select2({ allowClear: true });
	
	$('.inodata_dalivery_date').datepicker({ dateFormat: "yy-mm-dd" })
	
	//---Reactiva el widget de Select2 al crear nuevo Customer desde la ventana modal---//
	var element = $('.inodata_customer').closest('.sonata-ba-field-standard-natural');
	
	$(element).bind('DOMNodeInserted', function(){
	    if(!isCustomerToSelect2){
	        var selected = $('select.inodata_customer option:last').val();
	        $('.inodata_customer').select2();
	        $('.inodata_customer').select2('val', selected);
	    }
	    isCustomerToSelect2 = true;
	});
	
	$(element).bind('DOMNodeRemoved', function(){
	    isCustomerToSelect2 = false;
	});
	//---------------------------------------------------------------------------------//
	
	//--- TRUCO PARA RESOLVER EL PROBLEMA CON LOS TABS EN LA VENTANA MODAL ---//
	$('.ui-dialog').live('mouseenter', function(){
	    $(this).find('.nav-tabs li').each(function(){
	        var element = $(this).find('a:first');
	        var href = $(element).attr('href');
	        var title = $(element).text();
	
	        $(element).remove();
	        $(this).append('<a href="'+href+'">'+title+'</a>');
	    });
	});
	
	$('.ui-dialog .nav-tabs li > a').live('click', function(event){
		$(this).closest('ul').children().removeClass('active');
		$(this).parent().addClass('active');
	
		var tabId = $(this).attr('href');
	
		$('.ui-dialog .tab-content > div').removeClass('active');
		$(tabId).addClass('active');
	});
	
	$('.sonata-ba-action').click(function(){
		isEnteredInModal = false;
	});
	//--------------------------------------------------------------------------//
	
	$('.inodata_customer').live('change', function(){
		$(this).val()!=''?id=$(this).val():id=0;
	    var url = Routing.generate('inodata_flora_order_filter_contact_by_customer', {customerId:id });
	    
	    $.get(url, function(data){
	    	$("select.inodata_payment_contact option").remove();
	    	$("select.inodata_payment_contact").append(data.contacts);
	    	$(".inodata_payment_contact").select2('val','');
	    	
	    	$('.order-discount').eq(0).val(data.customer_discount);
	    	updateAjaxTotalsCost();
	    	
	    }, 'json');
	});
	
	// ---------------- Select a contact an load information ----------------//
	var paymentContactId = $(".inodata_payment_contact option:selected").val();
	if(paymentContactId!=""){
		loadPaymentContactInfo(paymentContactId);
	}
	
	$(".inodata_payment_contact").change(function(){
	    loadPaymentContactInfo($(this).val());
	});
	
	function loadPaymentContactInfo(id)
	{
		var url = Routing.generate('inodata_flora_order_payment_contact', {id:id});
	
		$.get(url, function(contact){
			setPContactDataOnInputs(contact);
		}, 'json');
	}

	function setPContactDataOnInputs(contact)
	{
		$('.payment_contact_form input').eq(0).val(contact.name);
		$('.payment_contact_form input').eq(1).val(contact.department);
		$('.payment_contact_form input').eq(2).val(contact.emp_number);
		$('.payment_contact_form input').eq(3).val(contact.phone);
		$('.payment_contact_form input').eq(4).val(contact.email);
	}
	
	// CREA EL PAYMENT CONTACT SI NO EXISTE
	$('.inodata_payment_contact').prev().find('input[type="text"]').keyup(function(event){
		if(event.which == 13){
			if($('.select2-results li:first').attr('class')=="select2-no-results" ){
				var url = Routing.generate('inodata_flora_order_payment_contact_create');
				var name = $(this).val();
	
				$.post(url, {'contactName' : name}, function(contact){
					$('select.inodata_payment_contact option:selected').removeAttr("selected");
					$('select.inodata_payment_contact')
						.append('<option value="'+contact.id+'" selected="selected">'+contact.name+'</option>');
						setPContactDataOnInputs(contact);
						$(".inodata_payment_contact").select2('val', contact.id);
						$(".inodata_payment_contact").select2('close');
					}, 'json');
				}
		}
	});
	//--------------------------------------------------------------------//
	
	// ------------------------ Messages --------------------------//
	filterMessagesList($('.inodata_category_day').val());
	
	$('.inodata_category_day').change(function(){
		filterMessagesList($(this).val());
	});
	
	function filterMessagesList(val)
	{
		val!=''?id=val:id=0;
		var url = Routing.generate('inodata_flora_order_filter_message_by_category', {categoryId:id });
	
		$.get(url, function(data){
			$('select.inodata_messages option').remove();
			$('select.inodata_messages').append(data.messages);
			$('.inodata_messages').select2('val', '');
		}, 'json');
	}
	
	$('.inodata_messages').change(function(){
		var message = $(this).children(':selected').attr('content');
		if(message!=''){
			$('iframe').contents().find('body').html(message);
	    }
	});
	//-------------------------------------------------------------//
	
	//-- Select a product and insert to select-options and list  --//
	$(".inodata_product").change(function(){
		var id = $(this).val();
	    var url = Routing.generate('inodata_flora_order_product', {id:id});
	    $.get(url, function(data){
	
	        hideEmptyNotification();
	        
	        if($('#'+data.id).length==0){
	        	//Add new new row if product doesn't exist
	            $(".list-products tbody").append(data.listField);
	        }else{
	            //Update total if exist
	            var cant = parseInt($('#'+data.id+" input").val());
	        	$('#'+data.id+" input").val(cant+1);
	        	calculateProductImport($('#'+data.id));
	        }
	        
	        //Add select option with product selected
	        $(".products-to-buy").append(data.selectOption);
	
	        //Clear select
	        $(".inodata_product").select2('val', '');
	
	        //Update totals table
	        updateAjaxTotalsCost();
	        
	    }, 'json');
	});
	//--------------------------------------------------------------//
	
	//-----Delete product from list and select options ----//
	$(".delete_link").live('click', function(){
	    var id = $(this).closest("tr").attr('id');
	    
		//Remove from list
	    $(this).closest("tr").remove();
	    //Remove from select
	    $(".products-to-buy ."+id).remove();
	
	    updateAjaxTotalsCost();
	    showEmptyNotification();
	});
	//-----------------------------------------------------//
	
	//----------Load initial data for edit order ----------//
	var id = $(".order-id").val();
	$(".products-to-buy option").remove();
	
	if(id!=''){
		var url = Routing.generate('inodata_flora_order_products', {id:id});
	    
	    $.get(url, function(data){
	    	$(".list-products tbody").append(data.listFields);
	    	$(".products-to-buy").append(data.selectOptions);
	    	hideEmptyNotification();
	    	//Load price totals for the order editing
	    	loadPriceTotals(data.totals);
	    	loadInvoiceOrderProducts($(data.listFields).clone(), data.totals);
	    }, 'json');
	}
	//----------------------------------------//
	
	
	function showEmptyNotification(){
		if($(".product").length==0){
	    	$("#no_products").css('display', 'table-row'); 
	    }
	}
	
	function hideEmptyNotification(){
		if($("#no_products").length>0){
	        $("#no_products").css('display', 'none'); 
	    }
	}
	
	//Update product list, select-option and prices when amount is change
	$('.product-total').live('keydown', function(event){
	    if(event.which==13){
	    	validateProductsTotalChange(this);
	    	return false;
	    }
	}).live('blur', function(){
			validateProductsTotalChange(this);
	});
	
	function validateProductsTotalChange(element)
	{
		if($(element).val().match('^(0|[0-9][0-9]*)$') && $(element).val()!='0')
		{
			var id = $(element).closest('tr').attr('id');
			var cant = parseInt($(element).val());
	
			var nOptions = $("."+id).length;
			while(nOptions != cant){
				if(nOptions<cant){
					var option = $("."+id).last().clone();
					$(".products-to-buy").append(option);
				}else{
					$("."+id).last().remove();
				}
				nOptions = $("."+id).length;
			}
			calculateProductImport($(element).closest('tr'));
			updateAjaxTotalsCost();
		}else{
			alert('Cantidad invalida');
		}
	}
	
	function calculateProductImport(element){
		var cant = parseInt($(element).find('input').val());
		var price = parseFloat($(element).find('.price').text());
		
		$(element).find('.import').text(cant*price);
	}
	//------------------------------------------------------------//
	
	//-----Update prices when shipping or discount changes -----//
	$('.order-shipping, .order-discount').live('keypress', function(event){
		if(event.which==13){
			validateShippingOrDiscountChange(this, 'key');
			return false;
		}
	}).live('blur', function(){
		validateShippingOrDiscountChange(this, null);
	});
	
	function validateShippingOrDiscountChange(element, event)
	{
		if($(element).val().match('[0-9]+(\.[0-9][0-9]?)?')){
			if((event=='key') && ($(element).attr('class')=='order-discount')){
				$('.order-discount:first').val($(element).val());
			}
			updateAjaxTotalsCost();
		}else{
			alert('Cantidad invalida');
			$(element).val('0');
		}
	}
	//-------------------------------------------------------------------//
	
	//-------------------------- Print Invoice---------------------------//
	function loadInvoiceOrderProducts(listFields, totals)
	{
		$(listFields).each(function(){
			var cant = $(this).children('td:first').find('input').val();
			$(this).children('td:first').html(cant);
			$(this).children('td:last').remove();
			$(this).attr('id', '').removeClass('product')
		});
		
		$('#invoice_order_products table > tbody').append(listFields);
		
		//Load totals
		$('.invoice-subtotal').append(totals.subtotal);
		$('.invoice-iva').append(totals.iva);
		$('.invoice-total').append(totals.total);
		$('.invoice-shipping').append(totals.shipping);
		$('.invoice-discount').append(totals.discount_net);
		
		var invoiceNumber = $('.inodata-invoice-number');
		$('.invoice_data .folio').append($(invoiceNumber).clone().attr('type', 'text'));
		$(invoiceNumber).remove();
		
		var inovicePCondition = $('.inodata-payment-condition');
		$('.invoice_data .payment-condition').append($(inovicePCondition).clone().attr('type', 'text'));
		$(inovicePCondition).remove();
		
		var invoiceComment = $('.inodata-invoice-comment');
		$('.comment-container').append($(invoiceComment).clone().attr('type', 'text'));
		$(invoiceComment).remove();
		
	}
	/*$('.btn-print-invoice').click(function(){
		var orderId = $(".order-id").val();
		if($('#order-invoice-print>div').length==0){
			if(orderId.length!=0){
				loadInvoiceAndPrint(orderId);
			}
		}else{
			window.print();
		}
	});
	function loadInvoiceAndPrint(orderId){
		var url = Routing.generate('inodata_flora_order_create_inovice_totals', {orderId:orderId});
		$.get(url, function(response){
			$('#order-invoice-print').append(response.inovice_totals);
			window.print();
		},'json');
	}*/
	//-------------------------------------------------------------------//
	
	//---------------- Hide select-option fields -----------------//
	hideElement($('.products-to-buy'));
	hideElement($('.order-shipping:first'));
	hideElement($('.order-discount:first'));
	
	function hideElement(element){
		$(element).closest('.control-group').css('display', 'none');
	}
	//------------------------------------------------------------//
	
	function loadPriceTotals(price)
	{
		$(".order-subtotal").text(price.subtotal);
		$(".order-shipping").val(price.shipping);
		$(".order-discount").eq(0).val(price.discount);
		$(".order-discount").eq(1).val(price.discount_net);
		$(".order-iva").text(price.iva);
		$(".order-total").text(price.total);
		$('.order-discount-percent').text(price.discount_percent);
	}
	
	// ---- Update costs via Ajax -----//
	function updateAjaxTotalsCost(){
		var url = Routing.generate('inodata_flora_order_update_totals_cost');
		var products = [];
		var shipping = $('.order-shipping').eq(1).val();
		var discount = $('.order-discount').eq(0).val();
		
		$('.product').each(function(){
			var productId = $(this).attr('product_id');
			var amount = $(this).find('.product-total').val();
			products.push({'id':productId, 'amount':amount});
		});
		
		var data = {'products':products, 'shipping':shipping, 'discount':discount};
		$.post(url, data, function(response){
			loadPriceTotals(response.prices);
		}, 'json');
	}
});
