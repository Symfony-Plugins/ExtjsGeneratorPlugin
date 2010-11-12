Ext.namespace('Ext.ux.form');
Ext.ux.form.TwinDateField = Ext.extend(Ext.form.DateField, {
  getTrigger : Ext.form.TwinTriggerField.prototype.getTrigger,
  initTrigger : Ext.form.TwinTriggerField.prototype.initTrigger,
  initComponent : Ext.form.TwinTriggerField.prototype.initComponent,
  trigger2Class : 'x-form-date-trigger',
  trigger1Class : 'x-form-clear-trigger',
  hideTrigger1 : true,
  submitOnSelect : true,
  submitOnClear : true,
  allowClear : true,
  defaultValue : null,

  onSelect : Ext.form.DateField.prototype.onSelect.createSequence(function(v) {
    if (this.value && this.ownerCt && this.ownerCt.buttons && this.submitOnSelect) {
      this.ownerCt.buttons[0].handler.call(this.ownerCt);
    }
  }),

  onRender : Ext.form.DateField.prototype.onRender.createSequence(function(v) {
    this.getTrigger(0).hide();
  }),

  setValue : Ext.form.DateField.prototype.setValue.createSequence(function(v) {
    if (v !== null && v != '') {
      if (this.allowClear)
        this.getTrigger(0).show();
    } else {
      this.getTrigger(0).hide();
    }
  }),

  reset : Ext.form.DateField.prototype.reset.createSequence(function() {
    this.originalValue = this.defaultValue;
    this.setValue(this.defaultValue);
    if (this.allowClear)
      this.getTrigger(0).hide();
  }),

  onTrigger2Click : function() {
    if (!this.readOnly)
      this.onTriggerClick();
  },

  onTrigger1Click : function() {
    if (!this.disabled && !this.readOnly) {
      this.clearValue();
      this.getTrigger(0).hide();
      if (this.ownerCt && this.ownerCt.buttons && this.submitOnClear) {
        this.ownerCt.buttons[0].handler.call(this.ownerCt);
      }
      this.fireEvent('clear', this);
      this.onFocus();
    }
  },

  /**
   * Clears any text/value currently set in the field
   */
  clearValue : function() {
    if (this.hiddenField) {
      this.hiddenField.value = '';
    }
    this.setRawValue('');
    this.lastSelectionText = '';
    this.applyEmptyText();
    this.value = '';
  }
});
Ext.ComponentMgr.registerType('twindatefield', Ext.ux.form.TwinDateField);
