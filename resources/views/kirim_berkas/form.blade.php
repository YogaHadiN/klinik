	@include('kirim_berkas.staf_form')
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group @if($errors->has('tanggal'))has-error @endif">
			  {!! Form::label('tanggal', 'Tanggal Pengiriman', ['class' => 'control-label']) !!}
				{!! Form::text('tanggal', date('d-m-Y'), array(
					'class'         => 'form-control rq tanggal'
				))!!}
			  @if($errors->has('tanggal'))<code>{{ $errors->first('tanggal') }}</code>@endif
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body">
			<h3>Hasil : </h3>
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Nama Asuransi</th>
							<th>Jumlah Tagihan</th>
							<th>Total Tagihan</th>
						</tr>
					</thead>
					<tbody id="rekap_pengecekan">

					</tbody>
				</table>
			</div>
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					@if(isset($update))
						<button class="btn btn-success btn-block" type="button" onclick='dummySubmit(this);return false;'>Update</button>
					@else
						<button class="btn btn-success btn-block" type="button" onclick='dummySubmit(this);return false;'>Submit</button>
					@endif
					{!! Form::submit('Submit', ['class' => 'btn btn-success hide', 'id' => 'submit']) !!}
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<a class="btn btn-danger btn-block" href="{{ url('home/') }}">Cancel</a>
				</div>
			</div>
		</div>
	</div>
	<div class="row hide">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group @if($errors->has('name'))has-error @endif">
			  {!! Form::label('name', 'Nama', ['class' => 'control-label']) !!}
				<textarea name="piutang_tercatat" id="piutang_tercatat" rows="8" cols="40">[]</textarea>
			  @if($errors->has('name'))<code>{{ $errors->first('name') }}</code>@endif
			</div>
		</div>
	</div>
