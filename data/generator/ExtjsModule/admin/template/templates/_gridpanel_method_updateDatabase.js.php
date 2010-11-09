<?php
  $pkn = $this->getPrimaryKeys(true);
?>
[?php
// updateDatabase
$configArr = array(
  'parameters' => 'e',
  'source' => "
    var params = {
      cmd: 'from_request',
      action: 'update',
      sf_method: 'put',
      sf_format: 'json'
    };
   
    // if the editor config has a name set field to the editor name value
    var editor = e.grid.getColumnModel().getCellEditor(e.column);
    if( editor && 'undefined' != typeof editor.field.initialConfig.name) {
        e.field = editor.field.initialConfig.name;
    } 
      
    // can't post a date object so format it as a string the database understands
    if(e.value == 'date') {
      e.value = e.value.dateFormat('m/d/Y');
    }
 
    params['<?php echo sfInflector::underscore($this->getModelClass()) ?>[' + e.field +']'] = e.value;

    Ext.Ajax.request({
      url:'" . url_for('@<?php echo $this->params['route_prefix'] ?>') . "/' + e.record.get('<?php echo sfInflector::underscore($this->getPrimaryKeys(true)) ?>'),
      method: 'POST',
      params: params,
      success: function(result, request) {
        var result = Ext.decode(result.responseText);
        
        //we will always get into success even if result.success: false
        if(result.success) {
          // marks dirty records as committed (no red triangle)
          e.grid.getStore().commitChanges();
        } else {
          var message = '';
          Ext.each(result.errors, function(error){
            message += error.error + ': ' + error.message + '<br />';
          });
          Ext.Msg.alert('Error', message);
          //reject the changes as they should have not been committed to the database if success:false is being sent
          e.grid.getStore().rejectChanges();
        }

      },
      failure: function(form, action) {
        //we only get here if there was a communications error and the server sent no response
        Ext.Msg.alert('".__('Saving data')."', 'There was a problem communicating with the server, your modification could not be saved!');
        e.grid.getStore().rejectChanges();
      }
    });
  "
);

$gridpanel->methods['updateDatabase'] = $sfExtjs3Plugin->asMethod($configArr);
?]