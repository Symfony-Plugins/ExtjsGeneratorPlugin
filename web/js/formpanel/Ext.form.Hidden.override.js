Ext.override(Ext.form.Hidden, {
  reset : function() {
    this.originalValue = this.defaultValue;
    this.setValue(this.defaultValue);
    this.clearInvalid();
  }
});