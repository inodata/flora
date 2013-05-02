$('document').ready(function(){	

	$('#filter_deliveryDate_value').datepicker({ dateFormat: "yy-mm-dd" });
	$('.inodata_messenger_list').select2();
	$('.inodata_id_list').select2();
	
	/* Refactorizar esta funcion */
	$('div.alert-success').fadeOut(5000, function(){
		$(this).remove();
	});

	$('#inodata_distribution_type_form_id').change(function(){
		var id = $(this).val()!=''?id=$(this).val():id=0;
		
		if( id != 0)
		{
			var url = Routing.generate('inodata_flora_distribution_add_preview_order_to_messenger', {orderId:id});
				
			$.get(url, function(data){
				
				$("tbody#messenger_orders").append(data.row);

				if( $('tbody#messenger_orders').find('tr').length > 1)
				{
					$("tbody#messenger_orders tr#no_orders").remove();
				}
				
				refreshOrders();
			}, 'json');
		}
	});

	$('.delete_link').live('click', function(){
		
		var id = $(this).parent().parent().attr('order_id');
		var row = $(this).parent().parent();
		var url = Routing.generate('inodata_flora_distribution_verify_order_status', {orderId:id});
		
		$.get(url, function(data){
			row.remove();
			if( data.isValidToAdd == 'true'){
				$('#inodata_distribution_type_form_id').append(data.option);
			}
			
			if( $('tbody#messenger_orders').find('tr').length < 1)
			{
				$("tbody#messenger_orders").html(data.empty_list);
			}
			
		}, 'json' );		
		
		
	});
	
	$('.add_link').live('click', function(){
		
		var messengerId = $('#inodata_distribution_type_form_messenger').val();
		var orderIds = "";
		
		$('#messenger_orders tr').each(function(){
			if( $(this).attr('id') == 'no_orders' ){
				rapidFlash(trans('alert.distribution_no_orders'), 'error', 'no-order', 5000);
				return;
			} else {
				removeFlash();
				orderIds = orderIds + $(this).attr('order_id');
				orderIds = orderIds+"+";
			}
			
		});

		if(orderIds == ''){
			rapidFlash(trans('alert.distribution_no_orders'), 'error', 'no-order', 5000);
			return;
		} else{
			removeFlash('no-order');
		}

		if( messengerId == '' ){
			rapidFlash(trans('alert.distribution_no_messenger'), 'error', 'no-messenger', 5000);
			return;
		} else {
			removeFlash('no-messenger');
		}

		var url = Routing.generate('inodata_flora_distribution_add_orders_to_messenger', { messengerId:messengerId, orderIds:orderIds } );
	
		$.get(url, function(data){
			
			$('#messenger_orders > tr').each(function(){
				$(this).remove();
			});
			$('#messenger_orders').html( data.empty_list);
				window.location.reload();
		}, 'json');
		
		//refreshOrderList();
		
		
	}); 
	
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
	
	function refreshOrderList()
	{
		var url = Routing.generate('distribution_list');
		
		$.get(url, function(data){
			$('#messenger_orders').html(data.list_orders);
		});
	}
	
	function refreshOrders()
	{
		$('#messenger_orders > tr').each(function(){
			var orderPreview = $(this).attr('order_id');
			$('#inodata_distribution_type_form_id > option').each(function(){
				if( orderPreview == $(this).val() )
				{
					$(this).remove();
				}
			});
		});
	}
});