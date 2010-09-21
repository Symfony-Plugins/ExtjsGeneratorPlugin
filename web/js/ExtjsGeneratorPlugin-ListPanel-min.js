Ext.ns("Ext.ux.list");Ext.ux.list.CheckColumn=Ext.extend(Ext.list.Column,{constructor:function(a){a.tpl=a.tpl||new Ext.XTemplate("{"+a.dataIndex+":this.format}");a.tpl.format=function(b){return String.format('<div class="x-grid3-check-col{0}">&#160;</div>',b?"-on":"")};Ext.ux.list.CheckColumn.superclass.constructor.call(this,a)}});Ext.reg("lvcheckcolumn",Ext.ux.list.CheckColumn);Ext.ns("Ext.ux.list");Ext.ux.list.GroupingListView=Ext.extend(Ext.ListView,{startCollapsed:false,startExpanded:[],groupHeaderTpl:'<tpl if="xindex == 1 || parent.rows[xindex-1].group_index !=  parent.rows[xindex-2].group_index"><div class="x-grid-group {[this.getCollapseClass(values)]}" group-index="{group_index}"><div class="x-grid-group-hd"><div class="x-grid-group-title">{group_index} </div></div><div class="x-grid-group-body"></tpl>',groupFooterTpl:'<tpl if="xindex == (xcount) || this.isBigger(xindex, (xcount-1)) || parent.rows[xindex-1].group_index !=  parent.rows[xindex].group_index"> </div></div></tpl>',groupState:{},initComponent:function(){var a="";if(this.startCollapsed){a=" x-grid-group-collapsed"}this.tpl=new Ext.XTemplate('<tpl for="rows">',this.groupHeaderTpl,'<dl class="x-grid3-row {[xindex % 2 === 0 ? "" :  "x-grid3-row-alt"]}">','<tpl for="parent.columns">','<dt style="width:{[values.width*100]}%;text-align:{align};">','<em unselectable="on"<tpl if="cls"> class="{cls}</tpl>">',"{[values.tpl.apply(parent)]}","</em>","</dt>","</tpl>",'<div class="x-clear"></div>',"</dl>",this.groupFooterTpl,"</tpl>",'<div class="dataview-border"></div>',{isBigger:function(c,b){return c>b},getCollapseClass:function(b){return b.group_collapsed?" x-grid-group-collapsed":""}});Ext.ux.list.GroupingListView.superclass.initComponent.apply(this,arguments)},collectData:function(d,h){var c=[],b={},g=[],e;for(var f=0,a=d.length;f<a;f++){c[f]=this.prepareData(d[f].data,h+f,d[f]);if(f==0||c[f]["group_index"]!=c[f-1]["group_index"]){if(f>0){b[e]=g;g=[]}e=c[f]["group_index"]}g.push(c[f])}b[e]=g;Ext.each(c,function(j,i){j.rs=b[j.group_index]});return{columns:this.columns,rows:c}},prepareData:function(e,b,a){var d={},c=a.data[a.store.groupField];c=this.groupRenderer?this.groupRenderer(c,b,a):String(c);Ext.apply(d,e);d.group_index=c;if(!this.groupState[c]){this.groupState[c]=this.startCollapsed&&(this.startExpanded.indexOf(c)==-1)?false:true}d.group_collapsed=!this.groupState[c];return d},onRender:function(){Ext.ux.list.GroupingListView.superclass.onRender.apply(this,arguments);this.innerBody.on("mousedown",this.interceptMouse,this)},interceptMouse:function(b){var a=b.getTarget(".x-grid-group-hd",this.innerBody);if(a){b.stopEvent();this.toggleGroup(a.parentNode)}},toggleGroup:function(d,b){var a=Ext.get(d);var c=a.getAttribute("group-index");b=Ext.isDefined(b)?b:a.hasClass("x-grid-group-collapsed");this.groupState[c]=b;a[b?"removeClass":"addClass"]("x-grid-group-collapsed")}});Ext.reg("groupinglistview",Ext.ux.list.GroupingListView);Ext.ns("Ext.ux.list");Ext.ux.list.ProgressColumn=Ext.extend(Ext.list.Column,{topText:null,bottomText:null,ceiling:100,colored:true,invertedColor:false,textPst:"%",cls:"x-list-progresscol",constructor:function(a){a.tpl=a.tpl||new Ext.XTemplate('{[this.format(values, "'+a.dataIndex+'")]}');a.tpl.column=this;a.tpl.format=function(b,c){return this.column.getColumnMarkup(b,c)};Ext.ux.list.ProgressColumn.superclass.constructor.call(this,a)},getStyle:function(b,c,a){var d="";if(this.colored==true){if(this.invertedColor==true){if(a>(this.ceiling*0.66)){d="-red"}if(a<(this.ceiling*0.67)&&a>(this.ceiling*0.33)){d="-orange"}if(a<(this.ceiling*0.34)){d="-green"}}else{if(a<=this.ceiling&&a>(this.ceiling*0.66)){d="-green"}if(a<(this.ceiling*0.67)&&a>(this.ceiling*0.33)){d="-orange"}if(a<(this.ceiling*0.34)){d="-red"}}}return d},getTopText:function(b,c,a){if(this.topText){return String.format('<div class="x-progress-toptext">{0}</div>',this.topText)}return""},getBottomText:function(b,c,a){if(this.bottomText){return String.format('<div class="x-progress-bottomtext">{0}</div>',this.bottomText)}return""},getText:function(c,d,b){var a=(b<(this.ceiling/1.818))?"x-progress-text-back":"x-progress-text-front"+(Ext.isIE?"-ie":"");var e=String.format('</div><div class="x-progress-text {0}">{1}</div></div>',a,b+this.textPst);return(b<(this.ceiling/1.031))?e.substring(0,e.length-6):e.substr(6)},getWrapperClass:function(b,c,a){return"x-list-progresscol-wrapper"},getColumnMarkup:function(b,c){var a=b[c];return String.format('<em class="{0}">{1}<div class="x-progress-wrap'+(Ext.isIE?' x-progress-wrap-ie">':'">')+'<!-- --><div class="x-progress-inner"><div class="x-progress-bar x-progress-bar{2}" style="width:{3}%;">{4}</div></div>{5}</em>',this.getWrapperClass(b,c,a),this.getTopText(b,c,a),this.getStyle(b,c,a),(a/this.ceiling)*100,this.getText(b,c,a),this.getBottomText(b,c,a))}});Ext.reg("lvprogresscolumn",Ext.ux.list.ProgressColumn);Ext.ns("Ext.ux.ListView.plugin");Ext.ux.ListView.plugin.CheckColumn=function(a){Ext.apply(this,a);this.addEvents("beforecheck","check");Ext.ux.ListView.plugin.CheckColumn.superclass.constructor.call(this)};Ext.extend(Ext.ux.ListView.plugin.CheckColumn,Ext.util.Observable,{isColumn:true,header:"&#160;",cls:"",width:".1",keepSelection:false,actionEvent:"click",init:function(b){this.tpl=this.tpl||new Ext.XTemplate("{"+this.dataIndex+":this.format}");this.tpl.format=function(c){return String.format('<div class="ux-lv-checkbox x-grid3-check-col{0}">&#160;</div>',c?"-on":"")};var a={scope:this};a[this.actionEvent]=this.onClick;b.afterRender=b.afterRender.createSequence(function(){b.on(a);b.on("destroy",this.purgeListeners,this)},this);if(true===this.keepSelection){b.onItemClick=b.onItemClick.createInterceptor(function(d,c,f){return !this.getCheckbox(f)},this)}},onClick:function(b,c,d,g){var f=this.getCheckbox(g);if(f){var a=b.getRecord(d);if("function"===typeof this.cb){this.cb(b,a,f,d,c)}if(false===this.fireEvent("beforecheck",b,a,f,d,c)){return}else{this.fireEvent("check",b,a,f,d,c);a.set(this.dataIndex,!a.data[this.dataIndex])}}},getCheckbox:function(c){var b=false;var a=c.getTarget(".ux-lv-checkbox");if(a){b=a}return b}});Ext.preg("lvcheckcolumnplugin",Ext.ux.ListView.plugin.CheckColumn);Ext.ns("Ext.ux.ListView.plugin");Ext.ux.ListView.plugin.RowActions=function(a){Ext.apply(this,a);this.addEvents("beforeaction","action");Ext.ux.ListView.plugin.RowActions.superclass.constructor.call(this)};Ext.extend(Ext.ux.ListView.plugin.RowActions,Ext.util.Observable,{isColumn:true,header:"&#160;",align:"left",cls:"",width:".1",actionEvent:"click",keepSelection:false,tplRow:'<div class="ux-row-action"><tpl for="actions"><div class="ux-row-action-item {cls} <tpl if="text">ux-row-action-text</tpl>" style="{hide}{style}" qtip="{qtip}"><tpl if="text"><span qtip="{qtip}">{text}</span></tpl></div></tpl></div>',init:function(c){if(!this.tpl){this.tpl=this.processActions(this.actions)}var b={scope:this};b[this.actionEvent]=this.onClick;c.afterRender=c.afterRender.createSequence(function(){c.on(b);c.on("destroy",this.purgeListeners,this)},this);if(true===this.keepSelection){c.onItemClick=c.onItemClick.createInterceptor(function(g,f,h){return !this.getAction(h)},this)}if(Ext.util.CSS.getRule(".ux-row-action-cell")==null){var a=".ux-row-action-item {float: "+(this.align||"left")+";min-width: 16px;height: 16px;background-repeat: no-repeat;margin: 0 5px 0 0;cursor: pointer;overflow: hidden;}.ext-ie .ux-row-action-item {width: 16px;}.ext-ie .ux-row-action-text {width: auto;}.ux-row-action-item span {vertical-align:middle; padding: 0 0 0 20px;  line-height: 18px;}.ext-ie .ux-row-action-item span {width: auto;}";var d=Ext.util.CSS.createStyleSheet("/* Ext.ux.ListView.plugin.RowActions stylesheet */\n"+a,"RowActions");Ext.util.CSS.refreshCache()}},processActions:function(d,c){var a=[];Ext.each(d,function(e,f){if(e.iconCls&&"function"===typeof(e.callback||e.cb)){this.callbacks=this.callbacks||{};this.callbacks[e.iconCls]=e.callback||e.cb}var g={cls:e.iconIndex?"{"+e.iconIndex+"}":(e.iconCls?e.iconCls:""),qtip:e.qtipIndex?"{"+e.qtipIndex+"}":(e.tooltip||e.qtip?e.tooltip||e.qtip:""),text:e.textIndex?"{"+e.textIndex+"}":(e.text?e.text:""),hide:e.hideIndex?'<tpl if="'+e.hideIndex+'">'+("display"===this.hideMode?"display:none":"visibility:hidden")+";</tpl>":(e.hide?("display"===this.hideMode?"display:none":"visibility:hidden;"):""),style:e.style?e.style:""};a.push(g)},this);var b=new Ext.XTemplate(c||this.tplRow);return new Ext.XTemplate(b.apply({actions:a}))},onClick:function(b,c,d,g){var a=b.getRecord(d);var f=this.getAction(g);if(false!==a&&false!==f){if(this.callbacks&&"function"===typeof this.callbacks[f]){this.callbacks[f](b,a,f,d,c)}if(false===this.fireEvent("beforeaction",b,a,f,d,c)){return}else{this.fireEvent("action",b,a,f,d,c)}}},getAction:function(c){var b=false;var a=c.getTarget(".ux-row-action-item");if(a){b=a.className.replace(/ux-row-action-item /,"");if(b){b=b.replace(/ ux-row-action-text/,"");b=b.trim()}}return b}});Ext.preg("lvrowactions",Ext.ux.ListView.plugin.RowActions);Ext.ns("Ext.ux");Ext.ux.ListViewPanel=Ext.extend(Ext.Panel,{layout:"fit",initComponent:function(){this.items=this.buildListView();Ext.ux.ListViewPanel.superclass.initComponent.call(this);this.relayEvents(this.getView(),["click"]);this.relayEvents(this.getStore(),["load"])},buildListView:function(){return{}},buildStore:function(){return{xtype:"jsonstore"}},clearView:function(){this.getStore().removeAll()},createAndSelectRecord:function(d){var b=this.getView();var a=new b.store.recordType(d);b.store.addSorted(a);var c=b.store.indexOf(a);b.select(c);return a},clearSelections:function(){return this.getView().clearSelections()},getView:function(){return this.items.items[0]},getStore:function(){return this.getView().store},getSelectedRecords:function(){return this.getView().getSelectedRecords()},getSelected:function(){return this.getSelectedRecords()[0]},refreshView:function(){this.getView().store.reload()},selectById:function(c){var a=this.getView();c=c||false;if(c){var b=a.store.find("id",c);a.select(b)}},loadStoreByParams:function(a){a=a||{};this.getStore().load({params:a})}});