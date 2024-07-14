
function addItem() {
	clearMessages();
	$.ajax({
		url: '/modit/ajax/showPageProperties/artist',
		data: {'id': 0},
		success: function(data,textStatus,jqXHR) {
			obj = eval('('+data+')');
			if (obj.status == 'true') {
				removeTinyMCE($('#popup textarea'));
				showPopup(obj.html);
				addTinyMCE($('#popup textarea'));
				if (obj.code && obj.code.length > 0) eval(obj.code);
			}
		}
	});
}

function moveFolder() {
	id = moveIt('members_folders',this);
	setTimeout(function() {
		loadTree('members',id);
		makeDroppable();
	},100);
}

$(document).ready(function() {
	var dropped = false;
	html = getContent('header',null,$('#header > div.inner')[0]);
	html = getContent('mainNav',null,$('#mainNav > div.inner')[0]);
	loadTree('members');
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
		url: '/modit/ajax/getFolderInfo/artist',
		data: {p_id: id[1]},
		success: function(data,textStatus,jqXHR) {
			try {
				obj = eval('('+data+')');
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
		url: '/modit/ajax/showPageProperties/artist',
		data: {id : cId},
		method: 'post',
		success: function(data,textStatus,jqXHR) {
			try {
				obj = eval('('+data+')');
				if (obj.status == 'true') {
					removeTinyMCE($('#popup textarea'));
					showPopup(obj.html);
					addTinyMCE($('#popup textarea'));
				}
				if (obj.code && obj.code.length > 0) eval(obj.code);
			} catch(err) {
				var x = 0;
			}
		}
	});
}

function loadContent(cId) {
	$.ajax({
		url: '/modit/ajax/showPageContent/artist',
		data: {p_id : cId},
		method: 'post',
		dataType: "html",
		success: function(data,textStatus,jqXHR) {
			try {
				obj = eval('('+data+')');
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
			url: '/modit/ajax/deleteContent/artist',
			data: {'p_id':cId},
			success: function(data,textStatus,jqXHR) {
				try {
					obj = eval('('+data+')');
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

function initTinyMCE_dnu() {
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		editor_deselector: "mceNoEditor",
		plugins : "advimage,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage,|,forecolor,backcolor,|,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",theme_advanced_resizing : true,
		remove_script_host : true,
		relative_urls:false,
		content_css : "/css/tinymce.css",
		template_templates : [
			{ title : "Store Content",
			src : "store-content.html",
			description : "2 column store template"
		}],
		setup : function(ed) {
			ed.onPostRender.add(function(ed, cm) {
				resetSize(ed.id);
			});
		}
	}); 
}

function loadSearchForm(id) {
	$.ajax({
		url: '/modit/ajax/showSearchForm/artist',
		data: {'p_id':id},
		success: function(data,textStatus,jqXHR) {
			try {
				obj = eval('('+data+')');
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
var editArticle = function(m_id,j_id) {
	$.ajax({
		url: '/modit/ajax/addContent/artist',
		data: {'m_id': m_id, 'j_id': j_id },
		success: function(data,textStatus,jqXHR) {
			try {
				obj = eval('('+data+')');
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

var myDrop_dnu = function (obj,event,ui) {
	var li = $( "<li></li>" ).text( ui.draggable.text() )
	li.draggable();
	for(var i = 0; i < ui.draggable.length; i++) {
		li[i].id = ui.draggable[i].id;
		li[i].className = ui.draggable[i].className;
	}
	li.appendTo(obj);
	ui.draggable.remove();
	$( "#fromList li" ).sortElements(function(a,b) {
		return mySort(a) > mySort(b) ? 1 : -1;
	});
	$( "#toList li" ).sortElements(function(a,b) {
		return mySort(a) > mySort(b) ? 1 : -1;
	});
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
function addCallback(obj) {

}

var formCheck_add = function (frmId,f_url,el) {
	//
	//	convert the <ul> to <select>
	//
	$("#".concat(frmId, ' #toList > li')).each(function(idx,el) {
		var ids = el.id.split('_');
		var o = $('<option selected="selected"></option>').text(el.innerHTML);
		o.val(ids[1]);
		o.appendTo($("#".concat(frmId, ' #artistDestFolders')));
	});
	formCheck(frmId,f_url,el,'addCallback');
}

var paginationDNU = function (pnum, url, el, obj) {
	var p_id = $('#artistFolderId')[0].innerHTML
	var frm = getParent(obj,'form');
	$('#'.concat(frm.id,' input[name=pagenum]')).val(pnum);
	formCheck(frm.id,url,el);
}

var pagination = function (pnum, url, el, obj) {
	var p_id = $('#artistFolderId')[0].innerHTML
	var frm = getParent(obj,'form');
	$('#'.concat(frm.id,' input[name=pagenum]')).val(pnum);
	formCheck(frm.id,url,el);
}

function artistDrop(obj,evt,el,dest) {
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
					"Copy the member": function() {
						$.ajax({
							url: '/modit/ajax/moveArticle/artist',
							data: {'src': srcId[0], 'dest': destId[1], 'type':'tree','copy':1},
							success: function(data,textStatus,jqXHR) {
								try {
									obj = eval('('+data+')');
									if (obj.status != 'true') {
										showError(obj.messages);
									}
									else {
										getInfo('li_'.concat(destId[1]));
									}
								}catch(err) {
									showError(err.message);
								}
							}
						});
						$( this ).dialog( "close" );
					},
					"Move the member": function() {
						$.ajax({
							url: '/modit/ajax/moveArticle/artist',
							data: {'src': srcId[1], 'dest': destId[1], 'type':'tree','move':1},
							success: function(data,textStatus,jqXHR) {
								try {
									obj = eval('('+data+')');
									if (obj.status != 'true') {
										showError(obj.messages);
									}
									else {
										getInfo('li_'.concat(destId[1]));
									}
								}catch(err) {
									showError(err.message);
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
			url: '/modit/ajax/moveArticle/artist',
			data: {'src': obj, 'dest': evt, 'type':'member'},
			success: function(data,textStatus,jqXHR) {
				try {
					obj = eval('('+data+')');
					if (obj.messages) showError(obj.messages);
					loadActiveFolder();
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
		loadContent(active[1]);
	});
}

function deleteArticle(m_id,j_id) {
	$.ajax({
		url: '/modit/ajax/deleteArticle/artist',
		data: {'j_id':j_id,'m_id':m_id},
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

function checkAddMember(id) {
	if (id == 0 || id == null) {
		loadMemberMedia(id,0);
	}
}

function initSearch() {
	if ($('#search-tabs').length > 0) {
		$('#pager').change(function() {
			formCheck("searchForm","/modit/ajax/showSearchForm/artist","middleContent");
		});
		addSortableDroppable(true);
		initDateFields();
	}
	else
		$(document).ready(function() {
			initSearch();
		});
}

function editProfile_dnu(p_id) {
	ret_data = $.ajax({
		url: '/modit/ajax/editProfile/artist',
		data: {'p_id':p_id},
		async: false
	});
	try {
		obj = eval('('+ret_data.responseText+')');
		if (obj.status == 'true' && obj.html && obj.html.length > 0) {
			showAltPopup(obj.html);
		}
	}
	catch(err) {
		showAltPopupError(err.message);
	}
};

//
//	editProfile seems to be a browser thing [based on spelling], works 1 time then fails 
//
function editprofile(p_id) {
	ret_data = $.ajax({
		url: '/modit/ajax/editProfile/artist',
		data: {'p_id':p_id},
		async: false
	});
	try {
		obj = eval('('+ret_data.responseText+')');
		if (obj.status == 'true' && obj.html && obj.html.length > 0) {
			showAltPopup(obj.html);
			addTinyMCE($('#altPopup textarea'));
		}
		if (obj.code && obj.code.length > 0) eval(obj.code);
		if (obj.messages && obj.messages.length > 0) showAltPopupError(obj.messages);
	}
	catch(err) {
		alert(err.message);
	}
}

function loadFromEdit() {
	if ($('#showFolderContent').length > 0) {
		formCheck('showFolderContent','/modit/ajax/showPageContent/artist','middleContent');
	}
	else {
		formCheck("searchForm","/modit/ajax/showSearchForm/artist","middleContent");
	}
}

function addSortableDroppable(searchForm) {
	if(searchForm == true) {
		$( "#articleList tbody tr" ).draggable({
			revert: 'invalid',
			cancel: '.edit a, .delete a',
			helper: function(event) {
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
					artistDrop(idx[1],curLoc,null,'member');
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
			artistDrop(this,event,ui,'tree');
		}
	});
}

function loadGroupMedia(f_id) {
	if (f_id == 0 || f_id == null)
		return;
	var retValue = $.ajax({
		url:'/modit/ajax/loadMedia/artist',
		data:{'f_id':f_id,'m_id':0},
		async:false
	});
	try {
		obj = eval('('+retValue.responseText+')');
		$('#tabs-4')[0].innerHTML = obj.html;
		if (obj.code && obj.code.length > 0) eval(obj.code);
		if (obj.messages)
			showPopupMessages(obj.messages);
	} catch(err) {
		showPopupMessages(err.message);
	}
}

function loadMemberMedia(m_id,f_id) {
	if (m_id == 0 || m_id == null)
		return;
	var retValue = $.ajax({
		url:'/modit/ajax/loadMedia/artist',
		data:{'m_id':m_id,'f_id':0},
		async:false
	});
	try {
		obj = eval('('+retValue.responseText+')');
		$('#tabs-6')[0].innerHTML = obj.html;
		if (obj.code && obj.code.length > 0) eval(obj.code);
		if (obj.messages)
			showPopupMessages(obj.messages);
	} catch(err) {
		showPopupMessages(err.message);
	}
}

function fnEditMedia(f_id,m_id,p_id) {
	var retValue = $.ajax({
		url:'/modit/ajax/editMedia/artist',
		data:{'m_id':m_id,'f_id':f_id,'p_id':p_id},
		async:false
	});
	try {
		obj = eval('('+retValue.responseText+')');
		showAltPopup(obj.html);
		if (obj.code && obj.code.length > 0) eval(obj.code);
		if (obj.messages)
			showAltPopupMessages(obj.messages);
	} catch(err) {
		showAltPopupMessages(err.message);
	}
}

function fnDeleteMedia(p_id,f_id,m_id) {
	if (p_id > 0) {
		if (!confirm('Are you sure?'))
			return false;
		var retStatus = $.ajax({
			url:'/modit/ajax/deleteMedia/artist',
			data:{'p_id':p_id,'m_id':m_id,'f_id':f_id},
			async:false
		});
		try {
			var obj = eval('('+retStatus.responseText+')');
			if (obj.status == 'true') {
				eval(obj.code);
			}
			if (obj.messages)
				showPopupMessages(obj.messages);
		}
		catch(err) {
			showPopupMessages(err.message);
		}
	}
}

function checkAddProfile(m_id,f_id) {
	if (f_id > 0) {
		var retValue = $.ajax({
			url:'/modit/ajax/loadMedia/artist',
			data:{'m_id':m_id,'f_id':f_id},
			async:false
		});
		try {
			obj = eval('('+retValue.responseText+')');
			$('#tabs-p3')[0].innerHTML = obj.html;
			if (obj.code && obj.code.length > 0) eval(obj.code);
			if (obj.messages)
				showAltPopupMessages(obj.messages);
		} catch(err) {
			showAltPopupMessages(err.message);
		}
	}
}

fnEditSocial = function(m_id,s_id) {
	$.ajax({
		url: "/modit/ajax/editSocial/artist",
		data:{'member_id':m_id,'s_id':s_id},
		success: function( obj, status, xhr ) {
			try {
				h = eval("("+obj+")");
				showAltPopup(h.html);
				if (h.code && h.code.length > 0) eval(h.code);
				if (h.messages)
					showAltPopupMessages(h.messages);
			} catch(err) {
				showAltPopupMessages(err.message);
			}
		}
	});
}

fnDeleteSocial = function(m_id,s_id) {
	$.ajax({
		url: "/modit/ajax/editSocial/artist",
		data:{'m_id':m_id,'s_id':s_id},
		success: function( obj, status, xhr ) {
			try {
				h = eval("("+obj+")");
				if (h.code && h.code.length > 0) eval(h.code);
				if (h.messages)
					showPopupMessages(h.messages);
			} catch(err) {
				showPopupMessages(err.message);
			}
		}
	});
}

fnRefreshSocial = function(m_id) {
	$.ajax({
		url: "/modit/ajax/getSocialLinks/artist",
		data: {'m_id':m_id},
		success: function( obj, status, xhr ) {
			try {
				h = eval("("+obj+")");
				$("#socialMedia tbody").html(h.html);
				closeAltPopup();
			}
			catch(err) {
				showError(err.message);
			}
		}
	});
}

fnEditGroup = function(m_id,g_id) {
	$.ajax({
		url: "/modit/ajax/editGroup/artist",
		data: {'member_id':m_id, 'g_id':g_id},
		success: function( obj, status, xhr ) {
			try {
				h = eval("("+obj+")");
				showAltPopup(h.html);
				eval(h.code);
			}
			catch(err) {
				showPopupError(err.message);
			}
		}
	});
}

fnEditPortfolio = function( g_id, p_id ) {
	$.ajax({
		url: "/modit/ajax/editPortfolio/artist",
		data: { member_id: g_id, 'p_id':p_id },
		success: function( obj, status, xhr ) {
			try {
				h = eval("("+obj+")");
				closeAltPopup();	// clear tinymce
				showAltPopup(h.html);
				eval(h.code);
			}
			catch(err) {
				showPopupError(err.message);
			}
		}
	});
}

fnReloadPortfolios = function(p_id) {
	$.ajax({
		url: "/modit/ajax/getPortfolios/artist",
		data: { p_id:p_id },
		success: function( obj, status, xhr ) {
			try {
				h = eval("("+obj+")");
				$("#portfoliosTab").html(h.html);
				eval(obj.code);
			}
			catch(err) {
				showPopupError(err.message);
			}
		}
	});
}

fnEditMedia = function(g_id,m_id) {
	$.ajax({
		url: "/modit/ajax/editMedia/artist",
		data: { g_id:g_id, m_id:m_id },
		success: function( obj, status, xhr ) {
			try {
				h = eval("("+obj+")");
				closeAltPopup();
				showAltPopup(h.html);
				eval(h.code);
			}
			catch(err) {
				showAltPopup(err.message);
			}
		}
	});
}

toggleMedia = function(el) {
	t = el.value.split("|");
	f = $(el).closest("form");
	if (t[1] == "E") {
		$(f).find(".external").removeClass("hidden");
		$(f).find(".image").addClass("hidden");
	}
	else {
		$(f).find(".image").removeClass("hidden");
		$(f).find(".external").addClass("hidden");
	}
}
