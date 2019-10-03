<?php

namespace Implement;

use CanvassPaint\Contract\RenderFunction;

class TestRenderFunction implements RenderFunction
{
    public function render($data)
    {
        return json_encode($data);
    }
}
