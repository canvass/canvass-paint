# Canvass Paint

A form rendering abstraction for Canvass.

*Note: This library should be paired with a concrete implementation library such as [CanvassPaint\Twig](https://github.com/canvass/canvass-paint-twig) or [CanvassPaint\Blade](https://github.com/canvass/canvass-paint-blade).*

## Installation
This can be installed via composer:
```bash
composer require canvass/canvass-paint
```

## Creating a Different Implementation
Create an implementation of `\CanvassPaint\Contract\RenderFunction` that incorporates an html rendering library.

Below is the CanvassPaint\Twig implementation:

```php
namespace CanvassPaint\Twig;

class RenderFunction implements \CanvassPaint\Contract\RenderFunction
{
    /** @var \Twig\Environment */
    private $twig;

    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render($data)
    {
        return $this->twig->render(
            '/form/form.twig',
            $data
        );
    }

    public function getTwigEnvironment(): Environment
    {
        return $this->twig;
    }
}
```

### Views
The Blade and Twig libraries can help guide you on how to set up the various field views.

### RenderFunction
Then pass the `RenderFunction` to the `RenderForm` action:
```php
$action = new RenderForm(new RenderFunction());

$html = $action->render($form_id);
```

