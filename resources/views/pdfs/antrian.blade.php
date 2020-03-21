<!DOCTYPE html>
<html lang="es" moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Struk {{ $antrian_poli->id }} | {{ $antrian_poli->pasien->nama }}</title>
		<link href="{!! asset('css/struk.css') !!}" rel="stylesheet">
    </head>
    <body>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="box title-print text-center">
                    <h1>{{ env("NAMA_KLINIK") }}</h1>
                    <h5>
                        {{ env("ALAMAT_KLINIK") }} <br>
                        Telp : {{ env("TELPON_KLINIK") }}  
                    </h5>
                    <h2 class="text-center border-top border-bottom">Pemeriksaan Dokter</h2>
                </div>
            <div class="box border-bottom">
                <table>
                    <tbody>
                        <tr>
                            <td>Nama Pasien</td>
                            <td>:</td>
                            <td>{{ $antrian_poli->pasien->nama }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td>{{App\Classes\Yoga::updateDatePrep(  $antrian_poli->tanggal  )}}</td>
                        </tr>
                        <tr>
                            <td>Jam Datang</td>
                            <td>:</td>
                            <td>{{ $antrian_poli->jam }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <table class="table table-condensed">
                    <tbody id="transaksi-print">
                        <tr>
							<td colspan="3" class="strong uang text-right" id="biaya-print">{{ $antrian_poli->antrian }}</td>
                        </tr>
					</tbody>
                </table>
				<br />
            </div>
        </div>
        <script src="{!! url('js/jquery-2.1.1.js') !!}"></script>
        <script type="text/javascript" charset="utf-8">
            window.print();
        </script>
    
	</body>
</html>
