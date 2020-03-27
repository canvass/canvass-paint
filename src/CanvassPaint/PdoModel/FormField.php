<?php

namespace CanvassPaint\PdoModel;

use Canvass\Contract\FormFieldModel;
use Canvass\Contract\FormModel;
use Canvass\Forge;
use Canvass\Support\FieldTypes;
use Canvass\Support\PreparesFormFieldData;

class FormField extends AbstractModel implements FormFieldModel
{
    /** @var FormModel */
    private $form_model;

    protected static $table = 'canvass_form_fields';

    use PreparesFormFieldData;

    public function __construct(
        \PDO $db,
        array $data = [],
        FormModel $form = null
    )
    {
        if (null !== $form) {
            $this->form_model = $form;
        } elseif (isset($data['form']) && $data['form'] instanceof FormModel) {
            $this->form_model = $data['form'];

            unset($data['form']);
        }

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

    /** @return string */
    public function getHtmlType()
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

    /** @return string */
    public function getGeneralType()
    {
        return $this->data['general_type'];
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

    /**
     * @return \Canvass\Contract\FormModel
     * @throws \WebAnvil\ForgeClosureNotFoundException
     */
    public function getFormModel()
    {
        if (null === $this->form_model) {
            $this->form_model = Forge::form()->find(
                $this->getData('form_id'),
                Forge::getOwnerId()
            );
        }

        return $this->form_model;
    }

    /**
     * @param \Canvass\Contract\FormModel $form_model
     * @return void
     */
    public function setFormModel(FormModel $form_model)
    {
        $this->form_model = $form_model;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        $this->convertAttributesToArray();

        return isset($this->data['attributes'][$key]);
    }

    public function setDataToAttributes($key, $value)
    {
        $this->convertAttributesToArray();

        $this->data['attributes'][$key] = $value;
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @param FormModel|null $form_model
     * @return \CanvassPaint\PdoModel\AbstractModel[]
     */
    protected function fetchModels(
        $sql,
        array $params = null,
        FormModel $form_model = null
    )
    {
        $rows = $this->fetchAll($sql, $params);

        $models = [];

        foreach ($rows as $row) {
            $models[] = new static($this->db, $row, $form_model);
        }

        return $models;
    }

    /** @return void */
    private function convertAttributesToArray()
    {
        if (empty($this->data['attributes'])) {
            $this->data['attributes'] = [];
        } elseif (is_string($this->data['attributes'])) {
            $this->data['attributes'] =
                json_decode($this->data['attributes'], true);
        } elseif (! is_array($this->data['attributes'])) {
            $this->data['attributes'] = (array) $this->data['attributes'];
        }
    }
}
