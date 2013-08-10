$('document').ready(function(){
	$('.inodata_id_list').select2({allowClear:true});
	$('.filter-deliver-date').datepicker({ dateFormat: "yy-mm-dd" });
	
	loadSlidingTabsEfects();
	
	function loadSlidingTabsEfects(){
		$("#slidetabs").slidetabs({ 
			responsive:true, 
			touchSupport:true, 
			autoHeight:true, 
			autoHeightSpeed:300, 
			contentEasing:"easeInOutQuart",
			onTabClick: function(){
				var id = $(this).attr('href').replace('#tab-', '');
				/*
				if(reAsigning!=0){
					reasignOrder(id);
				}*/
				
				$('.st_view.tab-'+id).find('.st_view_inner').prepend($('.inner-filters').detach());
				loadCollectorOrders(id);
			}
		});
	}
	
	function loadCollectorOrders(id){
		var url = Routing.generate('inodata_flora_collection_orders_by_collector', {id:id});
		
		$.get(url, function(data){
			$('.st_view.tab-'+data.id).find('tbody').html(data.orders);
			$('#slidetabs').slidetabs().setContentHeight();
			updatePayments(data);
		}, 'json');
	}
	
	$('#inodata_collection_type_form_id').change(function(){
		var orderId = $(this).val()!=''?id=$(this).val():id=0;
		var collectorId = $('.collector-tab.st_tab_active').attr('href').replace('#tab-', '');
		
		var data = {collector_id:collectorId, order_id:orderId}
		
		if( id != 0){
			var url = Routing.generate('inodata_flora_collection_add_order_to_collector');
			$.post(url, data, function(response){
				$('.st_view.tab-'+response.id).find('tbody').prepend(response.order);
				
				updatePayments(response);
				updateOrdersOptions(response.orderOptions);
			},'json');
		}
	});
	
	function updateOrdersOptions(orderOptions)
	{
		$('select.inodata_id_list').html(orderOptions)
			.select2({allowClear:true});
		$('#slidetabs').slidetabs().setContentHeight();
		
	}
	
	function updatePayments(data){
		$('#total-payments').text('$ '+data.payments);
		$('#total-commission').text('$ '+data.commission);
	}
	
	/**
	 * Change order status from list
	 */
	$('.order-action').live('click', function(){
		var orderId = $(this).attr('orderid');
		
		if($(this).hasClass('remove')){
			removeOrderFromCollector(this);
		}
		
		return false;
	});
	
	function removeOrderFromCollector(button){
		var url = $(button).attr('href');
		$.get(url, function(response){
			if(response.success){
				loadCollectorOrders(0);
				updateOrdersOptions(response.orderOptions);
			}
		}, 'json');
	}
	
	//Edit in place employee information
	
	$(".st_tabs_ul li").each(function(){
		$(this).children('a').append($(this).children('div').clone().removeClass('editable-form'));
	});
	
	$.fn.editable.defaults.mode = 'inline';
	var url = Routing.generate('inodata_flora_collectionn_collector_edit_in_place');
	$('.editable-form .edit-employee').editable({
		url:url,
		title:'ADato de empleado',
		emptytext: '----',
		success: function(response, newValue){
			if(response!="success"){
				return "Error";
			}else{
				var column = $(this).attr('column');
				$('.st_tabs_ul a > div .'+column).text(newValue);
			}
			
		}
	});
	
	/***** Funcion para hacer un abono a la order*****/
	var url = Routing.generate('inodata_flora_collection_order_deposit');
	$('.deposit').live('click', function(){
		$.fn.editable.defaults.mode = 'popup';
		$('.order-payments').editable({
			url:url,
			title:'Abono',
			value:'',
			emptytext: '',
			success: function(response, newValue){
				if(response=="success"){
					loadCollectorOrders(0);
				}else{
					if(response=="overflow"){
						return "El abono es mayor al resto"
					}
					return "Error";
				}
			}
			
		});
		
		$(this).closest('tr').find('.order-payments').click();
		return false;
	});
	/************************************************/
	
	/** HACER CORTE DE CAJA EN LOS ABONOS DE LA ORDEN*/
	$('.boxcut').live('click', function(){
		var url = $(this).attr("href");
		$.get(url, function(response){
			if(response.success){
				loadCollectorOrders(0);
			}else{
				alert("Error");
			}
		},'json');
		
		return false;
	});
	
	
	/** Carga popup para ver los abonos detalladamente
	 * para cada orden
	 */
	$('.payment-details').live('click', function(){
		var element = $(this);
		var url = $(this).attr('href');
		
		$.get(url, function(response){
			$(element).closest('.slidetabs').append(response.details);
		}, 'json');
		
		return false;
	});
	
	$('.close-details-popup').live('click', function(){
		$(this).closest('.editable-container').remove();
		return false;
	});
	
	/**
	 * PAY ALL BUTTON OR BOXCUT TO ALL
	 */
	$('.pay-all, .boxcut-all').live('click', function(){
		var url = $(this).attr('href');
		
		$.get(url, function(response){
			if(response.success){
				loadCollectorOrders(0);
			}
		}, 'json');
		
		return false;
	});
	/*---------------------------------*/
	
	
	
	
	
	
	
});