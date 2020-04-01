<div class="row hide">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		{!! Form::textarea('tipe_tindakans', $tipe_tindakans, ['class' => 'form-control', 'id' => 'tipe_tindakans'])!!}
	</div>
</div>
<script type='text/javascript'>

function dummySubmit(control){
	if(validatePass2(control, [
		{
			'selector' : '.kata_kunci',
			'testFunction' : kataKunciValid,
			'message' : 'Kata Kunci sudah dipakai'
		}
	])){
		$('#submit').click();
	}
}

function kataKunciValid(control){
	if(control == ''){
		return true;
	}
	var result = true;
	$.get(base + '/asuransis/kata_kunci/unique_test',
		{ 
			kata_kunci: control,
			asuransi_id: $('#asuransi_id').val()
		},
		function (data, textStatus, jqXHR) {
			data = $.trim(data);
			result = data == '1'; 
		}
	);
	return result;
}
	var biaya = '';
	var dibayar_asuransi = '';
	var jasa_dokter = '';
	var tipe_tindakan = '';

	var tarifs = $('#tarifs').val();
	tarifs = $.parseJSON(tarifs);

	var temp = '';
	for (var i = 0; i < tarifs.length; i++) {
		temp += '<tr>';
		temp += '<td nowrap class="jenis_tarif">' + tarifs[i].jenis_tarif + '</td>';
		temp += '<td nowrap class="biaya">' + tarifs[i].biaya + '</td>';
		temp += '<td nowrap class="jasa_dokter">' + tarifs[i].jasa_dokter + '</td>';
		temp += '<td class="tipe_tindakan">' +  tarifs[i].tipe_tindakan + '</td>';
		temp += '<td class="action">' + '<button type="button" class="btn btn-warning" onclick="rowEdit(this); return false;" value="' +i+ '">edit</buttom>' + '</td>';
		temp += '<td nowrap class="hide id">' + tarifs[i].id + '</td>';
		temp += '<td class="hide tipe_tindakan_id">' + tarifs[i].tipe_tindakan_id + '</td>';
		temp += '</tr>';
	}

	$('#tblTarif').html(temp);
	</script>
	<script>
		function rowEdit(control){
			var index     = $(control).closest('tr').index() + 1;

			biaya         = $('#tblTarif tr:nth-child(' + index + ') td:nth-child(2)').html();
			jasa_dokter   = $('#tblTarif tr:nth-child(' + index + ') td:nth-child(3)').html();
			tipe_tindakan = $('#tblTarif tr:nth-child(' + index + ') td:nth-child(4)').html();
			tipe_tindakan_id = $(control).closest('tr').find('.tipe_tindakan_id').html();

			var txtbiaya = '<div class="w"><input type="text" class="form-control" value="' +biaya+ '" id="txtbiaya" /></div>';
			var txtjasadokter = '<div class="w"><input type="text" class="form-control" value="' +jasa_dokter+ '" id="txtjasadokter"/></div>';
			var ddltipetindakan = ddlTipeTindakan(tipe_tindakan_id);

			var action = '';
			action += '<button type="button" class="btn btn-info btn-block" onclick="rowUpdate(this);return false;">Update</button>';
			action += '<button type="button" class="btn btn-danger btn-block" onclick="rowCancel(this);return false;">Cancel</button>';

			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(2)').html(txtbiaya);
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(3)').html(txtjasadokter);
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(4)').html(ddltipetindakan);
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(5)').html(action);

			$('#tblTarif tr:nth-child(' + index + ') input[type="text"]').on("click", function () {
				$(this).select();
			});

			$('#tblTarif tr:nth-child(' + index + ') #txtbiaya').click();

			$('.btn-warning').attr('disabled', 'disabled');

			$("#tblTarif .form-control").keydown(function (e) {
				console.log('masuk');
				// Allow: backspace, delete, tab, escape, enter and .
				if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
					 // Allow: Ctrl+A
					(e.keyCode == 65 && e.ctrlKey === true) ||
					 // Allow: Ctrl+C
					(e.keyCode == 67 && e.ctrlKey === true) ||
					 // Allow: Ctrl+X
					(e.keyCode == 88 && e.ctrlKey === true) ||
					 // Allow: home, end, left, right
					(e.keyCode >= 35 && e.keyCode <= 39)) {
						 // let it happen, don't do anything
						 return;
				}
				// Ensure that it is a number and stop the keypress
				if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
					e.preventDefault();
				}
			});

		}
	</script>
	<script>
		function rowCancel(control){

			var index = $(control).closest('tr').index() + 1;

			$('.btn-warning').removeAttr('disabled');

			var key = $(control).val();

		  
			var htmaction = '<button type="button" class="btn btn-warning" onclick="rowEdit(this); return false;" value="' +key+ '">edit</buttom>';

			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(2)').html(biaya);
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(3)').html(jasa_dokter);
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(4)').html(tipe_tindakan);
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(5)').html(htmaction);

		}
		function rowUpdate(control){

			var key   = $(control).closest('tr').index();
			var index = key + 1;
			var id    = $('#tblTarif tr:nth-child(' + index + ') td:last-child').html();

			var biaya_update            = $('#txtbiaya').val();
			var jasa_dokter_update      = $('#txtjasadokter').val();
			var tipe_tindakan_update    = $('#ddltipetindakan option:selected').text();
			var tipe_tindakan_id_update = $('#ddltipetindakan').val();

			tarifs[key]['biaya'] = empty(biaya_update);
			tarifs[key]['jasa_dokter'] = empty(jasa_dokter_update);
			tarifs[key]['tipe_tindakan_id'] = tipe_tindakan_id_update;

			key = $(control).val();

			var htmaction = '<button type="button" class="btn btn-warning" onclick="rowEdit(this); return false;" value="' + key + '">edit</buttom>';
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(2)').html(empty(biaya_update));
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(3)').html(empty(jasa_dokter_update));
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(4)').html(tipe_tindakan_update);
			$(control).closest('tr').find('.tipe_tindakan_id').html(tipe_tindakan_id_update);
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(5)').html(htmaction);
			$('#tblTarif tr:nth-child(' + index + ') td:nth-child(5)').html(htmaction);

			$('#tarifs').val(JSON.stringify(tarifs));


			$('.btn-warning').removeAttr('disabled');

		}

		function empty(val){

			if (val == '') {
				val = '0';
			}

			return val
		}
		function functionName(fun) {
		  var ret = fun.toString();
		  ret = ret.substr('function '.length);
		  ret = ret.substr(0, ret.indexOf('('));
		  return ret;
		}
		function ddlTipeTindakan(val){
			var tipe_tindakans = $('#tipe_tindakans').val();
			var tipe_tindakans = $.parseJSON(tipe_tindakans); 
			console.log('val');
			console.log(val);
			var temp  = '<select class="form-control" id="ddltipetindakan">';
			for (var i = 0; i < tipe_tindakans.length; i++) {
				temp += '<option value="' + tipe_tindakans[i].id + '"' ;
				if(val == tipe_tindakans[i].id){
					temp += ' selected';
				}
				temp += '>';
				temp +=  tipe_tindakans[i].tipe_tindakan + '</option>';
			}
			temp += '</select>' ;
			return temp;
		}

var asuransi_id = $('#asuransi_id').val();
$(':file').on('change', function () {
	  var file = this.files[0];
	  if (file.size > 10485760) {
		alert('File paling besar untuk di upload adalah 10 MB');
		$(this).val('');
	  } else if( $('#nama_file').val() == '' ) {
		alert('Peruntukan berkas harus diisi!');
		$(this).val('');
	  } else {
		  var ajax_data = {
				'asuransi_id': asuransi_id,
				'file':        $('#file').prop('files'),
				'nama_file':   $('#nama_file').val()
			};
		  console.log('ajax_data');
		  console.log(ajax_data);
		$.ajax({
			// Your server script to process the upload
			url: base + '/asuransis/' + asuransi_id + '/upload',
			type: 'post',

			// Form data
			data: new FormData($('form')[0]),

			// Tell jQuery not to process data or worry about content-type
			// You *must* include these options!
			cache: false,
			contentType: false,
			processData: false,

			// Custom XMLHttpRequest
			xhr: function () {
			  var myXhr = $.ajaxSettings.xhr();
			  if (myXhr.upload) {
				// For handling the progress of the upload
				myXhr.upload.addEventListener('progress', function (e) {
				  if (e.lengthComputable) {
					  var persen= e.loaded / e.total *100;
					$('#progress').attr({
					  'aria-valuenow': persen,
					  'style': 'width:' + persen + '%'
					});
					$('#progress').html(String(Math.floor(persen)) + ' %');
				  }
				}, false);
			  }
			  return myXhr;
			},
			success: function (data, textStatus, jqXHR) {
				var color = [
					'primary',
					'info',
					'warning',
					'danger'
				];
				var random_number = Math.floor(Math.random() * 4);
				console.log(random_number);
				var html = '<tr>';
				html += '<td>';
				html += '<a class="btn btn-' + color[random_number]  + ' btn-block" href="' + base + '/berkas/pemeriksaan/' + asuransi_id + '/' + data + '.pdf" target="_blank">Download ' + $('#nama_file').val() + '</a>';
				html += '</td><td nowrap class="autofit">';
				html += '<button type="button" onclick="deleteBerkas(' + data + ', this); return false;" class="btn btn-danger"> <i class="glyphicon glyphicon-remove"></i> </button>'
				html += '</td>';
				html += '</tr>';
				$('#download_container').append(html);
				$('#nama_file').val('');
				$(this).val('');
			}
		  });
	  }
});
function deleteBerkas(id, control){
	if( confirm( "Anda yakin mau menghapus berkas ini?" ) ){
		$.post( base + '/asuransis/berkas/hapus',
			{ berkas_id: id },
			function (data, textStatus, jqXHR) {
				if( parseInt(data) > 0 ){
					$(control).closest('tr').remove();
				} else {
					alert('menghapus gagal');
				}
			}
		);
	}
}
	</script>
