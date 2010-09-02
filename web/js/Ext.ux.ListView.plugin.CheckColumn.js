/**
 * @class Ext.ux.ListView.plugin.CheckColumn
 * @extends Ext.util.Observable
 *
 * @author Benjamin Runnels
 * @date 2 September 2010
 * @version 0.2
 * 
 * @license Ext.ux.ListView.plugin.CheckColumn is licensed under the terms of the Open
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
Ext.ux.ListView.plugin.CheckColumn = function(config) {
  Ext.apply(this, config);
  this.addEvents(
    /**
     * @event beforecheck Fires before the datastore is changed and before the callback 
     * is called. Return false to cancel the subsequent store change and callback.
     * @param {Ext.list.ListView}
     *          view
     * @param {Ext.data.Record}
     *          record Record corresponding to row clicked
     * @param {Object}
     *          checkbox The clicked Checkbox element.
     * @param {String}
     *          node The clicked node
     * @param {Integer}
     *          index Index of clicked row
     */  
    'beforecheck',
    
    /**
     * @event check Fires when Checkbox is checked or unchecked.
     * @param {Ext.list.ListView}
     *          view
     * @param {Ext.data.Record}
     *          record Record corresponding to row clicked
     * @param {Object}
     *          checkbox The clicked Checkbox element.
     * @param {String}
     *          node The clicked node
     * @param {Integer}
     *          index Index of clicked row
     */
    'check'
  );
  
  Ext.ux.ListView.plugin.CheckColumn.superclass.constructor.call(this);
};

Ext.extend(Ext.ux.ListView.plugin.CheckColumn, Ext.util.Observable, {
  /**
   * @cfg {Boolean} isColumn Tell ListView that we are column. Do not touch!
   * @private
   */
  isColumn : true,
  
  /**
   * @cfg {String} header CheckColumn column header
   */
  header : '&#160;',
  
  /**
   * @cfg {String} additional class to add to the column
   */
  cls : '',
  
  /**
   * @cfg {String} default column width
   */
  width : '.1',
  
  /**
   * @cfg {Boolean} keepSelection Set it to true if you do not want action
   *      clicks to affect selected row(s) (defaults to false). By default, when
   *      user clicks a CheckColumn the clicked row is selected and the action
   *      events are fired. If this option is true then the current selection is
   *      not affected, only the CheckColumn events are fired.
   */
  keepSelection : false,
  
  /**
   * @cfg {String} actionEvent Event to trigger actions, e.g. click, dblclick,
   *      mouseover (defaults to 'click')
   */
  actionEvent : 'click',

  /**
   * Init function
   * 
   * @param {Ext.view.ListView}
   *          listview ListView this plugin is attached to
   */
  init : function(listview) {
    this.tpl = this.tpl || new Ext.XTemplate('{' + this.dataIndex + ':this.format}');

    this.tpl.format = function(v) {
      return String.format('<div class="ux-lv-checkbox x-grid3-check-col{0}">&#160;</div>', v
          ? '-on' : '');
    };

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
        return !this.getCheckbox(e);
      }, this);
    }
  },

  /**
   * ListView body actionEvent event handler
   * 
   * @private
   */
  onClick : function(view, index, node, e) {
    var checkbox = this.getCheckbox(e);
    if (checkbox) {
      var record = view.getRecord(node);

      if ('function' === typeof this.cb) {
        this.cb(view, record, checkbox, node, index);
      }

      // fire events
      if (false === this.fireEvent('beforecheck', view, record, checkbox, node, index)) {
        return;
      } else {
        this.fireEvent('check', view, record, checkbox, node, index);
        record.set(this.dataIndex, !record.data[this.dataIndex]);
      }
    }
  },

  /**
   * Checks if the checkbox was clicked and if so returns the checkbox element
   * @param {Object} e Event object
   * @return {Object}
   */
  getCheckbox : function(e) {
    var checkbox = false;
    var t = e.getTarget('.ux-lv-checkbox');
    if (t)
      checkbox = t;
    return checkbox;
  }
});

Ext.preg('lvcheckcolumnplugin', Ext.ux.ListView.plugin.CheckColumn);
