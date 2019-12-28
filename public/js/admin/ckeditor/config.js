CKEDITOR.editorConfig = function( config ) {
	config.allowedContent = true;
	config.protectedSource.push( /<i[\s\S]*?\>/g ); //allows beginning <i> tag
	config.protectedSource.push( /<\/i[\s\S]*?\>/g ); //allows ending </i> tag
	config.enterMode = CKEDITOR.ENTER_BR;
};
CKEDITOR.dtd.$removeEmpty['i'] = false;
CKEDITOR.dtd.$removeEmpty['a'] = false;