<!DOCTYPE html>
<html lang="es" moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Struk {{ $antrian->id }}</title>
		<link href="{!! asset('css/struk.css') !!}" rel="stylesheet">
		<style type="text/css" media="all">
h2{
	font-size : 20px;
}
h3{
	font-size : 10px;
}
.margin-top {
	margin-top: 10px;
}

		</style>
    </head>
    <body>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="box title-print text-center">
                    <h2 class="text-center border-top">
						Selamat Datang di
						<br />{{ env('NAMA_KLINIK') }}
					</h2>
                    <h3 class="text-center border-bottom border-top">{{ env('ALAMAT_KLINIK') }}
						 <br />{{ env('TELPON_KLINIK') }}
					</h3>
                </div>
            <div>
				<h2 class="text-center">Nomor Antrian Anda Adalah :</h2>
                <table class="table table-condensed">
                    <tbody id="transaksi-print">
                        <tr>
							<td colspan="3" class="strong superbig text-center" id="biaya-print">{{ $antrian->jenis_antrian->prefix }}{{ $antrian->nomor }}</td>
                        </tr>
					</tbody>
                </table>
				<h2 class="text-center ">{{ ucwords( $antrian->jenis_antrian->jenis_antrian ) }}</h2>
				<h3 class="text-center ">{{ $antrian->created_at->format('d M y H:i:s') }}</h3>
				<h2 class="text-center border-top border-bottom margin-top">SEMOGA SEHAT SELALU</h2>
				<br />
            </div>
        </div>
        <script src="{!! url('js/jquery-2.1.1.js') !!}"></script>
        <script type="text/javascript" charset="utf-8">
            window.print();
        </script>
    
	</body>
</html>
