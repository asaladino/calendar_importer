<?php

namespace CalendarImport\Data\Model;


use Exception;

/**
 * Class ImportResponse is what is returned from an import.
 * @package CalendarImport\Data\Model
 */
class ImportResponse
{

    /**
     * Number of imported entries.
     *
     * @var int
     */
    public $imported = 0;

    /**
     * Import errors.
     *
     * @var Exception[]
     */
    public $errors;

    /**
     * Group of events imported.
     * @var array
     */
    public $group;

    /**
     * Node that was imported.
     * @var Node
     */
    public $node;
}