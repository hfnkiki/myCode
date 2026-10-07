@extends('layout.defaultBS5')
@section("content")
<!-- preview-view-rev: 2026-10-07-r7 -->
<style>
	#prev_fm .card-body .row {
		margin-top: 10px;
	}
	#prev_fm .card-body .labelDiv {
		margin-top: 15px;
	}
	#prev_fm .manyFiles {
		margin-right: 3px;
		margin-bottom: 5px;
	}

	/* ===== 簽核內容(上方卡片)：標籤欄大地色底 ===== */
	/* 標籤/標題的底色用 ::before 畫在 col 內距之內，
	   這樣所有 th 的左右邊界都與欄位(input)對齊，也不會超出外框 */
	
	#prev_fm .fmlabel,
	#prev_fm .labelDiv,
	#prev_fm .text-primary.border-bottom {
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
	
	#prev_fm .fmlabel::before,
	#prev_fm .labelDiv::before,
	#prev_fm .text-primary.border-bottom::before {
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

	/* ===== 各卡片等寬、內容不撐破版面 ===== */
	#prev_fm .card-boss,
	#prev_fm .card-boss .card-body {
		max-width: 100%;
		box-sizing: border-box;
	}
	#prev_fm .fmData {
		min-width: 0;            /* 讓 flex 子元素可縮小，避免被表格撐寬 */
		overflow-wrap: break-word;
	}
	/* 只有含表格的欄位才在內部橫向捲動；其餘維持 visible，
	   否則 textarea 的捲軸與右下角拉伸會被外層 overflow 吃掉 */
	#prev_fm .fmData.hasTable {
		overflow-x: auto;
	}
	#prev_fm .fmData textarea {
		resize: both !important;
		max-width: none;
		overflow: auto !important;
		pointer-events: auto !important;
		user-select: text;
	}
	/* 若外層有全域 pointer-events:none 之類的設定，在欄位區內一律還原 */
	#prev_fm .fmData div,
	#prev_fm .fmData table,
	#prev_fm .fmData tbody,
	#prev_fm .fmData tr,
	#prev_fm .fmData td,
	#prev_fm .fmData th,
	#prev_fm .fmData span {
		pointer-events: auto;
	}
	#prev_fm .fmData img,
	#prev_fm .fmData iframe {
		max-width: 100%;
	}

	#prev_fm .labelDiv,
	#prev_fm .text-primary.border-bottom {
		text-align: center !important;
		border-bottom: none !important;
		margin-bottom: 15px !important;
	}

	/* ===== 附件框線 ===== */
	#prev_fm .fmData a[href*="download"],
	#prev_fm .fmData a.btn-info,
	#prev_fm .fmData a.manyFiles,
	#prev_fm .fmData .manyFiles {
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
	#prev_fm .fmData a[href*="download"]:hover,
	#prev_fm .fmData a.btn-info:hover,
	#prev_fm .fmData a.manyFiles:hover {
		background-color: #f5f5f5 !important;
	}

	#prev_fm .fmData .label-default {
		background-color: #6c757d !important;
		color: white !important;
		padding: 0.35em 0.65em !important;
		font-size: 0.75em !important;
		font-weight: 700 !important;
		border-radius: 50rem !important;
		display: inline-block;
	}

	/* ===== 表單內的表格 ===== */
	#prev_fm .fmData table {
		width: 100%;
		max-width: 100%;
		border-collapse: collapse;
		margin-top: 10px;
		margin-bottom: 15px;
	}
	#prev_fm .fmData table td {
		border: 1px solid #dee2e6 !important;
		padding: 8px !important;
		overflow-wrap: break-word;
	}
	/* th 與「標題列」(勞健退資料等跨欄儲存格)：置中 + 大地色底 */
	#prev_fm .fmData table th,
	#prev_fm .fmData table td.tableTitle {
		background-color: #fff5ea !important;
		color: #333 !important;
		text-align: center !important;
		padding: 10px !important;
		font-weight: bold !important;
		border: 1px solid #eeddc8 !important;
	}

	/* ===== 計畫資料 (一)~(四)：由上而下排列，(五)維持原表格 (結構由 JS 重組為 .planBox) ===== */
	#prev_fm .planBox {
		width: calc(100% - 20px);   /* 與一般表格相同：左右各內縮 10px */
		margin: 10px 0 15px 10px;
		border: 1px solid #dee2e6;
	}
	#prev_fm .planBox .planSec {
		display: flex;
		border-bottom: 1px solid #dee2e6;
	}
	#prev_fm .planBox .planSec:last-child {
		border-bottom: none;
	}
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
		overflow-wrap: break-word;
	}
	#prev_fm .planBox table {
		margin: 0;
	}
	/* 被拆開的項目：取消原本的並排設定(float/固定寬/inline-block) */
	#prev_fm .planBox .planSplit,
	#prev_fm .planBox .planSplit > * {
		float: none !important;
		display: block !important;
		width: auto !important;
		max-width: 100% !important;
		margin: 0 !important;
		text-align: left !important;
	}

	/* html 內自帶的 .container 會被限制在 1320px，造成比簽核區窄 → 一律撐滿 */
	#prev_fm .fmData .container,
	#prev_fm .fmData .container-sm,
	#prev_fm .fmData .container-md,
	#prev_fm .fmData .container-lg,
	#prev_fm .fmData .container-xl,
	#prev_fm .fmData .container-xxl {
		max-width: 100% !important;
		width: 100% !important;
		padding-left: 0 !important;
		padding-right: 0 !important;
	}

	/* 學歷/經歷等一般表格往內縮 10px，不貼邊 */
	#prev_fm .fmData table {
		margin-left: 10px;
		width: calc(100% - 20px);
	}
	#prev_fm .planBox table {
		margin: 0;
		width: 100%;
	}
	
	#prev_fm th {
		background-color: #fff5ea !important;
		color: #333 !important;
		text-align: center;
		font-weight: bold;
	}

	/* 「下載」連結：白底方框 + 較大文字，文字為實際檔名 */
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
	#prev_fm .fmData a.fileLink:hover {
		background-color: #f5f5f5 !important;
	}

	/* 下載欄位在「貼齊卡片邊緣」的區塊(JS 偵測後加 .fileBleed)：
	   標籤加寬為 4 欄、與左邊留 25px，按鈕欄 2 欄 → 一行兩組 */
	#prev_fm .fmlabel.fileBleed {
		margin-left: 25px;
		flex: 0 0 auto;
		width: calc(50% - 25px);
		max-width: calc(50% - 25px);
	}
	@media (min-width: 768px) {
		#prev_fm .fmlabel.fileBleed {
			width: calc(33.33333333% - 25px);
			max-width: calc(33.33333333% - 25px);
		}
	}
	/* 貼邊的嵌套 row：補內距，標籤與區段標題不再貼著左右邊 */
	#prev_fm .nestedRow {
		padding-left: 16px;
		padding-right: 16px;
	}
	/* 後端產生的欄位 html 使用 Bootstrap 3 的 col-xs-* 格線，Bootstrap 5 沒有這組 class，
	   欄位會變成各佔一整行而疊在一起 → 在欄位區內補上等效的寬度 (如：分配項目/金額/參考比例 表) */
	#prev_fm .fmData [class*="col-xs-"] {
		float: left;
		flex: 0 0 auto;
		max-width: 100%;
		padding-left: 12px;
		padding-right: 12px;
	}
	#prev_fm .fmData .col-xs-1 { width: 8.33333333%; }
	#prev_fm .fmData .col-xs-2 { width: 16.66666667%; }
	#prev_fm .fmData .col-xs-3 { width: 25.00000000%; }
	#prev_fm .fmData .col-xs-4 { width: 33.33333333%; }
	#prev_fm .fmData .col-xs-5 { width: 41.66666667%; }
	#prev_fm .fmData .col-xs-6 { width: 50.00000000%; }
	#prev_fm .fmData .col-xs-7 { width: 58.33333333%; }
	#prev_fm .fmData .col-xs-8 { width: 66.66666667%; }
	#prev_fm .fmData .col-xs-9 { width: 75.00000000%; }
	#prev_fm .fmData .col-xs-10 { width: 83.33333333%; }
	#prev_fm .fmData .col-xs-11 { width: 91.66666667%; }
	#prev_fm .fmData .col-xs-12 { width: 100.00000000%; }
	/* 表格內的 row：不要上方間距(內容列緊密排列)；含 col-xs 表頭的 th 內距與 td 相同 */
	#prev_fm .fmData table .row { margin-top: 0; }
	#prev_fm .fmData table th.colHead { padding: 8px !important; }
	#prev_fm .planLabel { align-self: flex-start; }
	#prev_fm .fmData pre { margin: 0; white-space: pre-wrap; }
</style>

<pre id="msg" style="color:red;text-align:center;{{isset($msg)? '': 'display:none;'}}">{{isset($msg)? $msg: ''}}</pre>

@if(isset($fields))
<div id="prev_fm">
	<div class="card card-boss shadow-sm">
		<div class="card-body">
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
		<div class="row align-items-center mb-2">
	@endif
	@if($label)
		@php($roWidth += 2)
		<div class="fmlabel col-md-2 col-sm-6 col-12">{{$row->name}}<br>{{$item}}</div>
	@endif
	@php($roWidth += $width)
	@if($row->type === 'label')
		<div class="col-md-{{$width}} col-12 labelDiv fw-bold text-primary border-bottom pb-1 mb-2" style="font-size:16px;">{{$row->name}}</div>
		@continue
	@endif
	<div class="col-md-{{$width}} col-12 fmData">
		@if(isset($row->html))
			{!! $row->html !!}
		@endif
	</div>
@endforeach
<?php echo $roWidth != 0? '</div>': '';?>

			<div class="row align-items-center mb-2">
				<div class="col-12 labelDiv fw-bold text-primary border-bottom pb-1 mb-2" style="font-size:16px;">隱藏欄位</div>
			</div>
	@foreach($hidden as $item => $row)
			<div class="row align-items-center mb-2">
				<div class="fmlabel col-md-2 col-sm-6 col-12">{{$item}}</div>
				<div class="col-md-10 col-12 fmData"><pre>{{$row}}</pre></div>
			</div>
	@endforeach
		</div>
	</div>
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
 *  5. 整個表單區塊與頁面上方標題線(hr)同寬
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
					// (五)：保留原本 html 不改動(版面由 col-xs-* 對應 CSS 處理)
					$sec.find(".planBody").append($('<div class="planItem"></div>').html($c.html()));
				}
				else {
					// (一)~(四)：把並排的每一項拆開，各佔一行
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
		setCol($d, "col-12");
		setCol($d.prev(".fmlabel"), "col-12");
	});

	$("#prev_fm .fmData").has("table").addClass("hasTable");

	// ---- 其餘含表格的欄位：撐滿整列
	$("#prev_fm .fmData").not(".hasPlan").has("table").each(function(){
		setCol($(this), "col-12");
		setCol($(this).prev(".fmlabel"), "col-12");
	});

	// ---- 標籤去掉開頭的項目符號
	$(".fmlabel").each(function(){
		var node = $(this).contents().filter(function(){ return this.nodeType === 3 && $.trim(this.nodeValue) !== ""; }).first();
		if(node.length) node[0].nodeValue = node[0].nodeValue.replace(/^[\s •·]+/, "");
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
	//      已判定為貼邊下載欄位(.fileBleed)的 row
	$("#prev_fm .row").not("table .row").not(":has(.fileBleed)").each(function(){
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

	// ---- 表頭 th 內直接放 col-xs-* 欄位(如分配項目/分配金額/參考比例)：包進 row，
	//      與下方內容列的負邊距/內距一致，標題才會與內容對齊(只調整版面，不動內容)
	$("#prev_fm .fmData table th").each(function(){
		var $th = $(this);
		if($th.children('[class*="col-xs-"]').length && !$th.children(".row").length) {
			$th.addClass("colHead").wrapInner('<div class="row hdrRow"></div>');
		}
	});

	// ---- 「參考比例」欄：後端有時輸出小數(0.1)、有時輸出百分比(10%)；小數一律改以百分比顯示。
	//      只改顯示文字，且僅限表頭為「參考比例」的那一欄、內容為 0~1 的純數字時才轉換
	$("#prev_fm .fmData table").each(function(){
		var $tb = $(this), idx = -1;
		$tb.find("th .hdrRow").first().children().each(function(i){
			if($.trim($(this).text()) === "參考比例") { idx = i; return false; }
		});
		if(idx < 0) return;
		$tb.find("td .row").each(function(){
			var $c = $(this).children().eq(idx), t = $.trim($c.text());
			if($c.children().not("font").length || !/^(0(\.\d+)?|1(\.0+)?)$/.test(t)) return;
			$c.text(parseFloat((parseFloat(t) * 100).toFixed(4)) + "%");
		});
	});

	// ---- 標題列(單一跨欄儲存格)
	$("#prev_fm .fmData table tr").each(function(){
		var $tds = $(this).children("td, th"), $c = $tds.first(), t = $.trim($c.text());
		if($tds.length === 1 && $c.is("td") && parseInt($c.attr("colspan") || "1", 10) > 1
				&& t.length > 0 && t.length <= 20 && $c.find("input, select, textarea, table").length === 0) {
			$c.addClass("tableTitle");
		}
	});

	// ---- disabled 的 textarea 瀏覽器不允許拖曳捲軸/縮放
	//      → 換成 readonly 的複本(拿掉 name，與 disabled 一樣不會被送出)
	$("#prev_fm .fmData textarea:disabled").each(function(){
		var $o = $(this), v = $o.val();
		var $n = $o.clone().prop("disabled", false).removeAttr("disabled").removeAttr("name")
			.prop("readonly", true).val(v);
		$o.replaceWith($n);
	});

	// ---- 整個表單區塊與頁面上方「檢視表單內容」標題線(hr)同寬同位置
	function alignPanel() {
		var $c = $("#prev_fm .card-boss").first(), $hr = $("hr").not($("#prev_fm").find("hr")).first();
		if(!$c.length || !$hr.length) return;
		$c.css({width: "", maxWidth: "", marginLeft: "", marginRight: ""});
		var r = $hr[0].getBoundingClientRect(), t = $c[0].getBoundingClientRect();
		var ml = parseFloat($c.css("marginLeft")) || 0;
		$c.css({maxWidth: "none", width: r.width + "px", marginLeft: (ml + r.left - t.left) + "px"});
	}
	alignPanel();
	$(window).on("load resize", alignPanel);
})();

</script>
@endif
@endsection
