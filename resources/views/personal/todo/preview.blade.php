@extends('layout.default')
@section("content")
<!-- preview-view-rev: 2026-10-01-r3 -->
<style>
	/* 本頁為 Bootstrap 3 版型(panel / col-xs-*)：沿用簽核頁(sign)的樣式，gutter 為 15px */
	#prev_fm .row {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		margin-top: 10px;
	}
	#prev_fm .row:before,
	#prev_fm .row:after { display: none; }   /* BS3 clearfix 在 flex 下會變成多餘的欄位 */

	/* ===== th：標籤/區段標題 大地色底、粗體、無項目符號 =====
	   底色用 ::before 畫在欄位內距之內，所有 th 左右邊界與欄位對齊，也不會超出外框 */
	#prev_fm .fmlabel,
	#prev_fm .panel-divide {
		position: relative;
		min-width: 0;
		overflow-wrap: anywhere;   /* 長英文欄位名(如 ps1HolderBossUnitType)換行，不超出 th */
		word-break: break-word;
		isolation: isolate;
		background: transparent !important;
		color: #333 !important;
		font-weight: bold;
		padding: 8px 30px !important;
		box-sizing: border-box;
	}
	#prev_fm .fmlabel::before,
	#prev_fm .panel-divide::before {
		content: "";
		position: absolute;
		top: 0;
		bottom: 0;
		left: 15px;
		right: 15px;
		background-color: #fcebd7;
		border-radius: 4px;
		z-index: -1;
	}
	#prev_fm .panel-divide {
		float: none;
		width: 100%;
		text-align: center !important;
		border-bottom: none !important;
		margin: 15px 0 !important;
	}

	/* ===== 欄位不撐破版面；含表格的欄位才在內部橫向捲動 ===== */
	#prev_fm .fmData {
		min-width: 0;
		word-break: break-word;
	}
	#prev_fm .fmData.hasTable { overflow-x: auto; }
	#prev_fm .fmData img,
	#prev_fm .fmData iframe { max-width: 100%; }
	#prev_fm .fmData textarea {
		resize: both !important;
		max-width: none;
		overflow: auto !important;
		pointer-events: auto !important;
		user-select: text;
	}
	#prev_fm .fmData div,
	#prev_fm .fmData table,
	#prev_fm .fmData tbody,
	#prev_fm .fmData tr,
	#prev_fm .fmData td,
	#prev_fm .fmData th,
	#prev_fm .fmData span { pointer-events: auto; }

	/* ===== 附件 ===== */
	#prev_fm .fmData a[href*="download"],
	#prev_fm .fmData a.btn-info,
	#prev_fm .fmData a.manyFiles,
	#prev_fm .fmData .manyFiles {
		border: 1px solid #ccc !important;
		border-radius: 4px !important;
		padding: 6px 12px !important;
		background-color: #fff !important;
		display: inline-block;
		margin: 0 8px 8px 0;
		color: #333 !important;
		box-shadow: 0 1px 3px rgba(0,0,0,0.05);
		text-decoration: none;
	}
	#prev_fm .fmData a.fileLink {
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
	#prev_fm .fmData a.fileLink:hover { background-color: #f5f5f5 !important; }
	#prev_fm .fmData .label-default {
		background-color: #6c757d !important;
		color: #fff !important;
		padding: 0.35em 0.65em !important;
		font-size: 0.75em !important;
		font-weight: 700 !important;
		border-radius: 50rem !important;
		display: inline-block;
	}
	/* 下載欄位在「貼齊面板邊緣」的區塊(JS 偵測後加 .fileBleed)：標籤 4 欄、左邊留 25px，按鈕 2 欄 */
	#prev_fm .fmlabel.fileBleed {
		margin-left: 25px;
		flex: 0 0 auto;
		width: calc(50% - 25px);
		max-width: calc(50% - 25px);
	}
	@media (min-width: 992px) {
		#prev_fm .fmlabel.fileBleed {
			width: calc(33.33333333% - 25px);
			max-width: calc(33.33333333% - 25px);
		}
	}

	/* ===== 表單內的表格 ===== */
	#prev_fm .fmData table {
		width: calc(100% - 20px);
		max-width: 100%;
		margin: 10px 0 15px 10px;
		border-collapse: collapse;
	}
	#prev_fm .fmData table td {
		border: 1px solid #dee2e6 !important;
		padding: 8px !important;
		word-break: break-word;
		text-align: justify;
	}
	#prev_fm th,
	#prev_fm .fmData table td.tableTitle {
		background-color: #fff5ea !important;
		color: #333 !important;
		text-align: center !important;
		padding: 10px !important;
		font-weight: bold !important;
		border: 1px solid #eeddc8 !important;
	}

	/* ===== 計畫資料 (一)~(五)：由上而下排列(結構由 JS 重組為 .planBox) ===== */
	#prev_fm .planBox {
		width: calc(100% - 20px);   /* 與一般表格相同：左右各內縮 10px */
		margin: 10px 0 15px 10px;
		border: 1px solid #dee2e6;
	}
	#prev_fm .planBox .planSec {
		display: flex;
		border-bottom: 1px solid #dee2e6;
	}
	#prev_fm .planBox .planSec:last-child { border-bottom: none; }
	#prev_fm .planBox .planNo {
		flex: 0 0 56px;
		background-color: #fff5ea;
		border-right: 1px solid #eeddc8;
		font-weight: bold;
		text-align: center;
		padding: 8px 4px;
	}
	#prev_fm .planBox .planBody {
		flex: 1 1 auto;
		min-width: 0;
		padding: 6px 12px;
	}
	#prev_fm .planBox .planItem {
		padding: 3px 0;
		text-align: left;
		word-break: break-word;
	}
	#prev_fm .planBox table { margin: 0; width: 100%; }
	#prev_fm .planBox .planSplit,
	#prev_fm .planBox .planSplit > * {
		float: none !important;
		display: block !important;
		width: auto !important;
		max-width: 100% !important;
		margin: 0 !important;
		text-align: left !important;
	}
	#prev_fm .planLabel { align-self: flex-start; }

	/* 欄位 html 內自帶的 .container 限寬 → 撐滿 */
	#prev_fm .fmData .container {
		max-width: 100% !important;
		width: 100% !important;
		padding-left: 0 !important;
		padding-right: 0 !important;
	}
</style>

<pre id="msg" style="color:red;text-align:center;{{isset($msg)? '': 'display:none;'}}">{{isset($msg)? $msg: ''}}</pre>

@if(isset($fields))
<div class="container" id="prev_fm">
<div class="row">
	<div class="panel panel-boss">
		<div class="panel-body">
@php($roWidth = 0)
@foreach($fields as $item => $row)
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
		<div class="row">
	@endif
	@if($label)
		@php($roWidth += 2)
		<div class="fmlabel">{{$row->name}}<br>{{$item}}</div>
	@endif
	@php($roWidth += $width)
	@if($row->type === 'label')
		<div class="col-md-{{$width}} panel-divide" style="font-size:15px;font-weight: bold;">{{$row->name}}</div>
		@continue
	@endif
	<div class="col-md-{{$width}} fmData">
		@if(isset($row->html))
			{!! $row->html !!}
		@endif
	</div>
@endforeach
<?php echo $roWidth != 0? '</div>': '';?>

	<div class="row">
		<div class="col-md-12 panel-divide" style="font-size:15px;font-weight: bold;">隱藏欄位</div>
	</div>
	@foreach($hidden as $item => $row)
	<div class="row">
		<div class="fmlabel">{{$item}}</div>
		<div class="col-md-10 fmData"><pre>{{$row}}</pre></div>
	</div>
	@endforeach
		</div>
	</div>
</div>
</div>
<script>
//CSS
$(".fmlabel").addClass("col-md-2").addClass("col-sm-6").addClass("col-xs-12");
for(ii = 1; ii < 5; ii++)
	$(".fmData.col-md-" + ii).addClass("col-sm-6").addClass("col-xs-12");
for(ii = 5; ii < 13; ii++)
	$(".fmData.col-md-" + ii).addClass("col-sm-12").addClass("col-xs-12");

/*
 * 依表單內容自動調整版面(比照簽核頁)：
 *  1. 計畫資料 (一)~(五) 重組為由上而下，標籤在左、內容在右
 *  2. 其他含表格的欄位撐滿整列
 *  3. 「下載」連結改顯示實際檔名；貼齊面板邊緣的下載欄位標籤加寬並與左邊留 25px
 *  4. 跨欄的單一儲存格列視為標題：置中 + 大地色底
 *  5. disabled 的 textarea 換成 readonly 複本，捲軸與縮放才能操作
 */
(function(){
	var colRe = /(^|\s)col-(md|sm|xs)-\d+/g;
	function setCol($e, cls) {
		$e.removeClass(function(i, c){ return (c.match(colRe) || []).join(" "); }).addClass(cls);
	}

	// 把一個儲存格裡「並排」的項目拆成陣列(每項一段 html)
	var wrapTags = "div, ul, ol, p, tbody, tr, td, th, table, section";
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

	// ---- 計畫資料表格重組：(一)~(五) 都放進同一個框
	var noRe = /^[\s　]*[（(][一二三四五][）)]/;
	$("#prev_fm .fmData table").each(function(){
		var $t = $(this), found = false;
		if($t.parents("table").length) return;
		$t.find("tr").each(function(){
			var c = $(this).children("td, th").first();
			if(c.length && noRe.test(c.text())) { found = true; return false; }
		});
		if(!found) return;

		var $box = $('<div class="planBox"></div>'), $sec = null, isFive = false;
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
					$sec.find(".planBody").append($('<div class="planItem"></div>').html($c.html()));
				}
				else {
					$.each(splitItems($c), function(i, h){
						$sec.find(".planBody").append($('<div class="planItem planSplit"></div>').html(h));
					});
				}
			});
		});
		var $d = $t.closest(".fmData");
		$t.replaceWith($box);
		$d.addClass("hasPlan");
		// 與其他全寬表格(如送審資料)同寬：標籤獨立一行在上，內容撐滿整列
		setCol($d, "col-xs-12");
		setCol($d.prev(".fmlabel"), "col-xs-12");
	});

	$("#prev_fm .fmData").has("table").addClass("hasTable");

	// ---- 其餘含表格的欄位：撐滿整列
	$("#prev_fm .fmData").not(".hasPlan").has("table").each(function(){
		setCol($(this), "col-xs-12");
		setCol($(this).prev(".fmlabel"), "col-xs-12");
	});

	// ---- 「下載」連結改顯示實際檔名(取 download / title / data-* / href 檔名)
	$("#prev_fm .fmData a").each(function(){
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
		// 貼齊面板邊緣的區塊：標籤 4 欄 + 按鈕 2 欄，並與左邊留 25px
		var $d = $a.closest(".fmData");
		if($d.find("table").length) return;
		var $l = $d.prev(".fmlabel"), $row = $d.closest(".row"), $panel = $d.closest(".panel");
		var bleed = $row.length && $panel.length
			&& ($row[0].getBoundingClientRect().left + 15 - $panel[0].getBoundingClientRect().left) < 8;
		if(bleed) {
			setCol($l, "col-md-4 col-sm-6 col-xs-12");
			setCol($d, "col-md-2 col-sm-6 col-xs-12");
			$l.addClass("fileBleed");
		}
		else {
			setCol($d, "col-md-4 col-sm-6 col-xs-12");
		}
	});

	// ---- 標題列(單一跨欄儲存格)
	$("#prev_fm .fmData table tr").each(function(){
		var $tds = $(this).children("td, th"), $c = $tds.first(), t = $.trim($c.text());
		if($tds.length === 1 && $c.is("td") && parseInt($c.attr("colspan") || "1", 10) > 1
				&& t.length > 0 && t.length <= 20 && $c.find("input, select, textarea, table").length === 0) {
			$c.addClass("tableTitle");
		}
	});

	// ---- disabled 的 textarea → readonly 複本(拿掉 name，與 disabled 一樣不會被送出)
	$("#prev_fm .fmData textarea:disabled").each(function(){
		var $o = $(this), v = $o.val();
		var $n = $o.clone().prop("disabled", false).removeAttr("disabled").removeAttr("name")
			.prop("readonly", true).val(v);
		$o.replaceWith($n);
	});
})();
</script>
@endif
@endsection
