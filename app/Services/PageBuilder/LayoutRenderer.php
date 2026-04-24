<?php

namespace App\Services\PageBuilder;

use App\Models\Article;
use App\Models\Page;
use App\Services\ModuleRenderer\ModuleRendererService;

class LayoutRenderer
{
    public function __construct(
        protected ModuleRendererService $moduleRenderer
    ) {}

    public function render(Page $page, ?Article $article = null): string
    {
        $layout = $page->layout_json;

        if (empty($layout) || ! is_array($layout)) {
            return '';
        }

        $html = '';
        foreach ($layout as $row) {
            if (($row['active'] ?? true) === false) {
                continue;
            }

            $html .= $this->renderRow($row, $page, $article);
        }

        return $html;
    }

    protected function renderRow(array $row, Page $page, ?Article $article): string
    {
        $type = $row['type'] ?? 'body';
        $containerClass = $row['cont'] ?? 'container';
        $cssClass = $row['elcss'] ?? '';
        $elementId = $row['elid'] ?? '';
        $inlineStyle = $row['elstyle'] ?? '';
        $otherAttrs = $row['elother'] ?? '';

        $idAttr = $elementId ? " id=\"{$elementId}\"" : '';
        $styleAttr = $inlineStyle ? " style=\"{$inlineStyle}\"" : '';

        $html = "<section class=\"{$type}-section {$cssClass}\"{$idAttr}{$styleAttr} {$otherAttrs}>";
        $html .= "<div class=\"{$containerClass}\">";
        $html .= '<div class="row">';

        foreach ($row['children'] ?? [] as $columns) {
            if (is_array($columns)) {
                foreach ($columns as $column) {
                    if (($column['active'] ?? true) === false) {
                        continue;
                    }

                    $html .= $this->renderColumn($column, $page, $article);
                }
            }
        }

        $html .= '</div></div></section>';

        return $html;
    }

    protected function renderColumn(array $column, Page $page, ?Article $article): string
    {
        $colClasses = collect([
            $column['coltype'] ?? 'col-12',
            $column['colsmtype'] ?? '',
            $column['colmdtype'] ?? '',
            $column['collgtype'] ?? '',
            $column['colxltype'] ?? '',
            $column['celcss'] ?? '',
        ])->filter()->implode(' ');

        $colId = ! empty($column['celid']) ? " id=\"{$column['celid']}\"" : '';
        $colStyle = ! empty($column['celstyle']) ? " style=\"{$column['celstyle']}\"" : '';
        $colOther = $column['celother'] ?? '';

        $html = "<div class=\"{$colClasses}\"{$colId}{$colStyle} {$colOther}>";

        foreach ($column['children'] ?? [] as $modules) {
            if (is_array($modules)) {
                foreach ($modules as $module) {
                    if (($module['active'] ?? true) === false) {
                        continue;
                    }

                    $html .= $this->renderModule($module, $page, $article);
                }
            }
        }

        $html .= '</div>';

        return $html;
    }

    protected function renderModule(array $module, Page $page, ?Article $article): string
    {
        $moduleId = $module['modulid'] ?? null;
        $config = [];

        if (isset($module['json']) && is_array($module['json'])) {
            foreach ($module['json'] as $param) {
                if (isset($param['name'], $param['value'])) {
                    $config[$param['name']] = $param['value'];
                }
            }
        }

        return $this->moduleRenderer->render((int) $moduleId, $config, $page, $article);
    }
}
