#!/usr/bin/env bash
#
# generate_model.sh
# Ejemplo:
#   ./generate_model.sh ventas_cat_tipo_cambio [src/Models]
#

set -euo pipefail

# ——————————————————————————————————————————
# Función para convertir snake_case a PascalCase
# ——————————————————————————————————————————
snake_to_pascal() {
    local input="$1"
    local out=""
    IFS='_' read -r -a parts <<< "$input"
    for p in "${parts[@]}"; do
        # primera letra en mayúscula, resto igual
        first="${p:0:1}"
        rest="${p:1}"
        out+="$(tr '[:lower:]' '[:upper:]' <<< "$first")$rest"
    done
    printf "%s" "$out"
}

# ——————————————————————————————————————————
# Validar parámetros
# ——————————————————————————————————————————
if [ "$#" -lt 1 ] || [ "$#" -gt 2 ]; then
    echo "Uso: $0 <nombre_tabla> [<directorio_salida>]"
    echo "Ejemplo: $0 ventas_cat_tipo_cambio src/Models"
    exit 1
fi

tabla="$1"

# Usar 'src/Models' por defecto si no se proporciona el directorio de salida.
if [ "$#" -eq 2 ]; then
    directorio_salida="$2"
else
    directorio_salida="src/Models"
fi

# ——————————————————————————————————————————
# Construir nombre de clase: PascalCase + "Model.php"
# ——————————————————————————————————————————
pascal="$(snake_to_pascal "$tabla")"
filename="${pascal}Model.php"

# ——————————————————————————————————————————
# Crear carpeta de salida si no existe
# ——————————————————————————————————————————
mkdir -p "$directorio_salida"

# ——————————————————————————————————————————
# URL base (ajusta si fuera necesario)
# ——————————————————————————————————————————
base_url="http://localhost:8080/samreportes/slim4/build"

# ——————————————————————————————————————————
# Hacer curl y volcar resultado en el archivo
# ——————————————————————————————————————————
target_file="${directorio_salida%/}/${filename}"
curl -s "${base_url}/${tabla}" -o "$target_file"

echo "✔ Generado: $target_file"
