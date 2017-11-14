<?php

namespace CalendarImport\Core\Repository;

use CalendarImport\Data\Model\Event;

/**
 * Class CsvEventRepository reads and writes events from the csv file.
 * @package CalendarImport\Core\Repository
 */
class CsvEventRepository
{
    /**
     * Location of the file to import.
     *
     * @var string
     */
    private $file;

    /**
     * Events are stored here.
     *
     * @var array
     */
    private $events;

    /**
     * Build the repo with a file location.
     *
     * @param $file string location of the csv file.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @param bool $group if you want the events grouped.
     *
     * @return Event[]
     */
    public function findAll($group = false)
    {
        $this->events = [];
        $columns = [];
        $row = 0;
        if (($handle = fopen($this->file, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($row === 0) {
                    $columns = $data;
                } else {
                    $event = Event::build($columns, $data);
                    if ($group) {
                        $this->group($event);
                    } else {
                        $this->events[] = $event;
                    }
                }
                $row++;
            }
            fclose($handle);
        }
        return $this->events;
    }

    /**
     * Group the events.
     *
     * @param Event $newEvent
     */
    private function group($newEvent)
    {
        if (!isset($this->events[$newEvent->EventID])) {
            $this->events[$newEvent->EventID][] = $newEvent;
        } else {
            $foundEvent = false;
            /** @var Event $event */
            foreach ($this->events[$newEvent->EventID] as $event) {
                if ($newEvent->OccurrenceID === $event->OccurrenceID) {
                    $event->mergeWith($newEvent);
                    $foundEvent = true;
                    break;
                }
            }
            if (!$foundEvent) {
                $this->events[$newEvent->EventID][] = $newEvent;
            }
        }
    }

    /**
     * Saves an uploaded file to the correct location.
     * @param string $file that was uploaded.
     */
    public function save($file)
    {

    }

}