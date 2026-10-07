@extends('layout.defaultBS5')
@section('content')
<!-- sign-view-rev: 2026-10-07-r18 -->
<style>
	#todo_fm .card-body .row {
		margin-top: 10px;
	}
	#todo_fm .card-body .labelDiv {
		margin-top: 15px;
	}
	#todo_fm .manyFiles {
		margin-right: 3px;
		margin-bottom: 5px;
	}
	#todo_div .card-body div {
		margin-top: 10px;
	}
	table td {
		text-align: justify;
	}

	#toSign_btn {
		position: fixed;
		bottom: 100px;
		z-index: 90;
		right: 0px;
		transition: all 0.5s;
		cursor: pointer;
	}

	#signRecordTable pre {
		white-space: pre-wrap;
		white-space: -moz-pre-wrap !important;
		white-space: -pre-wrap;
		white-space: -o-pre-wrap;
		word-wrap: break-word;
	}

	/* ===== 簽核內容(上方卡片)：標籤欄大地色底 ===== */
	/* 標籤/標題的底色用 ::before 畫在 col 內距之內，
	   這樣所有 th 的左右邊界都與欄位(input)對齊，也不會超出外框 */
	#todo_div .card-body .fmlabel,
	#todo_fm .fmlabel,
	#todo_fm .labelDiv,
	#todo_fm .text-primary.border-bottom {
		position: relative;
		min-width: 0;
		overflow-wrap: anywhere;
		word-break: break-word;
		isolation: isolate;
		background: transparent !important;
		color: #333 !important;
		font-weight: bold;
		padding: 8px 24px !important;
		box-sizing: border-box;
	}
	#todo_div .card-body .fmlabel::before,
	#todo_fm .fmlabel::before,
	#todo_fm .labelDiv::before,
	#todo_fm .text-primary.border-bottom::before {
		content: "";
		position: absolute;
		top: 0;
		bottom: 0;
		left: 12px;
		right: 12px;
		background-color: #fff5ea;
		border-radius: 4px;
		z-index: -1;
	}
	#todo_div .card-body .fmData {
		padding: 8px 12px;
		word-break: break-word;
	}

	/* ===== 各卡片等寬、內容不撐破版面 ===== */
	.card-boss,
	.card-boss .card-body,
	#todo_fm .card {
		max-width: 100%;
		box-sizing: border-box;
	}
	#todo_fm .fmData {
		min-width: 0;            /* 讓 flex 子元素可縮小，避免被表格撐寬 */
		word-break: break-word;
	}
	/* 只有含表格的欄位才在內部橫向捲動；其餘維持 visible，
	   否則 textarea 的捲軸與右下角拉伸會被外層 overflow 吃掉 */
	#todo_fm .fmData.hasTable {
		overflow-x: auto;
	}
	#todo_fm .fmData textarea {
		resize: both !important;
		max-width: none;
		overflow: auto !important;
		pointer-events: auto !important;
		user-select: text;
	}
	/* 若外層有全域 pointer-events:none 之類的設定，在欄位區內一律還原 */
	#todo_fm .fmData div,
	#todo_fm .fmData table,
	#todo_fm .fmData tbody,
	#todo_fm .fmData tr,
	#todo_fm .fmData td,
	#todo_fm .fmData th,
	#todo_fm .fmData span {
		pointer-events: auto;
	}
	#todo_fm .fmData img,
	#todo_fm .fmData iframe {
		max-width: 100%;
	}

	#todo_fm .labelDiv,
	#todo_fm .text-primary.border-bottom {
		text-align: center !important;
		border-bottom: none !important;
		margin-bottom: 15px !important;
	}

	/* ===== 附件框線 ===== */
	#todo_fm .fmData a[href*="download"],
	#todo_fm .fmData a.btn-info,
	#todo_fm .fmData a.manyFiles,
	#todo_fm .fmData .manyFiles {
		border: 1px solid #ccc !important;
		border-radius: 4px !important;
		padding: 6px 12px !important;
		background-color: #fff !important;
		display: inline-block;
		margin-right: 8px;
		margin-bottom: 8px;
		color: #333 !important;
		box-shadow: 0 1px 3px rgba(0,0,0,0.05);
		text-decoration: none;
	}
	#todo_fm .fmData a[href*="download"]:hover,
	#todo_fm .fmData a.btn-info:hover,
	#todo_fm .fmData a.manyFiles:hover {
		background-color: #f5f5f5 !important;
	}

	#todo_fm .fmData .label-default {
		background-color: #6c757d !important;
		color: white !important;
		padding: 0.35em 0.65em !important;
		font-size: 0.75em !important;
		font-weight: 700 !important;
		border-radius: 50rem !important;
		display: inline-block;
	}

	/* ===== 表單內的表格 ===== */
	#todo_fm .fmData table {
		width: 100%;
		max-width: 100%;
		border-collapse: collapse;
		margin-top: 10px;
		margin-bottom: 15px;
	}
	#todo_fm .fmData table td {
		border: 1px solid #dee2e6 !important;
		padding: 8px !important;
		word-break: break-word;
	}
	/* th 與「標題列」(勞健退資料等跨欄儲存格)：置中 + 大地色底 */
	#todo_fm .fmData table th,
	#todo_fm .fmData table td.tableTitle {
		background-color: #fff5ea !important;
		color: #333 !important;
		text-align: center !important;
		padding: 10px !important;
		font-weight: bold !important;
		border: 1px solid #eeddc8 !important;
	}

	/* ===== 計畫資料 (一)~(四)：由上而下排列，(五)維持原表格 (結構由 JS 重組為 .planBox) ===== */
	#todo_fm .planBox {
		width: calc(100% - 20px);   /* 與一般表格相同：左右各內縮 10px */
		margin: 10px 0 15px 10px;
		border: 1px solid #dee2e6;
	}
	#todo_fm .planBox .planSec {
		display: flex;
		border-bottom: 1px solid #dee2e6;
	}
	#todo_fm .planBox .planSec:last-child {
		border-bottom: none;
	}
	#todo_fm .planBox .planNo {
		flex: 0 0 56px;
		background-color: #fff5ea;
		border-right: 1px solid #eeddc8;
		font-weight: bold;
		text-align: center;
		padding: 8px 4px;
	}
	#todo_fm .planBox .planBody {
		flex: 1 1 auto;
		min-width: 0;
		padding: 6px 12px;
	}
	#todo_fm .planBox .planItem {
		padding: 3px 0;
		text-align: left;
		word-break: break-word;
	}
	#todo_fm .planBox table {
		margin: 0;
	}
	/* 被拆開的項目：取消原本的並排設定(float/固定寬/inline-block) */
	#todo_fm .planBox .planSplit,
	#todo_fm .planBox .planSplit > * {
		float: none !important;
		display: block !important;
		width: auto !important;
		max-width: 100% !important;
		margin: 0 !important;
		text-align: left !important;
	}

	/* html 內自帶的 .container 會被限制在 1320px，造成比簽核區窄 → 一律撐滿 */
	#todo_fm .fmData .container,
	#todo_fm .fmData .container-sm,
	#todo_fm .fmData .container-md,
	#todo_fm .fmData .container-lg,
	#todo_fm .fmData .container-xl,
	#todo_fm .fmData .container-xxl {
		max-width: 100% !important;
		width: 100% !important;
		padding-left: 0 !important;
		padding-right: 0 !important;
	}

	/* 學歷/經歷等一般表格往內縮 10px，不貼邊 */
	#todo_fm .fmData table {
		margin-left: 10px;
		width: calc(100% - 20px);
	}
	#todo_fm .planBox table {
		margin: 0;
		width: 100%;
	}
	#todo_fm th,
	#signRecordTable th {
		background-color: #fff5ea !important;
		color: #333 !important;
		text-align: center;
		font-weight: bold;
	}

	/* 簽核區塊的標籤不要底色 */
	#todo_fm .fmlabel.signLabel::before { display: none; }
	#todo_fm .fmlabel.signLabel {
		padding: 8px 12px !important;
	}

	/* 「下載」連結：白底方框 + 較大文字，文字為實際檔名 */
	#todo_fm .fmData a.fileLink {
		background-color: #fff !important;
		color: #333 !important;
		border: 1px solid #aaa !important;
		border-radius: 4px !important;
		box-shadow: none;
		padding: 5px 12px !important;
		font-size: 1rem !important;
		font-weight: normal !important;
		display: inline-block;
		text-decoration: none;
	}
	#todo_fm .fmData a.fileLink:hover {
		background-color: #f5f5f5 !important;
	}

	/* 下載欄位在「貼齊卡片邊緣」的區塊(JS 偵測後加 .fileBleed)：
	   標籤加寬為 4 欄、與左邊留 25px，按鈕欄 2 欄 → 一行兩組 */
	#todo_fm .fmlabel.fileBleed {
		margin-left: 25px;
		flex: 0 0 auto;
		width: calc(50% - 25px);
		max-width: calc(50% - 25px);
	}
	@media (min-width: 768px) {
		#todo_fm .fmlabel.fileBleed {
			width: calc(33.33333333% - 25px);
			max-width: calc(33.33333333% - 25px);
		}
	}
	/* 貼邊的嵌套 row：補內距，標籤與區段標題不再貼著左右邊 */
	#todo_fm .nestedRow {
		padding-left: 16px;
		padding-right: 16px;
	}
	/* (五) 金額表：名稱靠左、金額靠右，各身份一致 */
	#todo_fm .planBox table.planFive {
		margin: 0;
		width: 100%;
		table-layout: fixed;
	}
	#todo_fm .fmData table.planFive td {
		border: none !important;
		padding: 1px 12px !important;
		line-height: 1.4;
		text-align: left;
		word-break: break-word;
	}
	#todo_fm .fmData table.planFive td.amt { text-align: right; }
	#todo_fm .fmData table.planFive td:first-child { width: 28%; }
	#todo_fm .planLabel { align-self: flex-start; }
</style>

<div class="row mb-3">
	<div class="col-12">
		<h5 class="mb-0">
			@php($backUrl = (isset($from) && $from === 'batchSign')
					? URL::action('Personal\BatchSignController@index'). '?roleId='. $roleId. '&flowCode='. $data->flowCode. '&pointCode='. $data->pointCode
					: URL::action('Personal\ToDoController@index'))
			<a href="{{ $backUrl }}" class="text-decoration-none">
				{{trans('common.back')}}
			</a>
		</h5>
	</div>
</div>

@if(!empty($data->msg))
<div class="row justify-content-center mb-3">
	<div class="col-12 col-md-6">
		<div class="alert alert-warning text-center fw-bold mb-0" style="font-size:16px;" role="alert">
		  {{isset($data->msg)? $data->msg: ''}}
		</div>
	</div>
</div>
@endif

<div class="container px-0">
	<div id="todo_div" class="row mb-4">
		<div class="col-12">
			<div class="card card-boss shadow-sm">
				<div class="card-header bg-light border-bottom-0">
					<h4 class="mb-0 fw-bold d-flex align-items-center text-secondary">
						<span class="bi bi-list-task me-2"></span>
						{{trans('personal_todo.signContent')}}
					</h4>
				</div>
				<div class="card-body pt-2">
					<div class="row align-items-center">
						<div class="fmlabel col-md-2 col-sm-6 col-12">{{trans('personal_todo.flowName')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->flowName}}</div>

						<div class="fmlabel col-md-2 col-sm-6 col-12">{{trans('personal_todo.formSubj')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->formSubj}}</div>
					</div>
					<div class="row align-items-center">
						<div class="fmlabel col-md-2 col-sm-6 col-12">{{trans('personal_todo.pointName')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->pointName}}</div>

						<div class="fmlabel col-md-2 col-sm-6 col-12">{{trans('personal_todo.pointType')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{trans('personal_todo.pointType'. $data->pointType)}}</div>
					</div>
					<div class="row align-items-center">
						<div class="fmlabel col-md-2 col-sm-6 col-12">{{trans('personal_todo.eformNo')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->eformNo}}</div>

						<div class="fmlabel col-md-2 col-sm-6 col-12">{{trans('personal_todo.pointCreatedAt')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->pointTime}}</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form id="todo_fm" action="{{URL::action('Personal\ToDoController@index')}}" method="post">
		<input type="hidden" name="_token" value="{!!csrf_token()!!}">
		<input type="hidden" name="pointNo" value="{{$data->pointNo}}">
		<input type="hidden" name="timestamp" value="{{$data->timestamp}}">

		<div class="row mb-4">
			<div class="col-12">
				<div class="card card-boss shadow-sm">
					<div class="card-body">
					@php($roWidth = 0)
					@foreach($data->fields as $item => $row)
						@if($row->type === 'label')
							@php($row->noLabel = 1)
							@php($row->width = 12)
						@endif
						@php($label = isset($row->noLabel)? false: true)
						@php($width = isset($row->width)? (int)$row->width: 2)
						@php($width = $label? (($width < 1 || 10 < $width)? 10: $width): (($width < 1 || 12 < $width)? 12: $width))

						@if($roWidth + ($label? 2: 0) + $width > 12 && $roWidth != 0)
							</div>
							@php($roWidth = 0)
						@endif

						@if($roWidth == 0)
							<div class="row align-items-center mb-2">
						@endif

						@if($label)
							@php($roWidth += 2)
							<div class="fmlabel col-md-2 col-sm-6 col-12">{{$row->name}}</div>
						@endif

						@php($roWidth += $width)

						@if($row->type === 'label')
							<div class="col-md-{{$width}} col-12 labelDiv fw-bold text-primary border-bottom pb-1 mb-2" style="font-size:16px;">{{$row->name}}</div>
							@continue
						@endif

						<div class="col-md-{{$width}} col-12 fmData">
						{!! isset($row->html)? $row->html: '' !!}
						</div>
					@endforeach
					<?php echo $roWidth != 0? '</div>': '';?>
					</div>
				</div>
			</div>
		</div>

		<script>
		@if(isset($unit))
		var unitlist = {!! json_encode($unit) !!};
		function selUnit(id) {
			$('#'+id).val('');
			var tar = $('#_show_'+id).val(), ii = 0, row;
			if(tar != '') {
				$('#_sel_'+id).html('').show();
				for(key in unitlist) {
					row = unitlist[key];
					if(ii < 5 && row.name.indexOf(tar) > -1) {
						ii++;
						$('#_sel_'+id).append('<a id="_a'+ii+'_'+id+'" onClick="setUnit(\''+id+'\', $(\'#_b'+ii+'_'+id+'\').val())" class="dropdown-item" style="cursor:pointer;"></a><input type="hidden" id="_b'+ii+'_'+id+'" value="">');
						$('#_a'+ii+'_'+id).text(row.name);
						$('#_b'+ii+'_'+id).val(row.unitNo);
					}
				}
			}
		}
		function setUnit(id, val) {
			$('#_sel_'+id).hide().html('');
			var unit = unitlist[val];
			if(unit == null) {
				$('#'+id).val('');
				$('#_show_'+id).val('');
			}
			else {
				$('#'+id).val(unit.unitNo);
				$('#_show_'+id).val(unit.name);
			}
		}
		$('.selUnit').each(function(){
			var id = $(this).prop('id');
			$(this)
				.before('<input type="text" id="_show_'+id+'" onKeyUp="selUnit(\''+id+'\')" autocomplete="off" class="form-control" style="width:100%">')
				.after('<div id="_sel_'+id+'" class="dropdown-menu shadow-sm" style="position:absolute; z-index:2000; width:95%; display:none;"></div>');
			setUnit(id, $(this).val());
		});
		@endif
		</script>

		<div id="toSign"></div>
		<div class="row mb-4">
			<div class="col-12">
				<div class="card border-0 shadow alignCard">
					<div class="card-header text-white" style="background-color: #336699;">
						<h4 class="mb-0 fw-bold d-flex align-items-center">
							<span class="bi bi-pencil-square me-2"></span>
							{{ ($uRoleType == '4')? "【".trans('personal_todo.agentDesc')."】" : "" }}{{trans('personal_todo.sign')}}
						</h4>
					</div>

					<div class="card-body" style="border: solid #336699 4px; border-top: none; background: #ecf2f9; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">

						<div class="row align-items-center mb-3">
							<div class="fmlabel signLabel col-md-2 col-sm-6 col-12 d-flex align-items-center">
								<span class="bi bi-caret-right-fill text-primary me-1"></span>
								{{trans('personal_todo.sign')}}
							</div>
							<div class="col-md-10 col-sm-6 col-12">
								@php($results = [])
								@if(array_key_exists('fm_pass', $data->results))
									@php($row = $data->results['fm_pass'])
									<div class="form-check form-check-inline mt-2">
										<input class="form-check-input _result" type="radio" name="fm_result" id="fm_pass" value="fm_pass" style="width:1.2em; height:1.2em;" {{isset($row->selected)? 'checked="checked"': ''}}>
										<label class="form-check-label ms-1" for="fm_pass" style="font-size:18px">{{$row->name}}</label>
									</div>
									@php($results['fm_pass'] = ['required' => (isset($row->required)? $row->required: null), 'append' => isset($row->append)? $row->append: null])
								@endif
								@if(array_key_exists('fm_unpass', $data->results))
									@php($row = $data->results['fm_unpass'])
									<div class="form-check form-check-inline mt-2">
										<input class="form-check-input _result" type="radio" name="fm_result" id="fm_unpass" value="fm_unpass" style="width:1.2em; height:1.2em;" {{isset($row->selected)? 'checked="checked"': ''}}>
										<label class="form-check-label ms-1" for="fm_unpass" style="font-size:18px">{{$row->name}}</label>
									</div>
									@php($results['fm_unpass'] = ['required' => (isset($row->required)? $row->required: null), 'append' => isset($row->append)? $row->append: null])
								@endif
								@if(array_key_exists('fm_return', $data->results))
									@php($row = $data->results['fm_return'])
									@if(isset($data->backOption) && sizeof($data->backOption))
										<div class="form-check form-check-inline mt-2">
											<input class="form-check-input _result" type="radio" name="fm_result" id="fm_return" value="fm_return" style="width:1.2em; height:1.2em;" {{isset($row->selected)? 'checked="checked"': ''}}>
											<label class="form-check-label ms-1" for="fm_return" style="font-size:18px">{{$row->name}}</label>
										</div>
										@php($results['fm_return'] = ['required' => (isset($row->required)? $row->required: null), 'append' => isset($row->append)? $row->append: null])
									@else
										<div class="form-check form-check-inline mt-2">
											<input class="form-check-input" type="radio" style="width:1.2em; height:1.2em;" disabled="disabled">
											<label class="form-check-label ms-1 text-muted" style="font-size:18px">{{$row->name}}</label>
										</div>
									@endif
								@endif
							</div>
						</div>

						@if(isset($data->backOption) && sizeof($data->backOption))
							<div id="_backOption" class="row align-items-center mb-3" style="display:none;">
								<div class="fmlabel signLabel col-md-2 col-sm-6 col-12 d-flex align-items-center">
									<span class="bi bi-caret-right-fill text-primary me-1"></span>
									{{trans('personal_todo.backOption')}}
								</div>
								<div class="col-md-4 col-sm-6 col-12">
									<select name="fm_backOption" id="fm_backOption" class="form-select">
									@if(count($data->backOption) < 1)
										<option value="">{{trans('personal_todo.noOption')}}</option>
									@else
										<option value="">-{{trans('common.pleaseSelect')}}-</option>
										@php($val = isset($data->fm_backOption)? $data->fm_backOption: null)
										@foreach($data->backOption as $pointNo => $row)
											<option value="{{$pointNo}}" {{$pointNo === $val? 'selected="selected"': ''}}>{{$row}}</option>
										@endforeach
									@endif
									</select>
								</div>
							</div>
						@endif

						<div class="row align-items-start mt-2">
							<div class="fmlabel signLabel col-md-2 col-sm-6 col-12 d-flex mt-2">
								<span class="bi bi-caret-right-fill text-primary me-1"></span>
								{{trans('personal_todo.signMsg')}}
							</div>
							<div class="col-md-10 col-sm-12 col-12">
								<textarea class="form-control" name="fm_msg" id="fm_msg" rows="3" maxlength="500">{{ isset($data->fm_msg)? $data->fm_msg: '' }}</textarea>
								<div class="text-danger mt-1 fw-bold" style="font-size:14px;">{{trans('personal_todo.signMsgRemark')}}</div>
							</div>
							<div class="col-12 text-center mt-4">
								<input type="button" id="_send" class="btn btn-primary px-5 fw-bold" value="{{trans('personal_todo.send')}}" disabled="disabled">
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="row mb-5">
		<div class="col-12">
			<div class="card card-boss shadow-sm alignCard">
				<div class="card-header bg-light border-bottom-0">
					<h4 class="mb-0 fw-bold d-flex align-items-center text-secondary">
						<span class="bi bi-clock-history me-2"></span>
						{{trans('personal_todo.signRecord')}}
					</h4>
				</div>
				<div class="card-body">
					@if(count($record) > 0)
					<div class="table-responsive">
						<table id="signRecordTable" class="table rwd-table table-hover align-middle mb-0">
							<thead>
								<tr>
									<th>{{trans('personal_todo.pointName')}}</th>
									<th>{{trans('personal_todo.signer')}}</th>
									<th>{{trans('personal_todo.signAt')}}</th>
									<th>{{trans('personal_todo.result')}}</th>
									<th>{{trans('personal_todo.signMsg')}}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($record as $row)
								<tr>
									<td>{{$row->pointName}}</td>
									<td>{{$row->realMemo}}</td>
									<td>{{$row->signAt}}</td>
									<td>{{$row->result}}</td>
									<td><pre class="mb-0 bg-transparent border-0">{!! $row->signMsg !!}</pre></td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@else
						<div class="text-center fw-bold">
							<p class='alert alert-warning mb-0' style="background-color: #fff5ea; border-color: #faebd7; color: #8a6d3b;">{{trans('personal_todo.noSignRecord')}}</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<div id="toSign_btn" title="{{trans('personal_todo.toSign')}}">
	<img src="{{ URL::asset('/images/goSign01.png')}}" alt="" />
</div>

<script>
$(".fmlabel").addClass("col-md-2").addClass("col-sm-6").addClass("col-12");

for(var ii = 1; ii < 5; ii++) {
	$(".fmData.col-md-" + ii).addClass("col-sm-6").addClass("col-12");
}
for(var ii = 5; ii < 13; ii++) {
	$(".fmData.col-md-" + ii).addClass("col-sm-12").addClass("col-12");
}

/*
 * 依表單內容自動調整版面（不同表單的欄位數量/結構不同）：
 *  1. 計畫資料 (一)~(五) 重組為由上而下，並與其他全寬表格同寬
 *  2. 其他含表格的欄位撐滿整列
 *  3. 下載連結改顯示實際檔名，標籤與其他欄位一致
 *  4. 標籤去除項目符號、跨欄標題列置中上色
 *  5. 簽核/簽核紀錄卡片對齊上方表單卡片的寬度
 */
(function(){
	var colRe = /(^|\s)col-(md|sm)-\d+/g;
	function setCol($e, cls) {
		$e.removeClass(function(i, c){ return (c.match(colRe) || []).join(" "); }).addClass(cls);
	}

	// 把一個儲存格裡「並排」的項目拆成陣列(每項一段 html)
	var wrapTags = "div, ul, ol, p, tbody, tr, td, th, table, section, span.row";
	function nodeHtml(n) { return $("<div></div>").append($(n).clone()).html(); }
	function hasContent(n) {
		if(n.nodeType === 3) return $.trim(n.nodeValue) !== "";
		if(n.nodeType !== 1) return false;
		return $.trim($(n).text()) !== "" || $(n).is("input, img, select, textarea, a");
	}
	function splitItems($el) {
		var kids = $el.contents().filter(function(){ return hasContent(this); }).toArray();
		if(kids.length === 0) return [];
		if(kids.length === 1 && kids[0].nodeType === 1 && $(kids[0]).is(wrapTags)) {
			var $o = $(kids[0]);
			if($o.is("table")) {
				var out = [];
				$o.find("td, th").each(function(){
					if($(this).find("table").length) return;
					out = out.concat(splitItems($(this)));
				});
				return out;
			}
			return splitItems($o);
		}
		if(kids.length === 1) return [$el.html()];
		var items = [], buf = "";
		$.each(kids, function(i, n){
			buf += nodeHtml(n);
			// 以「：」結尾的是標題，與下一個節點合併成同一項
			if(!/[：:]\s*$/.test($(n).text() || n.nodeValue || "")) { items.push(buf); buf = ""; }
		});
		if(buf) items.push(buf);
		return items;
	}

	// (五) 金額區：不同身份/表單產生的 html 結構不同(巢狀表格、逐格換行、「名稱：金額」…)，
	// 這裡一律只取出文字，依序重組成固定的「名稱 | 核定金額 | 流用後金額」表格
	function textTokens($el) {
		var out = [];
		$el.find("*").addBack().contents().each(function(){
			if(this.nodeType !== 3) return;
			$.each(this.nodeValue.split(/\n/), function(i, t){
				t = $.trim(t.replace(/ /g, " "));
				if(t) out.push(t);
			});
		});
		return out;
	}
	function buildFiveTable(tokens) {
		var amtRe = /^[\-\d,.]+\s*元?$/, kvRe = /^(.+?)[：:]\s*([\-\d,.]+\s*元?)$/;
		var headers = [], rows = [], cur = null, m;
		$.each(tokens, function(i, t){
			if(t === "核定金額" || t === "流用後金額") { headers.push(t); return; }
			if((m = kvRe.exec(t))) { cur = [m[1], m[2]]; rows.push(cur); return; }
			if(amtRe.test(t)) { if(cur) cur.push(t); return; }
			cur = [t]; rows.push(cur);
		});
		if(rows.length === 0) return null;
		var cols = headers.length + 1;
		$.each(rows, function(i, r){ cols = Math.max(cols, r.length); });
		var $t = $('<table class="planFive"><tbody></tbody></table>'), $b = $t.find("tbody");
		function addRow(cells) {
			var $r = $("<tr></tr>");
			for(var c = 0; c < cols; c++) $r.append($('<td></td>').addClass(c > 0 ? "amt" : "").text(cells[c] || ""));
			$b.append($r);
		}
		if(headers.length) addRow([""].concat(headers));
		$.each(rows, function(i, r){ addRow(r); });
		return $t;
	}

	// ---- 計畫資料表格重組：(一)~(五) 都放進同一個框
	var noRe = /^[\s　]*[（(][一二三四五][）)]/;
	$("#todo_fm .fmData table").each(function(){
		var $t = $(this), found = false;
		if($t.parents("table").length) return;
		$t.find("tr").each(function(){
			var c = $(this).children("td, th").first();
			if(c.length && noRe.test(c.text())) { found = true; return false; }
		});
		if(!found) return;

		var $box = $('<div class="planBox"></div>'), $sec = null, isFive = false;
		var $fiveBody = null, fiveTokens = [], fiveHtml = [];
		$t.find("tr").each(function(){
			var $cells = $(this).children("td, th"), $first = $cells.first();
			if(noRe.test($first.text())) {
				isFive = /五/.test($first.text());
				$sec = $('<div class="planSec"><div class="planNo"></div><div class="planBody"></div></div>');
				$sec.find(".planNo").text($.trim($first.text()).replace(/[（）]/g, function(c){ return c === "（" ? "(" : ")"; }));
				$box.append($sec);
				$cells = $cells.not($first);
			}
			if(!$sec) return;
			$cells.each(function(){
				var $c = $(this);
				if($.trim($c.text()) === "" && $c.find("input, img, a, select, textarea").length === 0) return;
				if(isFive) {
					// (五)：先收集文字，迴圈結束後統一重組成表格
					$fiveBody = $sec.find(".planBody");
					fiveTokens = fiveTokens.concat(textTokens($c));
					fiveHtml.push($c.html());
				}
				else {
					// (一)~(四)：把並排的每一項拆開，各佔一行
					$.each(splitItems($c), function(i, h){
						$sec.find(".planBody").append($('<div class="planItem planSplit"></div>').html(h));
					});
				}
			});
		});
		if($fiveBody) {
			var $ft = buildFiveTable(fiveTokens);
			if($ft) $fiveBody.append($ft);
			else $.each(fiveHtml, function(i, h){ $fiveBody.append($('<div class="planItem"></div>').html(h)); });
		}
		var $d = $t.closest(".fmData");
		$t.replaceWith($box);
		$d.addClass("hasPlan");
		// 與其他全寬表格(如送審資料)同寬：標籤獨立一行在上，內容撐滿整列
		setCol($d, "col-12");
		setCol($d.prev(".fmlabel"), "col-12");
	});

	$("#todo_fm .fmData").has("table").addClass("hasTable");

	// ---- 其餘含表格的欄位：撐滿整列
	$("#todo_fm .fmData").not(".hasPlan").has("table").each(function(){
		setCol($(this), "col-12");
		setCol($(this).prev(".fmlabel"), "col-12");
	});

	// ---- 標籤去掉開頭的項目符號
	$(".fmlabel").each(function(){
		var node = $(this).contents().filter(function(){ return this.nodeType === 3 && $.trim(this.nodeValue) !== ""; }).first();
		if(node.length) node[0].nodeValue = node[0].nodeValue.replace(/^[\s •·]+/, "");
	});

	// ---- 「下載」連結改顯示實際檔名(取 download / title / data-* / href 檔名)
	$("#todo_fm .fmData a").each(function(){
		var $a = $(this);
		if($.trim($a.text()) !== "下載") return;
		var name = $a.attr("download") || $a.attr("data-filename") || $a.attr("data-name") || $a.attr("title");
		if(!name) {
			var href = ($a.attr("href") || "").split("?")[0].split("/").pop();
			try { href = decodeURIComponent(href); } catch(e) {}
			if(/\.[A-Za-z0-9]{2,5}$/.test(href)) name = href;
		}
		if(name) $a.text(name);
		$a.addClass("fileLink");

		// 一行兩組。一般區塊：標籤 2 欄 + 按鈕 4 欄；
		// 貼齊卡片邊緣的區塊(row 沒有內距而外露)：標籤 4 欄 + 按鈕 2 欄，並與左邊留 25px
		var $d = $a.closest(".fmData");
		if($d.find("table").length) return;
		var $l = $d.prev(".fmlabel"), $row = $d.closest(".row"), $card = $d.closest(".card");
		var bleed = $row.length && $card.length
			&& ($row[0].getBoundingClientRect().left + 12 - $card[0].getBoundingClientRect().left) < 8;
		if(bleed) {
			setCol($l, "col-md-4 col-sm-6 col-12");
			setCol($d, "col-md-2 col-sm-6 col-12");
			$l.addClass("fileBleed");
		}
		else {
			setCol($d, "col-md-4 col-sm-6 col-12");
		}
	});

	// ---- 貼齊左右邊緣的 row(如勞健退資料區塊)：左右補 16px 內距，與一般區塊(如受雇者資料)的間距一致。
	//      判定方式(符合任一)：①外層元素的左內距不足以抵消 row 的負邊距 ②row 的內縮後左緣貼著所屬卡片邊框
	//      已判定為貼邊下載欄位(.fileBleed)的 row、簽核區塊不處理
	$("#todo_fm .row").not(".alignCard .row").not(":has(.fileBleed)").each(function(){
		var p = this.parentElement, $card = $(this).closest(".card");
		if(!p) return;
		var pl = parseFloat($(p).css("paddingLeft")) || 0, ml = parseFloat($(this).css("marginLeft")) || 0;
		var bleed = ml < 0 && pl < -ml;
		if(!bleed && $card.length) {
			var cl = $card[0].getBoundingClientRect().left + (parseFloat($card.css("borderLeftWidth")) || 0);
			bleed = (this.getBoundingClientRect().left - ml - cl) < 8;   // row 內容區左緣(扣掉負邊距)離卡片邊框的距離
		}
		if(bleed) $(this).addClass("nestedRow");
	});

	// ---- 標題列(單一跨欄儲存格)
	$("#todo_fm .fmData table tr").each(function(){
		var $tds = $(this).children("td, th"), $c = $tds.first(), t = $.trim($c.text());
		if($tds.length === 1 && $c.is("td") && parseInt($c.attr("colspan") || "1", 10) > 1
				&& t.length > 0 && t.length <= 20 && $c.find("input, select, textarea, table").length === 0) {
			$c.addClass("tableTitle");
		}
	});

	// ---- disabled 的 textarea 瀏覽器不允許拖曳捲軸/縮放
	//      → 換成 readonly 的複本(拿掉 name，與 disabled 一樣不會被送出)
	$("#todo_fm .fmData textarea:disabled").each(function(){
		var $o = $(this), v = $o.val();
		var $n = $o.clone().prop("disabled", false).removeAttr("disabled").removeAttr("name")
			.prop("readonly", true).val(v);
		$o.replaceWith($n);
	});

	// ---- 簽核/簽核紀錄卡片寬度對齊上方表單卡片
	function alignCards() {
		var ref = $("#todo_fm .card-boss").first()[0];
		if(!ref) return;
		var $cards = $(".alignCard").css({marginLeft: "", width: "", maxWidth: ""});
		var r = ref.getBoundingClientRect();
		$cards.each(function(){
			var t = this.getBoundingClientRect(), ml = parseFloat($(this).css("marginLeft")) || 0;
			$(this).css({marginLeft: (ml + r.left - t.left) + "px", width: r.width + "px", maxWidth: "none"});
		});
	}
	alignCards();
	$(window).on("load resize", alignCards);
})();

// 簽核結果變更邏輯
$("._result").change(function() {
	var rs = {!! json_encode($results)  !!}[$("input[name=fm_result]:checked").val()];
	if(rs == null)
		return;

	$(".required").removeClass("required");
	if(rs.required != null) {
		rs.required.forEach(function(row){
			$("#"+ row).addClass("required");
		});
	}
	$(".selUnit").each(function(){
		if($(this).hasClass("required") && $(this).val() == "") {
			$(this).removeClass("required");
			$("#_show_"+$(this).prop("id")).val("").addClass("required");
		}
	});

	if(rs.append == "sendBack") {
		$('#_backOption').fadeIn();
		$("#fm_backOption").addClass("required");
	}
	else {
		$('#_backOption').fadeOut();
	}

	$("#_send").prop('disabled', false);
});
$("._result").first().change();

// 表單送出
$("#_send").click(function(){
	$("#todo_fm").validate();
	$("#todo_fm").submit();
});

// 直達簽核意見區塊滾動效果
$('#toSign_btn').click(function(){
	$('html,body').animate({scrollTop:$('#toSign').offset().top}, 400);
});
</script>

@endsection
