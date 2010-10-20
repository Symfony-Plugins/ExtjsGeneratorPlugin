/**
 * @class Ext.ux.list.CheckColumn
 * @extends Ext.list.Column
 *
 * @author Benjamin Runnels
 * @date 26 August 2010
 * @version 0.1
 * 
 * @license Ext.ux.list.CheckColumn is licensed under the terms of the Open
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

Ext.ns('Ext.ux.list');
Ext.ux.list.CheckColumn = Ext.extend(Ext.list.Column, {
  constructor : function(c) {
    c.tpl = c.tpl || new Ext.XTemplate('{' + c.dataIndex + ':this.format}');

    c.tpl.format = function(v) {
      return String.format('<div class="x-grid3-check-col{0}">&#160;</div>', v ? '-on' : '');
    };

    Ext.ux.list.CheckColumn.superclass.constructor.call(this, c);
  }
});

Ext.reg('lvcheckcolumn', Ext.ux.list.CheckColumn);