<?php

namespace CalendarImport\Controllers;

/**
 * Class BaseController will handle typical controller tasks.
 * @package CalendarImport\Controllers
 */
class BaseController
{
    /**
     * Render the view based on the location
     *
     * @param string $location of view to render.
     * @param array $params to pass to the view.
     *
     * @return string of html to render.
     */
    public function render($location, /** @noinspection PhpUnusedParameterInspection */
                           $params)
    {
        ob_start();
        /** @noinspection PhpIncludeInspection */
        /** @noinspection PhpToStringImplementationInspection */
        include(dirname(__FILE__) . "/../Views$location.php");
        $returned = ob_get_contents();
        ob_end_clean();
        return $returned;
    }

    /**
     * Render json data and die!
     *
     * @param array $data to encode and render as json.
     */
    public function renderJson($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        die();
    }
}