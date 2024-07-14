
function addItem() {
	clearMessages();
	$.ajax({
		url: '/modit/ajax/showPageProperties/leasing',
		data: {'id': 0},
		success: function(data,textStatus,jqXHR) {
			try {
				var obj = eval('('+data+')');
				if (obj.status == 'true') {
					removeTinyMCE($('#popup textarea'));
					showPopup(obj.html);
					addTinyMCE($('#popup textarea'));
					if (obj.code && obj.code.length > 0) eval(obj.code);
				}
			}
			catch(err) {
				showError(err.message);
			}
		}
	});
}

function moveFolder() {
	id = moveIt('lease_folders',this);
	setTimeout(function() {
		loadTree('leasing',id);
		makeDroppable();
	},100);
}

$(document).ready(function() {
	var dropped = false;
	html = getContent('header',null,$('#header > div.inner')[0]);
	html = getContent('mainNav',null,$('#mainNav > div.inner')[0]);
	loadTree('leasing');
	initTinyMCE();
});


function getInfo(lnk) {
	clearMessages();
	$('#contentTree a.active').each(function (idx,el) {
		el.className = el.className.replace('active','');
	});
	$('#contentTree a#'.concat(lnk)).each(function (idx,el) {
		el.className = el.className.concat(' active');
	});
	var id = lnk.split("_");
	$.ajax({
		url: '/modit/ajax/getFolderInfo/leasing',
		data: {p_id: id[1]},
		success: function(data,textStatus,jqXHR) {
			try {
				var obj = eval('('+data+')');
				if (obj.status == 'true') {
					$('#pageInfo')[0].innerHTML = obj.html;
					
				}
				if (obj.code && obj.code.length > 0) eval(obj.code);
			} catch(err) {
				var x = 0;
			}
		}
	});
	loadContent(id[1]);
}

function editContent(cId) {
	$.ajax({
		url: '/modit/ajax/showPageProperties/leasing',
		data: {id : cId},
		method: 'post',
		success: function(data,textStatus,jqXHR) {
			try {
				var obj = eval('('+data+')');
				if (obj.status == 'true') {
					removeTinyMCE($('#popup textarea'));
					showPopup(obj.html);
					addTinyMCE($('#popup textarea'));
				}
				if (obj.messages && obj.messages.length > 0) showPopupError(obj.messages);
				if (obj.code && obj.code.length > 0) eval(obj.code);
			} catch(err) {
				showError(err.message);
			}
		}
	});
}

function loadContent(cId) {
	$.ajax({
		url: '/modit/ajax/showPageContent/leasing',
		data: {p_id : cId},
		method: 'post',
		dataType: "html",
		success: function(data,textStatus,jqXHR) {
			try {
				var obj = eval('('+data+')');
				if (obj.html != null && obj.html.length > 0) {
					removeTinyMCE($('#middleContent textarea'));
					$("#middleContent")[0].innerHTML = obj.html;
					addTinyMCE($('#middleContent textarea'));
				}
				if (obj.code != null && obj.code.length > 0) {
					eval(obj.code);
				}
			} catch(err) {
				$("#middleContent")[0].innerHTML = err.message;
			}
		}
	});
}

function getPid() {
	pid = 0;
	if (document.location.search.length > 0) {
		parms = document.location.search.replace("?","").split("&");
		for(var i = 0; i < parms.length; i++) {
			var p = parms[i].split("=");
			if (p.length > 1 && p[0] == "p_id")
				pid = p[1];
		}
	}
	return pid;
}

function deleteContent(cId) {
	if (confirm("Are you sure you want to delete this item?")) {
		$.ajax({
			url: '/modit/ajax/deleteContent/leasing',
			data: {'p_id':cId},
			success: function(data,textStatus,jqXHR) {
				try {
					var obj = eval('('+data+')');
					if (obj.status == "true")
						document.location = document.location;	// force a refresh
					else
						showError(obj.messages);
					if (obj.code && obj.code.length > 0) eval(obj.code);
				} catch(err) {
					showError(err.message);
				}
			}
		});
	}
}

function loadSearchForm(id) {
	$.ajax({
		url: '/modit/ajax/showSearchForm/leasing',
		data: {'p_id':id},
		success: function(data,textStatus,jqXHR) {
			try {
				var obj = eval('('+data+')');
				if (obj.status == 'true') {
					$("#searchForm")[0].innerHTML = obj.html;
				}
				if (obj.code && obj.code.length > 0) eval(obj.code);
			} catch(err) {
				var erm = err.message.concat('<br/>',data);
				$("#searchForm")[0].innerHTML = erm;
			}
		}
	});
}
//
//	browser seems to have an issue with function addContent() after it is called 1 time - still a problem
//
var editArticle = function(p_id) {
	$.ajax({
		url: '/modit/ajax/addContent/leasing',
		data: {'p_id': p_id },
		success: function(data,textStatus,jqXHR) {
			try {
				var obj = eval('('+data+')');
				//if (obj.status == 'true') {
					removeTinyMCE($('#popup textarea'))
					showPopup(obj.html);
					addTinyMCE($('#popup textarea'));
					if (obj.code && obj.code.length > 0) {
						eval(obj.code);
					}
				//}
			} catch(err) {
				var erm = err.message.concat('<br/>',data);
				showError(err.message);
				//$("#popup div.errorMessage")[0].innerHTML = erm;
			}
		}
	});
	return false;
}

function resetSize(id) {
	var x = $("#".concat(id));
}

function mySort(obj) {
	var cls = obj.className.split(" ");
	for(var i = 0; i < cls.length; i++) {
		if (cls[i].indexOf('sortorder') >= 0) {
			tmp = cls[i].split('_');
			return tmp[1];
		}
	}
	return 0;
}

var formCheck_add = function (frmId,f_url,el) {
	formCheck(frmId,f_url,el);
}

var formCheck_fldr = function (frmId,f_url,el) {
	//
	//	convert the <ul> to <select>
	//
	formCheck(frmId,f_url,el);
}

var pagination = function (pnum, url, el, obj) {
	var frm = getParent(obj,'form');
	$('#'.concat(frm.id,' input[name=pagenum]')).val(pnum);
	formCheck(frm.id,url,el);
}

function leasingDrop(obj,evt,el,dest) {
	// obj is the destination element
	// evt the event
	// el the object being dropped
	// dest the object type we dragged onto
	clearMessages();
	if (dest == 'tree') {
		destId = $(obj).find("a.icon_folder")[0].id.split("_");
		srcId = el.draggable.find("div.id")[0].innerHTML.split("/");
		if (srcId[0] > 0 && destId[1] > 0) {
			$("#dialog-confirm" ).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
					"Copy the leasing": function() {
						$.ajax({
							url: '/modit/ajax/moveArticle/leasing',
							data: {'src': srcId[0], 'dest': destId[1], 'type':'tree','copy':1},
							success: function(data,textStatus,jqXHR) {
								try {
									var obj = eval('('+data+')');
									if (obj.status != 'true') {
										showError(obj.messages);
									}
									else {
										getInfo('li_'.concat(destId[1]));
									}
								}catch(err) {
									showError(err.message.concat(' [',data,']'));
								}
							}
						});
						$( this ).dialog( "close" );
					},
					"Move the leasing": function() {
						$.ajax({
							url: '/modit/ajax/moveArticle/leasing',
							data: {'src': srcId[1], 'dest': destId[1], 'type':'tree','move':1},
							success: function(data,textStatus,jqXHR) {
								try {
									var obj = eval('('+data+')');
									if (obj.status != 'true') {
										showError(obj.messages);
									}
									else {
										getInfo('li_'.concat(destId[1]));
									}
								}catch(err) {
									showError(err.message.concat(' [',data,']'));
								}
							}
						});
						$( this ).dialog( "close" );
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		}
	}
	else {
		$.ajax({
			url: '/modit/ajax/moveArticle/leasing',
			data: {'src': obj, 'dest': evt, 'type':'leasing'},
			success: function(data,textStatus,jqXHR) {
				try {
					var obj = eval('('+data+')');
					if (obj.status != 'true') {
						showError(obj.messages);
					}
					else {
						loadActiveFolder()
					}
				}catch(err) {
					showError(err.message);
				}
			}
		});
	}
}

function loadActiveFolder() {
	$('#contentTree a.active').each(function (idx,el) {
		active = el.id.split("_");
	});
	loadContent(active[1]);
}

function deleteArticle(p_id,j_id) {
	$.ajax({
		url: '/modit/ajax/deleteArticle/leasing',
		data: {'j_id':j_id,'p_id':p_id},
		success: function(data,textStatus,jqXHR) {
			try {
				obj = eval('('+data+')');
				if (obj.status == 'true') showPopup(obj.html);
				if (obj.code && obj.code.length > 0) eval(obj.code);
				if (obj.messages && obj.messages.length > 0) showPopupError(obj.messages);
			} catch(err) {
				showError(err.message);
			}
		}
	});
}

var getUrl = function () {
	return pagingUrl;
}


function clearDraggable(idx,el) {
	el.className = el.className.replace('draggable','');
}

function initSearch() {
	if ($('#search-tabs').length > 0) {
		$('#pager').change(function() {
			formCheck("searchForm","/modit/ajax/showSearchForm/leasing","middleContent");
		});
		addSortableDroppable(true);
		initDateFields();
	}
	else
		$(document).ready(function() {
			initSearch();
		});
}

function addSortableDroppable(searchForm) {
	if(searchForm == true) {
		$( "#articleList tbody tr" ).draggable({
			revert: 'invalid',
			cancel: '.edit a, .delete a',
			helper: function(event) {
				//if ($.browser.msie) {
				//	var tr = $(event.target).closest('tr');
				//	var tmp = tr.clone();
				//	tr.children().each(function(idx,el) {
				//		$(tmp.children()[idx]).width(el.clientWidth);
				//	});
				//}
				//else
					var tmp = $('<div class="drag-row"><table></table></div>').find('table').append($(event.target).closest('tr').clone()).end();
				return tmp;
				//return $('<div class="drag-row"><table></table></div>').find('table').append($(event.target).closest('tr').clone()).end();
			},
			forceHelperSize: true,
			cursor: 'move',
			placeholder: 'placeholder'
		});
	} else {
		$( '#articleList tbody').sortable({
			revert: 'invalid',
			cancel: '.edit a, .delete a',
			helper: fixHelper,
			forceHelperSize: true,
			cursor: 'move',
			placeholder: 'placeholder',
			appendTo: $( '#articleList'),
			start: function( event, ui) {
				dropped = false;
			},
			update: function( event, ui) {
				if(!dropped) {
					idx = $(ui.item[0]).find("div.id")[0].innerHTML.split("/");
					curLoc = $(ui.item[0]).index();
					leasingDrop(idx[1],curLoc,null,'leasing');
				}
				dropped = false;
			}
		});
		$("#articleList tbody").disableSelection();
	}
	makeDroppable();
}

function makeDroppable() {
	$('#contentTree ol.ui-sortable li .wrapper').droppable({
		accept: "#articleList tr",
		hoverClass: "active_drop",
		tolerance:'pointer',
		drop: function( event, ui ) {
			dropped = true;
			leasingDrop(this,event,ui,'tree');
		}
	});
}

function sortableOptions() {
		$( '#optionsTable tbody').sortable({
			revert: 'invalid',
			cancel: '.edit a, .delete a',
			helper: fixHelper,
			forceHelperSize: true,
			cursor: 'move',
			placeholder: 'placeholder',
			appendTo: $( '#optionsTable'),
			start: function( event, ui) {
				dropped = false;
			},
			update: function( event, ui) {
				if(!dropped) {
					idx = ui.item[0].id.split("_");
					curLoc = $(ui.item[0]).index();
					$.ajax({
						url: '/modit/ajax/moveOption/leasing',
						data: {'src': idx[2], 'dest': curLoc},
						success: function(data,textStatus,jqXHR) {
							try {
								var obj = eval('('+data+')');
								if (obj.status != 'true') {
									showPopupError(obj.messages);
								}
							}catch(err) {
								showPopupError(err.message);
							}
						}
					});
				}
				dropped = false;
			}
		});
		$("#optionsTable tbody").disableSelection();
}

getModels = function(el) {
	$.ajax({
		url: "/modit/ajax/getModels/leasing",
		data: { m_id: $(el).val() },
		success: function( obj, status, xhr ) {
			try {
				obj = eval("("+obj+")");
				$("#model-selector").html(obj.html);
				$("#model-selector select").chosen();
			}
			catch(err) {
			}
		}
	});
}

getModelYears = function(el) {
	$.ajax({
		url: "/modit/ajax/getModelYears/leasing",
		data: { m_id: $(el).val() },
		success: function( obj, status, xhr ) {
			try {
				obj = eval("("+obj+")");
				$("#year-selector").html(obj.html);
				$("#year-selector select").chosen();
			}
			catch(err) {
			}
		}
	});
}

showMake = function() {
	$.ajax({
		url: "/modit/ajax/showMakes/leasing",
		success: function( obj, status, xhr ) {
			try {
				obj = eval("("+obj+")");
				$("#middleContent").html(obj.html);
				eval(obj.code);
			}
			catch(err) {
			}
		}
	});
}

editMake = function(p_id) {
	$.ajax({
		url: "/modit/ajax/editMake/leasing",
		data: {p_id:p_id},
		success: function( obj, status, xhr ) {
			try {
				obj = eval("("+obj+")");
				showPopup(obj.html);
				eval(obj.code);
			}
			catch(err) {
			}
		}
	});
}

editModel = function(p_id,g_id) {
	$.ajax({
		url: "/modit/ajax/editModel/leasing",
		data: {p_id:p_id,g_id:g_id},
		success: function( obj, status, xhr ) {
			try {
				obj = eval("("+obj+")");
				showAltPopup(obj.html);				
				eval(obj.code);
			}
			catch(err) {
			}
		}
	});
}

modelSort = function(typ,el) {
	f = $(el).closest("form");
	s = f.find("input[name=sortby]");
	if (s.val() == typ) {
		d = f.find("input[name=sortdir]");
		d.val( d.val() == "asc" ? "desc" : "asc" );
	}
	else
		s.val(typ);
	$(f).ajaxSubmit({
		success: function( obj, status, xhr ) {
			try {
				obj = eval("("+obj+")");
				$("#modelList").replaceWith(obj.html);
			}
			catch(err) {
			}
		}
	});
}