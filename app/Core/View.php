<?php

namespace App\Core;

class View
{
    protected static string $viewPath = __DIR__ . '/../Views/';

    /**
     * Render a view, optionally wrapped in a layout.
     * $view uses dot notation, e.g. "buildings.index" => Views/buildings/index.php
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layouts.app'): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = self::pathFor($view);

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: {$view}";
            return;
        }

        if ($layout === null) {
            require $viewFile;
            return;
        }

        // Capture the inner view's output, then hand it to the layout as $content.
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = self::pathFor($layout);
        require $layoutFile;
    }

    /** Include a reusable view fragment inline, e.g. from within another view. */
    public static function partial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require self::pathFor($view);
    }

    protected static function pathFor(string $dotted): string
    {
        return self::$viewPath . str_replace('.', '/', $dotted) . '.php';
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
