$(function () {
	$('#dummySubmitSupplier').click(function(){
		if( $('#nama_supplier').val() == '' ){
			swal("Peringatan",'nama supplier harus diisi', 'error');
			validasi('#nama_supplier', 'Harus Disi');
			$('#nama_supplier').focus();
		} else {
			$('#supplier_submit input[type="submit"]').click();
		}
	});
});
	 
