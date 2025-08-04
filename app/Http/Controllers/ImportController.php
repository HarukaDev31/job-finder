<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ImportService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    use ApiResponse;

    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * @OA\Post(
     *     path="/api/import/excel",
     *     summary="Importar datos desde archivo Excel",
     *     tags={"Importación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="excel_file",
     *                     type="file",
     *                     description="Archivo Excel (.xlsx, .xls) máximo 10MB"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Importación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Importación completada exitosamente"),
     *             @OA\Property(property="output", type="string", example="=== RESULTADOS DE LA IMPORTACIÓN ===\nTrabajos:\n  - Creados: 5\n  - Actualizados: 0\n  - Errores: 0")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="El archivo es requerido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=413,
     *         description="Archivo muy grande",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="El archivo es demasiado grande")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error durante la importación")
     *         )
     *     )
     * )
     */
    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240' // 10MB máximo
        ], [
            'excel_file.required' => 'El archivo Excel es requerido',
            'excel_file.file' => 'El archivo debe ser válido',
            'excel_file.mimes' => 'El archivo debe ser un Excel (.xlsx, .xls)',
            'excel_file.max' => 'El archivo no puede ser mayor a 10MB'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $file = $request->file('excel_file');
            $userId = auth()->id();

            $result = $this->importService->importFromExcel($file, $userId);

            if ($result['success']) {
                return $this->successResponse($result['output'], $result['message']);
            } else {
                return $this->serverErrorResponse($result['message']);
            }

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error durante la importación: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/import/template",
     *     summary="Descargar plantilla Excel",
     *     tags={"Importación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Archivo Excel descargado",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
     *         )
     *     )
     * )
     */
    public function downloadTemplate()
    {
        try {
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="plantilla_importacion_jobfinder.xlsx"'
            ];

            // Crear plantilla básica
            $template = $this->createTemplate();
            
            return response($template, 200, $headers);

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/import/stats",
     *     summary="Obtener estadísticas de importación",
     *     tags={"Importación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas obtenidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="trabajos", type="integer", example=150),
     *                 @OA\Property(property="postulantes", type="integer", example=75),
     *                 @OA\Property(property="postulaciones", type="integer", example=25),
     *                 @OA\Property(property="usuarios", type="integer", example=200)
     *             )
     *         )
     *     )
     * )
     */
    public function getStats()
    {
        try {
            $stats = $this->importService->getStats();
            return $this->successResponse($stats, 'Estadísticas obtenidas exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    private function createTemplate()
    {
        // Crear una plantilla básica con las columnas necesarias
        $template = [
            ['TIPO', 'TITULO', 'DESCRIPCION', 'SUELDO', 'EMAIL', 'NOMBRE', 'DOCUMENTO', 'TRABAJO_ID', 'POSTULANTE_ID', 'MENSAJE', 'ESTADO'],
            ['TRABAJO', 'Desarrollador Frontend', 'Desarrollar interfaces web con Vue.js', '2500000', '', '', '', '', '', '', ''],
            ['POSTULANTE', '', '', '', 'juan@email.com', 'Juan Pérez', '12345678', '', '', '', ''],
            ['POSTULACION', '', '', '', '', '', '', '1', '1', 'Me interesa mucho el puesto', 'pendiente']
        ];

        // Convertir a Excel usando Laravel Excel
        return \Maatwebsite\Excel\Facades\Excel::create('plantilla_importacion_jobfinder', function($excel) use ($template) {
            $excel->sheet('Importación', function($sheet) use ($template) {
                $sheet->fromArray($template);
                
                // Estilo para encabezados
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#4CAF50');
                    $row->setFontColor('#FFFFFF');
                });
            });
        })->string('xlsx');
    }
}
