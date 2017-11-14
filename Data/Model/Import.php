<?php

namespace CalendarImport\Data\Model;

/**
 * Used to help organize the import.
 * @package CalendarImport\Model
 */
class Import
{
    /**
     * Groups to import.
     * @var array
     */
    public $groups;
    /**
     * Buildings to import.
     * @var array
     */
    public $buildings;
    /**
     * Keyword parents to import.
     * @var array
     */
    public $keywordsParent;
    /**
     * Keywords to import.
     * @var array
     */
    public $keywords;
    /**
     * Number of reoccurring events found.
     * @var int
     */
    public $reoccurring;

    /**
     * Build an import object with:
     * @param $groups array
     * @param $buildings array
     * @param $keywordsParent array
     * @param $keywords array
     * @param $reoccurring int
     * @return Import
     */
    public static function build($groups, $buildings, $keywordsParent, $keywords, $reoccurring)
    {
        $import = new Import();
        $import->groups = $groups;
        $import->buildings = $buildings;
        $import->keywordsParent = $keywordsParent;
        $import->keywords = $keywords;
        $import->reoccurring = $reoccurring;
        return $import;
    }
}