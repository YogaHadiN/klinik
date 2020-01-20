<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Struk Gaji</title>
		<link rel="stylesheet" href="{{ url('css/struk.css') }}" title="" type="" />
    </head>
    <body>
        
        <div id="content-print">
                <div class="box title-print text-center">
                    <h1>{{ env("NAMA_KLINIK") }}</h1>
                    <h5>
                        {{ env("ALAMAT_KLINIK") }} <br>
                        Telp : {{ env("TELPON_KLINIK") }}  
                    </h5>
                </div>
				<h2 class="text-center border-top border-bottom">Slip Gaji</h2>
                <div>
                    <table>
                        <tbody>
                            <tr>
                                <td nowrap>Tanggal Mulai</td>
                                <td>{{ $bayar->mulai->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td nowrap>Tanggal Akhir</td>
                                <td>{{ $bayar->akhir->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td nowrap>Nama Staf Penerima</td>
                                <td>{{ $bayar->staf->nama }}</td>

                            </tr>
							<tr>
                                <td nowrap>Status Pernikahan</td>
                                <td>
									@if( $bayar->menikah == 1 )
										Menikah
									@else
										Belum Menikah
									@endif
								</td>
                            </tr>
							<tr>
                                <td nowrap>Jumlah Anak</td>
                                <td>{{ $bayar->jumlah_anak }}</td>
                            </tr>
                            <tr>
                                <td nowrap>Gaji Pokok</td>
                                <td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->gaji_pokok) }}</td>
                            </tr>
                            <tr>
                                <td nowrap>Bonus</td>
                                <td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->bonus) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td nowrap>Total Gaji</td>
								<td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->bonus + $bayar->gaji_pokok) }}</td>
                            </tr>
							@if( $bayar->pph21 )
							<tr class="border-top">
                                <td class="text-center" colspan="2">
									<h2>Perhitungan Pph</h2>
								
								</td>
                            </tr>
							<tr class="border-top">
                                <td nowrap>Total Gaji Bulan Ini</td>
                                <td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->total_gaji_bulan_ini) }}</td>
                            </tr>
							<tr>
                                <td nowrap>Biaya Jabatan</td>
                                <td class="text-right">
									@if($bayar->biaya_jabatan > 0)
										( {{ App\Classes\Yoga::buatrp($bayar->biaya_jabatan) }} )
									@else
										{{ App\Classes\Yoga::buatrp($bayar->biaya_jabatan) }}
									@endif
								</td>
                            </tr>
							<tr>
                                <td nowrap>Gaji Netto</td>
                                <td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->gaji_netto) }}</td>
                            </tr>
							<tr>
                                <td nowrap>Gaji Netto Setahun</td>
                                <td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->gaji_netto * 12) }}</td>
                            </tr>
							<tr>
                                <td nowrap>PTKP</td>
                                <td class="text-right">( {{ App\Classes\Yoga::buatrp($bayar->ptkp) }} )</td>
                            </tr>
							<tr class="border-top">
                                <td nowrap>Penghasilan Kena Pajak</td>
								<td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->penghasilan_kena_pajak)}}</td>
                            </tr>
							<tr>
								@include('pdfs.potongan5persen', ['bayar' => $bayar->penghasilan_kena_pajak])
								<td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->potongan5persen ) }}</td>
                            </tr>
							<tr>
								@include('pdfs.potongan15persen', ['bayar' => $bayar->penghasilan_kena_pajak])
								<td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->potongan15persen ) }}</td>
                            </tr>
							<tr>
								@include('pdfs.potongan25persen', ['bayar' => $bayar->penghasilan_kena_pajak])
								<td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->potongan25persen ) }}</td>
                            </tr>
							<tr>
								@include('pdfs.potongan30persen', ['bayar' => $bayar->penghasilan_kena_pajak])
								<td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->potongan30persen ) }}</td>
                            </tr>
							@if(empty( trim(  $bayar->staf->npwp  ) ))
								<tr class="border-top">
									<td colspan="2" class="text-right">
										{{ App\Classes\Yoga::buatrp(
											$bayar->potongan5persen +
											$bayar->potongan15persen +
											$bayar->potongan25persen +
											$bayar->potongan30persen
										) }}
									</td>
								</tr>
								<tr class="border-top">
									<td colspan="2"><strong>Karena Tidak punya NPWP,maka staf ini dibebani 1,2 kali pajak normal</strong></td>
								</tr>
								<tr>
									<td colspan="2" class="text-right">1,2 x {{ App\Classes\Yoga::buatrp(
																	$bayar->potongan5persen +
																	$bayar->potongan15persen +
																	$bayar->potongan25persen +
																	$bayar->potongan30persen
																) }} = {{ App\Classes\Yoga::buatrp($bayar->pph21setahun) }}
									</td>
								</tr>
							@endif
							<tr class="border-top">
                                <td nowrap>Pph21 setahun (simulasi)</td>
                                <td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->pph21setahun) }}</td>
                            </tr>
							<tr class="border-top">
                                <td nowrap>Pph21 bulan ini</td>
                                <td class="text-right">{{ App\Classes\Yoga::buatrp($bayar->pph21) }}</td>
                            </tr>
							@endif
                            <tr class="border-top">
                                <td colspan="2">Total</td>
                            </tr>
							<tr>
                                <td colspan="2" class="text-right">
                                    <h2 id="pembayaranDokter">
									{{ App\Classes\Yoga::buatrp( $bayar->gaji_pokok + $bayar->bonus  - $bayar->pph21) }}
                                    </h2>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Terbilang</td>
                            </tr>
							<tr>
                                <td colspan="2">{{ App\Classes\Yoga::terbilang( $bayar->gaji_pokok + $bayar->bonus  - $bayar->pph21) }} rupiah</td>
							</tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    Diserahkan pada <span id="tanggal">{{ $bayar->created_at->format('d-m-Y') }}</span> jam <span id="jam"> {{  $bayar->created_at->format('H:i:s')  }}</span>
                    <table class="table-center text-center">
                        <tbody>
                            <tr class="border-top">
                                <td>Diserahkan Oleh</td>
                                <td>Diterima Oleh</td>
                            </tr>
                            <tr class="tanda-tangan">
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>(...... ............. )</td>
                                <td>{{ $bayar->staf->nama }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				<div class="border-top">
					<div class="text-center" colspan="2">
						<h2>Ringkasan Gaji Bulan Ini</h2>
					
					</div>
				</div>
				<div>
					<table class="bordered">
						<thead>
							<tr>
								<th>Tanggal</th>
								<th>Pembayaran</th>
								<th>Pph21</th>
							</tr>
						</thead>
						<tbody>
							@foreach( $gajis as $gaji )
								<tr>
									<td>{{ $gaji->created_at->format('d M Y') }}</td>
									<td class="text-right">{{App\Classes\Yoga::buatrp(  $gaji->gaji_pokok + $gaji->bonus  )}}</td>
									<td class="text-right">{{ App\Classes\Yoga::buatrp( $gaji->pph21 ) }}</td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th class="text-right">{{ App\Classes\Yoga::buatrp( $total_pembayaran_bulan_ini )}}</th>
								<th class="text-right">{{ App\Classes\Yoga::buatrp( $total_pph_bulan_ini ) }}</th>
							</tr>
						</tfoot>
					</table>
				
				</div>
                <div class="small-padding">
                    .
                </div>
            </div>
    </body>
</html>
