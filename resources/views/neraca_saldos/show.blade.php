@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Neraca Saldo

@stop
@section('page-title') 
 <h2>Neraca Saldo</h2>
 <ol class="breadcrumb">
      <li>
          <a href="{!! url('laporans')!!}">Home</a>
      </li>
      <li class="active">
          <strong>Neraca Saldo</strong>
      </li>
</ol>
@stop
@section('content') 
  <div class="panel panel-default">
    <div class="panel-body">
		<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
			  <tr>
				<th>Kode Account</th>
				<th>Nama Account</th>
				<th>Debet</th>
				<th>Kredit</th>
			  </tr>
			</thead>
			<tbody>
			  @foreach($jurnalumums as $ju)
			  <tr>
				<td>{{ $ju['coa_id']}}</td>
				<td>{{ $ju['coa']}}</td>
				@if($ju['nilai'] > 0)
				  <td class="uang">{{ $ju['nilai']}}
				@else
				  <td>
				@endif
				</td>
				@if($ju['nilai'] < 0)
				<td class="uang">{{ abs($ju['nilai'])}}
				@else
				<td>
				@endif
				</td>
			  </tr>
			  @endforeach
			</tbody>
			<tfoot>
			  <td colspan="2"></td>
			  <td class="uang light-bold text-center">{{ App\Classes\Yoga::neracaDebet($jurnalumums)}}</td>
			  <td class="uang light-bold text-center">{{ App\Classes\Yoga::neracaKredit($jurnalumums)}}</td>
			</tfoot>
		  </table>
		</div>
    </div>
  </div>
@stop
@section('footer') 

@stop
