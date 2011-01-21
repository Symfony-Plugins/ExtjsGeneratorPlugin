(function(){var b=Ext.lib.Ajax,i=function(j){return typeof j!="undefined"},d=Ext.emptyFn||function(){},a=Object.prototype;Ext.lib.Ajax.Queue=function(j){j=j?(j.name?j:{name:j}):{};Ext.apply(this,j,{name:"q-default",priority:5,FIFO:true,callback:null,scope:null,suspended:false,progressive:false});this.requests=new Array();this.pending=false;this.priority=this.priority>9?9:(this.priority<0?0:this.priority)};Ext.extend(Ext.lib.Ajax.Queue,Object,{add:function(j){var k=b.events?b.fireEvent("beforequeue",this,j):true;if(k!==false){this.requests.push(j);this.pending=true;b.pendingRequests++;this.manager&&this.manager.start()}},suspended:false,activeRequest:null,next:function(j){var k=j?this.requests[this.FIFO?"first":"last"]():this.requests[this.FIFO?"shift":"pop"]();if(this.requests.length==0){this.pending=false;Ext.isFunction(this.callback)&&this.callback.call(this.scope||null,this);b.events&&b.fireEvent("queueempty",this)}return k||null},clear:function(){this.suspend();b.pendingRequests-=this.requests.length;this.requests.length=0;this.pending=false;this.resume();this.next()},suspend:function(){this.suspended=true},resume:function(){this.suspended=false},requestNext:function(j){var k;this.activeRequest=null;if(!this.suspended&&(k=this.next(j))){if(k.active){this.activeRequest=b.request.apply(b,k);b.pendingRequests--}else{return this.requestNext(j)}}return this.activeRequest}});Ext.lib.Ajax.QueueManager=function(j){Ext.apply(this,j||{},{quantas:10,priorityQueues:new Array(new Array(),new Array(),new Array(),new Array(),new Array(),new Array(),new Array(),new Array(),new Array(),new Array()),queues:{}})};Ext.extend(Ext.lib.Ajax.QueueManager,Object,{quantas:10,getQueue:function(j){return this.queues[j]},createQueue:function(j){if(!j){return null}var k=new b.Queue(j);k.manager=this;this.queues[k.name]=k;var l=this.priorityQueues[k.priority];l&&l.indexOf(k.name)==-1&&l.push(k.name);return k},removeQueue:function(j){if(j&&(j=this.getQueue(j.name||j))){j.clear();this.priorityQueues[j.priority].remove(j.name);delete this.queues[j.name]}},start:function(){if(!this.started){this.started=true;this.dispatch()}return this},suspendAll:function(){forEach(this.queues,function(j){j.suspend()})},resumeAll:function(){forEach(this.queues,function(j){j.resume()});this.start()},progressive:false,stop:function(){this.started=false;return this},dispatch:function(){var m=this,l=m.queues;var j=(b.activeRequests>b.maxConcurrentRequests);while(b.pendingRequests&&!j){var k=function(p){var o=l[p],n;while(o&&!o.suspended&&o.pending&&o.requestNext()){j||(j=b.activeRequests>b.maxConcurrentRequests);if(j){break}if(o.progressive||m.progressive){break}}if(j){return false}};forEach(this.priorityQueues,function(n){!!n.length&&forEach(n,k,this);j||(j=b.activeRequests>b.maxConcurrentRequests);if(j){return false}},this)}if(b.pendingRequests||j){this.dispatch.defer(this.quantas,this)}else{this.stop()}}});Ext.apply(b,{headers:b.headers||{},defaultPostHeader:b.defaultPostHeader||"application/x-www-form-urlencoded; charset=UTF-8",defaultHeaders:b.defaultHeaders||{},useDefaultXhrHeader:!!b.useDefaultXhrHeader,defaultXhrHeader:"Ext.basex",SCRIPTTAG_POOL:[],_domRefs:[],onUnload:function(){delete b._domRefs;delete b.SCRIPTTAG_POOL},monitoredNode:function(s,m,q,j,r){var k=null,p=(j||window).document,o=p?p.getElementsByTagName("head")[0]:null;if(s&&p&&o){k=s.toUpperCase()=="SCRIPT"&&!!b.SCRIPTTAG_POOL.length?Ext.get(b.SCRIPTTAG_POOL.pop()):null;if(k){k.removeAllListeners()}else{k=Ext.get(p.createElement(s))}var n=Ext.getDom(k);n&&forEach(m||{},function(u,t){u&&(t in n)&&n.setAttribute(t,u)});if(q){var l=(q.success||q).createDelegate(q.scope||null,[q||{}],0);Ext.isIE?k.on("readystatechange",function(){this.dom.readyState=="loaded"&&l()}):k.on("load",l)}r||n.parentNode||o.appendChild(n)}b._domRefs.push(k);return k},poll:{},pollInterval:b.pollInterval||50,queueManager:new b.QueueManager(),queueAll:false,activeRequests:0,pendingRequests:0,maxConcurrentRequests:Ext.isIE?Ext.value(window.maxConnectionsPerServer,2):4,forceActiveX:false,async:true,createXhrObject:function(r,s){var o={status:{isError:false},tId:r},n=null;s||(s={});try{s.xdomain&&window.XDomainRequest&&(o.conn=new XDomainRequest());if(!i(o.conn)&&Ext.capabilities.hasActiveX&&!!Ext.value(s.forceActiveX,this.forceActiveX)){throw ("IE7forceActiveX")}o.conn||(o.conn=new XMLHttpRequest())}catch(k){var j=Ext.capabilities.hasActiveX?(s.multiPart?this.activeXMultipart:this.activeX):null;if(j){for(var p=0,m=j.length;p<m;++p){try{o.conn=new ActiveXObject(j[p]);break}catch(q){n=(k=="IE7forceActiveX"?q:k)}}}}finally{o.status.isError=!i(o.conn);o.status.error=n}return o},createExceptionObject:function(n,m,k,j,l){return{tId:n,status:k?-1:0,statusText:k?"transaction aborted":"communication failure",isAbort:k,isTimeout:j,argument:m}},encoder:encodeURIComponent,serializeForm:function(){var l=/select-(one|multiple)/i,j=/file|undefined|reset|button/i,k=/radio|checkbox/i;return function(n){var o=n.elements||(document.forms[n]||Ext.getDom(n)).elements,u=false,t=this.encoder,r,v,m,p,q="",s;forEach(o,function(w){m=w.name;s=w.type;if(!w.disabled&&m){if(l.test(s)){forEach(w.options,function(x){if(x.selected){q+=String.format("{0}={1}&",t(m),t(x.hasAttribute&&x.hasAttribute("value")&&x.getAttribute("value")!==null?x.value:x.text))}})}else{if(!j.test(s)){if(!(k.test(s)&&!w.checked)&&!(s=="submit"&&u)){q+=t(m)+"="+t(w.value)+"&";u=/submit/i.test(s)}}}}});return q.substr(0,q.length-1)}}(),getHttpStatus:function(l,k,j){var n={status:0,statusText:"",isError:false,isLocal:false,isOK:true,error:null,isAbort:!!k,isTimeout:!!j};try{if(!l||!("status" in l)){throw ("noobj")}n.status=l.status;n.readyState=l.readyState;n.isLocal=(!l.status&&location.protocol=="file:")||(Ext.isSafari&&!i(l.status));n.isOK=(n.isLocal||(n.status==304||n.status==1223||(n.status>199&&n.status<300)));n.statusText=l.statusText||""}catch(m){}return n},handleTransactionResponse:function(n,p,l,k){p=p||{};var m=null;n.isPart||b.activeRequests--;if(!n.status.isError){n.status=this.getHttpStatus(n.conn,l,k);m=this.createResponseObject(n,p.argument,l,k)}n.isPart||this.releaseObject(n);n.status.isError&&(m=Ext.apply({},m||{},this.createExceptionObject(n.tId,p.argument,!!l,!!k,n.status.error)));m.options=n.options;m.fullStatus=n.status;if(!this.events||this.fireEvent("status:"+n.status.status,n.status.status,n,m,p,l)!==false){if(n.status.isOK&&!n.status.isError){if(!this.events||this.fireEvent("response",n,m,p,l,k)!==false){var j=n.isPart?"onpart":"success";Ext.isFunction(p[j])&&p[j].call(p.scope||null,m)}}else{if(!this.events||this.fireEvent("exception",n,m,p,l,k,m.fullStatus.error)!==false){Ext.isFunction(p.failure)&&p.failure.call(p.scope||null,m,m.fullStatus.error)}}}return m},releaseObject:function(j){j&&(j.conn=null);if(j&&Ext.value(j.tId,-1)+1){if(this.poll[j.tId]){window.clearInterval(this.poll[j.tId]);delete this.poll[j.tId]}if(this.timeout[j.tId]){window.clearInterval(this.timeout[j.tId]);delete this.timeout[j.tId]}}},decodeJSON:Ext.decode,reCtypeJSON:/(application|text)\/json/i,reCtypeXML:/(application|text)\/xml/i,createResponseObject:function(w,y,m,n){var A="content-type",p={responseXML:null,responseText:"",responseStream:null,responseJSON:null,contentType:null,getResponseHeader:d,getAllResponseHeaders:d};var k={},l="";if(m!==true){try{p.responseJSON=w.conn.responseJSON||null;p.responseStream=w.conn.responseStream||null;p.contentType=w.conn.contentType||null;p.responseText=w.conn.responseText}catch(B){w.status.isError=true;w.status.error=B}try{p.responseXML=w.conn.responseXML||null}catch(z){}try{l=("getAllResponseHeaders" in w.conn?w.conn.getAllResponseHeaders():null)||"";var r;l.split("\n").forEach(function(o){(r=o.split(":"))&&r.first()&&(k[r.first().trim().toLowerCase()]=(r.last()||"").trim())})}catch(x){w.status.isError=true;w.status.error=x}finally{p.contentType=p.contentType||k[A]||""}if((w.status.isLocal||w.proxied)&&typeof p.responseText=="string"){w.status.isOK=!w.status.isError&&((w.status.status=(!!p.responseText.length)?200:404)==200);if(w.status.isOK&&((!p.responseXML&&this.reCtypeXML.test(p.contentType))||(p.responseXML&&p.responseXML.childNodes.length===0))){var C=null;try{if(Ext.capabilities.hasActiveX){C=new ActiveXObject("MSXML2.DOMDocument.3.0");C.async=false;C.loadXML(p.responseText)}else{var t=null;try{t=new DOMParser();C=t.parseFromString(p.responseText,"application/xml")}catch(j){}finally{t=null}}}catch(v){w.status.isError=true;w.status.error=v}p.responseXML=C}if(p.responseXML){var u=(p.responseXML.documentElement&&p.responseXML.documentElement.nodeName=="parsererror")||(p.responseXML.parseError||0)!==0||p.responseXML.childNodes.length===0;u||(p.contentType=k[A]=p.responseXML.contentType||"text/xml")}}if(w.options.isJSON||(this.reCtypeJSON&&this.reCtypeJSON.test(k[A]||""))){try{Ext.isObject(p.responseJSON)||(p.responseJSON=Ext.isFunction(this.decodeJSON)&&Ext.isString(p.responseText)?this.decodeJSON(p.responseText):null)}catch(q){w.status.isError=true;w.status.error=q}}}w.status.proxied=!!w.proxied;Ext.apply(p,{tId:w.tId,status:w.status.status,statusText:w.status.statusText,contentType:p.contentType||k[A],getResponseHeader:function(o){return k[(o||"").trim().toLowerCase()]},getAllResponseHeaders:function(){return l},fullStatus:w.status,isPart:w.isPart||false});w.parts&&!w.isPart&&(p.parts=w.parts);i(y)&&(p.argument=y);return p},setDefaultPostHeader:function(j){this.defaultPostHeader=j||""},setDefaultXhrHeader:function(j){this.useDefaultXhrHeader=j||false},request:function(j,l,n,p,w){var t=w=Ext.apply({async:this.async||false,headers:false,userId:null,password:null,xmlData:null,jsonData:null,queue:null,proxied:false,multiPart:false,xdomain:false},w||{});var s;if(n.argument&&n.argument.options&&n.argument.options.request&&(s=n.argument.options.request.arg)){Ext.apply(t,{async:t.async||s.async,proxied:t.proxied||s.proxied,multiPart:t.multiPart||s.multiPart,xdomain:t.xdomain||s.xdomain,queue:t.queue||s.queue,onPart:t.onPart||s.onPart})}if(!this.events||this.fireEvent("request",j,l,n,p,t)!==false){if(!t.queued&&(t.queue||(t.queue=this.queueAll||null))){t.queue===true&&(t.queue={name:"q-default"});var r=t.queue;var m=r.name||r,v=this.queueManager;var k=v.getQueue(m)||v.createQueue(r);t.queue=k;t.queued=true;var u=[j,l,n,p,t];u.active=true;k.add(u);return{tId:this.transactionId++,queued:true,request:u,options:t}}w.onpart&&(n.onpart||(n.onpart=Ext.isFunction(w.onpart)?w.onpart.createDelegate(w.scope):null));t.headers&&forEach(t.headers,function(x,q){this.initHeader(q,x,false)},this);var o;if(o=(this.headers?this.headers["Content-Type"]||null:null)){delete this.headers["Content-Type"]}if(t.xmlData){o||(o="text/xml");j="POST";p=t.xmlData}else{if(t.jsonData){o||(o="application/json; charset=utf-8");j="POST";p=(Ext.isArray(t.jsonData)||Ext.isObject(t.jsonData))?Ext.encode(t.jsonData):t.jsonData}}if(p){o||(o=this.useDefaultHeader?this.defaultPostHeader:null);o&&this.initHeader("Content-Type",o,false)}return this.makeRequest(t.method||j,l,n,p,t)}return null},getConnectionObject:function(l,j,n){var p,m;var q=this.transactionId;j||(j={});try{if(m=j.proxied){p={tId:q,status:{isError:false},proxied:true,conn:{el:null,send:function(r){var s=(m.target||window).document,o=s.getElementsByTagName("head")[0];if(o&&this.el){o.appendChild(this.el.dom);this.readyState=2}},abort:function(){this.readyState=0;window[p.cbName]=undefined;Ext.isIE||delete window[p.cbName];var o=Ext.getDom(this.el);if(this.el){this.el.removeAllListeners();if(!p.debug){if(Ext.isIE){b.SCRIPTTAG_POOL.push(this.el)}else{this.el.remove();if(o){for(var r in o){delete o[r]}}}}}this.el=o=null},_headers:{},getAllResponseHeaders:function(){var o=[];forEach(this._headers,function(s,r){s&&o.push(r+": "+s)});return o.join("\n")},getResponseHeader:function(o){return this._headers[String(o).toLowerCase()]||""},onreadystatechange:null,onload:null,readyState:0,status:0,responseText:null,responseXML:null,responseJSON:null},debug:m.debug,params:Ext.isString(j.params)?Ext.urlDecode(j.params):j.params||{},cbName:m.callbackName||"basexCallback"+q,cbParam:m.callbackParam||null};window[p.cbName]=p.cb=function(o){o&&typeof(o)=="object"&&(this.responseJSON=o);this.responseText=o||null;this.status=!!o?200:404;this.abort();this.readyState=4;Ext.isFunction(this.onreadystatechange)&&this.onreadystatechange();Ext.isFunction(this.onload)&&this.onload()}.createDelegate(p.conn);p.conn.open=function(){if(p.cbParam){p.params[p.cbParam]=p.cbName}var o=Ext.urlEncode(Ext.apply(Ext.urlDecode(n)||{},p.params,l.indexOf("?")>-1?Ext.urlDecode(l.split("?").last()):false));p.uri=o?l.split("?").first()+"?"+o:l;this.el=b.monitoredNode(m.tag||"script",{type:m.contentType||"text/javascript",src:p.uri,charset:m.charset||j.charset||null},null,m.target,true);this._headers["content-type"]=this.el.dom.type;this.readyState=1;Ext.isFunction(this.onreadystatechange)&&this.onreadystatechange()};j.async=true}else{p=this.createXhrObject(q,j)}if(p){this.transactionId++}}catch(k){p&&(p.status.isError=!!(p.status.error=k))}finally{return p}},makeRequest:function(s,m,q,j,k){var p;if(p=this.getConnectionObject(m,k,j)){p.options=k;var l=p.conn;try{if(p.status.isError){throw p.status.error}b.activeRequests++;l.open(s.toUpperCase(),m,k.async,k.userId,k.password);("onreadystatechange" in l)&&(l.onreadystatechange=this.onStateChange.createDelegate(this,[p,q,"readystate"],0));("onload" in l)&&(l.onload=this.onStateChange.createDelegate(this,[p,q,"load",4],0));("onprogress" in l)&&(l.onprogress=this.onStateChange.createDelegate(this,[p,q,"progress"],0));if(q&&q.timeout){("timeout" in l)&&(l.timeout=q.timeout);("ontimeout" in l)&&(l.ontimeout=this.abort.createDelegate(this,[p,q,true],0));("ontimeout" in l)||(k.async&&(this.timeout[p.tId]=window.setInterval(function(){b.abort(p,q,true)},q.timeout)))}if(this.useDefaultXhrHeader&&!k.xdomain){this.defaultHeaders["X-Requested-With"]||this.initHeader("X-Requested-With",this.defaultXhrHeader,true)}this.setHeaders(p);if(!this.events||this.fireEvent("beforesend",p,s,m,q,j,k)!==false){l.send(j||null)}}catch(n){p.status.isError=true;p.status.error=n}if(p.status.isError){return Ext.apply(p,this.handleTransactionResponse(p,q))}k.async||this.onStateChange(p,q,"load");return p}},abort:function(k,l,j){k&&Ext.apply(k.status,{isAbort:!!!j,isTimeout:!!j,isError:!!j||!!k.status.isError});if(k&&k.queued&&k.request){k.request.active=k.queued=false;this.events&&this.fireEvent("abort",k,l);return true}else{if(k&&this.isCallInProgress(k)){if(!this.events||this.fireEvent(j?"timeout":"abort",k,l)!==false){("abort" in k.conn)&&k.conn.abort();this.handleTransactionResponse(k,l,k.status.isAbort,k.status.isTimeout)}return true}}return false},isCallInProgress:function(j){if(j&&j.conn){if("readyState" in j.conn&&{0:true,4:true}[j.conn.readyState]){return false}return true}return false},clearAuthenticationCache:function(j){try{if(Ext.isIE){document.execCommand("ClearAuthenticationCache")}else{var k;if(k=new XMLHttpRequest()){k.open("GET",j||"/@@",true,"logout","logout");k.send("");k.abort.defer(100,k)}}}catch(l){}},initHeader:function(j,k){(this.headers=this.headers||{})[j]=k},onStateChange:function(n,x,v){if(!n.conn||n.status.isTimeout||n.status.isError){return}var k=n.conn,t=("readyState" in k?k.readyState:0);if(v==="load"||t>2){var w;try{w=k.contentType||k.getResponseHeader("Content-Type")||""}catch(q){}if(w&&/multipart\//i.test(w)){var j=null,m=w.split('"')[1],u="--"+m;n.multiPart=true;try{j=k.responseText}catch(s){}var l=j?j.split(u):null;if(l){n.parts||(n.parts=[]);l.shift();l.pop();forEach(Array.slice(l,n.parts.length),function(o){var r=o.split("\n\n");var p=(r[0]?r[0]:"")+"\n";n.parts.push(this.handleTransactionResponse(Ext.apply(Ext.clone(n),{boundary:m,conn:{status:200,responseText:(r[1]||"").trim(),getAllResponseHeaders:function(){return p.split("\n").filter(function(y){return !!y}).join("\n")}},isPart:true}),x))},this)}}}(t===4||v==="load")&&b.handleTransactionResponse(n,x);this.events&&this.fireEvent.apply(this,["readystatechange"].concat(Array.slice(arguments,0)))},setHeaders:function(j){if(j.conn&&"setRequestHeader" in j.conn){this.defaultHeaders&&forEach(this.defaultHeaders,function(l,k){j.conn.setRequestHeader(k,l)});this.headers&&forEach(this.headers,function(l,k){j.conn.setRequestHeader(k,l)})}this.headers={};this.hasHeaders=false},resetDefaultHeaders:function(){delete this.defaultHeaders;this.defaultHeaders={};this.hasDefaultHeaders=false},activeXMultipart:["MSXML2.XMLHTTP.6.0","MSXML3.XMLHTTP"],activeX:["MSXML2.XMLHTTP.3.0","MSXML2.XMLHTTP","Microsoft.XMLHTTP"]});if(Ext.util.Observable){Ext.apply(b,{events:{request:true,beforesend:true,response:true,exception:true,abort:true,timeout:true,readystatechange:true,beforequeue:true,queue:true,queueempty:true},onStatus:function(j,n,m,l){var k=Array.slice(arguments,1);j=new Array().concat(j||new Array());forEach(j,function(o){o=parseInt(o,10);if(!isNaN(o)){var p="status:"+o;this.events[p]||(this.events[p]=true);this.on.apply(this,[p].concat(k))}},this)},unStatus:function(j,n,m,l){var k=Array.slice(arguments,1);j=new Array().concat(j||new Array());forEach(j,function(o){o=parseInt(o,10);if(!isNaN(o)){var p="status:"+o;this.un.apply(this,[p].concat(k))}},this)}},new Ext.util.Observable());Ext.hasBasex=true}Ext.stopIteration={stopIter:true};Ext.applyIf(Array.prototype,{map:function(k,n){var j=this.length;if(typeof k!="function"){throw new TypeError()}var m=new Array(j);for(var l=0;l<j;++l){l in this&&(m[l]=k.call(n||this,this[l],l,this))}return m},some:function(m){var n=Ext.isFunction(m)?m:function(){};var k=0,j=this.length,o=false;while(k<j&&!(o=!!n(this[k++]))){}return o},every:function(m){var n=Ext.isFunction(m)?m:function(){};var k=0,j=this.length,o=true;while(k<j&&(o=!!n(this[k++]))){}return o},include:function(m,k){if(!k&&typeof this.indexOf=="function"){return this.indexOf(m)!=-1}var l=false;try{this.forEach(function(o,n){if(l=(k?(o.include?o.include(m,k):(o===m)):o===m)){throw Ext.stopIteration}})}catch(j){if(j!=Ext.stopIteration){throw j}}return l},filter:function(l,k){var j=new Array();l||(l=function(m){return m});this.forEach(function(n,m){l.call(k,n,m)&&j.push(n)});return j},compact:function(k){var j=new Array();this.forEach(function(l){(l===null||l===undefined)||j.push(k&&Ext.isArray(l)?l.compact():l)},this);return j},flatten:function(){var j=new Array();this.forEach(function(k){Ext.isArray(k)?(j=j.concat(k)):j.push(k)},this);return j},indexOf:function(l){for(var k=0,j=this.length;k<j;++k){if(this[k]==l){return k}}return -1},lastIndexOf:function(k){var j=this.length-1;while(j>-1&&this[j]!=k){j--}return j},unique:function(k,l){var j=new Array();this.forEach(function(n,m){if(0==m||(k?j.last()!=n:!j.include(n,l))){j.push(n)}},this);return j},grep:function(n,m,l){var j=new Array();m||(m=function(o){return o});var k=l?m.createDelegate(l):m;if(typeof n=="string"){n=new RegExp(n)}n instanceof RegExp&&this.forEach(function(p,o){n.test(p)&&j.push(k(p,o))});return j},first:function(){return this[0]},last:function(){return this[this.length-1]},clear:function(){this.length=0},atRandom:function(k){var j=Math.floor(Math.random()*this.length);return this[j]||k},clone:function(j){if(!j){return this.concat()}var l=this.length||0,k=new Array(l);while(l--){k[l]=Ext.clone(this[l],true)}return k},forEach:function(k,j){Array.forEach(this,k,j)},reversed:function(){var k=this.length||0,j=[];while(k--){j.push(this[k])}return j}});window.forEach=function(k,n,l,j){l=l||k;if(k){if(typeof n!="function"){throw new TypeError()}var m=Object;if(k instanceof Function){m=Function}else{if(k.forEach instanceof Function){return k.forEach(n,l)}else{if(typeof k=="string"){m=String}else{if(Ext.isNumber(k.length)){m=Array}}}}return m.forEach(k,n,l,j)}};Ext.clone=function(k,j){if(k===null||k===undefined){return k}if(Ext.isFunction(k.clone)){return k.clone(j)}else{if(Ext.isFunction(k.cloneNode)){return k.cloneNode(j)}}var l={};forEach(k,function(n,m,o){l[m]=(n===o?l:j?Ext.clone(n,true):n)},k,j);return l};var h=Array.prototype.slice;var e=Array.prototype.filter;Ext.applyIf(Array,{slice:function(j){return h.apply(j,h.call(arguments,1))},filter:function(l,k){var j=l&&typeof l=="string"?l.split(""):[];return e.call(j,k)},forEach:function(o,n,m){if(typeof n!="function"){throw new TypeError()}for(var k=0,j=o.length>>>0;k<j;++k){(k in o)&&n.call(m||null,o[k],k,o)}}});Ext.applyIf(RegExp.prototype,{clone:function(){return new RegExp(this)}});Ext.applyIf(Date.prototype,{clone:function(j){return j?new Date(this.getTime()):this}});Ext.applyIf(Boolean.prototype,{clone:function(){return this===true}});Ext.applyIf(Number.prototype,{times:function(m,k){var l=parseInt(this,10)||0;for(var j=1;j<=l;){m.call(k,j++)}},forEach:function(){this.times.apply(this,arguments)},clone:function(){return(this)+0}});Ext.applyIf(String.prototype,{trim:function(){var j=/^\s+|\s+$/g;return function(){return this.replace(j,"")}}(),trimRight:function(){var j=/^|\s+$/g;return function(){return this.replace(j,"")}}(),trimLeft:function(){var j=/^\s+|$/g;return function(){return this.replace(j,"")}}(),clone:function(){return String(this)+""},forEach:function(k,j){String.forEach(this,k,j)}});var c=function(p,n){var o=typeof p=="function"?p:function(){};var m=o._ovl;if(!m){m={base:o};m[o.length||0]=o;o=function(){var r=arguments.callee._ovl;var l=r[arguments.length]||r.base;return l&&l!=arguments.callee?l.apply(this,arguments):undefined}}var q=[].concat(n);for(var k=0,j=q.length;k<j;++k){m[q[k].length]=q[k]}o._ovl=m;return o};Ext.apply(Ext,{overload:c(c,[function(j){return c(null,j)},function(l,k,j){return l[k]=c(l[k],j)}]),isIterable:function(j){if(Ext.isArray(j)||j.callee){return true}if(/NodeList|HTMLCollection/.test(a.toString.call(j))){return true}return(typeof j.nextNode!="undefined"||j.item)&&Ext.isNumber(j.length)},isArray:function(j){return a.toString.apply(j)=="[object Array]"},isObject:function(j){return !!j&&a.toString.apply(j)=="[object Object]"},isNumber:function(j){return typeof j=="number"&&isFinite(j)},isBoolean:function(j){return typeof j=="boolean"},isDocument:function(j){return a.toString.apply(j)=="[object HTMLDocument]"||(j&&j.nodeType===9)},isElement:function(j){if(j){var k=j.dom||j;return !!k.tagName||(/\[object html/i).test(a.toString.apply(k))}return false},isEvent:function(j){return a.toString.apply(j)=="[object Event]"||(Ext.isObject(j)&&!Ext.type(j.constructor)&&(window.event&&j.clientX&&j.clientX===window.event.clientX))},isFunction:function(j){return a.toString.apply(j)=="[object Function]"},isString:function(j){return typeof j=="string"},isPrimitive:function(j){return Ext.isString(j)||Ext.isNumber(j)||Ext.isBoolean(j)},isDefined:i});Ext.ns("Ext.capabilities");var g=Ext.capabilities;Ext.apply(g,{hasActiveX:i(window.ActiveXObject),hasXDR:function(){return i(window.XDomainRequest)||(i(window.XMLHttpRequest)&&"withCredentials" in new XMLHttpRequest())}(),hasChromeFrame:function(){try{if(i(window.ActiveXObject)&&!!(new ActiveXObject("ChromeTab.ChromeFrame"))){return true}}catch(j){}var k=navigator.userAgent.toLowerCase();return !!(k.indexOf("chromeframe")>=0||k.indexOf("x-clock")>=0)}(),hasFlash:(function(){if(i(window.ActiveXObject)){try{new ActiveXObject("ShockwaveFlash.ShockwaveFlash");return true}catch(m){}return false}else{if(navigator.plugins){for(var j=0,l=navigator.plugins,k=l.length;j<k;++j){if((/flash/i).test(l[j].name)){return true}}return false}}return false})(),hasCookies:Ext.isIE&&("dialogArguments" in window)?false:!!navigator.cookieEnabled,hasCanvas:!!document.createElement("canvas").getContext,hasCanvasText:function(){return !!(this.hasCanvas&&typeof document.createElement("canvas").getContext("2d").fillText=="function")}(),hasSVG:!!(document.createElementNS&&document.createElementNS("http://www.w3.org/2000/svg","svg").width),hasXpath:!!document.evaluate,hasWorkers:i(window.Worker)||g.hasGears,hasOffline:i(window.applicationCache),hasLocalStorage:i(window.localStorage),hasGeoLocation:i(navigator.geolocation),hasBasex:true,hasAudio:function(){var l=!!document.createElement("audio").canPlayType,m=("Audio" in window)?new Audio(""):{},n=l||("canPlayType" in m)?{tag:l,object:("play" in m),testMime:function(p){var q;return(q=m.canPlayType?m.canPlayType(p):"no")!=="no"&&q!==""}}:false,o,k,j={mp3:"audio/mpeg",ogg:"audio/ogg",wav:"audio/x-wav",basic:"audio/basic",aif:"audio/x-aiff"};if(n&&n.testMime){for(k in j){n[k]=n.testMime(j[k])}}return n}(),hasVideo:function(){var l=!!document.createElement("video").canPlayType,n=l?document.createElement("video"):{},m=("canPlayType" in n)?{tag:l,testCodec:function(p){var q;return(q=n.canPlayType?n.canPlayType(p):"no")!=="no"&&q!==""}}:false,o,j,k={mp4:'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',ogg:'video/ogg; codecs="theora, vorbis"'};if(m&&m.testCodec){for(j in k){m[j]=m.testCodec(k[j])}}return m}(),hasInputAutoFocus:function(){return("autofocus" in (document.createElement("input")))}(),hasInputPlaceHolder:function(){return("placeholder" in (document.createElement("input")))}(),hasInputType:function(k){var j=document.createElement("input");if(j){try{j.setAttribute("type",k)}catch(l){}return j.type!=="text"}return false},isEventSupported:function(){var l={select:"input",change:"input",submit:"form",reset:"form",load:"img",error:"img",abort:"img"};var j={},m=/^on/i,k=function(p,o){var n=Ext.getDom(o);return(n?(Ext.isElement(n)||Ext.isDocument(n)?n.nodeName.toLowerCase():o.self?"#window":o||"#object"):o||"div")+":"+p};return function(r,t){r=(r||"").replace(m,"");var s,q=false;var o="on"+r;var n=(t?t:l[r])||"div";var p=k(r,n);if(p in j){return j[p]}s=Ext.isString(n)?document.createElement(n):t;q=(!!s&&(o in s));q||(q=window.Event&&!!(String(r).toUpperCase() in window.Event));if(!q&&s){s.setAttribute&&s.setAttribute(o,"return;");q=Ext.isFunction(s[o])}j[p]=q;s=null;return q}}()});Ext.EventManager.on(window,"beforeunload",b.onUnload,b,{single:true})})();Ext.applyIf(Function.prototype,{forEach:function(a,e,d,c){if(a){var b;for(b in a){(!!c||a.hasOwnProperty(b))&&e.call(d||a,a[b],b,a)}}},createBuffered:function(a,c){var d=this,b=new Ext.util.DelayedTask();return function(){b.delay(a,d,c,Array.slice(arguments,0))}},createDelayed:function(c,d,b,a){var e=(d||b)?this.createDelegate(d,b,a):this;return c?function(){setTimeout(e,c)}:e},clone:function(a){return this}});Ext.ComponentMgr.create=Ext.ComponentMgr.create.createInterceptor(function(a,b){if(!Ext.ComponentMgr.isRegistered(a.xtype||b)){Ext.ComponentMgr.loadType(a.xtype||b)}});Ext.ComponentMgr.createPlugin=Ext.ComponentMgr.createPlugin.createInterceptor(function(a,b){if(!Ext.ComponentMgr.isPluginRegistered(a.ptype||b)){Ext.ComponentMgr.loadType(a.ptype||b)}});Ext.override(Ext.data.HttpProxy,{setMethod:function(c,a){var b=this.conn.method;this.conn.method=c;if(!a){this.on("load",function(){this.conn.method=b},this,{single:true})}}});Ext.ux.MessageBox=function(){f=function(){};f.prototype=Ext.MessageBox;var a=function(){};Ext.extend(a,f,function(){return{info:function(e,d,c,b){this.show({title:e,msg:d,minWidth:this.minWidth,icon:Ext.MessageBox.INFO});setTimeout(this.getDialog().close.createDelegate(this),2200);return this},error:function(e,d,c,b){this.show({title:e,msg:d,fn:c,scope:b,minWidth:this.minWidth,icon:Ext.MessageBox.ERROR});setTimeout(this.getDialog().close.createDelegate(this),2200);return this}}}());return new a()}();Ext.namespace("Ext.ux.tabpanel.plugin");Ext.ux.tabpanel.plugin.TabCloseMenu=function(){var a,c,b;this.init=function(e){a=e;a.on("contextmenu",d)};function d(i,h,j){if(!c){c=new Ext.menu.Menu([{id:a.id+"-close",text:"Close Tab",handler:function(){a.remove(b)}},{id:a.id+"-close-others",text:"Close Other Tabs",handler:function(){a.items.each(function(e){if(e.closable&&e!=b){a.remove(e)}})}}])}b=h;var g=c.items;g.get(a.id+"-close").setDisabled(!h.closable);var k=true;a.items.each(function(){if(this!=h&&this.closable){k=false;return false}});g.get(a.id+"-close-others").setDisabled(k);c.showAt(j.getPoint())}};Ext.preg("tabclosemenu",Ext.ux.tabpanel.plugin.TabCloseMenu);Ext.ux.IconMgr.setIconPath("/ExtjsGeneratorPlugin/Ext.ux.IconMgr");Ext.state.Manager.setProvider(new Ext.state.CookieProvider({expires:new Date(new Date().getTime()+(1000*60*60*24*7))}));