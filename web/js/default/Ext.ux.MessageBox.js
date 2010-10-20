Ext.ux.MessageBox = function() {
  f = function() {}
  f.prototype = Ext.MessageBox;
  var o = function() {}
  Ext.extend(o, f, function() {
    return {

      /**
       * Displays a standard read-only message box with no buttons and the
       * Ext.MessageBox.INFO icon that automatically closes after 2200ms. If a
       * callback function is passed it will be called after the message box is
       * closed.
       * 
       * @param {String}
       *          title The title bar text
       * @param {String}
       *          msg The message box body text
       * @param {Function}
       *          fn (optional) The callback function invoked after the message
       *          box is closed
       * @param {Object}
       *          scope (optional) The scope (<code>this</code> reference) in
       *          which the callback is executed. Defaults to the browser
       *          wnidow.
       * @return {Ext.MessageBox} this
       */
      info : function(title, msg, fn, scope) {
        this.show({
          title : title,
          msg : msg,
          minWidth : this.minWidth,
          icon : Ext.MessageBox.INFO
        });
        setTimeout(this.getDialog().close.createDelegate(this), 2200);
        return this;
      },

      /**
       * Displays a standard read-only message box with no buttons and the
       * Ext.MessageBox.ERROR icon that automatically closes after 2200ms. If a
       * callback function is passed it will be called after the message box is
       * closed.
       * 
       * @param {String}
       *          title The title bar text
       * @param {String}
       *          msg The message box body text
       * @param {Function}
       *          fn (optional) The callback function invoked after the message
       *          box is closed
       * @param {Object}
       *          scope (optional) The scope (<code>this</code> reference) in
       *          which the callback is executed. Defaults to the browser
       *          wnidow.
       * @return {Ext.MessageBox} this
       */
      error : function(title, msg, fn, scope) {
        this.show({
          title : title,
          msg : msg,
          fn : fn,
          scope : scope,
          minWidth : this.minWidth,
          icon : Ext.MessageBox.ERROR
        });
        setTimeout(this.getDialog().close.createDelegate(this), 2200);
        return this;
      }
    };
  }());
  return new o();
}();