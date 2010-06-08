$(document).ready(function() {
$("#date").datepicker();
$("#start_date").datepicker();
$("#end_date").datepicker();
$("#every_month").datepicker();
$("#every_year").datepicker();
$("#recurring_ends").datepicker();
$("#recurring_container").hide();
$("#form_select").change(function() {
	if ($("#form_select").val() != 1) {
		$("#recurring_container").show();
	}
	if ($("#form_select").val() == 1) 
		$("#recurring_container").hide();
	if ($("#form_select").val() == 2) {
		$("#week_day").hide();
		$("#monthly_recurring").hide();
		$("#yearly_recurring").hide(); 
		$("#day_recurring").show();
	}
 	if ($("#form_select").val() == 3) {
		$("#day_recurring").hide();
		$("#monthly_recurring").hide();
		$("#yearly_recurring").hide(); 
		$("#week_day").show();
	}
	if ($("#form_select").val() == 4) {
		$("#day_recurring").hide(); 
		$("#yearly_recurring").hide(); 
		$("#week_day").hide();
		$("#monthly_recurring").show();
	}
	if ($("#form_select").val() == 5) {
		$("#day_recurring").hide(); 
		$("#week_day").hide();
		$("#monthly_recurring").hide();
		$("#yearly_recurring").show();
	} 
		
	});
});
