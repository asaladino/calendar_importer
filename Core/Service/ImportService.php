<?php

namespace CalendarImport\Core\Service;

use CalendarImport\Core\Repository\CsvEventRepository;
use CalendarImport\Data\Model\Event;
use CalendarImport\Data\Model\Import;
use CalendarImport\Data\Model\ImportResponse;
use CalendarImport\Data\Model\Node;
use EntityFieldQuery;


/**
 * Class ImportService does all the heavy lifting for importing the csv.
 * @package CalendarImport\Core\Service
 */
class ImportService
{

    /**
     * Repo for retrieving csv events.
     *
     * @var CsvEventRepository
     */
    private $csvEventRepository;

    /**
     * File location of the csv file.
     *
     * @var string
     */
    private $file;

    /**
     * Create the importer service.
     *
     * @param $file string location of the csv file.
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->csvEventRepository = new CsvEventRepository($this->file);
    }

    /**
     * Get a report of what can be imported.
     *
     * @return \CalendarImport\Data\Model\Import
     */
    public function report()
    {
        $groups = $this->csvEventRepository->findAll(true);

        $reoccurring = 0;
        $keywords = [];
        $keywordsParent = [];
        $buildings = [];

        /** @var array $group */
        foreach ($groups as $group) {
            if (count($group) === 2) {
                $reoccurring++;
            }
            foreach ($group as $event) {
                $keywordsParent = array_merge($event->KeywordParent, $keywordsParent);
                $keywordsParent = array_unique($keywordsParent);

                $keywords = array_merge($event->Keyword, $keywords);
                $keywords = array_unique($keywords);

                $buildings[] = $event->Room;
                $buildings = array_unique($buildings);
            }
        }
        return Import::build($groups, $buildings, $keywordsParent, $keywords, $reoccurring);
    }

    /**
     * Import csv entries as events.
     *
     * @param array $group
     *
     * @return ImportResponse
     */
    public function import($group)
    {
        $importResponse = new ImportResponse();
        $importResponse->group = $group;
        foreach ($importResponse->group as $event) {
            // Check if the event already exists.
            $query = new EntityFieldQuery();
            $query->entityCondition('entity_type', 'node')
                ->fieldCondition('field_event_external_event_id', 'value', $event['EventID'])
                ->fieldCondition('field_event_external_occur_id', 'value', $event['OccurrenceID'])
                ->range(0, 1);
            $entities = $query->execute();
            // If it exists, return it.
            if (!empty($entities)) {
                return $importResponse;
            }

            $importResponse->node = Node::build($event, $group);

            $calendar_names = explode(Event::$mergeSpacer, $event['CalendarName']);
            $calendars = $this->getCalendars($calendar_names);
            $importResponse->node->setEventCalendars($calendars);

            $tag_names = $event['Keyword'];
            $tags = $this->getTags($tag_names);
            $importResponse->node->setEventTags($tags);

            try {
                node_save($importResponse->node);
                $importResponse->imported++;
            } catch (\Exception $e) {
                $importResponse->errors[] = $e;
            }
            // Just grab the first item in the group.
            break;
        }

        return $importResponse;
    }

    /**
     * Get calendars based on external name.
     * @param array $names of external calendars.
     * @return array of internal calendar id's "tid"
     */
    public function getCalendars($names = [])
    {
        if (empty($names)) {
            return [];
        }
        try {
            $query = new EntityFieldQuery();
            $query->entityCondition('entity_type', 'taxonomy_term')
                ->entityCondition('bundle', 'calendars')
                ->fieldCondition('field_calendar_external_name', 'value', $names, 'IN');
            $entities = $query->execute();
        } catch (\Exception $e) {
        }

        $results = [];
        if (!empty($entities)) {
            foreach ($entities['taxonomy_term'] as $term) {
                $results[] = ['tid' => (int)$term->tid];
            }
        }
        return $results;
    }

    /**
     * Get tags based on external name.
     * @param array $names of external tag.
     * @return array of internal tag id's "tid"
     */
    public function getTags($names = [])
    {
        if (empty($names)) {
            return [];
        }
        try {
            $query = new EntityFieldQuery();
            $query->entityCondition('entity_type', 'taxonomy_term')
                ->entityCondition('bundle', 'tags')
                ->fieldCondition('field_tags_external_name', 'value', $names, 'IN');
            $entities = $query->execute();
        } catch (\Exception $e) {
        }

        $results = [];
        if (!empty($entities)) {
            foreach ($entities['taxonomy_term'] as $term) {
                $results[] = ['tid' => (int)$term->tid];
            }
        }
        return $results;
    }

}