<?php

namespace CalendarImport\Data\Model;

/**
 * This is an import event from the previous calendar.
 *
 * @package CalendarImport\Model
 */
class Event
{
    /**
     * Used to merge arrays into strings.
     * @var string
     */
    public static $mergeSpacer = '||';

    public $OccurrenceID;
    public $EventID;
    public $Title;
    public $Subtitle;
    public $Description;
    public $Cost;

    public $KeywordParent;
    public $Keyword;

    public $CalendarName;
    public $CalendarDisplayedName;

    public $EventStartDate;
    public $EventEndDate;
    public $Room;

    public $eventType;
    public $columns;

    /**
     * Build an event from a csv entry
     *
     * @param $columns
     * @param $row
     *
     * @return Event
     */
    public static function build($columns, $row)
    {
        $event = new Event();
        $event->columns = $columns;
        foreach ($columns as $index => $field) {
            $event->{$field} = $row[$index];
        }
        $event->updateKeywordsParent();
        $event->updateKeyword();

        return $event;
    }

    /**
     * Merge keyword parents into one array and remove duplicates.
     */
    public function updateKeywordsParent()
    {
        $keywords = explode(self::$mergeSpacer, $this->KeywordParent);
        $this->KeywordParent = array_unique($keywords);
    }

    /**
     * Merge keywords into one array and remove duplicates.
     */
    public function updateKeyword()
    {
        $keywords = explode(self::$mergeSpacer, $this->Keyword);
        $this->Keyword = array_unique($keywords);
    }

    /**
     * Merge one event with another event.
     * @param Event $event to merge with.
     */
    public function mergeWith($event)
    {
        foreach ($this->columns as $field) {
            if ($this->{$field} !== $event->{$field} && !is_array($this->{$field})) {
                $this->{$field} .= self::$mergeSpacer . $event->{$field};
            }
            if (is_array($this->{$field})) {
                $terms = array_merge($event->{$field}, $this->{$field});
                $this->{$field} = array_unique($terms);
            }
        }
    }
}