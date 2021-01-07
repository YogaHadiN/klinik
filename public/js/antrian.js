function submitAntrian(control){
	var ajax_url = base + '/fasilitas/antrian_pasien/ajax/' + control;
	$(':button').prop('disabled', true);
	$.get( ajax_url,
		{  },
		function (data, textStatus, jqXHR) {

			var nomor_antrian = data['nomor_antrian'];
			var jenis_antrian = data['jenis_antrian'];
			var timestamp     = data['timestamp'];

			$('#nomor_antrian').html(nomor_antrian);
			$('#jenis_antrian').html(jenis_antrian);
			$('#timestamp').html(timestamp);

			$(':button').prop('disabled', false);
			window.print();
			Swal.fire({
			  icon: 'success',
			  title:nomor_antrian,
			  text:jenis_antrian,
			  showConfirmButton: false,
			  timer: 1500
			})
		}
	);
}
