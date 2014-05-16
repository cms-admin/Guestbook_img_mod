$(window).load(function(){
	$(':input','#guestbook')
		.not(':button, :submit, :reset, :hidden')
		.val('')
		.removeAttr('checked')
		.removeAttr('selected');
});
$(function () {
	var qMax = $('span#txt_max').text();
	$('#message').keyup(function(){
		var q = $(this).val();
		qSum = q.length;
		$('#txt_now').text(qSum);
		if (qSum > qMax) {
			$('#txt_now').attr("style","color:red")
		} else {
			$('#txt_now').removeAttr("style");
		}
	});
});