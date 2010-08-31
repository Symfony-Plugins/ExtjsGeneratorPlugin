Ext.ns('Ext.ux');
Ext.ux.GroupingListView = Ext.extend(Ext.ListView, {

  /**
   * @cfg {Boolean} startCollapsed true to start all groups collapsed (defaults
   *      to false)
   */
  startCollapsed : false,

  /**
   * @cfg {Array} startExpanded An Array of GroupeId's that should start
   *      Collapsed
   */
  startExpanded : [],
  
  groupHeaderTpl : '<tpl if="xindex == 1 || parent.rows[xindex-1].group_index !=  parent.rows[xindex-2].group_index">'
    + '<div class="x-grid-group {[this.getCollapseClass(values)]}" groupeindex="{group_index}">'
    + '<div class="x-grid-group-hd"><div class="x-grid-group-title">{group_index} '
    + '</div></div>'
    + '<div class="x-grid-group-body">'
    + '</tpl>',
    
  groupFooterTpl : '<tpl if="xindex == (xcount) || this.isBigger(xindex, (xcount-1)) || parent.rows[xindex-1].group_index !=  parent.rows[xindex].group_index"> '
    +'</div></div></tpl>',

  groupState : {},

  initComponent : function() {
    var collapsedClass = '';
    if (this.startCollapsed) {
      collapsedClass = ' x-grid-group-collapsed';
    }
    this.tpl = new Ext.XTemplate(
    '<tpl for="rows">'
    // grouping header
    + this.groupHeaderTpl,
    // endof grouping header
    
    // row template
    '<dl>',
    '<tpl for="parent.columns">',
    '<dt style="width:{[values.width*100]}%;text-align:{align};">',
    '<em unselectable="on"<tpl if="cls"> class="{cls}</tpl>">',
    '{[values.tpl.apply(parent)]}',
    '</em></dt>',
    '</tpl>',
    '<div class="x-clear"></div>',
    '</dl>'
    // endof row template
    
    // grouping footer
    + this.groupFooterTpl,
    // endof grouping footer

    '</tpl><div class="dataview-border"></div>', {
      isBigger : function(isbigger, than) {
        return isbigger > than;
      },
      getCollapseClass : function(values) {
        return values.group_collapsed ? ' x-grid-group-collapsed' : '';
      }
    }
    );
    Ext.ux.GroupingListView.superclass.initComponent.apply(this, arguments);
  },

  prepareData : function(recData, index, record) {
    var data = {}, groupId = record.data[record.store.groupField];

    Ext.apply(data, recData);
    data.group_index = groupId;
    if (!this.groupState[groupId]) {
      this.groupState[groupId] = this.startCollapsed && (this.startExpanded.indexOf(groupId) == -1)
        ? false : true;
    }
    // console.log(groupId, this.groupState[groupId]);
    data.group_collapsed = !this.groupState[groupId];
    return data;
  },

  onRender : function() {
    Ext.ux.GroupingListView.superclass.onRender.apply(this, arguments);
    this.innerBody.on('mousedown', this.interceptMouse, this);
  },

  interceptMouse : function(e) {
    var hd = e.getTarget('.x-grid-group-hd', this.innerBody);
    if (hd) {
      e.stopEvent();
      this.toggleGroup(hd.parentNode);
    }
  },

  /**
   * Toggles the specified group if no value is passed, otherwise sets the
   * expanded state of the group to the value passed.
   * 
   * @param {String}
   *          groupId The groupId assigned to the group (see getGroupId)
   * @param {Boolean}
   *          expanded (optional)
   */
  toggleGroup : function(group, expanded) {
    var gel = Ext.get(group);
    var groupIndex = gel.getAttribute('groupeindex');
    expanded = Ext.isDefined(expanded) ? expanded : gel.hasClass('x-grid-group-collapsed');
    this.groupState[groupIndex] = expanded;
    gel[expanded ? 'removeClass' : 'addClass']('x-grid-group-collapsed');
  }

}
);

Ext.reg('groupinglistview', Ext.ux.GroupingListView);