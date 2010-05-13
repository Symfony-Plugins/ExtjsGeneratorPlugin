Ext.override(Ext.form.TextField, {
  initComponent : function() {
    Ext.form.TextField.superclass.initComponent.call(this);
    this.addEvents(
      /**
       * @event autosize Fires when the <tt><b>{@link #autoSize}</b></tt>
       *        function is triggered. The field may or may not have actually
       *        changed size according to the default logic, but this event
       *        provides a hook for the developer to apply additional logic at
       *        runtime to resize the field if needed.
       * @param {Ext.form.Field}
       *          this This text field
       * @param {Number}
       *          width The new field width
       */
      'autosize',
      /**
       * @event keydown Keydown input field event. This event only fires if
       *        <tt><b>{@link #enableKeyEvents}</b></tt> is set to true.
       * @param {Ext.form.TextField}
       *          this This text field
       * @param {Ext.EventObject}
       *          e
       */
      'keydown',
      /**
       * @event keyup Keyup input field event. This event only fires if
       *        <tt><b>{@link #enableKeyEvents}</b></tt> is set to true.
       * @param {Ext.form.TextField}
       *          this This text field
       * @param {Ext.EventObject}
       *          e
       */
      'keyup',
      /**
       * @event keypress Keypress input field event. This event only fires if
       *        <tt><b>{@link #enableKeyEvents}</b></tt> is set to true.
       * @param {Ext.form.TextField}
       *          this This text field
       * @param {Ext.EventObject}
       *          e
       */
      'keypress',
      /**
       * @event reset Input field reset event. 
       */
      'reset'
    );
  },

  /**
   * Resets the current field value to the originally-loaded value and clears
   * any validation messages. Also adds <tt><b>{@link #emptyText}</b></tt>
   * and <tt><b>{@link #emptyClass}</b></tt> if the original value was
   * blank.
   */
  reset : function() {
    Ext.form.TextField.superclass.reset.call(this);
    this.fireEvent('reset');
    this.applyEmptyText();
  }
});