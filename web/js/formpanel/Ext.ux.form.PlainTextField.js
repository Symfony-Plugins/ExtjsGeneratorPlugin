Ext.namespace('Ext.ux.form');
Ext.ux.form.PlainTextField = Ext.extend(Ext.form.TextField, {
  fieldClass : 'x-static-text-field',
  onRender : function() {
    this.readOnly = true;
    this.disabled = !this.initialConfig.submitValue;
    Ext.ux.form.PlainTextField.superclass.onRender.apply(this, arguments);
  }
});

Ext.reg('plaintextfield', Ext.ux.form.PlainTextField);