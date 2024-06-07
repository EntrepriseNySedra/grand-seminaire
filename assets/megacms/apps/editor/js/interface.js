//-------------------------------------------------------------------------------------mobile

window.MEGA_mobilecheck = MEGA_check_device();
window.MEGA_labelset = [
  {
    selector: '[data-jo-content="repeated"]',
    class: 'MEGA_label_large',
    features: [
      ["/megacms/core/style/icons/plusgray.svg", MEGA_additem]
    ],
    init: function(e){
      $(e).find(".ui-sortable").add($(e).filter(".ui-sortable")).each(function(){
        if(typeof $(this).sortable('instance') != "undefined"){
          $(this).sortable("destroy");
        }
      });
      $(e).sortable({
        items: ">*:not(.MEGA_elem)",
        handle: ".MEGA_handle"
      });
    }
  },
  {
    selector: '[data-jo-content="repeated"]>*:not(.MEGA_label)',
    class: 'MEGA_label_small',
    features: [
      ["/megacms/core/style/icons/drag.svg", "MEGA_handle"],
      ["/megacms/core/style/icons/delete.svg", MEGA_delete]
    ],
    init: function(e){
      if($(e).parents("[data-jo='true']").is('.mce-content-body')){
        var mce_id = $(e).parents("[data-jo='true']").attr("id");
        $(e).find(".MEGA_label .MEGA_handle").mousedown(function(){
          tinymce.get(mce_id).setMode('readonly');
        }).mouseup(function(){
          tinymce.get(mce_id).setMode('design');
        });
      }
    }
  }
];

$(document).ready(function(e){
    //Speicher warnung
    window.onbeforeunload = function() {
        return MEGA_lang.MSG_LEAV;
    };

    //taskvalid
    window.MEGA_interval = setInterval("MEGA_taskvalid()",2000);

    MEGA_actionwindow(megacms_imageupload,function(){
      megacms_content('cancel');
    });

    MEGA_code_prepare($(document.body), false);
    MEGA_highlightarea($(document.body), window.MEGA_labelset);

    var selector = '[data-jo="true"]:not([data-jo-content="repeated"]), [data-jo="true"][data-jo-content="repeated"] [data-jo-content="editable"]:not([data-jo-content="noneditable"] [data-jo-content="editable"])';
    $(selector).each(function(){
      MEGA_tinymce_init(this, window.MEGA_mobilecheck);
    });
});

//-------------------------------------------------------------------------------------initialize editor
//input: object for editor
//output: id of editor
function MEGA_tinymce_init(elem, mobile = false){
  if($(elem).is('.mce-content-body[id]')){
     tinymce.get($(elem).attr('id')).destroy();
  }
  if($(elem).is(MEGA_set.ed_block)){
    block = true;
  }else{
    block = false;
  }
  var setup = "";
  if(mobile==false){
    if(block == false){
      var tools = ['undo redo',
          'bold italic underline forecolor',
          'backcolor link unlink'
      ];
    }else{
      var tools = ['undo redo styleselect',
          'bold italic underline forecolor',
          'fontselect fontsizeselect',
          'backcolor link unlink image',
          'alignleft alignright aligncenter alignjustify',
          'numlist bullist addmask'
      ];
      var setup = function (editor) {
          //actionbars for masks
          editor.on('SetContent', function(){
            MEGA_highlightarea(editor.bodyElement, window.MEGA_labelset);
          });
          //button for new masks
          editor.addButton('addmask', {
              icon: 'insertdatetime',
              image: tinymce.baseURL + "../../../../../../core/style/icons/maskblack.svg",
              tooltip: window.MEGA_lang['MSK_NEW'],
              onclick: function (){
                   MEGA_window({
                       id: "mask",
                       type: "list",
                       desc: window.MEGA_lang['MSK_NEW'],
                       cb: function(opt, cb){
                           var message = {content: "all"};
                           MEGA_ajax('/megacms/apps/mask/inc/getmask.php', message, function(e,data,url){
                               var mask_list = {};
                               e.masks.forEach(function(val){
                                   mask_list[val.id] = val.name;
                               });
                               cb(mask_list);
                           });
                       }
                   }, function(mask){
                       var message = {content: mask["mask"]};
                       MEGA_ajax('/megacms/apps/mask/inc/getmask.php', message, function(e,data,url){
                           editor.insertContent(MEGA_code_prepare(e.masks[0].code));
                           MEGA_highlightarea(editor.bodyElement, window.MEGA_labelset)
                       });
                   });
                }
          });
        };
    }
    var plugins = "link image imagetools lists textcolor noneditable";
  }else{
    if(block == false){
      var tools = ['undo redo bold italic underline'];
    }else{
      var tools = ['undo redo bold italic underline numlist bullist'];
    }
    var plugins = "lists noneditable";
  }

  var MEGA_tinymce_settings = {
    //init
    target: elem,
    toolbar: tools,
    plugins: plugins,
    setup: setup,

    //apperance
    skin: "custom",
    inline: true,
    statusbar: false,
    branding: false,
    fixed_toolbar_container:"#megacms_buttons",

    //functionality
    //custom_ui_selector: ".MEGA_panel_drag",
    relative_urls : false,
    entity_encoding: "raw",
    force_br_newlines : false,

    //menu
    menubar:"",
    language: window.MEGA_lang.MCE_FILE,

    //fonts
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",

    //media handling
    paste_data_images: false ,
    file_picker_types: "image",
    image_title: true,
    automatic_uploads: false,
    images_upload_url: "/megacms/apps/editor/inc/upload.php",
    file_picker_types: "image",
    images_reuse_filename: true,

    //filepicker for images
    file_picker_callback: function(callback, value, meta) {
        var form = [];
        form.push({
            id: "url",
            cb: function(opt, cb){
              MEGA_ajax('/megacms/apps/editor/inc/galery.php',"",function(e,data,url){
                  var url_list = {};
                  e.files.forEach(function(val){
                      url_list[val] = val;
                  });
                  cb(url_list);
              });
            },
            opt: meta.filetype,
            type: "thumbnails",
            desc: MEGA_lang.FORM_IMG
        });
        form.push({
            id: "desc",
            type: "text",
            desc: MEGA_lang.FORM_DESC
        });
       MEGA_window(form, function(attr){
           callback(attr.url, {alt: attr.desc});
       });
    }
  };
  tinymce.init(MEGA_tinymce_settings);
  return $(elem).attr('id');
}

//------------------------------------------------------------------------------------- megacms MEGA_ajax

function MEGA_taskvalid(){
  var content_complete = {};
  content_complete.url = window.location.href;
  MEGA_ajax('/megacms/core/inc/taskvalid.php',content_complete,function(e,data,url){});
}

function megacms_imageupload(){
    $("#megacms_functions_submit_save").addClass("MEGA_load");
    var ed = tinymce.editors;
    ed.counter = 0;
    for(i=0; i<ed.length; i++){
        tinymce.editors[i].uploadImages(function(success) {
             ed.counter = ed.counter + 1;
            if(ed.counter==ed.length){
                megacms_content("save");
            }
        });
    }

}

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
        content_complete.html = [];


        $("[data-jo='true']").each(function(){
          $(this).find('.mce-content-body').add($(this).filter('.mce-content-body')).each(function(){
            var id = $(this).attr("id");
            if($(this).is('[id^=mce]')){
              $(this).removeAttr("id");
            }
            var html = tinymce.get(id).getContent();
            tinymce.get(id).destroy();
            $(this).html(html);
          });
        });
        $('.MEGA_label').remove();
        $('.MEGA_elem').remove();
        $('.ui-sortable').removeClass('ui-sortable');
        $('.mceEditable').removeClass('mceEditable');
        $('.mceNonEditable').removeClass('mceNonEditable');
        $('.MsoNormal').removeClass('MsoNormal');
        $('[spellcheck]').removeAttr('spellcheck');
        $('[id^=mce]').removeAttr("id");

        $("[data-jo='true']").each(function(){
          if($(this).is('[data-cmspos]')){
            var pos = $(this).attr('data-cmspos');
            content_complete.html[pos] = MEGA_code_clean($(this).html());
          }
        });

    }
    content_complete.url = window.location.href;
    // console.log(document.documentElement.outerHTML);
    // console.log(content_complete);
    // alert(content_complete.html[0]);
    MEGA_ajax('/megacms/apps/editor/inc/save.php',content_complete,function(e,data,url){
        clearInterval(window.MEGA_interval);
        window.location.replace("/megacms/control/cms.php?path=" + encodeURIComponent(e.redirection));
    },false);
}


//------------------------------------------------------------------------------------- megacms floating labels

//inserts editable and noneditable areas in code segment
//input: code (text or jquery object), plain = false if jquery object
//return: plain html code if plain = true
function MEGA_code_prepare(code, plain=true){
    if(plain == true){
      var code = $.parseHTML(code);
    }
    var select_editable = "[data-jo-content='editable']";
    var select_noneditable = "[data-jo-content='repeated'], [data-jo-content='noneditable']"
    $(code).filter(select_editable).add($(code).find(select_editable)).each(function(){
        $(this).addClass("mceEditable");
    });
    $(code).filter(select_noneditable).add($(code).find(select_noneditable)).each(function(){
        $(this).addClass("mceNonEditable");
    });
    if(plain==true){
      return $(code).prop('outerHTML');
    }
    return code;
}
//removes all megacms items from code
//input: html code
//return: cleaned code
function MEGA_code_clean(code){
  var wrapper = $("<div></div>").html(code);
  var selector = ".mceEditable, .mceNonEditable";
  $(wrapper).find(selector).each(function(){
    $(this).removeClass(selector.replace(",", " "));
  });
  var selector = ".MEGA_label";
  $(wrapper).find(selector).each(function(){
    $(this).remove();
  });
  return $(wrapper).html();
}


function MEGA_delete(e){
    var remaining = $(e).parent().children("*:not(.MEGA_label)").length;
    if(remaining > 1){
      $(e).remove();
    }else{
      $(e).filter("[data-jo-content='editable']").add($(e).find("[data-jo-content='editable']")).each(function(){
          $(this).html("");
      });
      $(e).css("display", "none");
      $(e).find(".MEGA_label").remove();
    }
}

//adds masks
//input: repeated container object
//return: ref to new element
function MEGA_additem(elem){
    var code = $(elem).children("*:not(.MEGA_label)"); //////if no element?
    var len = $(code).length;
    var code_new = $($(code)[0].outerHTML);
    if(len < 2 && $(code[0]).is(":hidden")){
      $(code_new).css("display", "");
      $(code).remove();
    }
    $(code_new).filter("[data-jo-content='editable']").add($(code_new).find("[data-jo-content='editable']")).each(function(){
        $(this).html("Insert content here");
    });
    $(code_new).find(".MEGA_label").remove();
    $(elem).append(MEGA_code_prepare(code_new, false));
    $(code_new).find("[id]").add($(code_new).filter("[id]")).each(function(){
      $(this).removeAttr("id");
    });
    //tinymce.execCommand('mceAddEditor', false, mce_id);
    if($(elem).parents('.mce-content-body').length > 0){
      var mce_id = $(elem).parents("[data-jo='true']").attr("id");
      MEGA_highlightarea(tinymce.activeEditor.bodyElement, window.MEGA_labelset);
    }else{
      var selector = $(code_new).find('[data-jo-content="editable"]:not([data-jo-content="noneditable"] [data-jo-content="editable"])');
      var ed_id = "";
      $(selector).each(function(){
        ed_id = MEGA_tinymce_init(this, window.MEGA_mobilecheck);
      });
      MEGA_highlightarea(elem, window.MEGA_labelset);
      // if(typeof ed_id != 'undefined'){
      //     tinyMCE.get(ed_id).focus();
      // }
      return code_new;
    }

}
