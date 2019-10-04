<?php

namespace CanvassPaint\PdoModel;

use Canvass\Contract\FormFieldModel;
use Canvass\Support\FieldTypes;
use Canvass\Support\PreparesFormFieldData;

class FormField extends AbstractModel implements FormFieldModel
{
    protected static $table = 'canvass_form_fields';

    use PreparesFormFieldData;

    public function __construct(\PDO $db, array $data = [])
    {
        parent::__construct($db, $data);

        $this->convertAttributesToArray();
    }

    public function findAllByFormId($form_id, $parent_id = null)
    {
        $sql = ' SELECT * FROM ' . self::getTable() .
            'WHERE form_id = :form_id';

        $params = [':form_id' => $form_id];

        if (null !== $parent_id) {
            $sql .= ' AND parent_id = :parent_id';

            $params[':parent_id'] = $parent_id;
        }

        $sql .= ' ORDER BY sort ASC';

        return $this->fetchModels($sql, $params);
    }

    public function getHtmlType(): string
    {
        return $this->data['canvass_type'];
        $type = $this->data['type'];

        if (in_array($type, FieldTypes::INPUT_TYPES, true)) {
            return 'input';
        }

        if (strpos($type, 'group') !== false) {
            return 'group';
        }

        return $type;
    }

    public function retrieveChildren()
    {
        $sql = ' SELECT * FROM ' . self::getTable() .
            'WHERE parent_id = :parent_id ORDER BY sort ASC';

        return $this->fetchModels($sql, [':parent_id' => $this->data['id']]);
    }

    public function getDataFromAttributes($key)
    {
        $this->convertAttributesToArray();

        return $this->data['attributes'][$key] ?? null;
    }

    public function hasAttribute($key): bool
    {
        $this->convertAttributesToArray();

        return isset($this->data['attributes'][$key]);
    }

    public function setDataToAttributes($key, $value)
    {
        $this->convertAttributesToArray();

        $this->data['attributes'][$key] = $value;
    }

    private function convertAttributesToArray(): void
    {
        if (empty($this->data['attributes'])) {
            $this->data['attributes'] = [];
        } elseif (is_string($this->data['attributes'])) {
            $this->data['attributes'] = json_decode($this->data['attributes'], true);
        } elseif (! is_array($this->data['attributes'])) {
            $this->data['attributes'] = (array) $this->data['attributes'];
        }
    }
}
