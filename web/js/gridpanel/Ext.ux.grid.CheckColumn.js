/*!
 * Ext JS Library 3.3.0
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ns('Ext.ux.grid');

/**
 * @class Ext.ux.grid.CheckColumn
 * @extends Ext.grid.Column
 * <p>A Column subclass which renders a checkbox in each column cell which toggles the truthiness of the associated data field on click.</p>
 * <p><b>Note. As of ExtJS 3.3 this no longer has to be configured as a plugin of the GridPanel.</b></p>
 * <p>Example usage:</p>
 * <pre><code>
var cm = new Ext.grid.ColumnModel([{
       header: 'Foo',
       ...
    },{
       xtype: 'checkcolumn',
       header: 'Indoor?',
       dataIndex: 'indoor',
       width: 55
    }
]);

// create the grid
var grid = new Ext.grid.EditorGridPanel({
    ...
    colModel: cm,
    ...
});
 * </code></pre>
 * In addition to toggling a Boolean value within the record data, this
 * class toggles a css class between <tt>'x-grid3-check-col'</tt> and
 * <tt>'x-grid3-check-col-on'</tt> to alter the background image used for
 * a column.
 */
Ext.ux.grid.CheckColumn = Ext.extend(Ext.grid.Column, {

    /**
     * @private
     * Process and refire events routed from the GridView's processEvent method.
     */
    processEvent : function(name, e, grid, rowIndex, colIndex){
        if (name == 'mousedown') {
            if(this.editable) {
              var record = grid.store.getAt(rowIndex);
              if(!record.dirty)
              {
                record.set(this.dataIndex, !record.data[this.dataIndex]);
                
                grid.fireEvent('afteredit', {
                  grid : grid,
                  record : record,
                  field : this.name || this.dataIndex,
                  value : record.data[this.dataIndex],
                  originalValue : !record.data[this.dataIndex],
                  row : rowIndex,
                  column : colIndex
                });
              }
            }

            return false; // Cancel row selection.
        } else {
            return Ext.grid.ActionColumn.superclass.processEvent.apply(this, arguments);
        }
    },

    renderer : function(v, p, record){
        p.css += ' x-grid3-check-col-td'; 
        return String.format('<div class="x-grid3-check-col{0}">&#160;</div>', v ? '-on' : '');
    }
});

// register Column xtype
Ext.grid.Column.types.checkcolumn = Ext.ux.grid.CheckColumn;