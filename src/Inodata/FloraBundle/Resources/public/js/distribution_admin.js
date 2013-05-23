$('document').ready(function(){	

	$('#filter_deliveryDate_value').datepicker({ dateFormat: "yy-mm-dd" });
	$('.inodata_messenger_list').select2({allowClear:true});
	$('.inodata_id_list').select2({allowClear:true, closeOnSelect:false});
	
	/* Refactorizar esta funcion */
	$('div.alert-success').fadeOut(5000, function(){
		$(this).remove();
	});

	$('#inodata_distribution_type_form_id').change(function(){
		var id = $(this).val()!=''?id=$(this).val():id=0;
		
		if( id != 0){
			var url = Routing.generate('inodata_flora_distribution_add_preview_order_to_messenger', {orderId:id});
				
			$.get(url, function(data){
				$("tbody#messenger_orders").append(data.row);
				$('.inodata_id_list').select2('val', '');
				hideEmptyNotification();
				updateOrderSelectOptions();
			}, 'json');
		}
	});
	
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
	//loadMessengerOrders(0);
	
	$('.messenger-tab').click(function(){
		var id = $(this).attr('href').replace('#tab-', '');
		loadMessengerOrders(id);
	});
	
	function loadMessengerOrders(id){
		var url = Routing.generate('inodata_flora_distribution_orders_by_messenger', {id:id});
		
		$.get(url, function(data){
			$('.st_view.tab-'+data.id).find('tbody').html(data.orders);
		}, 'json');
	}
});
