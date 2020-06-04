<div class="row">
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		@if ( isset($home_visit) )
			{!! Form::text('nama_pasien', $home_visit->pasien->nama, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
			<div class="form-group hide @if($errors->has('pasien_id')) has-error @endif">
			  {!! Form::label('pasien_id', 'Pasien', ['class' => 'control-label']) !!}
			  {!! Form::text('pasien_id' , $home_visit->pasien_id, ['class' => 'form-control']) !!}
			  @if($errors->has('pasien_id'))<code>{{ $errors->first('pasien_id') }}</code>@endif
			</div>
		@else
			{!! Form::text('nama_pasien', $pasien->nama, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
			<div class="form-group hide @if($errors->has('pasien_id')) has-error @endif">
			  {!! Form::label('pasien_id', 'Pasien', ['class' => 'control-label']) !!}
			  {!! Form::text('pasien_id' , $pasien->id, ['class' => 'form-control']) !!}
			  @if($errors->has('pasien_id'))<code>{{ $errors->first('pasien_id') }}</code>@endif
			</div>
		@endif
		<div class="form-group @if($errors->has('sistolik')) has-error @endif">
		  {!! Form::label('sistolik', 'Sistolik', ['class' => 'control-label']) !!}
		  {!! Form::text('sistolik' , null, ['class' => 'form-control']) !!}
		  @if($errors->has('sistolik'))<code>{{ $errors->first('sistolik') }}</code>@endif
		</div>
		<div class="form-group @if($errors->has('diastolik')) has-error @endif">
		  {!! Form::label('diastolik', 'Diastolik', ['class' => 'control-label']) !!}
		  {!! Form::text('diastolik' , null, ['class' => 'form-control']) !!}
		  @if($errors->has('diastolik'))<code>{{ $errors->first('diastolik') }}</code>@endif
		</div>
		<div class="form-group @if($errors->has('berat_badan')) has-error @endif">
		  {!! Form::label('berat_badan', 'Berat Badan', ['class' => 'control-label']) !!}
		  {!! Form::text('berat_badan' , null, ['class' => 'form-control']) !!}
		  @if($errors->has('berat_badan'))<code>{{ $errors->first('berat_badan') }}</code>@endif
		</div>
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				@if(isset( $home_visit ))
					<button class="btn btn-success btn-block" type="button" onclick='dummySubmit(this);return false;'>Update</button>
				@else
					<button class="btn btn-success btn-block" type="button" onclick='dummySubmit(this);return false;'>Submit</button>
				@endif
				{!! Form::submit('Submit', ['class' => 'btn btn-success hide', 'id' => 'submit']) !!}
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<a class="btn btn-danger btn-block" href="{{ url('home_visits') }}">Cancel</a>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		<div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
			{!! Form::label('image', 'Image') !!}
			{!! Form::file('image') !!}
				@if (isset($home_visit) && $home_visit->image)
					<p> {!! HTML::image(asset($home_visit->image), null, ['class'=>'img-rounded upload']) !!} </p>
				@else
					<p> {!! HTML::image(asset('img/photo_not_available.png'), null, ['class'=>'img-rounded upload']) !!} </p>
				@endif
			{!! $errors->first('image', '<p class="help-block">:message</p>') !!}
		</div>
	</div>
</div>
