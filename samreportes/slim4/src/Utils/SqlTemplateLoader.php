<?php
namespace App\Utils;

class SqlTemplateLoader
{
    public static function load(string $templateName, array $replacements = [], string $path = ''): string
    {
        // 1) Define tus placeholders “base” aquí
        $defaults = [
            'SELECT_CLAUSE'   => '',
            'CONDITIONS'      => '',
            'GROUP_BY_CLAUSE' => '',
            'ORDER_BY_CLAUSE' => '',
        ];

        // 2) Mezcla los defaults con lo que te pasen
        $params = array_merge($defaults, $replacements);

        Path::combine(BASE_PATH,'sql_templates', $path, $templateName);

        // 3) Carga la plantilla
        $path = Path::combine(BASE_PATH,'sql_templates', $path, $templateName) . '.sql';
        if (!file_exists($path)) {
            throw new \Exception("Plantilla SQL '$templateName.sql' no encontrada.");
        }
        $sql = file_get_contents($path);

        // 4) Sustituye sólo las keys que realmente vinieron (los demás quedan en '')
        foreach ($params as $key => $value) {
            $sql = str_replace('{' . $key . '}', $value, $sql);
        }

        // 5) Limpia cualquier placeholder sobrante (si tuvieras otros)
        $sql = preg_replace('/\{[A-Z_]+\}/', '', $sql);

        return $sql;
    }
}
