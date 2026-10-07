@extends('layout.blankBS5')
@section('content')
<!-- flowtracking-view-rev: 2026-10-07-r1 -->
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

	/* ===== 表單明細：固定 4 欄格線(標籤 15% | 內容 35% | 標籤 15% | 內容 35%) =====
	   用 table-layout: fixed + colgroup，欄寬不再被內容(輸入框、巢狀表格)或空白欄位影響：
	   ① th 不會被壓成一個字寬 ② 內容再寬也不會把整個表格撐到右邊 ③ 沒資料的欄位不會縮成一小格 */
	.form-grid {
		table-layout: fixed;
		width: 100%;
		max-width: 100%;
	}
	.form-grid > tbody > tr > td,
	.form-grid > tbody > tr > th {
		min-width: 0;
		overflow-wrap: break-word;
		vertical-align: top;
	}
	/* 標籤欄 */
	.form-grid > tbody > tr > td.grid-label {
		font-weight: bold;
		overflow-wrap: anywhere;
	}
	/* 內容欄：裡面若有較寬的巢狀表格，只在該格內橫向捲動，不撐開整個表格 */
	.form-grid .cell-wrap {
		max-width: 100%;
		overflow-x: auto;
	}
	.form-grid .cell-wrap table { max-width: 100%; }
	.form-grid .cell-wrap input[type="text"],
	.form-grid .cell-wrap select,
	.form-grid .cell-wrap textarea { max-width: 100%; }
	.form-grid .cell-wrap textarea {
		resize: both;
		pointer-events: auto;
	}
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

	{{-- 先把欄位排成「每列剛好 4 欄」的資料，再輸出，避免各列欄數不一致造成跑版 --}}
	@php
		$gridRows = [];
		$curCells = [];
		$used = 0;
		foreach($fields as $item => $row) {
			if($row->type === 'label') {
				if(count($curCells)) { $gridRows[] = ['cells' => $curCells]; $curCells = []; $used = 0; }
				$gridRows[] = ['title' => $row->name];
				continue;
			}
			$hasLabel = !isset($row->noLabel);
			$width = isset($row->width)? (int)$row->width: 4;
			$need = $width > 4? 4: 2;               // 預設寬度的欄位占半列(2 欄)；較寬的占整列(4 欄)
			if($used > 0 && $used + $need > 4) {
				$gridRows[] = ['cells' => $curCells]; $curCells = []; $used = 0;
			}
			$curCells[] = [
				'label' => $hasLabel? $row->name: null,
				'html'  => isset($row->html)? $row->html: '',
				'span'  => $hasLabel? $need - 1: $need,
			];
			$used += $need;
			if($used >= 4) { $gridRows[] = ['cells' => $curCells]; $curCells = []; $used = 0; }
		}
		if(count($curCells)) $gridRows[] = ['cells' => $curCells];
	@endphp

	<div class="row justify-content-center mb-5">
		<div class="col-12 col-md-10">
			<div class="table-responsive">
				<table class="table form-table rwd-form-table form-grid align-middle w-100 mb-0">
					<colgroup>
						<col style="width: 15%;">
						<col style="width: 35%;">
						<col style="width: 15%;">
						<col style="width: 35%;">
					</colgroup>
					<tbody>
					@foreach($gridRows as $gr)
						@if(isset($gr['title']))
							<tr>
								<th colspan="4" class="text-center py-2" style="font-size: 16px;">{{$gr['title']}}</th>
							</tr>
						@else
							@php
								$sum = 0;
								foreach($gr['cells'] as $c) { $sum += ($c['label'] !== null? 1: 0) + $c['span']; }
								$last = count($gr['cells']) - 1;
							@endphp
							<tr>
								@foreach($gr['cells'] as $i => $c)
									@if($c['label'] !== null)
										<td class="grid-label">• {{$c['label']}}</td>
									@endif
									{{-- 該列最後一格往右補滿，不留多餘的空白欄 --}}
									<td colspan="{{ $c['span'] + ($i === $last? 4 - $sum: 0) }}"><div class="cell-wrap">{!! $c['html'] !!}</div></td>
								@endforeach
							</tr>
						@endif
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif
</div>
@endsection
