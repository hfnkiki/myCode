@extends('layout.blankBS5')
@section('content')
<!-- flowtracking-view-rev: 2026-10-07-r3 -->
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
	/* ===== 計畫資料 (一)~(五)：由上而下排列(結構由下方 script 重組為 .planBox) ===== */
	.form-grid .planBox {
		width: 100%;
		margin: 4px 0;
		border: 1px solid #dee2e6;
	}
	.form-grid .planBox .planSec {
		display: flex;
		border-bottom: 1px solid #dee2e6;
	}
	.form-grid .planBox .planSec:last-child { border-bottom: none; }
	.form-grid .planBox .planNo {
		flex: 0 0 56px;
		background-color: #fff5ea;
		border-right: 1px solid #eeddc8;
		font-weight: bold;
		text-align: center;
		padding: 8px 4px;
	}
	.form-grid .planBox .planBody {
		flex: 1 1 auto;
		min-width: 0;
		padding: 6px 12px;
	}
	.form-grid .planBox .planItem {
		padding: 3px 0;
		text-align: left;
		overflow-wrap: break-word;
	}
	/* 被拆開的項目：取消原本的並排設定(float/固定寬/inline-block/table-cell) */
	.form-grid .planBox .planSplit,
	.form-grid .planBox .planSplit > * {
		float: none !important;
		display: block !important;
		width: auto !important;
		max-width: 100% !important;
		margin: 0 !important;
		text-align: left !important;
	}
	.form-grid .planBox table { width: 100%; margin: 0; }
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
<script>
// 計畫資料 (一)~(五)：重組為由上而下排列(只調整版面，不改文字內容)
(function(){
	var noRe = /^[\s\u3000]*[（(][一二三四五][）)]/;
	var wrapSel = "div, ul, ol, p, tbody, tr, td, th, table, section";
	var grid = document.querySelector("table.form-grid");
	if(!grid) return;

	function esc(t) { var d = document.createElement("div"); d.textContent = t; return d.innerHTML; }
	function hasContent(n) {
		if(n.nodeType === 3) return n.nodeValue.trim() !== "";
		if(n.nodeType !== 1) return false;
		return n.textContent.trim() !== "" || n.matches("input, img, select, textarea, a");
	}
	// 把一個儲存格裡「並排」的項目拆成陣列(每項一段 html)
	function splitItems(el) {
		var kids = Array.prototype.filter.call(el.childNodes, hasContent);
		if(!kids.length) return [];
		if(kids.length === 1 && kids[0].nodeType === 1 && kids[0].matches(wrapSel)) {
			var o = kids[0];
			if(o.tagName === "TABLE") {
				var out = [];
				Array.prototype.forEach.call(o.querySelectorAll("td, th"), function(c){
					if(c.querySelector("table")) return;
					out = out.concat(splitItems(c));
				});
				return out;
			}
			return splitItems(o);
		}
		if(kids.length === 1) return [el.innerHTML];
		var items = [], buf = "";
		kids.forEach(function(n){
			buf += n.nodeType === 1 ? n.outerHTML : esc(n.nodeValue);
			// 以「：」結尾的是標題，與下一個節點合併成同一項
			if(!/[：:]\s*$/.test(n.textContent || "")) { items.push(buf); buf = ""; }
		});
		if(buf) items.push(buf);
		return items;
	}
	function addItem(body, html, split) {
		var d = document.createElement("div");
		d.className = "planItem" + (split ? " planSplit" : "");
		d.innerHTML = html;
		body.appendChild(d);
	}

	// 「參考比例」欄：小數(0.1)改以百分比(10%)顯示。只改畫面上的文字，不動資料與送出內容；
	// 僅限表頭文字為「參考比例」的那一欄，且內容為 0~1 的純數字時才轉換
	Array.prototype.forEach.call(grid.querySelectorAll(".cell-wrap table"), function(tb){
		var idx = -1;
		Array.prototype.some.call(tb.querySelectorAll("th"), function(th){
			var kids = Array.prototype.slice.call(th.children);
			for(var i = 0; i < kids.length; i++) {
				if(kids[i].textContent.trim() === "參考比例") { idx = i; return true; }
			}
			return false;
		});
		if(idx < 0) return;
		Array.prototype.forEach.call(tb.querySelectorAll("td .row"), function(row){
			var c = row.children[idx];
			if(!c) return;
			var onlyFont = Array.prototype.every.call(c.children, function(e){ return e.tagName === "FONT"; });
			var t = c.textContent.trim();
			if(!onlyFont || !/^(0(\.\d+)?|1(\.0+)?)$/.test(t)) return;
			c.textContent = parseFloat((parseFloat(t) * 100).toFixed(4)) + "%";
		});
	});

	Array.prototype.forEach.call(grid.querySelectorAll(".cell-wrap table"), function(t){
		if(t.parentElement.closest("table") !== grid) return;          // 只處理最外層的巢狀表格
		var found = Array.prototype.some.call(t.rows, function(r){
			return r.cells.length && noRe.test(r.cells[0].textContent);
		});
		if(!found) return;

		var box = document.createElement("div"), body = null, isFive = false;
		box.className = "planBox";
		Array.prototype.forEach.call(t.rows, function(r){
			var cells = Array.prototype.slice.call(r.cells), first = cells[0];
			if(first && noRe.test(first.textContent)) {
				isFive = /五/.test(first.textContent);
				var sec = document.createElement("div"), no = document.createElement("div");
				sec.className = "planSec"; no.className = "planNo";
				no.textContent = first.textContent.trim().replace(/[（）]/g, function(c){ return c === "（" ? "(" : ")"; });
				body = document.createElement("div"); body.className = "planBody";
				sec.appendChild(no); sec.appendChild(body); box.appendChild(sec);
				cells = cells.slice(1);
			}
			if(!body) return;
			cells.forEach(function(c){
				if(c.textContent.trim() === "" && !c.querySelector("input, img, a, select, textarea")) return;
				if(isFive) addItem(body, c.innerHTML, false);                 // (五) 保留原本 html
				else splitItems(c).forEach(function(h){ addItem(body, h, true); });
			});
		});
		t.parentNode.replaceChild(box, t);
	});
})();
</script>
@endsection
