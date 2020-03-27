<?php

namespace CanvassPaint\PdoModel;

use Canvass\Contract\FormFieldModel;
use Canvass\Contract\FormModel;
use Canvass\Support\PreparesFormData;

class Form extends AbstractModel implements FormModel
{
    protected static $table = 'canvass_forms';
    
    use PreparesFormData;

    public function findAllForListing($owner_id = null)
    {
        $sql = 'SELECT * FROM ' . self::$table;
        
        $params = null;
        
        if (null !== $owner_id) {
            $sql .= ' WHERE owner_id = :owner_id';
            
            $params = [':owner_id' => $owner_id];
        }

        return $this->fetchModels($sql, $params);
    }

    public function findField($field_id)
    {
        $sql = 'SELECT * FROM ' .
            FormField::getTable() .
            ' WHERE form_id = :form_id AND id = :field_id';

        $params = [
            ':form_id' => $this->data['id'],
            ':field_id' => $field_id
        ];

        $field = new FormField($this->db);

        return $field->fetchModel($sql, $params);
    }

    public function findFields($parent_id = null)
    {
        $sql = 'SELECT * FROM ' . FormField::getTable() .
            ' WHERE form_id = :form_id';
        
        $params = [':form_id' => $this->data['id']];
        
        if (null !== $parent_id) {
            $sql .= ' AND parent_id = :parent_id';
            
            $params = [':parent_id' => $parent_id];
        }

        $sql .= ' ORDER BY parent_id ASC, sort ASC';

        $field = new FormField($this->db);
        
        return $field->fetchModels($sql, $params, $this);
    }

    /**
     * @param int $sort
     * @param int $parent_id
     * @return \Canvass\Contract\FormFieldModel|null
     */
    public function findFieldWithSortOf(
        $sort,
        $parent_id = 0
    )
    {
        $sql = 'SELECT * FROM ' . FormField::getTable() .
            ' WHERE form_id = :form_id' .
            ' AND parent_id = :parent_id' .
            ' AND sort = :sort';

        $params = [
            ':form_id' => $this->data['id'],
            ':parent_id' => $parent_id,
            ':sort' => $sort
        ];

        /** @var FormFieldModel $field */
        $field = (new FormField($this->db))->fetchModel($sql, $params);

        return $field;
    }

    public function findFieldsWithSortGreaterThan($sort, $parent_id = 0)
    {
        $sql = 'SELECT * FROM ' . FormField::getTable() .
            ' WHERE form_id = :form_id' .
            ' AND parent_id = :parent_id' .
            ' AND sort > :sort';

        $params = [
            ':form_id' => $this->data['id'],
            ':parent_id' => $parent_id,
            ':sort' => $sort
        ];

        return (new FormField($this->db))->fetchModels($sql, $params);
    }
}
