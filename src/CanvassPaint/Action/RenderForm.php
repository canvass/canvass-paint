<?php

namespace CanvassPaint\Action;

use Canvass\Forge;
use CanvassPaint\Contract\RenderFunction;

class RenderForm
{
    /** @var \CanvassPaint\Contract\RenderFunction */
    private $renderer;

    public function __construct(RenderFunction $render)
    {
        $this->renderer = $render;
    }

    public function render($form_id, $owner_id = null, array $meta_data = [])
    {
        /** @var \Canvass\Contract\FormModel $form */
        $form = Forge::form()->find($form_id, $owner_id);

        $fields = $form->getNestedFields();

        $data = $form->prepareData($fields);

        $data['meta'] = $meta_data;

        return $this->renderer->render($data);
    }
}
