Ext.namespace('Ext.ux.form');
Ext.ux.form.TwinComboBox = Ext.extend(Ext.form.ComboBox, {
  getTrigger : Ext.form.TwinTriggerField.prototype.getTrigger,
  initTrigger : Ext.form.TwinTriggerField.prototype.initTrigger,
  trigger1Class : 'x-form-clear-trigger',
  trigger2Class : '',
  hideTrigger1 : true,
  submitOnSelect : true,
  submitOnClear : true,
  allowClear : true,
  defaultValue : null,
  triggerAction : 'all',
  plugins : ['comboListAutoSizer'],

  initComponent : function() {
    Ext.ux.form.TwinComboBox.superclass.initComponent.call(this);

    this.triggerConfig = {
      tag : 'span',
      cls : 'x-form-twin-triggers',
      cn : [{
        tag : 'img',
        src : Ext.BLANK_IMAGE_URL,
        cls : 'x-form-trigger ' + this.trigger1Class
      }, {
        tag : 'img',
        src : Ext.BLANK_IMAGE_URL,
        cls : 'x-form-trigger ' + this.trigger2Class
      }]
    };
  },

  onSelect : Ext.form.ComboBox.prototype.onSelect.createSequence(function(v) {
    if (this.value && this.ownerCt && this.ownerCt.buttons && this.submitOnSelect) {
      this.ownerCt.buttons[0].handler.call(this.ownerCt);
    }
  }),

  onRender : Ext.form.ComboBox.prototype.onRender.createSequence(function(v) {
    this.getTrigger(0).hide();
  }),

  setValue : Ext.form.ComboBox.prototype.setValue.createSequence(function(v) {
    if (v !== null && v != '') {
      if (this.allowClear)
        this.getTrigger(0).show();
    } else {
      this.getTrigger(0).hide();
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

  reset : Ext.form.ComboBox.prototype.reset.createSequence(function() {
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
      if (this.submitOnClear && this.ownerCt && this.ownerCt.buttons) {
        this.ownerCt.buttons[0].handler.call(this.ownerCt);
      }
      this.el.dom.qtip = null;
      this.fireEvent('clear', this);
      this.onFocus();
    }
  },

  applyState : function(state) {
    this.lastSelectionText = state.lastSelectionText ? state.lastSelectionText : this.defaultText;
    var selectedIndex = state.selectedIndex ? state.selectedIndex : this.defaultIndex;
    this.addRecordToStore(this.lastSelectionText, selectedIndex);
  },

  getState : function() {
    return {
      selectedIndex : this.getValue(),
      lastSelectionText : this.lastSelectionText
    };
  },

  // private
  addRecordToStore : function(display, value) {
    if ((this.store !== null) && (value != "")) {
      // add preloaded value to the store
      var o = new Array();
      o.data = new Array();
      o.data[0] = new Array();
      o.data[0][this.valueField] = value;
      o.data[0][this.displayField] = display;
      this.store.loadData(o, true);
    }
  },

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
  }

  // onViewClick :
  // Ext.form.ComboBox.prototype.onViewClick.createSequence(function() {
  // if (this.allowClear)
  // this.getTrigger(0).show();
  // }),
}
);
Ext.ComponentMgr.registerType('twincombo', Ext.ux.form.TwinComboBox);
