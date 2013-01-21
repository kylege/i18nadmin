/**
 * 弹出修改tags的界面
 * @param  {string} key 
 * @return {[type]}     
 */
function alterTagsOnclick(item_key){
	$.getJSON('./ajax/showitem.php', {'key':item_key}, function(data){
		$('#alert-model-dom .modal-body input').attr("checked", false);
		$('#alert-model-dom').data('id', 0).modal('show');
		for(i=0; i<data.tags.length; i++){
			var key = data.tags[i];
			$('#ckb-'+key).attr("checked", true);
		}
		$('#model-btn').unbind('click');
		$('#model-btn').bind('click',function(e){
			sendAlterTags(item_key);
		});
	});

}

/**
 * 修改tags提交
 * @return {bool} 
 */
function sendAlterTags(item_key){
	var keys =[];
	$('#alert-model-dom .modal-body input').each(function(){
		if($(this).attr('checked')){
			keys.push($(this).data('key'));
		}
	});
	$.ajax({
		type:'POST',
		dataType:'json',
		url:'./ajax/settag.php',
		data:'key='+item_key+'&tags='+keys,
		success:function(data){
			$('#alert-model-dom').modal('hide');
		}
	});
	return true;
}
/**
 * 添加语言
 * @return {bool} 
 */
function sendAddLang(){

	var content = $('#add-content').val();
	var keys =[];
	$('#add-form input').each(function(){
		if($(this).attr('checked')){
			keys.push($(this).data('key'));
		}
	});

	$.ajax({
		type:'POST',
		dataType:'json',
		url:'./ajax/add_lang.php',
		data:'cs='+content+'&tags='+keys,
		success:function(data){
			if(data.result){
				alert('添加成功');
			}else{
				alert(data.msg);
			}
		}
	});

	content = $('#add-content').val('');
}
/**
 * 修改语言内容
 * @return {} 
 */
function sendAlterLang(){
	var lang = $('#cur_lang').val();
	var key = $('#cur_key').val();
	var content = $('#add-content').val();

	$.ajax({
		type:'POST',
		dataType:'json',
		url:'./ajax/alter_lang.php',
		data:'key='+key+'&lang='+lang+'&content='+content,
		success:function(data){
			if(data.result){
				alert('修改成功');
			}else{
				alert(data.msg);
			}
		}
	});
}
/**
 * 导出语言包按钮
 * @return {bool} 
 */
function exportOnClick(){
	var tag = $('input[name=tag]:checked', '#add-form').val();
	var codetype = $('input[name=codetype]:checked', '#add-form').val();
	if(!tag || !codetype){
		return false;
	}
	window.location = './ajax/export.php?tag='+ tag + '&codetype='+ codetype ;
	return false;
}