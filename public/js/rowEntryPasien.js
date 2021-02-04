        function rowEntry(control) {
			$('#antrianpoli_poli').val('');
			$('#antrianpoli_tanggal').val('');

            $('#cekBPJSkontrol').hide();
            $('#cekGDSBPJS').hide();

            $('#cekProlanisDM').hide();
            $('#cekProlanisHT').hide();

			var nama             = $(control).closest('tr').find('td:nth-child(2) div').html();
			var image            = base + '/' + $(control).closest('tr').find('td:nth-child(12) div').html();
			var nama_asuransi    = $(control).closest('tr').find('td:nth-child(6) div').html();
			var option_asuransi  = '<option value="">- Pilih Pembayaran -</option>';
			option_asuransi     += '<option value="0">Biaya Pribadi</option>';

			var ID = $(control).closest('tr').find('td:first-child div').html();
			var asuransi_id = $(control).closest('tr').find('td:nth-child(11) div').html();

			var prolanis_dm = $(control).closest('tr').find('.prolanis_dm div').html();
			var prolanis_ht = $(control).closest('tr').find('.prolanis_ht div').html();

            if (prolanis_dm == '1') {
                $('#cekProlanisDM').show();

                $.get( base + '/pasiens/ajax/status_cel_gds_bulan_ini',
                    { 
                        pasien_id: ID
                    },
                    function (data, textStatus, jqXHR) {
                        data = $.trim(data);
                        if (data > 0) {
                            $('#gds_prolanis_status').html('Sudah ');
                        } else {
                            $('#gds_prolanis_status').html('Harus ');
                        }
                    }
                );

            }
            if (prolanis_ht == '1') {
                $('#cekProlanisHT').show();
            }


			cekBPJSkontrol(ID, asuransi_id);

            imgError();

            if (asuransi_id != '0') {
                option_asuransi += '<option value="' + asuransi_id + '">' + nama_asuransi + '</option>'
            };

            $('#lblInputNamaPasien').html(ID + ' - ' + nama)
                .closest('.form-group')
                .removeClass('has-error')
                .find('code')
                .remove();
            $('#namaPasien').val(nama);
            $('#imageForm').attr('src', image);
            $('#ID_PASIEN').val(ID);
            $("#ddlPembayaran").html(option_asuransi);
            resetComplain();
            $('#exampleModal').modal('show');
            return false;
        }
