	temp = parseTemp();
	render(temp);
	function dummySubmit(control){
		var htg = hitung();
		var kredit            = htg.kredit;
		var debit             = htg.debit;
		var biaya             = htg.biaya;
		var total_periksa     = htg.total_periksa;
		var total_harta_masuk = htg.total_harta_masuk;

		callValue(htg);

		if( 
			kredit == debit &&
			biaya == total_periksa &&
			biaya == total_harta_masuk
		){
			if(validatePass2(control)){
				$('#submit').click();
			}
		} else {
			if( kredit != debit ){
				alert('Jumlah Debit = ' + uang( debit ) + ', kredit = ' + uang( kredit ) + ' , HARUS SAMA!');
			}
			if( biaya != total_periksa  || biaya != total_harta_masuk){
				alert('Jumlah Biaya = ' + uang( biaya ) + ', Total Periksa = ' + uang( total_periksa ) + ' Total Harta Yang Masuk = ' + uang( total_harta_masuk )+ ', HARUS SAMA!');
			}
		}
	}
	function nilaiTransaksi(control){
		var nilai             = cleanUang( $(control).val() );
		var key               = $(control).attr('title');
		var jurnals           = $('#jurnals').val();
		jurnals               = JSON.parse(jurnals);
		jurnals[key]['biaya'] = nilai;
		jurnals               = JSON.stringify(jurnals);
		$('#jurnlas').val(jurnals);
		$('#debit_total').html(hitung().debit);
		$('#kredit_total').html(hitung().kredit);
	}
	
	function periksaKeyUp(control, tipe){
		var nilai = cleanUang( $(control).val() );
		var periksa = $('#periksa').val();
		periksa = JSON.parse(periksa);
		periksa[tipe] = nilai;
		periksa = JSON.stringify(periksa);
		$('#periksa').val(periksa);
		$('#periksa_total').html(hitung().total_periksa);
	}
	function coaChange(control){
		 var key   = parseInt( $(control).closest('tr').find('.key').html() );
		var coa_id = $(control).val();
		var data = $('#jurnals').val();
		data = JSON.parse(data);
		 data[key]['coa_id'] = coa_id;
		 data = JSON.stringify(data);
		 $('#jurnals').val(data);
	}

	function nilaiKeyUp(control){
		 var key   = parseInt( $(control).closest('tr').find('.key').html() );
		var nilai = cleanUang( $(control).val() );
		var data = $('#jurnals').val();
		data = JSON.parse(data);
		 data[key]['nilai'] = parseInt( nilai );
		 data = JSON.stringify(data);
		 $('#jurnals').val(data);
		var htg = hitung();
		callValue(htg);

	}
	function transaksiPeriksa(control){

		var nilai = cleanUang( $(control).val() );
		var key = $(control).attr('title');

		var transaksis = $('#transaksis').val();
		transaksis = JSON.parse(transaksis);
		transaksis[key].biaya = nilai;
		transaksis = JSON.stringify( transaksis );
		$('#transaksis').val(transaksis);
		$('#biaya_total').html(hitung().biaya);
		var htg = hitung();
		callValue(htg);
	}
	function hitung(){
			var jurnals = $('#jurnals').val();
			jurnals = JSON.parse(jurnals);
			var temp = $('#temp').val();
			temp = JSON.parse(temp);
			var transaksis = $('#transaksis').val();
			transaksis = JSON.parse(transaksis);
			var periksa = $('#periksa').val();
			periksa = JSON.parse(periksa);

			var debit = 0;
			var kredit = 0;
			var total_harta_masuk = 0;
			 for (var i = 0; i < jurnals.length; i++) {
				 if( jurnals[i].debit == '1' ){
					debit += parseInt( jurnals[i].nilai );
				 } else {
					kredit += parseInt( jurnals[i].nilai );
				 }
				 if( jurnals[i].coa_id.substring(0,2) == '11' && jurnals[i].debit == '1' ){
					 total_harta_masuk += parseInt( jurnals[i].nilai );
				 }
			 }
			 for (var i = 0; i < temp.length; i++) {
				 if( temp[i].debit == '1' ){
					debit += parseInt( temp[i].nilai );
				 } else {
					kredit += parseInt( temp[i].nilai );
				 }
				 if( temp[i].coa_id.substring(0,2) == '11' && temp[i].debit == '1' ){
					 total_harta_masuk += parseInt( temp[i].nilai );
				 }
			 }
			var biaya = 0;
			 for (var i = 0; i < transaksis.length; i++) {
				biaya += parseInt( transaksis[i].biaya );
			 }
			var total_periksa = parseInt( periksa.tunai ) + parseInt( periksa.piutang );

		return {
			'kredit' : kredit,
			'debit' : debit,
			'biaya' : biaya,
			'total_periksa' : total_periksa,
			'total_harta_masuk' : total_harta_masuk
		};
	}
	function stringifyJurnal(data){
		 data = JSON.stringify(data);
		 $('#jurnals').val(data);
	}
	function delJurnal(control){
		var jurnals = $('#jurnals').val();
		jurnals = JSON.parse(jurnals);
		var i = $(control).closest('tr').find('.key').html();
		i = $.trim(i);
		jurnals.splice(i, 1);
		console.log(jurnals);
		jurnals = JSON.stringify(jurnals);
		$('#jurnals').val(jurnals);
		$(control).closest('tr').remove();
		var htg = hitung();
		callValue(htg);
		var rows = $('#table_template_jurnal tbody tr').length;
		console.log(rows);
		for (var i = 0, len = rows; i < len; i++) {
			$('#table_template_jurnal tbody tr:nth-child(' + parseInt(i + 1) + ')').find('.key').html(i);
		}
	}
	function callValue(htg) {
		console.log('===============================================================');
		console.log('kredit');
		console.log(htg.kredit);
		console.log('debit');
		console.log(htg.debit);
		console.log('biaya');
		console.log(htg.biaya);
		console.log('total_periksa');
		console.log(htg.total_periksa);
		console.log('total_harta_masuk');
		console.log(htg.total_harta_masuk);
		console.log('===============================================================');
	}

