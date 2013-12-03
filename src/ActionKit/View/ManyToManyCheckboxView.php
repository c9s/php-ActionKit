<?php
namespace ActionKit\View;
use FormKit;
use FormKit\Element;
use FormKit\Widget\Label;
use FormKit\Widget\HiddenInput;
use FormKit\Widget\ButtonInput;
use FormKit\Widget\SubmitInput;
use FormKit\Widget\CheckboxInput;
use FormKit\Layout\GenericLayout;

/**
 * Render many-to-many relationship records as a checkbox list.
 */
class ManyToManyCheckboxView
{


    public function _createCheckedMap($subset) 
    {
        $checked = array();
        foreach( $subset as $item ) {
            $checked[ $id ] = $item;
        }
        return $checked;
    }


    /**
     * Render list item
     *
     * @param string $relationId   The relationship identity
     * @param BaseModel $item      Record object.
     * @return Element('li')
     */
    public function renderItem($relationId, $item, $on = false) 
    {
        $id = $item->id;
        $li       = new Element('li');
        $label    = new Label;
        $hiddenId = new HiddenInput("{$relationId}[{$id}][_foreign_id]", array( 'value' => $id ) );
        $checkboxValue = $on ? 1 : 0;
        $checkbox = new CheckboxInput("{$relationId}[{$id}][_connect]",array( 
            'value' => $checkboxValue,
        ));
        if ( $on ) {
            $checkbox->check();
        }
        $label->append( $checkbox );
        $label->appendText( $item->dataLabel() );
        $label->append( $hiddenId );
        $li->append($label);
        return $li;
    }

    /**
     * Render a checkbox list base on the subset and superset collections.
     *
     * @param string $relationId the relationship id, used to render the form field key.
     * @param BaseCollection[]   the related collection. (optional)
     * @param BaseCollection[]   the superset collection.
     */
    public function renderList($relationId, $subset, $superset) 
    {
        $ul = new Element('ul');
        $ul->addClass('actionkit-checkbox-view');

        $checked = $subset ? $this->_createCheckedMap($subset) : array();

        // now we should render the superset, and set the checkbox to be 
        // connected from the $checked array.
        foreach( $superset as $item ) {
            $li = $this->renderItem( $relationId, $item, isset($checked[$item->id]) );
            $li->appendTo($ul);
        }
        return $ul;
    }



    /**
     * @param string                    $relationId the relationship id of the record.
     * @param LazyRecord\BaseModel      $record     the record object.
     * @param LazyRecord\BaseCollection $collection the colletion object.
     */
    public function render($relationId, $record, $collection)
    {
        return $this->renderList( $relationId,
            ($record->id ? $record->{$relationId} : null),
            $collection);
        /**
         * $record: the main record
         * relationId: the relationship id
         * collection: collection records
         */
        $ul = new Element('ul');
        $ul->addClass('actionkit-checkbox-view');
        $connected = array();

        if ( $record->id && isset($record->{ $relationId }) ) {
            // so that we can get product_id field since we joined the table.
            $foreignRecords = $record->{ $relationId };
            foreach ( $foreignRecords as $fRecord ) {
                $fId = $fRecord->id;
                $li       = new Element('li');
                $label    = new Label;
                $hiddenId = new HiddenInput(   "{$relationId}[{$fId}][_foreign_id]", array( 'value' => $fId ) );
                $checkbox = new CheckboxInput( "{$relationId}[{$fId}][_connect]",array( 
                    'value' => 1,
                ));
                $checkbox->check();
                $label->append( $checkbox );
                $label->appendText( $fRecord->dataLabel() );
                $label->append( $hiddenId );
                $li->append($label)->appendTo($ul);
                $connected[ $fId ] = $fRecord;
            }
        }

        foreach( $collection as $record ) {
            if ( isset($connected[$record->id]) ) {
                continue;
            }
            $li = new Element('li');
            $label = new Label;
            $hiddenId = new HiddenInput(   "{$relationId}[{$record->id}][_foreign_id]", array( 'value' => $record->id ) );
            $checkbox = new CheckboxInput( "{$relationId}[{$record->id}][_connect]",array(
                'value' => 0,
            ));
            $label->append($checkbox);
            $label->appendText($record->dataLabel());
            $label->append( $hiddenId );
            $li->append($label)->appendTo($ul);
        }
        return $ul;
    }
}


