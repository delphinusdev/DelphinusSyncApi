<?php
namespace App\Utils;

use Exception;
use DateTime;
use ReflectionClass;
use ReflectionProperty; // Importa ReflectionProperty para la verificación isInitialized
use App\Models\BaseModel; // Asegúrate de importar BaseModel

class AddOperations
{
    private array $data; // Almacena los parámetros de la reserva proporcionados por el usuario
    private array $operationsToApply; // Almacena las operaciones a aplicar
    private array $modelGeneratedDefaults = []; // Almacena los valores por defecto generados desde el modelo

    /**
     * Constructor de la clase.
     * Inicializa los parámetros de la reserva y las operaciones.
     * Se usa internamente o cuando no se genera desde un modelo.
     *
     * @param array $initialParams Parámetros iniciales opcionales para la reserva.
     */
    public function __construct(array $initialParams = [])
    {
        $this->data = []; // Inicializa la data como un array vacío
        $this->operationsToApply = []; // Inicializa las operaciones vacías
        $this->with($initialParams); // Aplica cualquier parámetro inicial provisto
    }

    /**
     * Crea una instancia de RsrvReservationComposer inicializándola con valores por defecto
     * basados en las propiedades de una clase de modelo dada.
     *
     * @param string $modelClass El nombre completo de la clase del modelo (ej. 'App\Models\RsrvReservasModel').
     * @param array $initialParams Parámetros iniciales opcionales para la reserva que sobreescribirán los por defecto del modelo.
     * @return self
     * @throws Exception Si la clase del modelo no existe o no se puede reflejar.
     */
    public static function fromModel(string $modelClass, array $initialParams = []): self
    {
        if (!class_exists($modelClass)) {
            throw new Exception("La clase del modelo '$modelClass' no existe.");
        }

        $composer = new self(); // Crea una nueva instancia del compositor

        try {
            $reflectionClass = new ReflectionClass($modelClass);
            $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

            //Intentar leer las propiedades estáticas $primaryKey y $Identity del modelo

            $primaryKeyProperty = null;
            if ($reflectionClass->hasProperty('primaryKey')) {
                $pkReflection = $reflectionClass->getProperty('primaryKey');
                if ($pkReflection->isStatic()) {
                    $primaryKeyProperty = $pkReflection->getValue(null); // null para propiedades estáticas
                }
            }

            $identityProperty = null;
            if ($reflectionClass->hasProperty('Identity')) {
                $identityReflection = $reflectionClass->getProperty('Identity');
                if ($identityReflection->isStatic()) {
                    $identityProperty = $identityReflection->getValue(null); // null para propiedades estáticas
                }
            }
            
            foreach ($properties as $property) {
                $propertyName = $property->getName();

                // EXCLUSIÓN DE LA LLAVE PRIMARIA/IDENTITY:
                // Si la propiedad actual es la llave primaria o la columna IDENTITY, la saltamos.
                if ($propertyName === $identityProperty) {
                    continue;
                }

                $defaultValue = null; // Valor por defecto general

                // Intentar obtener el tipo de la propiedad para asignar un valor por defecto
                if ($property->hasType()) {
                    $type = $property->getType();
                    $typeName = $type->getName();
                    $isNullable = $type->allowsNull();

                    switch ($typeName) {
                        case 'int':
                            $defaultValue = $isNullable ? null : 0;
                            break;
                        case 'float':
                            $defaultValue = $isNullable ? null : 0.0;
                            break;
                        case 'string':
                            $defaultValue = $isNullable ? null : "";
                            break;
                        case 'bool':
                            $defaultValue = $isNullable ? null : false;
                            break;
                        case 'DateTime': // O \DateTime
                        case '\DateTime':
                            $defaultValue = null; // Las fechas se gestionarán dinámicamente si es necesario
                            break;
                        default:
                            $defaultValue = null; // Para otros tipos de objetos, default a null
                            break;
                    }
                }
                // Si no tiene tipo o es un tipo desconocido, se queda en null
                $composer->modelGeneratedDefaults[$propertyName] = $defaultValue;
            }
        } catch (\ReflectionException $e) {
            throw new Exception("Error al reflejar la clase del modelo '$modelClass': " . $e->getMessage());
        }

        // Fusionar los valores por defecto del modelo con cualquier parámetro inicial provisto
        $composer->with($composer->modelGeneratedDefaults); // Primero aplica los del modelo
        $composer->with($initialParams); // Luego los iniciales del usuario (tienen prioridad)

        return $composer;
    }

    /**
     * Establece uno o más parámetros de la reserva.
     * Permite encadenamiento de métodos.
     *
     * @param string|array $key El nombre del parámetro (string) o un array asociativo de parámetros.
     * @param mixed $value El valor del parámetro, si $key es un string.
     * @return self
     */
    public function with(string|array $key, mixed $value = null): self
    {
        if (is_array($key)) {
            // Si se pasa un array, fusionar con la data existente
            $this->data = array_merge($this->data, $key);
        } else {
            // Si se pasa una clave y un valor, establecer ese parámetro
            $this->data[$key] = $value;
        }
        return $this; // Permite el encadenamiento de métodos
    }

    /**
     * Añade una operación matemática para ser aplicada a los parámetros de la reserva.
     * Permite encadenamiento de métodos.
     *
     * @param string $targetField El campo donde se almacenará el resultado de la operación.
     * @param string $field El campo cuyo valor se utilizará en la operación.
     * @param string $operator El tipo de operación ('sum', 'subtract', 'multiply', 'divide').
     * @return self
     */
    public function addOperation(string $targetField, string $field, string $operator): self
    {
        $this->operationsToApply[$targetField] = [
            'field' => $field,
            'op' => $operator
        ];
        return $this; // Permite el encadenamiento de métodos
    }

    /**
     * Construye y devuelve el array final de parámetros de la reserva,
     * aplicando los valores por defecto del modelo y las operaciones definidas.
     *
     * @return array El array completo de parámetros de la reserva.
     */
    public function get(): array
    {
        // Los valores por defecto del modelo y los parámetros iniciales ya están en $this->data
        // gracias al constructor o al método fromModel().
        $finalData = $this->data;

        // Maneja valores por defecto dinámicos si no han sido provistos
        // Estos se aplican ÚNICAMENTE si el valor es null/vacío después de todas las fusiones.
        if (empty($finalData['momento_alta'])) {
            $finalData['momento_alta'] = (new DateTime())->format('Y-m-d H:i:s');
        }
        if (empty($finalData['momento_edicion'])) {
            $finalData['momento_edicion'] = (new DateTime())->format('Y-m-d H:i:s');
        }
        if (empty($finalData['rowguid'])) {
            $finalData['rowguid'] = uniqid(); // Genera un ID único para la columna rowguid
        }
        if (empty($finalData['uniqid'])) {
             $finalData['uniqid'] = uniqid(); // Genera un ID único para la columna uniqid
        }
        if (empty($finalData['fecha_servicio'])) {
             $finalData['fecha_servicio'] = (new DateTime())->format('Y-m-d H:i:s'); // Por ejemplo, fecha actual si no se provee
        }


        // Aplica las operaciones matemáticas definidas
        foreach ($this->operationsToApply as $targetField => $spec) {
            // Verifica que los campos necesarios para la operación existan
            if (!isset($finalData[$targetField], $finalData[$spec['field']])) {
                // Puedes loguear un aviso o lanzar una excepción más específica aquí
                // Por simplicidad, continuamos si falta un campo.
                continue;
            }

            $a = (float) $finalData[$targetField];
            $b = (float) $finalData[$spec['field']];

            // Realiza la operación basada en el operador especificado
            $finalData[$targetField] = match ($spec['op']) {
                'sum'      => $a + $b,
                'subtract' => $a - $b,
                'multiply' => $a * $b,
                'divide'   => $b != 0 ? $a / $b : 0, // Evita división por cero
                default    => $a, // Si el operador no es reconocido, mantiene el valor original
            };
        }

        return $finalData;
    }
}
