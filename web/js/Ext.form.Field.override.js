Ext.override(Ext.form.Field, {
  initComponent : function() {
    if (this.required) {
      this.allowBlank = false;
      this.fieldLabel = this.fieldLabel + ((this.labelSeparator) ? this.labelSeparator : ':') + '<em>required</em>';
      this.labelSeparator = '';
      this.itemCls = 'x-form-item-required';
      this.blankText = 'This field is required';
    }

    Ext.form.Field.superclass.initComponent.call(this);
    this.addEvents('focus', 'blur', 'specialkey', 'change', 'invalid', 'valid');
  }
});