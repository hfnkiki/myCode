@extends('layout.blankBS5')
@section('content')
<style>
	.flow-info-section{
		padding-bottom: 1.5rem;
		margin-bottom: 2.5rem;
		border-bottom: 1px solid #cfcfcf;
	}

	.info-value{
		min-height: 32px;
		display: flex;
		align-items: center;
		border-bottom: 1px solid #999;
		padding: 0.25rem 0;
	}

	/* ================= 以下為新增：只有 CSS，沒有改動任何 HTML / PHP / JS ================= */

	/* 標籤欄不可被壓成一個字寬(直排)：允許換行，但至少保留 9 個字寬 */
	.rwd-form-table td.text-nowrap {
		white-space: normal !important;
		min-width: 9em;
		overflow-wrap: anywhere;
	}
	/* 內容欄至少保留寬度，沒資料(空欄位)時不會縮成一小格 */
	.rwd-form-table td:not(.text-nowrap) {
		min-width: 12em;
		overflow-wrap: break-word;
	}
	/* 內容再寬也不撐出表格外：輸入框、巢狀表格、圖片都限制在欄位內 */
	.rwd-form-table td > *,
	.rwd-form-table td input[type="text"],
	.rwd-form-table td select,
	.rwd-form-table td textarea,
	.rwd-form-table td img {
		max-width: 100%;
	}
	.rwd-form-table td table {
		max-width: 100%;
		width: 100%;
	}
	.rwd-form-table td textarea {
		resize: both;
		overflow: auto;
	}

	/* 後端欄位 html 使用 Bootstrap 3 的 col-xs-* 格線，Bootstrap 5 沒有這組 class → 補上等效寬度 */
	.rwd-form-table [class*="col-xs-"] {
		float: left;
		flex: 0 0 auto;
		max-width: 100%;
		padding-left: 12px;
		padding-right: 12px;
	}
	.rwd-form-table .col-xs-1  { width: 8.33333333%; }
	.rwd-form-table .col-xs-2  { width: 16.66666667%; }
	.rwd-form-table .col-xs-3  { width: 25%; }
	.rwd-form-table .col-xs-4  { width: 33.33333333%; }
	.rwd-form-table .col-xs-5  { width: 41.66666667%; }
	.rwd-form-table .col-xs-6  { width: 50%; }
	.rwd-form-table .col-xs-7  { width: 58.33333333%; }
	.rwd-form-table .col-xs-8  { width: 66.66666667%; }
	.rwd-form-table .col-xs-9  { width: 75%; }
	.rwd-form-table .col-xs-10 { width: 83.33333333%; }
	.rwd-form-table .col-xs-11 { width: 91.66666667%; }
	.rwd-form-table .col-xs-12 { width: 100%; }
	.rwd-form-table td table .row { margin-top: 0; }
</style>
<div class="container px-3 px-md-4">	
	<div class="row justify-content-center mt-4 mb-3">
		<div class="col-12 col-md-10">
			<h1 class="h4 pb-2 fw-bold border-bottom" style="color: #6c757d; border-color: #ccc !important;">
				{{trans('menu.flowTracking')}}
			</h1>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-12 col-md-10">
			<pre id="msg" class="alert alert-danger text-center fw-bold" style="{{isset($msg)? '': 'display:none;'}}">{{isset($msg)? $msg: ''}}</pre>
		</div>
	</div>

	@if(isset($fields))
	<div class="row justify-content-center mt-2">
		<div class="col-12 col-md-10">
			<div class="flow-info-section">
				<div class="row align-items-center g-3">
					<div class="col-md-2 col-12 fw-bold text-dark">
						• {{ trans('personal_todo.flowName') }}
					</div>
					<div class="col-md-4 col-12">
						<div class="info-value">
							{{ $flowName }}
						</div>
					</div>
					<div class="col-md-2 col-12 fw-bold text-dark">
						• {{ trans('personal_todo.formSubj') }}
					</div>
					<div class="col-md-4 col-12">
						<div class="info-value">
							{{ $formSubj }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row justify-content-center mb-5">
		<div class="col-12 col-md-10">
			<div class="table-responsive">
				<table class="table form-table rwd-form-table align-middle w-100 mb-0">
					<tbody>
					@php($roWidth = 0)
					@foreach($fields as $item => $row)
						
						@if($row->type === 'label')
							@if($roWidth != 0)
								@php($roWidth = 0)
							@endif
							<tr>
								<th colspan="4" class="text-center py-2" style="font-size: 16px;">{{$row->name}}</th>
							</tr>
							@continue
						@endif

						@php($label = isset($row->noLabel)? false: true)
						@php($width = isset($row->width)? (int)$row->width: 4)

						@if($roWidth + ($label? 2: 0) + $width > 12 && $roWidth != 0)
							</tr>
							@php($roWidth = 0)
						@endif

						@if($roWidth == 0)
							<tr>
						@endif

						@if($label)
							@php($roWidth += 2)
							<td class="text-nowrap" style="width: 15%;">• {{$row->name}}</td>
						@endif

						@php($roWidth += $width)
						<td colspan="{{ $width >= 10 ? 3 : 1 }}" style="width: {{ $width >= 10 ? '85%' : '35%' }};">
							@if(isset($row->html))
								{!! $row->html !!}
							@endif
						</td>
					@endforeach
					
					@if($roWidth != 0)
						</tr>
					@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif
</div>
@endsection
