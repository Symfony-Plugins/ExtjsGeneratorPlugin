Ext.namespace('Ext.ux.form');
Ext.ux.form.IsEmptyCheckbox = Ext.extend(Ext.form.Checkbox, {
  inputValue : 'true',
  hideLabel : true,

  // private
  onRender : function(ct, position) {
    Ext.form.Checkbox.superclass.onRender.call(this, ct, position);
    if (this.inputValue !== undefined) {
      this.el.dom.value = this.inputValue;
    }
    this.wrap = this.el.wrap({
      cls : 'x-form-check-wrap'
    }).setStyle({
      paddingLeft : '5px',
      marginTop : '-10px'
    });
    if (this.boxLabel) {
      this.wrap.createChild({
        tag : 'label',
        htmlFor : this.el.id,
        cls : 'x-form-cb-label',
        style: 'color: #808080;',
        html : this.boxLabel
      });
    }
    if (this.checked) {
      this.setValue(true);
    } else {
      this.checked = this.el.dom.checked;
    }
    // Need to repaint for IE, otherwise positioning is broken
    if (Ext.isIE) {
      this.wrap.repaint();
    }
    this.resizeEl = this.positionEl = this.wrap;

    this.sibling = this.previousSibling();
  },

  // private
  onClick : function() {
    if (this.el.dom.checked != this.checked) {
      this.setValue(this.el.dom.checked);

      if (this.checked && this.sibling) {
        this.sibling.reset();
        if (this.sibling.hasListener('focus')) {
          this.sibling.removeListener('focus', this.reset, this);
        }
        this.sibling.on('focus', this.reset, this, {
          single : true
        });

      }

      if (this.ownerCt && this.ownerCt.buttons) {
        this.ownerCt.buttons[0].handler.call(this.ownerCt);
      }
    }
  },

  reset : Ext.form.Field.prototype.reset.createSequence(function() {
    this.originalValue = this.defaultValue;
    this.setValue(this.defaultValue);
  })

});
Ext.ComponentMgr.registerType('isemptycheckbox', Ext.ux.form.IsEmptyCheckbox);