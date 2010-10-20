/**
 * @class Ext.ux.ListView.plugin.CheckboxSelection
 * @extends Ext.util.Observable
 * 
 * @author Benjamin Runnels
 * @date 6 October 2010
 * @version 0.1
 * 
 * @license Ext.ux.ListView.plugin.CheckboxSelection is licensed under the terms of
 *          the Open Source LGPL 3.0 license. Commercial use is permitted to the
 *          extent that the code/component(s) do NOT become part of another Open
 *          Source or Commercially licensed development library or toolkit
 *          without explicit permission.
 * 
 * <p>
 * License details: <a href="http://www.gnu.org/licenses/lgpl.html"
 * target="_blank">http://www.gnu.org/licenses/lgpl.html</a>
 * </p>
 */

Ext.ns('Ext.ux.ListView.plugin');
Ext.ux.ListView.plugin.CheckboxSelection = Ext.extend(Ext.util.Observable, {
  /**
   * @cfg {String} additional class to add to the column
   */
  cls : 'ux-lv-checkboxsel-wrap',

  /**
   * @cfg {String} header CheckboxSelection column header
   */
  header : '<div class="ux-lv-checkboxsel x-grid3-check-col">&#160;</div>',

  /**
   * @cfg {String} default CheckboxSelection column width
   */
  width : '.027',

  /**
   * Init function
   * 
   * @param {Ext.view.ListView}
   *          listview ListView this plugin is attached to
   */
  init : function(listview) {
    this.tpl = this.tpl || new Ext.XTemplate('<div class="ux-lv-checkboxsel x-grid3-check-col">&#160;</div>');

    listview.initialConfig.columns.unshift({
      header : this.header,
      width : this.width,
      cls : this.cls,
      isColumn : true,
      tpl : this.tpl
    });

    var cs = listview.initialConfig.columns, allocatedWidth = 0, colsWithWidth = 0, len = cs.length, columns = [];

    for (var i = 0; i < len; i++) {
      var c = cs[i];
      if (!c.isColumn) {
        c.xtype = c.xtype ? (/^lv/.test(c.xtype) ? c.xtype : 'lv' + c.xtype) : 'lvcolumn';
        c = Ext.create(c);
      }
      if (c.width) {
        allocatedWidth += c.width * 100;
        colsWithWidth++;
      }
      columns.push(c);
    }

    cs = listview.columns = columns;

    // auto calculate missing column widths
    if (colsWithWidth < len) {
      var remaining = len - colsWithWidth;
      if (allocatedWidth < listview.maxWidth) {
        var perCol = ((listview.maxWidth - allocatedWidth) / remaining) / 100;
        for (var j = 0; j < len; j++) {
          var c = cs[j];
          if (!c.width) {
            c.width = perCol;
          }
        }
      }
    }

    listview.afterRender = listview.afterRender.createSequence(function() {
      listview.mon(listview.innerHd, 'click', this.onHdClick, listview);
      
      listview.on({
        click : this.onClick,
        scope : this
      });

      listview.on('destroy', this.purgeListeners, this);
    }, this);

    listview.onItemClick = listview.onItemClick.createInterceptor(function(item, index, e) {
      return !this.getCheckbox(e);
    }, this);

    listview.select = listview.select.createSequence(this.select, listview);
    listview.deselect = listview.deselect.createSequence(this.deselect, listview);
    listview.clearSelections = listview.clearSelections.createInterceptor(this.clearSelections, listview);
    
    // do our CSS here so we don't have to include it
    if (Ext.util.CSS.getRule('div.ux-lv-checkboxsel') == null) {
      var styleBody = 'div.ux-lv-checkboxsel {height:14px; width:100% !important;}' +
      '.x-list-header .ux-lv-checkboxsel {margin-left:1px;}' +
      'em#ext-comp-1023-xlhd-1 {padding:4px 4px 4px 3px;}' +
      '.x-list-body .ux-lv-checkboxsel-wrap {background:#FAFAFA url(/sfExtjs3Plugin/extjs/resources/images/default/grid/grid3-special-col-bg.gif) repeat-y scroll right center !important;}';
      var styleSheet = Ext.util.CSS.createStyleSheet(
        '/* Ext.ux.ListView.plugin.CheckboxSelection stylesheet */\n' + styleBody, 'CheckboxSelection'
      );
      Ext.util.CSS.refreshCache();
    }
  },

  select : function(nodeInfo, keepExisting, suppressEvent) {
    if (!Ext.isArray(nodeInfo)) {
      var node = this.getNode(nodeInfo);
      if (node) {
        var checkbox;
        if (checkbox = Ext.fly(node).select('div.ux-lv-checkboxsel', true).first()) {
          checkbox.replaceClass('x-grid3-check-col', 'x-grid3-check-col-on');
        }
      }
    }
  },

  deselect : function(node) {
    node = this.getNode(node);
    if (node) {
      var headerCheck;
      if (headerCheck = this.innerHd.select('div.ux-lv-checkboxsel', true).first()) {
        headerCheck.replaceClass('x-grid3-check-col-on', 'x-grid3-check-col');
      }

      var checkbox;
      if (checkbox = Ext.fly(node).select('div.ux-lv-checkboxsel', true).first()) {
        checkbox.replaceClass('x-grid3-check-col-on', 'x-grid3-check-col');
      }
    }
  },

  clearSelections : function(suppressEvent, skipUpdate) {
    if ((this.multiSelect || this.singleSelect) && this.selected.getCount() > 0) {
      var headerCheck;
      if (headerCheck = this.innerHd.select('div.ux-lv-checkboxsel', true).first()) {
        headerCheck.replaceClass('x-grid3-check-col-on', 'x-grid3-check-col');
      }
      
      Ext.each(this.getSelectedNodes(), function(node) {
        var checkbox;
        if (checkbox = Ext.fly(node).select('div.ux-lv-checkboxsel', true).first()) {
          checkbox.replaceClass('x-grid3-check-col-on', 'x-grid3-check-col');
        }
      });
    }
  },

  /**
   * ListView body click event handler
   * 
   * @private
   */
  onClick : function(view, index, node, e) {
    var checkbox = this.getCheckbox(e);
    if (checkbox) {
      if (checkbox.hasClass('x-grid3-check-col')) {
        view.select(node, true);
      } else {
        view.deselect(node);
      }
    }
  },

  /**
   * ListView header click event handler
   * 
   * @private
   */
  onHdClick : function(e) {
    var hd = e.getTarget('em', 3);
    if (hd && !this.disableHeaders) {
      var index = this.findHeaderIndex(hd);
      var checkbox = e.getTarget('.ux-lv-checkboxsel');

      if (checkbox) {
        checkbox = Ext.fly(checkbox);
        if (checkbox.hasClass('x-grid3-check-col')) {
          checkbox.replaceClass('x-grid3-check-col', 'x-grid3-check-col-on');
          this.select(this.getNodes(), true, true);
        } else {
          this.clearSelections(true);
        }
        this.fireEvent('selectionchange', this, this.getSelectedNodes());
      }
    }
  },

  /**
   * Checks if the checkbox was clicked and if so returns the checkbox element
   * 
   * @param {Object}
   *          e Event object
   * @return {Object}
   */
  getCheckbox : function(e) {
    var checkbox = false;
    var t = e.getTarget('.ux-lv-checkboxsel');
    if (t)
      checkbox = Ext.fly(t);
    return checkbox;
  }
});

Ext.preg('lvcheckboxselection', Ext.ux.ListView.plugin.CheckboxSelection);
