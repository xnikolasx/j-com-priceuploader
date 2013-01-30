<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
 
// load tooltip behavior
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_priceuploader'); ?>" 
      method="post" name="adminForm" id="adminForm" 
      enctype="multipart/form-data">
    <fieldset>
        <legend>
            Выберите файл прайс-листа на Вашем компьютере
            и нажмите кнопку "Загрузить"
        </legend>
        <label for="price_file">
            Файл с базовыми ценами (xls):
        </label>
        <input type="file" name="price_file" id="price_file" /><br />
        
        <!--
        <label for="city_file">
            Файл с ценами по городам (xls):
        </label>
        <input type="file" name="city_file" id="city_file" /><br />
        -->
    </fieldset>
    <input type="submit" value="Загрузить" />
        <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>