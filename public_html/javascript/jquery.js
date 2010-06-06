$(document).ready(function() {
$("#date").datepicker();
$("#start_date").datepicker();
$("#end_date").datepicker();
$("#recurring_container").hide();
$("#form_select").change(function() {
	if ($("#form_select").val() == 1) 
		$("#recurring_container").hide();
	if ($("#form_select").val() == 2) {
		$("#recurring_container").show();
		$("#week_day").hide();
	}
 	if ($("#form_select").val() == 3) {
		$("#day_recurring").hide();
		$("#week_day").show();
	} 
	});
});
