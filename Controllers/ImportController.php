<?php

namespace CalendarImport\Controllers;

use CalendarImport\Core\Service\ImportService;
use CalendarImport\Data\Model\ImportResponse;

class ImportController extends BaseController
{
    /**
     * Location of the csv file to import.
     * @var string
     */
    private $file;

    /**
     * ImportController constructor.
     */
    public function __construct()
    {
        $this->file = dirname(__FILE__) . '/../import/calendar.csv';
    }

    public function index()
    {
        return $this->render('/Import/index', ['file' => $this->file]);
    }

    public function report()
    {
        $importService = new ImportService($this->file);
        $import = $importService->report();

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->renderJson(compact('import'));
    }

    public function import()
    {
        $importResponse = new ImportResponse();
        if (!empty($_POST)) {
            $group = $_POST['group'];
            $importService = new ImportService($this->file);
            $importResponse = $importService->import($group);
        }
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->renderJson(compact('importResponse'));
    }
}