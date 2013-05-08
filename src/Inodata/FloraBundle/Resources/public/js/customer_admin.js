$('document').ready(function(){
	$('.use-fiscal-address, .use-payment-address').live('click', function(){
		var fiscalForm = $('.use-fiscal-address').closest('.tab-pane').prev().find('.controls');
		var paymentForm = $('.use-fiscal-address').closest('.tab-pane').find('.controls');
		
		if($(this).hasClass('use-fiscal-address')){
			loadAddress(fiscalForm, paymentForm);
		}else{
			loadAddress(paymentForm, fiscalForm);
		}
	});
	
	function loadAddress(sourceForm, targetForm)
	{
		$(targetForm).eq(1).children().val($(sourceForm).eq(1).children().val());
		$(targetForm).eq(2).children().val($(sourceForm).eq(2).children().val());
		$(targetForm).eq(3).children().val($(sourceForm).eq(3).children().val());
		$(targetForm).eq(4).children().val($(sourceForm).eq(4).children().val());
		$(targetForm).eq(5).children().val($(sourceForm).eq(5).children().val());
		$(targetForm).eq(6).children().val($(sourceForm).eq(6).children().val());
		$(targetForm).eq(7).children().val($(sourceForm).eq(7).children().val());
		$(targetForm).eq(8).children().val($(sourceForm).eq(8).children().val());
		$(targetForm).eq(9).children().val($(sourceForm).eq(9).children().val());
	}
});