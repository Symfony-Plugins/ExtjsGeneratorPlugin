Ext.namespace('Ext.ux.grid');
Ext.ux.grid.ForeignFieldColumn = Ext.extend(Ext.grid.Column, {
  isColumn: true,
  constructor : Ext.grid.Column.prototype.constructor.createSequence(function(config) {
    this.renderer = function(combo) {
      return function(value, meta, record, rowIndex, colIndex, store) {
        meta.css += 'x-grid3-cell-wrap';
        if (value === '')
          return;

        // get returnValue from comboBox-store
        var idx = combo.store.findBy(function(record) {
          if (record.get(combo.valueField) == value) {
            value = record.get(combo.displayField);
            return true;
          }
        });

        return value;
      };
    };

    this.renderer = this.renderer(this.editor);
  })
});
Ext.reg('foreignfieldcolumn', Ext.ux.grid.ForeignFieldColumn);

Ext.grid.Column.types.foreignfieldcolumn = Ext.ux.grid.ForeignFieldColumn;