<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INSPINIA | Login</title>
    {!! HTML::style('css/bootstrap.min.css')!!}
    {!! HTML::style('font-awesome/css/font-awesome.css')!!}
    {!! HTML::style('css/animate.css')!!}
    {!! HTML::style('css/style.css')!!}
	<style type="text/css" media="all">
		.imgKonfirmasi {
			width : 300px;
			height : 300px;
		}
		.superbig-button{
			padding : 50px;
			font-size : 50px;
			border-radius : 20px;
		}
		.content-secondary{
			padding : 0px 150px;
		}
		h1{
			font-size: 100px;
			margin-bottom : 50px;
		}
		h2{
			font-size: 75px;
			margin-bottom : 30px;
		}
	</style>

</head>

<body class="gray-bg">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			@if (Session::has('pesan'))
				{!! Session::get('pesan')!!}
			@endif
		</div>
	</div>
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="content-secondary">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
					<h1>Klinik Jati Elok</h1>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
					<h2>Pilih Antrian</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<a class="btn btn-lg btn-block btn-success superbig-button" href="{{ url('fasilitas/antrian_pasien/umum') }}">Dokter Umum</a>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<a class="btn btn-lg btn-block btn-primary superbig-button" href="{{ url('fasilitas/antrian_pasien/gigi') }}">Dokter Gigi</a>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<br />
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<a class="btn btn-lg btn-block btn-info superbig-button" href="{{ url('fasilitas/antrian_pasien/kebidanan') }}">Bidan</a>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<a class="btn btn-lg btn-block btn-warning superbig-button" href="{{ url('fasilitas/antrian_pasien/estetika') }}">Kecantikan</a>
				</div>
			</div>
		</div>
	</div>
    <!-- Mainly scripts -->
	<script type="text/javascript" charset="utf-8">
			 setTimeout(function(){ 
				$('.alert').fadeOut(500);
			 }, 10000);
	</script>

    {!! HTML::script('js/jquery-2.1.1.js')!!}
    {!! HTML::script('js/bootstrap.min.js')!!}

</body>

</html>
