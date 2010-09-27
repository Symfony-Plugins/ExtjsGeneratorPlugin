Ext.ns('Ext.ux.list');
Ext.ux.list.GroupingListView = Ext.extend(Ext.ListView, {

  /**
   * @cfg {Boolean} startCollapsed true to start all groups collapsed (defaults
   *      to false)
   */
  startCollapsed : false,

  /**
   * @cfg {Array} startExpanded An Array of GroupId's that should start
   *      Collapsed
   */
  startExpanded : [],

  groupHeaderTpl : '<tpl if="xindex == 1 || parent.rows[xindex-1].group_index !=  parent.rows[xindex-2].group_index">'
    + '<div class="x-grid-group {[this.getCollapseClass(values)]}" group-index="{group_index}">'
      + '<div class="x-grid-group-hd">'
        + '<div class="x-grid-group-title">'
          + '{group_index} '
        + '</div>'
      + '</div>'
    + '<div class="x-grid-group-body">' +
  '</tpl>',

  groupFooterTpl : '<tpl if="xindex == (xcount) || this.isBigger(xindex, (xcount-1)) || parent.rows[xindex-1].group_index !=  parent.rows[xindex].group_index"> '
    + '</div></div></tpl>',

  groupState : {},

  initComponent : function() {
    var collapsedClass = '';
    if (this.startCollapsed) {
      collapsedClass = ' x-grid-group-collapsed';
    }
    this.tpl = new Ext.XTemplate(
    '<tpl for="rows">',
      // grouping header
      this.groupHeaderTpl,
      // endof grouping header

      // row template
      '<dl class="x-grid3-row {[xindex % 2 === 0 ? "" :  "x-grid3-row-alt"]}">',
        '<tpl for="parent.columns">',
          '<dt style="width:{[values.width*100]}%;text-align:{align};">',
            '<em unselectable="on"<tpl if="cls"> class="{cls}</tpl>">',
              '{[values.tpl.apply(parent)]}',
            '</em>',
          '</dt>',
        '</tpl>',
        '<div class="x-clear"></div>',
      '</dl>',
      // endof row template

      // grouping footer
      this.groupFooterTpl,
      // endof grouping footer

    '</tpl>',
    '<div class="dataview-border"></div>', {
      isBigger : function(isbigger, than) {
        return isbigger > than;
      },
      getCollapseClass : function(values) {
        return values.group_collapsed ? ' x-grid-group-collapsed' : '';
      }
    }
    );
    Ext.ux.list.GroupingListView.superclass.initComponent.apply(this, arguments);
  },

  collectData : function(records, startIndex) {
    var rs = [], groups = {}, groupRs = [], j;
    for (var i = 0, len = records.length; i < len; i++) {
      rs[i] = this.prepareData(records[i].data, startIndex + i, records[i]);

      if (i == 0 || rs[i]['group_index'] != rs[i - 1]['group_index']) {
        if (i > 0) {
          groups[j] = groupRs;
          groupRs = [];
        }
        j = rs[i]['group_index'];
      }
      groupRs.push(rs[i]);
    }

    // add the final array of rs objects
    groups[j] = groupRs;

    Ext.each(rs, function(item, index) {
      item.rs = groups[item.group_index];
    });

    return {
      columns : this.columns,
      rows : rs
    }
  },

  prepareData : function(recData, index, record) {
    var data = {}, groupId = record.data[record.store.groupField];

    groupId = this.groupRenderer ? this.groupRenderer(groupId, index, record) : String(groupId);

    Ext.apply(data, recData);
    data.group_index = groupId;
    if (!this.groupState[groupId]) {
      this.groupState[groupId] = this.startCollapsed && (this.startExpanded.indexOf(groupId) == -1)
        ? false : true;
    }
    data.group_collapsed = !this.groupState[groupId];
    return data;
  },

  onRender : function() {
    Ext.ux.list.GroupingListView.superclass.onRender.apply(this, arguments);
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
    var groupIndex = gel.getAttribute('group-index');
    expanded = Ext.isDefined(expanded) ? expanded : gel.hasClass('x-grid-group-collapsed');
    this.groupState[groupIndex] = expanded;
    gel[expanded ? 'removeClass' : 'addClass']('x-grid-group-collapsed');
  },

  /**
   * Toggles all groups if no value is passed, otherwise sets the expanded state
   * of all groups to the value passed.
   *
   * @param {Boolean}
   *          expanded (optional)
   */
  toggleAllGroups : function(expanded) {
    var groups = this.innerBody.dom.childNodes;
    for (var i = 0, len = groups.length; i < len; i++) {
      this.toggleGroup(groups[i], expanded);
    }
  },

  /**
   * Expands all grouped rows.
   */
  expandAllGroups : function() {
    this.toggleAllGroups(true);
  },

  /**
   * Collapses all grouped rows.
   */
  collapseAllGroups : function() {
    this.toggleAllGroups(false);
  }
});

Ext.reg('groupinglistview', Ext.ux.list.GroupingListView);
