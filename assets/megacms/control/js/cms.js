//Executed when frame has loaded DOM, adds url to buttons and adress bar and checks if contains data-jo attribute
function MEGA_frame_ready(){
    clearInterval(window.MEGA_frame_interval);
    window.MEGA_frame_interval = false;

    var iframe = document.querySelector('#frame');

    iframe.contentWindow.onunload = MEGA_frame_loading;
    var url = $("#frame").contents().get(0).location.href;
    var a = document.createElement('a');
    a.href = url;
    var url = a.pathname.split('#')[0].split('?')[0];

    history.pushState({info: "megacms"},"megacms","?path=" + encodeURIComponent(url));

    var iframedoc = iframe.contentDocument || iframe.contentWindow.document;
    if(iframedoc.querySelectorAll("*[data-jo='true'], *[data-cms='cms']").length != 0){
        $("#edit").remove();
        $("#buttons").append('<div id="edit" onclick="MEGA_openfile(\'' + url + '\')"><img src="/megacms/core/style/icons/edit.svg" title="'+MEGA_lang.CMS_EDIT+'"/></div>');
    }else{
        $("#edit").remove();
    }

    $("#areaselector").attr("href","/megacms/apps/areaselector/index.php?file=" + encodeURIComponent(url));
    $("#code").attr("href","/megacms/apps/codeeditor/index.php?file=" + encodeURIComponent(url));
    $("#history").attr("href","/megacms/apps/history/index.php?file=" + encodeURIComponent(url));

}
//Interval for checking if DOM is ready
function MEGA_frame_loading(){
    $("#edit").remove();
    var iframe = document.querySelector('#frame');
    window.MEGA_frame_interval = setInterval(function(){
        var iframedoc = iframe.contentDocument || iframe.contentWindow.document;
        if(iframedoc.querySelectorAll("*[data-jo='true'], *[data-cms='cms']").length != 0){
            MEGA_frame_ready();
        }
    },500)

}
$(document).ready(function(){
        document.getElementById("frame").contentWindow.addEventListener('error', function (error) {
          megacms_noticebar_func('js_error','File: ' + error.error.fileName + ' Location: ' + error.error.lineNumber + '/' + error.error.columnNumber);
        });
        function megacms_explorer(e){
            var type = $(e).attr("id");
            if($(e).hasClass("explorer_close") === true){
              type = "";
            }

            $(".openmenu").find(".explorer_close").remove();
            $(".openmenu").removeClass("explorer_close");
            $(".openmenu").find("img").css("display","");

            switch (type) {
              case "explorer_files":
                  $("#explorer div").hide();
                  $("#file_explorer").show();
                  MEGA_explorer_toggle(e);
                break;
              case "explorer_more":
                  $("#explorer div").hide();
                  $("#options_explorer").show();
                  MEGA_explorer_toggle(e);
                break;
              default:
                if($("#navigation").width() > 50){
                    $("#navigation").width("45px");
                }
            }
        }

        function MEGA_explorer_toggle(e){
          $(e).find("img").css("display","none");
          $(e).addClass("explorer_close");
          $(e).append("<img src='/megacms/core/style/icons/close.svg' class='explorer_close' title='"+MEGA_lang.FORM_DISM+"' />");
          if($("#navigation").width() < 50){
              $("#navigation").width("350px");
          }
        }

        //file explorer functions
        MEGA_folder_toggle($(".MEGA_folder"));

        $(".MEGA_folder").each(function(){
            if($(this).parent().find(".megacms_file").length == 0){
              $(this).addClass("MEGA_folder_inactive");
              $(this).css({"pointer-events":"none"});
            }
        });

        $(".megacms_file a").click(function(){
            var url = $(this).data("url") + "?v=" + Math.floor((Math.random() * 100) + 1);
            $('iframe').attr("src",url);
            megacms_explorer();
        });

        $(".openmenu").click(function(){
            megacms_explorer(this);
        });

        //set up edit button
        MEGA_frame_loading();
        $("#frame").load(function(){
            if(window.MEGA_frame_interval != false){
                MEGA_frame_ready();
            }

        });

    });

function MEGA_openfile(url){
  $("#edit").addClass("MEGA_load");
  var content_complete = {};
  content_complete.url = encodeURIComponent(url);
  MEGA_ajax('/megacms/apps/editor/inc/filemanager.php',content_complete,function(e,data,url){
      window.location.assign(decodeURIComponent(e.url)+ "_cms_temp.php?megacms=" +  e.pin);
  });
}
