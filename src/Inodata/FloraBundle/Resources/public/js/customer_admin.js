$('document').ready(function(){
	$('.use-fiscal-address').live('click', function(){
		var fiscalForm = $('.use-fiscal-address').closest('.tab-pane').prev().find('.controls');
		var paymentForm = $('.use-fiscal-address').closest('.tab-pane').find('.controls');

		$(paymentForm).eq(1).children().val($(fiscalForm).eq(0).children().val());
		$(paymentForm).eq(2).children().val($(fiscalForm).eq(1).children().val());
		$(paymentForm).eq(3).children().val($(fiscalForm).eq(2).children().val());
		$(paymentForm).eq(4).children().val($(fiscalForm).eq(3).children().val());
		$(paymentForm).eq(5).children().val($(fiscalForm).eq(4).children().val());
		$(paymentForm).eq(6).children().val($(fiscalForm).eq(5).children().val());
		$(paymentForm).eq(7).children().val($(fiscalForm).eq(6).children().val());
		$(paymentForm).eq(8).children().val($(fiscalForm).eq(7).children().val());
		$(paymentForm).eq(9).children().val($(fiscalForm).eq(8).children().val());
	});
});