#!/usr/bin/env php
<?php
//This Script sets the unpublished Documents to published.
//The script runs as a cron job.
// Bootstrapping
require_once dirname(__FILE__) . ('/common/bootstrap.php');

$docFinder = new Opus_DocumentFinder();
$docFinder->setServerState('unpublished');
foreach ($docFinder->ids() as $id) {
    $doc = null;

    try {
        $doc = new Opus_Document($id);
        $originalDate = $doc->getCompletedDate();
    }
    catch (Opus_Model_NotFoundException $e) {
        // document with id $id does not exist
        continue;
    }
    if (! is_null($doc)) {
        $date = new Opus_Date();
        $newDate = $date->setNow();
        if ($originalDate == $newDate) {
            $doc->setServerState('published');
            $doc->setServerDatePublished($date);
            $doc->store();
            echo "publishing of document with id $id was successful\n";
        }
    }
}
echo "done.\n";
exit();
