<div class="table-responsive">
	  <table class="table table-condensed table-hover">
		  <thead>
			  <tr>
				  <th>Nama Asuransi</th>
				  <th>Jumlah</th>
				  @foreach($polis as $poli)	
					  <th>{{ $poli }}</th>
				  @endforeach
			  </tr>
		  </thead>
		  <tbody>
			  @if (count($hariinis) > 0)
				  @foreach ($hariinis as $hariini)
					  <tr>
						  <td>{!! $hariini->nama !!}</td>
						  <th>{!! $hariini->jumlah !!}</th>
						  @foreach($polis as $poli)	
							  <td class="text-center">{{ App\Classes\Yoga::periksa_by_asuransi($tanggal, $hariini->id, $poli) }}</td>
						  @endforeach
					  </tr>
				  @endforeach
			  @else
				  <tr>
					  <td colspan="2" class="text-center">Tidak ada data untuk ditampilkan :p</td>
				  </tr>
			  @endif
		  </tbody>
		  <tfoot>
			  <th> Jumlah </th>
			  <th>{!! count($periksas) !!}</th>
			  @foreach($polis as $poli)	
				  <th class="text-center">{{ App\Classes\Yoga::periksa_by_poli($tanggal, $poli) }}</th>
			  @endforeach
		  </tfoot>
	  </table>
  </div>
