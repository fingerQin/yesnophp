//滚动条
$(function() {
	$(":text").addClass('input-text');
})

/**
 * 全选checkbox,注意：标识checkbox id固定为为check_box
 * @param string name 列表check名称,如 uid[]
 */
function selectall(name) {
	if ($('#check_box').is(':checked')) {
		$("input[name='"+name+"']").each(function() {
  			$(this).prop("checked", true);
			
		});
	} else {
		$("input[name='"+name+"']").each(function() {
  			$(this).prop("checked", false );
		});
	}
}

/**
 * airDialog5版弹出框tips。
 * @param message 提示内容。
 * @param interval 间隔时间。单位秒。
 * @return void
 */
function dialogTips(message, interval) {
	var d = dialog({
		id : 'dialogTips' + '_' + Math.random(),
	    content: message
	});
	d.show();
	setTimeout(function () {
	    d.close().remove();
	}, interval * 1000);
}

/**
 * 弹出一个添加/编辑操作的对话框。
 * @param dialog_id 弹出框的ID。
 * @param page_url 表单页面URL。
 * @param title 弹出框名称。
 * @param concat_form 合并表单数据
 * @param scrolling ifream是否滚动。yes、no。
 * @return void
 */
function postDialog(dialog_id, page_url, dialog_title, dialog_width, dialog_height, concat_form, scrolling) {
	var scrolling_val = 'no';
	if (scrolling == 'yes') {
		scrolling_val = 'yes';
	}
	var d = top.dialog({
		id : dialog_id + '_' + Math.random(),
	    title: dialog_title,
	    url: page_url,
	    width: dialog_width,
	    height: dialog_height,
	    data: concat_form,
	    scrolling : scrolling_val,
	    onclose: function () {
	    	if (this.returnValue.refresh != undefined && this.returnValue.refresh == 1) {
	    		location.reload();
	    	}
		}
	});
	d.showModal();
}

/**
 * 弹出一个普通操作的对话框（类似于Yes or No这样简单的对话框）。
 * @param dialog_id 弹出框的ID。
 * @param request_url 操作请求的URL。
 * @param title 操作提示。
 * @return void
 */
function normalDialog(dialog_id, request_url, title) {
	var d = top.dialog({
		id : dialog_id + '_' + Math.random(),
	    title: '操作提醒',
	    content: title, 
		okValue: '确定',
		ok : function() {
			$.ajax({
				type: "GET",
				url: request_url,
				dataType: 'json',
				success: function(data){
					if (data.errcode) {
						d.close();
						dialogTips(data.errmsg, 5);
					} else {
						d.close({"refresh" : 1});
					}
				}
			});
			return false;
		},
		onclose: function () {
			if (this.returnValue.refresh != undefined && this.returnValue.refresh == 1) {
	    		location.reload();
	    	}
		}
	});
	d.showModal();
}

/**
 * 弹出一个删除操作的对话框。
 * @param dialog_id 弹出框的ID。
 * @param request_url 执行删除操作的URL。
 * @param title 要删除的记录的标题或名称。
 * @return void
 */
function deleteDialog(dialog_id, request_url, title) {
	var d = top.dialog({
		id : dialog_id + '_' + Math.random(),
	    title: '操作提醒',
	    content: '您确定要删除【' + title + '】吗？', 
		okValue: '确定',
		ok : function() {
			$.ajax({
				type: "GET",
				url: request_url,
				dataType: 'json',
				success: function(data){
					if (data.errcode) {
						d.close();
						dialogTips(data.errmsg, 5);
					} else {
						d.close({"refresh" : 1});
					}
				}
			});
			return false;
		},
		onclose: function () {
			if (this.returnValue.refresh != undefined && this.returnValue.refresh == 1) {
	    		location.reload();
	    	}
		}
	});
	d.showModal();
}

/**
 * 普通文本提示框。
 * @param dialog_id 弹出框的ID。
 * @param title 弹出框标题。
 * @param message 弹出框内容。
 * @param dialog_width 弹出框宽度。
 * @param dialog_height 弹出框高度。
 */
function textDialog(dialog_id, title, message, dialog_width, dialog_height) {
	var d = top.dialog({
		id : dialog_id + '_' + Math.random(),
	    title: title,
	    width: dialog_width,
	    height: dialog_height,
	    content: message, 
		okValue: '关闭',
		ok : function() {
			// ...
		},
		onclose: function () {
			// ...
		}
	});
	d.showModal();
}