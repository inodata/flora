$('document').ready(function(){	

	$('.inodata_delivery_date').datepicker({ dateFormat: "yy-mm-dd" });
	$('.inodata_messenger').select2();
	$('.print_link').live('click', function(){
		window.print();
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
		$(this).parent().parent().remove();
		var url = Routing.generate('inodata_flora_distribution_verify_order_status', {orderId:id});
		$.get(url, function(data){
			if( data.isValidToAdd == 'true'){
				$('#inodata_distribution_type_form_id').append(data.option);
			}
		}, 'json' );		
		
		
	});
	
	$('.add_link').live('click', function(){
		
		var messengerId = $('#inodata_distribution_type_form_messenger').val();
		var orderIds = "";
		
		$('#messenger_orders tr').each(function(){
			if( $(this).attr('id') == 'no_orders' ){
				alert('Seleccione al menos una orden');
				return;
			}
			orderIds = orderIds + $(this).attr('order_id');
			orderIds = orderIds+"+";

			
		});

		if(orderIds == ''){
			alert('Seleccione al menos una orden');
			return;
		}

		if( messengerId == '' ){
			alert('Seleccione un Repartidor');
			return;
		}

		var url = Routing.generate('inodata_flora_distribution_add_orders_to_messenger', { messengerId:messengerId, orderIds:orderIds } );
	
		$.get(url, function(data){
			alert('Se asignaron las ordenes al Repartidor');
			
			$('#messenger_orders > tr').each(function(){
				$(this).remove();
			});
			$('#messenger_orders').html( data.empty_list);
			
		}, 'json');
	}); 
	

	
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