<?php
// Se incluye la conexión a la base de datos
require_once __DIR__ . "/../../config/database.php";
// Se incluye el modelo encargado de consultas relacionadas con PDF
require_once __DIR__ . "/../models/Pdf.php";
// Librería FPDF para generar documentos PDF
require_once __DIR__ . "/../../public/libraries/fpdf/fpdf.php";
// Librería PHPlot para generar gráficas de pastel
require_once __DIR__ . "/../../public/libraries/phplot/phplot.php";

class PdfController {

    private $model;

    public function __construct() {
        // Inicia sesión para validar permisos y manejar mensajes
        session_start(); 
        // Obtiene conexión a la base de datos
        $conexion = Database::getConnection();
        // Instancia del modelo Pdf
        $this->model = new Pdf($conexion);
    }

    /*=====================================
      PDF INDIVIDUAL DE SOLICITUD
      Genera un PDF con la información de una sola solicitud
    =====================================*/
    public function pdfSolicitud() {

        // Valida que el usuario sea alumno
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "alumno") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Verifica que se haya recibido un ID por GET
        if (!isset($_GET['id'])) die("ID no recibido");
        $id = intval($_GET['id']);

        // Consulta los datos de la solicitud indicada
        $s = $this->model->consultarSolicitudIndividual($id);
        if (!$s) die("Solicitud no encontrada");

        // Reemplaza valores NULL por cadenas vacías para evitar errores en el PDF
        foreach ($s as $k => $v) {
            if ($v === null) $s[$k] = "";
        }

        // Crea un PDF nuevo con orientación vertical, milímetros y tamaño A4
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);

        // Título centrado con el número de solicitud
        $pdf->Cell(0,10,utf8_decode('Solicitud #'.$s['id_solicitud']),0,1,'C');
        $pdf->Ln(3);

        // Secciones del PDF usando método auxiliar campo()
        $this->campo($pdf, 'Solicitante:', utf8_decode($s['solicitante']));
        $this->campo($pdf, 'Tipo:', $s['tipo']);
        $this->campo($pdf, 'Carrera:', utf8_decode($s['carrera']));
        $this->campo($pdf, 'Material:', utf8_decode($s['material']));
        $this->campo($pdf, 'Cantidad:', $s['cantidad']);
        $this->campo($pdf, 'Fecha:', $s['fecha_solicitud']);
        $this->campo($pdf, 'Ubicación:', utf8_decode($s['ubicacion']));
        $this->campo($pdf, 'Correo:', $s['correo']);
        $this->campo($pdf, 'Teléfono:', $s['telefono']);

        // Observaciones
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,8,'Observaciones:',0,1);
        $pdf->SetFont('Arial','',12);
        $pdf->MultiCell(0,8,utf8_decode($s['observaciones']));

        // Genera la descarga del PDF
        $pdf->Output('D','Solicitud_'.$s['id_solicitud'].'.pdf');
    }

    // Método auxiliar para imprimir una línea con etiqueta y valor
    private function campo($pdf, $label, $value) {
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(50,8,utf8_decode($label),0,0);
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,8,utf8_decode($value),0,1);
    }

    /*=====================================
        PDF ESTADÍSTICO
        Genera gráficas de pastel y las incrusta en un PDF
    =====================================*/
    public function generarReportePastel() {

        // Solo coordinadores pueden acceder a este reporte
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "coordinador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Consultas de datos estadísticos
        $materiales = $this->model->topMateriales(30);
        $carreras   = $this->model->topCarreras(30);
        $generos    = $this->model->solicitudesGenero(30);

        // Rutas donde se guardarán las imágenes generadas
        $imgMat = "public/media/graphs/pastel_materiales.png";
        $imgCar = "public/media/graphs/pastel_carreras.png";
        $imgGen = "public/media/graphs/pastel_genero.png";

        // Generación de cada gráfica de pastel
        $this->crearPastelPHPlot($materiales, "Materiales más solicitados", $imgMat);
        $this->crearPastelPHPlot($carreras, "Carreras con más solicitudes", $imgCar);
        $this->crearPastelPHPlot($generos, "Solicitudes por género", $imgGen);

        // Crear PDF del reporte
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont("Arial","B",16);
        $pdf->Cell(0,10,utf8_decode("Reporte Estadístico"),0,1,"C");
        $pdf->Ln(5);

        // GRÁFICA 1
        $pdf->SetFont("Arial","B",14);
        $pdf->Cell(0,10,utf8_decode("Materiales más solicitados"),0,1,"C");
        $pdf->Image($imgMat, 30, $pdf->GetY(), 150, 100);
        $pdf->Ln(110);

        // GRÁFICA 2
        $pdf->AddPage();
        $pdf->Cell(0,10,utf8_decode("Carreras con más solicitudes"),0,1,"C");
        $pdf->Image($imgCar, 30, $pdf->GetY(), 150, 100);

        // GRÁFICA 3
        $pdf->AddPage();
        $pdf->Cell(0,10,utf8_decode("Solicitudes por género"),0,1,"C");
        $pdf->Image($imgGen, 30, $pdf->GetY(), 150, 100);

        // Forzar descarga del PDF
        $pdf->Output("D","ReporteCompleto.pdf");
    }

    // Función que genera gráficas de pastel utilizando PHPlot
    private function crearPastelPHPlot($data, $titulo, $filename)
    {
        // Se crea el lienzo en 900x600 píxeles
        $plot = new PHPlot(900, 600);
        $plot->SetDataType('text-data-single');

        // Se prepara la estructura requerida por PHPlot
        $valores = [];
        foreach ($data as $d) {
            $valores[] = [
                utf8_decode($d['etiqueta']),
                floatval($d['porcentaje'])
            ];
        }

        // Se asignan los datos al gráfico
        $plot->SetDataValues($valores);
        // Tipo de gráfico: pastel
        $plot->SetPlotType('pie');
        // Título del gráfico
        $plot->SetTitle(utf8_decode($titulo));
        // Leyenda con etiquetas
        $plot->SetLegend(array_column($valores, 0));

        // Paleta de colores personalizada
        $plot->SetDataColors([
            "#a0e7e5", "#b4c6d1", "#d8c7f1", "#ffb6b9",
            "#527b92", "#a0c4ff", "#ffc6ff", "#bde0fe", "#fcd5ce"
        ]);

        // Tamaños de fuente del gráfico
        $plot->SetFont('title', '5');
        $plot->SetFont('legend', '4');
        $plot->SetFont('generic', '5');

        // Salida hacia archivo PNG
        $plot->SetOutputFile($filename);
        $plot->SetIsInline(true);
        $plot->DrawGraph();
    }

}
?>
