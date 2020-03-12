function cek(control){
	var html_barcode = $('.barcode').html();
	var x = $('.barcode').find('.i').html();
    var sudah_dibayar       = $(control).closest('tr').find('td:nth-child(4)').html();
    var piutang             = $(control).closest('tr').find('td:nth-child(3)').html();
    sudah_dibayar           = cleanUang(sudah_dibayar.trim());
    piutang                 = cleanUang(piutang.trim());
    var akan_dibayar        = parseInt(piutang) - parseInt(sudah_dibayar);
    var key                 = $(control).val();
    var Array               = $('#pembayarans').val();
    Array                   = JSON.parse(Array);
    Array[key].akan_dibayar = akan_dibayar;
    $('#pembayarans').val(JSON.stringify(Array));
    view();
	if( html_barcode != '' ){
		deleteCekPembayaran(x);
	}
}
function reset(control){
    var key = $(control).val();
    var Array = $('#pembayarans').val();
    Array = $.parseJSON(Array);
    Array[key].akan_dibayar = 0;
    $('#pembayarans').val(JSON.stringify(Array));
    view();
}
function cekAll(){
    var Array = $('#pembayarans').val();
    Array = $.parseJSON(Array);
    for (var i                = 0; i < Array.length; i++) {
        var piutang           = Array[i].piutang;
        var sudah_dibayar     = Array[i].pembayaran;
        var akan_dibayar      = parseInt(piutang) - parseInt(sudah_dibayar);
        Array[i].akan_dibayar = akan_dibayar;
    };
	
    $('#pembayarans').val(JSON.stringify(Array));
    view();
}
function view(pertama_kali = false){
    let MyArray             = $('#pembayarans').val();
    MyArray                 = $.parseJSON(MyArray);
    var temp                = '';
    var temp2               = '';
    var temp_excel_pembayaran               = '';
    var akan_dibayar        = 0;
    var piutang_total       = 0;
    var sudah_dibayar_total = 0;
    var belum_dibayar_total = 0;
    var excel_pembayaran = $.parseJSON($('#excel_pembayaran').val());

	console.log('excel_pembayaran');
	console.log(excel_pembayaran);

	var cocok = 0;
	var total  = excel_pembayaran.length;
	console.log('excel_pembayaran');
	console.log(excel_pembayaran);
    for (var i = 0; i < MyArray.length; i++) {
		// {{-- if( pertama_kali ){ --}}
		// {{-- 	for (var r = 0; r < excel_pembayaran.length; r++) { --}}
		// {{-- 		var excel_tagihan = excel_pembayaran[r].tagihan; --}}
		// {{-- 		if ( --}}
		// {{-- 			excel_pembayaran[r].peserta == null && --}}
		// {{-- 			MyArray[i].piutang == excel_tagihan --}}
		// {{-- 		) { --}}
		// {{-- 			cocok = cocok + 1; --}}
		// {{-- 			excel_pembayaran.splice(r, 1); --}}
		// {{-- 			if(MyArray[i].piutang - MyArray[i].pembayaran > 0){ --}}
		// {{-- 				MyArray[i].akan_dibayar = excel_tagihan; --}}
		// {{-- 			} --}}
		// {{-- 			break; --}}
		// {{-- 		} --}}
		// {{-- 	}; --}}
		// {{-- } --}}

        if(MyArray[i].piutang - MyArray[i].pembayaran > 0){
            piutang_total += MyArray[i].piutang;
            sudah_dibayar_total += MyArray[i].pembayaran;
            belum_dibayar_total += MyArray[i].piutang - MyArray[i].pembayaran;
            temp2 += '<tr>';
            temp2 += '<td>' + MyArray[i].periksa_id + '</td>';
			temp2 += '<td>' + MyArray[i].nama_pasien + '</td>';
            temp2 += '<td class="uang">' + MyArray[i].piutang + '</td>';
            temp2 += '<td class="uang">' + MyArray[i].pembayaran + '</td>';
            temp2 += '<td><input class="form-control angka2 akan_dibayar" value="' + MyArray[i].akan_dibayar + '" onkeyup="akanDibayarKeyup(this);return false;" /></td>';
            if(MyArray[i].piutang - MyArray[i].pembayaran < 1){
            var status = '<div class="alert-success">';
            status += 'Sudah Lunas';
            status += '</div>';
            } else {
            var status = '<div class="alert-danger">';
            status += 'Belum Lunas';
            status += '</div>';
            }
            temp2 += '<td>' + status + '</td>';
            temp2 += '<td><button class="btn btn-sm btn-primary" onclick="cek(this);return false;" type="button" value="' + i + '">Cek</button> ';
            temp2 += '<button class="btn btn-sm btn-warning" onclick="reset(this);return false;" type="button" value="' + i + '">Reset</button></td>';
            temp2 += '</tr>';
            akan_dibayar += parseInt( MyArray[i].akan_dibayar );
        }
    };
	if( $.trim( temp2 ) == '' ){
		temp2 = '<tr><td colspan="7" class="text-center">Tidak Ada Piutang Yang Belum Dibayar</td></tr>';
	}

	refreshExcelPembayaran(excel_pembayaran);

	tidak_cocok = total - cocok;


    $('#jumlah_pasien').html(i);
    $('#table_temp').html(temp);
    $('#table_temp2').html(temp2);
	if(!pertama_kali){
		$('#piutang').val(uang2( akan_dibayar ));
	}
    $('#piutang_total').html(piutang_total);
    $('#belum_dibayar_total').html(belum_dibayar_total);
    $('#sudah_dibayar_total').html(sudah_dibayar_total);
    $('#dibayar_sebesar').html(akan_dibayar);
    $('#tidak_cocok').html(tidak_cocok);
    $('#cocok').html(cocok);
    $('#pembayarans').val(JSON.stringify(MyArray));
    formatUang();
}
function resetAll(){
    var Array = $('#pembayarans').val();
    Array = $.parseJSON(Array);
    for (var i = 0; i < Array.length; i++) {
        Array[i].akan_dibayar = 0;
    };
    $('#pembayarans').val(JSON.stringify(Array));
    view();
}

function submitPage(control){
	var val = $('#rekening_id').val();
	$.get(base + '/transaksi/avail',
		{ id: val },
		function (data, textStatus, jqXHR) { 
			console.log('dataaa');
			console.log(data);
			validate(control, $.trim(data));
		});

}
function akanDibayarKeyup(control){

	var before = $(control).val();
	$(control).val(parseInt(before) || '');
	if ( $(control).val() == '' ) {
		$(control).val('0')
	}

	var jumlahAkanDibayar =0;
	$('.akan_dibayar').each(function(){
		 jumlahAkanDibayar += parseInt( $(this).val() );
	});

	jumlahAkanDibayar = uang(jumlahAkanDibayar);


	$('#piutang').val(jumlahAkanDibayar); 

	var tempJson = $('#pembayarans').val();
	var tempArray = JSON.parse(tempJson);

	var i = $(control).closest('tr').find('.btn-primary').val();

	tempArray[i].akan_dibayar = $(control).val();

	$('#pembayarans').val( JSON.stringify(tempArray) );
}
function deleteCekPembayaran(i){
	var excel_pembayaran = $.parseJSON($('#excel_pembayaran').val());

	excel_pembayaran.splice(i, 1);

	refreshExcelPembayaran(excel_pembayaran);

	$('.nav-tabs a[href="#excel_gak_cocok"]').tab('show');

	$("#excel_gak_cocok")[0].scrollIntoView()
}

function cekExcelPembayaran(control){
	var nama_peserta = $(control).closest('tr').find('.nama_peserta').html();
	var tagihan = $(control).closest('tr').find('.tagihan').html();
	var i = $(control).closest('tr').find('.i').html();
	var temp = '<p class="bg-padding bg-success">';
	$('.nav-tabs a[href="#detail_pembayaran"]').tab('show');
	temp += '<span class="nama_peserta">';
	temp += nama_peserta
	temp += '</span> ';
	temp += '<span class="tagihan">';
	temp += tagihan
	temp += '</span> ';
	temp += '<span class="i">';
	temp += i
	temp += '</span> ';
	temp += '<br />';
	temp += '<button class="btn btn-info" type="button" onclick="jadikanCatatan(this); return false;">Jadikan Catatan</button>'
	temp += '<button class="btn btn-danger" type="button" onclick="deleteCek(' + i + ');">Selesai</button>'
	temp += '<button class="btn btn-success" type="button" onclick="bersihkan();">Clear</button>'
	temp += '<br />';
	temp += '</p>';
	$('#panel_perbandingan').html(temp);
}

function deleteCek(i){
	deleteCekPembayaran(i);
}

function jadikanCatatan(control){
	var nama_peserta = $(control).closest('.barcode').find('.nama_peserta').html();
	var tagihan      = $(control).closest('.barcode').find('.tagihan').html();
	var x            = $(control).closest('.barcode').find('.i').html();

	catatan(nama_peserta, tagihan, x);
}

function catatan(nama_peserta, tagihan, x){
	var array = {
		'nama_peserta': nama_peserta,
		'tagihan':      tagihan
	};
	if(confirm('Masukkan ke dalam catatan?')){
		var catatanExisting = parseCatatanExisting();
		catatanExisting.push(array);
		viewCatatanExisting(catatanExisting);
		deleteCekPembayaran(x);
	}
}



function refreshExcelPembayaran(excel_pembayaran){
	var temp_excel_pembayaran = '';
	for (var r = 0; r < excel_pembayaran.length; r++) {
		temp_excel_pembayaran += '<tr>';
		temp_excel_pembayaran += '<td class="i hide">' + r + '</td>';
		temp_excel_pembayaran += '<td class="nama_peserta">' + excel_pembayaran[r].peserta + '</td>';
		temp_excel_pembayaran += '<td class="tagihan">' + excel_pembayaran[r].tagihan + '</td>';
		temp_excel_pembayaran += '<td>';
		temp_excel_pembayaran += ' <button type="button" class="btn btn-warning btn-sm" onclick="cekExcelPembayaran(this); return false;">Bandingkan</button>';
		temp_excel_pembayaran += ' <button type="button" class="btn btn-info btn-sm" onclick="jadikanCatatanDisini(this); return false;">Catatan</button>';
		temp_excel_pembayaran += ' <button type="button" class="btn btn-danger btn-sm" onclick="deleteCekPembayaran(' +r+ '); return false;">Delete</button>';
		temp_excel_pembayaran += ' </td>';
	}
    $('#excel_pembayaran').val(JSON.stringify(excel_pembayaran));
    $('#bandingkan_data').html(temp_excel_pembayaran);
	$('#panel_perbandingan').html('');
}
function arr_diff (a1, a2) {

    var a = [], diff = [];

    for (var i = 0; i < a1.length; i++) {
        a[a1[i]] = true;
    }

    for (var i = 0; i < a2.length; i++) {
        if (a[a2[i]]) {
            delete a[a2[i]];
        } else {
            a[a2[i]] = true;
        }
    }

    for (var k in a) {
        diff.push(k);
    }
    return diff;
}
function delCatatan(control){
	if(confirm("Anda akan menghapus catatan ini")){
		var nama_peserta = $(control).closest('tr').find('.nama_peserta').html();
		var tagihan      = $(control).closest('tr').find('.tagihan').html();
		var i            = $(control).closest('tr').find('.i').html();

		var array = {
			'peserta' : nama_peserta,
			'tagihan' : tagihan
		};

		var excel_pembayaran = $.parseJSON($('#excel_pembayaran').val());

		excel_pembayaran.push(array);

		refreshExcelPembayaran(excel_pembayaran);

		var catatanExisting = parseCatatanExisting();
		catatanExisting.splice( i, 1 );
		viewCatatanExisting(catatanExisting);
	}

}
function parseCatatanExisting(){
	var catatanExisting = $('#catatan_container').html();
	catatanExisting = JSON.parse(catatanExisting);

	return catatanExisting;
}

function viewCatatanExisting(catatanExisting){
	var temp = '';
	for (var i = 0; i < catatanExisting.length; i++) {
		temp += '<tr>';
		temp += '<td class="i hide">';
		temp += i
		temp += '</td>';
		temp += '<td class="nama_peserta">';
		temp += catatanExisting[i].nama_peserta;
		temp += '</td>';
		temp += '<td class="tagihan">';
		temp += catatanExisting[i].tagihan;
		temp += '</td>';
		temp += '<td>';
		temp += '<button type="button" class="btn btn-danger btn-sm" onclick="delCatatan(this);return false;">del</button>';
		temp += '</td>';
		temp += '</tr>';
	}
	catatanExisting = JSON.stringify(catatanExisting);
	$('#catatan_container').html(catatanExisting);
	$('#container_catatan').html(temp);
}

function stripString(str){
	str = $.trim(str);
	str = str.toLowerCase();
	str = str.replace(/[^\w\s]|_/g, "").replace(/\s+/g, " ");
	str = str.replace(/\s/g, '');
	str = str.split(',')[0];
	str = str.replace(/\s/g, '');
	return str;
}
function bersihkan(){
	 $('.barcode').html('');
}
function jadikanCatatanDisini(control){
	var nama_peserta = $(control).closest('tr').find('.nama_peserta').html();
	var tagihan      = $(control).closest('tr').find('.tagihan').html();
	var x            = $(control).closest('tr').find('.i').html();
	catatan(nama_peserta, tagihan, x);
}
function validate(control, dt){
	var data = $('#pembayarans').val();
	data = JSON.parse(data);
	var akanDibayar = 0;

    for (var i = 0; i < data.length; i++) {
        if(data[i].piutang - data[i].pembayaran > 0){
			akanDibayar += parseInt( data[i].akan_dibayar );
			if( data[i].akan_dibayar > ( data[i].piutang - data[i].pembayaran ) ){
				var baris = parseInt(i) + 1;
				alert('Pembayaran ' + data[i].nama_pasien + ', baris ke ' + baris + ' lebih besar dari nilai piutangnya, harap diperbaiki');
				return false;
			}
		}
    };

	var found_tr_id = true;
	if(dt == '0'){
		found_tr_id = false;
	}

	console.log('found_tr_id');
	console.log(found_tr_id);

	console.log('dt == "0"');
	console.log(dt == '0');

	if(
		validatePass2(control) 
		&& cleanUang( $('#piutang').val() ) > 0 
		&& data.length > 0
		&& akanDibayar > 0
		&& found_tr_id
	){
		 $('#submit').click();
	} else if(cleanUang( $('#piutang').val() ) < 1 ){
		alert('Nilai yang dibayarkan harus lebih besar dari 0');
		validasi('#piutang', 'nilai harus lebih dari Rp. 0 ');
	} else if(akanDibayar < 1 ){
		alert('Harus ada pasien yang di ceklist');
	} 
	if(!found_tr_id ){
		validasi1( $('#rekening_id'), 'Transaksi tidak ditemukan')
	}
}

function cekRekening(control){

	var id = $(control).val();

	$.get(base + '/rekenings/cek_id',
		{ id: id },
		function (data, textStatus, jqXHR) {
			$(control).closest('.form-group').find('.alert').remove();
			if( data ){
				$(control).closest('.form-group').append('<div><div class="alert alert-info"><h2>' + uang(data.nilai) + '</h2><h4>'+ moment(data.tanggal, 'YYYY-MM-DD HH:II:SS').format('DD MMM YYYY')+'</h4>' + data.deskripsi + '</div></div>')
			}
		}
	);
}
function tbhInput2(control){
	var tr = $(control).closest('tr');
	var val = tr.find('select').val();
	tambahInput(control);
	tr.next().find('select option[value="' +val+ '"]').remove();
	tr.next().find('select').val('');
	getPiutangAsuransiDetail(val);
}
function getPiutangAsuransiDetail(control){
	var invoice_ids = [];
	$(control).closest('tbody').find('select').each(function(){
		invoice_ids.push( $(this).val() );
	});
	var val = $(control).val();
	$.get(base + '/pendapatans/pembayaran_show/detail/piutang_asuransis',
		{ id: invoice_ids },
		function (data, textStatus, jqXHR) {
			viewInvoice(data);
		}
	);
}
function viewInvoice(data){
	var temp = '';
	for (var i = 0, len = data.length; i < len; i++) {
		temp += '<tr>';
		temp += '<td>';
		temp += data[i].nama_asuransi;
		temp += '</td>';
		temp += '<td>';
		temp += data[i].jumlah_tagihan + ' tagihan';
		temp += '</td>';
		temp += '<td class="text-right" nowrap>';
		temp += uang(data[i].total_tagihan);
		temp += '</td>';
		temp += '</tr>';
	}
	$('#body_invoice').html(temp);
}
