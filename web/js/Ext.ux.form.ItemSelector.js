/*
 * ! Ext JS Library 3.2.0 Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com http://www.extjs.com/license
 */
/*
 * Note that this control will most likely remain as an example, and not as a
 * core Ext form control. However, the API will be changing in a future release
 * and so should not yet be treated as a final, stable API at this time.
 */

/**
 * @class Ext.ux.form.ItemSelector
 * @extends Ext.form.Field A control that allows selection of between two
 *          Ext.ux.form.MultiSelect controls.
 * 
 * @history 2008-06-19 bpm Original code contributed by Toby Stuart (with
 *          contributions from Robert Williams)
 * 
 * @constructor Create a new ItemSelector
 * @param {Object}
 *          config Configuration options
 * @xtype itemselector
 */
Ext.ux.form.ItemSelector = Ext.extend(Ext.form.Field, {
  hideNavIcons : false,
  imagePath : '/ExtjsGeneratorPlugin/Ext.ux.IconMgr/icons',
  iconUp : 'control-090.png',
  iconDown : 'control-270.png',
  iconLeft : 'control-180.png',
  iconRight : 'control.png',
  iconTop : 'control-stop-090.png',
  iconBottom : 'control-stop-270.png',
  drawUpIcon : false,
  drawDownIcon : false,
  drawLeftIcon : true,
  drawRightIcon : true,
  drawTopIcon : false,
  drawBotIcon : false,
  delimiter : ',',
  bodyStyle : null,
  border : false,
  defaultAutoCreate : {
    tag : "div"
  },
  /**
   * @cfg {String} delimiter The string used to delimit between items when set
   *      or returned as a string of values (defaults to ',').
   */
  delimiter : ',',

  /**
   * @cfg {Array} multiselects An array of {@link Ext.ux.form.MultiSelect}
   *      config objects, with at least all required parameters (e.g., store)
   */
  multiselects : null,

  initComponent : function() {
    Ext.ux.form.ItemSelector.superclass.initComponent.call(this);
    this.addEvents({
      'rowdblclick' : true,
      'change' : true
    });
  },

  onRender : function(ct, position) {
    Ext.ux.form.ItemSelector.superclass.onRender.call(this, ct, position);

    // Internal default configuration for both multiselects
    var msConfig = [
    {
      legend : 'Available',
      draggable : true,
      droppable : true,
      width : 200,
      height : 200
    }, {
      legend : 'Selected',
      droppable : true,
      draggable : true,
      width : 200,
      height : 200
    }
    ];

    this.fromMultiselect = new Ext.ux.form.MultiSelect(Ext.applyIf(this.multiselects[0], msConfig[0]));
    this.fromMultiselect.on('dblclick', this.onRowDblClick, this);

    this.toMultiselect = new Ext.ux.form.MultiSelect(Ext.applyIf(this.multiselects[1], msConfig[1]));
    this.toMultiselect.on('dblclick', this.onRowDblClick, this);

    var p = new Ext.Panel({
      bodyStyle : this.bodyStyle,
      border : this.border,
      layout : "table",
      width : this.fromMultiselect.width + this.toMultiselect.width + 20,
      layoutConfig : {
        columns : 3
      }
    });

    p.add(this.fromMultiselect);
    var icons = new Ext.Panel({
      header : false
    });
    p.add(icons);
    p.add(this.toMultiselect);
    p.render(this.el);
    icons.el.down('.' + icons.bwrapCls).remove();

    // ICON HELL!!!
    if (this.imagePath != "" && this.imagePath.charAt(this.imagePath.length - 1) != "/")
      this.imagePath += "/";
    this.iconUp = this.imagePath + (this.iconUp || 'control-090.png');
    this.iconDown = this.imagePath + (this.iconDown || 'control-270.png');
    this.iconLeft = this.imagePath + (this.iconLeft || 'control-180.png');
    this.iconRight = this.imagePath + (this.iconRight || 'control.png');
    this.iconTop = this.imagePath + (this.iconTop || 'control-stop-090.png');
    this.iconBottom = this.imagePath + (this.iconBottom || 'control-stop-270.png');
    var el = icons.getEl();
    this.toTopIcon = el.createChild({
      tag : 'img',
      src : this.iconTop,
      style : {
        cursor : 'pointer',
        margin : '2px'
      }
    });
    el.createChild({
      tag : 'br'
    });
    this.upIcon = el.createChild({
      tag : 'img',
      src : this.iconUp,
      style : {
        cursor : 'pointer',
        margin : '2px'
      }
    });
    el.createChild({
      tag : 'br'
    });
    this.addIcon = el.createChild({
      tag : 'img',
      src : this.iconRight,
      style : {
        cursor : 'pointer',
        margin : '2px'
      }
    });
    el.createChild({
      tag : 'br'
    });
    this.removeIcon = el.createChild({
      tag : 'img',
      src : this.iconLeft,
      style : {
        cursor : 'pointer',
        margin : '2px'
      }
    });
    el.createChild({
      tag : 'br'
    });
    this.downIcon = el.createChild({
      tag : 'img',
      src : this.iconDown,
      style : {
        cursor : 'pointer',
        margin : '2px'
      }
    });
    el.createChild({
      tag : 'br'
    });
    this.toBottomIcon = el.createChild({
      tag : 'img',
      src : this.iconBottom,
      style : {
        cursor : 'pointer',
        margin : '2px'
      }
    });
    this.toTopIcon.on('click', this.toTop, this);
    this.upIcon.on('click', this.up, this);
    this.downIcon.on('click', this.down, this);
    this.toBottomIcon.on('click', this.toBottom, this);
    this.addIcon.on('click', this.fromTo, this);
    this.removeIcon.on('click', this.toFrom, this);
    if (!this.drawUpIcon || this.hideNavIcons) {
      this.upIcon.dom.style.display = 'none';
    }
    if (!this.drawDownIcon || this.hideNavIcons) {
      this.downIcon.dom.style.display = 'none';
    }
    if (!this.drawLeftIcon || this.hideNavIcons) {
      this.addIcon.dom.style.display = 'none';
    }
    if (!this.drawRightIcon || this.hideNavIcons) {
      this.removeIcon.dom.style.display = 'none';
    }
    if (!this.drawTopIcon || this.hideNavIcons) {
      this.toTopIcon.dom.style.display = 'none';
    }
    if (!this.drawBotIcon || this.hideNavIcons) {
      this.toBottomIcon.dom.style.display = 'none';
    }

    var tb = p.body.first();
    this.el.setWidth(p.body.first().getWidth());
    p.body.removeClass();

    this.hiddenName = this.name;
    var hiddenTag = {
      tag : "input",
      type : "hidden",
      value : "",
      name : this.name
    };
    this.hiddenField = this.el.createChild(hiddenTag);
  },

  doLayout : function() {
    if (this.rendered) {
      this.fromMultiselect.fs.doLayout();
      this.toMultiselect.fs.doLayout();
    }
  },

  afterRender : function() {
    Ext.ux.form.ItemSelector.superclass.afterRender.call(this);

    this.toStore = this.toMultiselect.store;
    this.toStore.on('add', this.valueChanged, this);
    this.toStore.on('remove', this.valueChanged, this);
    this.toStore.on('load', this.valueChanged, this);
    this.valueChanged(this.toStore);
  },

  toTop : function() {
    var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
    var records = [];
    if (selectionsArray.length > 0) {
      selectionsArray.sort();
      for (var i = 0; i < selectionsArray.length; i++) {
        record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
        records.push(record);
      }
      selectionsArray = [];
      for (var i = records.length - 1; i > -1; i--) {
        record = records[i];
        this.toMultiselect.view.store.remove(record);
        this.toMultiselect.view.store.insert(0, record);
        selectionsArray.push(((records.length - 1) - i));
      }
    }
    this.toMultiselect.view.refresh();
    this.toMultiselect.view.select(selectionsArray);
  },

  toBottom : function() {
    var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
    var records = [];
    if (selectionsArray.length > 0) {
      selectionsArray.sort();
      for (var i = 0; i < selectionsArray.length; i++) {
        record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
        records.push(record);
      }
      selectionsArray = [];
      for (var i = 0; i < records.length; i++) {
        record = records[i];
        this.toMultiselect.view.store.remove(record);
        this.toMultiselect.view.store.add(record);
        selectionsArray.push((this.toMultiselect.view.store.getCount()) - (records.length - i));
      }
    }
    this.toMultiselect.view.refresh();
    this.toMultiselect.view.select(selectionsArray);
  },

  up : function() {
    var record = null;
    var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
    selectionsArray.sort();
    var newSelectionsArray = [];
    if (selectionsArray.length > 0) {
      for (var i = 0; i < selectionsArray.length; i++) {
        record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
        if ((selectionsArray[i] - 1) >= 0) {
          this.toMultiselect.view.store.remove(record);
          this.toMultiselect.view.store.insert(selectionsArray[i] - 1, record);
          newSelectionsArray.push(selectionsArray[i] - 1);
        }
      }
      this.toMultiselect.view.refresh();
      this.toMultiselect.view.select(newSelectionsArray);
    }
  },

  down : function() {
    var record = null;
    var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
    selectionsArray.sort();
    selectionsArray.reverse();
    var newSelectionsArray = [];
    if (selectionsArray.length > 0) {
      for (var i = 0; i < selectionsArray.length; i++) {
        record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
        if ((selectionsArray[i] + 1) < this.toMultiselect.view.store.getCount()) {
          this.toMultiselect.view.store.remove(record);
          this.toMultiselect.view.store.insert(selectionsArray[i] + 1, record);
          newSelectionsArray.push(selectionsArray[i] + 1);
        }
      }
      this.toMultiselect.view.refresh();
      this.toMultiselect.view.select(newSelectionsArray);
    }
  },

  fromTo : function() {
    var selectionsArray = this.fromMultiselect.view.getSelectedIndexes();
    var records = [];
    if (selectionsArray.length > 0) {
      for (var i = 0; i < selectionsArray.length; i++) {
        record = this.fromMultiselect.view.store.getAt(selectionsArray[i]);
        records.push(record);
      }
      if (!this.allowDup)
        selectionsArray = [];
      for (var i = 0; i < records.length; i++) {
        record = records[i];
        if (this.allowDup) {
          var x = new Ext.data.Record();
          record.id = x.id;
          delete x;
          this.toMultiselect.view.store.add(record);
        } else {
          this.fromMultiselect.view.store.remove(record);
          this.toMultiselect.view.store.add(record);
          selectionsArray.push((this.toMultiselect.view.store.getCount() - 1));
        }
      }
    }
    this.toMultiselect.view.refresh();
    this.fromMultiselect.view.refresh();
    var si = this.toMultiselect.store.sortInfo;
    if (si) {
      this.toMultiselect.store.sort(si.field, si.direction);
    }
    this.toMultiselect.view.select(selectionsArray);
  },

  toFrom : function() {
    var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
    var records = [];
    if (selectionsArray.length > 0) {
      for (var i = 0; i < selectionsArray.length; i++) {
        record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
        records.push(record);
      }
      selectionsArray = [];
      for (var i = 0; i < records.length; i++) {
        record = records[i];
        this.toMultiselect.view.store.remove(record);
        if (!this.allowDup) {
          this.fromMultiselect.view.store.add(record);
          selectionsArray.push((this.fromMultiselect.view.store.getCount() - 1));
        }
      }
    }
    this.fromMultiselect.view.refresh();
    this.toMultiselect.view.refresh();
    var si = this.fromMultiselect.store.sortInfo;
    if (si) {
      this.fromMultiselect.store.sort(si.field, si.direction);
    }
    this.fromMultiselect.view.select(selectionsArray);
  },

  valueChanged : function(store) {
    var record = null;
    var values = [];
    for (var i = 0; i < store.getCount(); i++) {
      record = store.getAt(i);
      values.push(record.get(this.toMultiselect.valueField));
    }
    this.hiddenField.dom.value = values.join(this.delimiter);
    this.fireEvent('change', this, this.getValue(), this.hiddenField.dom.value);
  },

  getValue : function() {
    return this.hiddenField.dom.value;
  },

  setValue : function(values) {
    this.reset();
    if (!values)
      return;

    var records = [];

    if (!Ext.isArray(values)) {
      values = values.split(this.delimiter);
    }

    if (values.length > 0) {
      for (var i = 0; i < values.length; i++) {
        var index = this.fromMultiselect.view.store.find(this.fromMultiselect.valueField, values[i]);
        var record = this.fromMultiselect.view.store.getAt(index);
        if ('object' == typeof record)
          records.push(record);
      }
      if (!this.allowDup)
        values = [];
      for (var i = 0; i < records.length; i++) {
        record = records[i];
        if (this.allowDup) {
          var x = new Ext.data.Record();
          record.id = x.id;
          delete x;
          this.toMultiselect.view.store.add(record);
        } else {
          this.fromMultiselect.view.store.remove(record);
          this.toMultiselect.view.store.add(record);
          values.push((this.toMultiselect.view.store.getCount() - 1));
        }
      }
    }
    this.toMultiselect.view.refresh();
    this.fromMultiselect.view.refresh();
    var si = this.toMultiselect.store.sortInfo;
    if (si) {
      this.toMultiselect.store.sort(si.field, si.direction);
    }
    this.toMultiselect.view.select(values);

  },

  setValue : function(val) {
    this.reset();
    if (!val)
      return;
      
    if (!Ext.isArray(val)) {
      val = val.split(this.delimiter);
    }
    var vf, df, rec, i, id, idx;
    for (i = 0; i < val.length; i++) {
      vf = this.fromMultiselect.valueField;
      df = this.fromMultiselect.displayField;
      id = val[i];
      idx = this.toMultiselect.view.store.findBy(function(record) {
        return record.data[vf] == id;
      });
      if (idx != -1)
        continue;
      idx = this.fromMultiselect.view.store.findBy(function(record) {
        return record.data[vf] == id;
      });
      rec = this.fromMultiselect.view.store.getAt(idx);
      if (rec) {
        this.toMultiselect.view.store.add(rec);
        this.fromMultiselect.view.store.remove(rec);
      }
    }
  },

  onRowDblClick : function(vw, index, node, e) {
    if (vw == this.toMultiselect.view) {
      this.toFrom();
    } else if (vw == this.fromMultiselect.view) {
      this.fromTo();
    }
    return this.fireEvent('rowdblclick', vw, index, node, e);
  },

  reset : function() {
    range = this.toMultiselect.store.getRange();
    this.toMultiselect.store.removeAll();
    this.fromMultiselect.store.add(range);
    var si = this.fromMultiselect.store.sortInfo;
    if (si) {
      this.fromMultiselect.store.sort(si.field, si.direction);
    }
    this.valueChanged(this.toMultiselect.store);
  }
});

Ext.reg('itemselector', Ext.ux.form.ItemSelector);
