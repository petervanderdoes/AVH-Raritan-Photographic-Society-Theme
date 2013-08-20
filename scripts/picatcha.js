 // var jQuery;
$P = jQuery;

/**************************JSON lib support for IE 7 & 8*************************************************/

var JSON;if(!JSON){JSON={}}(function(){'use strict';function f(n){return n<10?'0'+n:n}if(typeof Date.prototype.toJSON!=='function'){Date.prototype.toJSON=function(key){return isFinite(this.valueOf())?this.getUTCFullYear()+'-'+f(this.getUTCMonth()+1)+'-'+f(this.getUTCDate())+'T'+f(this.getUTCHours())+':'+f(this.getUTCMinutes())+':'+f(this.getUTCSeconds())+'Z':null};String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(key){return this.valueOf()}}var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'},rep;function quote(string){escapable.lastIndex=0;return escapable.test(string)?'"'+string.replace(escapable,function(a){var c=meta[a];return typeof c==='string'?c:'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+string+'"'}function str(key,holder){var i,k,v,length,mind=gap,partial,value=holder[key];if(value&&typeof value==='object'&&typeof value.toJSON==='function'){value=value.toJSON(key)}if(typeof rep==='function'){value=rep.call(holder,key,value)}switch(typeof value){case'string':return quote(value);case'number':return isFinite(value)?String(value):'null';case'boolean':case'null':return String(value);case'object':if(!value){return'null'}gap+=indent;partial=[];if(Object.prototype.toString.apply(value)==='[object Array]'){length=value.length;for(i=0;i<length;i+=1){partial[i]=str(i,value)||'null'}v=partial.length===0?'[]':gap?'[\n'+gap+partial.join(',\n'+gap)+'\n'+mind+']':'['+partial.join(',')+']';gap=mind;return v}if(rep&&typeof rep==='object'){length=rep.length;for(i=0;i<length;i+=1){if(typeof rep[i]==='string'){k=rep[i];v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v)}}}}else{for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v)}}}}v=partial.length===0?'{}':gap?'{\n'+gap+partial.join(',\n'+gap)+'\n'+mind+'}':'{'+partial.join(',')+'}';gap=mind;return v}}if(typeof JSON.stringify!=='function'){JSON.stringify=function(value,replacer,space){var i;gap='';indent='';if(typeof space==='number'){for(i=0;i<space;i+=1){indent+=' '}}else if(typeof space==='string'){indent=space}rep=replacer;if(replacer&&typeof replacer!=='function'&&(typeof replacer!=='object'||typeof replacer.length!=='number')){throw new Error('JSON.stringify')}return str('',{'':value})}}if(typeof JSON.parse!=='function'){JSON.parse=function(text,reviver){var j;function walk(holder,key){var k,v,value=holder[key];if(value&&typeof value==='object'){for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=walk(value,k);if(v!==undefined){value[k]=v}else{delete value[k]}}}}return reviver.call(holder,key,value)}text=String(text);cx.lastIndex=0;if(cx.test(text)){text=text.replace(cx,function(a){return'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4)})}if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,'@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,']').replace(/(?:^|:|,)(?:\s*\[)+/g,''))){j=eval('('+text+')');return typeof reviver==='function'?walk({'':j},''):j}throw new SyntaxError('JSON.parse')}}}());

/**************************PICATCHA JAVASCRIPT*************************************************/
/* You need to enter public key that you've received in Public_Key section of given array */

var Picatcha = {
    TOTAL_STAGES: 1,
    API_SERVER: 'http://api.picatcha.com',
    PUBLIC_KEY: '',
    FORMAT: '2',
    STYLE: '#2a1f19',
    LINK: '1',
    IMG_SIZE: '75',
    NOISE_LEVEL:0,
    NOISE_TYPE:0,
    IECOMPATIBLE: true,
    MOBILE: false,

    setCustomization: function (params) {
        var map = [
            ['format', 'FORMAT'],
            ['color', 'STYLE'],
            ['link', 'LINK'],
            ['image_size', 'IMG_SIZE'],
            ['noise_level', 'NOISE_LEVEL'],
            ['noise_type', 'NOISE_TYPE']
        ];
        for(var i=0; i < map.length; i++){
            var param_key = map[i][0];
            var obj_key = map[i][1];
            if(params[param_key] != undefined){
                Picatcha[obj_key] = params[param_key];
            }
        }
        PicatchaOptions = {
            'lang': params['lang'],
            'langOverride': params['langOverride']
        };
    },
    create: function (element_id) {
        // variables
        this.element_id = element_id;
        this.total_stages_num = null;
        this.cur_stage_idx = null;
        this.challenge_data = null;
        this.stage_htmls = null;
        this.elm = $P('#' + element_id);
        this.elm_prefix = element_id;
        Picatcha.IECOMPATIBLE = navigator.appVersion.search('MSIE 8.0') > 0 ||
        navigator.appVersion.search('MSIE 7.0') > 0 ||
        navigator.appVersion.search('MSIE 6.0') > 0 || document.documentMode<9;

        //check if mobile, if so, do stuff:
        if(RegExp('/Mobile|Android|BlackBerry|iPhone|iPad|Windows Phone/').test(navigator.appVersion)){
            if(Picatcha.IMG_SIZE==75){
                Picatcha.IMG_SIZE='60';
            }
            Picatcha.MOBILE = true;
        }

        // check for HTTP or HTTPS
        if(window.location.protocol == "https:"){
            this.API_SERVER = this.API_SERVER.replace('http:', 'https:');
        }


        Picatcha._init_build_html();
        Picatcha._init_add_callback();
        Picatcha.refresh();
        $P(window).unload(function(){Picatcha.refresh()});
    },

    _init_add_callback: function () {
        // When images are clicked
        $P('#picatcha').on('click', 'img', function (event) {
            var img_id, ckbx, img;
            img_id = this.id.substring('picatcha_img_'.length);
            ckbx = Picatcha.elm.find('#picatcha_img_checkbox_' + img_id).first();
            img = Picatcha.elm.find('#picatcha_img_' + img_id).first();

            if (ckbx.attr('checked')) {
                img.removeClass('selected');
                if (Picatcha.IECOMPATIBLE) {
                    ckbx.attr('checked', false);
                }
            } else {
                img.addClass('selected');
                if (Picatcha.IECOMPATIBLE) {
                    ckbx.attr('checked', true);
                }
            }
        });
        // When refresh button is clicked
        $P('#picatcha').on('click', '.picatcha_refresh', function () {
            Picatcha.refresh();
        });
    },
    //back and forth language library
    languages:{
        "English": "en",
        "Français": "fr",
        "ελληνικά": "el",
        "Español": "es",
        "Deutch": "de",
        "हिंदी": "hi",
        "Magyar": "hu",
        "Íslenska": "is",
        "Pусский": "ru",
        "中国": "zh",
        "العربية":"ar",
        "Filipino": "tl",
        "Italiano": "it",
        "Việt": "vi",
        "Nederlands": "nl",
        "Portugês": "pt",
        "Türkçe": "tr",
        "Slovenských": "sk",
        "en": "English",
        "fr": "Français",
        "es": "Español",
        "el": "ελληνικά",
        "de": "Deutch",
        "hi": "हिंदी",
        "hu": "Magyar",
        "is": "Íslenska",
        "ru": "Pусский",
        "zh": "中国",
        "ar": "العربية",
        "tl": "Filipino",
        "it": "Italiano",
        "vi": "Việt",
        "nl": "Nederlands",
        "pt": "Portugês",
        "tr": "Türkçe",
        "sk": "Slovenských"
    },

    translate: function(lang){
        if(lang==undefined || lang==null){
            lang='en';
        }

        if(lang=="en"){
            $P('.default_str').css("display","inline-block");
            $P('.picatcha_question_str').css("display","inline-block");
            $P('.picatcha_translated_str').css("display","none");
        }else{
            string = $P('.picatcha_question_str').html();
            $P.ajax({
                //be sure to change this for the dev or production server!
                url: this.API_SERVER+'/t?lang='+lang+'&str='+string,
                dataType:'jsonp',
                jsonpCallback: "handleResponse",
                success: function(data){
                    $P('.picatcha_translated_str').css("display","inline-block");
                    $P('.default_str').css("display","none");
                    $P('.picatcha_question_str').css("display","none");
                    $P('.picatcha_translated_str')[0].innerHTML=data.data.translations[0].question+
                    "<b>"+data.data.translations[0].category+"</b>";
                }
            });
        }
    },
    _init_build_html: function () {
        var html, question;

        html = Picatcha.elm.addClass('picatcha');
        html.append('<a id="picatcha_mini_top_link" href="http://www.picatcha.com">Powered By Picatcha</a>');
        html.append('<div class="picatcha_mesg">');

        question = $P('<p id="picatcha_question_id" class="picatcha_question">');
        //translator
        if(typeof PicatchaOptions !='undefined' && PicatchaOptions.langOverride==1){
            question.append('<select id="picatchaSelectLanguage" onchange=\'Picatcha.translate(this.value);PicatchaOptions.lang=this.value;\' style=\'font-size:10px;\'>'+
            '<option value="en">English</option>'+
            '<option value="es">Español</option>'+
            '<option value="fr">Français</option>'+
            '<option value="el">ελληνικά</option>'+
            '<option value="de">Deutch</option>'+
            '<option value="hi">हिंदी</option>'+
            '<option value="hu">Magyar</option>'+
            '<option value="is">Íslenska</option>'+
            '<option value="ru">Pусский</option>'+
            '<option value="zh">中国</option>'+
            '<option value="ar">العربية</option>'+
            '<option value="tl">Filipino</option>'+
            '<option value="it">Italiano</option>'+
            '<option value="vi">Việt</option>'+
            '<option value="nl">Nederlands</option>'+
            '<option value="pt">Portugês</option>'+
            '<option value="tr">Türkçe</option>'+
            '<option value="sk">Slovenských</option>'+
            '</select><br />');
        }
        if (Picatcha.TOTAL_STAGES > 1) {
            question.append('<span class="picatcha_step">Step <span class="picatcha_cur_step_num"></span> of <span class="picatcha_total_step"></span>: ');
        }
        question.append('<span class="refresh_str"></span> <span class="default_str"></span> <span class="picatcha_question_str"></span><span class="picatcha_translated_str"></span>');
        html.append(question);
        html.append('<input type="hidden" name="picatcha[token]" class="picatcha_token" value="" />');
        html.append('<div class="picatcha_cur_step">');
        html.append('<div class="picatcha_link"><span class="picatcha_ad_str"></span></div>');

        //Audio button
        if (!Picatcha.IECOMPATIBLE){

        //html.append('<a class="picatchaAudioButton" onclick="Picatcha.audio()">Audio</a>');
        }
        //refresh button
        html.append('<a class="picatchaRefreshButton" onclick="Picatcha.refresh()">Refresh</a>');

        var img_size = Picatcha.IMG_SIZE;
        var left_index = 0;


        if(Picatcha.LINK != "1") {
            $P(".picatcha_link").css("display", "none");
        }
        $P(".picatcha_td").width(Picatcha.IMG_SIZE).height(Picatcha.IMG_SIZE);
    },

    audio: function () {
        var data, url;
        data = {
            s: this.TOTAL_STAGES,
            k: this.PUBLIC_KEY,
            f: this.FORMAT
        };
        url = Picatcha.API_SERVER + '/ga';
        Picatcha.elm.find('.refresh_str').text("Refresh if you do not see any image ");
        Picatcha.elm.find('.refresh_str').css('display', 'block');

        $P.ajax({
            url: url,
            data: data,
            dataType: 'jsonp',
            success: function (data, textStatus, jqXHR) {

                Picatcha.challenge_data = data;
                Picatcha.total_stages_num = data.s.length;
                //Set Token
                Picatcha.token = data.t
                Picatcha.elm.find('.picatcha_token').val(Picatcha.token);

                // Set default str and removing the refresh str
                Picatcha.elm.find('.refresh_str').text("");
                Picatcha.elm.find('.default_str').text("Type what you hear:");
                Picatcha.elm.find('.refresh_str').css('display', 'none');


                // Show the first stage
                Picatcha._show_audio_stage(0);

                //fade in stage
                $P('#picatcha_table').animate({opacity:1});

                //change the color
                Picatcha.change_colors();

                // change the color of the powered by link if it ends up white...
                if($P('.picatcha_link a').css('color')=="rgb(255, 255, 255)"){$('.picatcha_link a').css('color','#333')}

                //translate, if necessary
                if(typeof PicatchaOptions !='undefined' && PicatchaOptions.lang!='en'){
                    if(PicatchaOptions.langOverride==1){
                        $P('#picatchaSelectLanguage')[0].value=PicatchaOptions.lang;
                    }
                    Picatcha.translate(PicatchaOptions.lang);

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                Picatcha._show_error('Oops, some minor issue! Please press the refresh button to generate the CAPTCHA ');
            }
        });
    },

    refresh: function () {
        var data, url;
        data = {
            s: this.TOTAL_STAGES,
            k: this.PUBLIC_KEY,
            f: this.FORMAT,
            is: this.IMG_SIZE,
            nl:this.NOISE_LEVEL,
            nt:this.NOISE_TYPE
        };
        url = Picatcha.API_SERVER + '/g';
        Picatcha.elm.find('.refresh_str').text("Refresh if you do not see any image ");
        Picatcha.elm.find('.refresh_str').css('display', 'block');

        $P.ajax({
            url: url,
            data: data,
            dataType: 'jsonp',
            success: function (data, textStatus, jqXHR) {
                //if a free user, recalculate the image size appropriately
                Picatcha.IMG_SIZE = data.is;

                Picatcha.challenge_data = data;
                Picatcha.total_stages_num = data.s.length;

                // Set default str and removing the refresh str
                Picatcha.elm.find('.refresh_str').text("");
                Picatcha.elm.find('.default_str').text("Select ALL the images of ");
                Picatcha.elm.find('.refresh_str').css('display', 'none');

                // Set token
                Picatcha.token = data.t;
                Picatcha.elm.find('.picatcha_token').val(Picatcha.token);
                // Set question, total stage
                Picatcha.elm.find('.picatcha_total_step').text(data.s.length);

                // Show the first stage
                Picatcha._show_stage(0);

                //add in the advertisement link
                if(data.s[0].misc!=''){
                    //Picatcha._get_ad_msg();
                    Picatcha.elm.find('.picatcha_ad_str').html(Picatcha._get_ad_msg(data.s[0].misc));
                }

                //fade in stage
                $P('#picatcha_table').animate({opacity:1});

                //change the color
                Picatcha.change_colors();
                if(Picatcha.MOBILE){
                    Picatcha.responsiveWidth();
                    $P(window).resize(function(){Picatcha.responsiveWidth()});
                }


                // change the color of the powered by link if it ends up white...
                //if($P('.picatcha_link a').css('color')=="rgb(255, 255, 255)"){$('.picatcha_link a').css('color','#333')}

                Picatcha.trackUsage.setup();

                //translate, if necessary
                if(typeof PicatchaOptions !='undefined' && PicatchaOptions.lang!='en'){
                    if(PicatchaOptions.langOverride==1){
                        $P('#picatchaSelectLanguage')[0].value=PicatchaOptions.lang;
                    }
                    Picatcha.translate(PicatchaOptions.lang);

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                Picatcha._show_error('Oops, some minor issue! Please press the refresh button to generate the CAPTCHA ');
            }
        });
    },

    _build_stages: function (stages_data) {
        var i;
        Picatcha.stage_htmls = []; // TODO destroy?
        for (i = 0; i < stages_data.length; i += 1) {
            var stage_data = stages_data[i];
            Picatcha.stage_htmls.push(Picatcha._build_stage(i, stage_data));
        }
    },

    _build_stage: function (stage_idx, stage_data) {
        var html, table, tr, i, j;
        html = $P('<div>').attr('id', 'stage_' + stage_idx);
        /* Create hidden input fields */
        html.append('<input type="hidden" name="picatcha[stages]" value="' + stage_idx + '" />');
        Picatcha.FORMAT = (stage_data.i.length/2)-2;
        imageGrid = $P('<div id="picatcha_table">').css('min-height',2*(parseInt(Picatcha.IMG_SIZE)+6)).css('max-width',(parseInt(Picatcha.FORMAT)+2)*(parseInt(Picatcha.IMG_SIZE)+6));
        //diable picatcha ad location
        if (stage_data.misc==''){
            picatcha_ad_location=-1;
        }else{
            picatcha_ad_location = stage_data.i.length/2;
        }

        for (var i = 0; i < stage_data.i.length; i++ ){
            var img_url = Picatcha.API_SERVER + '/media/i/' + stage_data.i[i];
            var img_id = stage_idx + '_' + i; // image id for html element

            div = $P('<span>');
            div.attr('class','picatcha_td');
            if(i == picatcha_ad_location) {
                div.append('<label for="picatcha_img_checkbox_'+ img_id +'" ><span class="picatcha_preview" id="' + img_url + '"><a target="_blank" id="picatcha_ad_img_link" href="http://www.picatcha.com"><img src="' + img_url + '" alt="Picatcha image" class="img_btn" id="picatcha_img_' + img_id + '"/></a></span></label>');
                div.append('<input type="checkbox" style="display:none" name="picatcha[r][s' + stage_idx + '][]" id="picatcha_img_checkbox_' + img_id + '" value="' + stage_data.i[i] + '" />');
            }
            else {
                div.append('<label for="picatcha_img_checkbox_'+ img_id +'" ><span class="picatcha_preview" id="' + img_url + '"><img src="' + img_url + '" alt="Picatcha image" class="img_btn" id="picatcha_img_' + img_id + '"/></span></label>');
                div.append('<input type="checkbox" style="display:none" name="picatcha[r][s' + stage_idx + '][]" id="picatcha_img_checkbox_' + img_id + '" value="' + stage_data.i[i] + '" />');
            }
            imageGrid.append(div);
        }

        //html.append(table);
        html.append(imageGrid);

        return html.html();
    },

    _build_audio_stage: function (stage_idx, stage_data) {
    var audio_url = Picatcha.API_SERVER + '/media/i/' + stage_data.i[0];
    Picatcha.elm.find('.picatcha_question_str').text("");
    var html, textbox;
    html = $P('<div>').attr('id', 'stage_' + stage_idx);
    /*Create a widget to play audio*/
    audiobox=$P('<audio id="picatcha_audio_widget" controls="controls"><source src="'+audio_url+'" type="audio/mpeg" /><embed height="50px" width="100px" src="'+audio_url+'" /></audio>');
    html.append(audiobox);

    /* Create hidden input fields */
    html.append('<input type="hidden" name="picatcha[stages]" value="' + stage_idx + '" />');
    /*Create a text-box to get input data*/
    textbox=$P('<input type="text" name="picatcha[r][s' + stage_idx + '][]">');
    html.append(textbox);
        return html.html();
    },

    _deselect_all: function (stage_idx) {
        Picatcha.elm.find('.picatcha_cur_step input[type=checkbox]').removeAttr("checked");
        Picatcha.elm.find('.picatcha_cur_step .img_btn').removeClass('selected');
    },

    _get_ad_href: function(href_data) {
        picatcha_ad_href = Picatcha.API_SERVER + '/redirect?t=' + Picatcha.token + href_data;
        return picatcha_ad_href;
    },

    _get_ad_msg: function (ad) {
        //also make sure that the powered by doesn't get hidden
        $P('.picatcha_link').css('display','initial');
        var picatcha_ad_href = "";
        var picatcha_ad_msg = "";
        switch(ad)
        {
        case "1_sponsored":
            picatcha_ad_href = Picatcha._get_ad_href("&u=http://www.kloudless.com");
            picatcha_ad_msg = '<a target="_blank" href="'+ picatcha_ad_href +'">Kloudless: Inbox getting crowdy?</a>';
            break;
        case "2_sponsored":
            picatcha_ad_href = Picatcha._get_ad_href("&u=http://bit.ly/wallitappweb");
            picatcha_ad_msg = '<a target="_blank" href="'+ picatcha_ad_href +'">Wallit: Walls for Places with Augmented Reality</a>';
            break;
        case "3_sponsored":
            picatcha_ad_href = Picatcha._get_ad_href("&u=http://www.gooverseas.com");
            picatcha_ad_msg = '<a target="_blank" href="'+ picatcha_ad_href +'">Go Overseas</a>: Teach|Study|Intern|Volunetter abroad</a>';
            break;
        case "4_sponsored":
            picatcha_ad_href = Picatcha._get_ad_href("&u=http://www.lewistaylorshirts.com");
            picatcha_ad_msg = '<a target="_blank" href="'+ picatcha_ad_href +'">Lewis & Taylor: Custom Design Shirts</a>';
            break;
        default:
            return ""
        }
        $P("#picatcha_ad_img_link").attr("href", picatcha_ad_href);
        return picatcha_ad_msg;

    },

    _show_stage: function (stage_idx) {
        var stage_data, html;
        Picatcha.cur_stage_idx = stage_idx;
        stage_data = Picatcha.challenge_data.s[stage_idx];
        html = Picatcha._build_stage(stage_idx, stage_data);
        Picatcha.elm.find('.picatcha_question_str').text(stage_data.q);
        //Picatcha.elm.find('.picatcha_ad_str').html(Picatcha._get_ad_msg(stage_data.misc));
        var category = stage_data.q.replace('_',' ');
        Picatcha.elm.find('.picatcha_question_str').text(category);
        Picatcha.elm.find('.picatcha_cur_step').html(html);
        Picatcha.elm.find('.picatcha_cur_step_num').text(stage_idx + 1);

        Picatcha._deselect_all(stage_idx);
    },

    _show_audio_stage: function (stage_idx) {
        var stage_data, html;
        Picatcha.cur_stage_idx = stage_idx;
        stage_data = Picatcha.challenge_data.s[stage_idx];
        html = Picatcha._build_audio_stage(stage_idx, stage_data);
        var category = stage_data.q.replace('_',' ');
        Picatcha.elm.find('.picatcha_cur_step').html(html);
        Picatcha.elm.find('.picatcha_cur_step_num').text(stage_idx + 1);

        Picatcha._deselect_all(stage_idx);
    },

    _show_error: function (mesg) {
        Picatcha.elm.find('.picatcha_mesg').text(mesg);
        // TODO hide rest elements
    },
    wordpressHelper: function(){
        // Wordpress Helper replaced by a button to validate keys
        // But keeping it here in case of a future need
        //code to enable the API key check on wordpress installations
        //for jQuery 1.6.x and earlier
        //$P('#picatchaPublicKey').bind('blur', function(event){Picatcha.clientKeyCheck(this.value,'pub')});
        //$P('#picatchaPrivateKey').bind('blur', function(event){Picatcha.clientKeyCheck(this.value,'pri')});
        //for jQuery 1.7 and later
        //$P('#picatchaPublicKey').on('blur', function(event){Picatcha.clientKeyCheck(this.value,'pub')});
        //$P('#picatchaPrivateKey').on('blur', function(event){Picatcha.clientKeyCheck(this.value,'pri')});
    },
    wpCheckKeysBtn: function(){
        if($P('#picatchaPublicKey').val()!=''){
            Picatcha.clientKeyCheck($P('#picatchaPublicKey').val(), 'pub');
        }else{
            jQuery('#validpubKey').empty();
        }
        if($P('#picatchaPrivateKey').val()!=''){
            Picatcha.clientKeyCheck($P('#picatchaPrivateKey').val(), 'pri');
        }else{
            jQuery('#validpriKey').empty();
        }
    },
    //Begin Joomla JavaScript Additions
    joomlaHelper: function(){
        //helper to set up functionality of the joomla admin back-end
        //attach key checking to the Validate button
        jQuery('#PicatchaKeyValidationCheck').bind('click',function(event){Picatcha.jCheckKeysBtn()});
    },
    jCheckKeysBtn: function(){
        picatchaKeysToCheck =new Array();
        if(jQuery('#jform_params_picatchaPublicKey').val()){
            picatchaKeysToCheck.push(['pub',jQuery('#jform_params_picatchaPublicKey').val()]);
        }
        if(jQuery('#jform_params_picatchaPrivateKey').val()){
            picatchaKeysToCheck.push(['pri',jQuery('#jform_params_picatchaPrivateKey').val()]);
        }
        for (k=0; k<picatchaKeysToCheck.length; k++){
            this.joomlaClientKeyCheck(picatchaKeysToCheck[k][1],picatchaKeysToCheck[k][0]);
        }
    },
    joomlaClientKeyCheck: function(key,type){
        jQuery.ajax({
            url:this.API_SERVER+'/vk?'+picatchaKeysToCheck[k][0]+'='+picatchaKeysToCheck[k][1],
            dataType:'jsonp',
            jsonpCallback: 'keyHandler'+picatchaKeysToCheck[k][0],
            success:function(data){
                keyType={'pub':'Public','pri':'Private'};
                if(data.s==true){
                    jQuery('#jform_params_picatcha'+keyType[type]+'Key').css('color','green');
                }else{
                    jQuery('#jform_params_picatcha'+keyType[type]+'Key').css('color','red');
                }
            }
        });
    },
    clientKeyCheck: function(key, type){
        // client key check function.
        jQuery.ajax({
            url:this.API_SERVER+'/vk?'+type+'='+key,
            dataType:'jsonp',
            jsonpCallback: 'keyHandler'+type,
            success:function(data){
                if(data.s==true){
                    jQuery('#valid'+type+'Key').html('<span style="color:green;">Valid</span>');
                }else{
                    jQuery('#valid'+type+'Key').html('<span style="color:red;">Invalid!</span>');
                }
            }
        });
    },
    // End Joomla JavaScript Additions

    getContrastYIQ: function(hexcolor){
        if(hexcolor.length==4){
            var r = parseInt(hexcolor.substr(1,1)+hexcolor.substr(1,1),16);
            var g = parseInt(hexcolor.substr(2,1)+hexcolor.substr(2,1),16);
            var b = parseInt(hexcolor.substr(3,1)+hexcolor.substr(3,1),16);
            var yiq = ((r*299)+(g*587)+(b*114))/1000;
            return (yiq >= 128) ? 'black' : 'white';
        }else{
            var r = parseInt(hexcolor.substr(1,2),16);
            var g = parseInt(hexcolor.substr(3,2),16);
            var b = parseInt(hexcolor.substr(5,2),16);
            var yiq = ((r*299)+(g*587)+(b*114))/1000;
            return (yiq >= 128) ? 'black' : 'white';
        }
    },

    change_colors: function()
    {
        $P(".picatchaRefreshButton").css("color", Picatcha.getContrastYIQ(Picatcha.STYLE));
        $P("#picatcha_mini_top_link").css("color", Picatcha.getContrastYIQ(Picatcha.STYLE));
        $P(".picatchaRefreshButton").css("background-color", Picatcha.STYLE);
        $P("#picatcha_mini_top_link").css("background-color", Picatcha.STYLE);
        $P(".picatchaRefreshButton").css("border", "2px solid "+Picatcha.STYLE);
        $P("#picatcha").css("border", "1px solid "+Picatcha.STYLE);
        pixCaptchaWidth = ((parseInt(Picatcha.IMG_SIZE)+6)*(parseInt(Picatcha.FORMAT)+2));
        $P("#picatcha.picatcha").css('max-width', pixCaptchaWidth+'px');
    },

    getPosition: function(){
        var picTop = parseInt($P('#picatcha_table').offset().top);
        var picLeft = parseInt($P('#picatcha_table').offset().left);
        var position = [];
        if (picTop -115 < 0){
            if(picLeft + 120 > 0){
                position = [picTop + 10, picLeft - 150];
            }
            else {
                position = [picTop + 10, picLeft + 220];
            }
        }
        else{
            position = [picTop-115, picLeft + 110];
        }
        return position;
    },

    responsiveWidth: function(){
        var width = parseInt($P('#picatcha').css('width').slice(0,-2));
        var imageSize = (parseInt(Picatcha.IMG_SIZE)+4);

        $P('#picatcha_table').css('width',Math.floor(width/imageSize)*imageSize);
    },

    trackUsage: {
        setup: function(){
            // Set up the basic information about the stage
            this.data['Category']=$P('.picatcha_question_str').html().replace(/ /g,'_');
            this.data['Token']=$P('.picatcha_token').val();
            this.data['allEvents']=[];
            this['mousePositions']=[];
            this['position'] = $P('#picatcha').offset();
            // add all the event listeners here
            $P('#picatcha').mouseenter(function(){Picatcha.trackUsage['timer'] = setInterval("Picatcha.trackUsage.timedPosition()", 100)});
            $P('#picatcha').mouseleave(function(){clearTimeout(Picatcha.trackUsage['timer']);});
            $P('#picatcha').mouseup(function(event){Picatcha.trackUsage.data.allEvents.push(['click',event.pageX-Picatcha.trackUsage.position.left,event.pageY-Picatcha.trackUsage.position.top])});
            $P('#picatcha').mousemove(function(event){Picatcha.trackUsage.mousePositions.push([event.pageX-Picatcha.trackUsage.position.left,event.pageY-Picatcha.trackUsage.position.top])});

            //Attach a listener to post the data when the user submits
            $P('#picatcha').parent().find('[type|="submit"]').mousedown(function(){Picatcha.trackUsage.sendData()});
        },
        widgetEvent:function(id, type){
            d = new Date();
            this.data['allEvents'].push({'type':type,'id':id,'timestamp':d});
        },
        timedPosition: function(){
            index = this['mousePositions'].length - 1;
            this.data.allEvents.push(this.mousePositions[index]);
        },
        //where all the data is stored
        data:{},
        sendData: function(){
            //when the page is unloaded or picatcha refreshed, send the data to the server
            $P.ajax({
                url:Picatcha.API_SERVER+'/v1/tracking',
                data: {data:JSON.stringify(this.data)},
                dataType:'jsonp',
                jsonp:'callback',
                jsonpCallback:'jsonpCallback',
                success:function(data){
                }
            });
        }
    }
};

