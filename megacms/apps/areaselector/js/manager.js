document.addEventListener("DOMContentLoaded", function(){
    function MEGA_append(elem,type,src){
      var s = document.createElement(elem);
      s.type = type;
      s.src = src;
      s.async = false;
      document.head.appendChild(s);
    }

    if(!window.jQuery){
        MEGA_append('script','text/javascript','https://code.jquery.com/jquery-latest.js');
    }

    var s = document.createElement('link');
    s.rel = 'stylesheet';
    s.href = '/megacms/core/style/css/stylesheet.css';
    document.head.appendChild(s);

    var s = document.createElement('link');
    s.rel = 'stylesheet';
    s.href = '/megacms/apps/areaselector/css/stylesheet.css';
    document.head.appendChild(s);

    MEGA_append('script','text/javascript','https://code.jquery.com/ui/1.12.1/jquery-ui.js');

    MEGA_append("script","text/javascript","/megacms/core/js/ui.js");

    MEGA_append("script","text/javascript","/megacms/apps/areaselector/js/area.js");
}, false);
