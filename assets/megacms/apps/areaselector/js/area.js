$(document).ready(function(e){

    //warning bevore leaving
    window.onbeforeunload = function() {
        return MEGA_lang.MSG_LEAV;
    };

    //taskvalid interval
    setInterval("MEGA_taskvalid()",2000);

    //create floating panel
    MEGA_actionwindow(function(){
      megacms_content('save');
    },function(){
      megacms_content('cancel');
    });
    $('<input id="MEGA_delete_check" type="checkbox"/><label for="MEGA_delete_check">'+MEGA_lang.AREA_DEL+'</label>').appendTo('#megacms_buttons');

    //highlight editable areas
    $('[data-jo="true"]').each(function(){
      var id = $(this).attr('data-cmsid');
      $(this).addClass('MEGA_selected');
    });

    $('*[data-cmsid]').each(function(){
      $(this).off();
    });


    //adds class to an element
    //input: element reference, class class_name
    //return: processed element reference
    function element_select(elem_input,class_name){
      $('*[data-cmsid]').removeClass('MEGA_target');
      if($(elem_input).parents('.MEGA_selected').length == 0){
        var elem = elem_input;
      }else{
        var elem = $(elem_input).parents('.MEGA_selected')[0];
      }

      if($(elem).hasClass('MEGA_selected') == true && $(elem).parent('*[data-cmsid]').length != 0){
        var elem = $(elem).parent();
      }
      $(elem).addClass(class_name);
      $(elem).find('.' + class_name).each(function(){
        $(this).removeClass(class_name);
      });
      return elem;
    }

    var selector = '[data-cmsid]';

    //handles click events on elements
    //input: click element reference
    //return: -
    $('*' + selector).click(function(event){
      event.preventDefault();

      function remove_class(elem){
        if(!$(elem).is('[data-jo-content="repeated"]')){
          $(elem).removeClass('MEGA_selected');
        }
        $(elem).find('[data-jo-content="repeated"]:not([data-jo-content="repeated"] *)').each(function(){
          element_select(this, 'MEGA_selected');
        });
      }

      if($('#MEGA_delete_check').prop('checked')){
        if($(this).is('.MEGA_selected')){
          remove_class(this);
        }
        $(this).parents('.MEGA_selected').each(function(){
          remove_class(this);
        });

      }else{
        var elem = element_select(this,'MEGA_selected');
        element_select(elem,'MEGA_target');
      }
      event.stopPropagation();
    });

    //handles hover events on elements
    //input: hover element reference
    //return: -
    $('*' + selector).mouseover(function(event){
      if($('#MEGA_delete_check').prop('checked') == false){
        element_select(this,'MEGA_target');
        event.stopPropagation();
      }
    });
});


//------------------------------------------------------------------------------------- megacms MEGA_ajax

//checks if task is still valis
//input: -
//return: -
function MEGA_taskvalid(){
  var content_complete = {};
  content_complete.url = window.location.href;
  MEGA_ajax('/megacms/core/inc/taskvalid.php',content_complete,function(e,data,url){});
}

//processes save/cancel MEGA_action
//input: save/cancel advice
//return: custom callback function
function megacms_content(advice){

    //Warnung ausschalten
    window.onbeforeunload = null;

    //var zum verschicken
    var content_complete = {};
    content_complete.type = "";

    //unterscheiden zw cancel und save
    if(advice != "save"){
        content_complete.type = "cancel";
        $("#megacms_functions_submit_cancel").addClass("MEGA_load");
    }else{
        //Editierbare Elemente in array
        content_complete.sec = [];

        $('.MEGA_selected').each(function(){
          content_complete.sec.push($(this).attr('data-cmsid'));
        });

    }

    content_complete.url = window.location.href;
    MEGA_ajax('/megacms/apps/areaselector/inc/save.php',content_complete,function(e,data,url){
        clearInterval(window.MEGA_interval);
        window.location.replace("/megacms/control/cms.php?path=" + encodeURIComponent(e.redirection));
    },false);
}
