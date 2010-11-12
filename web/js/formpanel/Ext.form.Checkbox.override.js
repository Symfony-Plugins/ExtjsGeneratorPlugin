// This override prevents checkboxes from being VISIBLY checked/unchecked if
// they are set to readonly
Ext.override(Ext.form.Checkbox, {
  markEl : 'wrap',
  mustCheckText : 'This field is required',
  alignErrorIcon : function() {
    this.errorIcon.alignTo(this.wrap, 'tl-tr', [2, 0]);
  },
  markInvalid : Ext.form.Checkbox.superclass.markInvalid,
  clearInvalid : Ext.form.Checkbox.superclass.clearInvalid,
  validateValue : function(value) {
    if (this.mustCheck && !value) {
      this.markInvalid(this.mustCheckText);
      return false;
    }
    if (this.vtype) {
      var vt = Ext.form.VTypes;
      if (!vt[this.vtype](value, this)) {
        this.markInvalid(this.vtypeText || vt[this.vtype + 'Text']);
        return false;
      }
    }
    if (typeof this.validator == "function") {
      var msg = this.validator(value);
      if (msg !== true) {
        this.markInvalid(msg);
        return false;
      }
    }
    if (this.regex && !this.regex.test(value)) {
      this.markInvalid(this.regexText);
      return false;
    }
    return true;
  },
  
  onClick : function(e, o) {
    if (this.readOnly === true) {
      e.preventDefault();
    } else {
      if (this.el.dom.checked != this.checked) {
        this.setValue(this.el.dom.checked);
      }
    }
  }
});