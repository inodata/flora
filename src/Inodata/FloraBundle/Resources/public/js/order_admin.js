$(document).ready(function() { 
	var isEnteredInModal = false;
	var isCustomerToSelect2 = false;
	var isEditingCustomer = false;
	
		//var customer = $("select.inodata_customer").first();
	//alert($(customer).attr("class"));
	$("select.inodata_customer").select2({
		allowClear: 'true'
	});
	
	$('.inodata_delivery_date').datepicker({ dateFormat: "dd/mm/yy" });
	$(".inodata_invoice_date").closest('fieldset').css('display','none');
    
    //APLICA AUTOSCROLL A LA PANTALLA DE CAPTURA DE ORDEN
    if($(".order-capture").length > 0){
        $(window).scrollTop(120);
    }
	
	 $( "input, .select2-container" ).tooltip({
		 show: {
		 effect: "slideDown",
		 delay: 250}
	 });
     
     //FUNCIONALIDAD DEL BUTTON GUARDAR COMO NUEVO
     $('.save_as-new').click(function(){
         var url = $(this).closest('form').attr('action');
         var uniqid = url.split('?');
         uniqid = uniqid[1];
         
         var newUrl = $(this).attr('url')+"?"+uniqid;
         $(this).closest('form').attr('action',newUrl);
         
         return true;
     });
	
	//Mueve el elemento de orservaciones en el pedido al final.
	var notesContainer = $('.inodata-order-notes').closest('div.control-group');
	$('.list-products-content .table-1').append($(notesContainer).clone());
	$(notesContainer).remove();
	
	//---Reactiva el widget de Select2 al crear nuevo Customer desde la ventana modal---//
	var customerAction ="";
	var element = $('.inodata_customer').closest('.sonata-ba-field-standard-natural');
	
	$(element).bind('DOMNodeInserted', function(){
	    if(isCustomerToSelect2){
	    	if(customerAction=="new" && $('select.inodata_customer').length > 0){
		    	var selected = $('select.inodata_customer option:last').val();
		    	$('select.inodata_customer').val(selected).change();
		    	isCustomerToSelect2 = false; customerAction = "";
	    	}
	    	updateEditCustomerButton();
	    }
	});
	
	$(".ui-dialog-titlebar-close").live('mousedown', function(){
		customerAction = "";
	});
	
	$(element).bind('DOMNodeRemoved', function(){
	    isCustomerToSelect2 = true;
	});
	
	$(".btn").live('click', function(){
		if($(this).hasClass('btn_edit_customer')){
			customerAction = "edit";
		}else if($(this).closest('div').find(".inodata_customer").length > 0){
			customerAction = "new";
		}
	});
	//---------------------------------------------------------------------------------//
	
	//----------------Fila de seleccion de cliente en la orden------------------//
	$('.inodata_customer').live('change', function(){
		$(this).val()!=''?id=$(this).val():id=0;
	    var url = Routing.generate('inodata_flora_order_filter_contact_by_customer', {customerId:id });
	    
	    $.get(url, function(data){
	    	$("select.inodata_payment_contact option").remove();
	    	$("select.inodata_payment_contact").append(data.contacts);
	    	$("select.inodata_payment_contact").select2({'val':'', allowClear:true});
	    	
	    	$('.order-discount').eq(0).val(data.customer_discount);
	    	updateAjaxTotalsCost();
	    	updateEditCustomerButton();
	    	
	    }, 'json');
	});
	
	updateEditCustomerButton();
	
	function updateEditCustomerButton()
	{
		var id = $('select.inodata_customer option:selected').val();
		var url = Routing.generate('admin_inodata_flora_customer_edit', {id:id});
		
		if(id){
			if($('.btn_edit_customer').length<1){
				createEditCustomerButton(url);
			}else{
				$('.btn_edit_customer').attr('href', url);
			}
		}else{
			$('.btn_edit_customer').remove();
		}
	}
	
	function createEditCustomerButton(url)
	{	
		var button = $('select.inodata_customer').parent().next().children(':first-child').children().clone();
		$(button).attr('href', url).removeClass('sonata-ba-action').addClass('edit_link btn_edit_customer')
			.attr('title', 'Editar').html('<i class="icon-edit"></i>Editar').css('margin', '0 4px 0 4px');
		$('select.inodata_customer').parent().next().children(':first-child').prepend(button);
	}
	//--------------------------------------------------------------------------//
	
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
        $('.payment_contact_form input').eq(4).val(contact.extension);
		$('.payment_contact_form input').eq(5).val(contact.email);
	}
	
	// CREA EL PAYMENT CONTACT SI NO EXISTE
	$('.select2-search > input[type="text"]').live('keyup', function(event){
		if(event.which == 13 && $('div.inodata_payment_contact').hasClass('select2-dropdown-open')){
			if($('.select2-results li:first').attr('class')=="select2-no-results" ){
				var url = Routing.generate('inodata_flora_order_payment_contact_create');
				var customerId = $('select.inodata_customer option:selected').val();
				var name = $(this).val();
	
				$.post(url, {'contactName':name, "customerId":customerId}, function(contact){
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
	    	addProductToList(data);
	    }, 'json');
	});
	
	$('.product-code input').keypress(function(event){
		if (event.which == 13){
			var code = $(this).val(); $(this).val("");
			
		    var url = Routing.generate('inodata_flora_order_product_by_code', {code:code});
		    $.get(url, function(data){
		    	if(data.id!=-1){
		    		addProductToList(data);
		    	}else{
		    		alert('El producto con el codigo "'+code+'" no existe.');
		    	}
		    }, 'json');
			return false;
		}
	});
	
	$('.create-and-add-product').click(function(){
		var code = $('.create-product-form .product-code input').val();
		var description = $('.create-product-form .product-description input').val();
		var price = $('.create-product-form .product-price input').val();
		
		if(description.replace(/ /g,'')!="" && price.replace(/ /g,'')!=""){
			var data = {code:code, description:description, price:price }
			var url = Routing.generate('inodata_flora_order_product_create_and_add');
			
			$.post(url, data, function(data){
				addProductToList(data);
				clearNewProductFields();
			}, 'json');
		}else{
			alert("Datos incompletos");
		}
	});
	
	function addProductToList(data)
	{
		hideEmptyNotification();
        
        if($('#product-'+data.id).length==0){
        	//Add new new row if product doesn't exist
            $(".list-products tbody").append(data.listField);
            //Create a hidden to update on DB
            $('.list-products-content').append(data.optionsToSave);
        }else{
            //Update total if exist
            var cant = parseInt($('#product-'+data.id+" input").val());
        	$('#product-'+data.id+" input").val(cant+1);
        	//Update hidden total to insert in DB
        	$('#data_product_'+data.id).val(cant+1);
        	
        	calculateProductImport($('#product-'+data.id));
        }

        //Clear select
        $(".inodata_product").select2('val', '');

        //Update totals table
        updateAjaxTotalsCost();
	}
	
	function clearNewProductFields(){
		$('.create-product-form .product-code input').val('');
		$('.create-product-form .product-description input').val('');
		$('.create-product-form .product-price input').val('');
	}
	//--------------------------------------------------------------//
	
	//-----Delete product from list and select options ----//
	$(".delete_link").live('click', function(){
	    var id = $(this).closest("tr").attr('product_id');
	    
		//Remove from list
	    $(this).closest("tr").remove();
	    //Remove from imput for save
	    $("#data_product_"+id).remove();
	
	    updateAjaxTotalsCost();
	    showEmptyNotification();
	});
	//-----------------------------------------------------//
	
	//----------Load initial data for edit order ----------//
	var id = $(".order-id").val();
	
	function showEmptyNotification(){
		if($(".product").length===0){
	    	$("#no_products").css('display', 'table-row'); 
	    }
	}
	
	function hideEmptyNotification(){
		var noProducts = $("#no_products");
		if(noProducts.length>0){
	        noProducts.css('display', 'none');
	    }
	}
	
	//Update product list, select-option and prices when amount is change
	$('.product-total').live('keydown', function(event){
	    if(event.which===13){
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
			var id = $(element).closest('tr').attr('product_id');
			var cant = parseInt($(element).val());
			$('#data_product_'+id).val(cant);
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
		if(event.which===13){
			validateShippingOrDiscountChange(this, 'key');
			return false;
		}
	}).live('blur', function(){
		validateShippingOrDiscountChange(this, null);
	});
	
	function validateShippingOrDiscountChange(element, event)
	{
		if($(element).val().match('[0-9]+(\.[0-9][0-9]?)?')){
			if((event==='key') && ($(element).attr('class')==='order-discount')){
				$('.order-discount:first').val($(element).val());
			}
			updateAjaxTotalsCost();
		}else{
			alert('Cantidad inválida');
			$(element).val('0');
		}
	}
	//-------------------------------------------------------------------//

	//-------------------------- Print Card -----------------------------//
	$('.btn-print-card').live('click', function(){
		var from = $('.inodata_from').val();
		var to = $('.inodata_to').val();
		var message = $('iframe').contents().find('body>p').html();
		if( from == "" || to == "" || message == "<br>"){
			alert(trans("alert.card_missing_fields"));
		}else{
            $('form').removeClass('hide_template');
			$(".card_from").html(from);
			$(".card_to").html(to);
			$("table.message_container td").html($('iframe').contents().find('body>p').html());

			$('.invoice_page').addClass('hide_template');
			$('.payment-note').addClass('hide_template'); //Vieja nota
			$('.payment_note_2').addClass('hide_template'); //Nueva nota
			$('.card_page').removeClass('hide_template');
			printCard();
		}
	});

	// Agrega boton para imprimir tarjeta
	var btnPrinCard = $('.btn-print-card');
	$('.inodata_message').closest('.control-group').append($(btnPrinCard).clone());
	$(btnPrinCard).remove();
	//-------------------------------------------------------------------------//
	
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
		var orderDiscount = $(".order-discount");
		if(price.discount===''){
			price.discount = 0;
		}
		
		$(".order-subtotal").text(price.subtotal);
		$(".order-shipping").val(price.shipping);
		orderDiscount.eq(0).val(price.discount);
		orderDiscount.eq(1).val(price.discount_net);
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
		//FIXME: Hack para inicializar el descuento
		if(discount === ''){
			discount = 0;
		}
		var hasInvoice = $('.inodata-has-invoice:checked').val();
		
		if(!hasInvoice){
			hasInvoice = 0;
		}
		
		$('.product').each(function(){
			var productId = $(this).attr('product_id');
			var amount = $(this).find('.product-total').val();
			products.push({'id':productId, 'amount':amount});
		});
		
		var data = {'products':products, 'shipping':shipping, 'discount':discount, 'hasInvoice':hasInvoice};
		$.post(url, data, function(response){
			loadPriceTotals(response.prices);
		}, 'json');
	}
	//------------------------------------------------//
	
	//-------------- Is inovoice require -----------------/
	$('.inodata-has-invoice').click(function(){
		updateAjaxTotalsCost();
	});
	//-----------------------------------------------------
	
	//------------- PRINT INVOICE/NOTE ACTIONS ------------
	if(id){
		var postSaveAction = $('.post_save_action').val();
		switch(postSaveAction)
		{
			case 'print-note':
				$('div.alert > .close').click();
				$('.invoice_page').addClass('hide_template');
				$('.card_page').addClass('hide_template');
				//$('.payment-note').removeClass('hide_template'); //Nota Vieja
				$('.payment-note').addClass('hide_template');
				$('form').addClass('hide_template'); //Nota nueva
				$('.payment_note_2').removeClass('hide_template');//Nota nueva
				printNote();
			break;
			case 'print-invoice':
				$('.invoice_page').removeClass('hide_template');
				$('.payment-note').addClass('hide_template');
				$('.payment_note_2').addClass('hide_template'); //Nueva nota
				$('.card_page').addClass('hide_template');
				printInvoice();
			break;
		}
	}
	//-----------------------------------------------------
	
	// ------------------- INVOICE EDIT IN PLACE ----------------//
	$.fn.editable.defaults.mode = "inline";
	
	var edit_url = Routing.generate('inodata_flora_order_invoice_edit_in_place', {module:"customer"});
	$('.customer-edit-in-place').editable({
		url:edit_url,
		success: function(response, newValue){
			if(!response.success){
				return response.msg;
			}
		},
		emptytext: '-----'
	});
	
	var edit_url = Routing.generate('inodata_flora_order_invoice_edit_in_place', {module:"address"});
	$('.adress-edit-in-place').editable({
		url:edit_url,
		success: function(response, newValue){
			if(!response.success){
				return response.msg;
			}
		},
		emptytext: '-----'
	});
	
	var data='{';
	$('.inodata-shipping-address .mx_state option').each(function(){
		var val = $(this).val();
		var text = $(this).text();
		
		data+='"'+val+'":"'+text+'", ';
	});
	data+='}';
	
	$('.address-select-state').editable({
		source:data,
		url: edit_url,
		success: function(response, newValue){
			if(!response.success){
				return response.msg;
			}
		},
		emptytext: '-----'
	});
	
	function createProductXEditable()
	{
		var edit_url = Routing.generate('inodata_flora_order_invoice_edit_in_place', {module:"orderProduct"});
		
		$.fn.editable.defaults.mode = "inline";
		$('.product-x-editable').editable({
			url:edit_url,
			success: function(response, newValue){
				if(!response.success){
					return response.msg;
				}
			},
			emptytext: '-----'
		});
	}
	
	createProductXEditable();
	//------------------------------------------------------------//
});
