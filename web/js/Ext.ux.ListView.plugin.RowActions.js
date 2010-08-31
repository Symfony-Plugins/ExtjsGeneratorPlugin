Ext.ns('Ext.ux.ListView.plugin');
Ext.ux.ListView.plugin.RowActions = function(config) {
  Ext.apply(this, config);

  this.addEvents('beforeaction', 'action');

  // call parent
  Ext.ux.ListView.plugin.RowActions.superclass.constructor.call(this);
};

Ext.extend(Ext.ux.ListView.plugin.RowActions, Ext.util.Observable, {
  isColumn : true,
  header : '&#160;',
  align : 'left',
  cls : '',
  width : '.1',
  actionEvent : 'click',

  tplRow : '<div class="ux-row-action"><tpl for="actions">'
    + '<div class="ux-row-action-item {cls} <tpl if="text">'
    + 'ux-row-action-text</tpl>" style="{hide}{style}" qtip="{qtip}">'
    + '<tpl if="text"><span qtip="{qtip}">{text}</span></tpl></div></tpl></div>',

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

    // do our CSS here so we don't have to include it
    if (Ext.util.CSS.getRule('.ux-row-action-cell') == null) {
      var styleBody = '.ux-row-action-item {float: ' + (this.align || 'left') + ';min-width: 16px;height: 16px;background-repeat: no-repeat;margin: 0 5px 0 0;cursor: pointer;overflow: hidden;}'
        + '.ext-ie .ux-row-action-item {width: 16px;}'
        + '.ext-ie .ux-row-action-text {width: auto;}'
        + '.ux-row-action-item span {vertical-align:middle; padding: 0 0 0 20px;  line-height: 18px;}'
        + '.ext-ie .ux-row-action-item span {width: auto;}'
      var styleSheet = Ext.util.CSS.createStyleSheet(
        '/* Ext.ux.ListView.plugin.RowActions stylesheet */\n' + styleBody, 'RowActions'
      );
      Ext.util.CSS.refreshCache();
    }
  },

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