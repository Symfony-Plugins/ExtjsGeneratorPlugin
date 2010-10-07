/**
 * @class Ext.ux.ListView.plugin.RowActions
 * @extends Ext.util.Observable
 * 
 * RowActions plugin for Ext ListView. Contains renderer for icons and fires events
 * when an icon is clicked.
 * 
 * Important general information: Actions are identified by iconCls. Wherever an
 * <i>action</i> is referenced (event argument, callback argument), the iconCls
 * of clicked icon is used. In other words, action identifier === iconCls.
 * 
 * @author Benjamin Runnels
 * @author Ing. Jozef Sakáloš
 * @date 2 September 2010
 * @version 1.1
 * 
 * @license Ext.ux.ListView.plugin.RowActions is licensed under the terms of the Open
 *          Source LGPL 3.0 license. Commercial use is permitted to the extent
 *          that the code/component(s) do NOT become part of another Open Source
 *          or Commercially licensed development library or toolkit without
 *          explicit permission.
 * 
 * <p>
 * License details: <a href="http://www.gnu.org/licenses/lgpl.html"
 * target="_blank">http://www.gnu.org/licenses/lgpl.html</a>
 * </p>
 */

Ext.ns('Ext.ux.ListView.plugin');
Ext.ux.ListView.plugin.RowActions = function(config) {
  Ext.apply(this, config);
  this.addEvents(
    /**
     * @event beforeaction Fires before action event. Return false to cancel the
     *        subsequent action event.
     * @param {Ext.list.ListView}
     *          view
     * @param {Ext.data.Record}
     *          record Record corresponding to row clicked
     * @param {String}
     *          action Identifies the action icon clicked. Equals to icon css
     *          class name.
     * @param {String}
     *          node The clicked node
     * @param {Integer}
     *          index Index of clicked row
     */
    'beforeaction',
    
    /**
     * @event action Fires when icon is clicked
     * @param {Ext.list.ListView}
     *          view
     * @param {Ext.data.Record}
     *          record Record corresponding to row clicked
     * @param {String}
     *          action Identifies the action icon clicked. Equals to icon css
     *          class name.
     * @param {String}
     *          node The clicked node
     * @param {Integer}
     *          index Index of clicked row
     */
    'action'
  );
  
  Ext.ux.ListView.plugin.RowActions.superclass.constructor.call(this);
};

Ext.extend(Ext.ux.ListView.plugin.RowActions, Ext.util.Observable, {
  /**
   * @cfg {Boolean} isColumn Tell ListView that we are column. Do not touch!
   * @private
   */
  isColumn : true,
  
  /**
   * @cfg {String} header Actions column header
   */
  header : '&#160;',
  
  /**
   * @cfg {String} default alignment of the cell data
   */
  align : 'left',
  
  /**
   * @cfg {String} additional class to add to the column
   */
  cls : 'ux-row-action',
  
  /**
   * @cfg {String} default column width
   */
  width : '.1',
  
  /**
   * @cfg {String} actionEvent Event to trigger actions, e.g. click, dblclick,
   *      mouseover (defaults to 'click')
   */
  actionEvent : 'click',
  
  /**
   * @cfg {Boolean} keepSelection Set it to true if you do not want action
   *      clicks to affect selected row(s) (defaults to false). By default, when
   *      user clicks an action icon the clicked row is selected and the action
   *      events are fired. If this option is true then the current selection is
   *      not affected, only the action events are fired.
   */
  keepSelection : false,

  /**
   * @cfg {String} tplRow Template for row actions
   * @private
   */
  tplRow : '<tpl for="actions">'
    + '<em class="ux-row-action-item {cls} <tpl if="text">'
    + 'ux-row-action-text</tpl>" style="{hide}{style}" qtip="{qtip}">'
    + '<tpl if="text"><span qtip="{qtip}">{text}</span></tpl></em></tpl>',
  
  /**
   * Init function
   * 
   * @param {Ext.view.ListView}
   *          listview ListView this plugin is attached to
   */
  init : function(listview) {
    if (!this.tpl) {
      this.tpl = this.processActions(this.actions);
    }
    
    var cfg = {
      scope : this
    };
    cfg[this.actionEvent] = this.onClick;
    listview.afterRender = listview.afterRender.createSequence(function() {
      listview.on(cfg);
      listview.on('destroy', this.purgeListeners, this);
    }, this);
    
    // cancel click
    if (true === this.keepSelection) {
      listview.onItemClick = listview.onItemClick.createInterceptor(function(item, index, e) {
        return !this.getAction(e);
      }, this);
    }

    // do our CSS here so we don't have to include it
    if (Ext.util.CSS.getRule('.ux-row-action-item') == null) {
      var styleBody = 
        '.ux-row-action-item {float: ' + (this.align || 'left') + ';min-width:16px;height:16px;background-repeat:no-repeat;margin: 0 5px 0 0;cursor:pointer;overflow:hidden;}' +
        '.x-list-body .ux-row-action em {padding:0px;}' +
        '.x-list-body em.ux-row-action {padding:2px 3px 0;}' +
        '.ext-ie .ux-row-action-item {width: 16px;}' +
        '.ext-ie .ux-row-action-text {width: auto;}' +
        '.ux-row-action-item span {vertical-align:middle; padding: 0 0 0 20px;  line-height: 18px;}' +
        '.ext-ie .ux-row-action-item span {width: auto;}'
      var styleSheet = Ext.util.CSS.createStyleSheet(
        '/* Ext.ux.ListView.plugin.RowActions stylesheet */\n' + styleBody, 'RowActions'
      );
      Ext.util.CSS.refreshCache();
    }
  },
  
  /**
   * Processes actions configs and returns template.
   * 
   * @param {Array}
   *          actions
   * @param {String}
   *          template Optional. Template to use for one action item.
   * @return {String}
   * @private
   */
  processActions : function(actions, template) {
    var acts = [];

    // actions loop
    Ext.each(actions, function(a, i) {
      // save callback
      if (a.iconCls && 'function' === typeof(a.callback || a.cb)) {
        this.callbacks = this.callbacks || {};
        this.callbacks[a.iconCls] = a.callback || a.cb;
      }

      // data for intermediate template
      var o = {
        cls : a.iconIndex ? '{' + a.iconIndex + '}' : (a.iconCls ? a.iconCls : ''),
        qtip : a.qtipIndex ? '{' + a.qtipIndex + '}' : (a.tooltip || a.qtip
          ? a.tooltip || a.qtip : ''),
        text : a.textIndex ? '{' + a.textIndex + '}' : (a.text ? a.text : ''),
        hide : a.hideIndex
          ? '<tpl if="' + a.hideIndex + '">'
            + ('display' === this.hideMode ? 'display:none' : 'visibility:hidden') + ';</tpl>'
          : (a.hide ? ('display' === this.hideMode ? 'display:none' : 'visibility:hidden;') : ''),
        style : a.style ? a.style : ''
      };
      acts.push(o);

    }, this);

    var xt = new Ext.XTemplate(template || this.tplRow);
    return new Ext.XTemplate(xt.apply({
      actions : acts
    }));
  },
  
  /**
   * ListView body actionEvent event handler
   * 
   * @private
   */
  onClick : function(view, index, node, e) {
    var record = view.getRecord(node);
    var action = this.getAction(e);
    if (false !== record && false !== action) {
      // call callback if any
      if (this.callbacks && 'function' === typeof this.callbacks[action]) {
        this.callbacks[action](view, record, action, node, index);
      }

      // fire events
      if (false === this.fireEvent('beforeaction', view, record, action, node, index)) {
        return;
      } else {
        this.fireEvent('action', view, record, action, node, index);
      }
    }
  },

  /**
   * Checks if an rowaction was clicked if so returns the associated action 
   * @param {Object} e Event object
   * @return {String}
   */
  getAction : function(e) {
    var action = false;
    var t = e.getTarget('.ux-row-action-item');
    if (t) {
      action = t.className.replace(/ux-row-action-item /, '');
      if (action) {
        action = action.replace(/ ux-row-action-text/, '');
        action = action.trim();
      }
    }
    return action;
  }
});

Ext.preg('lvrowactions', Ext.ux.ListView.plugin.RowActions);
