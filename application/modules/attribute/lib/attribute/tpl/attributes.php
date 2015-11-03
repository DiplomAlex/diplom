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
					<div class="key"><span data-type="text"></span></div>
					<div class="type"><span data-type="select" data-value="string"></span></div>
					<div class="default"><span data-type="text"></span></div>
					<button class="btn">Удалить</button>
				</div>
			</div>
		</div>
		<div id="toolbar">
			<button onclick="backend.addAttribute();" class="btn">Добавить</button>
			<button onclick="backend.saveValues();" class="btn" class="btn">Сохранить</button>
			<button onclick="backend.loadValues();" class="btn">Отменить</button>
			<span id="ajaxLoader"><img src="<?=$selfUrl?>?action=proxy&path=ajax-loader.gif" />&nbsp;Обработка...</span>
			<span id="ajaxError">&nbsp;Ошибка сохранения/загрузки!</span>
		</div>
		<div id="wrapper"></div>	
		
		<script type="text/javascript">
			jQuery.fn.editable.defaults.mode = 'inline';
			var $wrapper = jQuery('#wrapper');
			var $cover = jQuery('#cover');
			var $loader = jQuery('#ajaxLoader');
			var $error = jQuery('#ajaxError');
			var templates = {
				row: function(name, type, value, index) {
					var $row = jQuery('#rowtemplate .row').clone();
					$row.find('.key span').html(name);
					$row.find('.type span').html(type).attr('data-value', type);
					$row.find('.default span').html(value);
					$row.attr('rel', index);
					$row.find('button').attr('onclick', 'backend.deleteRow('+index+');');
					return $row;
				}
			};

			var backend = {
				editableText: {
					emptytext: 'undefined'
				},
				editableSelect: {
					source : [
	              		{value: 'string', text: 'string'},
	              		{value: 'int', text: 'int'},
	              		{value: 'float', text: 'float'},
	              		{value: 'boolean', text: 'boolean'},
	              		{value: 'datetime', text: 'datetime'},
	              		{value: 'text', text: 'text'}
			        ]
				},
				loadValues: function() {
					$cover.hide();
					var self = this;
					jQuery.post('<?=$selfUrl?>?action=getAttributes', {}, function(data) {
						$wrapper.html('');
						var i = 1;
						jQuery.each(data, function(index, value) {
							var $row = templates.row(value.name, value.type, value.value, i++);
							$row.find('.key span').editable(self.editableText);
							$row.find('.type span').editable(self.editableSelect);
			            	$row.find('.default span').editable(self.editableText);
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
						var k = $this.find('.key span').text();
						data['attr_'+k] = $this.find('.default span').text();
						data['type_'+k] = $this.find('.type span').text();
					});
					jQuery.post('<?=$selfUrl?>?action=setAttributes', data, function(data) {
						self.loadValues();
					});
				},
				addAttribute: function(index) {
					var $row = templates.row('', 'string', 'undefined');
					$row.find('.key span').editable(this.editableText);
					$row.find('.type span').editable(this.editableSelect);
			        $row.find('.default span').editable(this.editableText);
					$row.appendTo($wrapper);
				},
				deleteRow: function(index) {
					$wrapper.find('.row[rel='+index+']').remove();
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