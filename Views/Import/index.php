<?php
/**
 * @var array $params
 */
?>
<h2>Calendar Import</h2>
<?php if (!file_exists($params['file'])): ?>
    <div>File to import not found. Please upload a file.</div>
<?php else: ?>
    <div id="calendar-import-status">Loading...</div>
    <br/>
    <a id="calendar-import-start" class="button">Start Import</a>
    <a id="calendar-import-cancel" class="button" style="display:none;">Cancel Import</a>
<?php endif ?>
<br/>
<br/>
<hr/>
<table>
    <thead>
    <tr>
        <th style="width: 50%">Info</th>
        <th>Output</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <h3>Total Events</h3>
            <span id="calendar-import-groups-count"></span>

            <h3>Re-occurring Events</h3>
            <span id="calendar-import-reoccurring"></span>

            <h3>Buildings</h3>
            <span id="calendar-import-buildings-count"></span>

            <h3><a id="calendar-import-show-keywords-parent">Keywords Parent</a></h3>
            <span id="calendar-import-keywords-parent-count"></span>

            <h3><a id="calendar-import-show-keywords">Keywords</a></h3>
            <span id="calendar-import-keywords-count"></span>
        </td>
        <td>
            <pre id="calendar-import-output"></pre>
        </td>
    </tr>
    </tbody>
</table>
