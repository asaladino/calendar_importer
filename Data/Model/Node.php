<?php

namespace CalendarImport\Data\Model;

use DateObject;

/**
 * Class Node is an Event in a format that drupal likes.
 * @package CalendarImport\Data\Model
 */
class Node
{
    public $type = 'event';
    public $status;
    public $uid;
    public $title;
    public $promote;
    public $created;
    public $timestamp;
    public $sticky;
    public $format;
    public $language = LANGUAGE_NONE;
    public $teaser;
    public $body;
    public $revision;

    public $field_event_cost;
    public $field_event_room;
    public $field_event_subtitle;
    public $field_event_start_date;
    public $field_event_building;
    public $field_event_tag;
    public $field_event_calendar;
    public $field_event_external_event_id;
    public $field_event_external_occur_id;
    public $field_event_external_calendar;
    public $field_event_external_keyword;
    public $field_event_external_repeating;

    /**
     * Build a node from an event.
     *
     * @param $event Event
     * @param $group array
     * @return \CalendarImport\Data\Model\Node
     */
    public static function build($event, $group)
    {
        global $user;

        $external_repeating = "";
        if (count($group) > 1) {
            foreach ($group as $e) {
                $external_repeating .= $e['EventStartDate'] . " to " . $e['EventEndDate'] . "\n";
            }
        }

        $startDate = new DateObject($event['EventStartDate']);
        $endDate = new DateObject($event['EventEndDate']);

        $node = new Node();
        $node->title = $event['Title'];
        /** @noinspection PhpUndefinedFieldInspection */
        $node->uid = $user->uid;
        $node->body[$node->language][0]['value'] = $event['Description'];
        $node->body[$node->language][0]['summary'] = text_summary($event['Description']);
        $node->body[$node->language][0]['format'] = 'filtered_html';

        $node->field_event_cost[$node->language][0]['value'] = $event['Cost'];
        $node->field_event_room[$node->language][0]['value'] = $event['Room'];
        $node->field_event_subtitle[$node->language][0]['value'] = $event['Subtitle'];
        $node->field_event_external_repeating[$node->language][0]['value'] = $external_repeating;

        $calendar_name = explode(Event::$mergeSpacer, $event['CalendarName']);
        $calendar_name_display = explode(Event::$mergeSpacer, $event['CalendarDisplayedName']);
        $calendar = array_merge($calendar_name, $calendar_name_display);
        $calendar = array_unique($calendar);
        $node->field_event_external_calendar[$node->language][0]['value'] = implode(Event::$mergeSpacer, $calendar);

        $keyword = implode(Event::$mergeSpacer, $event['KeywordParent']) . implode(Event::$mergeSpacer, $event['Keyword']);
        $node->field_event_external_keyword[$node->language][0]['value'] = $keyword;

        $node->field_event_start_date[$node->language][0]['value']['date'] = $startDate->format(DateObject::W3C);
        if ($endDate->format(DateObject::W3C) !== false) {
            $node->field_event_start_date[$node->language][0]['value2']['date'] = $endDate->format(DateObject::W3C);
        }

        $node->field_event_external_event_id[$node->language][0]['value'] = $event['EventID'];
        $node->field_event_external_occur_id[$node->language][0]['value'] = $event['OccurrenceID'];

        // Keeping this around just in case I decide to try and map values.
//        $node->field_event_building[$node->language][0]['target_id'] = 12;
//        $node->field_event_building[$node->language][1]['target_id'] = 13;

        node_object_prepare($node);

        return $node;
    }

    /**
     * Add event tags to the node.
     * @param array $tags
     */
    function setEventTags($tags = [])
    {
        $this->field_event_tag[$this->language] = $tags;
    }

    /**
     * Add calendars to the node.
     * @param array $calendars
     */
    function setEventCalendars($calendars = [])
    {
        $this->field_event_calendar[$this->language] = $calendars;
    }
}