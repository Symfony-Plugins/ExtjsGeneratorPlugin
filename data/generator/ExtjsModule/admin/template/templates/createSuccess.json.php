[?php use_helper('I18N') ?]
[?php if ($sf_user->hasFlash('error')):?]
{"success":false,"message":"[?php echo __($sf_user->getFlash('error'), array(), '<?php echo $this->getI18nCatalogue() ?>') ?]"}
[?php else:?]
{"success":true,"message":"[?php echo __($sf_user->getFlash('notice'), array(), '<?php echo $this->getI18nCatalogue() ?>') ?]","key":"[?php echo $<?php echo $this->getSingularName() ?>->get<?php echo $this->getPrimaryKeys(true) ?>() ?]","title":"[?php echo addslashes(<?php echo $this->getI18NString('edit.title', 'Edit '.$this->configuration->getObjectName(), false) ?>) ?]"}
[?php endif;?]