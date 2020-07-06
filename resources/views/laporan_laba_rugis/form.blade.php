            <div id="header" class="text-center">
              <h3>{{ env('NAMA_KLINIK') }}</h3>
              <h4>{{ env('ALAMAT_KLINIK') }}</h4>
            </div>
            <hr>
            <h3 class="text-center">LAPORAN LABA RUGI</h3>
            <h4 class="red text-center">Periode {{ date('d M Y', strtotime($tanggal_awal)) }} sampai dengan {{ date('d M Y', strtotime($tanggal_akhir)) }}</h4>
            <div class="content">
				<div class="table-responsive">
				  <table class="table table-bordered table-condensed">
					<tbody>
					  <tr>
						<td colspan="5" class="light-bold">Pendapatan Usaha</td>
					  </tr>
					  @foreach($pendapatan_usahas['akuns'] as $p)
						  <tr>
							<td></td>
							<td colspan="2"><a href="{{ url('buku_besars/show?coa_id='. $p['coa_id'] . '&bulan='. $bulan . '&tahun='. $tahun) }}">{{ $p['coa'] }}</a></td>
							<td class="text-right" nowrap>{{ abs( $p['nilai']  ) }}</td>
							<td></td>
						  </tr>
					  @endforeach
					  <tr>
						<td></td>
						<td></td>
						<td>Total Pendapatan Usaha</td>
						<td></td>
						<td class="text-right" nowrap>{{ $pendapatan_usahas['total_nilai']}}</td>
						{{-- <td class="text-right" nowrap>{{App\Classes\Yoga::buatrp(  $pendapatan_usahas['total_nilai']  )}}</td> --}}
					  </tr>
					  <tr>
						<td colspan="5" class="light-bold">Harga Pokok Penjualan</td>
					  </tr>
					  @foreach($hpps['akuns'] as $hpp)
					  <tr>
						<td></td>
						<td colspan="2"><a href="{{ url('buku_besars/show?coa_id='. $hpp['coa_id'] . '&bulan='. $bulan . '&tahun='. $tahun) }}">{{ $hpp['coa'] }}</a></td>
						<td class="text-right">{{ abs($hpp['nilai'])  }}</td>
						<td></td>
					  </tr>
					  @endforeach
					  <tr>
						<td></td>
						<td></td>
						<td>Total Harga Pokok Penjualan</td>
						<td></td>
						{{-- <td class="text-right" nowrap>({{ $hpps['total_nilai'] }})</td> --}}
						<td class="text-right" nowrap>({{ $hpps['total_nilai'] }})</td>
					  </tr>
					  <tr class="red light-bold">
						<td colspan="2"></td>
						<td>Laba Rugi Kotor</td>
						<td></td>
						<td class="text-right">{{ $pendapatan_usahas['total_nilai'] - $hpps['total_nilai'] }}</td>
						{{-- App\Classes\Yoga::buatrp( $pendapatan_usahas['total_nilai'] - $hpps['total_nilai'] ) --}}
					  </tr>

					  <tr>
						<td colspan="5" class="light-bold">Biaya Operasional</td>
					  </tr>
					  @foreach($biayas['akuns'] as $biaya)
					  <tr>
						<td></td>
						<td colspan="2"><a href="{{ url('buku_besars/show?coa_id='. $biaya['coa_id'] . '&bulan='. $bulan . '&tahun='. $tahun) }}">{{ $biaya['coa'] }}</a></td>
						<td class="text-right" nowrap>{{ abs($biaya['nilai'])  }}</td>
						<td></td>
					  </tr>
					  @endforeach
					  <tr>
						<td></td>
						<td></td>
						<td>Total Biaya Operasional</td>
						<td></td>
						<td class="text-right" nowrap>({{ $biayas['total_nilai'] }})</td>
						{{-- <td class="text-right" nowrap>({{App\Classes\Yoga::buatrp(  $biayas['total_nilai'] )}})</td> --}}
					  </tr>
					  <tr class="red light-bold">
						<td></td>
						<td></td>
						<td>Laba Rugi Kotor</td>
						<td></td>
						{{-- <td class="text-right" nowrap>{{App\Classes\Yoga::buatrp(  $pendapatan_usahas['total_nilai']- $hpps['total_nilai'] -  $biayas['total_nilai']  )}}</td> --}}
						<td class="text-right" nowrap>{{ $pendapatan_usahas['total_nilai']- $hpps['total_nilai'] -  $biayas['total_nilai']  }}</td>
					  </tr>
					  <tr>
						<td colspan="5" class="light-bold">Pendapatan Lain</td>
					  </tr>
					  @foreach($pendapatan_lains['akuns'] as $pend)
					  <tr>
						<td></td>
						<td colspan="2"><a href="{{ url('buku_besars/show?coa_id='. $pend['coa_id'] . '&bulan='. $bulan . '&tahun='. $tahun) }}">{{ $pend['coa'] }}</a></td>
						<td class="text-right" nowrap>{{ abs($pend['nilai'])  }}</td>
						<td></td>
					  </tr>
					  @endforeach
					  <tr>
						<td></td>
						<td></td>
						<td>Total Pendapatan Lain</td>
						<td></td>
						<td class="text-right" nowrap>{{ $pendapatan_lains['total_nilai'] }}</td>
						{{-- <td class="text-right" nowrap>{{App\Classes\Yoga::buatrp(  $pendapatan_lains['total_nilai'] )}}</td> --}}
					  <tr class="red light-bold">
						<td colspan="2"></td>
						<td>Laba Rugi Bersih</td>
						<td></td>
						<td class="text-right" nowrap>{{ $pendapatan_usahas['total_nilai'] - $hpps['total_nilai'] -  $biayas['total_nilai'] +  $pendapatan_lains['total_nilai']  }}</td>
						{{-- <td class="text-right" nowrap>{{App\Classes\Yoga::buatrp(  $pendapatan_usahas['total_nilai'] - $hpps['total_nilai'] -  $biayas['total_nilai'] +  $pendapatan_lains['total_nilai']  )}}</td> --}}
					  </tr>
					  </tr>
					</tbody>
				  </table>
				</div>
            </div>
        
