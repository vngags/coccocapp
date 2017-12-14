/*
 * Lazy Load - jQuery plugin for lazy loading images
 *
 * Copyright (c) 2007-2013 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/lazyload
 *
 * Version:  1.9.1
 *
 */

(function($, window, document, undefined) {
    var $window = $(window);

    $.fn.lazyload = function(options) {
        var elements = this;
        var $container;
        var settings = {
            threshold       : 0,
            failure_limit   : 0,
            event           : "scroll",
            effect          : "show",
            container       : window,
            data_attribute  : "original",
            skip_invisible  : true,
            appear          : null,
            load            : null,
            placeholder     : "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
        };

        function update() {
            var counter = 0;

            elements.each(function() {
                var $this = $(this);
                if (settings.skip_invisible && !$this.is(":visible")) {
                    return;
                }
                if ($.abovethetop(this, settings) ||
                    $.leftofbegin(this, settings)) {
                        /* Nothing. */
                } else if (!$.belowthefold(this, settings) &&
                    !$.rightoffold(this, settings)) {
                        $this.trigger("appear");
                        /* if we found an image we'll load, reset the counter */
                        counter = 0;
                } else {
                    if (++counter > settings.failure_limit) {
                        return false;
                    }
                }
            });

        }

        if(options) {
            /* Maintain BC for a couple of versions. */
            if (undefined !== options.failurelimit) {
                options.failure_limit = options.failurelimit;
                delete options.failurelimit;
            }
            if (undefined !== options.effectspeed) {
                options.effect_speed = options.effectspeed;
                delete options.effectspeed;
            }

            $.extend(settings, options);
        }

        /* Cache container as jQuery as object. */
        $container = (settings.container === undefined ||
                      settings.container === window) ? $window : $(settings.container);

        /* Fire one scroll event per scroll. Not one scroll event per image. */
        if (0 === settings.event.indexOf("scroll")) {
            $container.bind(settings.event, function() {
                return update();
            });
        }

        this.each(function() {
            var self = this;
            var $self = $(self);

            self.loaded = false;

            /* If no src attribute given use data:uri. */
            if ($self.attr("src") === undefined || $self.attr("src") === false) {
                if ($self.is("img")) {
                    $self.attr("src", settings.placeholder);
                }
            }

            /* When appear is triggered load original image. */
            $self.one("appear", function() {
                if (!this.loaded) {
                    if (settings.appear) {
                        var elements_left = elements.length;
                        settings.appear.call(self, elements_left, settings);
                    }
                    $("<img />")
                        .bind("load", function() {

                            var original = $self.attr("data-" + settings.data_attribute);
                            $self.hide();
                            if ($self.is("img")) {
                                $self.attr("src", original);
                            } else {
                                $self.css("background-image", "url('" + original + "')");
                            }
                            $self[settings.effect](settings.effect_speed);

                            self.loaded = true;

                            /* Remove image from array so it is not looped next time. */
                            var temp = $.grep(elements, function(element) {
                                return !element.loaded;
                            });
                            elements = $(temp);

                            if (settings.load) {
                                var elements_left = elements.length;
                                settings.load.call(self, elements_left, settings);
                            }
                        })
                        .attr("src", $self.attr("data-" + settings.data_attribute));
                }
            });

            /* When wanted event is triggered load original image */
            /* by triggering appear.                              */
            if (0 !== settings.event.indexOf("scroll")) {
                $self.bind(settings.event, function() {
                    if (!self.loaded) {
                        $self.trigger("appear");
                    }
                });
            }
        });

        /* Check if something appears when window is resized. */
        $window.bind("resize", function() {
            update();
        });

        /* With IOS5 force loading images when navigating with back button. */
        /* Non optimal workaround. */
        if ((/(?:iphone|ipod|ipad).*os 5/gi).test(navigator.appVersion)) {
            $window.bind("pageshow", function(event) {
                if (event.originalEvent && event.originalEvent.persisted) {
                    elements.each(function() {
                        $(this).trigger("appear");
                    });
                }
            });
        }

        /* Force initial check if images should appear. */
        $(document).ready(function() {
            update();
        });

        return this;
    };

    /* Convenience methods in jQuery namespace.           */
    /* Use as  $.belowthefold(element, {threshold : 100, container : window}) */

    $.belowthefold = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = (window.innerHeight ? window.innerHeight : $window.height()) + $window.scrollTop();
        } else {
            fold = $(settings.container).offset().top + $(settings.container).height();
        }

        return fold <= $(element).offset().top - settings.threshold;
    };

    $.rightoffold = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.width() + $window.scrollLeft();
        } else {
            fold = $(settings.container).offset().left + $(settings.container).width();
        }

        return fold <= $(element).offset().left - settings.threshold;
    };

    $.abovethetop = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.scrollTop();
        } else {
            fold = $(settings.container).offset().top;
        }

        return fold >= $(element).offset().top + settings.threshold  + $(element).height();
    };

    $.leftofbegin = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.scrollLeft();
        } else {
            fold = $(settings.container).offset().left;
        }

        return fold >= $(element).offset().left + settings.threshold + $(element).width();
    };

    $.inviewport = function(element, settings) {
         return !$.rightoffold(element, settings) && !$.leftofbegin(element, settings) &&
                !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
     };

    /* Custom selectors for your convenience.   */
    /* Use as $("img:below-the-fold").something() or */
    /* $("img").filter(":below-the-fold").something() which is faster */

    $.extend($.expr[":"], {
        "below-the-fold" : function(a) { return $.belowthefold(a, {threshold : 0}); },
        "above-the-top"  : function(a) { return !$.belowthefold(a, {threshold : 0}); },
        "right-of-screen": function(a) { return $.rightoffold(a, {threshold : 0}); },
        "left-of-screen" : function(a) { return !$.rightoffold(a, {threshold : 0}); },
        "in-viewport"    : function(a) { return $.inviewport(a, {threshold : 0}); },
        /* Maintain BC for couple of versions. */
        "above-the-fold" : function(a) { return !$.belowthefold(a, {threshold : 0}); },
        "right-of-fold"  : function(a) { return $.rightoffold(a, {threshold : 0}); },
        "left-of-fold"   : function(a) { return !$.rightoffold(a, {threshold : 0}); }
    });

})(jQuery, window, document);

function backtotop() {
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

$(window).scroll(function () {
    var $this = $(this);
    if ($this.scrollTop() > 500) {
       $('.scrollTop').fadeIn();
    } else {
       $('.scrollTop').fadeOut();
    }
});

$("img").not('.lazyloaded').lazyload({
    effect : "fadeIn",
    effectTime: 2000,
    threshold: 150
}).addClass('lazyloaded');

//Loadmore Posts
function fetchPosts() {
    var start = $('#articles-container').attr('start');
    var total = $('#articles-container').attr('total');
    if(parseInt(start) > 0 && parseInt(start) < parseInt(total)) {
        clearTimeout($.data(this, "scrollCheck"));
        $.data(this, "scrollCheck", setTimeout(function() {
            var bottom_page = $(window).height() + $(window).scrollTop() + 100;
            if(bottom_page >= $(document).height()) {
                $('.form-group.loading').css('display', 'block');
                $.get('?start='+start, function(data) {
                    $('#articles-container').append(data.posts);
                    $('#articles-container').attr('start', data.cursor);
                    $('.form-group.loading').css('display', 'none');
                    $("img").not('.lazyloaded').lazyload({
                        effect : "fadeIn",
                        effectTime: 2000,
                        threshold: 150
                    }).addClass('lazyloaded');
                });
            }
        }, 500))
    }
}
// function fetchPosts() {
//     var next_page = $('#articles-container').attr('nextpage');
//     var hasmore_page = $('#articles-container').attr('hasmore');
//     if(next_page != null && hasmore_page == 1) {
//         clearTimeout($.data(this, "scrollCheck"));
//         $.data(this, "scrollCheck", setTimeout(function() {
//             var bottom_page = $(window).height() + $(window).scrollTop() + 100;
//             if(bottom_page >= $(document).height()) {
//                 $('.form-group.loading').css('display', 'block');
//                 $.get(next_page, function(data) {
//                     $('#articles-container').append(data.posts);
//                     $('#articles-container').attr('nextpage', data.next_page);
//                     $('#articles-container').attr('page', data.current_page);
//                     $('#articles-container').attr('hasmore', data.hasmore_page);
//                     $('.form-group.loading').css('display', 'none');
//                     //Uncomment nếu muốn mỗi khi load dữ liệu xong sẽ tự động scroll đến item đầu tiên vừa load
//                     //$('html, body').animate({ scrollTop: $('#article-'+data.first_item).offset().top - 60 }, 'slow');
//                 });
//             }
//         }, 500))
//     }
// }
//\Loadmore posts

//rê chuột hiện thông tin author ngoài trang chủ
// var get_author_info;
// $(document).on({
//     mouseenter: function() {
//         var user_dropdown = $(this).find('[data_render_container]');
//         var user_slug = $(user_dropdown).attr('data-user');
//         // get_author_info = window.setTimeout(function() {
//         //     $(user_dropdown).find('[data_render_main]').html('<i class="fa fa-spinner fa-spin"></i>');
//         //     $(user_dropdown).show().animate({opacity:1},1000, function(){                
//         //         $.get(`/u/${user_slug}`, function(data) {
//         //             $(user_dropdown).find('[data_render_main]').html(data.user);
//         //         });
//         //     });
//         // }, 750)
//         $(user_dropdown).delay(500).fadeTo(500, 1).animate({opacity:1},500, function(){
//             $.get(`/u/${user_slug}`, function(data) {
//                 $(user_dropdown).find('[data_render_main]').html(data.user);
//             });
//         });
//     },
//     mouseleave: function() {
//         $('[data_render_container]').delay(750).fadeOut();
//         // $('[data_render_container]').stop(true, true).css("display", "none");
//         setTimeout(function() {            
//             // $('[data_render_main]').html('');
//              window.clearTimeout(get_author_info);
//         }, 1000);
//     }
// }, '.author_hover');
//Kết thúc phần rê chuột


!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):e.Sweetalert2=t()}(this,function(){"use strict";var e={title:"",titleText:"",text:"",html:"",type:null,customClass:"",target:"body",animation:!0,allowOutsideClick:!0,allowEscapeKey:!0,allowEnterKey:!0,showConfirmButton:!0,showCancelButton:!1,preConfirm:null,confirmButtonText:"OK",confirmButtonAriaLabel:"",confirmButtonColor:"#3085d6",confirmButtonClass:null,cancelButtonText:"Cancel",cancelButtonAriaLabel:"",cancelButtonColor:"#aaa",cancelButtonClass:null,buttonsStyling:!0,reverseButtons:!1,focusConfirm:!0,focusCancel:!1,showCloseButton:!1,closeButtonAriaLabel:"Close this dialog",showLoaderOnConfirm:!1,imageUrl:null,imageWidth:null,imageHeight:null,imageAlt:"",imageClass:null,timer:null,width:500,padding:20,background:"#fff",input:null,inputPlaceholder:"",inputValue:"",inputOptions:{},inputAutoTrim:!0,inputClass:null,inputAttributes:{},inputValidator:null,grow:!1,position:"center",progressSteps:[],currentProgressStep:null,progressStepsDistance:"40px",onBeforeOpen:null,onOpen:null,onClose:null,useRejections:!0},t=function(e){var t={};for(var n in e)t[e[n]]="swal2-"+e[n];return t},n=t(["container","shown","iosfix","modal","overlay","fade","show","hide","noanimation","close","title","content","buttonswrapper","confirm","cancel","icon","image","input","file","range","select","radio","checkbox","textarea","inputerror","validationerror","progresssteps","activeprogressstep","progresscircle","progressline","loading","styled","top","top-left","top-right","center","center-left","center-right","bottom","bottom-left","bottom-right","grow-row","grow-column","grow-fullscreen"]),o=t(["success","warning","info","question","error"]),r=function(e,t){(e=String(e).replace(/[^0-9a-f]/gi,"")).length<6&&(e=e[0]+e[0]+e[1]+e[1]+e[2]+e[2]),t=t||0;for(var n="#",o=0;o<3;o++){var r=parseInt(e.substr(2*o,2),16);n+=("00"+(r=Math.round(Math.min(Math.max(0,r+r*t),255)).toString(16))).substr(r.length)}return n},i=function(e){console.warn("SweetAlert2: "+e)},a=function(e){console.error("SweetAlert2: "+e)},l={previousWindowKeyDown:null,previousActiveElement:null,previousBodyPadding:null},s=function(e){var t=c();t&&t.parentNode.removeChild(t);{if("undefined"!=typeof document){var o=document.createElement("div");o.className=n.container,o.innerHTML=u;("string"==typeof e.target?document.querySelector(e.target):e.target).appendChild(o);var r=d(),i=E(r,n.input),l=E(r,n.file),s=r.querySelector("."+n.range+" input"),p=r.querySelector("."+n.range+" output"),f=E(r,n.select),m=r.querySelector("."+n.checkbox+" input"),g=E(r,n.textarea);return i.oninput=function(){I.resetValidationError()},l.onchange=function(){I.resetValidationError()},s.oninput=function(){I.resetValidationError(),p.value=s.value},s.onchange=function(){I.resetValidationError(),s.previousSibling.value=s.value},f.onchange=function(){I.resetValidationError()},m.onchange=function(){I.resetValidationError()},g.oninput=function(){I.resetValidationError()},r}a("SweetAlert2 requires document to initialize")}},u=('\n <div role="dialog" aria-modal="true" aria-labelledby="'+n.title+'" aria-describedby="'+n.content+'" class="'+n.modal+'" tabindex="-1">\n   <ul class="'+n.progresssteps+'"></ul>\n   <div class="'+n.icon+" "+o.error+'">\n     <span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span>\n   </div>\n   <div class="'+n.icon+" "+o.question+'">?</div>\n   <div class="'+n.icon+" "+o.warning+'">!</div>\n   <div class="'+n.icon+" "+o.info+'">i</div>\n   <div class="'+n.icon+" "+o.success+'">\n     <div class="swal2-success-circular-line-left"></div>\n     <span class="swal2-success-line-tip"></span> <span class="swal2-success-line-long"></span>\n     <div class="swal2-success-ring"></div> <div class="swal2-success-fix"></div>\n     <div class="swal2-success-circular-line-right"></div>\n   </div>\n   <img class="'+n.image+'" />\n   <h2 class="'+n.title+'" id="'+n.title+'"></h2>\n   <div id="'+n.content+'" class="'+n.content+'"></div>\n   <input class="'+n.input+'" />\n   <input type="file" class="'+n.file+'" />\n   <div class="'+n.range+'">\n     <output></output>\n     <input type="range" />\n   </div>\n   <select class="'+n.select+'"></select>\n   <div class="'+n.radio+'"></div>\n   <label for="'+n.checkbox+'" class="'+n.checkbox+'">\n     <input type="checkbox" />\n   </label>\n   <textarea class="'+n.textarea+'"></textarea>\n   <div class="'+n.validationerror+'" id="'+n.validationerror+'"></div>\n   <div class="'+n.buttonswrapper+'">\n     <button type="button" class="'+n.confirm+'">OK</button>\n     <button type="button" class="'+n.cancel+'">Cancel</button>\n   </div>\n   <button type="button" class="'+n.close+'">×</button>\n </div>\n').replace(/(^|\n)\s*/g,""),c=function(){return document.body.querySelector("."+n.container)},d=function(){return c()?c().querySelector("."+n.modal):null},p=function(e){return c()?c().querySelector("."+e):null},f=function(){return p(n.title)},m=function(){return p(n.content)},g=function(){return p(n.image)},b=function(){return p(n.progresssteps)},v=function(){return p(n.validationerror)},h=function(){return p(n.confirm)},y=function(){return p(n.cancel)},w=function(){return p(n.buttonswrapper)},C=function(){return p(n.close)},k=function(){var e=Array.from(d().querySelectorAll('[tabindex]:not([tabindex="-1"]):not([tabindex="0"])')).sort(function(e,t){return e=parseInt(e.getAttribute("tabindex")),t=parseInt(t.getAttribute("tabindex")),e>t?1:e<t?-1:0}),t=Array.prototype.slice.call(d().querySelectorAll('button, input:not([type=hidden]), textarea, select, a, [tabindex="0"]'));return function(e){var t=[];for(var n in e)-1===t.indexOf(e[n])&&t.push(e[n]);return t}(e.concat(t))},x=function(e,t){return!!e.classList&&e.classList.contains(t)},S=function(e){if(e.focus(),"file"!==e.type){var t=e.value;e.value="",e.value=t}},A=function(e,t){if(e&&t){t.split(/\s+/).filter(Boolean).forEach(function(t){e.classList.add(t)})}},B=function(e,t){if(e&&t){t.split(/\s+/).filter(Boolean).forEach(function(t){e.classList.remove(t)})}},E=function(e,t){for(var n=0;n<e.childNodes.length;n++)if(x(e.childNodes[n],t))return e.childNodes[n]},P=function(e,t){t||(t="block"),e.style.opacity="",e.style.display=t},L=function(e){e.style.opacity="",e.style.display="none"},T=function(e){return e.offsetWidth||e.offsetHeight||e.getClientRects().length},q=function(){var e=document.createElement("div"),t={WebkitAnimation:"webkitAnimationEnd",OAnimation:"oAnimationEnd oanimationend",animation:"animationend"};for(var n in t)if(t.hasOwnProperty(n)&&void 0!==e.style[n])return t[n];return!1}(),V="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},O=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e},M=O({},e),N=[];"undefined"==typeof Promise&&a("This package requires a Promise library, please include a shim to enable it in this browser (See: https://github.com/limonte/sweetalert2/wiki/Migration-from-SweetAlert-to-SweetAlert2#1-ie-support)");var H=function(e){("string"==typeof e.target&&!document.querySelector(e.target)||"string"!=typeof e.target&&!e.target.appendChild)&&(i('Target parameter is not valid, defaulting to "body"'),e.target="body");var t=void 0,r=d(),l="string"==typeof e.target?document.querySelector(e.target):e.target;t=r&&l&&r.parentNode!==l.parentNode?s(e):r||s(e);for(var u in e)I.isValidParameter(u)||i('Unknown parameter "'+u+'"');t.style.width="number"==typeof e.width?e.width+"px":e.width,t.style.padding=e.padding+"px",t.style.background=e.background;for(var p=t.querySelectorAll("[class^=swal2-success-circular-line], .swal2-success-fix"),v=0;v<p.length;v++)p[v].style.background=e.background;var k=c(),x=f(),S=m(),E=w(),T=h(),q=y(),O=C();if(e.titleText?x.innerText=e.titleText:x.innerHTML=e.title.split("\n").join("<br />"),e.text||e.html){if("object"===V(e.html))if(S.innerHTML="",0 in e.html)for(var M=0;M in e.html;M++)S.appendChild(e.html[M].cloneNode(!0));else S.appendChild(e.html.cloneNode(!0));else e.html?S.innerHTML=e.html:e.text&&(S.textContent=e.text);P(S)}else L(S);if(e.position in n&&A(k,n[e.position]),e.grow&&"string"==typeof e.grow){var N="grow-"+e.grow;N in n&&A(k,n[N])}e.showCloseButton?(O.setAttribute("aria-label",e.closeButtonAriaLabel),P(O)):L(O),t.className=n.modal,e.customClass&&A(t,e.customClass);var H=b(),j=parseInt(null===e.currentProgressStep?I.getQueueStep():e.currentProgressStep,10);e.progressSteps.length?(P(H),function(e){for(;e.firstChild;)e.removeChild(e.firstChild)}(H),j>=e.progressSteps.length&&i("Invalid currentProgressStep parameter, it should be less than progressSteps.length (currentProgressStep like JS arrays starts from 0)"),e.progressSteps.forEach(function(t,o){var r=document.createElement("li");if(A(r,n.progresscircle),r.innerHTML=t,o===j&&A(r,n.activeprogressstep),H.appendChild(r),o!==e.progressSteps.length-1){var i=document.createElement("li");A(i,n.progressline),i.style.width=e.progressStepsDistance,H.appendChild(i)}})):L(H);for(var R=d().querySelectorAll("."+n.icon),U=0;U<R.length;U++)L(R[U]);if(e.type){var W=!1;for(var D in o)if(e.type===D){W=!0;break}if(!W)return a("Unknown alert type: "+e.type),!1;var K=t.querySelector("."+n.icon+"."+o[e.type]);if(P(K),e.animation)switch(e.type){case"success":A(K,"swal2-animate-success-icon"),A(K.querySelector(".swal2-success-line-tip"),"swal2-animate-success-line-tip"),A(K.querySelector(".swal2-success-line-long"),"swal2-animate-success-line-long");break;case"error":A(K,"swal2-animate-error-icon"),A(K.querySelector(".swal2-x-mark"),"swal2-animate-x-mark")}}var z=g();e.imageUrl?(z.setAttribute("src",e.imageUrl),z.setAttribute("alt",e.imageAlt),P(z),e.imageWidth?z.setAttribute("width",e.imageWidth):z.removeAttribute("width"),e.imageHeight?z.setAttribute("height",e.imageHeight):z.removeAttribute("height"),z.className=n.image,e.imageClass&&A(z,e.imageClass)):L(z),e.showCancelButton?q.style.display="inline-block":L(q),e.showConfirmButton?function(e,t){e.style.removeProperty?e.style.removeProperty(t):e.style.removeAttribute(t)}(T,"display"):L(T),e.showConfirmButton||e.showCancelButton?P(E):L(E),T.innerHTML=e.confirmButtonText,q.innerHTML=e.cancelButtonText,T.setAttribute("aria-label",e.confirmButtonAriaLabel),q.setAttribute("aria-label",e.cancelButtonAriaLabel),e.buttonsStyling&&(T.style.backgroundColor=e.confirmButtonColor,q.style.backgroundColor=e.cancelButtonColor),T.className=n.confirm,A(T,e.confirmButtonClass),q.className=n.cancel,A(q,e.cancelButtonClass),e.buttonsStyling?(A(T,n.styled),A(q,n.styled)):(B(T,n.styled),B(q,n.styled),T.style.backgroundColor=T.style.borderLeftColor=T.style.borderRightColor="",q.style.backgroundColor=q.style.borderLeftColor=q.style.borderRightColor=""),!0===e.animation?B(t,n.noanimation):A(t,n.noanimation),e.showLoaderOnConfirm&&!e.preConfirm&&i("showLoaderOnConfirm is set to true, but preConfirm is not defined.\nshowLoaderOnConfirm should be used together with preConfirm, see usage example:\nhttps://limonte.github.io/sweetalert2/#ajax-request")},j=function(){null===l.previousBodyPadding&&document.body.scrollHeight>window.innerHeight&&(l.previousBodyPadding=document.body.style.paddingRight,document.body.style.paddingRight=function(){if("ontouchstart"in window||navigator.msMaxTouchPoints)return 0;var e=document.createElement("div");e.style.width="50px",e.style.height="50px",e.style.overflow="scroll",document.body.appendChild(e);var t=e.offsetWidth-e.clientWidth;return document.body.removeChild(e),t}()+"px")},R=function(){if(/iPad|iPhone|iPod/.test(navigator.userAgent)&&!window.MSStream&&!x(document.body,n.iosfix)){var e=document.body.scrollTop;document.body.style.top=-1*e+"px",A(document.body,n.iosfix)}},I=function e(){for(var t=arguments.length,o=Array(t),i=0;i<t;i++)o[i]=arguments[i];if(void 0===o[0])return a("SweetAlert2 expects at least 1 attribute!"),!1;var s=O({},M);switch(V(o[0])){case"string":s.title=o[0],s.html=o[1],s.type=o[2];break;case"object":O(s,o[0]),s.extraParams=o[0].extraParams,"email"===s.input&&null===s.inputValidator&&(s.inputValidator=function(e){return new Promise(function(t,n){/^[a-zA-Z0-9.+_-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(e)?t():n("Invalid email address")})}),"url"===s.input&&null===s.inputValidator&&(s.inputValidator=function(e){return new Promise(function(t,n){/^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_+.~#?&//=]*)$/.test(e)?t():n("Invalid URL")})});break;default:return a('Unexpected type of argument! Expected "string" or "object", got '+V(o[0])),!1}H(s);var u=c(),p=d();return new Promise(function(t,o){s.timer&&(p.timeout=setTimeout(function(){e.closeModal(s.onClose),s.useRejections?o("timer"):t({dismiss:"timer"})},s.timer));var i=function(e){if(!(e=e||s.input))return null;switch(e){case"select":case"textarea":case"file":return E(p,n[e]);case"checkbox":return p.querySelector("."+n.checkbox+" input");case"radio":return p.querySelector("."+n.radio+" input:checked")||p.querySelector("."+n.radio+" input:first-child");case"range":return p.querySelector("."+n.range+" input");default:return E(p,n.input)}};s.input&&setTimeout(function(){var e=i();e&&S(e)},0);for(var O=function(n){s.showLoaderOnConfirm&&e.showLoading(),s.preConfirm?s.preConfirm(n,s.extraParams).then(function(o){e.closeModal(s.onClose),t(o||n)},function(t){e.hideLoading(),t&&e.showValidationError(t)}):(e.closeModal(s.onClose),t(s.useRejections?n:{value:n}))},M=function(n){var a=n||window.event,l=a.target||a.srcElement,u=h(),c=y(),d=u&&(u===l||u.contains(l)),p=c&&(c===l||c.contains(l));switch(a.type){case"mouseover":case"mouseup":s.buttonsStyling&&(d?u.style.backgroundColor=r(s.confirmButtonColor,-.1):p&&(c.style.backgroundColor=r(s.cancelButtonColor,-.1)));break;case"mouseout":s.buttonsStyling&&(d?u.style.backgroundColor=s.confirmButtonColor:p&&(c.style.backgroundColor=s.cancelButtonColor));break;case"mousedown":s.buttonsStyling&&(d?u.style.backgroundColor=r(s.confirmButtonColor,-.2):p&&(c.style.backgroundColor=r(s.cancelButtonColor,-.2)));break;case"click":if(d&&e.isVisible())if(e.disableButtons(),s.input){var f=function(){var e=i();if(!e)return null;switch(s.input){case"checkbox":return e.checked?1:0;case"radio":return e.checked?e.value:null;case"file":return e.files.length?e.files[0]:null;default:return s.inputAutoTrim?e.value.trim():e.value}}();s.inputValidator?(e.disableInput(),s.inputValidator(f,s.extraParams).then(function(){e.enableButtons(),e.enableInput(),O(f)},function(t){e.enableButtons(),e.enableInput(),t&&e.showValidationError(t)})):O(f)}else O(!0);else p&&e.isVisible()&&(e.disableButtons(),e.closeModal(s.onClose),s.useRejections?o("cancel"):t({dismiss:"cancel"}))}},N=p.querySelectorAll("button"),I=0;I<N.length;I++)N[I].onclick=M,N[I].onmouseover=M,N[I].onmouseout=M,N[I].onmousedown=M;C().onclick=function(){e.closeModal(s.onClose),s.useRejections?o("close"):t({dismiss:"close"})},u.onclick=function(n){n.target===u&&s.allowOutsideClick&&(e.closeModal(s.onClose),s.useRejections?o("overlay"):t({dismiss:"overlay"}))};var U=w(),W=h(),D=y();s.reverseButtons?W.parentNode.insertBefore(D,W):W.parentNode.insertBefore(W,D);var K=function(e,t){for(var n=k(s.focusCancel),o=0;o<n.length;o++){(e+=t)===n.length?e=0:-1===e&&(e=n.length-1);var r=n[e];if(T(r))return r.focus()}},z=function(n){var r=n||window.event;if("Enter"===r.key){if(r.target===i()){if("textarea"===r.target.tagName.toLowerCase())return;e.clickConfirm(),r.preventDefault()}}else if("Tab"===r.key){for(var a=r.target||r.srcElement,l=k(s.focusCancel),u=-1,c=0;c<l.length;c++)if(a===l[c]){u=c;break}r.shiftKey?K(u,-1):K(u,1),r.stopPropagation(),r.preventDefault()}else-1!==["ArrowLeft","ArrowRight","ArrowUp","Arrowdown"].indexOf(r.key)?document.activeElement===W&&T(D)?D.focus():document.activeElement===D&&T(W)&&W.focus():"Escape"!==r.key&&"Esc"!==r.key||!0!==s.allowEscapeKey||(e.closeModal(s.onClose),s.useRejections?o("esc"):t({dismiss:"esc"}))};window.onkeydown&&window.onkeydown.toString()===z.toString()||(l.previousWindowKeyDown=window.onkeydown,window.onkeydown=z),s.buttonsStyling&&(W.style.borderLeftColor=s.confirmButtonColor,W.style.borderRightColor=s.confirmButtonColor),e.hideLoading=e.disableLoading=function(){s.showConfirmButton||(L(W),s.showCancelButton||L(w())),B(U,n.loading),B(p,n.loading),p.removeAttribute("aria-busy"),W.disabled=!1,D.disabled=!1},e.getTitle=function(){return f()},e.getContent=function(){return m()},e.getInput=function(){return i()},e.getImage=function(){return g()},e.getButtonsWrapper=function(){return w()},e.getConfirmButton=function(){return h()},e.getCancelButton=function(){return y()},e.enableButtons=function(){W.disabled=!1,D.disabled=!1},e.disableButtons=function(){W.disabled=!0,D.disabled=!0},e.enableConfirmButton=function(){W.disabled=!1},e.disableConfirmButton=function(){W.disabled=!0},e.enableInput=function(){var e=i();if(!e)return!1;if("radio"===e.type)for(var t=e.parentNode.parentNode.querySelectorAll("input"),n=0;n<t.length;n++)t[n].disabled=!1;else e.disabled=!1},e.disableInput=function(){var e=i();if(!e)return!1;if(e&&"radio"===e.type)for(var t=e.parentNode.parentNode.querySelectorAll("input"),n=0;n<t.length;n++)t[n].disabled=!0;else e.disabled=!0},e.showValidationError=function(e){var t=v();t.innerHTML=e,P(t);var o=i();o&&(o.setAttribute("aria-invalid",!0),o.setAttribute("aria-describedBy",n.validationerror),S(o),A(o,n.inputerror))},e.resetValidationError=function(){var e=v();L(e);var t=i();t&&(t.removeAttribute("aria-invalid"),t.removeAttribute("aria-describedBy"),B(t,n.inputerror))},e.getProgressSteps=function(){return s.progressSteps},e.setProgressSteps=function(e){s.progressSteps=e,H(s)},e.showProgressSteps=function(){P(b())},e.hideProgressSteps=function(){L(b())},e.enableButtons(),e.hideLoading(),e.resetValidationError();for(var Z=["input","file","range","select","radio","checkbox","textarea"],Q=void 0,Y=0;Y<Z.length;Y++){var _=n[Z[Y]],$=E(p,_);if(Q=i(Z[Y])){for(var J in Q.attributes)if(Q.attributes.hasOwnProperty(J)){var X=Q.attributes[J].name;"type"!==X&&"value"!==X&&Q.removeAttribute(X)}for(var F in s.inputAttributes)Q.setAttribute(F,s.inputAttributes[F])}$.className=_,s.inputClass&&A($,s.inputClass),L($)}var G=void 0;switch(s.input){case"text":case"email":case"password":case"number":case"tel":case"url":(Q=E(p,n.input)).value=s.inputValue,Q.placeholder=s.inputPlaceholder,Q.type=s.input,P(Q);break;case"file":(Q=E(p,n.file)).placeholder=s.inputPlaceholder,Q.type=s.input,P(Q);break;case"range":var ee=E(p,n.range),te=ee.querySelector("input"),ne=ee.querySelector("output");te.value=s.inputValue,te.type=s.input,ne.value=s.inputValue,P(ee);break;case"select":var oe=E(p,n.select);if(oe.innerHTML="",s.inputPlaceholder){var re=document.createElement("option");re.innerHTML=s.inputPlaceholder,re.value="",re.disabled=!0,re.selected=!0,oe.appendChild(re)}G=function(e){for(var t in e){var n=document.createElement("option");n.value=t,n.innerHTML=e[t],s.inputValue.toString()===t&&(n.selected=!0),oe.appendChild(n)}P(oe),oe.focus()};break;case"radio":var ie=E(p,n.radio);ie.innerHTML="",G=function(e){for(var t in e){var o=document.createElement("input"),r=document.createElement("label"),i=document.createElement("span");o.type="radio",o.name=n.radio,o.value=t,s.inputValue.toString()===t&&(o.checked=!0),i.innerHTML=e[t],r.appendChild(o),r.appendChild(i),r.for=o.id,ie.appendChild(r)}P(ie);var a=ie.querySelectorAll("input");a.length&&a[0].focus()};break;case"checkbox":var ae=E(p,n.checkbox),le=i("checkbox");le.type="checkbox",le.value=1,le.id=n.checkbox,le.checked=Boolean(s.inputValue);var se=ae.getElementsByTagName("span");se.length&&ae.removeChild(se[0]),(se=document.createElement("span")).innerHTML=s.inputPlaceholder,ae.appendChild(se),P(ae);break;case"textarea":var ue=E(p,n.textarea);ue.value=s.inputValue,ue.placeholder=s.inputPlaceholder,P(ue);break;case null:break;default:a('Unexpected type of input! Expected "text", "email", "password", "number", "tel", "select", "radio", "checkbox", "textarea", "file" or "url", got "'+s.input+'"')}"select"!==s.input&&"radio"!==s.input||(s.inputOptions instanceof Promise?(e.showLoading(),s.inputOptions.then(function(t){e.hideLoading(),G(t)})):"object"===V(s.inputOptions)?G(s.inputOptions):a("Unexpected type of inputOptions! Expected object or Promise, got "+V(s.inputOptions))),function(e,t,o){var r=c(),i=d();null!==t&&"function"==typeof t&&t(i),e?(A(i,n.show),A(r,n.fade),B(i,n.hide)):B(i,n.fade),P(i),r.style.overflowY="hidden",q&&!x(i,n.noanimation)?i.addEventListener(q,function e(){i.removeEventListener(q,e),r.style.overflowY="auto"}):r.style.overflowY="auto",A(document.documentElement,n.shown),A(document.body,n.shown),A(r,n.shown),j(),R(),l.previousActiveElement=document.activeElement,null!==o&&"function"==typeof o&&setTimeout(function(){o(i)})}(s.animation,s.onBeforeOpen,s.onOpen),s.allowEnterKey?s.focusCancel&&T(D)?D.focus():s.focusConfirm&&T(W)?W.focus():K(-1,1):document.activeElement&&document.activeElement.blur(),c().scrollTop=0})};return I.isVisible=function(){return!!d()},I.queue=function(e){N=e;var t=function(){N=[],document.body.removeAttribute("data-swal2-queue-step")},n=[];return new Promise(function(e,o){!function r(i,a){i<N.length?(document.body.setAttribute("data-swal2-queue-step",i),I(N[i]).then(function(e){n.push(e),r(i+1,a)},function(e){t(),o(e)})):(t(),e(n))}(0)})},I.getQueueStep=function(){return document.body.getAttribute("data-swal2-queue-step")},I.insertQueueStep=function(e,t){return t&&t<N.length?N.splice(t,0,e):N.push(e)},I.deleteQueueStep=function(e){void 0!==N[e]&&N.splice(e,1)},I.close=I.closeModal=function(e){var t=c(),o=d();if(o){B(o,n.show),A(o,n.hide),clearTimeout(o.timeout),function(){if(window.onkeydown=l.previousWindowKeyDown,l.previousActiveElement&&l.previousActiveElement.focus){var e=window.scrollX,t=window.scrollY;l.previousActiveElement.focus(),e&&t&&window.scrollTo(e,t)}}();var r=function(){t.parentNode&&t.parentNode.removeChild(t),B(document.documentElement,n.shown),B(document.body,n.shown),null!==l.previousBodyPadding&&(document.body.style.paddingRight=l.previousBodyPadding,l.previousBodyPadding=null),function(){if(x(document.body,n.iosfix)){var e=parseInt(document.body.style.top,10);B(document.body,n.iosfix),document.body.style.top="",document.body.scrollTop=-1*e}}()};q&&!x(o,n.noanimation)?o.addEventListener(q,function e(){o.removeEventListener(q,e),x(o,n.hide)&&r()}):r(),null!==e&&"function"==typeof e&&setTimeout(function(){e(o)})}},I.clickConfirm=function(){return h().click()},I.clickCancel=function(){return y().click()},I.showLoading=I.enableLoading=function(){var e=d();e||I(""),e=d();var t=w(),o=h(),r=y();P(t),P(o,"inline-block"),A(t,n.loading),A(e,n.loading),o.disabled=!0,r.disabled=!0,e.setAttribute("aria-busy",!0),e.focus()},I.isValidParameter=function(t){return e.hasOwnProperty(t)||"extraParams"===t},I.setDefaults=function(e){if(!e||"object"!==(void 0===e?"undefined":V(e)))return a("the argument for setDefaults() is required and has to be a object");for(var t in e)I.isValidParameter(t)||(i('Unknown parameter "'+t+'"'),delete e[t]);O(M,e)},I.resetDefaults=function(){M=O({},e)},I.noop=function(){},I.version="6.11.5",I.default=I,I}),window.Sweetalert2&&(window.sweetAlert=window.swal=window.Sweetalert2);
/*! jquery-qrcode v0.14.0 - https://larsjung.de/jquery-qrcode/ */
(function (vendor_qrcode) {
    'use strict';

    var jq = window.jQuery;

    // Check if canvas is available in the browser (as Modernizr does)
    var hasCanvas = (function () {
        var elem = document.createElement('canvas');
        return !!(elem.getContext && elem.getContext('2d'));
    }());

    // Wrapper for the original QR code generator.
    function createQRCode(text, level, version, quiet) {
        var qr = {};

        var vqr = vendor_qrcode(version, level);
        vqr.addData(text);
        vqr.make();

        quiet = quiet || 0;

        var qrModuleCount = vqr.getModuleCount();
        var quietModuleCount = vqr.getModuleCount() + 2 * quiet;

        function isDark(row, col) {
            row -= quiet;
            col -= quiet;

            if (row < 0 || row >= qrModuleCount || col < 0 || col >= qrModuleCount) {
                return false;
            }
            return vqr.isDark(row, col);
        }

        function addBlank(l, t, r, b) {
            var prevIsDark = qr.isDark;
            var moduleSize = 1 / quietModuleCount;

            qr.isDark = function (row, col) {
                var ml = col * moduleSize;
                var mt = row * moduleSize;
                var mr = ml + moduleSize;
                var mb = mt + moduleSize;

                return prevIsDark(row, col) && (l > mr || ml > r || t > mb || mt > b);
            };
        }

        qr.text = text;
        qr.level = level;
        qr.version = version;
        qr.moduleCount = quietModuleCount;
        qr.isDark = isDark;
        qr.addBlank = addBlank;

        return qr;
    }

    // Returns a minimal QR code for the given text starting with version `minVersion`.
    // Returns `undefined` if `text` is too long to be encoded in `maxVersion`.
    function createMinQRCode(text, level, minVersion, maxVersion, quiet) {
        minVersion = Math.max(1, minVersion || 1);
        maxVersion = Math.min(40, maxVersion || 40);
        for (var version = minVersion; version <= maxVersion; version += 1) {
            try {
                return createQRCode(text, level, version, quiet);
            } catch (err) {/* empty */}
        }
        return undefined;
    }

    function drawBackgroundLabel(qr, context, settings) {
        var size = settings.size;
        var font = 'bold ' + settings.mSize * size + 'px ' + settings.fontname;
        var ctx = jq('<canvas/>')[0].getContext('2d');

        ctx.font = font;

        var w = ctx.measureText(settings.label).width;
        var sh = settings.mSize;
        var sw = w / size;
        var sl = (1 - sw) * settings.mPosX;
        var st = (1 - sh) * settings.mPosY;
        var sr = sl + sw;
        var sb = st + sh;
        var pad = 0.01;

        if (settings.mode === 1) {
            // Strip
            qr.addBlank(0, st - pad, size, sb + pad);
        } else {
            // Box
            qr.addBlank(sl - pad, st - pad, sr + pad, sb + pad);
        }

        context.fillStyle = settings.fontcolor;
        context.font = font;
        context.fillText(settings.label, sl * size, st * size + 0.75 * settings.mSize * size);
    }

    function drawBackgroundImage(qr, context, settings) {
        var size = settings.size;
        var w = settings.image.naturalWidth || 1;
        var h = settings.image.naturalHeight || 1;
        var sh = settings.mSize;
        var sw = sh * w / h;
        var sl = (1 - sw) * settings.mPosX;
        var st = (1 - sh) * settings.mPosY;
        var sr = sl + sw;
        var sb = st + sh;
        var pad = 0.01;

        if (settings.mode === 3) {
            // Strip
            qr.addBlank(0, st - pad, size, sb + pad);
        } else {
            // Box
            qr.addBlank(sl - pad, st - pad, sr + pad, sb + pad);
        }

        context.drawImage(settings.image, sl * size, st * size, sw * size, sh * size);
    }

    function drawBackground(qr, context, settings) {
        if (jq(settings.background).is('img')) {
            context.drawImage(settings.background, 0, 0, settings.size, settings.size);
        } else if (settings.background) {
            context.fillStyle = settings.background;
            context.fillRect(settings.left, settings.top, settings.size, settings.size);
        }

        var mode = settings.mode;
        if (mode === 1 || mode === 2) {
            drawBackgroundLabel(qr, context, settings);
        } else if (mode === 3 || mode === 4) {
            drawBackgroundImage(qr, context, settings);
        }
    }

    function drawModuleDefault(qr, context, settings, left, top, width, row, col) {
        if (qr.isDark(row, col)) {
            context.rect(left, top, width, width);
        }
    }

    function drawModuleRoundedDark(ctx, l, t, r, b, rad, nw, ne, se, sw) {
        if (nw) {
            ctx.moveTo(l + rad, t);
        } else {
            ctx.moveTo(l, t);
        }

        if (ne) {
            ctx.lineTo(r - rad, t);
            ctx.arcTo(r, t, r, b, rad);
        } else {
            ctx.lineTo(r, t);
        }

        if (se) {
            ctx.lineTo(r, b - rad);
            ctx.arcTo(r, b, l, b, rad);
        } else {
            ctx.lineTo(r, b);
        }

        if (sw) {
            ctx.lineTo(l + rad, b);
            ctx.arcTo(l, b, l, t, rad);
        } else {
            ctx.lineTo(l, b);
        }

        if (nw) {
            ctx.lineTo(l, t + rad);
            ctx.arcTo(l, t, r, t, rad);
        } else {
            ctx.lineTo(l, t);
        }
    }

    function drawModuleRoundendLight(ctx, l, t, r, b, rad, nw, ne, se, sw) {
        if (nw) {
            ctx.moveTo(l + rad, t);
            ctx.lineTo(l, t);
            ctx.lineTo(l, t + rad);
            ctx.arcTo(l, t, l + rad, t, rad);
        }

        if (ne) {
            ctx.moveTo(r - rad, t);
            ctx.lineTo(r, t);
            ctx.lineTo(r, t + rad);
            ctx.arcTo(r, t, r - rad, t, rad);
        }

        if (se) {
            ctx.moveTo(r - rad, b);
            ctx.lineTo(r, b);
            ctx.lineTo(r, b - rad);
            ctx.arcTo(r, b, r - rad, b, rad);
        }

        if (sw) {
            ctx.moveTo(l + rad, b);
            ctx.lineTo(l, b);
            ctx.lineTo(l, b - rad);
            ctx.arcTo(l, b, l + rad, b, rad);
        }
    }

    function drawModuleRounded(qr, context, settings, left, top, width, row, col) {
        var isDark = qr.isDark;
        var right = left + width;
        var bottom = top + width;
        var radius = settings.radius * width;
        var rowT = row - 1;
        var rowB = row + 1;
        var colL = col - 1;
        var colR = col + 1;
        var center = isDark(row, col);
        var northwest = isDark(rowT, colL);
        var north = isDark(rowT, col);
        var northeast = isDark(rowT, colR);
        var east = isDark(row, colR);
        var southeast = isDark(rowB, colR);
        var south = isDark(rowB, col);
        var southwest = isDark(rowB, colL);
        var west = isDark(row, colL);

        if (center) {
            drawModuleRoundedDark(context, left, top, right, bottom, radius, !north && !west, !north && !east, !south && !east, !south && !west);
        } else {
            drawModuleRoundendLight(context, left, top, right, bottom, radius, north && west && northwest, north && east && northeast, south && east && southeast, south && west && southwest);
        }
    }

    function drawModules(qr, context, settings) {
        var moduleCount = qr.moduleCount;
        var moduleSize = settings.size / moduleCount;
        var fn = drawModuleDefault;
        var row;
        var col;

        if (settings.radius > 0 && settings.radius <= 0.5) {
            fn = drawModuleRounded;
        }

        context.beginPath();
        for (row = 0; row < moduleCount; row += 1) {
            for (col = 0; col < moduleCount; col += 1) {
                var l = settings.left + col * moduleSize;
                var t = settings.top + row * moduleSize;
                var w = moduleSize;

                fn(qr, context, settings, l, t, w, row, col);
            }
        }
        if (jq(settings.fill).is('img')) {
            context.strokeStyle = 'rgba(0,0,0,0.5)';
            context.lineWidth = 2;
            context.stroke();
            var prev = context.globalCompositeOperation;
            context.globalCompositeOperation = 'destination-out';
            context.fill();
            context.globalCompositeOperation = prev;

            context.clip();
            context.drawImage(settings.fill, 0, 0, settings.size, settings.size);
            context.restore();
        } else {
            context.fillStyle = settings.fill;
            context.fill();
        }
    }

    // Draws QR code to the given `canvas` and returns it.
    function drawOnCanvas(canvas, settings) {
        var qr = createMinQRCode(settings.text, settings.ecLevel, settings.minVersion, settings.maxVersion, settings.quiet);
        if (!qr) {
            return null;
        }

        var $canvas = jq(canvas).data('qrcode', qr);
        var context = $canvas[0].getContext('2d');

        drawBackground(qr, context, settings);
        drawModules(qr, context, settings);

        return $canvas;
    }

    // Returns a `canvas` element representing the QR code for the given settings.
    function createCanvas(settings) {
        var $canvas = jq('<canvas/>').attr('width', settings.size).attr('height', settings.size);
        return drawOnCanvas($canvas, settings);
    }

    // Returns an `image` element representing the QR code for the given settings.
    function createImage(settings) {
        return jq('<img/>').attr('src', createCanvas(settings)[0].toDataURL('image/png'));
    }

    // Returns a `div` element representing the QR code for the given settings.
    function createDiv(settings) {
        var qr = createMinQRCode(settings.text, settings.ecLevel, settings.minVersion, settings.maxVersion, settings.quiet);
        if (!qr) {
            return null;
        }

        // some shortcuts to improve compression
        var settings_size = settings.size;
        var settings_bgColor = settings.background;
        var math_floor = Math.floor;

        var moduleCount = qr.moduleCount;
        var moduleSize = math_floor(settings_size / moduleCount);
        var offset = math_floor(0.5 * (settings_size - moduleSize * moduleCount));

        var row;
        var col;

        var containerCSS = {
            position: 'relative',
            left: 0,
            top: 0,
            padding: 0,
            margin: 0,
            width: settings_size,
            height: settings_size
        };
        var darkCSS = {
            position: 'absolute',
            padding: 0,
            margin: 0,
            width: moduleSize,
            height: moduleSize,
            'background-color': settings.fill
        };

        var $div = jq('<div/>').data('qrcode', qr).css(containerCSS);

        if (settings_bgColor) {
            $div.css('background-color', settings_bgColor);
        }

        for (row = 0; row < moduleCount; row += 1) {
            for (col = 0; col < moduleCount; col += 1) {
                if (qr.isDark(row, col)) {
                    jq('<div/>')
                        .css(darkCSS)
                        .css({
                            left: offset + col * moduleSize,
                            top: offset + row * moduleSize
                        })
                        .appendTo($div);
                }
            }
        }

        return $div;
    }

    function createHTML(settings) {
        if (hasCanvas && settings.render === 'canvas') {
            return createCanvas(settings);
        } else if (hasCanvas && settings.render === 'image') {
            return createImage(settings);
        }

        return createDiv(settings);
    }

    // Plugin
    // ======

    // Default settings
    // ----------------
    var defaults = {
        // render method: `'canvas'`, `'image'` or `'div'`
        render: 'canvas',

        // version range somewhere in 1 .. 40
        minVersion: 1,
        maxVersion: 40,

        // error correction level: `'L'`, `'M'`, `'Q'` or `'H'`
        ecLevel: 'L',

        // offset in pixel if drawn onto existing canvas
        left: 0,
        top: 0,

        // size in pixel
        size: 200,

        // code color or image element
        fill: '#000',

        // background color or image element, `null` for transparent background
        background: null,

        // content
        text: 'no text',

        // corner radius relative to module width: 0.0 .. 0.5
        radius: 0,

        // quiet zone in modules
        quiet: 0,

        // modes
        // 0: normal
        // 1: label strip
        // 2: label box
        // 3: image strip
        // 4: image box
        mode: 0,

        mSize: 0.1,
        mPosX: 0.5,
        mPosY: 0.5,

        label: 'no label',
        fontname: 'sans',
        fontcolor: '#000',

        image: null
    };

    // Register the plugin
    // -------------------
    jq.fn.qrcode = function (options) {
        var settings = jq.extend({}, defaults, options);

        return this.each(function (idx, el) {
            if (el.nodeName.toLowerCase() === 'canvas') {
                drawOnCanvas(el, settings);
            } else {
                jq(el).append(createHTML(settings));
            }
        });
    };
}(function () {
    // `qrcode` is the single public function defined by the `QR Code Generator`
    //---------------------------------------------------------------------
    //
    // QR Code Generator for JavaScript
    //
    // Copyright (c) 2009 Kazuhiko Arase
    //
    // URL: http://www.d-project.com/
    //
    // Licensed under the MIT license:
    //  http://www.opensource.org/licenses/mit-license.php
    //
    // The word 'QR Code' is registered trademark of
    // DENSO WAVE INCORPORATED
    //  http://www.denso-wave.com/qrcode/faqpatent-e.html
    //
    //---------------------------------------------------------------------

    var qrcode = function() {

      //---------------------------------------------------------------------
      // qrcode
      //---------------------------------------------------------------------

      /**
       * qrcode
       * @param typeNumber 1 to 40
       * @param errorCorrectLevel 'L','M','Q','H'
       */
      var qrcode = function(typeNumber, errorCorrectLevel) {

        var PAD0 = 0xEC;
        var PAD1 = 0x11;

        var _typeNumber = typeNumber;
        var _errorCorrectLevel = QRErrorCorrectLevel[errorCorrectLevel];
        var _modules = null;
        var _moduleCount = 0;
        var _dataCache = null;
        var _dataList = new Array();

        var _this = {};

        var makeImpl = function(test, maskPattern) {

          _moduleCount = _typeNumber * 4 + 17;
          _modules = function(moduleCount) {
            var modules = new Array(moduleCount);
            for (var row = 0; row < moduleCount; row += 1) {
              modules[row] = new Array(moduleCount);
              for (var col = 0; col < moduleCount; col += 1) {
                modules[row][col] = null;
              }
            }
            return modules;
          }(_moduleCount);

          setupPositionProbePattern(0, 0);
          setupPositionProbePattern(_moduleCount - 7, 0);
          setupPositionProbePattern(0, _moduleCount - 7);
          setupPositionAdjustPattern();
          setupTimingPattern();
          setupTypeInfo(test, maskPattern);

          if (_typeNumber >= 7) {
            setupTypeNumber(test);
          }

          if (_dataCache == null) {
            _dataCache = createData(_typeNumber, _errorCorrectLevel, _dataList);
          }

          mapData(_dataCache, maskPattern);
        };

        var setupPositionProbePattern = function(row, col) {

          for (var r = -1; r <= 7; r += 1) {

            if (row + r <= -1 || _moduleCount <= row + r) continue;

            for (var c = -1; c <= 7; c += 1) {

              if (col + c <= -1 || _moduleCount <= col + c) continue;

              if ( (0 <= r && r <= 6 && (c == 0 || c == 6) )
                  || (0 <= c && c <= 6 && (r == 0 || r == 6) )
                  || (2 <= r && r <= 4 && 2 <= c && c <= 4) ) {
                _modules[row + r][col + c] = true;
              } else {
                _modules[row + r][col + c] = false;
              }
            }
          }
        };

        var getBestMaskPattern = function() {

          var minLostPoint = 0;
          var pattern = 0;

          for (var i = 0; i < 8; i += 1) {

            makeImpl(true, i);

            var lostPoint = QRUtil.getLostPoint(_this);

            if (i == 0 || minLostPoint > lostPoint) {
              minLostPoint = lostPoint;
              pattern = i;
            }
          }

          return pattern;
        };

        var setupTimingPattern = function() {

          for (var r = 8; r < _moduleCount - 8; r += 1) {
            if (_modules[r][6] != null) {
              continue;
            }
            _modules[r][6] = (r % 2 == 0);
          }

          for (var c = 8; c < _moduleCount - 8; c += 1) {
            if (_modules[6][c] != null) {
              continue;
            }
            _modules[6][c] = (c % 2 == 0);
          }
        };

        var setupPositionAdjustPattern = function() {

          var pos = QRUtil.getPatternPosition(_typeNumber);

          for (var i = 0; i < pos.length; i += 1) {

            for (var j = 0; j < pos.length; j += 1) {

              var row = pos[i];
              var col = pos[j];

              if (_modules[row][col] != null) {
                continue;
              }

              for (var r = -2; r <= 2; r += 1) {

                for (var c = -2; c <= 2; c += 1) {

                  if (r == -2 || r == 2 || c == -2 || c == 2
                      || (r == 0 && c == 0) ) {
                    _modules[row + r][col + c] = true;
                  } else {
                    _modules[row + r][col + c] = false;
                  }
                }
              }
            }
          }
        };

        var setupTypeNumber = function(test) {

          var bits = QRUtil.getBCHTypeNumber(_typeNumber);

          for (var i = 0; i < 18; i += 1) {
            var mod = (!test && ( (bits >> i) & 1) == 1);
            _modules[Math.floor(i / 3)][i % 3 + _moduleCount - 8 - 3] = mod;
          }

          for (var i = 0; i < 18; i += 1) {
            var mod = (!test && ( (bits >> i) & 1) == 1);
            _modules[i % 3 + _moduleCount - 8 - 3][Math.floor(i / 3)] = mod;
          }
        };

        var setupTypeInfo = function(test, maskPattern) {

          var data = (_errorCorrectLevel << 3) | maskPattern;
          var bits = QRUtil.getBCHTypeInfo(data);

          // vertical
          for (var i = 0; i < 15; i += 1) {

            var mod = (!test && ( (bits >> i) & 1) == 1);

            if (i < 6) {
              _modules[i][8] = mod;
            } else if (i < 8) {
              _modules[i + 1][8] = mod;
            } else {
              _modules[_moduleCount - 15 + i][8] = mod;
            }
          }

          // horizontal
          for (var i = 0; i < 15; i += 1) {

            var mod = (!test && ( (bits >> i) & 1) == 1);

            if (i < 8) {
              _modules[8][_moduleCount - i - 1] = mod;
            } else if (i < 9) {
              _modules[8][15 - i - 1 + 1] = mod;
            } else {
              _modules[8][15 - i - 1] = mod;
            }
          }

          // fixed module
          _modules[_moduleCount - 8][8] = (!test);
        };

        var mapData = function(data, maskPattern) {

          var inc = -1;
          var row = _moduleCount - 1;
          var bitIndex = 7;
          var byteIndex = 0;
          var maskFunc = QRUtil.getMaskFunction(maskPattern);

          for (var col = _moduleCount - 1; col > 0; col -= 2) {

            if (col == 6) col -= 1;

            while (true) {

              for (var c = 0; c < 2; c += 1) {

                if (_modules[row][col - c] == null) {

                  var dark = false;

                  if (byteIndex < data.length) {
                    dark = ( ( (data[byteIndex] >>> bitIndex) & 1) == 1);
                  }

                  var mask = maskFunc(row, col - c);

                  if (mask) {
                    dark = !dark;
                  }

                  _modules[row][col - c] = dark;
                  bitIndex -= 1;

                  if (bitIndex == -1) {
                    byteIndex += 1;
                    bitIndex = 7;
                  }
                }
              }

              row += inc;

              if (row < 0 || _moduleCount <= row) {
                row -= inc;
                inc = -inc;
                break;
              }
            }
          }
        };

        var createBytes = function(buffer, rsBlocks) {

          var offset = 0;

          var maxDcCount = 0;
          var maxEcCount = 0;

          var dcdata = new Array(rsBlocks.length);
          var ecdata = new Array(rsBlocks.length);

          for (var r = 0; r < rsBlocks.length; r += 1) {

            var dcCount = rsBlocks[r].dataCount;
            var ecCount = rsBlocks[r].totalCount - dcCount;

            maxDcCount = Math.max(maxDcCount, dcCount);
            maxEcCount = Math.max(maxEcCount, ecCount);

            dcdata[r] = new Array(dcCount);

            for (var i = 0; i < dcdata[r].length; i += 1) {
              dcdata[r][i] = 0xff & buffer.getBuffer()[i + offset];
            }
            offset += dcCount;

            var rsPoly = QRUtil.getErrorCorrectPolynomial(ecCount);
            var rawPoly = qrPolynomial(dcdata[r], rsPoly.getLength() - 1);

            var modPoly = rawPoly.mod(rsPoly);
            ecdata[r] = new Array(rsPoly.getLength() - 1);
            for (var i = 0; i < ecdata[r].length; i += 1) {
              var modIndex = i + modPoly.getLength() - ecdata[r].length;
              ecdata[r][i] = (modIndex >= 0)? modPoly.getAt(modIndex) : 0;
            }
          }

          var totalCodeCount = 0;
          for (var i = 0; i < rsBlocks.length; i += 1) {
            totalCodeCount += rsBlocks[i].totalCount;
          }

          var data = new Array(totalCodeCount);
          var index = 0;

          for (var i = 0; i < maxDcCount; i += 1) {
            for (var r = 0; r < rsBlocks.length; r += 1) {
              if (i < dcdata[r].length) {
                data[index] = dcdata[r][i];
                index += 1;
              }
            }
          }

          for (var i = 0; i < maxEcCount; i += 1) {
            for (var r = 0; r < rsBlocks.length; r += 1) {
              if (i < ecdata[r].length) {
                data[index] = ecdata[r][i];
                index += 1;
              }
            }
          }

          return data;
        };

        var createData = function(typeNumber, errorCorrectLevel, dataList) {

          var rsBlocks = QRRSBlock.getRSBlocks(typeNumber, errorCorrectLevel);

          var buffer = qrBitBuffer();

          for (var i = 0; i < dataList.length; i += 1) {
            var data = dataList[i];
            buffer.put(data.getMode(), 4);
            buffer.put(data.getLength(), QRUtil.getLengthInBits(data.getMode(), typeNumber) );
            data.write(buffer);
          }

          // calc num max data.
          var totalDataCount = 0;
          for (var i = 0; i < rsBlocks.length; i += 1) {
            totalDataCount += rsBlocks[i].dataCount;
          }

          if (buffer.getLengthInBits() > totalDataCount * 8) {
            throw new Error('code length overflow. ('
              + buffer.getLengthInBits()
              + '>'
              + totalDataCount * 8
              + ')');
          }

          // end code
          if (buffer.getLengthInBits() + 4 <= totalDataCount * 8) {
            buffer.put(0, 4);
          }

          // padding
          while (buffer.getLengthInBits() % 8 != 0) {
            buffer.putBit(false);
          }

          // padding
          while (true) {

            if (buffer.getLengthInBits() >= totalDataCount * 8) {
              break;
            }
            buffer.put(PAD0, 8);

            if (buffer.getLengthInBits() >= totalDataCount * 8) {
              break;
            }
            buffer.put(PAD1, 8);
          }

          return createBytes(buffer, rsBlocks);
        };

        _this.addData = function(data) {
          var newData = qr8BitByte(data);
          _dataList.push(newData);
          _dataCache = null;
        };

        _this.isDark = function(row, col) {
          if (row < 0 || _moduleCount <= row || col < 0 || _moduleCount <= col) {
            throw new Error(row + ',' + col);
          }
          return _modules[row][col];
        };

        _this.getModuleCount = function() {
          return _moduleCount;
        };

        _this.make = function() {
          makeImpl(false, getBestMaskPattern() );
        };

        _this.createTableTag = function(cellSize, margin) {

          cellSize = cellSize || 2;
          margin = (typeof margin == 'undefined')? cellSize * 4 : margin;

          var qrHtml = '';

          qrHtml += '<table style="';
          qrHtml += ' border-width: 0px; border-style: none;';
          qrHtml += ' border-collapse: collapse;';
          qrHtml += ' padding: 0px; margin: ' + margin + 'px;';
          qrHtml += '">';
          qrHtml += '<tbody>';

          for (var r = 0; r < _this.getModuleCount(); r += 1) {

            qrHtml += '<tr>';

            for (var c = 0; c < _this.getModuleCount(); c += 1) {
              qrHtml += '<td style="';
              qrHtml += ' border-width: 0px; border-style: none;';
              qrHtml += ' border-collapse: collapse;';
              qrHtml += ' padding: 0px; margin: 0px;';
              qrHtml += ' width: ' + cellSize + 'px;';
              qrHtml += ' height: ' + cellSize + 'px;';
              qrHtml += ' background-color: ';
              qrHtml += _this.isDark(r, c)? '#000000' : '#ffffff';
              qrHtml += ';';
              qrHtml += '"/>';
            }

            qrHtml += '</tr>';
          }

          qrHtml += '</tbody>';
          qrHtml += '</table>';

          return qrHtml;
        };

        _this.createImgTag = function(cellSize, margin) {

          cellSize = cellSize || 2;
          margin = (typeof margin == 'undefined')? cellSize * 4 : margin;

          var size = _this.getModuleCount() * cellSize + margin * 2;
          var min = margin;
          var max = size - margin;

          return createImgTag(size, size, function(x, y) {
            if (min <= x && x < max && min <= y && y < max) {
              var c = Math.floor( (x - min) / cellSize);
              var r = Math.floor( (y - min) / cellSize);
              return _this.isDark(r, c)? 0 : 1;
            } else {
              return 1;
            }
          } );
        };

        return _this;
      };

      //---------------------------------------------------------------------
      // qrcode.stringToBytes
      //---------------------------------------------------------------------

      qrcode.stringToBytes = function(s) {
        var bytes = new Array();
        for (var i = 0; i < s.length; i += 1) {
          var c = s.charCodeAt(i);
          bytes.push(c & 0xff);
        }
        return bytes;
      };

      //---------------------------------------------------------------------
      // qrcode.createStringToBytes
      //---------------------------------------------------------------------

      /**
       * @param unicodeData base64 string of byte array.
       * [16bit Unicode],[16bit Bytes], ...
       * @param numChars
       */
      qrcode.createStringToBytes = function(unicodeData, numChars) {

        // create conversion map.

        var unicodeMap = function() {

          var bin = base64DecodeInputStream(unicodeData);
          var read = function() {
            var b = bin.read();
            if (b == -1) throw new Error();
            return b;
          };

          var count = 0;
          var unicodeMap = {};
          while (true) {
            var b0 = bin.read();
            if (b0 == -1) break;
            var b1 = read();
            var b2 = read();
            var b3 = read();
            var k = String.fromCharCode( (b0 << 8) | b1);
            var v = (b2 << 8) | b3;
            unicodeMap[k] = v;
            count += 1;
          }
          if (count != numChars) {
            throw new Error(count + ' != ' + numChars);
          }

          return unicodeMap;
        }();

        var unknownChar = '?'.charCodeAt(0);

        return function(s) {
          var bytes = new Array();
          for (var i = 0; i < s.length; i += 1) {
            var c = s.charCodeAt(i);
            if (c < 128) {
              bytes.push(c);
            } else {
              var b = unicodeMap[s.charAt(i)];
              if (typeof b == 'number') {
                if ( (b & 0xff) == b) {
                  // 1byte
                  bytes.push(b);
                } else {
                  // 2bytes
                  bytes.push(b >>> 8);
                  bytes.push(b & 0xff);
                }
              } else {
                bytes.push(unknownChar);
              }
            }
          }
          return bytes;
        };
      };

      //---------------------------------------------------------------------
      // QRMode
      //---------------------------------------------------------------------

      var QRMode = {
        MODE_NUMBER :    1 << 0,
        MODE_ALPHA_NUM : 1 << 1,
        MODE_8BIT_BYTE : 1 << 2,
        MODE_KANJI :     1 << 3
      };

      //---------------------------------------------------------------------
      // QRErrorCorrectLevel
      //---------------------------------------------------------------------

      var QRErrorCorrectLevel = {
        L : 1,
        M : 0,
        Q : 3,
        H : 2
      };

      //---------------------------------------------------------------------
      // QRMaskPattern
      //---------------------------------------------------------------------

      var QRMaskPattern = {
        PATTERN000 : 0,
        PATTERN001 : 1,
        PATTERN010 : 2,
        PATTERN011 : 3,
        PATTERN100 : 4,
        PATTERN101 : 5,
        PATTERN110 : 6,
        PATTERN111 : 7
      };

      //---------------------------------------------------------------------
      // QRUtil
      //---------------------------------------------------------------------

      var QRUtil = function() {

        var PATTERN_POSITION_TABLE = [
          [],
          [6, 18],
          [6, 22],
          [6, 26],
          [6, 30],
          [6, 34],
          [6, 22, 38],
          [6, 24, 42],
          [6, 26, 46],
          [6, 28, 50],
          [6, 30, 54],
          [6, 32, 58],
          [6, 34, 62],
          [6, 26, 46, 66],
          [6, 26, 48, 70],
          [6, 26, 50, 74],
          [6, 30, 54, 78],
          [6, 30, 56, 82],
          [6, 30, 58, 86],
          [6, 34, 62, 90],
          [6, 28, 50, 72, 94],
          [6, 26, 50, 74, 98],
          [6, 30, 54, 78, 102],
          [6, 28, 54, 80, 106],
          [6, 32, 58, 84, 110],
          [6, 30, 58, 86, 114],
          [6, 34, 62, 90, 118],
          [6, 26, 50, 74, 98, 122],
          [6, 30, 54, 78, 102, 126],
          [6, 26, 52, 78, 104, 130],
          [6, 30, 56, 82, 108, 134],
          [6, 34, 60, 86, 112, 138],
          [6, 30, 58, 86, 114, 142],
          [6, 34, 62, 90, 118, 146],
          [6, 30, 54, 78, 102, 126, 150],
          [6, 24, 50, 76, 102, 128, 154],
          [6, 28, 54, 80, 106, 132, 158],
          [6, 32, 58, 84, 110, 136, 162],
          [6, 26, 54, 82, 110, 138, 166],
          [6, 30, 58, 86, 114, 142, 170]
        ];
        var G15 = (1 << 10) | (1 << 8) | (1 << 5) | (1 << 4) | (1 << 2) | (1 << 1) | (1 << 0);
        var G18 = (1 << 12) | (1 << 11) | (1 << 10) | (1 << 9) | (1 << 8) | (1 << 5) | (1 << 2) | (1 << 0);
        var G15_MASK = (1 << 14) | (1 << 12) | (1 << 10) | (1 << 4) | (1 << 1);

        var _this = {};

        var getBCHDigit = function(data) {
          var digit = 0;
          while (data != 0) {
            digit += 1;
            data >>>= 1;
          }
          return digit;
        };

        _this.getBCHTypeInfo = function(data) {
          var d = data << 10;
          while (getBCHDigit(d) - getBCHDigit(G15) >= 0) {
            d ^= (G15 << (getBCHDigit(d) - getBCHDigit(G15) ) );
          }
          return ( (data << 10) | d) ^ G15_MASK;
        };

        _this.getBCHTypeNumber = function(data) {
          var d = data << 12;
          while (getBCHDigit(d) - getBCHDigit(G18) >= 0) {
            d ^= (G18 << (getBCHDigit(d) - getBCHDigit(G18) ) );
          }
          return (data << 12) | d;
        };

        _this.getPatternPosition = function(typeNumber) {
          return PATTERN_POSITION_TABLE[typeNumber - 1];
        };

        _this.getMaskFunction = function(maskPattern) {

          switch (maskPattern) {

          case QRMaskPattern.PATTERN000 :
            return function(i, j) { return (i + j) % 2 == 0; };
          case QRMaskPattern.PATTERN001 :
            return function(i, j) { return i % 2 == 0; };
          case QRMaskPattern.PATTERN010 :
            return function(i, j) { return j % 3 == 0; };
          case QRMaskPattern.PATTERN011 :
            return function(i, j) { return (i + j) % 3 == 0; };
          case QRMaskPattern.PATTERN100 :
            return function(i, j) { return (Math.floor(i / 2) + Math.floor(j / 3) ) % 2 == 0; };
          case QRMaskPattern.PATTERN101 :
            return function(i, j) { return (i * j) % 2 + (i * j) % 3 == 0; };
          case QRMaskPattern.PATTERN110 :
            return function(i, j) { return ( (i * j) % 2 + (i * j) % 3) % 2 == 0; };
          case QRMaskPattern.PATTERN111 :
            return function(i, j) { return ( (i * j) % 3 + (i + j) % 2) % 2 == 0; };

          default :
            throw new Error('bad maskPattern:' + maskPattern);
          }
        };

        _this.getErrorCorrectPolynomial = function(errorCorrectLength) {
          var a = qrPolynomial([1], 0);
          for (var i = 0; i < errorCorrectLength; i += 1) {
            a = a.multiply(qrPolynomial([1, QRMath.gexp(i)], 0) );
          }
          return a;
        };

        _this.getLengthInBits = function(mode, type) {

          if (1 <= type && type < 10) {

            // 1 - 9

            switch(mode) {
            case QRMode.MODE_NUMBER    : return 10;
            case QRMode.MODE_ALPHA_NUM : return 9;
            case QRMode.MODE_8BIT_BYTE : return 8;
            case QRMode.MODE_KANJI     : return 8;
            default :
              throw new Error('mode:' + mode);
            }

          } else if (type < 27) {

            // 10 - 26

            switch(mode) {
            case QRMode.MODE_NUMBER    : return 12;
            case QRMode.MODE_ALPHA_NUM : return 11;
            case QRMode.MODE_8BIT_BYTE : return 16;
            case QRMode.MODE_KANJI     : return 10;
            default :
              throw new Error('mode:' + mode);
            }

          } else if (type < 41) {

            // 27 - 40

            switch(mode) {
            case QRMode.MODE_NUMBER    : return 14;
            case QRMode.MODE_ALPHA_NUM : return 13;
            case QRMode.MODE_8BIT_BYTE : return 16;
            case QRMode.MODE_KANJI     : return 12;
            default :
              throw new Error('mode:' + mode);
            }

          } else {
            throw new Error('type:' + type);
          }
        };

        _this.getLostPoint = function(qrcode) {

          var moduleCount = qrcode.getModuleCount();

          var lostPoint = 0;

          // LEVEL1

          for (var row = 0; row < moduleCount; row += 1) {
            for (var col = 0; col < moduleCount; col += 1) {

              var sameCount = 0;
              var dark = qrcode.isDark(row, col);

              for (var r = -1; r <= 1; r += 1) {

                if (row + r < 0 || moduleCount <= row + r) {
                  continue;
                }

                for (var c = -1; c <= 1; c += 1) {

                  if (col + c < 0 || moduleCount <= col + c) {
                    continue;
                  }

                  if (r == 0 && c == 0) {
                    continue;
                  }

                  if (dark == qrcode.isDark(row + r, col + c) ) {
                    sameCount += 1;
                  }
                }
              }

              if (sameCount > 5) {
                lostPoint += (3 + sameCount - 5);
              }
            }
          };

          // LEVEL2

          for (var row = 0; row < moduleCount - 1; row += 1) {
            for (var col = 0; col < moduleCount - 1; col += 1) {
              var count = 0;
              if (qrcode.isDark(row, col) ) count += 1;
              if (qrcode.isDark(row + 1, col) ) count += 1;
              if (qrcode.isDark(row, col + 1) ) count += 1;
              if (qrcode.isDark(row + 1, col + 1) ) count += 1;
              if (count == 0 || count == 4) {
                lostPoint += 3;
              }
            }
          }

          // LEVEL3

          for (var row = 0; row < moduleCount; row += 1) {
            for (var col = 0; col < moduleCount - 6; col += 1) {
              if (qrcode.isDark(row, col)
                  && !qrcode.isDark(row, col + 1)
                  &&  qrcode.isDark(row, col + 2)
                  &&  qrcode.isDark(row, col + 3)
                  &&  qrcode.isDark(row, col + 4)
                  && !qrcode.isDark(row, col + 5)
                  &&  qrcode.isDark(row, col + 6) ) {
                lostPoint += 40;
              }
            }
          }

          for (var col = 0; col < moduleCount; col += 1) {
            for (var row = 0; row < moduleCount - 6; row += 1) {
              if (qrcode.isDark(row, col)
                  && !qrcode.isDark(row + 1, col)
                  &&  qrcode.isDark(row + 2, col)
                  &&  qrcode.isDark(row + 3, col)
                  &&  qrcode.isDark(row + 4, col)
                  && !qrcode.isDark(row + 5, col)
                  &&  qrcode.isDark(row + 6, col) ) {
                lostPoint += 40;
              }
            }
          }

          // LEVEL4

          var darkCount = 0;

          for (var col = 0; col < moduleCount; col += 1) {
            for (var row = 0; row < moduleCount; row += 1) {
              if (qrcode.isDark(row, col) ) {
                darkCount += 1;
              }
            }
          }

          var ratio = Math.abs(100 * darkCount / moduleCount / moduleCount - 50) / 5;
          lostPoint += ratio * 10;

          return lostPoint;
        };

        return _this;
      }();

      //---------------------------------------------------------------------
      // QRMath
      //---------------------------------------------------------------------

      var QRMath = function() {

        var EXP_TABLE = new Array(256);
        var LOG_TABLE = new Array(256);

        // initialize tables
        for (var i = 0; i < 8; i += 1) {
          EXP_TABLE[i] = 1 << i;
        }
        for (var i = 8; i < 256; i += 1) {
          EXP_TABLE[i] = EXP_TABLE[i - 4]
            ^ EXP_TABLE[i - 5]
            ^ EXP_TABLE[i - 6]
            ^ EXP_TABLE[i - 8];
        }
        for (var i = 0; i < 255; i += 1) {
          LOG_TABLE[EXP_TABLE[i] ] = i;
        }

        var _this = {};

        _this.glog = function(n) {

          if (n < 1) {
            throw new Error('glog(' + n + ')');
          }

          return LOG_TABLE[n];
        };

        _this.gexp = function(n) {

          while (n < 0) {
            n += 255;
          }

          while (n >= 256) {
            n -= 255;
          }

          return EXP_TABLE[n];
        };

        return _this;
      }();

      //---------------------------------------------------------------------
      // qrPolynomial
      //---------------------------------------------------------------------

      function qrPolynomial(num, shift) {

        if (typeof num.length == 'undefined') {
          throw new Error(num.length + '/' + shift);
        }

        var _num = function() {
          var offset = 0;
          while (offset < num.length && num[offset] == 0) {
            offset += 1;
          }
          var _num = new Array(num.length - offset + shift);
          for (var i = 0; i < num.length - offset; i += 1) {
            _num[i] = num[i + offset];
          }
          return _num;
        }();

        var _this = {};

        _this.getAt = function(index) {
          return _num[index];
        };

        _this.getLength = function() {
          return _num.length;
        };

        _this.multiply = function(e) {

          var num = new Array(_this.getLength() + e.getLength() - 1);

          for (var i = 0; i < _this.getLength(); i += 1) {
            for (var j = 0; j < e.getLength(); j += 1) {
              num[i + j] ^= QRMath.gexp(QRMath.glog(_this.getAt(i) ) + QRMath.glog(e.getAt(j) ) );
            }
          }

          return qrPolynomial(num, 0);
        };

        _this.mod = function(e) {

          if (_this.getLength() - e.getLength() < 0) {
            return _this;
          }

          var ratio = QRMath.glog(_this.getAt(0) ) - QRMath.glog(e.getAt(0) );

          var num = new Array(_this.getLength() );
          for (var i = 0; i < _this.getLength(); i += 1) {
            num[i] = _this.getAt(i);
          }

          for (var i = 0; i < e.getLength(); i += 1) {
            num[i] ^= QRMath.gexp(QRMath.glog(e.getAt(i) ) + ratio);
          }

          // recursive call
          return qrPolynomial(num, 0).mod(e);
        };

        return _this;
      };

      //---------------------------------------------------------------------
      // QRRSBlock
      //---------------------------------------------------------------------

      var QRRSBlock = function() {

        var RS_BLOCK_TABLE = [

          // L
          // M
          // Q
          // H

          // 1
          [1, 26, 19],
          [1, 26, 16],
          [1, 26, 13],
          [1, 26, 9],

          // 2
          [1, 44, 34],
          [1, 44, 28],
          [1, 44, 22],
          [1, 44, 16],

          // 3
          [1, 70, 55],
          [1, 70, 44],
          [2, 35, 17],
          [2, 35, 13],

          // 4
          [1, 100, 80],
          [2, 50, 32],
          [2, 50, 24],
          [4, 25, 9],

          // 5
          [1, 134, 108],
          [2, 67, 43],
          [2, 33, 15, 2, 34, 16],
          [2, 33, 11, 2, 34, 12],

          // 6
          [2, 86, 68],
          [4, 43, 27],
          [4, 43, 19],
          [4, 43, 15],

          // 7
          [2, 98, 78],
          [4, 49, 31],
          [2, 32, 14, 4, 33, 15],
          [4, 39, 13, 1, 40, 14],

          // 8
          [2, 121, 97],
          [2, 60, 38, 2, 61, 39],
          [4, 40, 18, 2, 41, 19],
          [4, 40, 14, 2, 41, 15],

          // 9
          [2, 146, 116],
          [3, 58, 36, 2, 59, 37],
          [4, 36, 16, 4, 37, 17],
          [4, 36, 12, 4, 37, 13],

          // 10
          [2, 86, 68, 2, 87, 69],
          [4, 69, 43, 1, 70, 44],
          [6, 43, 19, 2, 44, 20],
          [6, 43, 15, 2, 44, 16],

          // 11
          [4, 101, 81],
          [1, 80, 50, 4, 81, 51],
          [4, 50, 22, 4, 51, 23],
          [3, 36, 12, 8, 37, 13],

          // 12
          [2, 116, 92, 2, 117, 93],
          [6, 58, 36, 2, 59, 37],
          [4, 46, 20, 6, 47, 21],
          [7, 42, 14, 4, 43, 15],

          // 13
          [4, 133, 107],
          [8, 59, 37, 1, 60, 38],
          [8, 44, 20, 4, 45, 21],
          [12, 33, 11, 4, 34, 12],

          // 14
          [3, 145, 115, 1, 146, 116],
          [4, 64, 40, 5, 65, 41],
          [11, 36, 16, 5, 37, 17],
          [11, 36, 12, 5, 37, 13],

          // 15
          [5, 109, 87, 1, 110, 88],
          [5, 65, 41, 5, 66, 42],
          [5, 54, 24, 7, 55, 25],
          [11, 36, 12, 7, 37, 13],

          // 16
          [5, 122, 98, 1, 123, 99],
          [7, 73, 45, 3, 74, 46],
          [15, 43, 19, 2, 44, 20],
          [3, 45, 15, 13, 46, 16],

          // 17
          [1, 135, 107, 5, 136, 108],
          [10, 74, 46, 1, 75, 47],
          [1, 50, 22, 15, 51, 23],
          [2, 42, 14, 17, 43, 15],

          // 18
          [5, 150, 120, 1, 151, 121],
          [9, 69, 43, 4, 70, 44],
          [17, 50, 22, 1, 51, 23],
          [2, 42, 14, 19, 43, 15],

          // 19
          [3, 141, 113, 4, 142, 114],
          [3, 70, 44, 11, 71, 45],
          [17, 47, 21, 4, 48, 22],
          [9, 39, 13, 16, 40, 14],

          // 20
          [3, 135, 107, 5, 136, 108],
          [3, 67, 41, 13, 68, 42],
          [15, 54, 24, 5, 55, 25],
          [15, 43, 15, 10, 44, 16],

          // 21
          [4, 144, 116, 4, 145, 117],
          [17, 68, 42],
          [17, 50, 22, 6, 51, 23],
          [19, 46, 16, 6, 47, 17],

          // 22
          [2, 139, 111, 7, 140, 112],
          [17, 74, 46],
          [7, 54, 24, 16, 55, 25],
          [34, 37, 13],

          // 23
          [4, 151, 121, 5, 152, 122],
          [4, 75, 47, 14, 76, 48],
          [11, 54, 24, 14, 55, 25],
          [16, 45, 15, 14, 46, 16],

          // 24
          [6, 147, 117, 4, 148, 118],
          [6, 73, 45, 14, 74, 46],
          [11, 54, 24, 16, 55, 25],
          [30, 46, 16, 2, 47, 17],

          // 25
          [8, 132, 106, 4, 133, 107],
          [8, 75, 47, 13, 76, 48],
          [7, 54, 24, 22, 55, 25],
          [22, 45, 15, 13, 46, 16],

          // 26
          [10, 142, 114, 2, 143, 115],
          [19, 74, 46, 4, 75, 47],
          [28, 50, 22, 6, 51, 23],
          [33, 46, 16, 4, 47, 17],

          // 27
          [8, 152, 122, 4, 153, 123],
          [22, 73, 45, 3, 74, 46],
          [8, 53, 23, 26, 54, 24],
          [12, 45, 15, 28, 46, 16],

          // 28
          [3, 147, 117, 10, 148, 118],
          [3, 73, 45, 23, 74, 46],
          [4, 54, 24, 31, 55, 25],
          [11, 45, 15, 31, 46, 16],

          // 29
          [7, 146, 116, 7, 147, 117],
          [21, 73, 45, 7, 74, 46],
          [1, 53, 23, 37, 54, 24],
          [19, 45, 15, 26, 46, 16],

          // 30
          [5, 145, 115, 10, 146, 116],
          [19, 75, 47, 10, 76, 48],
          [15, 54, 24, 25, 55, 25],
          [23, 45, 15, 25, 46, 16],

          // 31
          [13, 145, 115, 3, 146, 116],
          [2, 74, 46, 29, 75, 47],
          [42, 54, 24, 1, 55, 25],
          [23, 45, 15, 28, 46, 16],

          // 32
          [17, 145, 115],
          [10, 74, 46, 23, 75, 47],
          [10, 54, 24, 35, 55, 25],
          [19, 45, 15, 35, 46, 16],

          // 33
          [17, 145, 115, 1, 146, 116],
          [14, 74, 46, 21, 75, 47],
          [29, 54, 24, 19, 55, 25],
          [11, 45, 15, 46, 46, 16],

          // 34
          [13, 145, 115, 6, 146, 116],
          [14, 74, 46, 23, 75, 47],
          [44, 54, 24, 7, 55, 25],
          [59, 46, 16, 1, 47, 17],

          // 35
          [12, 151, 121, 7, 152, 122],
          [12, 75, 47, 26, 76, 48],
          [39, 54, 24, 14, 55, 25],
          [22, 45, 15, 41, 46, 16],

          // 36
          [6, 151, 121, 14, 152, 122],
          [6, 75, 47, 34, 76, 48],
          [46, 54, 24, 10, 55, 25],
          [2, 45, 15, 64, 46, 16],

          // 37
          [17, 152, 122, 4, 153, 123],
          [29, 74, 46, 14, 75, 47],
          [49, 54, 24, 10, 55, 25],
          [24, 45, 15, 46, 46, 16],

          // 38
          [4, 152, 122, 18, 153, 123],
          [13, 74, 46, 32, 75, 47],
          [48, 54, 24, 14, 55, 25],
          [42, 45, 15, 32, 46, 16],

          // 39
          [20, 147, 117, 4, 148, 118],
          [40, 75, 47, 7, 76, 48],
          [43, 54, 24, 22, 55, 25],
          [10, 45, 15, 67, 46, 16],

          // 40
          [19, 148, 118, 6, 149, 119],
          [18, 75, 47, 31, 76, 48],
          [34, 54, 24, 34, 55, 25],
          [20, 45, 15, 61, 46, 16]
        ];

        var qrRSBlock = function(totalCount, dataCount) {
          var _this = {};
          _this.totalCount = totalCount;
          _this.dataCount = dataCount;
          return _this;
        };

        var _this = {};

        var getRsBlockTable = function(typeNumber, errorCorrectLevel) {

          switch(errorCorrectLevel) {
          case QRErrorCorrectLevel.L :
            return RS_BLOCK_TABLE[(typeNumber - 1) * 4 + 0];
          case QRErrorCorrectLevel.M :
            return RS_BLOCK_TABLE[(typeNumber - 1) * 4 + 1];
          case QRErrorCorrectLevel.Q :
            return RS_BLOCK_TABLE[(typeNumber - 1) * 4 + 2];
          case QRErrorCorrectLevel.H :
            return RS_BLOCK_TABLE[(typeNumber - 1) * 4 + 3];
          default :
            return undefined;
          }
        };

        _this.getRSBlocks = function(typeNumber, errorCorrectLevel) {

          var rsBlock = getRsBlockTable(typeNumber, errorCorrectLevel);

          if (typeof rsBlock == 'undefined') {
            throw new Error('bad rs block @ typeNumber:' + typeNumber +
                '/errorCorrectLevel:' + errorCorrectLevel);
          }

          var length = rsBlock.length / 3;

          var list = new Array();

          for (var i = 0; i < length; i += 1) {

            var count = rsBlock[i * 3 + 0];
            var totalCount = rsBlock[i * 3 + 1];
            var dataCount = rsBlock[i * 3 + 2];

            for (var j = 0; j < count; j += 1) {
              list.push(qrRSBlock(totalCount, dataCount) );
            }
          }

          return list;
        };

        return _this;
      }();

      //---------------------------------------------------------------------
      // qrBitBuffer
      //---------------------------------------------------------------------

      var qrBitBuffer = function() {

        var _buffer = new Array();
        var _length = 0;

        var _this = {};

        _this.getBuffer = function() {
          return _buffer;
        };

        _this.getAt = function(index) {
          var bufIndex = Math.floor(index / 8);
          return ( (_buffer[bufIndex] >>> (7 - index % 8) ) & 1) == 1;
        };

        _this.put = function(num, length) {
          for (var i = 0; i < length; i += 1) {
            _this.putBit( ( (num >>> (length - i - 1) ) & 1) == 1);
          }
        };

        _this.getLengthInBits = function() {
          return _length;
        };

        _this.putBit = function(bit) {

          var bufIndex = Math.floor(_length / 8);
          if (_buffer.length <= bufIndex) {
            _buffer.push(0);
          }

          if (bit) {
            _buffer[bufIndex] |= (0x80 >>> (_length % 8) );
          }

          _length += 1;
        };

        return _this;
      };

      //---------------------------------------------------------------------
      // qr8BitByte
      //---------------------------------------------------------------------

      var qr8BitByte = function(data) {

        var _mode = QRMode.MODE_8BIT_BYTE;
        var _data = data;
        var _bytes = qrcode.stringToBytes(data);

        var _this = {};

        _this.getMode = function() {
          return _mode;
        };

        _this.getLength = function(buffer) {
          return _bytes.length;
        };

        _this.write = function(buffer) {
          for (var i = 0; i < _bytes.length; i += 1) {
            buffer.put(_bytes[i], 8);
          }
        };

        return _this;
      };

      //=====================================================================
      // GIF Support etc.
      //

      //---------------------------------------------------------------------
      // byteArrayOutputStream
      //---------------------------------------------------------------------

      var byteArrayOutputStream = function() {

        var _bytes = new Array();

        var _this = {};

        _this.writeByte = function(b) {
          _bytes.push(b & 0xff);
        };

        _this.writeShort = function(i) {
          _this.writeByte(i);
          _this.writeByte(i >>> 8);
        };

        _this.writeBytes = function(b, off, len) {
          off = off || 0;
          len = len || b.length;
          for (var i = 0; i < len; i += 1) {
            _this.writeByte(b[i + off]);
          }
        };

        _this.writeString = function(s) {
          for (var i = 0; i < s.length; i += 1) {
            _this.writeByte(s.charCodeAt(i) );
          }
        };

        _this.toByteArray = function() {
          return _bytes;
        };

        _this.toString = function() {
          var s = '';
          s += '[';
          for (var i = 0; i < _bytes.length; i += 1) {
            if (i > 0) {
              s += ',';
            }
            s += _bytes[i];
          }
          s += ']';
          return s;
        };

        return _this;
      };

      //---------------------------------------------------------------------
      // base64EncodeOutputStream
      //---------------------------------------------------------------------

      var base64EncodeOutputStream = function() {

        var _buffer = 0;
        var _buflen = 0;
        var _length = 0;
        var _base64 = '';

        var _this = {};

        var writeEncoded = function(b) {
          _base64 += String.fromCharCode(encode(b & 0x3f) );
        };

        var encode = function(n) {
          if (n < 0) {
            // error.
          } else if (n < 26) {
            return 0x41 + n;
          } else if (n < 52) {
            return 0x61 + (n - 26);
          } else if (n < 62) {
            return 0x30 + (n - 52);
          } else if (n == 62) {
            return 0x2b;
          } else if (n == 63) {
            return 0x2f;
          }
          throw new Error('n:' + n);
        };

        _this.writeByte = function(n) {

          _buffer = (_buffer << 8) | (n & 0xff);
          _buflen += 8;
          _length += 1;

          while (_buflen >= 6) {
            writeEncoded(_buffer >>> (_buflen - 6) );
            _buflen -= 6;
          }
        };

        _this.flush = function() {

          if (_buflen > 0) {
            writeEncoded(_buffer << (6 - _buflen) );
            _buffer = 0;
            _buflen = 0;
          }

          if (_length % 3 != 0) {
            // padding
            var padlen = 3 - _length % 3;
            for (var i = 0; i < padlen; i += 1) {
              _base64 += '=';
            }
          }
        };

        _this.toString = function() {
          return _base64;
        };

        return _this;
      };

      //---------------------------------------------------------------------
      // base64DecodeInputStream
      //---------------------------------------------------------------------

      var base64DecodeInputStream = function(str) {

        var _str = str;
        var _pos = 0;
        var _buffer = 0;
        var _buflen = 0;

        var _this = {};

        _this.read = function() {

          while (_buflen < 8) {

            if (_pos >= _str.length) {
              if (_buflen == 0) {
                return -1;
              }
              throw new Error('unexpected end of file./' + _buflen);
            }

            var c = _str.charAt(_pos);
            _pos += 1;

            if (c == '=') {
              _buflen = 0;
              return -1;
            } else if (c.match(/^\s$/) ) {
              // ignore if whitespace.
              continue;
            }

            _buffer = (_buffer << 6) | decode(c.charCodeAt(0) );
            _buflen += 6;
          }

          var n = (_buffer >>> (_buflen - 8) ) & 0xff;
          _buflen -= 8;
          return n;
        };

        var decode = function(c) {
          if (0x41 <= c && c <= 0x5a) {
            return c - 0x41;
          } else if (0x61 <= c && c <= 0x7a) {
            return c - 0x61 + 26;
          } else if (0x30 <= c && c <= 0x39) {
            return c - 0x30 + 52;
          } else if (c == 0x2b) {
            return 62;
          } else if (c == 0x2f) {
            return 63;
          } else {
            throw new Error('c:' + c);
          }
        };

        return _this;
      };

      //---------------------------------------------------------------------
      // gifImage (B/W)
      //---------------------------------------------------------------------

      var gifImage = function(width, height) {

        var _width = width;
        var _height = height;
        var _data = new Array(width * height);

        var _this = {};

        _this.setPixel = function(x, y, pixel) {
          _data[y * _width + x] = pixel;
        };

        _this.write = function(out) {

          //---------------------------------
          // GIF Signature

          out.writeString('GIF87a');

          //---------------------------------
          // Screen Descriptor

          out.writeShort(_width);
          out.writeShort(_height);

          out.writeByte(0x80); // 2bit
          out.writeByte(0);
          out.writeByte(0);

          //---------------------------------
          // Global Color Map

          // black
          out.writeByte(0x00);
          out.writeByte(0x00);
          out.writeByte(0x00);

          // white
          out.writeByte(0xff);
          out.writeByte(0xff);
          out.writeByte(0xff);

          //---------------------------------
          // Image Descriptor

          out.writeString(',');
          out.writeShort(0);
          out.writeShort(0);
          out.writeShort(_width);
          out.writeShort(_height);
          out.writeByte(0);

          //---------------------------------
          // Local Color Map

          //---------------------------------
          // Raster Data

          var lzwMinCodeSize = 2;
          var raster = getLZWRaster(lzwMinCodeSize);

          out.writeByte(lzwMinCodeSize);

          var offset = 0;

          while (raster.length - offset > 255) {
            out.writeByte(255);
            out.writeBytes(raster, offset, 255);
            offset += 255;
          }

          out.writeByte(raster.length - offset);
          out.writeBytes(raster, offset, raster.length - offset);
          out.writeByte(0x00);

          //---------------------------------
          // GIF Terminator
          out.writeString(';');
        };

        var bitOutputStream = function(out) {

          var _out = out;
          var _bitLength = 0;
          var _bitBuffer = 0;

          var _this = {};

          _this.write = function(data, length) {

            if ( (data >>> length) != 0) {
              throw new Error('length over');
            }

            while (_bitLength + length >= 8) {
              _out.writeByte(0xff & ( (data << _bitLength) | _bitBuffer) );
              length -= (8 - _bitLength);
              data >>>= (8 - _bitLength);
              _bitBuffer = 0;
              _bitLength = 0;
            }

            _bitBuffer = (data << _bitLength) | _bitBuffer;
            _bitLength = _bitLength + length;
          };

          _this.flush = function() {
            if (_bitLength > 0) {
              _out.writeByte(_bitBuffer);
            }
          };

          return _this;
        };

        var getLZWRaster = function(lzwMinCodeSize) {

          var clearCode = 1 << lzwMinCodeSize;
          var endCode = (1 << lzwMinCodeSize) + 1;
          var bitLength = lzwMinCodeSize + 1;

          // Setup LZWTable
          var table = lzwTable();

          for (var i = 0; i < clearCode; i += 1) {
            table.add(String.fromCharCode(i) );
          }
          table.add(String.fromCharCode(clearCode) );
          table.add(String.fromCharCode(endCode) );

          var byteOut = byteArrayOutputStream();
          var bitOut = bitOutputStream(byteOut);

          // clear code
          bitOut.write(clearCode, bitLength);

          var dataIndex = 0;

          var s = String.fromCharCode(_data[dataIndex]);
          dataIndex += 1;

          while (dataIndex < _data.length) {

            var c = String.fromCharCode(_data[dataIndex]);
            dataIndex += 1;

            if (table.contains(s + c) ) {

              s = s + c;

            } else {

              bitOut.write(table.indexOf(s), bitLength);

              if (table.size() < 0xfff) {

                if (table.size() == (1 << bitLength) ) {
                  bitLength += 1;
                }

                table.add(s + c);
              }

              s = c;
            }
          }

          bitOut.write(table.indexOf(s), bitLength);

          // end code
          bitOut.write(endCode, bitLength);

          bitOut.flush();

          return byteOut.toByteArray();
        };

        var lzwTable = function() {

          var _map = {};
          var _size = 0;

          var _this = {};

          _this.add = function(key) {
            if (_this.contains(key) ) {
              throw new Error('dup key:' + key);
            }
            _map[key] = _size;
            _size += 1;
          };

          _this.size = function() {
            return _size;
          };

          _this.indexOf = function(key) {
            return _map[key];
          };

          _this.contains = function(key) {
            return typeof _map[key] != 'undefined';
          };

          return _this;
        };

        return _this;
      };

      var createImgTag = function(width, height, getPixel, alt) {

        var gif = gifImage(width, height);
        for (var y = 0; y < height; y += 1) {
          for (var x = 0; x < width; x += 1) {
            gif.setPixel(x, y, getPixel(x, y) );
          }
        }

        var b = byteArrayOutputStream();
        gif.write(b);

        var base64 = base64EncodeOutputStream();
        var bytes = b.toByteArray();
        for (var i = 0; i < bytes.length; i += 1) {
          base64.writeByte(bytes[i]);
        }
        base64.flush();

        var img = '';
        img += '<img';
        img += '\u0020src="';
        img += 'data:image/gif;base64,';
        img += base64;
        img += '"';
        img += '\u0020width="';
        img += width;
        img += '"';
        img += '\u0020height="';
        img += height;
        img += '"';
        if (alt) {
          img += '\u0020alt="';
          img += alt;
          img += '"';
        }
        img += '/>';

        return img;
      };

      //---------------------------------------------------------------------
      // returns qrcode function.

      return qrcode;
    }();

    (function (factory) {
      if (typeof define === 'function' && define.amd) {
          define([], factory);
      } else if (typeof exports === 'object') {
          module.exports = factory();
      }
    }(function () {
        return qrcode;
    }));
    //---------------------------------------------------------------------
    //
    // QR Code Generator for JavaScript UTF8 Support (optional)
    //
    // Copyright (c) 2011 Kazuhiko Arase
    //
    // URL: http://www.d-project.com/
    //
    // Licensed under the MIT license:
    //  http://www.opensource.org/licenses/mit-license.php
    //
    // The word 'QR Code' is registered trademark of
    // DENSO WAVE INCORPORATED
    //  http://www.denso-wave.com/qrcode/faqpatent-e.html
    //
    //---------------------------------------------------------------------

    !function(qrcode) {

      //---------------------------------------------------------------------
      // overwrite qrcode.stringToBytes
      //---------------------------------------------------------------------

      qrcode.stringToBytes = function(s) {
        // http://stackoverflow.com/questions/18729405/how-to-convert-utf8-string-to-byte-array
        function toUTF8Array(str) {
          var utf8 = [];
          for (var i=0; i < str.length; i++) {
            var charcode = str.charCodeAt(i);
            if (charcode < 0x80) utf8.push(charcode);
            else if (charcode < 0x800) {
              utf8.push(0xc0 | (charcode >> 6),
                  0x80 | (charcode & 0x3f));
            }
            else if (charcode < 0xd800 || charcode >= 0xe000) {
              utf8.push(0xe0 | (charcode >> 12),
                  0x80 | ((charcode>>6) & 0x3f),
                  0x80 | (charcode & 0x3f));
            }
            // surrogate pair
            else {
              i++;
              // UTF-16 encodes 0x10000-0x10FFFF by
              // subtracting 0x10000 and splitting the
              // 20 bits of 0x0-0xFFFFF into two halves
              charcode = 0x10000 + (((charcode & 0x3ff)<<10)
                | (str.charCodeAt(i) & 0x3ff));
              utf8.push(0xf0 | (charcode >>18),
                  0x80 | ((charcode>>12) & 0x3f),
                  0x80 | ((charcode>>6) & 0x3f),
                  0x80 | (charcode & 0x3f));
            }
          }
          return utf8;
        }
        return toUTF8Array(s);
      };

    }(qrcode);

    return qrcode; // eslint-disable-line no-undef
}()));
