$(document).ready(function(){
      
$('.MultiFile').MultiFile({ 
    accept:'txt|csv|zip|tar|gz', max:1, STRING: { 
        remove:'удалить',
        file:'$file', 
        selected:'Выбраны: $file', 
        denied:'Неверный тип файла: $ext!', 
        duplicate:'Этот файл уже выбран:\n$file!' 
    } 
});          
      
$("#loading").ajaxStart(function(){
    $(this).show();
})

.ajaxComplete(function(){
    $(this).hide();
});
      

$('#uploadForm').ajaxForm({
    beforeSubmit: function(a,f,o) {
        o.dataType = "html";
        $('#uploadOutput').html('Загрузка...');
    },
    success: function(data) {
        var $out = $('#uploadOutput');
        //$out.html('Form success handler received: <strong>' + typeof data + '</strong>');
		$out.html('');
        if (typeof data == 'object' && data.nodeType)
            data = elementToString(data.documentElement, true);
        else if (typeof data == 'object')
            data = objToString(data);
        $out.append('<div>'+ data +'</div>');
    }
});
});	