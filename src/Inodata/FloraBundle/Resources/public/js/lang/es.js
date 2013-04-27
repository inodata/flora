//-------------------------- Translate messages ---------------------//
function trans(message_label){
	return lang[message_label];
}
//-------------------------------------------------------------------//

var lang = {
	"alert.card_missing_fields": "Faltan datos para imprimir la tarjeta, completar 'De', 'Para' o el 'Mensaje'",
	"alert.distribution_no_orders": "Seleccione al menos un pedido"	,
	"alert.distribution_no_messenger": "Seleccione un repartidor"
};