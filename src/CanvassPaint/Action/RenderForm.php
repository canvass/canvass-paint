<?php

namespace CanvassPaint\Action;

use Canvass\Forge;
use CanvassPaint\Contract\RenderFunction;

class RenderForm
{
    /** @var \Canvass\Contract\FormModel */
    private $form;

    /** @var \CanvassPaint\Contract\RenderFunction */
    private $renderer;

    public function __construct(RenderFunction $render)
    {
        $this->renderer = $render;
    }

    public function render($form_id, $owner_id = null, array $meta_data = [])
    {
        /** @var \Canvass\Contract\FormModel $form */
        $this->form = Forge::form()->find($form_id, $owner_id);

        $fields = $this->form->getNestedFields();

        $data = $this->form->prepareData($fields);

        $data['meta'] = $meta_data;

        return $this->renderer->render($data);
    }

    public function getForm(): \Canvass\Contract\FormModel
    {
        return $this->form;
    }
}
