Ext.override(Ext.grid.GridPanel,{getView:function(){if(!this.view){this.view=new Ext.grid.GridView(this.viewConfig)}else{Ext.apply(this.view,this.viewConfig)}return this.view}});
/*
 * Ext JS Library 3.3.0
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ns("Ext.ux.grid");Ext.ux.grid.CheckColumn=Ext.extend(Ext.grid.Column,{processEvent:function(c,f,d,g,b){if(c=="mousedown"){if(this.editable){var a=d.store.getAt(g);if(!a.dirty){a.set(this.dataIndex,!a.data[this.dataIndex]);d.fireEvent("afteredit",{grid:d,record:a,field:this.name||this.dataIndex,value:a.data[this.dataIndex],originalValue:!a.data[this.dataIndex],row:g,column:b})}}return false}else{return Ext.grid.ActionColumn.superclass.processEvent.apply(this,arguments)}},renderer:function(b,c,a){c.css+=" x-grid3-check-col-td";return String.format('<div class="x-grid3-check-col{0}">&#160;</div>',b?"-on":"")}});Ext.grid.Column.types.checkcolumn=Ext.ux.grid.CheckColumn;Ext.namespace("Ext.ux.grid");Ext.ux.grid.ForeignFieldColumn=Ext.extend(Ext.grid.Column,{isColumn:true,constructor:Ext.grid.Column.prototype.constructor.createSequence(function(a){this.renderer=function(b){return function(g,h,d,i,f,e){h.css+="x-grid3-cell-wrap";if(g===""){return}var c=b.store.findBy(function(j){if(j.get(b.valueField)==g){g=j.get(b.displayField);return true}});return g}};this.renderer=this.renderer(this.editor)})});Ext.reg("foreignfieldcolumn",Ext.ux.grid.ForeignFieldColumn);Ext.grid.Column.types.foreignfieldcolumn=Ext.ux.grid.ForeignFieldColumn;Ext.namespace("Ext.ux.grid.plugin");Ext.ux.grid.plugin.ProgressColumn=function(a){Ext.apply(this,a);this.renderer=this.renderer.createDelegate(this);Ext.ux.grid.plugin.ProgressColumn.superclass.constructor.call(this)};Ext.extend(Ext.ux.grid.plugin.ProgressColumn,Ext.util.Observable,{topText:null,bottomText:null,ceiling:100,textPst:"%",colored:true,invertedColor:false,actionEvent:"click",init:function(b){this.grid=b;this.view=b.getView();this.id=this.id||Ext.id();var c=b.getColumnModel().lookup;delete (c[undefined]);c[this.id]=this;if(this.editor&&b.isEditor){var a={scope:this};a[this.actionEvent]=this.onClick;b.afterRender=b.afterRender.createSequence(function(){this.view.mainBody.on(a);b.on("destroy",this.purgeListeners,this)},this)}},onClick:function(d,c){var f=d.getTarget(".x-grid3-row").rowIndex;var a=this.view.findCellIndex(c.parentNode.parentNode);var b=d.getTarget(".x-progress-text");if(b){this.grid.startEditing(f,a)}},getStyle:function(b,d,a){var c="";if(this.colored==true){if(this.invertedColor==true){if(b>(this.ceiling*0.66)){c="-red"}if(b<(this.ceiling*0.67)&&b>(this.ceiling*0.33)){c="-orange"}if(b<(this.ceiling*0.34)){c="-green"}}else{if(b<=this.ceiling&&b>(this.ceiling*0.66)){c="-green"}if(b<(this.ceiling*0.67)&&b>(this.ceiling*0.33)){c="-orange"}if(b<(this.ceiling*0.34)){c="-red"}}}return c},getTopText:function(b,c,a){if(this.topText){return String.format('<div class="x-progress-toptext">{0}</div>',this.topText)}return""},getBottomText:function(b,c,a){if(this.bottomText){return String.format('<div class="x-progress-bottomtext">{0}</div>',this.bottomText)}return""},getText:function(c,d,b){var a=(c<(this.ceiling/1.818))?"x-progress-text-back":"x-progress-text-front";var e=String.format('</div><div class="x-progress-text {0}">{1}</div></div>',a,c+this.textPst);return(c<(this.ceiling/1.031))?e.substring(0,e.length-6):e.substr(6)},renderer:function(b,c,a){c.css+=" x-grid3-progresscol";if(!b){b=0}return String.format('{0}<div class="x-progress-wrap'+(Ext.isIE?' x-progress-wrap-ie">':'">')+'<!-- --><div class="x-progress-inner"><div class="x-progress-bar x-progress-bar{1}" style="width:{2}%;">{3}</div></div>{4}',this.getTopText(b,c,a),this.getStyle(b,c,a),(b/this.ceiling)*100,this.getText(b,c,a),this.getBottomText(b,c,a))}});Ext.preg("progresscolumn",Ext.ux.grid.plugin.ProgressColumn);Ext.ns("Ext.ux.grid.plugin");if("function"!==typeof RegExp.escape){RegExp.escape=function(a){if("string"!==typeof a){return a}return a.replace(/([.*+?\^=!:${}()|\[\]\/\\])/g,"\\$1")}}Ext.ux.grid.plugin.RowActions=function(a){Ext.apply(this,a);this.addEvents("beforeaction","action","beforegroupaction","groupaction");Ext.ux.grid.plugin.RowActions.superclass.constructor.call(this)};Ext.extend(Ext.ux.grid.plugin.RowActions,Ext.util.Observable,{actionEvent:"click",autoWidth:true,dataIndex:"",editable:false,header:"",isColumn:true,keepSelection:false,menuDisabled:true,sortable:false,tplGroup:'<tpl for="actions"><div class="ux-grow-action-item<tpl if="\'right\'===align"> ux-action-right</tpl> {cls}" style="{style}" qtip="{qtip}">{text}</div></tpl>',tplRow:'<div class="ux-row-action"><tpl for="actions"><div class="ux-row-action-item {cls} <tpl if="text">ux-row-action-text</tpl>" style="{hide}{style}" qtip="{qtip}"><tpl if="text"><span qtip="{qtip}">{text}</span></tpl></div></tpl></div>',hideMode:"visibility",widthIntercept:4,widthSlope:21,init:function(f){this.grid=f;this.id=this.id||Ext.id();var g=f.getColumnModel().lookup;delete (g[undefined]);g[this.id]=this;if(!this.actions){var e=f.getColumnModel().getIndexById(this.id);var d=f.getColumnModel().config;f.getColumnModel().config=[d[e]];d.splice(e,1);f.getColumnModel().setConfig(d);return}if(!this.tpl){this.tpl=this.processActions(this.actions)}if(this.autoWidth){this.width=this.widthSlope*this.actions.length+this.widthIntercept;this.fixed=true}var c=f.getView();var b={scope:this};b[this.actionEvent]=this.onClick;f.afterRender=f.afterRender.createSequence(function(){c.mainBody.on(b);f.on("destroy",this.purgeListeners,this)},this);if(!this.renderer){this.renderer=function(m,i,j,n,l,k){i.css+=(i.css?" ":"")+"ux-row-action-cell";return this.tpl.apply(this.getData(m,i,j,n,l,k))}.createDelegate(this)}if(c.groupTextTpl&&this.groupActions){c.interceptMouse=c.interceptMouse.createInterceptor(function(i){if(i.getTarget(".ux-grow-action-item")){return false}});c.groupTextTpl='<div class="ux-grow-action-text">'+c.groupTextTpl+"</div>"+this.processActions(this.groupActions,this.tplGroup).apply()}if(true===this.keepSelection){f.processEvent=f.processEvent.createInterceptor(function(i,j){if("mousedown"===i){return !this.getAction(j)}},this)}if(Ext.util.CSS.getRule(".ux-row-action-cell")==null){var a=".ux-row-action-cell .x-grid3-cell-inner {padding: 1px 0 0 0;}.ux-row-action-item {float: left;min-width: 16px;height: 16px;background-repeat: no-repeat;margin: 0 5px 0 0;cursor: pointer;overflow: hidden;}.ext-ie .ux-row-action-item {width: 16px;}.ext-ie .ux-row-action-text {width: auto;}.ux-row-action-item span {vertical-align:middle; padding: 0 0 0 20px;  line-height: 18px;}.ext-ie .ux-row-action-item span {width: auto;}.x-grid-group-hd div {position: relative;height: 16px;}.ux-grow-action-item {min-width: 16px;height: 16px;background-repeat: no-repeat;background-position: 0 50% ! important;margin: 0 0 0 4px;padding: 0 ! important;cursor: pointer;float: left;}.ext-ie .ux-grow-action-item {width: 16px;}.ux-action-right {float: right;margin: 0 3px 0 2px;padding: 0 ! important;}.ux-grow-action-text {padding: 0 ! important;margin: 0 ! important;background: transparent none ! important;float: left;}";var h=Ext.util.CSS.createStyleSheet("/* Ext.ux.grid.plugin.RowActions stylesheet */\n"+a,"RowActions");Ext.util.CSS.refreshCache()}},getData:function(e,a,b,f,d,c){return b.data||{}},processActions:function(d,c){var a=[];Ext.each(d,function(e,f){if(e.iconCls&&"function"===typeof(e.callback||e.cb)){this.callbacks=this.callbacks||{};this.callbacks[e.iconCls]=e.callback||e.cb}var g={cls:e.iconIndex?"{"+e.iconIndex+"}":(e.iconCls?e.iconCls:""),qtip:e.qtipIndex?"{"+e.qtipIndex+"}":(e.tooltip||e.qtip?e.tooltip||e.qtip:""),text:e.textIndex?"{"+e.textIndex+"}":(e.text?e.text:""),hide:e.hideIndex?'<tpl if="'+e.hideIndex+'">'+("display"===this.hideMode?"display:none":"visibility:hidden")+";</tpl>":(e.hide?("display"===this.hideMode?"display:none":"visibility:hidden;"):""),align:e.align||"right",style:e.style?e.style:""};a.push(g)},this);var b=new Ext.XTemplate(c||this.tplRow);return new Ext.XTemplate(b.apply({actions:a}))},getAction:function(c){var b=false;var a=c.getTarget(".ux-row-action-item");if(a){b=a.className.replace(/ux-row-action-item /,"");if(b){b=b.replace(/ ux-row-action-text/,"");b=b.trim()}}return b},onClick:function(g,h){var i=this.grid.getView();var l=g.getTarget(".x-grid3-row");var a=i.findCellIndex(h.parentNode.parentNode);var c=this.getAction(g);if(false!==l&&false!==a&&false!==c){var f=this.grid.store.getAt(l.rowIndex);if(this.callbacks&&"function"===typeof this.callbacks[c]){this.callbacks[c](this.grid,f,c,l.rowIndex,a)}if(true!==this.eventsSuspended&&false===this.fireEvent("beforeaction",this.grid,f,c,l.rowIndex,a)){return}else{if(true!==this.eventsSuspended){this.fireEvent("action",this.grid,f,c,l.rowIndex,a)}}}t=g.getTarget(".ux-grow-action-item");if(t){var j=i.findGroup(h);var d=j?j.id.replace(/ext-gen[0-9]+-gp-/,""):null;var b;if(d){var k=new RegExp(RegExp.escape(d));b=this.grid.store.queryBy(function(e){return e._groupId.match(k)});b=b?b.items:[]}c=t.className.replace(/ux-grow-action-item (ux-action-right )*/,"");if("function"===typeof this.callbacks[c]){this.callbacks[c](this.grid,b,c,d)}if(true!==this.eventsSuspended&&false===this.fireEvent("beforegroupaction",this.grid,b,c,d)){return false}this.fireEvent("groupaction",this.grid,b,c,d)}}});Ext.preg("rowactions",Ext.ux.grid.plugin.RowActions);Ext.ns("Ext.ux.grid.plugin");Ext.ux.grid.plugin.RowExpander=Ext.extend(Ext.util.Observable,{expandOnEnter:true,expandOnDblClick:true,header:"",width:20,sortable:false,fixed:true,hideable:false,menuDisabled:true,dataIndex:"",id:"expander",lazyRender:true,enableCaching:true,constructor:function(a){Ext.apply(this,a);this.addEvents({beforeexpand:true,expand:true,beforecollapse:true,collapse:true});Ext.ux.grid.plugin.RowExpander.superclass.constructor.call(this);if(this.tpl){if(typeof this.tpl=="string"){this.tpl=new Ext.Template(this.tpl)}this.tpl.compile()}this.state={};this.bodyContent={}},getRowClass:function(a,e,d,c){d.cols=d.cols-1;var b=this.bodyContent[a.id];if(!b&&!this.lazyRender){b=this.getBodyContent(a,e)}if(b){d.body=b}return this.state[a.id]?"x-grid3-row-expanded":"x-grid3-row-collapsed"},init:function(b){this.grid=b;var a=b.getView();a.getRowClass=this.getRowClass.createDelegate(this);a.enableRowBody=true;b.on("render",this.onRender,this);b.on("destroy",this.onDestroy,this)},onRender:function(){var a=this.grid;var b=a.getView().mainBody;b.on("mousedown",this.onMouseDown,this,{delegate:".x-grid3-row-expander"});if(this.expandOnEnter){this.keyNav=new Ext.KeyNav(this.grid.getGridEl(),{enter:this.onEnter,scope:this})}if(this.expandOnDblClick){a.on("rowdblclick",this.onRowDblClick,this)}},onDestroy:function(){if(this.keyNav){this.keyNav.disable();delete this.keyNav}var a=this.grid.getView().mainBody;if(a){a.un("mousedown",this.onMouseDown,this)}},onRowDblClick:function(a,b,c){this.toggleRow(b)},onEnter:function(h){var f=this.grid;var j=f.getSelectionModel();var b=j.getSelections();for(var c=0,a=b.length;c<a;c++){var d=f.getStore().indexOf(b[c]);this.toggleRow(d)}},getBodyContent:function(a,b){if(!this.enableCaching){return this.tpl.apply(a.data)}var c=this.bodyContent[a.id];if(!c){c=this.tpl.apply(a.data);this.bodyContent[a.id]=c}return c},onMouseDown:function(b,a){b.stopEvent();var c=b.getTarget(".x-grid3-row");this.toggleRow(c)},renderer:function(b,c,a){c.cellAttr='rowspan="2"';return'<div class="x-grid3-row-expander">&#160;</div>'},beforeExpand:function(b,a,c){if(this.fireEvent("beforeexpand",this,b,a,c)!==false){if(this.tpl&&this.lazyRender){a.innerHTML=this.getBodyContent(b,c)}return true}else{return false}},toggleRow:function(a){if(typeof a=="number"){a=this.grid.view.getRow(a)}this[Ext.fly(a).hasClass("x-grid3-row-collapsed")?"expandRow":"collapseRow"](a)},expandRow:function(c){if(typeof c=="number"){c=this.grid.view.getRow(c)}var b=this.grid.store.getAt(c.rowIndex);var a=Ext.DomQuery.selectNode("tr:nth(2) div.x-grid3-row-body",c);if(this.beforeExpand(b,a,c.rowIndex)){this.state[b.id]=true;Ext.fly(c).replaceClass("x-grid3-row-collapsed","x-grid3-row-expanded");this.fireEvent("expand",this,b,a,c.rowIndex)}},collapseRow:function(c){if(typeof c=="number"){c=this.grid.view.getRow(c)}var b=this.grid.store.getAt(c.rowIndex);var a=Ext.fly(c).child("tr:nth(1) div.x-grid3-row-body",true);if(this.fireEvent("beforecollapse",this,b,a,c.rowIndex)!==false){this.state[b.id]=false;Ext.fly(c).replaceClass("x-grid3-row-expanded","x-grid3-row-collapsed");this.fireEvent("collapse",this,b,a,c.rowIndex)}}});Ext.preg("rowexpander",Ext.ux.grid.plugin.RowExpander);