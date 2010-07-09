Ext.namespace('Ext.ux.form.ComboBox.plugin');
Ext.ux.form.ComboBox.plugin.ComboListAutoSizer = (function() {
  function autoSizeList(combo) {
    var itemEl = combo.view && combo.view.getEl() && combo.view.getEl().child(combo.itemSelector || '.x-combo-list-item');
    if (!itemEl)
      return;
    var textMetrics = Ext.util.TextMetrics.createInstance(itemEl);
    var autoWidth = Math.max(combo.minListWidth, combo.getWidth());
    combo.getStore().each(function(record) {
      autoWidth = Math.max(autoWidth, textMetrics.getWidth(record.get(combo.displayField)) + 25);
    });
    combo.list.setWidth(autoWidth);
    combo.innerList.setWidth(autoWidth - combo.list.getFrameWidth('lr'));
    combo.list.alignTo(combo.wrap, combo.listAlign);
  }

  // Public API
  return {
    init : function(combo) {
      combo.on('expand', autoSizeList, null, {
        single : true
      });
      var store = combo.getStore();
      store.on('load', function() {
        autoSizeList(combo);
      });
      store.on('update', function() {
        autoSizeList(combo);
      });
    }
  };
})();

Ext.preg('comboListAutoSizer', Ext.ux.form.ComboBox.plugin.ComboListAutoSizer);
