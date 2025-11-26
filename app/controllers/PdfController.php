<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Pdf.php";
require_once __DIR__ . "/../../public/libraries/fpdf/fpdf.php";
require_once __DIR__ . "/../../public/libraries/phplot/phplot.php";

class PdfController {

    private $model;

    public function __construct() {
        session_start(); // SOLO AQUÍ
        $conexion = Database::getConnection();
        $this->model = new Pdf($conexion);
    }

    /*=====================================
      PDF INDIVIDUAL DE SOLICITUD
    =====================================*/
    public function pdfSolicitud() {

        // Validación de rol
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "alumno") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        if (!isset($_GET['id'])) die("ID no recibido");
        $id = intval($_GET['id']);

        $s = $this->model->consultarSolicitudIndividual($id);
        if (!$s) die("Solicitud no encontrada");

        // Limpieza de valores NULL
        foreach ($s as $k => $v) {
            if ($v === null) $s[$k] = "";
        }

        // PDF
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);

        $pdf->Cell(0,10,utf8_decode('Solicitud #'.$s['id_solicitud']),0,1,'C');
        $pdf->Ln(3);

        $this->campo($pdf, 'Solicitante:', utf8_decode($s['solicitante']));
        $this->campo($pdf, 'Tipo:', $s['tipo']);
        $this->campo($pdf, 'Carrera:', utf8_decode($s['carrera']));
        $this->campo($pdf, 'Material:', utf8_decode($s['material']));
        $this->campo($pdf, 'Cantidad:', $s['cantidad']);
        $this->campo($pdf, 'Fecha:', $s['fecha_solicitud']);
        $this->campo($pdf, 'Ubicación:', utf8_decode($s['ubicacion']));
        $this->campo($pdf, 'Correo:', $s['correo']);
        $this->campo($pdf, 'Teléfono:', $s['telefono']);

        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,8,'Observaciones:',0,1);
        $pdf->SetFont('Arial','',12);
        $pdf->MultiCell(0,8,utf8_decode($s['observaciones']));

        $pdf->Output('D','Solicitud_'.$s['id_solicitud'].'.pdf');
    }

    private function campo($pdf, $label, $value) {
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(50,8,utf8_decode($label),0,0);
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,8,utf8_decode($value),0,1);
    }

    /*=====================================
        PDF ESTADÍSTICO
    =====================================*/
    public function generarReportePastel() {

        // Validación de rol
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "coordinador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Obtener datos
        $materiales = $this->model->topMateriales(30);
        $carreras   = $this->model->topCarreras(30);
        $generos    = $this->model->solicitudesGenero(30);

        // Rutas
        $imgMat = "public/media/graphs/pastel_materiales.png";
        $imgCar = "public/media/graphs/pastel_carreras.png";
        $imgGen = "public/media/graphs/pastel_genero.png";

        // Crear gráficas
        $this->crearPastelPHPlot($materiales, "Materiales más solicitados", $imgMat);
        $this->crearPastelPHPlot($carreras, "Carreras con más solicitudes", $imgCar);
        $this->crearPastelPHPlot($generos, "Solicitudes por género", $imgGen);

        // Crear PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont("Arial","B",16);
        $pdf->Cell(0,10,utf8_decode("Reporte Estadístico"),0,1,"C");
        $pdf->Ln(5);

        // --- GRÁFICA 1 ---
        $pdf->SetFont("Arial","B",14);
        $pdf->Cell(0,10,utf8_decode("Materiales más solicitados"),0,1,"C");
        $pdf->Image($imgMat, 30, $pdf->GetY(), 150, 100);
        $pdf->Ln(110);

        // --- GRÁFICA 2 ---
        $pdf->AddPage();
        $pdf->Cell(0,10,utf8_decode("Carreras con más solicitudes"),0,1,"C");
        $pdf->Image($imgCar, 30, $pdf->GetY(), 150, 100);

        // --- GRÁFICA 3 ---
        $pdf->AddPage();
        $pdf->Cell(0,10,utf8_decode("Solicitudes por género"),0,1,"C");
        $pdf->Image($imgGen, 30, $pdf->GetY(), 150, 100);

        $pdf->Output("D","ReporteCompleto.pdf");
    }


    private function crearPastelPHPlot($data, $titulo, $filename)
    {
        $plot = new PHPlot(900, 600);
        $plot->SetDataType('text-data-single');

        $valores = [];
        foreach ($data as $d) {
            $valores[] = [
                utf8_decode($d['etiqueta']),
                floatval($d['porcentaje'])
            ];
        }

        $plot->SetDataValues($valores);
        $plot->SetPlotType('pie');
        $plot->SetTitle(utf8_decode($titulo));
        $plot->SetLegend(array_column($valores, 0));

        // Colores
        $plot->SetDataColors([
            "#a0e7e5", "#b4c6d1", "#d8c7f1", "#ffb6b9",
            "#527b92", "#a0c4ff", "#ffc6ff", "#bde0fe", "#fcd5ce"
        ]);

        $plot->SetFont('title', '5');
        $plot->SetFont('legend', '4');
        $plot->SetFont('generic', '5');

        $plot->SetOutputFile($filename);
        $plot->SetIsInline(true);
        $plot->DrawGraph();
    }

}
?>
