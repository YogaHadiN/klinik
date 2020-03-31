
        $(document).ready(function() {


            $('.uangInput').autoNumeric('init', {
                aSep: '.',
                aDec: ',', 
                aSign: 'Rp. ',
                vMin: '-9999999999999.99' ,
                mDec: 0
            });

            formatUang();
            
            $('.jumlah').each(function() {
                var number = $(this).html();
                number = number.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // 43,434
                $(this).html(number);
            });

            $('.selectpick')
                .selectpicker({
                style: 'btn-default',
                size: 10,
                selectOnTab : true,
                style : 'btn-white'
            });
        //plug in datetimepicker waktu bebas terserah

            $('.tanggal').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: 'dd-mm-yyyy'
            });

			$('.tanggal').closest('form').attr('autocomplete', 'off');

            $('.bulanTahun').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: 'mm-yyyy',
                minViewMode: 'months'
            });

            $('.DTa').dataTable({
                "dom": 'T<"clear">lfrtip',
            });

            $('.DTs').dataTable({
                "dom": 'T<"clear">lfrtip',
                "bSort" : false,
                "searching" : false
            });

            $('.DTsWithI').dataTable({
                "dom": 'T<"clear">lfrtip',
                "searching" : false
            });

            $('.DT').dataTable({
                "dom": 'T<"clear">lfrtip',
                "bSort" : false
            });

		  $('[data-toggle="tooltip"]').tooltip();

            $('.DTi').dataTable({
                "aaSorting": [[ 6, "desc" ]],
                "responsive" : true,
                "dom": 'T<"clear">lfrtip',
                // "bSort" : false,
                "tableTools": {
                    "sSwfPath": "js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
                }
            });
            /* Init DataTables */
            var oTable = $('#editable').dataTable();
            /* Apply the jEditable handlers to the table */
            oTable.$('td').editable( '../example_ajax.php', {
                "callback": function( sValue, y ) {
                    var aPos = oTable.fnGetPosition( this );
                    oTable.fnUpdate( sValue, aPos[0], aPos[1] );
                },
                "submitdata": function ( value, settings ) {
                    return {
                        "row_id": this.parentNode.getAttribute('id'),
                        "column": oTable.fnGetPosition( this )[2]
                    };
                },
                "width": "90%",
                "height": "100%"
            });
	});
      function fnClickAddRow() {
            $('#editable').dataTable().fnAddData( [
                "Custom row",
                "New row",
                "New row",
                "New row",
                "New row" ] );
        }
		function updateLandingLinkClass(){
			var jumlah_antrian = $('#jumlah_antrian').html();
			console.log('jumlah_antrian');
			console.log(jumlah_antrian);
			if(
				parseInt(jumlah_antrian) > 0 &&
				!$('#jumlah_antrian').closest('li').hasClass('landing_link')
			){
				$('#jumlah_antrian').closest('li').addClass('landing_link')
			} else if (
				parseInt(jumlah_antrian) < 1 &&
				$('#jumlah_antrian').closest('li').hasClass('landing_link')
			) {
				$('#jumlah_antrian').closest('li').removeClass('landing_link')
			}
			$('#jumlah_antrian').closest('li').fadeOut(function(){
				$(this).fadeIn();
			});
		}
		function playBell(){
			document.getElementById('myAudio').play();
		}
		function pglPasien(sound){
			var x     = document.getElementById("myAudio");
			x.play();
			var index = 0;
			x.onended = function() {
				if(index < sound.length){
					x.src=base + '/sound/' + sound[index];
					x.play();
					index++;
				} else {
					x.src=base + '/sound/bel.mpeg';
				}
			};
		}
		function pusherCaller(){
			var channel_name    = 'my-channel';
			var event_name      = 'form-submitted';

			Pusher.logToConsole = true;

			var pusher = new Pusher('281b6730814874b6b533', {
			  cluster: 'ap1',
			  forceTLS: true
			});

			var channel = pusher.subscribe(channel_name);
			channel.bind(event_name, function(data) {
				$('#jumlah_antrian').html(data.text.count);
				updateLandingLinkClass();
			});
		}
		function validatePass2(control, extraValid = []){
			var pass  = true;
			var value = '';
			var param = [
				{
					'selector': '.rq',
					'testFunction': validateNotEmpty,
					'message':   'Harus diisi'
				},
				{
					'selector': '.tanggal',
					'testFunction': validatedate,
					'message':   'Format Tanggal tidak benar'
				},
				{
					'selector': '.numeric',
					'testFunction': validateNumeric,
					'message':   'Format Tanggal tidak benar'
				},
				{
					'selector': '.email',
					'testFunction': validateEmail,
					'message':   'Format Email tidak benar'
				},
				{
					'selector': '.phone',
					'testFunction': validatePhone,
					'message':   'Format Telepon tidak benar'
				}
			];
			for (var i = 0, len = extraValid.length; i < len; i++) {
				param.push(extraValid[i]);
			}
			for (var i = 0, len = param.length; i < len; i++) {
				$(control).closest('form').find( param[i].selector + ':not(div)').each(function(index, el) {
				  value = $(this).val();
				  if ( !param[i].testFunction(value) ) {
					validasi1($(this), param[i].message);
					pass = false;
				  }
				});
			}
			if (!pass) {
				$(control).closest('form').find('.rq').each(function(index, el) {
				  if ($(this).val() == '') {
					$(this).focus();
					return false;
				  }
				});
			}
			return pass;
		}
    
