<?php

use function PHPSTORM_META\type;

require_once './FPDF/fpdf.php';



class PDF extends FPDF
{
    public function Header()
    {
        $this->Image('./img/Logo/logo.png', 10, 1, 35);
        $this->SetFont('arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(100, 10, "La Comanda", 1, 0, 'C');
        $this->Ln(20);
    }

    public function Body($contenido)
    {
        $this->SetFont('arial', 'B', 12);
        $this->MultiCell(0, 5, $contenido);
        $this->Ln();
    }
    public function FancyTable($header, $data)
    {
    
    // Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Cabecera
        $w = array();
        $ancho = 30;
        for ($i=0; $i <count($header) ; $i++) {
            $w[]=$ancho +15;
        }

        for ($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        // Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Datos
        $fill = false;
        
        foreach ($data as $clave => $row) {
            foreach ($row as $key => $value) {
                $this->Cell($w[0], 6, $value, 'LR', 0, 'L', $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }
        // Línea de cierre
        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('arial', 'B', 8);
        $this->Cell(0, 10, 'Alejandro Bongioanni/{nb}', 0, 0, 'C');
    }
}
