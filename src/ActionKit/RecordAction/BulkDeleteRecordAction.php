<?php
namespace ActionKit\RecordAction;

abstract class BulkDeleteRecordAction extends BulkRecordAction
{
    const TYPE = 'bulk_delete';


    /*
     * Define your record class here:
     *
     * public $recordClass;
     */


    public function run()
    {
        $records = $this->loadRecords();
        foreach ($records as $record) {
            $ret = $record->delete();
        }
        return $this->success( count($records) . '個項目刪除成功');
    }
}
