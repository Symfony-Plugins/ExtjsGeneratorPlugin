Ext.override(Ext.form.TriggerField, {
  setReadOnly : function(readOnly) {
    if (readOnly != this.readOnly) {
      this.readOnly = readOnly;
      this.updateEditState();
    }

    if (this.readOnly) {
      this.focusClass = false;
      this.wrapFocusClass = '';
    } else {
      this.focusClass = 'x-form-focus';
      this.wrapFocusClass = 'x-trigger-wrap-focus';
    }
  },

  updateEditState : function() {
    if (this.rendered) {
      if (this.readOnly) {
        this.el.dom.readOnly = true;
        this.el.addClass('x-trigger-noedit');
        this.mun(this.el, 'click', this.onTriggerClick, this);
        this.getActionEl().addClass(this.disabledClass);
      } else {
        if (!this.editable) {
          this.el.dom.readOnly = true;
          this.el.addClass('x-trigger-noedit');
          this.mon(this.el, 'click', this.onTriggerClick, this);
        } else {
          this.el.dom.readOnly = false;
          this.el.removeClass('x-trigger-noedit');
          this.mun(this.el, 'click', this.onTriggerClick, this);
        }
        this.trigger.setDisplayed(!this.hideTrigger);
        this.getActionEl().removeClass(this.disabledClass);
      }
      this.onResize(this.width || this.wrap.getWidth());
    }
  },

  onRender : function(ct, position) {
    this.doc = Ext.isIE ? Ext.getBody() : Ext.getDoc();
    Ext.form.TriggerField.superclass.onRender.call(this, ct, position);

    this.wrap = this.el.wrap({
      cls : 'x-form-field-wrap x-form-field-trigger-wrap'
    });
    this.trigger = this.wrap.createChild(this.triggerConfig || {
      tag : "img",
      src : Ext.BLANK_IMAGE_URL,
      alt : "",
      cls : "x-form-trigger " + this.triggerClass
    });
    this.initTrigger();
    if (!this.width) {
      this.wrap.setWidth(this.el.getWidth() + this.trigger.getWidth());
    }

    this.resizeEl = this.positionEl = this.wrap;

    // if grideditor resize the editor to account for the size of the triggers
    if (this.gridEditor) {
      this.mon(this.gridEditor, 'beforeshow', function() {
        var sz = this.field.wrap.getSize();
        var triggersWidth = ('undefined' != typeof this.field.allowClear && !this.field.allowClear) ? 18 : 36;
        var width = (!this.field.minEditorWidth || sz.width + triggersWidth > this.field.minEditorWidth) ? sz.width
          + triggersWidth : this.field.minEditorWidth;
        this.setSize(width, sz.height);
      });
    }
  }
});