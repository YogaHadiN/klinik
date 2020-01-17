<div class="barcode">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button class="btn btn-info btn-block" type="button" onclick='refresh();return false;'>Refresh</button>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			@if($antrianperiksa->gambars->count() < 1)
				<a href="{{ url('antrianperiksa/'.  $antrianperiksa->id . '/images') }}">
					<img src="{!! url( 'qrcode?text=' . $url . '/antrianperiksa/' . $antrianperiksa->id . '/images' ) !!}" alt="">
				</a>
			@else
				<a href="{{ url('antrianperiksa/'.  $antrianperiksa->id . '/images/edit') }}">
					<img src="{!! url( 'qrcode?text=' . $url . '/antrianperiksa/' . $antrianperiksa->id . '/images/edit' ) !!}" alt="">
				</a>
			@endif
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="panel panel-success">
			<div class="panel-body">
				<h3>Gambar :</h3>
				<div id="gambar">
					
				</div>
			</div>
		</div>
	</div>
</div>


