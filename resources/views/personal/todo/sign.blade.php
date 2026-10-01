@extends('layout.defaultBS5')
@section('content')
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
	#todo_div .card-body .fmlabel {
		background-color: #fcebd7;
		color: #333;
		font-weight: bold;
		padding: 8px 12px;
		border-radius: 4px;
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
		overflow-x: auto;        /* 真的放不下時才在欄位內捲動，不影響整頁 */
		word-break: break-word;
	}
	#todo_fm .fmData img,
	#todo_fm .fmData iframe {
		max-width: 100%;
	}

	/* ===== 區段標題(label) ===== */
	#todo_fm .card-body .labelDiv {
		background-color: #fcebd7 !important;
		color: #333 !important;
		text-align: center !important;
		border-bottom: none !important;
		padding: 10px !important;
		font-weight: bold;
		border-radius: 4px;
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

	/* ===== 計畫資料 (一)~(四)：由上而下排列 ，(五)維持原樣 =====
	   由 JS 為列加上 .stackRow；第一格(編號)靠左，其餘儲存格往下堆疊 */
	#todo_fm .fmData table tr.stackRow {
		display: grid;
		grid-template-columns: 56px minmax(0, 1fr);
		border: 1px solid #dee2e6;
	}
	#todo_fm .fmData table tr.stackRow > td {
		display: block;
		border: none !important;
		grid-column: 2;
		width: auto !important;
		text-align: left;
		padding: 4px 8px !important;
	}
	#todo_fm .fmData table tr.stackRow > td:first-child {
		grid-column: 1;
		grid-row: 1 / span 30;
		background-color: #fff5ea;
		font-weight: bold;
		text-align: center;
		border-right: 1px solid #eeddc8 !important;
	}
	/* 儲存格內若有並排的 span/div，一併改為逐行 */
	#todo_fm .fmData table tr.stackRow > td > span,
	#todo_fm .fmData table tr.stackRow > td > div {
		display: block;
	}
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
						<div class="fmlabel col-md-2 col-sm-6 col-12">•&nbsp;{{trans('personal_todo.flowName')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->flowName}}</div>

						<div class="fmlabel col-md-2 col-sm-6 col-12">•&nbsp;{{trans('personal_todo.formSubj')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->formSubj}}</div>
					</div>
					<div class="row align-items-center">
						<div class="fmlabel col-md-2 col-sm-6 col-12">•&nbsp;{{trans('personal_todo.pointName')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->pointName}}</div>

						<div class="fmlabel col-md-2 col-sm-6 col-12">•&nbsp;{{trans('personal_todo.pointType')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{trans('personal_todo.pointType'. $data->pointType)}}</div>
					</div>
					<div class="row align-items-center">
						<div class="fmlabel col-md-2 col-sm-6 col-12">•&nbsp;{{trans('personal_todo.eformNo')}}</div>
						<div class="col-md-4 col-sm-6 col-12 fmData">{{$data->eformNo}}</div>

						<div class="fmlabel col-md-2 col-sm-6 col-12">•&nbsp;{{trans('personal_todo.pointCreatedAt')}}</div>
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
							<div class="fmlabel col-md-2 col-sm-6 col-12">• {{$row->name}}</div>
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
				<div class="card border-0 shadow">
					<div class="card-header text-white" style="background-color: #336699;">
						<h4 class="mb-0 fw-bold d-flex align-items-center">
							<span class="bi bi-pencil-square me-2"></span>
							{{ ($uRoleType == '4')? "【".trans('personal_todo.agentDesc')."】" : "" }}{{trans('personal_todo.sign')}}
						</h4>
					</div>

					<div class="card-body" style="border: solid #336699 4px; border-top: none; background: #ecf2f9; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">

						<div class="row align-items-center mb-3">
							<div class="fmlabel col-md-2 col-sm-6 col-12 d-flex align-items-center">
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
								<div class="fmlabel col-md-2 col-sm-6 col-12 d-flex align-items-center">
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
							<div class="fmlabel col-md-2 col-sm-6 col-12 d-flex mt-2">
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
			<div class="card card-boss shadow-sm">
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
							<p class='alert alert-warning mb-0' style="background-color: #fdf5e6; border-color: #faebd7; color: #8a6d3b;">{{trans('personal_todo.noSignRecord')}}</p>
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
 *  1. 欄位內含表格 → 該欄位撐滿整列，標籤移到上方，避免表格被擠窄而跑版
 *  2. 計畫資料 (一)~(四) 的列改為由上而下排列，(五) 以後保持原表格
 *  3. 單一跨欄儲存格的列(如「勞健退資料」)視為標題：置中 + 大地色底
 */
(function(){
	var numRe = /^[\s　]*[（(][一二三四][）)]/;

	$("#todo_fm .fmData").has("table").each(function(){
		var $d = $(this);
		$d.removeClass(function(i, c){ return (c.match(/(^|\s)col-(md|sm)-\d+/g) || []).join(" "); })
		  .addClass("col-12");
		var $l = $d.prev(".fmlabel");
		if($l.length) {
			$l.removeClass(function(i, c){ return (c.match(/(^|\s)col-(md|sm)-\d+/g) || []).join(" "); })
			  .addClass("col-12");
		}
	});

	$("#todo_fm .fmData table tr").each(function(){
		var $tr = $(this), $tds = $tr.children("td, th");

		// 計畫資料 (一)~(四)
		if($tds.length > 1 && numRe.test($tds.first().text())) {
			$tr.addClass("stackRow");
			return;
		}
		// 標題列：只有一個跨欄儲存格，且內容是純文字、很短
		if($tds.length === 1 && $tds.first().is("td")
				&& parseInt($tds.first().attr("colspan") || "1", 10) > 1
				&& $.trim($tds.first().text()).length > 0
				&& $.trim($tds.first().text()).length <= 20
				&& $tds.first().find("input, select, textarea, table").length === 0) {
			$tds.first().addClass("tableTitle");
		}
	});
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
