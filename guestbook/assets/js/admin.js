$('.to_hide').unbind('click').on('click', function() {
	var arr = new Array();
	$('input[name=ids]:checked').each(function() {
		arr.push(parseInt($(this).val()));
	});
	$.post('/admin/components/cp/guestbook/update_status',
		{
			id: arr,
			status: 0
		},
		function(data) {
			$('.notifications').append(data);
		}
	);
});

$('.to_show').unbind('click').on('click', function() {
	var arr = new Array();
	$('input[name=ids]:checked').each(function() {
		arr.push(parseInt($(this).val()));
	});
	$.post('/admin/components/cp/guestbook/update_status',
		{
			id: arr,
			status: 1
		},
		function(data) {
			$('.notifications').append(data);
		}
	);
});

$('#to_del').unbind('click').on('click', function() {
	var arr = new Array();
	$('input[name=ids]:checked').each(function() {
		arr.push(parseInt($(this).val()));
	});
	$.post('/admin/components/cp/guestbook/delete',
		{
			id: arr
		},
		function(data) {
			$('.notifications').append(data);
		}
	);
});