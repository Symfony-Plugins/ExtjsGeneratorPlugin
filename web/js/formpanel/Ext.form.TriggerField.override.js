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

  // private
  onFocus : function() {
    Ext.form.TriggerField.superclass.onFocus.call(this);
    if (!this.mimicing) {
      this.wrap.addClass(this.wrapFocusClass);
      this.mimicing = true;
      this.doc.on('mousedown', this.mimicBlur, this, {
        delay : 10
      });
      if (this.monitorTab) {
        this.on('specialkey', this.checkTab, this);
      }
    }
    // if grid editor widen the editor to account for the size of the
    // trigger
    // fields
    if (!this.ownerCt) {
      var sz = this.wrap.getSize();
      this.minEditorWidth = (!this.minEditorWidth) ? sz.width + 36 : this.minEditorWidth;
      this.setSize(this.minEditorWidth, sz.height);
    }
  }

});