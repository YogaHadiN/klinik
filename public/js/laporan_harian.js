function printStruk(control){
	alert( $(control).closest('tr').find('.periksa_id').html() );
}
function updateAsuransiPeriksa(control){

	var asuransi_id     = $(control).val();
	var periksa_id      = $(control).closest('tr').find('.periksa_id').html();
	var nama_pasien     = $(control).closest('tr').find('.nama_pasien').html();
	var tanggal         = $(control).closest('tr').find('.tanggal').html();
	var old_asuransi_id = $(control).closest('tr').find('.old_asuransi_id').html();

	console.log('base');
	console.log( base );
	console.log('asuransi_id');
	console.log( asuransi_id );
	
	$.post(base + '/laporans/harian/update_asuransi',
		{ 
			'asuransi_id': asuransi_id ,
			'periksa_id': periksa_id
		},
		function (data, textStatus, jqXHR) {
			data = $.trim(data)

			if ( data == 0 ) {
				
				swal('Oops','Ada kesalahan, asuransi tidak bisa diubah', 'error' );
				$(control).val( old_asuransi_id );
				$(control).selectpicker('refresh');

			} else {
				swal('Berhasil','Asuransi pemeriksaan ' + nama_pasien + ' berhasil di update ', 'success' );
			}

		}
	);
}

