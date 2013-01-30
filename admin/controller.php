<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

define('PHP_EXCEL', JPATH_COMPONENT_ADMINISTRATOR .
        DS . 'phpexcel' . DS . 'classes' . DS . 'PHPExcel.php');
define('PHP_EXCEL_UPLOADS', JPATH_COMPONENT_ADMINISTRATOR . DS . 'uploads' . DS);

/**
 * General Controller of HelloWorld component
 */
class PriceUploaderController extends JController {

    protected $_db = null;

    /**
     * display task
     *
     * @return void
     */
    function display($cachable = false, $urlparams = false) {
        // Отправлялись ли файлы?
        if (!empty($_FILES['price_file']['tmp_name'])) {

            // Подгрузка расширения для работы с xls
            require_once(PHP_EXCEL);

            // Получение объекта базы данных
            $this->_db = JFactory::getDbo();

            if (($fpath = $this->_check_file('price_file')) !== false)
                if (($fdata = $this->_parse_file($fpath)) !== false)
                    if ($this->_clear_table("#__pricelistbase") &&
                            $this->_clear_table("#__pricelistcity")) {
                        $this->_add_data("#__pricelistbase", '`id`, `title`, `oper`, `price1`, `price2`, `price3`', $fdata['base']);
                        $this->_add_data("#__pricelistcity", '`id`, `title`, `city`, `price1`, `price2`, `price3`', $fdata['city']);
                    }

            /*
            // Обработка прайса с базовыми ценами
            if (($fpath = $this->_check_file('base_file')) !== false)
                if (($fdata = $this->_parse_file($fpath)) !== false)
                    if ($this->_clear_table("#__pricelistbase")) {
                        // Создание запроса
                        $query = $this->_db->getQuery(true)
                                        ->insert("#__pricelistbase")->columns('
                            `id`, `title`, `oper`, `price1`, `price2`, `price3`
                        ');

                        // Добавление данных к запросу
                        foreach ($fdata as $data_row) {
                            $query->values("
                                {$data_row[0]},
                                '{$data_row[1]}',
                                '{$data_row[2]}',
                                {$data_row[3]},
                                {$data_row[4]},
                                {$data_row[5]}
                            ");
                        }

                        //  Выполнение запроса
                        if (!$this->_db->setQuery($query)->query())
                        // Дамп запроса, если он не был выполнен
                            echo $query->dump();
                    }

            // Прайс с ценами по городу
            if (($fpath = $this->_check_file('city_file')) !== false)
                if (($fdata = $this->_parse_file($fpath)) !== false)
                    if ($this->_clear_table("#__pricelistcity")) {
                        // Создание запроса
                        $query = $this->_db->getQuery(true)
                                        ->insert("#__pricelistcity")->columns('
                            `id`, `id_base`, `city`, `price1`, `price2`, `price3`
                        ');

                        // Добавление данных к запросу
                        foreach ($fdata as $data_row) {
                            $query->values("
                                {$data_row[0]},
                                {$data_row[1]},
                                '{$data_row[2]}',
                                {$data_row[3]},
                                {$data_row[4]},
                                {$data_row[5]}
                            ");
                        }

                        //  Выполнение запроса
                        if (!$this->_db->setQuery($query)->query())
                        // Дамп запроса, если он не был выполнен
                            echo $query->dump();
                    }
             */
        }

        $this->_clean_uploads();

        // set default view if not set
        $input = JFactory::getApplication()->input;
        $input->set('view', $input->getCmd('view', 'Base'));

        // Настройка тулбара
        $this->addToolBar();

        // call parent behavior
        parent::display($cachable);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() {
        JToolBarHelper::title(JText::_('Загрузчик прайс-листа'));
        //JToolBarHelper::deleteList('', 'helloworlds.delete');
        //JToolBarHelper::editList('helloworld.edit');
        //JToolBarHelper::addNew('helloworld.add');
    }

    /**
     * Проверяет формат файла и загружает в папку
     * @var string file_name ключ массива FILES
     * @return  string полный путь к файлу
     */
    private function _check_file($file_key) {
        if (!empty($_FILES[$file_key]['tmp_name']))
            if ($_FILES[$file_key]['type'] == 'application/vnd.ms-excel') {
                // Перемещение файла и возврат значения
                $file_name = time();
                $full_path = PHP_EXCEL_UPLOADS . $file_name . '.xls';

                if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $full_path))
                    return $full_path;
            }
        return false;
    }

    /**
     * Конвертирует первый лист xls файла в массив
     * @var string full_file_path ключ массива FILES
     * @return array
     */
    private function _parse_file($full_file_path) {
        if (file_exists($full_file_path)) {
            // Получение экземпляра ридера и загрузка файла
            $xls_reader = new PHPExcel_Reader_Excel5();
            $xls_file = $xls_reader->load($full_file_path);

            // Конвертируем первые два листа в массивы
            $result = array(
                'base' => $this->_parse_list($xls_file),
                'city' => $this->_parse_list($xls_file, 1)
            );

            unset($xls_reader);
            unset($xls_file);

            // Возвращаем значение
            return $result;
        }
        return false;
    }

    /**
     * Очищает папку для загрузок прайс-листов
     */
    private function _clean_uploads() {
        if (($dh = opendir(PHP_EXCEL_UPLOADS)) !== false) {
            while (false !== ($file = readdir($dh))) {
                if ($file != '.' && $file != '..' && $file != 'index.html')
                    unlink(PHP_EXCEL_UPLOADS . $file);
            }
        }
    }

    /**
     * Удаляет все записи из выбранной таблицы
     * @var string table_name название таблицы #__pricelistbase 
     *  или #__pricelistcity
     * @return boolean
     */
    private function _clear_table($table_name) {
        if (!empty($table_name)) {
            // Создание запроса
            $query = $this->_db->getQuery(true)
                            ->delete($table_name)->where("1");

            // Выполнение запроса и возврат результата
            if ($this->_db->setQuery($query)->query())
                return true;

            // Дамп запроса, если он не был выполнен
            echo $query->dump();
        }
        return false;
    }

    /**
     * Конвертирует лист xls файла с заданным индексом в массив
     * @param type $xls_file - файл xls экземпляр PHPExcel
     * @param type $list_index - индекс требуемого листа
     * @return type
     */
    private function _parse_list(&$xls_file, $list_index = 0) {
        $result = $xls_file->setActiveSheetIndex($list_index)->toArray();
        array_shift($result);
        return $result;
    }

    /**
     * Добавление массива данных в БД
     * @param string $columns - строка, указывающая заполняемые параметры
     * @param array $data - массив данных
     */
    private function _add_data($table, $columns, $data) {
        // Составление скелета запроса
        $query = $this->_db->getQuery($table)->insert($table)->columns($columns);

        // Добавление данных
        foreach ($data as $row) {
            $query->values("
                    {$row[0]},
                    '{$row[1]}',
                    '{$row[2]}',
                    {$row[3]},
                    {$row[4]},
                    {$row[5]}
                ");
        }

        // Выполнение запроса
        if (!$this->_db->setQuery($query)->query())
            echo $query->dump();
    }

}