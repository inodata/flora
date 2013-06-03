$('document').ready(function(){	

	$('#filter_deliveryDate_value').datepicker({ dateFormat: "yy-mm-dd" });
	$('.inodata_messenger_list').select2({allowClear:true});
	$('.inodata_id_list').select2({allowClear:true});
	
	/* Refactorizar esta funcion */
	$('div.alert-success').fadeOut(5000, function(){
		$(this).remove();
	});
	/* -----------------------------------*/
	
	$('.delete_link').live('click', function(){
		$(this).closest('tr').remove();
		updateOrderSelectOptions();
		showEmptyNotification();
	});
	
	function updateOrderSelectOptions()
	{
		var orders=[];
		var url = Routing.generate('inodata_flora_distribution_update_orders_available');
		
		$('#messenger_orders tr').each(function(){
			var id = $(this).attr('order_id');
			orders[id] = true;
		});
		
		$.post(url, {orders:orders}, function(data){
			$('#inodata_distribution_type_form_id').html(data.orderOptions);
		}, 'json');
	}
	
	
	$('.add_link').live('click', function(){
		
		var messengerId = $('#inodata_distribution_type_form_messenger').val();
		var orderIds = [];
		var hasOne = false;
		
		$('#messenger_orders tr.item').each(function(index){
			
			var id = $(this).attr('order_id');
			orderIds[index] = id;
			hasOne = true;
		});
		
		/* Valida que exista un Messenger a quien asignarle las ordenes */
		if( messengerId == '' ){
			rapidFlash(trans('alert.distribution_no_messenger'), 'error', 'no-messenger', 5000);
			return;
		} else {
			removeFlash('no-messenger');
		}
		
		/* Valida que cuando menos exista una orden para asignar */
		if( hasOne == false){
			rapidFlash(trans('alert.distribution_no_orders'), 'error', 'no-order', 5000);
			return;
		} else {
			removeFlash('no-order');
		}
		
		var url = Routing.generate('inodata_flora_distribution_add_orders_to_messenger' );
		$.post(url, { messenger_id:messengerId, order_ids:orderIds }, function(data){
			window.location.reload();
		}, 'json');
		
	}); 
	
	function showEmptyNotification(){
		if($(".item").length==0){
	    	$("#no_orders").css('display', 'table-row'); 
	    }
	}
	
	function hideEmptyNotification(){
		if($("#no_orders").length>0){
	        $("#no_orders").css('display', 'none'); 
	    }
	}
	
	function removeFlash(id)
	{
		$('div#'+id).remove();
	}
	
	function addFlash(msg, type, id)
	{
		var alertClass = 'alert alert-'+type+' '+id;

		if( $('.sonata-bc > div.container-fluid').children(0).attr('class') != alertClass )
		{
			$('.sonata-bc > div.container-fluid')
				.prepend('<div id="'+id+'" class="'+alertClass+'">'+msg+'</div>');
		}
	}
	
	function rapidFlash(msg, type, id, time)
	{
		addFlash(msg, type, id);

		$('div#'+id).fadeOut(time, function(){
			$(this).remove();
		});
	}
	
	/** CREADO EN SEGUNDA VERSION */
	//Parametro '0' inalida messenger, hace que el controller detecte al messenger por default
	var updated = false;
	loadMessengerOrders(0);
	
	/* MODIFICADO PARA LA SEGUNDA VERSIO*/
	$('#inodata_distribution_type_form_id').change(function(){
		var orderId = $(this).val()!=''?id=$(this).val():id=0;
		var messengerId = $('.messenger-tab.st_tab_active').attr('href').replace('#tab-', '');
		
		var data = {messenger_id:messengerId, order_id:orderId}
		
		if( id != 0){
			var url = Routing.generate('inodata_flora_distribution_add_order_to_messenger');
			$.post(url, data, function(response){
				$('.st_view.tab-'+response.id).find('tbody').prepend(response.order);
				$('#num-pendings').html(response.n_in_transit);
				$('#num-delivered').html(response.n_delivered);
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
	
	loadSlidingTabsEfects();
	
	function loadMessengerOrders(id)
	{
		var url = Routing.generate('inodata_flora_distribution_orders_by_messenger', {id:id});
		
		$.get(url, function(data){
			$('.st_view.tab-'+data.id).find('tbody').html(data.orders);
			$('#slidetabs').slidetabs().setContentHeight();
			
			$('#num-delivered').html(data.n_delivered);
			$('#num-pendings').html(data.n_in_transit);
			
			$('.num-boxes').html(data.boxes);
			$('.num-lamps').html(data.lamps);
		}, 'json');
	}
	
	$('.order-action').live('click', function(){
		var orderId = $(this).attr('orderid');
		
		if($(this).hasClass('deliver')){
			action="delivered";
		}
		if($(this).hasClass('intransit')){
			action="intransit";
		}
		if($(this).hasClass('cancel')){
			action="open";
		}
		if($(this).hasClass('deliver-all')){
			action="deliver-all";
		}
		
		var url = Routing.generate('inodata_flora_distribution_order_action');
		var data = {orderId:orderId, action:action};
		
		$.post(url, data, function(response){
			loadMessengerOrders(0);
			if(response.success == 'open'){
				updateOrdersOptions(response.orderOptions);
			}
		}, 'json');
	});
	
	function loadSlidingTabsEfects(){
		$("#slidetabs").slidetabs({ 
			responsive:true, 
			touchSupport:true, 
			autoHeight:true, 
			autoHeightSpeed:300, 
			contentEasing:"easeInOutQuart",
			onTabClick: function(){
				var id = $(this).attr('href').replace('#tab-', '');
				
				$('.st_view.tab-'+id).find('.st_view_inner').prepend($('.inner-filters').detach());
				loadMessengerOrders(id);
			}
		});
	}
	
	//Edit in place employee information
	$(".st_tabs_ul li").each(function(){
		$(this).children('a').append($(this).children('div').clone().removeClass('editable-form'));
	});
	
	var url = Routing.generate('inodata_flora_distribution_messenger_edit_in_place');
	$('.editable-form .edit-employee').editable(url, {
		width:'100px', height:'20px',
		indicator : 'Guardando...',
		callback: function(value, settings){
			var column = $(this).attr('column');
			var el= $('.st_tabs_ul a > div .'+column).text(value);
		}
	});
	
	//more/less objects
	$('.boxes a').click(function(){
		changeObjects('boxes', $(this).text());
	});
	$('.lamps a').click(function(){
		changeObjects('lamps', $(this).text());
	});
	
	function changeObjects(object, action){
		var data = {object:object, action:action};
		var url = Routing.generate('inodata_flora_distribution_objects_edit');
		
		$.post(url, data, function(response){
			$('.num-'+response.object).html(response.value);
		}, 'json');
	}
});
