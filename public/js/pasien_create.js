$("#tanggal_lahir").datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	calendarWeeks: true,
	autoclose: true,
	format: 'dd-mm-yyyy'
}).on('changeDate', function (ev) {
	$.get(base + '/pasiens/cek/tanggal_lahir/sama',
		{ tanggal_lahir_cek : $(this).val() },
		function (data, textStatus, jqXHR) {
			var temp = '';
			for (var i = 0, len = data.length; i < len; i++) {
				temp += '<tr>';
				temp += '<td class="nama">';
				temp += data[i].nama;
				temp += '</td>';
				temp += '<td class="alamat">';
				temp += data[i].alamat;
				temp += '</td>';
				temp += '<td class="no_telp">';
				temp += data[i].no_telp;
				temp += '</td>';
				temp += '</tr>';
			}
			if ( data.length > 0 ) {
				$('#row_ajax_container').fadeIn('slow');
				$('#ajax_container').html(temp);
			}
		}
	);
});
	
