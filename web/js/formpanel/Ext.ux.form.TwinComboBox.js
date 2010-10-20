Ext.namespace('Ext.ux.form');
Ext.ux.form.TwinComboBox = Ext.extend(Ext.form.ComboBox, {
  getTrigger : Ext.form.TwinTriggerField.prototype.getTrigger,
  initTrigger : Ext.form.TwinTriggerField.prototype.initTrigger,
  trigger1Class : 'x-form-clear-trigger',
  hideTrigger1 : true,
  submitOnSelect : true,
  submitOnClear : true,
  allowClear : true,
  defaultValue : null,
  triggerAction: 'all',
  plugins : [
    'comboListAutoSizer'
  ],

  initComponent : function() {
    Ext.ux.form.TwinComboBox.superclass.initComponent.call(this);

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
  },

  initEvents : function() {
    Ext.ux.form.TwinComboBox.superclass.initEvents.call(this);

    this.on({
      select : {
        fn : function() {
          if (this.submitOnSelect && this.ownerCt && this.ownerCt.buttons) {
            this.ownerCt.buttons[0].handler.call(this.ownerCt);
          }
        },
        scope : this
      }
    });
  },

  // private
  setValue : Ext.form.ComboBox.prototype.setValue.createSequence(function(v) {
    if (this.allowClear) {
      if (v !== null && v != '') {
        this.triggers[0].show();
      } else {
        this.triggers[0].hide();
      }
    }

    var textWidth = Ext.util.TextMetrics.measure(this.el, this.lastSelectionText).width;
    if (textWidth > (this.el.getWidth() - 20)) {
      this.el.dom.qtip = this.lastSelectionText;
      this.el.dom.qwidth = textWidth + 5;

      if (Ext.QuickTips) {
        Ext.QuickTips.enable();
      }
    }
  }),

  // private
  findRecord : function(prop, value) {
    var record;
    if (this.store.getCount() > 0) {
      this.store.each(function(r) {
        if (r.data[prop] == value) {
          record = r;
          // return false;
        }
      });
    }
    return record;
  },

  reset : Ext.form.Field.prototype.reset.createSequence(function() {
    this.originalValue = this.defaultValue;
    this.setValue(this.defaultValue);
    if (this.allowClear)
      this.triggers[0].hide();
  }),

  onViewClick : Ext.form.ComboBox.prototype.onViewClick.createSequence(function() {
    if (this.allowClear)
      this.triggers[0].show();
  }),

  onTrigger2Click : function() {
    this.onTriggerClick();
  },

  onTrigger1Click : function() {
    if (!this.disabled) {
      this.clearValue();
      this.triggers[0].hide();
      if (this.submitOnClear && this.ownerCt && this.ownerCt.buttons) {
        this.ownerCt.buttons[0].handler.call(this.ownerCt);
      }
      this.el.dom.qtip = null;
      this.fireEvent('clear', this);
    }
  },
  
  // private
  onFocus : Ext.form.ComboBox.prototype.onFocus.createSequence(function() {
    // if grid editor widen the editor to account for the size of the trigger fields
    if (!this.ownerCt) {
      var sz = this.wrap.getSize();
      this.minEditorWidth = (!this.minEditorWidth) ? sz.width + 36 : this.minEditorWidth;
      this.setSize(this.minEditorWidth, sz.height);
    }
  })
});
Ext.ComponentMgr.registerType('twincombo', Ext.ux.form.TwinComboBox);