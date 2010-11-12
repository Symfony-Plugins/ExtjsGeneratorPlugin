Ext.override(Ext.form.TextField, {
  defaultValue : null,
  initComponent : function() {
    Ext.form.TextField.superclass.initComponent.call(this);
    this.addEvents('autosize', 'keydown', 'keyup', 'keypress', 'reset');
  },

  /**
   * Resets the current field value to the originally-loaded value and clears
   * any validation messages. Also adds <tt><b>{@link #emptyText}</b></tt>
   * and <tt><b>{@link #emptyClass}</b></tt> if the original value was
   * blank.
   */
  reset : function() {
    Ext.form.TextField.superclass.reset.call(this);
    this.originalValue = this.defaultValue;
    this.setValue(this.defaultValue);
    this.fireEvent('reset');
    this.applyEmptyText();
  },

  setReadOnly : function(readOnly) {
    if (this.rendered) {
      this.el.dom.readOnly = readOnly;
    }
    this.readOnly = readOnly;
    if(this.readOnly) {
      this.focusClass = false;      
      this.getActionEl().addClass(this.disabledClass);
    } else {
      this.focusClass =  'x-form-focus';
      this.getActionEl().removeClass(this.disabledClass);
    }
  }
});