<table class="table borderless table-condensed">
	<thead>
		<tr>
			<th class="">Jurnal Id</th>
			<th class="hide">Key</th>
			<th>Akun </th>
			<th>Debet</th>
			<th>Kredit</th>
			<th>Created At / Updated At</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@if ($count > 0)
			@foreach($jurnals as $k=>$jur)
				   <tr>
					   <td class="id"> {{ $jur->id }}</td>
					   <td class="key hide"> {{ $k }}</td>
					   <td>
						   {!! Form::select('coa_id', App\Coa::list(), $jur->coa_id, [
							   'class'            => 'form-control selectpick',
							   'data-live-search' => 'true',
							   'onchange'         => 'coaChange(this);'
						   ])!!}
					   
					   </td>
					@if($jur->debit == 1)
					   <td>
						   {!! Form::text('nilai', $jur->nilai, [
							   'class' => 'form-control uangInput text-right',
							   'title' => $k,
							   'onkeyup' => 'nilaiKeyUp(this);return false;'
						   ]) !!}
						</td>
					   <td></td>
				   @else

					   <td></td>
					   <td>
						   {!! Form::text('nilai', $jur->nilai, [
							   'class' => 'form-control uangInput text-right',
							   'title' => $k,
							   'onkeyup' => 'nilaiKeyUp(this);return false;'
						   ]) !!}
					   </td>
					@endif
						<td>
						   {!! Form::text('created_at', $jur->created_at, [
							   'class' => 'form-control text-right',
							   'title' => $k,
							   'onkeyup' => 'createdAtKeyUp(this);return false;'
						   ]) !!}
					   </td>
						<td>
							<button value="{{ $k }}" class="btn btn-danger btn-xs btn-block" type="button" onclick="delJurnal(this);return false;">delete</button>
						</td>
					</tr>
			@endforeach
		  @else
			<tr>
			  <td colspan="
			  @if( \Auth::id() == '28' )
				  8
			  @else
				  7
			  @endif
				  " class="text-center">Tidak ada Data Untuk Ditampilkan :p</td>
			</tr>
		  @endif
	</tbody>
</table>
