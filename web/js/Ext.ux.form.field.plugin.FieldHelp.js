Ext.namespace('Ext.ux.form.field.plugin');
Ext.ux.form.field.plugin.FieldHelp = Ext.extend(Object, (function() {
  function syncInputSize(w, h) {
    this.el.setSize(w, h);
  }

  function afterFieldRender() {

    var style = 'background-color:transparent; background-repeat:no-repeat;';
    // radio
    if (this.inputType == 'radio') {
      style += 'font-size:13px; padding-left:11px; display:inline; position:relative;';
      style += (this.boxLabel != '&#160;') ? 'left:4px;' : 'left:-4px;';
      style += Ext.isIE ? 'top:1px;' : 'top:2px;';

      if (!this.entendedHelpText)
        this.extendedHelpText = this.helpText;
      this.helpText = '&nbsp;';
    }
    // checkbox
    else if ('undefined' != typeof this.checked) {
      style += 'font-size:13px; padding-left:11px; display:inline; position:relative;';
      style += (this.boxLabel != '&#160;') ? 'left:4px;' : 'left:-4px;';
      style += Ext.isIE ? 'top:1px;' : 'top:2px;';

      if (!this.entendedHelpText)
        this.extendedHelpText = this.helpText;
      this.helpText = '&nbsp;';
    }
    // everything else
    else {
      style += 'color:#888; font-size:10px; line-height:16px; padding-left:18px;'
    }

    if (!this.wrap) {
      this.wrap = this.el.wrap({
        cls : 'x-form-field-wrap'
      });
      this.positionEl = this.resizeEl = this.wrap;
      this.actionMode = 'wrap';
      this.onResize = this.onResize.createSequence(syncInputSize);
    }

    var helpEl = this.wrap[this.helpAlign == 'top' ? 'insertFirst' : 'createChild']({
      cls : Ext.ux.IconMgr.getIcon('help'),
      style : style,
      html : this.helpText
    });

    if (this.extendedHelpText) {
      Ext.QuickTips.register({
        target : helpEl,
        text : this.extendedHelpText,
        enabled : true
      });
      helpEl.applyStyles({cursor:'help'});
    }
  }

  return {
    constructor : function(t, align) {
      this.helpText = t;
      this.align = align;
    },

    init : function(f) {
      f.helpAlign = this.align;
      if ('object' != typeof this.helpText)
        f.helpText = this.helpText;
      f.afterRender = f.afterRender.createSequence(afterFieldRender);
    }
  };
})());

Ext.preg('fieldHelp', Ext.ux.form.field.plugin.FieldHelp);
