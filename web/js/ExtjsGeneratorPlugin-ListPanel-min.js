Ext.ns("Ext.ux.list");Ext.ux.list.CheckColumn=Ext.extend(Ext.list.Column,{constructor:function(a){a.tpl=a.tpl||new Ext.XTemplate("{"+a.dataIndex+":this.format}");a.tpl.format=function(b){return String.format('<div class="x-grid3-check-col{0}">&#160;</div>',b?"-on":"")};Ext.ux.list.CheckColumn.superclass.constructor.call(this,a)}});Ext.reg("lvcheckcolumn",Ext.ux.list.CheckColumn);Ext.ns("Ext.ux.list");Ext.ux.list.GroupingListView=Ext.extend(Ext.ListView,{startCollapsed:false,startExpanded:[],groupHeaderTpl:'<tpl if="xindex == 1 || parent.rows[xindex-1].group_index !=  parent.rows[xindex-2].group_index"><div class="x-grid-group {[this.getCollapseClass(values)]}" group-index="{group_index}"><div class="x-grid-group-hd"><div class="x-grid-group-title">{group_index} </div></div><div class="x-grid-group-body"></tpl>',groupFooterTpl:'<tpl if="xindex == (xcount) || this.isBigger(xindex, (xcount-1)) || parent.rows[xindex-1].group_index !=  parent.rows[xindex].group_index"> </div></div></tpl>',groupState:{},initComponent:function(){var a="";if(this.startCollapsed){a=" x-grid-group-collapsed"}var b;if(!this.tpl){b='<dl><tpl for="parent.columns"><dt style="width:{[values.width*100]}%;text-align:{align};"><em unselectable="on"<tpl if="cls"> class="{cls}</tpl>">{[values.tpl.apply(parent)]}</em></dt></tpl><div class="x-clear"></div></dl>'}else{b=this.tpl.html.substr(16,this.tpl.html.length-22)}this.tpl=new Ext.XTemplate('<tpl for="rows">',this.groupHeaderTpl,b,this.groupFooterTpl,"</tpl>",'<div class="dataview-border"></div>',{isBigger:function(d,c){return d>c},getCollapseClass:function(c){return c.group_collapsed?" x-grid-group-collapsed":""}});Ext.ux.list.GroupingListView.superclass.initComponent.apply(this,arguments)},collectData:function(d,h){var c=[],b={},g=[],e;for(var f=0,a=d.length;f<a;f++){c[f]=this.prepareData(d[f].data,h+f,d[f]);if(f==0||c[f]["group_index"]!=c[f-1]["group_index"]){if(f>0){b[e]=g;g=[]}e=c[f]["group_index"]}g.push(c[f])}b[e]=g;Ext.each(c,function(j,i){j.rs=b[j.group_index]});return{columns:this.columns,rows:c}},prepareData:function(e,b,a){var d={},c=a.data[a.store.groupField];c=this.groupRenderer?this.groupRenderer(c,b,a):String(c);Ext.apply(d,e);d.group_index=c;if(!this.groupState[c]){this.groupState[c]=this.startCollapsed&&(this.startExpanded.indexOf(c)==-1)?false:true}d.group_collapsed=!this.groupState[c];return d},onRender:function(){Ext.ux.list.GroupingListView.superclass.onRender.apply(this,arguments);this.innerBody.on("mousedown",this.interceptMouse,this)},interceptMouse:function(b){var a=b.getTarget(".x-grid-group-hd",this.innerBody);if(a){b.stopEvent();this.toggleGroup(a.parentNode)}},toggleGroup:function(d,b){var a=Ext.get(d);var c=a.getAttribute("group-index");b=Ext.isDefined(b)?b:a.hasClass("x-grid-group-collapsed");this.groupState[c]=b;a[b?"removeClass":"addClass"]("x-grid-group-collapsed")},toggleAllGroups:function(c){var b=this.innerBody.dom.childNodes;for(var d=0,a=b.length;d<a;d++){this.toggleGroup(b[d],c)}},expandAllGroups:function(){this.toggleAllGroups(true)},collapseAllGroups:function(){this.toggleAllGroups(false)}});Ext.reg("groupinglistview",Ext.ux.list.GroupingListView);Ext.ns("Ext.ux.list");Ext.ux.list.ProgressColumn=Ext.extend(Ext.list.Column,{topText:null,bottomText:null,ceiling:100,colored:true,invertedColor:false,textPst:"%",cls:"x-list-lvprogresscol",constructor:function(a){a.tpl=a.tpl||new Ext.XTemplate('{[this.format(values, "'+a.dataIndex+'")]}');a.tpl.column=this;a.tpl.format=function(b,c){return this.column.getColumnMarkup(b,c)};Ext.ux.list.ProgressColumn.superclass.constructor.call(this,a)},getStyle:function(b,c,a){var d="";if(this.colored==true){if(this.invertedColor==true){if(a>(this.ceiling*0.66)){d="-red"}if(a<(this.ceiling*0.67)&&a>(this.ceiling*0.33)){d="-orange"}if(a<(this.ceiling*0.34)){d="-green"}}else{if(a<=this.ceiling&&a>(this.ceiling*0.66)){d="-green"}if(a<(this.ceiling*0.67)&&a>(this.ceiling*0.33)){d="-orange"}if(a<(this.ceiling*0.34)){d="-red"}}}return d},getTopText:function(b,c,a){if(this.topText){return String.format('<em class="x-progress-toptext">{0}</em>',this.topText)}return""},getBottomText:function(b,c,a){if(this.bottomText){return String.format('<em class="x-progress-bottomtext">{0}</em>',this.bottomText)}return""},getText:function(c,d,b){var a=(b<(this.ceiling/1.818))?"x-progress-text-back":"x-progress-text-front";return String.format('<em class="x-lvprogresscol-text {0}" style="width:100%;">{1}</em>',a,b+this.textPst)},getWrapperClass:function(b,c,a){return""},getColumnMarkup:function(b,c){var a=b[c];return String.format('<em class="{0}">{1}<em class="x-progress-wrap"><em class="x-progress-bar  x-progress-bar{2}" style="width:{3}%;"></em></em>{4}{5}</em>',this.getWrapperClass(b,c,a),this.getTopText(b,c,a),this.getStyle(b,c,a),(a/this.ceiling)*100,this.getText(b,c,a),this.getBottomText(b,c,a))}});Ext.reg("lvprogresscolumn",Ext.ux.list.ProgressColumn);Ext.ns("Ext.ux.ListView.plugin");Ext.ux.ListView.plugin.CheckboxSelection=Ext.extend(Ext.util.Observable,{cls:"ux-lv-checkboxsel-wrap",header:'<div class="ux-lv-checkboxsel x-grid3-check-col">&#160;</div>',width:".027",plain:true,init:function(g){this.tpl=this.tpl||new Ext.XTemplate('<div class="ux-lv-checkboxsel x-grid3-check-col">&#160;</div>');g.initialConfig.columns.unshift({header:this.header,width:this.width,cls:this.cls,isColumn:true,tpl:this.tpl});var l=g.initialConfig.columns,h=0,k=0,m=l.length,b=[];for(var f=0;f<m;f++){var o=l[f];if(!o.isColumn){o.xtype=o.xtype?(/^lv/.test(o.xtype)?o.xtype:"lv"+o.xtype):"lvcolumn";o=Ext.create(o)}if(o.width){h+=o.width*100;k++}b.push(o)}l=g.columns=b;if(k<m){var d=m-k;if(h<g.maxWidth){var a=((g.maxWidth-h)/d)/100;for(var e=0;e<m;e++){var o=l[e];if(!o.width){o.width=a}}}}g.afterRender=g.afterRender.createSequence(function(){g.mon(g.innerHd,"click",this.onHdClick,g);g.on({click:this.onClick,scope:this});g.on("destroy",this.purgeListeners,this)},this);g.onItemClick=g.onItemClick.createInterceptor(function(i,c,j){return !this.getCheckbox(j)},this);g.select=g.select.createSequence(this.select,g);g.deselect=g.deselect.createSequence(this.deselect,g);g.clearSelections=g.clearSelections.createInterceptor(this.clearSelections,g);if(Ext.util.CSS.getRule("div.ux-lv-checkboxsel")==null){var n="div.ux-lv-checkboxsel {height:14px; width:100% !important;}.x-list-header .ux-lv-checkboxsel {margin-left:1px;}em#ext-comp-1023-xlhd-1 {padding:4px 4px 4px 3px;}"+(this.plain)?"":".x-list-body .ux-lv-checkboxsel-wrap {background:#FAFAFA url(/sfExtjs3Plugin/extjs/resources/images/default/grid/grid3-special-col-bg.gif) repeat-y scroll right center !important;}";var p=Ext.util.CSS.createStyleSheet("/* Ext.ux.ListView.plugin.CheckboxSelection stylesheet */\n"+n,"CheckboxSelection");Ext.util.CSS.refreshCache()}},select:function(b,e,a){if(!Ext.isArray(b)){var c=this.getNode(b);if(c){var d;if(d=Ext.fly(c).select("div.ux-lv-checkboxsel",true).first()){d.replaceClass("x-grid3-check-col","x-grid3-check-col-on")}}}},deselect:function(a){a=this.getNode(a);if(a){var c;if(c=this.innerHd.select("div.ux-lv-checkboxsel",true).first()){c.replaceClass("x-grid3-check-col-on","x-grid3-check-col")}var b;if(b=Ext.fly(a).select("div.ux-lv-checkboxsel",true).first()){b.replaceClass("x-grid3-check-col-on","x-grid3-check-col")}}},clearSelections:function(a,c){if((this.multiSelect||this.singleSelect)&&this.selected.getCount()>0){var b;if(b=this.innerHd.select("div.ux-lv-checkboxsel",true).first()){b.replaceClass("x-grid3-check-col-on","x-grid3-check-col")}Ext.each(this.getSelectedNodes(),function(d){var e;if(e=Ext.fly(d).select("div.ux-lv-checkboxsel",true).first()){e.replaceClass("x-grid3-check-col-on","x-grid3-check-col")}})}},onClick:function(a,b,c,f){var d=this.getCheckbox(f);if(d){if(d.hasClass("x-grid3-check-col")){a.select(c,true)}else{a.deselect(c)}}},onHdClick:function(d){var c=d.getTarget("em",3);if(c&&!this.disableHeaders){var a=this.findHeaderIndex(c);var b=d.getTarget(".ux-lv-checkboxsel");if(b){b=Ext.fly(b);if(b.hasClass("x-grid3-check-col")){b.replaceClass("x-grid3-check-col","x-grid3-check-col-on");this.select(this.getNodes(),true,true)}else{this.clearSelections(true)}this.fireEvent("selectionchange",this,this.getSelectedNodes())}}},getCheckbox:function(c){var b=false;var a=c.getTarget(".ux-lv-checkboxsel");if(a){b=Ext.fly(a)}return b}});Ext.preg("lvcheckboxselection",Ext.ux.ListView.plugin.CheckboxSelection);Ext.ns("Ext.ux.ListView.plugin");Ext.ux.ListView.plugin.CheckColumn=function(a){Ext.apply(this,a);this.addEvents("beforecheck","check");Ext.ux.ListView.plugin.CheckColumn.superclass.constructor.call(this)};Ext.extend(Ext.ux.ListView.plugin.CheckColumn,Ext.util.Observable,{isColumn:true,header:"&#160;",cls:"",width:".1",keepSelection:false,actionEvent:"click",init:function(b){this.tpl=this.tpl||new Ext.XTemplate("{"+this.dataIndex+":this.format}");this.tpl.format=function(c){return String.format('<div class="ux-lv-checkbox x-grid3-check-col{0}">&#160;</div>',c?"-on":"")};var a={scope:this};a[this.actionEvent]=this.onClick;b.afterRender=b.afterRender.createSequence(function(){b.on(a);b.on("destroy",this.purgeListeners,this)},this);if(true===this.keepSelection){b.onItemClick=b.onItemClick.createInterceptor(function(d,c,f){return !this.getCheckbox(f)},this)}},onClick:function(b,c,d,g){var f=this.getCheckbox(g);if(f){var a=b.getRecord(d);if("function"===typeof this.cb){this.cb(b,a,f,d,c)}if(false===this.fireEvent("beforecheck",b,a,f,d,c)){return}else{this.fireEvent("check",b,a,f,d,c);a.set(this.dataIndex,!a.data[this.dataIndex])}}},getCheckbox:function(c){var b=false;var a=c.getTarget(".ux-lv-checkbox");if(a){b=a}return b}});Ext.preg("lvcheckcolumnplugin",Ext.ux.ListView.plugin.CheckColumn);Ext.ns("Ext.ux.ListView.plugin");Ext.ux.ListView.plugin.RowActions=function(a){Ext.apply(this,a);this.addEvents("beforeaction","action");Ext.ux.ListView.plugin.RowActions.superclass.constructor.call(this)};Ext.extend(Ext.ux.ListView.plugin.RowActions,Ext.util.Observable,{isColumn:true,header:"&#160;",align:"left",cls:"ux-row-action",width:".1",actionEvent:"click",keepSelection:false,tplRow:'<tpl for="actions"><em class="ux-row-action-item {cls} <tpl if="text">ux-row-action-text</tpl>" style="{hide}{style}" qtip="{qtip}"><tpl if="text"><span qtip="{qtip}">{text}</span></tpl></em></tpl>',init:function(c){if(!this.tpl){this.tpl=this.processActions(this.actions)}var b={scope:this};b[this.actionEvent]=this.onClick;c.afterRender=c.afterRender.createSequence(function(){c.on(b);c.on("destroy",this.purgeListeners,this)},this);if(true===this.keepSelection){c.onItemClick=c.onItemClick.createInterceptor(function(g,f,h){return !this.getAction(h)},this)}if(Ext.util.CSS.getRule(".ux-row-action-item")==null){var a=".ux-row-action-item {float: "+(this.align||"left")+";min-width:16px;height:16px;background-repeat:no-repeat;margin: 0 5px 0 0;cursor:pointer;overflow:hidden;}.x-list-body .ux-row-action em {padding:0px;}.x-list-body em.ux-row-action {padding:2px 3px 0;}.ext-ie .ux-row-action-item {width: 16px;}.ext-ie .ux-row-action-text {width: auto;}.ux-row-action-item span {vertical-align:middle; padding: 0 0 0 20px;  line-height: 18px;}.ext-ie .ux-row-action-item span {width: auto;}";var d=Ext.util.CSS.createStyleSheet("/* Ext.ux.ListView.plugin.RowActions stylesheet */\n"+a,"RowActions");Ext.util.CSS.refreshCache()}},processActions:function(d,c){var a=[];Ext.each(d,function(e,f){if(e.iconCls&&"function"===typeof(e.callback||e.cb)){this.callbacks=this.callbacks||{};this.callbacks[e.iconCls]=e.callback||e.cb}var g={cls:e.iconIndex?"{"+e.iconIndex+"}":(e.iconCls?e.iconCls:""),qtip:e.qtipIndex?"{"+e.qtipIndex+"}":(e.tooltip||e.qtip?e.tooltip||e.qtip:""),text:e.textIndex?"{"+e.textIndex+"}":(e.text?e.text:""),hide:e.hideIndex?'<tpl if="'+e.hideIndex+'">'+("display"===this.hideMode?"display:none":"visibility:hidden")+";</tpl>":(e.hide?("display"===this.hideMode?"display:none":"visibility:hidden;"):""),style:e.style?e.style:""};a.push(g)},this);var b=new Ext.XTemplate(c||this.tplRow);return new Ext.XTemplate(b.apply({actions:a}))},onClick:function(b,c,d,g){var a=b.getRecord(d);var f=this.getAction(g);if(false!==a&&false!==f){if(this.callbacks&&"function"===typeof this.callbacks[f]){this.callbacks[f](b,a,f,d,c)}if(false===this.fireEvent("beforeaction",b,a,f,d,c)){return}else{this.fireEvent("action",b,a,f,d,c)}}},getAction:function(c){var b=false;var a=c.getTarget(".ux-row-action-item");if(a){b=a.className.replace(/ux-row-action-item /,"");if(b){b=b.replace(/ ux-row-action-text/,"");b=b.trim()}}return b}});Ext.preg("lvrowactions",Ext.ux.ListView.plugin.RowActions);Ext.ns("Ext.ux");Ext.ux.ListViewPanel=Ext.extend(Ext.Panel,{layout:"fit",initComponent:function(){this.items=this.buildListView();Ext.ux.ListViewPanel.superclass.initComponent.call(this);this.relayEvents(this.getView(),["click"]);this.relayEvents(this.getStore(),["load"])},buildListView:function(){return{}},buildStore:function(){return{xtype:"jsonstore"}},clearView:function(){this.getStore().removeAll()},createAndSelectRecord:function(d){var b=this.getView();var a=new b.store.recordType(d);b.store.addSorted(a);var c=b.store.indexOf(a);b.select(c);return a},clearSelections:function(){return this.getView().clearSelections()},getView:function(){return this.items.items[0]},getStore:function(){return this.getView().store},getSelectedRecords:function(){return this.getView().getSelectedRecords()},getSelected:function(){return this.getSelectedRecords()[0]},refreshView:function(){this.getView().store.reload()},selectById:function(c){var a=this.getView();c=c||false;if(c){var b=a.store.find("id",c);a.select(b)}},loadStoreByParams:function(a){a=a||{};this.getStore().load({params:a})}});