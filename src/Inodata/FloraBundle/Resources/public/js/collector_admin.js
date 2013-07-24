$('document').ready(function(){
	$('.inodata_id_list').select2({allowClear:true});
	
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
			
			//TODO: Cargar resultados para el cobrador
		}, 'json');
	}
	
	//Edit in place employee information
	$(".st_tabs_ul li").each(function(){
		$(this).children('a').append($(this).children('div').clone().removeClass('editable-form'));
	});
	
	var url = "";//Routing.generate('inodata_flora_collection_collector_edit_in_place');
	$('.editable-form .edit-employee').editable(url, {
		width:'100px', height:'20px',
		indicator : 'Guardando...',
		callback: function(value, settings){
			var column = $(this).attr('column');
			$('.st_tabs_ul a > div .'+column).text(value);
		}
	});
	
	$('#inodata_collection_type_form_id').change(function(){
		var orderId = $(this).val()!=''?id=$(this).val():id=0;
		var collectorId = $('.collector-tab.st_tab_active').attr('href').replace('#tab-', '');
		
		var data = {collector_id:collectorId, order_id:orderId}
		
		if( id != 0){
			var url = Routing.generate('inodata_flora_collection_add_order_to_collector');
			$.post(url, data, function(response){
				$('.st_view.tab-'+response.id).find('tbody').prepend(response.order);
				
				//TODO: Cargar informacion de las ganancias del cobrador
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
});