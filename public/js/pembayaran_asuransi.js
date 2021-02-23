	cariPembayaranAsuransi();
	var timeout;
	var length = $("#table_pembayaran_asuransi").find('thead').find('th').length;
	function clearAndSearch(key = 0){
		$("#pembayaran_asuransi_container").html("<tr><td colspan='" +length + "' class='text-center'><img class='loader' src='" + base + "/img/loader.gif'></td></tr>");
		window.clearTimeout(timeout);
		timeout = window.setTimeout(function(){
			if($('#paging').data("twbs-pagination")){
				$('#paging').twbsPagination('destroy');
			}
			cariPembayaranAsuransi(key);
		},600);
	}
    $(function () {
		var session_print = $('#session_print').val();
          if( $('#print').length > 0 ){
            window.open(base + "/pdfs/pembayaran_asuransi/" + session_print, '_blank');
          }
    });

  function dummySubmit(){
    if (validatePass()) {
      $('#submit').click();
    }
  }
  function asuransiChange(control){
	  var asuransi_id = $(control).val();
	  
	  var param = { 
	  	'asuransi_id' : asuransi_id
	  };
	  $.post(base + '/pendapatans/pembayaran/asuransis/riwayatHutang', param, function(data) {
		  data = JSON.parse(data);
		  var temp = '<table class="table table-hover table-condensed table-bordered DTs">';
		  temp += '<table class="table table-hover table-condensed table-bordered"><thead> <tr> <th>Bulan</th> <th>Hutang</th> <th>Sudah Dibayar</th> </tr> </thead>';
		  temp += '<tbody>';
			for (var i = 0; i < data.length; i++) {
				if( i < 8 ){
					temp += '<tr>';
					temp += '<td class="uangNew">' + data[i].bulan + '-' + data[i].tahun + '</td>';
					temp += '<td class="text-right">' + data[i].hutang + '</td>';
					temp += '<td class="text-right">' + data[i].sudah_dibayar + '</td>';
					temp += '</tr>';
				}
			};
		  temp += '</tbody> </table>';
		  $('#riwayatHutang').html(temp);
		  $('#riwayat_hutang_asuransi').dataTable();
		  $('#namaAsuransi').html(
			  '<a href="' +base + '/asuransis/' + data[0].asuransi_id + '/hutang/pembayaran">Riwayat Hutang'+ data[0].nama_asuransi +'</a>'
		  );
	  });
  }
	function cariPembayaranAsuransi(key = 0){
		var pages;
		var id                 = $('#table_pembayaran_asuransi').find('.id').val();
		var created_at         = $('#table_pembayaran_asuransi').find('.created_at').val();
		var nama_asuransi      = $('#table_pembayaran_asuransi').find('.nama_asuransi').val();
		var periode            = $('#table_pembayaran_asuransi').find('.periode').val();
		var pembayaran         = $('#table_pembayaran_asuransi').find('.pembayaran').val();
		var tanggal_pembayaran = $('#table_pembayaran_asuransi').find('.tanggal_pembayaran').val();
		var tujuan_kas         = $('#table_pembayaran_asuransi').find('.tujuan_kas').val();

		$.get(base + '/pendapatans/pembayaran_asuransi/cari_pembayaran',
			{ 
				id:                 id,
				created_at:         created_at,
				nama_asuransi:      nama_asuransi,
				periode:            periode,
				pembayaran:         pembayaran,
				displayed_rows:     $('#displayed_rows').val(),
				tanggal_pembayaran: tanggal_pembayaran,
				tujuan_kas:         tujuan_kas,
				key:         key
			},
			function (data, textStatus, jqXHR) {
				var temp = '';
				if( data.data.length > 0 ){
					for (var i = 0; i < data.data.length; i++) {
						temp += '<tr>'
						temp += '<td class="pembayaran_asuransi_id">'
						temp += data.data[i].id
						temp += '</td>'
						temp += '<td>'
						temp += data.data[i].created_at
						temp += '</td>'
						temp += '<td class="nama_asuransi">'
						temp += data.data[i].nama_asuransi
						temp += '</td>'
						temp += '<td>'
						temp += data.data[i].periode
						temp += '</td>'
						temp += '<td class="text-right">'
						temp += uang(data.data[i].pembayaran)
						temp += '</td>'
						temp += '<td class="tanggal_pembayaran">'
						temp += data.data[i].tanggal_pembayaran
						temp += '</td>'
						temp += '<td>'
						temp += data.data[i].tujuan_kas
						temp += '</td>'
						temp += '<td>'
						temp += '<button type="button" class="btn btn-danger btn-sm" onclick="deletePembayaranAsuransi(this);return false;" >Hapus</button>';
						temp += '</td>'
						temp += '</tr>'
					}
				} else {
						temp += '<tr>'
						temp += '<td class="text-center" colspan=' + length+ '>'
						temp += 'Tidak ada data untuk ditampilkan'
						temp += '</td>'
						temp += '</tr>'
				}
				$('#pembayaran_asuransi_container').html(temp);
				$('#rows').html(data.rows);
				pages = data.pages;
				$('#paging').twbsPagination({
					startPage: parseInt(key) +1,
					totalPages: pages,
					visiblePages: 7,
					onPageClick: function (event, page) {
						cariPembayaranAsuransi(parseInt( page ) -1);
					}
				});
			}
		);
	}
	function deletePembayaranAsuransi(control){
		var pembayaran_asuransi_id = $(control).closest('tr').find('.pembayaran_asuransi_id').html();
		var nama_asuransi          = $(control).closest('tr').find('.nama_asuransi').html();
		var tanggal_pembayaran     = $(control).closest('tr').find('.tanggal_pembayaran').html();
		console.log(pembayaran_asuransi_id);
		console.log(nama_asuransi);
		console.log(tanggal_pembayaran);
		if ( confirm( 'Anda yakin mau menghapus pembayaran ' + pembayaran_asuransi_id + ' untuk asuransi ' + nama_asuransi+ ' yang dibayarkan pada tanggal ' + tanggal_pembayaran ) ) {
			$.post( base + '/pendapatans/pembayaran/asuransi/delete',
				{ 'pembayaran_asuransi_id': pembayaran_asuransi_id },
				function (data, textStatus, jqXHR) {
					data = parseInt($.trim(data));
					if ( data == 0 ) {
						swal('Oops','Ada kesalahan, Tidak Bisa Dihapus', 'error' );
					} else {
						Swal.fire(
						  'Good job!',
						  'Pembayaran Asuransi Berhasil Dihapus',
						  'success'
						);
						// swal('Berhasil','Pembayaran asuransi ' + pembayaran_asuransi_id + ' berhasil direset', 'success' );
						// $(control).closest('tr').css('background', 'red');
						$(control)
						.closest('tr')
						.children('td')
						.animate({ padding: 0 }) 
						.wrapInner('<div />') 
						.children() 
						.fadeOut(function() { 
							$(this).closest('tr').remove();
						});					}
				}
			);
		}
	}
