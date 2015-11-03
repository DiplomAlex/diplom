<!doctype html><?php
	$selfUrl = strtok("//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], '?');
	$attrs = Attr::inst()->getAttributes(); 
?>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="<?=$selfUrl?>?action=proxy&path=style.css" />
		<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script src="<?=$selfUrl?>?action=proxy&path=jquery-1.11.0.min.js"></script>
		
		<script src="<?=$selfUrl?>?action=proxy&path=jquery-editable/moment.min.js"></script>

		<link href="<?=$selfUrl?>?action=proxy&path=jquery-editable/css/jquery-editable.css" rel="stylesheet">
		<script src="<?=$selfUrl?>?action=proxy&path=jquery-editable/js/jquery-editable-poshytip.min.js"></script>


	</head>
	<body>
		<div class="templates">
			<div id="rowtemplate">
				<div class="row">
					<div class="key"></div>
					<div class="value"></div>
					<button class="btn">Удалить</button>
				</div>
			</div>
			<!--<div id="attrtemplate">
				<span><a href="javascript:void(0);"></a><br /></span>
			</div>-->
		</div>
		<div id="toolbar">
			<button onclick="backend.addAttributeDialog();" class="btn">Добавить</button>
			<button onclick="backend.saveValues();" class="btn" class="btn">Сохранить</button>
			<button onclick="backend.loadValues();" class="btn">Отменить</button>
			<span id="ajaxLoader"><img src="<?=$selfUrl?>?action=proxy&path=ajax-loader.gif" />&nbsp;Обработка...</span>
			<span id="ajaxError">&nbsp;Ошибка сохранения/загрузки!</span>
		</div>
		<div id="wrapper"></div>	
		<div id="cover">
			<div class="attrSelect">
				<strong>Доступные атрибуты:</strong><br />
				<span id="attrSelect">
				<?php $i = 0; foreach ($attrs as $attr) { ?>
					<span><a href="javascript:void(0);" onclick="backend.addAttribute(<?=$i++?>);"><?=$attr['name']?></a><br /></span>
				<?php } ?>
				</span>
			</div>
		</div>
		
		<script type="text/javascript">
			jQuery.fn.editable.defaults.mode = 'inline';
			var $wrapper = jQuery('#wrapper');
			var $cover = jQuery('#cover');
			var $loader = jQuery('#ajaxLoader');
			var $error = jQuery('#ajaxError');
			var templates = {
				row: function(name, type, value, index) {
					var $row = jQuery('#rowtemplate .row').clone();
					$row.find('.key').attr('rel', type).html(name);
					$row.find('.value').html(value);
					$row.attr('rel', index);
					$row.find('button').attr('onclick', 'backend.deleteRow('+index+');');
					return $row;
				}/*,
				attr: function(index, name) {
					var $attr = jQuery('#attrtemplate span').clone();
					$attr.find('a').attr('onclick', 'backend.addAttribute('+index+');').html(name);
					return $attr;
				}*/
			};

			var backend = {
				attributes: [<? $attrJS = array(); foreach ($attrs as $attr) { $attrJS[] = json_encode($attr); } echo(implode(',', $attrJS)); ?>],
				editable: {
					emptytext: 'undefined'
				},
				loadValues: function() {
					$cover.hide();
					var self = this;
					jQuery.post('<?=$selfUrl?>?action=getValues&uid=<?=@$_REQUEST['uid']?>', {}, function(data) {
						$wrapper.html('');
						var i = 1;
						jQuery.each(data, function(index, value) {
							var $row = templates.row(index, value.type, value.value, i++);
							$row.find('.value').editable(self.editableParams(value.type, value.value));
							$row.appendTo($wrapper);
						});
					}, 'json');
				},
				saveValues: function() {
					$cover.hide();
					var data = {};
					var self = this;
					$wrapper.find('.row').each(function() {
						var $this = jQuery(this);
						data['attr_'+$this.find('.key').text()] = $this.find('.value').text();
					});
					jQuery.post('<?=$selfUrl?>?action=setValues&uid=<?=@$_REQUEST['uid']?>', data, function(data) {
						self.loadValues();
					});
				},
				addAttributeDialog: function() {
					//var attr = [];
					var currAttr = [];
					$wrapper.find('.key').each(function() {
						currAttr.push(jQuery(this).text());
					});
					$cover.find('span').show();
					jQuery.each(this.attributes, function(index, value) {
						for (var i = 0; i < currAttr.length; i++) {
							if (currAttr[i] == value.name) {
								var c = index+1;
								$cover.find('#attrSelect span:nth-child('+c+')').hide();
							}
						}
					});
					$cover.show();
				},
				addAttribute: function(index) {
					var attr = this.attributes[index];
					var $row = templates.row(attr.name, attr.type, attr.value);
					$row.find('.value').editable(this.editableParams(attr.type, attr.value));
					$row.appendTo($wrapper);
					$cover.hide();
				},
				deleteRow: function(index) {
					$wrapper.find('.row[rel='+index+']').remove();
				},
				editableParams: function(type, value) {
					var params = {'type': 'text'};
					if (type === 'datetime') {
						params.type = 'combodate';
						params.format = 'YYYY-MM-DD HH:mm:ss';
						params.template = 'D / MM / YYYY';// HH:mm';
						//params.format = 'yyyy-mm-dd hh:ii:ss';
        				//params.viewformat = 'dd/mm/yyyy hh:ii:ss';  
        				params.datetimepicker = {
                			weekStart: 1
           				};
					}
					if (type === 'boolean') {
						params.type = 'select';
						params.value = value;
						params.source = [
			              {value: 1, text: '1'},
			              {value: 0, text: '0'}
			            ];
					}
					return jQuery.extend(this.editable, params);
				}
			};
		</script>
		<script type="text/javascript">
			jQuery(document).on('ready', function() {
				backend.loadValues();
			}).ajaxStart(function() {
			  $error.hide();
			  $loader.show();
			}).ajaxComplete(function() {
			  $loader.fadeOut();
			}).ajaxError(function() {
			  $error.show();
			});
		</script>

	</body>
</html>