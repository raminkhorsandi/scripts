#!/usr/bin/env php
<?php
//This Script sets the unpublished Documents to published.
//The script runs as a cron job.
// Bootstrapping
require_once dirname(__DIR__) . ('/common/bootstrap.php');

$docFinder = new Opus_DocumentFinder();
$docFinder->setServerState('unpublished');

foreach ($docFinder->ids() as $id)
{

    $d = null;
    try
    {
        $d = new Opus_Document($id);
        $originalDate = $d->getCompletedDate();

    }
    catch (Opus_Model_NotFoundException $e)
    {
        // document with id $id does not exist
        continue;
    }

    if (!is_null($d))
    {
        $date = new Opus_Date();
        $newDate = $date->setNow();
        if ($originalDate <= $newDate)
        {
            $d->setServerState('published');
            $d->setServerDatePublished($date);
            $d->store();
            echo "publishing of document with id $id was successful\n";
        }
    }
}

echo "done.\n";
exit();
