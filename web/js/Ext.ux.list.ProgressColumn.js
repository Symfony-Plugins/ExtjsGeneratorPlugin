/**
 * @class Ext.ux.list.ProgressColumn
 * @extends Ext.list.Column
 * 
 * @author Benjamin Runnels
 * @date 1 September 2010
 * @version 0.1
 * 
 * @license Ext.ux.list.ProgressColumn is licensed under the terms of the Open
 *          Source LGPL 3.0 license. Commercial use is permitted to the extent
 *          that the code/component(s) do NOT become part of another Open Source
 *          or Commercially licensed development library or toolkit without
 *          explicit permission.
 * 
 * <p>
 * License details: <a href="http://www.gnu.org/licenses/lgpl.html"
 * target="_blank">http://www.gnu.org/licenses/lgpl.html</a>
 * </p>
 */

Ext.ns('Ext.ux.list');
Ext.ux.list.ProgressColumn = Ext.extend(Ext.list.Column, {
  /**
   * @cfg {String} Text to display above the progress bar (defaults to null)
   */ 
  topText: null,
  
  /**
   * @cfg {String} Text to display below the progress bar (defaults to null)
   */
  bottomText: null,
  
  /**
   * @cfg {Integer} upper limit for full progress indicator (defaults to 100)
   */
  ceiling : 100,
  
  /**
   * @cfg {Boolean} colored determines whether use special progression coloring
   *      or the standard Ext.ProgressBar coloring for the bar (defaults to true)
   */
  colored : true,
  
  /**
   * @cfg {Boolean} inverts the colors when colored is used.  Normally the progression
   *      is red, orange, green.  This switches it to green, orange, red. (defaults to false)
   */
  invertedColor : false,
  
  /**
   * @cfg {String} symbol appended after the numeric value (defaults to %)
   */
  textPst : '%',
  
  /**
   * @cfg {String} the class to use for this column (defaults to x-list-progresscol)
   */
  cls: 'x-list-progresscol',

  constructor : function(c) {
    c.tpl = c.tpl || new Ext.XTemplate('{[this.format(values, "' + c.dataIndex + '")]}');

    c.tpl.column = this;

    c.tpl.format = function(values, index) {
      return this.column.getColumnMarkup(values, index);
    };
     
    Ext.ux.list.ProgressColumn.superclass.constructor.call(this, c);
  },
  
  getStyle: function(values, index, v)
  {
    var style = '';
    if (this.colored == true) {
      if(this.invertedColor == true) {
        if (v > (this.ceiling * 0.66)) style = '-red';
        if (v < (this.ceiling * 0.67) && v > (this.ceiling * 0.33)) style = '-orange';
        if (v < (this.ceiling * 0.34)) style = '-green';
      } else {
        if (v <= this.ceiling && v > (this.ceiling * 0.66)) style = '-green';
        if (v < (this.ceiling * 0.67) && v > (this.ceiling * 0.33)) style = '-orange';
        if (v < (this.ceiling * 0.34)) style = '-red';
      }
    }
    return style;
  },
  
  getTopText: function(values, index, v) {
    if(this.topText) {
      return String.format('<div class="x-progress-toptext">{0}</div>', this.topText);
    }
    return '';
  },
  
  getBottomText: function(values, index, v) {
    if(this.bottomText) {
      return String.format('<div class="x-progress-bottomtext">{0}</div>', this.bottomText);
    }
    return '';
  }, 
  
  // ugly hack to get IE looking the same as FF
  getText: function(values, index, v) {
    var textClass = (v < (this.ceiling / 1.818)) ? 'x-progress-text-back' : 'x-progress-text-front' + (Ext.isIE ? '-ie' : '');    
    var text = String.format('</div><div class="x-progress-text {0}">{1}</div></div>',
      textClass, 
      v + this.textPst
    );       
    return (v < (this.ceiling / 1.031)) ? text.substring(0, text.length - 6) : text.substr(6);    
  }, 
  
  getWrapperClass: function(values, index, v)
  {
    return 'x-list-progresscol-wrapper';
  },

  getColumnMarkup: function(values, index) {
    //we get all the values for this row so extra things can be done by overriding any of the methods
    var v = values[index];
    // the empty comment makes IE collapse empty divs
    return String.format(
      '<em class="{0}">{1}<div class="x-progress-wrap' + (Ext.isIE ? ' x-progress-wrap-ie">' : '">') +
        '<!-- --><div class="x-progress-inner">' +
          '<div class="x-progress-bar x-progress-bar{2}" style="width:{3}%;">{4}' +
        '</div>' +
      '</div>{5}</em>',
      this.getWrapperClass(values, index, v),
      this.getTopText(values, index, v),
      this.getStyle(values, index, v), 
      (v / this.ceiling) * 100, 
      this.getText(values, index, v),
      this.getBottomText(values, index, v)      
    );
  }
});

Ext.reg('lvprogresscolumn', Ext.ux.list.ProgressColumn);
