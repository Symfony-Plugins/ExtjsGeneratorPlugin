Ext.override(Ext.data.HttpProxy, {
  /**
   * Used for overriding the post method used for a single request. Designed to
   * be called during a beforeaction event. Calling setMethod will override any
   * method set via the api configuration parameter. Set the optional parameter
   * makePermanent to set the method for all subsequent requests. If not set to
   * makePermanent, the next request will use the same method or api
   * configuration defined in the initial proxy configuration.
   * 
   * @param {String}
   *          method
   * @param {Boolean}
   *          makePermanent (Optional) [false]
   * 
   * (e.g.: beforeload, beforesave, etc).
   */
  setMethod : function(method, makePermanent) {
    var confMethod = this.conn.method;
    this.conn.method = method;
    if (!makePermanent) {
      this.on('load', function(){this.conn.method = confMethod;}, this, {single:true});
    }
  }
});