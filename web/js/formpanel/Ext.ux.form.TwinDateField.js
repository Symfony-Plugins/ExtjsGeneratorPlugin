Ext.namespace('Ext.ux.form');
Ext.ux.form.TwinDateField = Ext.extend(Ext.form.DateField, {
  submitOnSelect : true,
  submitOnClear : true,
  allowClear : true,
  defaultValue : null,
  initComponent : function() {
    Ext.ux.form.TwinDateField.superclass.initComponent.call(this);

    this.triggerConfig = {
      tag : 'span',
      cls : 'x-form-twin-triggers',
      cn : [
      {
        tag : 'img',
        src : Ext.BLANK_IMAGE_URL,
        cls : 'x-form-trigger ' + this.trigger1Class
      }, {
        tag : 'img',
        src : Ext.BLANK_IMAGE_URL,
        cls : 'x-form-trigger ' + this.trigger2Class
      }
      ]
    };
    this.addEvents('valuechange');
  },

  initEvents : function() {
    Ext.ux.form.TwinDateField.superclass.initEvents.call(this);

    this.on({
      'select' : {
        fn : function() {
          if (this.value && this.ownerCt && this.ownerCt.buttons && this.submitOnSelect) {
            this.ownerCt.buttons[0].handler.call(this.ownerCt);
          }
        },
        scope : this
      },
      'valuechange' : {
        fn : function() {

          if (this.getValue() && this.triggers[0]) {
            this.triggers[0].show();
          }
        },
        scope : this
      }
    });
  },

  getTrigger : Ext.form.TwinTriggerField.prototype.getTrigger,
  initTrigger : Ext.form.TwinTriggerField.prototype.initTrigger,
  trigger2Class : 'x-form-date-trigger',
  trigger1Class : 'x-form-clear-trigger',
  hideTrigger1 : true,

  reset : Ext.form.Field.prototype.reset.createSequence(function() {
    this.originalValue = this.defaultValue;
    this.setValue(this.defaultValue);
    if (this.allowClear)
      this.triggers[0].hide();
  }),

  onTrigger2Click : function() {
    this.onTriggerClick();
  },

  onTrigger1Click : function() {
    if (!this.disabled) {
      this.clearValue();
      this.triggers[0].hide();
      if (this.ownerCt && this.ownerCt.buttons && this.submitOnClear) {
        this.ownerCt.buttons[0].handler.call(this.ownerCt);
      }
      this.fireEvent('clear', this);
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
  },

  setValue : Ext.form.DateField.prototype.setValue.createSequence(function(v) {
    if (this.allowClear) {
      if (v !== null && v != '') {
        this.triggers[0].show();
      } else {
        this.triggers[0].hide();
      }
    }
  })

});
Ext.ComponentMgr.registerType('twindatefield', Ext.ux.form.TwinDateField);