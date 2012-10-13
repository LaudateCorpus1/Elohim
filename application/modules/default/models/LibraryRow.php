<?php

class Default_Model_LibraryRow extends Islamine_Model_SearchableRow
{
    public function getSearchFields()
    {
        $fields = array();
        $fields['class'] = $this->modelType();
        $fields['key'] = $this['id'];
        $fields['content'] = $this['content'];
        $fields['title'] = $this['title'];
        //$fields['tags'] = $this->reported_by;
        return $fields;
    }
    /**
    *Each model row exposes what type it is.. This helps make our search
    * more generic.
    * @return <type>
    */
    public function modelType()
    {
        return "Library";
    }
}

